<?php
	use models\BuilderDomain;
	use models\Users;
	use mailer\WebMail;
	use classes\WebBuilder;
	Assets::footer(
		"/assets/chart/progress.js",
		"/assets/chart/progress.css",
		"/assets/website-manager/style.css",
		"/assets/website-manager/script.js",
	);
	$defaultCategories = array_key_first( WEB_BUILDER["categories"] );
	$delayCreate=permission("seller|agency|website_manager") ? 0 : Storage::setting("builder_parameters_delay_create", 60);
	$websitePending=BuilderDomain::where( "users_id", user("id") )->where("created_at", ">", date("Y-m-d H:i:s", time()-$delayCreate) )->first();
	global $Schedule;
	$Schedule->d( function(){
		Folder::clear( PUBLIC_ROOT."/backups" );
		Users::where("id", ">", 0)->update( ["website_created_today"=>0] );
	} );
	$configSSLPrice = WEB_BUILDER["sslPrice"];
	$configBackupPrice = WEB_BUILDER["backupPrice"];
	$discountText = '';
	// Giảm giá cho cộng tác viên
	if( permission("seller") ){
		$discount = (int)(WEB_BUILDER['discount']['seller']);
		$configSSLPrice = $configSSLPrice - ($configSSLPrice * $discount / 100);
		$configBackupPrice = $configBackupPrice - ($configBackupPrice * $discount / 100);
	}
	// Giảm giá cho đại lý
	if( permission("agency") ){
		$discount = (int)(WEB_BUILDER['discount']['agency']);
		$configSSLPrice = $configSSLPrice - ($configSSLPrice * $discount / 100);
		$configBackupPrice = $configBackupPrice - ($configBackupPrice * $discount / 100);
	}
	if( isset($discount) ){
		$discountText = ' (Đã giảm '.$discount.' %)';
	}
	if( permission("website_manager") ){
		if( empty( user('check_admin') ) ){
			Users::where("id", user("id") )->update(["check_admin"=>md5( randomString(20) )]);
		}
	}
?>
<?php if( isset($_POST["create"]) ): ?>
	<?php
		if( isset($websitePending->id) ){
			$createWebsiteMsg="Vui lòng chờ vài giây để tạo tiếp";
		}
		$webConfig=[];
		$template=BuilderDomain::where("app_name", "!=", "")->where("id", POST("template") )->first();
		if( !permission("website_manager") && empty($template->id) ){
			$createWebsiteMsg="Dữ liệu không hợp lệ";
		}
		$domainName=vnStrFilter( POST("domain"), "-");
		$webConfig["domain"]=$domainName.".".DOMAIN;
		if( empty($domainName)  ){
			$createWebsiteMsg="Vui lòng nhập tên miền bạn muốn tạo";
		}else if( BuilderDomain::where("domain", $webConfig["domain"])->orWhere("default_domain", $webConfig["domain"])->exists() || in_array($domainName, ["builder", "www", "hacker"]) || is_dir( WebBuilder::userPublic($webConfig["domain"]) ) ) {
			$createWebsiteMsg="Tên miền này đã có người đăng ký, hãy chọn tên khác!";
		}else if( $domainName != POST("domain") ){
			$createWebsiteMsg="Hãy nhập tên miền tạm thời dạng: <b>".vnStrFilter( explode( ".", POST("domain") )[0] )."</b>";
			if( strpos( POST("domain"), ".")!==false ){
				$createWebsiteMsg.="<br>Bạn có thể đổi sang <b>".POST("domain")."</b> sau khi đã khởi tạo xong";
			}
		}
		if( !permission("member") ){
			$createWebsiteMsg='Bạn cần đăng ký 1 tài khoản để tạo website <a target="_blank" href="/user/register">Đăng ký ngay</a>';
		}
		if(BuilderDomain::where("users_id", user("id") )->total()>=Storage::setting("builder_parameters_quantity_website", 1) && !permission("seller|agency|website_manager") ){
			$createWebsiteMsg="Bạn chỉ được tạo tối đa ".Storage::setting("builder_parameters_quantity_website", 1)." website, hãy nâng cấp gói seller!";
		}
		if( user("website_created_today")>=5 && !permission("seller|agency|website_manager") ){
			$createWebsiteMsg="Bạn chỉ được tạo thử tối đa 5 lần/1 ngày, vui lòng tạo tiếp vào ngày mai!";
		}
		if( empty( POST("password") ) ){
			$createWebsiteMsg = "Vui lòng nhập mật khẩu";
		}
		$webConfig["app"]=$template->app ?? POST("app");
		$webConfig["expired"] = time()+3600*24*(WEB_BUILDER["expired"]);
		if( permission("website_manager") ){
			$webConfig["expired"] = strtotime('+1000 years'); // Web được tạo bởi quản lý
		}else if( permission("agency|seller") ){
			//$webConfig["expired"] = strtotime('+45 days'); // Web được tạo bởi đại lý & cộng tác viên
		}
		$webConfig["password"] = md5( POST("password") );
		//Tạo web mẫu
		if( empty($createWebsiteMsg) && permission("website_manager") && POST('template') == 0 ){
			if( empty( POST("customer") ) ){
				$webConfig["app"] = $domainName;
				if( BuilderDomain::where("app", vnStrFilter($webConfig["app"], "_") )->exists() ){
					$createWebsiteMsg="Tên theme đã tồn tại";
				}
			}
			if( empty($_FILES["source"]["name"]) ){
				$createWebsiteMsg="Vui lòng tải lên mã nguồn.zip";
			}
			if( empty($_FILES["database"]["name"]) ){
				$createWebsiteMsg="Vui lòng tải lên database.sql";
			}
			$webConfig["expired"] = strtotime('+1000 years');
		}
		if( !permission("website_manager") || POST('template') != 0 ){
			if( POST("password") != POST("password2") ){
				$createWebsiteMsg = "Mật khẩu nhập lại không khớp";
			}
			if( !filter_var( POST("user_login") , FILTER_VALIDATE_EMAIL) ){
				$createWebsiteMsg = "Email đăng nhập không hợp lệ";
			}
		}
		//Tiến hành khởi tạo nếu không có lỗi
		if(empty($createWebsiteMsg)){
			$webConfig["template"] = $template->id ?? 0;
			$webConfig["app_name"] = (permission("website_manager") && POST('template') == 0 && empty( POST("customer") ) ? "Tên web mẫu" : "");
			$webConfig["domainID"] = BuilderDomain::insertGetId([
				"domain"         => $webConfig["domain"],
				"default_domain" => $webConfig["domain"],
				"users_id"       => user("id"),
				"app"            => $webConfig["app"],
				"app_name"       => $webConfig["app_name"],
				"app_price"      => (permission("website_manager") && POST('template') == 0 && empty( POST("customer") ) ? 500000 : 0),
				"app_categories" => (permission("website_manager") && POST('template') == 0 && empty( POST("customer") ) ? $defaultCategories : ""),
				"ssl_type"       => 1,
				"expired"        => $webConfig["expired"],
				"user_login"     => POST("user_login", user("email") ),
				"password"       => $webConfig["password"],
				"created_at"     => timestamp(),
				"updated_at"     => timestamp()
			]);
			Users::where("id", user("id") )->update(["website_created_today"=>(INT)user("website_created_today")+1]);
			Users::updateStorage(user("id"), ["website_created_total"=>(INT)user("website_created_total")+1]);
			// Upload code & database web mẫu
			if( permission("website_manager") && isset($_FILES["source"]["name"]) ){
				//Nếu là tạo web mẫu
				if( !is_dir(SYSTEM_ROOT."/builder/database-template") ){
					mkdir(SYSTEM_ROOT."/builder/database-template", 0755, true);
				}
				if( !is_dir(SYSTEM_ROOT."/builder/database-setup") ){
					mkdir(SYSTEM_ROOT."/builder/database-setup", 0755, true);
				}
				$sourceFilePath = SYSTEM_ROOT."/builder/domains/".$webConfig["domain"]."/public_html";
				Folder::clear($sourceFilePath);
				if( empty($webConfig["app"]) ){
					// Web code riêng
					$databaseFile = SYSTEM_ROOT."/builder/database-setup/data_u".$webConfig["domainID"].".sql";
				}else{
					// Web mẫu
					$databaseFile = SYSTEM_ROOT."/builder/database-template/".$webConfig["app"].".sql";
				}
				move_uploaded_file($_FILES['database']['tmp_name'], $databaseFile);
				move_uploaded_file($_FILES['source']['tmp_name'], $sourceFilePath."/code.zip");
				//Giải nén code
				$zip = new ZipArchive();
				$x = $zip->open($sourceFilePath."/code.zip");
				if ($x === true) {
					$zip->extractTo($sourceFilePath);
					$zip->close();
					unlink($sourceFilePath."/code.zip");
				}
			}else if( !permission("website_manager") ){
				WebMail::send([
					"To"          => [user("email")],
					"Subject"     => "[".DOMAIN."] Tạo website thành công: #{$webConfig["domain"]}",
					"Body"        => '
					Kính gửi: '.user("name").'
					<br>
					Quý khách đã tạo website thành công
					<br>
					<span style="color: red">
					Quý khách có <b>'.WEB_BUILDER["expired"].'</b> ngày dùng thử website miễn phí.
					</span>
					<br>
					Hãy click vào link bên dưới để quản lý web nhé:
					<br>
					<a href="'.$webConfig["domain"].'/builder/installer?email='.urlencode(user("email")).'&name='.urlencode(user("name")).'">Click vào đây để quản lý web</a>
					<br>
					',
					"Attachments" => []
				]);
			}
		}
		if( empty($createWebsiteMsg) ){
			redirect(THIS_LINK, true, ($createWebsiteMsg??"") );
		}else{
			echo '<section><div id="create-website-error">'.$createWebsiteMsg.'</div></section>';
		}
	?>
<?php endif; ?>
<?php
	// Lấy thông tin website
	$w = BuilderDomain::select("*");
	if( !permission("website_manager") ){
		$w = $w->where("users_id", user("id"));
	}
	if( is_numeric( GET("id") ) ){
		$w = $w->where("id", GET("id") );
	}else{
		$w = $w->where("domain", GET("id") );
	}
	if( $w->total() == 0 ){
		redirect("/admin/WebsiteList", true);
	}
	$w = $w->first();
	echo '<script>document.title="'.strtoupper( explode(".", $w->domain)[0] ).' - Quản lý website";</script>';
	// Tính giá tiền gia hạn
	define("EXPIRY_DATE", WEB_BUILDER["expiry_date"][ ($_POST["website_renew"]["days"] ?? null) ] ?? ['roles' => 'member']);
	define("EXPIRY_DATE_DAYS", EXPIRY_DATE["days"] ?? 1);
	if( in_array( user('role'), (array)(WEB_BUILDER["package"][ $_POST["website_renew"]["package"] ?? null ]['roles'] ?? []) ) ){
		$getPackage = WEB_BUILDER["package"][ $_POST["website_renew"]["package"] ?? null ] ?? null;
		if( $w->package == ($_POST["website_renew"]["package"] ?? null) && $w->renew_price > 0 ){
			$getPackage['price'] = number_format($w->renew_price);
		}
		define("PACKAGE", $getPackage);
	}else{
		define("PACKAGE", ['price' => 0, 'name' => 'Free', 'disk' => 0]);
	}
	$webMoney = round( toNumber(PACKAGE["price"])/365*EXPIRY_DATE_DAYS, -3);
	$upgradeDisk=(int)webConfig($w->domain, "BUILDER_MAXDISK") - (WEB_BUILDER["package"][$w->package]['disk'] ?? WEB_BUILDER['disk']);
	$diskMoney = round( $upgradeDisk*WEB_BUILDER["upgradeDisk"]["price"]/365*EXPIRY_DATE_DAYS, -3);
	if( toNumber(PACKAGE['price']) > toNumber(WEB_BUILDER["package"][$w->package]['price'] ?? 0) ){
		$upgradeDisk = 0;
		$diskMoney = 0;
	}
	$sslMoney = $w->ssl_type != 1 || $w->domain == $w->default_domain ? 0 : round( WEB_BUILDER["sslPrice"]/365*EXPIRY_DATE_DAYS, -3);
	$themePrice = WEB_BUILDER["apps"][$w->app]["app_price"] ?? 1000000;
	$upgradeDiskPrice = WEB_BUILDER["upgradeDisk"]["price"];
	// Giảm giá cho cộng tác viên
	if( permission("seller") ){
		$discountForSeller = (int)(WEB_BUILDER['discount']['seller'] ?? 20);
		$webMoneySaleOffer = $webMoney - ($webMoney * $discountForSeller / 100);
		$sslMoney = $sslMoney - ($sslMoney * $discountForSeller / 100);
		$themePrice = $themePrice - ($themePrice * $discount / 100);
		$upgradeDiskPrice = $upgradeDiskPrice - ($upgradeDiskPrice * $discount / 100);
	}
	// Giảm giá cho đại lý
	if( permission("agency") ){
		$discountForAgency = (int)(WEB_BUILDER['discount']['agency'] ?? 30);
		$sslMoney = $sslMoney - ($sslMoney * $discountForAgency / 100);
		$themePrice = $themePrice - ($themePrice * $discount / 100);
		$upgradeDiskPrice = $upgradeDiskPrice - ($upgradeDiskPrice * $discount / 100);
	}
	if( in_array( user('role'), (array)EXPIRY_DATE['roles']) && (int)EXPIRY_DATE['sale_offer'] > 0 ){
		$webMoneySaleOffer = $webMoney - ($webMoney * (int)EXPIRY_DATE['sale_offer'] / 100);
	}
	$totalMoney = round( ($webMoneySaleOffer ?? $webMoney) + $diskMoney + $sslMoney, -3);
	
	// Kiểm tra tên miền đã thuộc hệ thống hiện tại hay chưa
	$correctDomain = (explode(".", $w->default_domain)[0]).".".DOMAIN; // Tên miền đang thuộc hệ thống hiện tại
	if($w->default_domain == $correctDomain){
		// Kết nối tới SQL database của web
		$connection = [
			'db_user'     => webConfig($w->domain, "DB_USER"),
			'db_password' => webConfig($w->domain, "DB_PASSWORD"),
			'db_name'     => webConfig($w->domain, "DB_NAME")
		];
		$prefix = webConfig($w->domain, 'table_prefix');
		$getLoginAdmin = DB::table("{$prefix}usermeta", $connection)
			->rightJoin("{$prefix}users", "{$prefix}users.ID", "=", "{$prefix}usermeta.user_id")
			->where("{$prefix}usermeta.meta_key", "{$prefix}user_level")
			->where("{$prefix}usermeta.meta_value", 10)
			->orderBy("{$prefix}usermeta.user_id", "ASC")
			->first(true);
		if( empty($prefix) ){
			echo '
				<div class="alert-danger">
					Hãy xóa website & khởi tạo lại nếu bị lỗi!
				</div>
			';
		}else if( !empty($getLoginAdmin->user_login) ){
			$userLogin = $getLoginAdmin->user_login;
			$userPassword = $getLoginAdmin->user_pass;
		}
		// Check khởi tạo website
		echo WebBuilder::setup($w, $userLogin ?? null, $connection);
	}else{
		// Chuyển web sang tên miền chính
		echo WebBuilder::moveToNewDomain($w, $correctDomain);
	}
	// Thao tác quản lý website
	if(isset($_POST["website"])){
		$data=(array)$_POST["website"];
		if( permission("website_manager") ){
			$getWeb=BuilderDomain::where("id", $data["id"]??0)->first(true);
		}else{
			$getWeb=BuilderDomain::where("id", $data["id"]??0)->where("users_id", user("id") )->first(true);
		}
		if( empty($getWeb->id) && !in_array($data["action"], ["create"]) ){
			die;
		}
		switch($data["action"]??""){
			
			//Đổi tên miền
			case "changeDomain":
				$newDomain = strtolower( trim($data["newDomain"], ".") );
				$newDomain = trim($newDomain);
				if( empty($newDomain) && isset($data['primary_domain']) ){
					// Chọn tên miền chính
					$newDomain = $_POST['website']['primary_domain'] ?? null;
					if( !in_array($newDomain, [$getWeb->default_domain, $getWeb->alias_domain, 'www.'.$getWeb->alias_domain]) ){
						die;
					}
					switch($newDomain){
						// Dùng tên miền đã trỏ (không có www)
						case $getWeb->alias_domain:
							WebBuilder::updateConfig($getWeb->domain, ["BUILDER_WWW" => "false"]);
							if( $getWeb->domain != $newDomain ){
								if( !empty(WebBuilder::change($getWeb->domain, $newDomain))){
									BuilderDomain::find($getWeb->id)->update(["domain"=>$newDomain]);
								}
							}
						break;
						// Dùng tên miền đã trỏ (có www)
						case 'www.'.$getWeb->alias_domain:
							WebBuilder::updateConfig($getWeb->domain, ["BUILDER_WWW"=>"true"]);
							if( $getWeb->domain != $getWeb->alias_domain ){
								$newDomain = str_replace('www.', '', $newDomain);
								if( !empty(WebBuilder::change($getWeb->domain, $newDomain))){
									BuilderDomain::find($getWeb->id)->update(["domain"=>$newDomain]);
								}
							}
						break;
						// Dùng tên miền mặc định hệ thống
						case $getWeb->default_domain:
							WebBuilder::updateConfig($getWeb->domain, ["BUILDER_WWW"=>"false", "BUILDER_SSL" => "true"]);
							if( $getWeb->domain != $getWeb->default_domain ){
								if( !empty(WebBuilder::change($getWeb->domain, $newDomain))){
									BuilderDomain::find($getWeb->id)->update(["domain" => $newDomain, "ssl_type" => 1]);
								}
							}
						break;
					}
				}else{
					// Thêm tên miền mới
					//Kiểm tra tên miền đã trỏ về IP chưa
					$domainIP = gethostbyname($newDomain);
					if( $domainIP!=$_SERVER["SERVER_ADDR"] || !checkdnsrr($newDomain.".", 'ANY') ){
						$websiteActionMsg="
							<div>Vui lòng trỏ tên miền <b>{$newDomain}</b> sang địa chỉ IP: <b>".$_SERVER["SERVER_ADDR"]."</b></div>
							<div>Nếu đã trỏ, vui lòng đợi khoảng 5 phút trước khi thêm.</div>
							<div>Hãy chat với hỗ trợ nếu bạn chưa biết trỏ.</div>
						";
					}
					//Kiểm tra tên miền đã tồn tại trên hệ thống
					$checkDomainCorrect=file_get_contents_curl("http://$newDomain/add-domain.txt");
					if($domainIP==$_SERVER["SERVER_ADDR"] && $checkDomainCorrect!="@true" ){
						$websiteActionMsg="
							<div>Tên miền này đang hoạt động tại hệ thống</div>
							<div>Vui lòng liên hệ hỗ trợ</div>
						";
					}
					//Tên miền có ký tự không hợp lệ
					if( strpos($newDomain, ".")===FALSE || $newDomain==DOMAIN ||  is_numeric( vnStrFilter($newDomain,"") ) ){
						$websiteActionMsg="Tên miền không hợp lệ";
					}
					if( strpos($newDomain, ".".DOMAIN)!==FALSE && !permission("website_manager") ){
						$websiteActionMsg="Tên miền không hợp lệ";
					}
					//Tên miền đã được dùng
					if( BuilderDomain::where("domain", $newDomain)->orWhere("alias_domain", $newDomain)->orWhere("default_domain", $newDomain)->exists() ){
						$websiteActionMsg="Tên miền đã tồn tại trên hệ thống, Hãy liên hệ để được trợ giúp";
					}
					
					//Tiến hành đổi tên miền
					if( empty($websiteActionMsg) ){
						if( !empty(WebBuilder::change($getWeb->domain, $newDomain))){
							WebBuilder::updateConfig($newDomain, ["BUILDER_SSL"=>"false"]);
							BuilderDomain::find($getWeb->id)->update(["domain"=>$newDomain, "alias_domain"=>$newDomain, "ssl_type" => 0]);
						}
					}
				}
				if( empty($websiteActionMsg) ){
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Thay đổi tên miền thành công'
					];
				}
			break;
			//Xóa web
			case "delete":
				if( !empty($getWeb->app_name) ){
					$checkWebUsedApp=BuilderDomain::where("app", $getWeb->app)->total()-1;
					if($checkWebUsedApp>0){
						$websiteActionMsg = '<a href="/admin/WebsiteList?app='.$getWeb->app.'" target="_blank">Không thể xóa web mẫu vì đang có '.$checkWebUsedApp.' website đang dùng mẫu này, click để xem</a>';
					}
				}
				if( !captchaCorrect($_POST["captcha"]) ){
					$websiteActionMsg = 'Mã xác minh không hợp lệ';
				}
				if( empty($websiteActionMsg) ){
					WebBuilder::delete($getWeb->domain);
					if( $getWeb->app_price > 0 && file_exists(PUBLIC_ROOT."/files/builder/images/template/{$getWeb->app}.jpg") ){
						unlink(PUBLIC_ROOT."/files/builder/images/template/{$getWeb->app}.jpg");
					}
					BuilderDomain::destroy($getWeb->id);
				}
				if( empty($websiteActionMsg) ){
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Xóa website thành công'
					];
				}
			break;
			//Nâng cấp dung lượng
			case "upgradeDisk":
				$diskMax=WEB_BUILDER["upgradeDisk"]["max"];
				$packedPrice=$data["upgradeDisk"]*$upgradeDiskPrice;
				$websiteActionMsg=checkMoneyCorrect($packedPrice);
				if($data["upgradeDisk"]+webConfig($getWeb->domain, "BUILDER_MAXDISK")-(WEB_BUILDER["package"][$w->package]['disk'] ?? WEB_BUILDER['disk'])>$diskMax){
					$websiteActionMsg="Bạn chỉ được nâng cấp tối đa {$diskMax} Mb";
				}else if( $data["upgradeDisk"]<50 ){
					$websiteActionMsg="Dung lượng tối thiểu phải trên 50Mb";
				}
				if(empty($websiteActionMsg)){
					$newDisk=( webConfig($getWeb->domain, "BUILDER_MAXDISK")+$data["upgradeDisk"] );
					if( !permission("website_manager") ){
						userPayment($packedPrice);
						userPaymentHistory([
							"name"     => "Mua thêm dung lượng",
							"amount"   => -$packedPrice,
							"note"     => "Mua <b>{$data["upgradeDisk"]}</b>Mb data cho website <b>{$getWeb->domain}</b>",
							"users_id" => null
						]);
					}
					WebBuilder::updateConfig($getWeb->domain, ["BUILDER_MAXDISK"=>$newDisk]);
					BuilderDomain::find($getWeb->id)->update(["disk"=>$newDisk]);
				}
				if( empty($websiteActionMsg) ){
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Nâng cấp dung lượng thành công'
					];
				}
			break;
			//Nâng cấp dung lượng
			case "changeDisk":
				if(empty($websiteActionMsg)){
					$newDisk = $data["changeDisk"];
					WebBuilder::updateConfig($getWeb->domain, ["BUILDER_MAXDISK"=>$newDisk]);
					BuilderDomain::find($getWeb->id)->update(["disk"=>$newDisk]);
				}
				if( empty($websiteActionMsg) ){
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Thay đổi dung lượng thành công'
					];
				}
			break;
			//Gia hạn web
			case "renew":
				$packedPrice = $totalMoneySaleOffer ?? $totalMoney;
				$websiteActionMsg=checkMoneyCorrect($packedPrice);
				if( empty(EXPIRY_DATE) || empty(PACKAGE) ){
					$websiteActionMsg = "Dữ liệu không hợp lệ!";
				}
				if( empty( $_POST["website_renew"]["package"] ) ){
					$websiteActionMsg = 'Vui lòng chọn gói cần gia hạn';
				}else if( $_POST["website_renew"]["package"] == $getWeb->package ){
					//$websiteActionMsg = "Quý khách đang sử dụng gói này!";
				}
				if(empty($websiteActionMsg)){
					$newExpired=($getWeb->expired+3600*24*EXPIRY_DATE_DAYS);
					if( !permission("website_manager") ){
						userPayment($packedPrice);
						userPaymentHistory([
							"name"     => "Gia hạn website",
							"amount"   => -$packedPrice,
							"note"     => "Website: <b>{$getWeb->domain}</b><br>Gói: ".PACKAGE["name"]." <br>Thời hạn: ".EXPIRY_DATE["name"]."",
							"users_id" => null
						]);
					}
					WebBuilder::updateConfig($getWeb->domain, ["BUILDER_EXPIRED" => $newExpired, "BUILDER_MAXDISK" => (int)PACKAGE["disk"] + $upgradeDisk]);
					BuilderDomain::find($getWeb->id)->update([
						"expired"           => $newExpired,
						"package"           => $_POST["website_renew"]["package"],
						"renew_price"       => toNumber(PACKAGE["price"]),
						"contact_status"    => 0,
						"contact_detail"    => null,
						"expiration_notice" => 0
					]);
				}
				if( empty($websiteActionMsg) ){
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Gia hạn website thành công'
					];
				}
			break;
			//Tải code
			case "backup":
				$packedPrice=$configBackupPrice;
				$websiteActionMsg=checkMoneyCorrect($packedPrice);
				if( strlen($getWeb->package) == 0 ){
					$websiteActionMsg="Vui lòng gia hạn website để sử dụng dịch vụ sao lưu";
				}
				if(empty($websiteActionMsg)){
					if( !permission("website_manager") ){
						userPayment($packedPrice);
						userPaymentHistory([
							"name"     => "Sao lưu website",
							"amount"   => -$packedPrice,
							"note"     => "Mua dịch vụ sao lưu cho website <b>{$getWeb->domain}</b>",
							"users_id" => null
						]);
					}
					BuilderDomain::find($getWeb->id)->update([
						"backup" => 1,
					]);
				}
				if( empty($websiteActionMsg) ){
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Mua dịch vụ sao lưu thành công'
					];
				}
			break;
			// Mua code
			case "buyCode":
				$packedPrice = $themePrice;
				$websiteActionMsg = checkMoneyCorrect($packedPrice);
				if(empty($websiteActionMsg)){
					if( !permission("website_manager") ){
						userPayment($packedPrice);
						userPaymentHistory([
							"name"     => "Mua giao diện",
							"amount"   => -$packedPrice,
							"note"     => "Mua giao diện website <b>{$getWeb->domain}</b>",
							"users_id" => null
						]);
					}
					BuilderDomain::find($getWeb->id)->update([
						"backup" => 1,
					]);
				}
				if( empty($websiteActionMsg) ){
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Mua giao diện thành công'
					];
				}
			break;
			//Tùy chỉnh cấu hình
			case "manager":
				$webConfig=[];
				foreach(["WWW"] as $key){
					$webConfig[$key]=vnStrFilter($data["config"][$key]);
				}
				WebBuilder::updateConfig($getWeb->domain, $webConfig);
			break;
			//Tùy chỉnh web mẫu
			case "template":
				if( isset($data["config"]["app_name"]) && permission("website_manager") ){
					$appPrice = toNumber($data["config"]["app_price"] ?? 50000);
					if( empty($appPrice) ){
						$appPrice = 50000;
					}
					BuilderDomain::find($getWeb->id)->update([
						"app_id"          => $data["config"]["app_id"],
						"app_name"        => empty($data["config"]["app_name"]) ? $getWeb->app_name : $data["config"]["app_name"],
						"app_price"       => $appPrice,
						"app_categories"  => $data["config"]["app_categories"],
						"app_description" => $data["config"]["app_description"],
					]);
				}
			break;
			//Cài SSL
			case "ssl":
				switch($data["ssl_type"]){
					//Mua SSL
					case 1:
						// Nếu là domain mặc định
						if($getWeb->domain != $getWeb->default_domain){
							$packedPrice=$getWeb->ssl_type==1 ? 0 : $configSSLPrice;
							$websiteActionMsg=checkMoneyCorrect($packedPrice);
							$domainIP=gethostbyname("www.".$getWeb->domain);
							if( $domainIP!=$_SERVER["SERVER_ADDR"] || !checkdnsrr("www.".$getWeb->domain.".", 'ANY') ){
								$websiteActionMsg="Vui lòng trỏ cả <b>www.</b> sang IP: ".$_SERVER["SERVER_ADDR"];
							}
							if( empty($websiteActionMsg) ){
								$createSSL = WebBuilder::createSSL($getWeb->domain);
								if( !empty($createSSL["error"]) ){
									// Lỗi cài
									$websiteActionMsg = "<b>Vui lòng ấn lại một lần nữa</b><br><br> <i>".$createSSL["text"].": </i><br>".$createSSL["details"]."";
								}else{
									// Cài xong
									if( !permission("website_manager") && $packedPrice > 0 ){
										userPayment($packedPrice);
										userPaymentHistory([
											"name"     => "Mua Chứng chỉ SSL",
											"amount"   => -$packedPrice,
											"note"     => "Mua dịch vụ chứng chỉ SSL cho website <b>{$getWeb->domain}</b>",
											"users_id" => null
										]);
									}
								}
							}
						}
					break;
					//Dùng SSL riêng
					case 2:
						if( strpos($data["ssl_certificate"], "----")===false || strpos($data["ssl_cacert"], "----")===false ){
							$websiteActionMsg="Chứng chỉ SSL không hợp lệ!";
						}else{
							WebBuilder::updateSSL($getWeb->domain, $data["ssl_private_key"], $data["ssl_certificate"], $data["ssl_cacert"]);
						}
					break;
				}
				if( empty($websiteActionMsg) ){
					BuilderDomain::find($getWeb->id)->update([
						"ssl_private_key" => $data["ssl_private_key"],
						"ssl_certificate" => $data["ssl_certificate"],
						"ssl_cacert"      => $data["ssl_cacert"],
						"ssl_type"        => $data["ssl_type"]
					]);
					if( $data["ssl_type"] > 0 ){
						$sslConfig = "true";
					}else{
						$sslConfig = "false";
					}
					WebBuilder::updateConfig($getWeb->domain, ["BUILDER_SSL"=>$sslConfig]);
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Cài đặt chứng chỉ SSL thành công, liên hệ nếu gặp lỗi'
					];
				}
				if( empty($websiteActionMsg) ){
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Cài đặt chứng chỉ SSL thành công, liên hệ nếu gặp lỗi'
					];
				}
			break;
			// Đổi ngày hết hạn website
			case "change_expired":
				if( permission("website_manager") ){
					$date = POST('change_expired');
					$date = '23:59:59 '.str_replace('/', '-', $date);
					$date = strtotime($date);
					if( $date < time() ){
						$websiteActionMsg = 'Vui lòng chọn hạn sử dụng hợp lệ';
					}else{
						WebBuilder::updateConfig($getWeb->domain, ["BUILDER_EXPIRED" => $date]);
						BuilderDomain::find($getWeb->id)->update([
							"expired"     => $date,
							"package"     => empty($_POST['package']) ? null : $_POST['package'],
							"renew_price" => toNumber( POST('renew_price') ),
							"contact_status" => 0,
							"contact_detail" => null
						]);
					}
				}
				if( empty($websiteActionMsg) ){
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Đổi hạn sử dụng website thành công'
					];
				}
			break;
			// Đổi mật khẩu web
			case "changePassword":
				if( empty($userLogin) ){
					$websiteActionMsg = 'Website chưa được khởi tạo';
				}
				$userLoginNew = $_POST["change_password"]["user"];
				$userPassword = md5($_POST["change_password"]["password"]);
				if( empty($userLoginNew) || empty($_POST["change_password"]["password"]) ){
					$websiteActionMsg = 'Vui lòng nhập mật khẩu và tên đăng nhập';
				}
				if( empty($websiteActionMsg) ){
					DB::table("".webConfig($w->domain, "table_prefix")."users", $connection)
						->where("user_login", $userLogin)
						->update([
							"user_login" => $userLoginNew,
							"user_pass"  => $userPassword
						]);
				}
				if( empty($websiteActionMsg) ){
					$_SESSION['notify'] = [
						'status' => 'success',
						'msg'    => 'Đổi mật khẩu website thành công'
					];
				}
			break;
		}
		if( empty($websiteActionMsg) ){
			die;
		}
	}
	// Load phần gia hạn
	if( isset($_POST["website_renew"]["days"]) && empty($_POST["website"]["action"]) ){
		returnData('
			<div id="website-renew-package">
				'.( isset($_POST["website_renew"]) ? '
					'.call_user_func(function($w, $webMoney, $diskMoney, $sslMoney, $upgradeDisk, $totalMoney, $totalMoneySaleOffer, $webMoneySaleOffer){
						$out = '';
						if( empty( WEB_BUILDER["package"][ $_POST["website_renew"]["package"] ?? null ] ) ){
							$out = '<div class="alert-danger">Vui lòng chọn gói</div>';
						}else if( empty(EXPIRY_DATE["days"]) ){
							$out = '<div class="alert-danger">Vui lòng thời hạn</div>';
						}else{
							$out = '
							'.(empty($webMoneySaleOffer) ? '
								<div class="flex flex-middle pd-20">
									<div style="width: 140px"><i class="fa-icon fa fa-globe"></i> Phí gia hạn:</div>
									<div><b>'.number_format($webMoney).' ₫</b></div>
								</div>
							' : '
								<div class="flex flex-middle pd-20">
									<div style="width: 140px"><i class="fa-icon fa fa-globe"></i> Phí gia hạn:</div>
									<div>
										<div>
											<b>'.number_format($webMoneySaleOffer).' ₫</b>
											<small><s>'.number_format($webMoney).'</s></small>
										</div>
										<div class="blue">
											Quý khách được '.( permission("seller|agency") ? 'chiết khấu' : 'giảm giá ('.EXPIRY_DATE['sale_offer'].'%)' ).': <b>'.number_format($webMoney - $webMoneySaleOffer).' ₫</b>
										</div>
									</div>
								</div>
							').'
							
							'.($diskMoney > 0 ? '
								<div class="flex flex-middle pd-20">
									<div style="width: 140px"><i class="fa-icon fa fa fa-database"></i> Dung lượng:</div>
									<div><b>'.number_format($diskMoney).' ₫</b> / '.($upgradeDisk).'Mb</div>
								</div>
							' : '').'
							'.($sslMoney > 0 ? '
								<div class="flex flex-middle pd-20">
									<div style="width: 140px"><i class="fa-icon fa fa-lock"></i> Chứng chỉ SSL:</div>
									<div><b>'.number_format($sslMoney).' ₫</b></div>
								</div>
							' : '').'
							'.(empty($totalMoneySaleOffer) ? '
								<div class="flex flex-middle pd-20" style="color: tomato">
									<div style="width: 140px"><i class="fa-icon fa fa-plus"></i> Tổng tiền:</div>
									<div>
										<span>
											<b>'.number_format($totalMoney).' ₫</b>
										</span>
									</div>
								</div>
							' : '
								<div class="flex flex-middle pd-20" style="color: tomato">
									<div style="width: 140px"><i class="fa-icon fa fa-plus"></i> Tổng tiền:</div>
									<div>
										<span>
											<b><s>'.number_format($totalMoney).'</s> ₫</b>
										</span>
									</div>
								</div>
								<div class="flex flex-middle pd-20">
									<div style="width: 140px"><i class="fa-icon fa fa-minus"></i> Giảm giá '.EXPIRY_DATE['sale_offer'].'%:</div>
									<div>
										<b>'.number_format($totalMoney - $totalMoneySaleOffer).' ₫</b>
									</div>
								</div>
								<div class="flex flex-middle pd-20">
									<div style="width: 140px"><i class="fa-icon fa fa-check"></i> Cần thanh toán:</div>
									<div>
									<b>'.number_format($totalMoneySaleOffer).' ₫</b>
									</div>
								</div>
							').'
							'.($w->package == $_POST["website_renew"]["package"] ? '
								<div class="alert-success">Quý khách đang dùng gói này</div>
							' : '
								<div class="alert-warning" style="margin-top: 10px">Bạn có đồng ý gia hạn gói <b>'.PACKAGE["name"].'</b> thêm <b>'.EXPIRY_DATE["name"].'</b> với giá: <b>'.number_format($totalMoneySaleOffer ?? $totalMoney).' ₫</b> ?</div>
							').'
						';
						}
						return $out;
					}, $w, $webMoney, $diskMoney, $sslMoney, $upgradeDisk, $totalMoney, ($totalMoneySaleOffer ?? null), ($webMoneySaleOffer ?? null)).'
				' : '
				
				' ).'
		</div>
		');
	}
	$wLink=($w->ssl_type==0 ? 'http://' : 'https://').''.(webConfig($w->domain, "BUILDER_WWW") ? 'www.' : '').''.$w->domain;
?>
<?php if( file_get_contents_curl($wLink."/builder/active.txt") != 1 ): ?>
	<?php
		$pendingSetup = true;
		//echo file_get_contents_curl($wLink."/builder/active.txt");
		// Cập nhật chứng chỉ mặc định theo tên miền chính
		if( $w->domain == explode('.', $w->domain)[0].'.'.DOMAIN ){
			WebBuilder::updateSSL(
				$w->domain,
				Storage::setting("builder_ssl_private_key"),
				Storage::setting("builder_ssl_certificate"),
				Storage::setting("builder_ssl_cacert")
			);
		}
	?>
<?php endif; ?>
<?php echo Widget::show("website_header"); ?>
<main class="main-layout">
<?php if( permission("member") ): ?>
<div class="flex flex-large">
	<section class="width-60 flex-margin panel-list">
		<?php
			$expiredDays = round( ($w->expired-time())/3600/24);
		?>
		<div style="margin-top: 20px">
			<h2 class="heading-simple">Thông tin website</h2>
			<?php
				if($w->app_price > 0){
					$webInfo=[
						"domain"    => ["title"=>"Tên miền", "icon"=>"fa-globe"],
						"app_name"  => ["title"=>"Tên mẫu", "icon"=>"fa fa-tag"],
						"app_price" => ["title"=>"Giá bán", "icon"=>"fa fa-money"],
						"ssl_type"  => ["title"=>"Chứng chỉ SSL", "icon"=>"fa fa-lock"],
					];
				}else{
					$webInfo=[
						"domain"   => ["title"=>"Tên miền", "icon"=>"fa-globe"],
						"status"   => ["title"=>"Trạng thái", "icon"=>"fa-bookmark"],
						"ssl_type" => ["title"=>"Chứng chỉ SSL", "icon"=>"fa-expeditedssl"],
						"app_price" => ["title"=>"Giá bán theme", "icon"=>"fa fa-money"],
					];
					if( permission('admin') ){
						$webInfo["app"] = ["title"=>"Tên mẫu", "icon"=>"fa fa-tag"];
					}
					if( permission("website_manager", $w->users_id) ){
						unset($webInfo['status']);
					}
					if( permission("website_manager") || $w->package ){
						unset($webInfo['app_price']);
					}
				}
			?>
			<div class="panel-body hidden" style="display: block;padding: 0">
				<div class="website-info">
					<?php foreach($webInfo as $key=>$item): ?>
						<?php
							$itemColor="";
							$value = $w->$key ?? "";
						?>
						<?php switch($key): ?><?php case "app": ?>
								<?php
									$value = '<a target="_blank" href="/admin/WebsiteManager?id='.$w->app.'.'.DOMAIN.'">'.(WEB_BUILDER["apps"][$w->app]["app_name"] ?? 'Website').'</a>';
								?>
							<?php break; ?>;<?php case "status": ?>
								<?php
									if( strlen($w->package) == 0 ){
										$value = '<span class="label-warning">Dùng thử</span>';
									}else if($expiredDays <= 0){
										$value = '<span class="label-danger">Đã hết hạn</span>';
									}else if($expiredDays <= 30){
										$value = '<span class="label-danger">Sắp hết hạn</span>';
									}else{
										$value = '<span class="label-success">Đang sử dụng</span>';
									}
								?>
							<?php break; ?>;<?php case "ssl_type": ?>
								<?php
									$sslText=["Chưa kích hoạt", "Đã kích hoạt", "Dùng SSL riêng"];
									$value=$sslText[$w->ssl_type];
								?>
							<?php break; ?>;<?php case "domain": ?>
								<?php
									$value = '<div class="text-inline"><b'.($w->app_price > 0 ? ' style="color: skyblue"' : '').'>'.$w->domain .'</b></div>';
								?>
							<?php break; ?><?php case "app_price": ?>
								<?php
									$value = BuilderDomain::where('app', $w->app )->value('app_price');
									$value = number_format($value).''.$discountText;
								?>
							<?php break; ?>
							<?php default: ?>
								<?php
									$value = $w->$key;
									if( is_numeric($value) ){
										$value = number_format($value);
									}
								?>
							<?php break; ?>
						<?php endswitch; ?>
						<div class="flex" style="color: <?php echo  (empty($itemColor) ? "initial" : $itemColor) ; ?>">
							<div style="width: 170px;padding: 12px">
								<i class="fa-icon fa <?php echo htmlEncode($item["icon"]); ?>"></i> <?php echo htmlEncode($item["title"]); ?>
							</div>
							<div style="width: calc(100% - 170px);padding: 10px">
								<?php echo $value; ?>
							</div>
						</div>
					<?php endforeach; ?>
					<?php if( isset($pendingSetup) ): ?>
						
						<div class="center width-100 bg pd-20" id="create-website-pending">
							<section style="background-color: #FFFFFF; animation: none">
								<div>
									<i class="fa fa-cog fa-spin fa-5x primary-color"></i>
								</div>
								<div class="pd-10 center" style="font-size: 16px">
									Đang cập nhật tên miền, xin vui lòng chờ...
								</div>
							</section>
						</div>
						<script type="text/javascript">
							$(document).ready(function(){
								if( $(document).find("#website-setup-progress").length == 0 ){
									var _checkWebsiteCreated = setInterval(function(){
										$.get("", {not_captcha: 1}, function(response){
											var el=$(response).find("#create-website-pending").html();
											if(typeof el=="undefined"){
												clearInterval(_checkWebsiteCreated);
												location.reload();
											}
										});
									}, 5e3);
								}
							});
						</script>
					<?php else: ?>
						
						<?php if( $w->app_price > 0 && !empty($userLogin) ): ?>
							<div class="alert-danger" id="auto-update-template">
								Đang cập nhật dữ liệu web mẫu
							</div>
							<script type="text/javascript">
								function autoUpdateTemplate(){
									if( $('.modal-setup').length > 0 ){
										return;
									}
									$.ajax({
										url: "/api/websiteManager/updateWebTemplate",
										type: "POST",
										dataType: "JSON",
										data: {onlyDatabase: true, id: <?php echo htmlEncode( $w->id ); ?>},
										success: function(){
											$("#auto-update-template").removeClass("alert-danger").addClass("alert-info").text("Cập nhật dữ liệu web mẫu thành công");
											setTimeout(function(){
												$("#auto-update-template").fadeOut();
											}, 2000);
										},
										error: function(){
											setTimeout(function(){
												autoUpdateTemplate();
											}, 1000);
										}
									});
								}
								setTimeout(function(){
									autoUpdateTemplate();
								}, 1000);
							</script>
						<?php endif; ?>
						
						<div class="website-goto bg">
							<div class="flex flex-middle">
								<div class="width-33x pd-5">
									<div class="center">
										<?php if( permission("website_template_master") ): ?>
											<a target="_blank" href="<?php echo htmlEncode(($w->ssl_type==0 ? 'http://' : 'https://')); ?><?php echo htmlEncode($w->domain); ?>?_is_builder_admin=<?php echo htmlEncode(user("check_admin")); ?>&auto_login_by_user=<?php echo htmlEncode( urlencode($userLogin ?? null) ); ?>&go_to_dashboard">
										<?php else: ?>
											<a target="_blank" href="<?php echo htmlEncode(($w->ssl_type==0 ? 'http://' : 'https://')); ?><?php echo htmlEncode( $w->domain ); ?>/admin/?auto_login_by_user=<?php echo htmlEncode( urlencode($userLogin ?? null) ); ?>&_login_key=<?php echo htmlEncode( md5($userPassword ?? null) ); ?>&go_to_dashboard">
										<?php endif; ?>
											<span>
												<i class="fa fa-cogs"></i>
											</span>
											<span>QUẢN TRỊ WEBSITE</span>
										</a>
									</div>
								</div>
								<div class="width-33x pd-5">
									<?php if( strlen($w->package) == 0 && !permission("website_manager") ): ?>
										<div class="center">
											<a data-modal="fa-dashboard<?php echo htmlEncode( $w->id ); ?>" class="modal-click">
												<span>
													<i class="fa fa-newspaper-o"></i>
												</span>
												<span>MUA THEME</span>
											</a>
										</div>
									<?php endif; ?>
								</div>
								<div class="width-33x pd-5">
									<div class="center">
										<?php if( permission("website_template_master") ): ?>
											<a target="_blank" href="<?php echo htmlEncode(($w->ssl_type==0 ? 'http://' : 'https://')); ?><?php echo htmlEncode($w->domain); ?>?_is_builder_admin=<?php echo htmlEncode(user("check_admin")); ?>&auto_login_by_user=<?php echo htmlEncode( urlencode($userLogin ?? null) ); ?>">
										<?php else: ?>
											<a target="_blank" href="<?php echo htmlEncode(($w->ssl_type==0 ? 'http://' : 'https://')); ?><?php echo htmlEncode( $w->domain ); ?>/admin/?auto_login_by_user=<?php echo htmlEncode( urlencode($userLogin ?? null) ); ?>&_login_key=<?php echo htmlEncode( md5($userPassword ?? null) ); ?>">
										<?php endif; ?>
											<span>
												<i class="fa fa-home"></i>
											</span>
											<span>XEM WEBSITE</span>
										</a>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<?php
					$checkWebUsedApp=empty($w->app_name) ? 0 : BuilderDomain::where("app", $w->app)->total()-1;
					$webAction=[
						//Quản lý web
						"manager"  =>[
							"title"=>"Đăng nhập quản lý web",
							"color"=>"blue",
							"icon"=>"fa-wrench",
							"hidden"=>true,
							"body"=>'
							<div class="menu">
								<div class="pd-5">Quý khách có thể truy cập link dưới để quản lý web:</div>
								<div class="pd-5"><a class="block btn-info" target="_blank" href="'.$wLink.'/builder/installer?email='.urlencode(user("email")).'&name='.urlencode(user("name")).'">
									'.($w->ssl_type==0 ? 'http://' : 'https://').''.(webConfig($w->domain, "BUILDER_WWW") ? 'www.' : '').''.$w->domain.'/admin
								</a></div>
							</div>
							'
						],
						//Đổi tên miền
						"changeDomain" =>[
							"title"=>"Thay đổi tên miền",
							"class"=>"",
							"icon"=>"fa-refresh",
							"body"=>'
							<div class="alert-info">
								Hãy trỏ tên miền của bạn sang IP bên dưới<br/>
								Liên hệ hỗ trợ nếu bạn chưa biết trỏ.
							</div>
							<!--
								<a class="alert-warning block" href="/huong-dan-tro-ten-mien" target="_blank">Xem hướng dẫn mua & trỏ tên miền</a>
							-->
							<div class="panel panel-info">
								<div class="panel-body">
									<div class="flex flex-middle pd-5">
										<div style="width: 120px">Trỏ IP sang</div>
										<div style="width: calc(100% - 120px); color: red"><b>'.$_SERVER['SERVER_ADDR'].'</b></div>
									</div>
									<div class="flex flex-middle pd-5">
										<div style="width: 120px">Tên miền cũ</div>
										<div style="width: calc(100% - 120px)"><b>'.$w->domain.'</b></div>
									</div>
									<div class="flex flex-middle pd-5">
										<div style="width: 120px">Tên miền mới</div>
										<div style="width: calc(100% - 120px)"><input name="website[newDomain]" class="width-100" type="text" placeholder="VD: tenmien.com" /></div>
									</div>
									'.( empty($w->alias_domain) ? '' : '
										<div>
											<label class="check radio">
												<input type="radio" name="website[primary_domain]" value="'.$w->alias_domain.'"'.($w->domain == $w->alias_domain && !webConfig($w->domain, "BUILDER_WWW") ? ' checked' : '').'>
												<s></s>
												'.$w->alias_domain.'
											</label>
										</div>
										<div>
											<label class="check radio">
												<input type="radio" name="website[primary_domain]" value="www.'.$w->alias_domain.'" '.($w->domain == $w->alias_domain && webConfig($w->domain, "BUILDER_WWW") ? 'checked' : '').'>
												<s></s>
												www.'.$w->alias_domain.'
											</label>
										</div>
										<div>
											<label class="check radio">
												<input type="radio" name="website[primary_domain]" value="'.$w->default_domain.'"'.($w->domain == $w->default_domain ? 'checked' : '').'>
												<s></s>
												'.$w->default_domain.'
											</label>
										</div>
									').'
								</div>
							</div>
							',
							"button"=>"Đổi tên miền"
						],
						//Tải mã nguồn
						"backup"      =>[
							"title"=>"Xuất bản website",
							"class"=>"",
							"renewRequired" => true,
							"hiddenIfManager" => true,
							"icon"=>"fa-download",
							"body"=>'
							'.($w->backup ? '
							<div class="alert-info">
								Bạn đã mua dịch vụ sao lưu website thành công, bạn có thể tự do xuất bản website về máy tính bất cứ khi nào.
								<br>
								<span class="red">
									Chỉ nên sử dụng code cho mục đích cá nhân, vui lòng không chia sẻ hoặc bán lại mã nguồn cho bên thứ ba.
								<span>
							</div>
							<div class="menu center"><a class="btn-primary link" onclick="downloadWebsiteBackup('.$w->id.')">Tải bản sao lưu</a></div>
							' : '
							<div class="alert-info">
								Dịch vụ sao lưu website cho phép bạn tải về toàn bộ mã nguồn website giúp bạn lưu lại dữ liệu website hoặc xuất bản chạy trên hosting riêng của bạn.
								<br>
								Bạn chỉ cần mua dịch vụ sao lưu 1 lần là có thể tự do xuất bản website bất cứ lúc nào cần!
							</div>
							').'
							',
							"button"=>$w->backup ? "" : "Mua dịch vụ: <b>".number_format($configBackupPrice)."</b> ₫"
						],
						// Mua giao diện
						"buyCode"      =>[
							"title"=>"Mua giao diện",
							"class"=>"",
							"icon"=>"fa-dashboard",
							"hidden" => true,
							"body"=>'
							<div class="alert-info">
								Bạn có thể mua theme trực tiếp trên '.DOMAIN.' và xuất bản website lên hosting riêng của mình để tự quản lý hoặc phát triển theo ý với 3 bước như sau:
								<br>
								Bước 1. Nạp tiền vào tài khoản và bấm mua theme.
								<br>
								Bước 2. Khi đã thanh toán xong hệ thống sẽ tự động backup tải xuống trực tiếp mã nguồn không cần phải chờ đợi.
								<br>
								Bước 3. Xuất bản lên hosting của bạn hoặc liên hệ kỹ thuật
								<br>
								'.DOMAIN.' sẽ hỗ trợ bạn up miễn phí.
							</div>
							'.($w->backup ? '
							<div class="alert-info">Bạn đã mua theme thành công, hãy ấn vào Xuất bản website để tải trực tiếp mã nguồn về máy tính bạn.</div>
							<div class="menu center"><a class="btn-primary link" onclick="downloadWebsiteBackup('.$w->id.')">Xuất bản website</a></div>
							' : '
							').'
							',
							"button"=>$w->backup ? "" : "Mua theme này với giá  <b>".number_format( $themePrice)."</b> ₫"
						],
						//Cài chứng chỉ SSL
						"ssl"      =>[
							"title"=>"Cài đặt chứng chỉ SSL",
							"icon"=>"fa-lock",
							"class"=>"",
							"renewRequired" => true,
							"body"=>'
							<div class="panel panel-info">
								<div class="heading link">Chứng chỉ SSL là gì?</div>
								<div class="panel-body hidden">
									SSL là chứng chỉ số website, nếu web không có SSL thì sẽ không thể truy cập link dạng <b>https://'.$w->domain.'</b><br/>
									Nếu không có chứng chỉ, trình duyệt sẽ báo website không an toàn trên thanh địa chỉ.<br/>
									Có nhiều loại chứng chỉ với giá cả khác nhau.
								</div>
							</div>
							<div class="menu">
								Cài đặt SSL cho: <b>'.$w->domain.'</b>
							</div>
							<div class="website-action-ssl-outer">
								'.call_user_func(function($w, $configSSLPrice) use ($discountText){
									$out="";
									$items=[
										["label"=>"Không dùng SSL", "body"=>
											($w->ssl_type==1 ? '
												<span style="color: red">Quý khách có muốn hủy gói SSL đã mua?</span>
											' : '
												<span style="color: red">Không có SSL, trình duyệt sẽ báo web không an toàn</span>			
											')
										],
										["label"=>($w->ssl_type==1 ? 'Kích hoạt dùng SSL' : 'Mua chứng chỉ SSL'), "body"=>
											($w->ssl_type==1 ? '
												Bạn đã mua chứng chỉ SSL với giá <b>'.number_format($configSSLPrice).' ₫</b>/1 năm '.$discountText.'
												<br/><span style="color: red; font-size: 13px">Ấn nút cập nhật lại SSL nếu vẫn chưa truy cập được:
												<br/><a target="_blank" href="https://'.$w->domain.'">https://'.$w->domain.'</a><span>
											' : '
												<span style="color: tomato">
													Bạn muốn đăng ký kích hoạt chứng chỉ SSL với giá <b>'.number_format($configSSLPrice).'</b> đ/năm. '.$discountText.'
													<br>
													SSL sẽ được cài đặt ngay lập tức khi bạn hoàn tất thanh toán.
												</span>				
											')
										],
										["label"=>"Sử dụng SSL riêng", "body"=>'
										'.($w->ssl_type==1 ? '
											<span style="color: tomato">Nếu dùng SSL riêng, gói SSL đã mua <b>'.number_format($configSSLPrice).'</b> của quý khách sẽ bị mất</span>
										' : '
											Nếu bạn đã mua SSL hãy dán mã vào bên dưới.				
										').'
										<br/><br/>
											<b>Private key</b>
<textarea placeholder="-----BEGIN PRIVATE KEY-----
...
-----END PRIVATE KEY-----" class="width-100" rows="8" name="website[ssl_private_key]">'.$w->ssl_certificate.'</textarea>
										<br/><br/>
											<b>Certificate key</b>
<textarea placeholder="-----BEGIN CERTIFICATE-----
...
-----END CERTIFICATE-----" class="width-100" rows="8" name="website[ssl_certificate]">'.$w->ssl_certificate.'</textarea>
									<br/>
									<b>CAcert key</b>
<textarea placeholder="-----BEGIN CERTIFICATE-----
...
-----END CERTIFICATE-----
" class="width-100" rows="8" name="website[ssl_cacert]">'.$w->ssl_cacert.'</textarea>
										'],
									];
									foreach($items as $i=>$item){
										$out.='
										<div class="website-action-ssl" style="padding: 2px 8px;">
											<label class="check radio">
												<input type="radio" name="website[ssl_type]" value="'.$i.'" '.($w->ssl_type==$i ? 'checked' : '').' /> <s></s> '.$item["label"].'
											</label>
											<div data-id="'.$i.'" class="pd-5 hidden" style="color: #218bd1; '.($w->ssl_type==$i ? 'display: block' : '').'">
												'.$item["body"].'
											</div>
										</div>
										';
									}
									return $out;
								}, $w, $configSSLPrice).'
							</div>	
							',
							"button"=>"Cập nhật".($w->ssl_type==1 ? ' lại SSL' : '')
						],
						// Đổi mật khẩu web
						"changePassword"      =>[
							"title"=>"Đổi mật khẩu website",
							"color"=>"",
							"icon"=>"fa-key",
							"body"=>'
							<div>
								'.( isset($userLogin) ? '
									<div class="flex flex-middle pd-10">
										<div class="width-30">Tên đăng nhập </div>
										<div class="width-70">
											<input placeholder="Tên đăng nhập" class="width-100" type="text" name="change_password[user]" value="'.$userLogin.'">
										</div>
									</div>
									<div class="flex flex-middle pd-10">
										<div class="width-30">Mật khẩu </div>
										<div class="width-70">
											<input placeholder="Mật khẩu mới" class="width-100" type="text" name="change_password[password]" value="">
										</div>
									</div>
								' : '
									<div class="alert-warning">
										<a class="block btn-info" target="_blank" href="'.$wLink.'/builder/installer?email='.urlencode(user("email")).'&name='.urlencode(user("name")).'">
										Ấn vào đây để tiến hành khởi tạo website
										</a>
									</div>
										').'
							</div>
							',
							"button" => ( isset($userLogin) ? 'Đổi mật khẩu' : '')
						],
						//Xóa web
						"delete"      =>[
							"title"=>"Xóa website",
							"color"=>"",
							"icon"=>"fa-trash",
							"hiddenIfRenew" => true,
							"body"=>'
							<div class="alert-warning">
								<i class="fa-icon fa fa-warning"></i> Toàn bộ dữ liệu của Website này sẽ bị xóa vĩnh viễn<br/>
								<i class="fa-icon fa fa-times"></i> Website đã xóa không thể phục hồi!!!
							</div>
							<div class="flex flex-middle pd-5">
								<div class="width-80">
									<input type="text" name="captcha" class="width-100" placeholder="Nhập mã xác minh">
								</div>
								<div class="width-20 center">
									'.( empty($_POST) && empty($_REQUEST["not_captcha"]) ? '
										<img id="delete-website-captcha" src="/api/captcha.png" />
									' : '').'
								</div>
							</div>
							'.($checkWebUsedApp > 0 ? '
								<div class="alert-danger">
									<a href="/admin/WebsiteList?app='.$w->app.'" target="_blank" style="color: white">
										Không thể xóa web mẫu vì đang có '.$checkWebUsedApp.' website đang dùng mẫu này, click để xem
									</a>
								</div>
							' : '').'',
							"button"=>($checkWebUsedApp>0 ? "" : "XÓA: {$w->domain}")
						],
						//Nâng cấp dung lượng
						"upgradeDisk"      =>[
							"title"=>"Mua thêm dung lượng",
							"icon"=>"fa-plus",
							"hidden"=>true,
							"renewRequired" => true,
							"body"=>'
							<div class="menu">
								Nâng cấp thêm dung lượng cho: <b>'.$w->domain.'</b>
							</div>
							<div class="menu flex">
								<input name="website[upgradeDisk]" step="50" data-price="'.$upgradeDiskPrice.'" type="number" placeholder="Vui lòng nhập dung lượng" min="0" max="'.WEB_BUILDER["upgradeDisk"]["max"].'" class="rm-radius input website-upgrade-disk width-80" value="0" />
								<input type="text" class="rm-radius input width-20" value="Mb" readonly/>
							</div>
							<div class="alert-info hidden">
								Số tiền cần thanh toán cho <i></i> là: <span></span>
								'.$discountText.'
							</div>
							',
							"button"=>"Nâng cấp"
						],
						// Đổi dung lượng
						"changeDisk"      =>[
							"title"=>"Thay đổi dung lượng",
							"icon"=>"fa-floppy-o",
							"hidden"=>true,
							"body"=>'
							<div class="menu">
								Đổi dung lượng cho: <b>'.$w->domain.'</b>
							</div>
							<div class="menu flex">
								<input name="website[changeDisk]" step="50" value="'.webConfig($w->domain, "BUILDER_MAXDISK").'" type="number" placeholder="Vui lòng nhập dung lượng" class="rm-radius input width-80" value="0" />
								<input type="text" class="rm-radius input width-20" value="Mb" readonly/>
							</div>
							',
							"button"=>"Thay đổi"
						],
						//Gia hạn
						"renew"      =>[
							"title"=>"Gia hạn website",
							"icon"=>"fa-credit-card",
							"hidden"=>true,
							"width"=>"860px",
							"body"=>'
								<section id="website-renew">
									'.call_user_func(function($w){
										$out = '
										<div class="flex flex-center flex-medium" style="align-items: stretch">
										';
										foreach(WEB_BUILDER["package"] as $id => $item){
											if( in_array( user('role'), (array)$item['roles'] ) ){
												if( $w->package == $id && $w->renew_price > 0 ){
													$item["price"] = number_format($w->renew_price);
												}
												$out .= '
												<div class="width-50 pd-20">
													<div style="border: 1px solid #EAEAEA; box-shadow: 0 0 20px -2px rgba(0,0,0,0.25); border-radius: 15px; position: relative; padding-bottom: 20px">
														<div class="price-list-package-item-header" style="background-image: linear-gradient(-45deg, '.$item["background_color1"].', '.$item["background_color2"].'); color: white">
															'.(empty($item["suggest"]) ? '' : '<img src="/files/uploads/2019/08/best-price.png">').'
															<div class="price-list-package-item-title">
																<div>
																	<span>
																		'.$item["name"].'
																	</span>
																</div>
															</div>
															<div class="price-list-package-item-price">
																<span style="color: #FFFFFF;font-size: 20px">
																	'.$item["price"].' ₫ / năm
																</span>
															</div>
														</div>
														<div class="pd-10" style="line-height: 1.5; padding-left: 45px">
															<i class="fa fa-icon fa-save"></i>
															Dung lượng: <b>'.$item["disk"].'</b> Mb
															<br>
															'.nl2br( str_replace(['++', '--'], ['<i class="fa fa-icon fa-check"></i>', '<i class="fa fa-icon fa-times"></i>'], $item["description"]) ).'
														</div>
														<div class="center" style="margin-top: 10px; position: absolute;right: 0;top: 5px;">
															<label class="check radio">
																<input type="radio" name="website_renew[package]" value="'.$id.'"'.($w->package == $id ? ' checked' : '').'>
																<s></s>
															</label>
														</div>
													</div>
												</div>
												';
											}
											Assets::footer("/assets/general/css/widgets/bang-gia.css");
										}
										$out .= '
										</div>
										<div class="pd-20 flex flex-center flex-medium">
										';
										$i = 0;
										foreach(WEB_BUILDER["expiry_date"] as $id=>$item){
											$i++;
											$out .= '
											<div class="pd-5" style="width: '.(100 / count(WEB_BUILDER["expiry_date"])).'%;">
												<div class="pd-5" style="border: 1px solid #EAEAEA; border-radius: 5px;  position: relative">
													<label class="check radio">
														<input type="radio" name="website_renew[days]" value="'.$id.'"'.($i == 1 ? ' checked' : '').'>
														<s></s>
														'.$item["name"].'
													</label>
													'.($item['sale_offer'] > 0 && in_array( user('role'), (array)$item['roles']) ? '
														<sup class="label-danger" style="font-size: 13px; display: inline-block; position: absolute; top: 0; right: 5px; border-radius: 0">
															giảm '.$item['sale_offer'].'%
														</sup>
													' : '').'
												</div>
											</div>
											';
										}
										$out .= '</div>';
										return $out;
									}, $w).'
								<div id="website-renew-package"></div>
							</section>
							',
							"button"=>'<i class="fa fa-check"></i> Đồng ý gia hạn'
						],
						// Đổi hạn sử dụng
						"change_expired"  =>[
							"title"=>"Thay đổi hạn sử dụng",
							"color"=>"",
							"icon"=>"fa-calendar",
							"hidden"=>true,
							"body"=>'
							<div class="menu">
								<div class="alert-warning">Các website do admin quản lý sẽ không bị xóa khi hết hạn</div>
								'.call_user_func(function($w){
									Assets::footer("/assets/form/date-picker.css", "/assets/form/date-picker.js");
									$tomorrow = strtotime("+1 day");
									$out = '
									<div class="form-date-wrap tooltip" data-format="day/month/year" title="Hạn sử dụng">
										<div class="form-date-picker form-date-picker-bottom hidden"></div>
										<div class="input-icon">
											<i class="fa fa-calendar"></i>
											<input class="input width-100 input-disabled" placeholder="Chọn ngày hết hạn" type="text" name="change_expired" value="'.date('d/m/Y', $w->expired).'" readonly=""/>
										</div>
										<code>{
											"allow":
											{
												"hours":[],
												"minutes":"",
												"requiredHour":false,
												"days":"",
												"months":"",
												"weekDay":["mon","tue","wed","thu","fri","sat","sun"],
												"min":
												{
													"y": "'.date('Y', $tomorrow).'",
													"m": "'.date('m', $tomorrow).'",
													"d": "'.date('d', $tomorrow).'"
												},
												"max":
													{
														"y": "'.(date("Y") + 30).'",
														"m": "2",
														"d": "14"
													}
												},
												"value": {
													"day": "'.date('d', $w->expired).'",
													"month": "'.date('m', $w->expired).'",
													"year": "'.date('Y', $w->expired).'"
												}
											}
										</code>
									</div>
									';
									$out .= '<div class="tooltip" style="margin-top: 5px" title="Giá gia hạn"><input class="input width-100 input-currency" placeholder="Giá gia hạn mỗi năm" type="text" name="renew_price" value="'.number_format($w->renew_price).'"></div>';
									$out .= '<div style="margin-top: 5px" class="tooltip" title="Gói website"><select name="package" class="width-100">';
									$out .= '<option value="">Gói Free</option>';
									foreach(WEB_BUILDER["package"] as $name => $p){
										$out .= '<option value="'.$name.'"'.($w->package == $name ? ' selected' : '').'>'.$p['name'].'</option>';
									}
									$out .= '</select></div>';
									return $out;
								}, $w).'
							</div>
							',
							"button"=>'<i class="fa fa-check"></i> Lưu lại'
						],
					];
				?>
			</div>
		</div>
		<?php if(  empty($w->app_name) || empty($w->app)  ): ?>
			<div style="margin-top: 20px">
				<div class="heading-simple">Thông tin gói dịch vụ</div>
				<?php
					$itemColor = $expiredDays>7 || $w->expired == 0 ? '#218bd1' : 'red';
				?>
				<div class="pd-10 bg">
					<div class="flex menu">
						<div style="width: 150px">
							<i class="fa fa-icon fa-calendar-check-o"></i> Ngày khởi tạo:
						</div>
						<div style="width: calc(100% - 150px)">
							<b><?php echo htmlEncode( date("H:i - d/m/Y", timestamp($w->created_at) ) ); ?></b>
						</div>
					</div>
					<div class="flex flex-middle menu">
						<div style="width: 150px">
							<i class="fa fa-icon fa-calendar-times-o"></i> Ngày hết hạn:
						</div>
						<div style="width: calc(100% - 150px); color: <?php echo htmlEncode($itemColor); ?>">
							<?php if( permission('admin', $w->users_id) ): ?>
								<?php if( $w->expired > 0 ): ?>
									<div><b><?php echo htmlEncode( date("H:i - d/m/Y", ($w->expired) ) ); ?></b></div>
								<?php endif; ?>
								<div class="blue">Website của admin sẽ không tự động xóa</div>
							<?php else: ?>
								<div><b><?php echo htmlEncode( date("H:i - d/m/Y", ($w->expired) ) ); ?></b> (<span<?php echo  $expiredDays < 1 ? ' style="color: red"' : '' ; ?>>Còn <b><?php echo htmlEncode( $expiredDays ); ?></b> ngày</span>)</div>
								<?php if( $expiredDays <= 0 ): ?>
									<div>Website sẽ bị xóa vĩnh viễn vào ngày <?php echo htmlEncode( date("d/m/Y", $w->expired + (3600 * 24 * WEB_BUILDER["expired_delete_delay"]) ) ); ?></div>
								<?php elseif( $expiredDays<31 ): ?>
									<div>Website sẽ xóa vĩnh viễn nếu bạn không gia hạn website trước hạn</div>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
					<div class="flex menu">
						<div style="width: 150px">
							<i class="fa fa-icon fa-shopping-basket"></i> Gói dịch vụ:
						</div>
						<div style="width: calc(100% - 150px)">
							<a href="/bang-gia" target="_blank">
								<b><?php echo htmlEncode( WEB_BUILDER["package"][$w->package]["name"] ?? 'Free' ); ?></b>
							</a>
						</div>
					</div>
					<?php if($w->renew_price > 0): ?>
						<div class="flex menu">
							<div style="width: 150px">
								<i class="fa fa-icon fa-money"></i> Phí gia hạn:
							</div>
							<div style="width: calc(100% - 150px)">
								<b><?php echo htmlEncode( number_format($w->renew_price) ); ?></b>/năm
							</div>
						</div>
					<?php elseif( !empty($w->package) ): ?>
						<?php
							BuilderDomain::find($w->id)->update([
								"renew_price" => toNumber( WEB_BUILDER["package"][$w->package]['price'] ?? 0 )
							]);
						?>
					<?php endif; ?>
					<div class="menu">
						<?php if( permission("website_manager") ): ?>
							<button data-modal="fa-calendar<?php echo htmlEncode($w->id); ?>" class="btn btn-info center modal-click">
								<i class="fa fa-calendar"></i> Thay đổi gói & hạn website
							</button>
						<?php else: ?>
							<button data-modal="fa-credit-card<?php echo htmlEncode($w->id); ?>" class="btn btn-info center modal-click">
								<i class="fa fa-credit-card"></i> Gia hạn website
							</button>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		
		
		<?php if( permission("website_manager") ): ?>
			
			<div style="margin-top: 20px">
				<div class="heading-simple">Khách hàng</div>
				<div class="pd-10 bg">
					<div class="menu">
						<i class="fa-icon fa fa-user"></i> <?php echo  user("name_color", $w->users_id) ; ?>
					</div>
					<div class="menu">
						<i class="fa-icon fa fa-envelope"></i> <?php echo htmlEncode( user("email", $w->users_id) ); ?>
					</div>
					<div class="menu">
						<i class="fa-icon fa fa-phone"></i> <?php echo htmlEncode( user("phone", $w->users_id) ); ?>
					</div>
					<div class="menu">
						<button data-modal="website-change-user" class="btn btn-info center modal-click">
							<i class="fa fa-user"></i>
							Đổi người quản lý
						</button>
						<a target="_blank" href="/admin/PaymentHistory?uid=<?php echo htmlEncode( $w->users_id ); ?>" class="btn btn-success">
							<i class="fa fa-history"></i>
							Lịch sử giao dịch
						</a>
					</div>
				</div>
			</div>
			<?php echo  modal("website-change-user", "Đổi người quản lý", '
				<div id="website-change-user">
					'.($expiredDays > 2 ? '
						<div class="menu">
							<input class="input width-100" type="text" placeholder="Nhập email hoặc số điện thoại chủ mới" />
						</div>
					' : '
						<div class="alert-danger">
							'.($w->app_price > 0 ? '
								Không thể đổi chủ cho web mẫu
							' : '
								Website phải có hạn sử dụng tối thiểu 3 ngày, hãy đổi hạn sử dụng trước khi đổi chủ quản lý
							').'
						</div>
					').'
					<form method="POST">
						'.call_user_func(function($w){
							$out = "";
							if( isset($_POST["website_change_user"]) ){
								$getUser = Users::where("email", $_POST["website_change_user"])
									->orWhere("phone", $_POST["website_change_user"])
									->first();
								if( empty($getUser->id) ){
									$out .= '<div class="alert-danger">Không tìm thấy tài khoản nào</div>';
								}else{
									if( isset($_POST["website_change_user_confirm"]) ){
										BuilderDomain::where("id", $w->id)
											->update(["users_id" => $getUser->id]);
											redirect("", true);
										if( permission("website_manager", $getUser->id) ){
											WebBuilder::updateConfig($w->domain, ["BUILDER_BACKUP"=>"true"]);
										}else{
											WebBuilder::updateConfig($w->domain, ["BUILDER_BACKUP"=>"false"]);
										}
									}
									$out .='
										<input type="hidden" name="website_change_user" value="'.$getUser->phone.'">
										<div class="alert-info">
											<div class="pd-10">
												<i class="fa-icon fa fa-user"></i> '.$getUser->name.'
											</div>
											<div class="pd-10">
												<i class="fa-icon fa fa-envelope"></i> '.$getUser->email.'
											</div>
											<div class="pd-10">
												<i class="fa-icon fa fa-phone"></i> '.$getUser->phone.'
											</div>
										</div>
										<div class="center pd-20">
											<input class="btn-danger" name="website_change_user_confirm" type="submit" value="Xác nhận đổi">
										</div>
									';
								}
							}
							return $out;
						}, $w).'
					</form>
				</div>
			','450px', false, true, true) ; ?>
			<script type="text/javascript">
				$("#website-change-user").on("keyup change", ".input", function(){
					$.post("", {website_change_user: $(this).val()}, function(response){
						var el = $(response).find("#website-change-user form").html();
						$("#website-change-user form").html(el);
					});
				});
			</script>
		<?php endif; ?>
	</section>
	<section class="width-40 flex-margin">
		
		<div class="heading-simple" style="margin-top: 20px">Quản lý website và tên miền</div>
		<div>
			<div class="bg pd-10">
				<?php foreach($webAction as $action=>$item): ?>
					<?php if( !isset($item["hidden"]) ): ?>
						<?php if( empty($item["hiddenIfRenew"]) || isset($item["hiddenIfRenew"]) && strlen($w->package) == 0  || permission("website_manager") ): ?>
							<?php if( empty($item["hiddenIfManager"]) || isset($item["hiddenIfManager"]) && !permission("website_manager") ): ?>
								<a data-modal="<?php echo htmlEncode($item["icon"]); ?><?php echo htmlEncode($w->id); ?>" class="modal-click block pd-10" style="<?php echo htmlEncode( ( isset($item["color"]) ? 'color: '.$item["color"].' !important' : '' ) ); ?>">
									<i class="fa-icon fa <?php echo htmlEncode($item["icon"]); ?>"></i> <?php echo htmlEncode($item["title"]); ?>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<?php foreach($webAction as $action=>$item): ?>
				<?php echo  modal($item["icon"]."".$w->id, $item["title"], '
					<form class="bg website-action-form">
						'.$item["body"].'
						<div class="website-action-msg-'.$w->id.' hidden alert-danger">'.($websiteActionMsg??'').'</div>
						'.(!empty($item["button"]) ? '
							'.( permission('website_manager') || empty($item['renewRequired']) || isset($item['renewRequired']) && strlen($w->package) > 0 ? '
								'.($action == "delete" && strlen($w->package) > 0 && !permission('website_manager') ? '
									<div class="alert-danger">
										Vui lòng liên hệ hỗ trợ nếu muốn xóa website đã gia hạn
									</div>
								' : '
									<div class="pd-10 center"><button class="'.($action=="delete" ? 'btn-danger' : 'btn-primary').' website-action" type="button" data-action="'.$action.'" data-id="'.$w->id.'" data-domain="'.$w->domain.'">'.$item["button"].'</button></div>
								').'
							' : '
								<div class="alert-danger">
									Vui lòng gia hạn website trước khi sử dụng chức năng này
								</div>
							' ).'
						' : '').'
					</form>
				',$item['width'] ?? '450px', false, true, true) ; ?>
			<?php endforeach; ?>
		</div>
		
		<div style="margin-top: 20px">
			<div class="heading-simple">Thông số dung lượng</div>
			<div class="menu">
				<div class="center" id="website-disk-stats">
					<?php
						if( isset($_POST["disk_stats"]) ){
							$getDiskMax = webConfig($w->domain, "BUILDER_MAXDISK");
							if( $getDiskMax > 0 ){
								$diskUsed = folderSize( WebBuilder::userPublic($w->domain) );
								$percent = $diskUsed/mb2Bytes( webConfig($w->domain, "BUILDER_MAXDISK") )*100;
								$percent = round($percent);
								echo '
								<div class="progress-pie" data-color="" data-value="'.$percent.'" data-size="250">
									<p>
										<i style="font-size: 40px" class="fa fa-floppy-o"></i><br/>
										<b>Dung lượng lưu trữ</b><br/>
										'.bytesConvert($diskUsed).' / '.bytesConvert(mb2Bytes( webConfig($w->domain, "BUILDER_MAXDISK") )).'<br/>
										Còn trống '.(100-$percent).'%
									</p>
								</div>
								';
							}
						}else{
							echo '<div class="pd-10"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>';
						}
					?>
				</div>
				<div class="center">
					<?php if( permission('website_manager') ): ?>
						<button data-modal="fa-floppy-o<?php echo htmlEncode($w->id); ?>" class="btn btn-info center modal-click">
						<i class="fa fa-floppy-o"></i> Đổi dung lượng
					</button>
					<?php else: ?>
						<button data-modal="fa-plus<?php echo htmlEncode($w->id); ?>" class="btn btn-info center modal-click">
							<i class="fa fa-plus"></i> Mua thêm dung lượng
						</button>
					<?php endif; ?>
				</div>
			</div>
			<?php if( $upgradeDisk > 0 ): ?>
				<div class="alert-info">
					Dung lượng đã mua thêm: <b><?php echo htmlEncode( $upgradeDisk ); ?></b> Mb
				</div>
			<?php endif; ?>
		</div>
		<?php if( !isset($pendingSetup) ): ?>
			<script type="text/javascript">
				// Thống kê dung lượng
				setTimeout(function(){
					$.post("", {disk_stats: 1, not_captcha: 1}, function(response){
						var el = $(response).find("#website-disk-stats").html();
						$("#website-disk-stats").html(el);
						progressPieInstall();
					});
				}, 2e3);
			</script>
		<?php endif; ?>
		<?php if( permission('website_manager') ): ?>
			
			<div style="margin-top: 20px">
				<div class="heading-simple">Sao lưu và đình chỉ website</div>
				<div class="menu">
				<section style="margin: 0">
					<table class="table table-border width-100">
						<?php
							// Lưu thiết lập
							if(isset($_POST["edit_web"])){
								$data = $_POST["edit_web"];
								BuilderDomain::find($w->id)->update(["suspended"=>empty($data["suspended"]) ? 0 : 1 ]);
								classes\WebBuilder::updateConfig($w->domain, ["BUILDER_SUSPENDED"=>$data["suspended"]]);
							}
							$field = [
								"db_user"=>"Database name"
							];
						?>
						<?php foreach($field as $key=>$label): ?>
							<?php
								$out=$w->$key??null;
								$value=$out;
							?>
							<?php switch($key): ?><?php case "db_user": ?>
									<?php
										$value=webConfig($w->domain, "DB_USER");
									?>
								<?php break; ?>;
							<?php endswitch; ?>
							<tr>
								<?php if( !empty($label) ): ?>
									<td class="width-40 menu"><?php echo htmlEncode($label); ?></td>
								<?php endif; ?>
								<td class="width-<?php echo htmlEncode((empty($label) ? '100' : '60')); ?> menu"><?php echo htmlEncode( $value ); ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<div class="flex flex-medium">
						<div class="pd-5 width-100">
							<div class="pd-5">
								<a class="btn-primary width-100 link" onclick="downloadWebsiteBackup(<?php echo htmlEncode( $w->id ); ?>)">Xuất bản website</a>
							</div>
						</div>
					</div>
					<form method="POST">
						<div class="pd-5">
							<textarea rows="5" class="width-100" placeholder="Nhập nội dung để đình chỉ web" name="edit_web[suspended]"><?php echo htmlEncode(webConfig($w->domain, "BUILDER_SUSPENDED")); ?></textarea>
						</div>
						<div class="pd-5"><button class="width-100 btn-danger" type="submit">Đình chỉ website</button></div>
					</form>
			</section>
			</div>
		<?php endif; ?>
	</section>
</div>
<?php endif; ?>
</div>
<div class="center" style="padding: 20px 20px 40px 20px">
	<a href="/admin/WebsiteList" class="btn-primary">
		<i class="fa fa-reply"></i>
		Quay lại danh sách
	</a>
</div>
</main>