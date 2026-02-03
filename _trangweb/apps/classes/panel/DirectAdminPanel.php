<?php
/**
 * Adapter DirectAdmin (CMD_API_*).
 */
namespace classes\panel;

class DirectAdminPanel implements PanelManagerInterface {

	public function request(array $params): array {
		$command = $params["command"] ?? "";
		$method = $params["method"] ?? "GET";
		$query = $params["query"] ?? null;
		$daAccount = $params["daAccount"] ?? [];
		$ssl = CONFIG["BUILDER"]["ssl"] ?? false;
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_SSL_VERIFYPEER => $ssl,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 120
		]);
		$cacert = __DIR__ . "/../cacert.pem";
		if ($ssl && file_exists($cacert)) {
			curl_setopt($ch, CURLOPT_CAINFO, realpath($cacert));
		}
		$user = $daAccount["username"] ?? CONFIG["BUILDER"]["username"];
		$pass = $daAccount["password"] ?? CONFIG["BUILDER"]["password"];
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . base64_encode($user . ":" . $pass)]);
		$query = is_array($query) ? http_build_query($query) : (is_string($query) ? $query : null);
		$host = $params["host"] ?? CONFIG["BUILDER"]["host"] ?? $_SERVER["SERVER_ADDR"];
		$port = CONFIG["BUILDER"]["port"] ?? 2222;
		$url = ($ssl ? "https" : "http") . "://{$host}:{$port}/" . $command . ($method == "GET" && is_string($query) ? "?" . $query : "");
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => $method != "GET" ? $query : null
		]);
		$response = curl_exec($ch);
		curl_close($ch);
		parse_str(urldecode($response), $out);
		return $out;
	}

	public function createDomain($web): ?string {
		$r = $this->request([
			"command" => "CMD_API_DOMAIN",
			"method" => "POST",
			"query" => [
				"action" => "create",
				"domain" => $web->domain,
				"ubandwidth" => "unlimited",
				"uquota" => "unlimited",
				"ssl" => "ON",
				"cgi" => "ON",
				"php" => "ON"
			]
		]);
		if (empty($r["error"])) return null;
		return empty($r["text"]) ? "Đang tạo lại tên miền ({$web->domain})" : "<b>Lỗi khởi tạo tên miền ({$web->domain})</b>: " . ($r["details"] ?? "");
	}

	public function createDatabase($web) {
		$DB["name"] = empty($web->app_name) ? "u{$web->id}" : "w{$web->id}";
		$DB["password"] = randomString(15);
		$r = $this->request([
			"command" => "CMD_API_DATABASES",
			"method" => "POST",
			"query" => [
				"action" => "create",
				"name" => $DB["name"],
				"user" => $DB["name"],
				"passwd" => $DB["password"],
				"passwd2" => $DB["password"]
			]
		]);
		if (empty($r["error"])) return $DB;
		return empty($r["text"]) ? "Đang tạo lại database" : "<b>Lỗi tạo database</b>: " . ($r["details"] ?? "");
	}

	public function deleteDomain(string $domain): void {
		$this->request([
			"command" => "CMD_API_DOMAIN",
			"method" => "POST",
			"query" => ["confirmed" => "Confirm", "delete" => "yes", "select0" => $domain]
		]);
	}

	public function deleteDatabase(string $dbName): void {
		$this->request([
			"command" => "CMD_API_DATABASES",
			"method" => "POST",
			"query" => ["action" => "delete", "select0" => $dbName]
		]);
	}

	public function changeDomain(string $oldDomain, string $newDomain): array {
		return $this->request([
			"command" => "CMD_CHANGE_DOMAIN",
			"method" => "POST",
			"query" => ["old_domain" => $oldDomain, "new_domain" => $newDomain]
		]);
	}

	public function createSSL(string $domain): array {
		return $this->request([
			"command" => "CMD_API_SSL",
			"method" => "POST",
			"query" => [
				"domain" => $domain,
				"type" => "create",
				"request" => "letsencrypt",
				"name" => "www.{$domain}",
				"email" => function_exists("user") ? user("email") : "",
				"keysize" => 4096,
				"encryption" => "sha256",
				"le_select0" => $domain,
				"le_select2" => "www.{$domain}",
				"action" => "save"
			]
		]);
	}

	public function updateSSL(string $domain, string $privateKey, string $certificate, string $cacert): void {
		$this->request([
			"command" => "CMD_API_SSL",
			"method" => "POST",
			"query" => [
				"domain" => $domain,
				"type" => "paste",
				"certificate" => $privateKey . $certificate,
				"action" => "save"
			]
		]);
		$this->request([
			"command" => "CMD_SSL",
			"method" => "POST",
			"query" => ["domain" => $domain, "type" => "cacert", "active" => "yes", "cacert" => $cacert, "action" => "save"]
		]);
	}

	public function getPublicPath(string $domain): string {
		$tpl = CONFIG["BUILDER"]["public_path_template"] ?? null;
		if ($tpl !== null) return sprintf($tpl, $domain);
		return SERVER_ROOT . "/domains/{$domain}/public_html";
	}

	public function getPanelType(): string {
		return "directadmin";
	}

	public function createUser(array $params): ?string {
		$r = $this->request([
			"command" => "CMD_API_ACCOUNT_USER",
			"method" => "POST",
			"query" => array_merge([
				"action" => "create",
				"add" => "Submit",
				"ip" => $_SERVER["SERVER_ADDR"] ?? "",
				"notify" => "no"
			], $params)
		]);
		if (!isset($r["error"])) return $r["details"] ?? "Lỗi kết nối";
		if ($r["error"] == 1) return $r["details"] ?? "Không xác định";
		return null;
	}

	public function deleteUser(string $username): array {
		return $this->request([
			"command" => "CMD_API_SELECT_USERS",
			"method" => "POST",
			"query" => [
				"confirmed" => "Confirm",
				"delete" => "yes",
				"select0" => $username
			]
		]);
	}

	public function extractFile(array $params, array $account = []): array {
		return $this->request([
			"command" => "CMD_API_FILE_MANAGER",
			"method" => "POST",
			"query" => $params,
			"daAccount" => $account
		]);
	}
}
