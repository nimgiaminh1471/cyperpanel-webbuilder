<?php
/*
# Class kết nối thao tác với server (DirectAdmin / CyberPanel) qua PanelManager
*/
/*
# Cấu hình DirectAdmin:
1. /usr/local/directadmin/conf/directadmin.conf: check_subdomain_owner=0//Cho phép dùng subdomain dạng tên miền chính
2. Thêm file /var/www/html/add-domain.txt với nội dung là: @true
3. Bỏ disable functions ex
*/
namespace classes;
use DB, Folder, ZipArchive, Assets, Storage;
use models\BuilderDomain;

require_once __DIR__ . '/panel/PanelManagerInterface.php';
require_once __DIR__ . '/panel/DirectAdminPanel.php';
require_once __DIR__ . '/panel/CyberPanelManager.php';
require_once __DIR__ . '/panel/PanelManager.php';

class WebBuilder{
	/** Kết nối tới server (ủy quyền cho panel: DirectAdmin hoặc CyberPanel) */
	public static function connect($params){
		$params["daAccount"] = $params["daAccount"] ?? null;
		return self::panel()->request($params);
	}

	// Khởi tạo website
	public static function setup($web, $userLogin, $connection){

		// // Tạo tên miền nếu chưa có
		// if( !is_dir( self::userPublic($web->domain) ) ){
		// 	if( isset($_POST["setup"]) ){
		// 		$status = self::createDomain($web);
		// 	}
		// 	return self::loading([
		// 		"msg"    => "Đang khởi tạo tên miền, xin vui lòng chờ...",
		// 		"status" => $status ?? null
		// 	]);
		// }

		// // Copy mã nguồn sang tên miền
		// if( !is_dir( self::userPublic($web->domain)."/builder" ) ){
		// 	if( isset($_POST["setup"]) ){
		// 		$status = self::copySourceCodeToDomain($web);
		// 	}
		// 	return self::loading([
		// 		"msg"    => "Đang khởi tạo mã nguồn website, xin vui lòng chờ...",
		// 		"status" => $status ?? null
		// 	]);
		// }

		// // Tạo database
		// if( empty( webConfig($web->domain, "DB_USER") ) ){
		// 	if( isset($_POST["setup"]) ){
		// 		$status = self::createDatabase($web);
		// 	}
		// 	// Cập nhật chứng chỉ mặc định theo tên miền chính
		// 	WebBuilder::updateSSL(
		// 		$web->domain,
		// 		Storage::setting("builder_ssl_private_key"),
		// 		Storage::setting("builder_ssl_certificate"),
		// 		Storage::setting("builder_ssl_cacert")
		// 	);
		// 	return self::loading([
		// 		"msg"    => "Đang khởi tạo dữ liệu, xin vui lòng chờ...",
		// 		"status" => $status ?? null
		// 	]);
		// }
		
		// Import dữ liệu
		if( empty($userLogin) ){
			if( isset($_POST["setup"]) ){
				$status = self::importDatabase($web, $connection);
			}
			return self::loading([
				"msg"    => "Hoàn tất cập nhật dữ liệu, xin vui lòng chờ...",
				"status" => $status ?? null
			]);
		}
	}

	// Tự động thay đổi tên miền cũ sang mới
	public static function moveToNewDomain($web, $correctDomain){
		$aliasDomain = empty($web->alias_domain) ? $correctDomain : $web->alias_domain;
		$sourceCodePath    = SYSTEM_ROOT."/builder/domains/{$web->domain}/public_html";
		$sourceCodePathNew = SYSTEM_ROOT."/builder/domains/{$aliasDomain}/public_html";
		if( is_dir( $sourceCodePath ) ){
			$databaseName = webConfig($web->domain, "DB_NAME", $sourceCodePath."/wp-config.php");
			if( empty($databaseName) ){
				$error = "Không thể đọc dữ liệu của code cũ: <b>{$sourceCodePath}</b>";
			}else{
				$databaseFileName = SYSTEM_ROOT."/builder/database-setup/{$databaseName}.sql";
				if( !file_exists( $databaseFileName ) && $web->app_price > 0 ){
					$databaseFileName = SYSTEM_ROOT."/builder/database-template/{$web->app}.sql";
				}
				if( !file_exists( $databaseFileName ) ){
					$error = "Vui lòng upload file database: <b>{$databaseFileName}</b>";
				}
			}
		}else{
			$error = "Vui lòng upload code cũ của <b>{$web->domain}</b> vào thư mục: <br> <b>{$sourceCodePath}</b>";
		}
		if( is_dir( self::userPublic($aliasDomain) ) ){
			$error = "Đã tồn tại thư mục của tên miền mới: <b>".self::userPublic($aliasDomain)."</b>";
		}
		
		if( empty($error) ){
			// Không có lỗi xảy ra
			$oldDomain = $web->domain;
			BuilderDomain::find($web->id)->update([
				"domain"         => $aliasDomain,
				"default_domain" => $correctDomain,
				"user_login"     => user("email"),
				"password"       => user("password")
			]);
			rename( dirname($sourceCodePath), dirname($sourceCodePathNew) );
			return self::loading([
				"msg"    => "Đang đổi tên miền từ <b>{$oldDomain}</b> sang <b>{$aliasDomain}</b>",
				"status" => $status ?? null
			]);
		}else{
			// Có lỗi xảy ra
			return self::loading([
				"msg"    => $error,
				"status" => $status ?? null
			]);
		}
	}
	// Tạo tên miền
	public static function createDomain($web){
		return self::panel()->createDomain($web);
	} 

	// Tạo database
	public static function createDatabase($web){
		$result = self::panel()->createDatabase($web);
		if (is_string($result)) return $result;
		$DB = $result;
		$configConfig = [
			"DB_USER"              => CONFIG["BUILDER"]["username"].'_'.$DB["name"],
			"DB_NAME"              => CONFIG["BUILDER"]["username"].'_'.$DB["name"],
			"DB_PASSWORD"          => $DB["password"],
			"BUILDER_MAXDISK"      => WEB_BUILDER["disk"],
			"BUILDER_EXPIRED"      => $web->expired,
			"BUILDER_DOMAIN"       => DOMAIN,
			"BUILDER_SSL"          => "true",
			"BUILDER_BACKUP"       => (permission("admin") ? "true" : "false"),
			"BUILDER_IS_TEMPLATE"  => empty($web->app_price) ? "false" : "true",
			"BUILDER_WWW"          => "false",
			"BUILDER_IS_OF_MEMBER" => permission("seller|agency") ? "false" : "true"
		];
		if (file_exists(self::userPublic($web->domain)."/wp-config.php")) {
			unlink(self::userPublic($web->domain)."/wp-config.php");
		}
		self::updateConfig($web->domain, $configConfig);
		return null;
	}

	// Copy dữ liệu sang tên miền
	public static function copySourceCodeToDomain($web){
		if( file_exists( self::userPublic($web->domain)."/index.php" ) ){
			return;
		}
		// Thư mục chứa code
		$deleteSourceCode = true;
		$sourceCodePath = SYSTEM_ROOT."/builder/domains/{$web->domain}/public_html";
		if( !is_dir($sourceCodePath) ){
			// Copy code từ web mẫu nếu thư mục chứa code không tồn tại
			$templateDomain = BuilderDomain::where("app", $web->app)->where("app_name", "!=", "")->first()->domain ?? null;
			$sourceCodePath = self::userPublic($templateDomain);
			$deleteSourceCode = false;
		}
		if( !is_dir($sourceCodePath) ){
			return "
				<div>Vui lòng upload code vào thư mục dưới:</div>
				<div>
					<b>".SYSTEM_ROOT."/builder/domains/{$web->domain}/public_html</b>
				</div>
			";
		}
		//Folder::clear( self::userPublic($web->domain) );// Dọn dẹp thư mục public
		//Folder::copy($sourceCodePath, self::userPublic($web->domain)); // Copy mã nguồn
		shell_exec("rm -rf ".self::userPublic($web->domain)." && cp -r $sourceCodePath ".dirname( self::userPublic($web->domain) )." && rm ".self::userPublic($web->domain)."/wp-config.php"); // Copy mã nguồn
		//Tạo file .htaccess nếu chưa có
		if( !file_exists( self::userPublic($web->domain)."/.htaccess") ){
	file_put_contents( self::userPublic($web->domain)."/.htaccess", "# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress");
		}
		if( $deleteSourceCode ){
			//Folder::clear( dirname($sourceCodePath), false); // Xóa thư mục chứa code tạm thời
			shell_exec("rm -rf ".dirname($sourceCodePath) );
		}
		Folder::copy(APPS_ROOT."/classes/builder", self::userPublic($web->domain)."/builder"); // Copy thư mục tài nguyên
	}

	// Cập nhật file cấu hình của web
	public static function updateConfig($domain, $data){
		$configFile=self::userPublic($domain)."/wp-config.php";
		if( !file_exists($configFile) ){
			copy(self::userPublic($domain)."/wp-config-sample.php", $configFile);
		}
		$configContents=file_get_contents($configFile, FILE_USE_INCLUDE_PATH);
		
		//Chèn thêm nội dung vào file config
		if( strpos($configContents, "BUILDER_DOMAIN")===false ){
			$configContents.='
define("BUILDER_IS_TEMPLATE", true);
define("BUILDER_EXPIRED", 0);
define("BUILDER_SUSPENDED", "");
define("BUILDER_MAXDISK", 200);
define("BUILDER_SSL", false);
define("BUILDER_WWW", false);
define("BUILDER_BACKUP", false);
define("BUILDER_IS_OF_MEMBER", true);

define("BUILDER_DOMAIN", "");
define("BUILDER_HOME", "https://".BUILDER_DOMAIN);
define("PUBLIC_ROOT", __DIR__);

foreach( glob( dirname(__DIR__, 2)."/".BUILDER_DOMAIN."/_trangweb/builder/includes/*.php") as $file){
	include($file); // Nhúng các file hệ thống
}
';
		}
		foreach($data as $key=>$value){
			$value=str_replace('"', '\"', $value);
			$replaceValue=preg_match("/[^0-9]/", $value) || substr($value, 0, 1)==0 && strlen($value)>1 || $value=="" ? '"'.$value.'"' : $value;
			if($value==="false"){
				$replaceValue="false";
			}else if( $value==="true" ){
				$replaceValue="true";
			}else if( $value==="null" ){
				$replaceValue="null";
			}
			$configContents = str_replace("define( '", "define('", $configContents);
			$configContents = preg_replace('/define\((\'|\")'.$key.'(\'|\"),(.+?)\)/i', 'define("'.$key.'", '.$replaceValue.')', $configContents);
		}
		file_put_contents($configFile, $configContents);
	}

	// Import database
	public static function importDatabase($w, $connection){
		$home="http://{$w->domain}";
		$publicRoot = self::userPublic($w->domain);
		$databaseFileNameLoad = glob(SYSTEM_ROOT."/builder/database-setup/*_w{$w->id}.sql")[0] ?? glob(SYSTEM_ROOT."/builder/database-setup/*_u{$w->id}.sql")[0] ?? null;
		$databaseFileName     = $databaseFileNameLoad ?? SYSTEM_ROOT."/builder/database-template/{$w->app}.sql";
		// var_dump all info 
		var_dump($w, $connection, $databaseFileName, $databaseFileNameLoad);
		die();
		if( !file_exists($databaseFileName) ){
			return "
				<div>
					Không tìm thấy file database:
				</div>
				<div>
					<b>".SYSTEM_ROOT."/builder/database-setup/".CONFIG["BUILDER"]["username"]."_u{$w->id}.sql</b>
					<br>
					Hoặc
					<br>
					<b>{$databaseFileName}</b>
				</div>
			";
		}
		$data = [
			"user"     => $w->user_login,
			"email"    => $w->user_login,
			"name"     => user("name"),
			"password" => $w->password
		];
		$DBContents = file_get_contents($databaseFileName, FILE_USE_INCLUDE_PATH);
		$DB         = explode(";\n", $DBContents);
		foreach($DB as $query){
			if( empty($getFirstTable[1]) ){
				preg_match("/CREATE TABLE `([a-zA-Z0-9].+?)_users`/i", $DBContents, $getFirstTable);
			}else{
				break;
			}
			/*
			if( strlen($query)>5 ){
				//$query = preg_replace("/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.ninhbinhweb\.(com|net)/i", "", $query);
				DB::table("empty", $connection)->sqlQuery($query);
			}
			//echo '<textarea class="width-100" rows="10">'.$query.'</textarea>';
			*/
		}
		shell_exec("mysql -u {$connection['db_user']} -p{$connection['db_password']} {$connection['db_name']} --default-character-set=utf8mb4 < $databaseFileName");
		//Cập nhật lại prefix
		if( empty($getFirstTable[1]) ){
			return 'Database bị lỗi, không thể cập nhật prefix<br>'.$databaseFileName;
		}
		$tablePrefix=explode("_",$getFirstTable[1])[0];
		$configFile=$publicRoot."/wp-config.php";
		$configContents=file_get_contents($configFile, FILE_USE_INCLUDE_PATH);
		$configContents=preg_replace("/\\\$table_prefix(.+?)\;/", '$table_prefix = \''.$tablePrefix.'_\';', $configContents);
		file_put_contents($configFile, $configContents);

		//Cập nhật lại dữ liệu
		DB::table("{$tablePrefix}_options", $connection)->where("option_name", "siteurl")->update(["option_value"=>$home]);
		DB::table("{$tablePrefix}_options", $connection)->where("option_name", "home")->update(["option_value"=>$home]);
		DB::table("{$tablePrefix}_options", $connection)->where("option_name", "admin_email")->update(["option_value"=>$data["email"]]);
		$firstID = DB::table("{$tablePrefix}_users", $connection)->orderBy('ID', 'ASC')->first()->ID;
		DB::table("{$tablePrefix}_users", $connection)->where("ID", $firstID)->update([
			"user_login"=>$data["user"],
			"user_email"=>$data["email"],
			"display_name"=>$data["name"],
			"user_pass"=> $data["password"]
		]);
		DB::table("{$tablePrefix}_usermeta", $connection)
			->where('meta_key', $tablePrefix.'user_level')
			->where('user_id', $firstID)
			->update(['meta_value' => 10]);
		if( file_exists($databaseFileNameLoad) ){
			unlink($databaseFileNameLoad);
		}
	}

	/** Đường dẫn thư mục public của domain (theo panel: DA/CyberPanel) */
	public static function userPublic($domain){
		return self::panel()->getPublicPath($domain);
	}

	//Xóa tài khoản
	public static function delete($domain){
		$dbName=webConfig($domain, "DB_USER");
		
		//Xóa bản backup database
		$web = BuilderDomain::where("domain", $domain)->first();
		$backupPath = SYSTEM_ROOT."/builder/database-template/".($web->app ?? null).".sql";
		if( ($web->app_price ?? 0) > 0  && file_exists($backupPath) ){
			unlink($backupPath);
		}
		$templatePath = SYSTEM_ROOT."/builder/domains/".$domain;
		if( is_dir($templatePath) ){
			Folder::clear($templatePath, false);
		}
		self::panel()->deleteDomain($domain);
		self::panel()->deleteDatabase($dbName);
	}

	//Đổi tên miền
	public static function change($old, $new){
		return self::panel()->changeDomain($old, $new);
	}

	//Cài chứng chỉ SSL
	public static function createSSL($domain){
		return self::panel()->createSSL($domain);
	}

	//Lưu chứng chỉ SSL
	public static function updateSSL($domain, $privateKey, $certificate, $cacert){
		self::panel()->updateSSL($domain, $privateKey, $certificate, $cacert);
	}

	// Hiện thông báo setup
	public static function loading($params){
		extract($params);
		Assets::footer("/assets/loading-bar/loading-bar.css", "/assets/loading-bar/loading-bar.js");
		$out = '
		<style>
			body{
				overflow: hidden;
			}
			.admin-left,
			.admin-header{
				display: none !important
			}
			.website-setup-step{
				padding: 20px 10px;
				color: #B2B2B2;
				margin-bottom: 15px;
				border-radius: 10px
			}
			.website-setup-step-line{
				width: 80%;
				height: 3px;
				background: #B2B2B2;
				margin: auto
			}

			.website-setup-step .progress-circle[data-value="20"],
			.website-setup-step .progress-circle[data-value="100"] {
				background-image: -webkit-linear-gradient(left, transparent 50%, #B2B2B2 0);
				background-image: linear-gradient(to right, transparent 50%, #B2B2B2 0);
			}
			.website-setup-step .progress-circle[data-value="20"]:before,
			.website-setup-step .progress-circle[data-value="100"]:before {
				background-color: #B2B2B2 !important
			}
			.website-setup-step-active{
				color: #4CC9D8;
			}
			.website-setup-step-active .progress-circle {
				background-image: -webkit-linear-gradient(left, transparent 50%, #4CC9D8 0);
				background-image: linear-gradient(to right, transparent 50%, #4CC9D8 0);
			}
			/*.website-setup-step-active .progress-circle:before {
				background-color: #4CC9D8 !important
			}*/
			.website-setup-step-active .website-setup-step-line{
				background: #4CC9D8;
			}
			.website-setup-step-item>.flex>div{
				padding: 0 5px;
			}
			@media(max-width: 768px){
				.modal-setup .modal-body{
					padding: 10px !important;
				}
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function(){
				var bar1 = new ldBar("#loading-bar");
				var bar2 = document.getElementById("loading-bar").ldBar;
				var percent = 0;
				setInterval(function(){
					percent += 4;
					bar1.set(percent);
				}, 1e3);
			});
		</script>
		<section class="modal modal-setup" style="background-color: white; animation: none;">
			<div style="max-width: 1100px; margin: 10px auto;">
				'.Assets::show("/assets/loading-bar/progress-circle.css").'
				<div class="website-setup-step" style="padding: 5px">
					<div class="flex flex-middle hidden-small hidden-medium">
						<div class="width-20 center website-setup-step-item">
							<div class="progress-circle" data-value="20">
								<div>
									<span>
										<i>BƯỚC 1</i>
										<br>
										CHỌN GIAO DIỆN
									</span>
								</div>
							</div>
						</div>
						<div class="width-20 center">
							<div class="website-setup-step-line">
							</div>
						</div>
						<div class="width-20 center website-setup-step-item website-setup-step-active">
							<div class="progress-circle" data-value="80">
								<div>
									<span>
										<i>BƯỚC 2</i>
										<br>
										TẠO WEBSITE
									</span>
								</div>
							</div>
						</div>
						<div class="width-20 center">
							<div class="website-setup-step-line">
							</div>
						</div>
						<div class="width-20 center website-setup-step-item">
							<div class="progress-circle" data-bg="red" data-value="100">
								<div>
									<span>
										<i>BƯỚC 3</i>
										<br>
										HOÀN TẤT
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-body" style="padding: 200px 0;">
				<div class="modal-content center" style="background: transparent">
					<div>
						<img src="'.Storage::setting('builder_setting_setup_image', '/files/uploads/2019/09/gears.gif').'" style="max-height: 150px">
					</div>
					<div class="center primary-color" style="padding: 10px 20px 60px 20px; font-size: 35px; letter-spacing: 3px; font-weight: bold">
						<div class="ldBar" data-aspect-ratio="none" data-preset="rainbow" data-stroke-width="16" data-stroke-trail-width="5" style="width: 60%;margin: auto" id="loading-bar" data-value="0"></div>
					</div>
					<div id="website-setup-progress" class="pd-10" style="font-size: 16px">
						<div>'.$msg.'</div>
						<div class="pd-10'.(empty($status) ? ' hidden' : '').'" style="font-size: 13px; color: red">'.$status.'</div>
					</div>
					'.( permission("website_manager") ? Assets::footer('
						<div style="position: fixed; right: 5px; bottom: 5px; font-size: 14px;z-index: 19999999999997">
							<a class="btn-danger" href="/admin/WebsiteTools?delete_website='.GET("id").'">
								<i class="fa fa-trash"></i>
								Xóa website
							</a>
						</div>
					') : '' ).'
				</div>
			</div>
			<div class="hidden" id="online-chat-disabled"></div>
		</section>

		<style type="text/css">
			#online-chat{
				display: none
			}
		</style>

		<script type="text/javascript">
			var _installSuccessful = false;
			function setupRefresh(){
				if(_installSuccessful){
					return;
				}
				var data = {"setup": 1};
				$.ajax({
					"url"  : "",
					"data" : {"setup": 1},
					"type" : "POST",
					success: function(response){
						var el = $(response).find("#website-setup-progress").html();
						if(typeof el == "undefined"){
							var el = $(response).find("#create-website-pending .center").html();
						}
						var active = $(response).find(".website-goto").length;
						if(typeof el == "undefined" && active > 0){
							_installSuccessful = true;
							setTimeout(function(){
								location.reload();
							}, 5e3);
						}else{
							setTimeout(function(){
								setupRefresh();
							}, 2e3);
							$("#website-setup-progress").html(el);
						}
					},
					complete: function(){
						
					},
					error: function(error){
						console.log(error);
						setTimeout(function(){
							setupRefresh();
						}, 5e3);
					}
				});
			}
			$(document).ready(function(){
				setupRefresh();
			});
		</script>
		';
		return $out;
	}



	/**
	 * Tạo tài khoản panel (DirectAdmin / CyberPanel).
	 * $params: username, email, passwd (hoặc password), domain (DA), package, passwd2 (DA), ...
	 * Trả về null nếu thành công, string lỗi nếu thất bại.
	 */
	public static function createUser(array $params = []) {
		$defaults = [
			'username' => 'u005',
			'email'    => 'kiemcoder@gmail.com',
			'passwd'   => 'kiemcoder1',
			'passwd2'  => 'kiemcoder1',
			'domain'   => 'demo.azit.vn',
			'package'  => 'builder',
			'ip'       => $_SERVER['SERVER_ADDR'] ?? '',
			'notify'   => 'no'
		];
		return self::panel()->createUser(array_merge($defaults, $params));
	}

	/**
	 * Xóa tài khoản panel theo username (DirectAdmin / CyberPanel).
	 * Trả về array response từ panel.
	 */
	public static function deleteUser(string $username = 'u003') {
		return self::panel()->deleteUser($username);
	}

	/*
	 * Upload file
	 */
	public static function ftpUpload($params = []){
		extract($params);
		if( ftp_put(self::ftpConnect(), $savePath, $sourceFile, FTP_BINARY) ){
			$status = true;
		}else{
			$status = false;
		}
		ftp_close( self::ftpConnect() );
		return $status;
	}


	/**
	 * Gọi API quản lý file / giải nén (DirectAdmin / CyberPanel).
	 * $params: tùy panel (DA: action, path, ...; CyberPanel: path, fileName, extractPath).
	 * $account: [username, password] cho user cụ thể; mặc định dùng CONFIG nếu rỗng.
	 * Trả về array response từ panel.
	 */
	public static function extractFile(array $params = [], array $account = []) {
		$defaultAccount = [
			'username' => 'u005',
			'password' => 'kiemcoder1'
		];
		return self::panel()->extractFile($params, !empty($account) ? $account : $defaultAccount);
	}

	/*
	 * Copy thư mục
	 */
	public static function copySourceCode($params = []){
		extract($params);
		// Backup files
		$rootPath = realpath($sourcePath);

		// Initialize archive object
		$backupFile="/backups/backup-test-".date("H-i_d-m-Y").".zip";
		$zip = new ZipArchive();
		$zip->open(PUBLIC_ROOT.$backupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

		// Create recursive directory iterator
		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($rootPath),
			\RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file)
		{
		    // Skip directories (they would be added automatically)
			if (!$file->isDir())
			{
		        // Get real and relative path for current file
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($rootPath) + 1);

		        // Add current file to archive
		        if($filePath!=$sourcePath."/wp-config.php"){
					$zip->addFile($filePath, $relativePath);
		        }
			}
		}
		return $status ?? 1;
	}

	/*
	 * Lấy nội dung của 1 file
	 */
	public static function ftpReadFile($path){
		$temp_path = tempnam(sys_get_temp_dir(), "ftp");
		if( ftp_get(self::ftpConnect(), $temp_path, $path, FTP_BINARY) ){
			$contents = file_get_contents($temp_path);
			unlink($temp_path);
		}else{
			$contents = false;
		}
		ftp_close( self::ftpConnect() );
		return $contents;
	}

	/*
	 * Xóa file
	 */
	public static function ftpDelete($path){
		if( ftp_delete(self::ftpConnect(), $path) ){
			$status = true;
		}else{
			$status = false;
		}
		ftp_close( self::ftpConnect() );
		return $status;
	}

	/*
	 * Kết nối FTP
	 */
	public static function ftpConnect(){
		$ftpConfig = [
			'username' => 'u005',
			'password' => 'kiemcoder1'
		];
		$ftp_conn   = ftp_connect(DOMAIN) or die("Không thể kết nối tới máy chủ FTP, vui lòng thử lại!");
		ftp_login($ftp_conn, $ftpConfig['username'], $ftpConfig['password']);
		return $ftp_conn;
	}

	/** Trả về adapter panel (DirectAdmin hoặc CyberPanel) theo config */
	private static function panel() {
		return \classes\panel\PanelManager::get();
	}

}