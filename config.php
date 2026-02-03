<?php
/*
 * Thông số cấu hình (chạy từ public_html)
 */

date_default_timezone_set("Asia/Ho_Chi_Minh"); //Múi giờ mặc định
define("CONFIG", [
	// Database
	"DB"=>[
		"db_host"     =>  "localhost",
		"db_user"     =>  "ezdi_dev",
		"db_name"     =>  "ezdi_dev",
		"db_password" =>  '@Kw21w3%5xnwsB6t',
		"db_charset"  =>  "utf8mb4"
	],
	// Panel: "directadmin" | "cyberpanel"
	// Thông tin đăng nhập panel (DirectAdmin hoặc CyberPanel)
	"BUILDER" => [
		"panel"    => "directadmin",  // directadmin | cyberpanel
		"username" => "web",
		"password" => 'lfo7mH7e5misnu2LDH6je1zm1531L6GyPr7Dpq35582RIoVM4ZhAIomOC3Y70CF0',
		"host"     => null,            // null = $_SERVER['SERVER_ADDR']
		"port"     => 2222,            // DA: 2222, CyberPanel: 8090
		"ssl"      => false,           // HTTPS tới panel
		"public_path_template" => null,  // null = mặc định. CyberPanel: "/home/%s/public_html"
		"package" => "Default"            // CyberPanel: package name khi tạo website
	],
	"SSL"           => true, // Chuyển sang https
	"WWW"           => false, // Dùng www.
	"MAXDISK"       => 7000, // Dung lượng tối đa (Mb)
]);

// public_html là document root; SERVER_ROOT = thư mục cha của public_html
define("SERVER_ROOT", dirname(__DIR__));
define("SYSTEM_ROOT", __DIR__ . "/_trangweb");
define("APPS_ROOT", SYSTEM_ROOT . "/apps");

define('ORDER_STATUS', [
	0 => [
		'label' => 'Wait for pay',
		'color' => 'warning'
	],
	1 => [
		'label' => 'Paid',
		'color' => 'success'
	],
	9 => [
		'label' => 'Canceled',
		'color' => 'danger'
	],
]);

define('DOMAIN_STATUS', [
	'active' => [
		'label' => 'Active',
		'color' => 'success'
	],
	'suspended' => [
		'label' => 'Temporarily locked',
		'color' => 'warning'
	],
	'deleted' => [
		'label' => 'Deleted',
		'color' => 'danger'
	],
]);
