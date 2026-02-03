<?php
/**
 * Adapter CyberPanel (REST API port 8090, JSON).
 */
namespace classes\panel;

class CyberPanelManager implements PanelManagerInterface {

	private function baseUrl(): string {
		$host = CONFIG["BUILDER"]["host"] ?? $_SERVER["SERVER_ADDR"];
		$port = CONFIG["BUILDER"]["port"] ?? 8090;
		$ssl = CONFIG["BUILDER"]["ssl"] ?? false;
		return ($ssl ? "https" : "http") . "://{$host}:{$port}";
	}

	public function request(array $params): array {
		$endpoint = $params["endpoint"] ?? "/api/runFunction";
		$body = $params["body"] ?? $params["query"] ?? [];
		$method = strtoupper($params["method"] ?? "POST");
		$url = $this->baseUrl() . $endpoint;
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => CONFIG["BUILDER"]["ssl"] ?? false,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 120,
			CURLOPT_POST => $method === "POST",
			CURLOPT_POSTFIELDS => is_array($body) ? json_encode($body) : $body,
			CURLOPT_HTTPHEADER => [
				"Content-Type: application/json",
				"Authorization: Basic " . base64_encode(CONFIG["BUILDER"]["username"] . ":" . CONFIG["BUILDER"]["password"])
			]
		]);
		if (isset($params["daAccount"])) {
			$acc = $params["daAccount"];
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Content-Type: application/json",
				"Authorization: Basic " . base64_encode(($acc["username"] ?? "") . ":" . ($acc["password"] ?? ""))
			]);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		$response = curl_exec($ch);
		curl_close($ch);
		$decoded = json_decode($response, true);
		return is_array($decoded) ? $decoded : ["error" => 1, "details" => $response];
	}

	public function createDomain($web): ?string {
		$r = $this->request([
			"body" => [
				"function" => "createWebsite",
				"domainName" => $web->domain,
				"owner" => CONFIG["BUILDER"]["username"],
				"email" => function_exists("user") ? user("email") : "",
				"packageName" => CONFIG["BUILDER"]["package"] ?? "Default",
				"php" => "PHP 8.0",
				"ssl" => 1
			]
		]);
		if (isset($r["createWebSiteStatus"]) && $r["createWebSiteStatus"] == 1) return null;
		if (isset($r["status"]) && $r["status"] == 1) return null;
		$msg = $r["error_message"] ?? $r["details"] ?? $r["createWebSiteStatus"] ?? json_encode($r);
		return "<b>Lỗi khởi tạo tên miền ({$web->domain})</b>: " . $msg;
	}

	public function createDatabase($web) {
		$dbName = empty($web->app_name) ? "u{$web->id}" : "w{$web->id}";
		$dbPass = randomString(15);
		$r = $this->request([
			"body" => [
				"function" => "createDatabase",
				"databaseName" => $dbName,
				"databaseUsername" => $dbName,
				"databasePassword" => $dbPass,
				"website" => $web->domain
			]
		]);
		if (isset($r["status"]) && $r["status"] == 1) return ["name" => $dbName, "password" => $dbPass];
		if (isset($r["createDatabaseStatus"]) && $r["createDatabaseStatus"] == 1) return ["name" => $dbName, "password" => $dbPass];
		$msg = $r["error_message"] ?? $r["details"] ?? json_encode($r);
		return "<b>Lỗi tạo database</b>: " . $msg;
	}

	public function deleteDomain(string $domain): void {
		$this->request(["body" => ["function" => "deleteWebsite", "domainName" => $domain]]);
	}

	public function deleteDatabase(string $dbName): void {
		$this->request(["body" => ["function" => "deleteDatabase", "databaseName" => $dbName]]);
	}

	public function changeDomain(string $oldDomain, string $newDomain): array {
		return $this->request([
			"body" => [
				"function" => "changeDomainName",
				"domainName" => $oldDomain,
				"newDomainName" => $newDomain
			]
		]);
	}

	public function createSSL(string $domain): array {
		return $this->request([
			"body" => [
				"function" => "issueSSL",
				"domainName" => $domain,
				"email" => function_exists("user") ? user("email") : ""
			]
		]);
	}

	public function updateSSL(string $domain, string $privateKey, string $certificate, string $cacert): void {
		$this->request([
			"body" => [
				"function" => "submitSSL",
				"domainName" => $domain,
				"cert" => $certificate,
				"key" => $privateKey,
				"cabundle" => $cacert
			]
		]);
	}

	public function getPublicPath(string $domain): string {
		$tpl = CONFIG["BUILDER"]["public_path_template"] ?? null;
		if ($tpl !== null) return sprintf($tpl, $domain);
		return "/home/{$domain}/public_html";
	}

	public function getPanelType(): string {
		return "cyberpanel";
	}

	public function createUser(array $params): ?string {
		$username = $params["username"] ?? "";
		$password = $params["passwd"] ?? $params["password"] ?? "";
		$r = $this->request([
			"body" => [
				"function" => "createUser",
				"firstName" => $username,
				"lastName" => $username,
				"email" => $params["email"] ?? "",
				"userName" => $username,
				"password" => $password,
				"package" => $params["package"] ?? CONFIG["BUILDER"]["package"] ?? "Default",
				"acl" => $params["acl"] ?? "user",
				"websitesLimit" => (int)($params["websitesLimit"] ?? 1)
			]
		]);
		if (isset($r["status"]) && $r["status"] == 1) return null;
		if (isset($r["createUserStatus"]) && $r["createUserStatus"] == 1) return null;
		$msg = $r["error_message"] ?? $r["details"] ?? $r["createUserStatus"] ?? json_encode($r);
		return $msg;
	}

	public function deleteUser(string $username): array {
		return $this->request([
			"body" => [
				"function" => "deleteUser",
				"userName" => $username
			]
		]);
	}

	public function extractFile(array $params, array $account = []): array {
		$body = [
			"function" => "extractFile",
			"path" => $params["path"] ?? "",
			"fileName" => $params["fileName"] ?? $params["file"] ?? "",
			"extractPath" => $params["extractPath"] ?? $params["path"] ?? ""
		];
		$req = ["body" => $body];
		if (!empty($account)) {
			$req["daAccount"] = ["username" => $account["username"] ?? "", "password" => $account["password"] ?? ""];
		}
		return $this->request($req);
	}
}
