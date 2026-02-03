<?php
/**
 * Interface cho Panel quản lý hosting (DirectAdmin / CyberPanel).
 * Config: CONFIG["BUILDER"]["panel"] = "directadmin" | "cyberpanel"
 */
namespace classes\panel;

interface PanelManagerInterface {

	public function request(array $params): array;
	public function createDomain($web): ?string;
	public function createDatabase($web);
	public function deleteDomain(string $domain): void;
	public function deleteDatabase(string $dbName): void;
	public function changeDomain(string $oldDomain, string $newDomain): array;
	public function createSSL(string $domain): array;
	public function updateSSL(string $domain, string $privateKey, string $certificate, string $cacert): void;
	public function getPublicPath(string $domain): string;
	public function getPanelType(): string;

	/** Tạo tài khoản panel (username, email, passwd, domain, package, ...). Trả về null nếu thành công, string lỗi nếu thất bại. */
	public function createUser(array $params): ?string;

	/** Xóa tài khoản panel theo username. Trả về array response từ panel. */
	public function deleteUser(string $username): array;

	/** Gọi API quản lý file (giải nén, ...). $params tùy panel; $account = [username, password] cho user cụ thể. Trả về array response. */
	public function extractFile(array $params, array $account = []): array;
}
