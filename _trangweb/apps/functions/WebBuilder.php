<?php
/*
# Các function hệ thống tạo web
*/
//Tạo web
	$__apps=[];
	foreach(models\BuilderDomain::where("app_name", "!=", "")->get(true) as $web){
		$__apps[$web->app]=(array)$web;
	}

	define("WEB_BUILDER", [
		"disk"=>Storage::setting("builder_parameters_disk", 100),//Dung lượng mặc định (Mb)
		"expired"=>Storage::setting("builder_parameters_expired", 3),//Ngày dùng thử
		"expired_delete_delay"=>Storage::setting("builder_parameters_expired_delete_delay", 3),// Số ngày xóa web đã gia hạn (hết hạn)
		//Code website
		"categories"=>Storage::builder("categories", []),
		"apps"=>$__apps,
		//Giá nâng cấp dung lượng
		"upgradeDisk"=>[
			"max"=>2000,//Cho phép nâng cấp tối đa (Mb)
			"price"=>toNumber( Storage::setting("builder_parameters_upgrade_disk_price", 50000) ),//Giá tiền cho mỗi Mb
		],
		"sslPrice"=>toNumber( Storage::setting("builder_parameters_ssl_price", 10000) ),//Giá tiền chứng chỉ SSL
		"backupPrice"=>toNumber( Storage::setting("builder_parameters_backup_price", 50000) ),//Giá tiền dịch vụ backup code
		"sellerPrice"=>5000000,//Giá nâng cấp seller
		"package"=>Storage::builder("package", []),
		"expiry_date"=>Storage::builder("expiry_date", []),
		"discount"=>[
			"seller" =>Storage::setting("builder_discount_seller", 20),
			"agency" =>Storage::setting("builder_discount_agency", 20),
		]
	]);


//Xóa website hết hạn
function deleteWebsiteExpired(){
	if( permission("member") ){
		$webExpired = models\BuilderDomain::where("expired", "<", ( time() - (3600 * 24 * WEB_BUILDER["expired_delete_delay"]) ) )
			->where("expired", ">", 0)
			->orWhere( "expired", "<", time() )
			->whereNull("package")
			->where("expired", ">", 0)
			->limit(2)
			->get();
		foreach($webExpired as $w){
			if( permission("admin", $w->users_id) ){
				// Nếu là web của admin
				models\BuilderDomain::find($w->id)->update(["expired" => strtotime('+30 years')]);
			}else{
				// Xóa nếu là web người khác
				classes\WebBuilder::delete($w->domain);
				models\BuilderDomain::destroy($w->id);
			}
		}
	}
}


//Lấy thông tin config của từng web
function webConfig($domain, $key = null, $configFile = null){
	$indexFile = $configFile ?? classes\WebBuilder::userPublic($domain)."/wp-config.php";
	if( !file_exists($indexFile) ){
		return null;
	}
	$indexContents=file_get_contents($indexFile, FILE_USE_INCLUDE_PATH);
	if( $key == 'table_prefix' ){
		preg_match("/\\\$table_prefix(.*)=(.*)'(.+?)'\;/", $indexContents, $getPrefix);
		return $getPrefix[3] ?? null;
	}
	preg_match('/define\(\"'.$key.'\", ([\"]*)(.*?)([\"]*)\);/is', $indexContents, $config);
	$out=$config[2]??null;
	if( $out==="true" ){
		return true;
	}else if($out==="false"){
		return false;
	}
	return $out;
}

//Trừ tiền của user
function userPayment($amount, $uid=null){
	if( is_null($uid) ){
		$uid=user("id");
	}
	models\Users::where("id", $uid)->update(["money"=>( user("money", $uid)-$amount )]);
}

// Lưu lịch sử giao dịch của user
function userPaymentHistory($data){
	if( empty($data["users_id"]) ){
		$data["users_id"] = user("id");
	}
	$data["category"] = vnStrFilter($data["name"]);

	if( !DB::table("payment_history_categories")->where("name", $data["category"] )->exists() ){
		DB::table("payment_history_categories")->insert([
			"name"  => $data["category"],
			"label" => $data["name"]
		]);
	}
	unset($data["name"]);
	models\PaymentHistory::create($data);
}

//Kiểm tra link lỗi
function is404($url){
	$handle = curl_init($url);
	curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
	$response = curl_exec($handle);
	$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	curl_close($handle);
	if ($httpCode >= 200 && $httpCode < 303) {
		return false;
	} else {
		return true;
	}
}

/*
 * Kiểm tra tên miền đã đăng ký hay chưa 
 */
function checkDomainIsRegistered($domain){
	$domain = mb_strtolower($domain);
	$domainName = explode(".", $domain)[0];
	if( empty($domainName) ){
		$error = "Vui lòng nhập tên miền";
	}
	$domainExt = explode(".", $domain);
	unset($domainExt[0]);
	$domainExt = ".".implode(".", $domainExt);
	$domain = $domainName.$domainExt;
	if( strpos($domainExt, ".") === false || strlen($domainExt) < 2 ){
		$error = "Đuôi tên miền không hợp lệ";
	}
	if(!preg_match('/^([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?)$/i', $domainName) || strpos($domainName,'--') !==false){
		$error = "Tên miền <b>$domain</b> không hợp lệ";
	}

   if( empty($error) ){
	   // Kiểm tra tên miền tại whois.net.vn
	   $kq = file_get_contents("http://www.whois.net.vn/whois.php?domain=".$domain); // Kết quả bằng 1: đã đăng ký, 0: chưa đăng ký
	   if($kq==1){
			// Lấy thông tin về chủ sở hữu tên miền (tùy chọn)
	   		$msg = file_get_contents("http://www.whois.net.vn/whois.php?domain=$domain&act=getwhois");
	   }else{
			// Chưa đăng ký
	   		$msg = null;
	   }
	   return ["success" => $msg];
	}else{
		//return ["error" => $error];
	}
}

//Kiểm tra có đủ tiền không
function checkMoneyCorrect(INT $price=0){
	if( $price>0 && user("money")<$price && !permission("website_manager") ){
		return '
		<div>Tài khoản của quý khách không đủ <b>'.number_format($price).' ₫</b> <a href="/admin/Recharge" class="label-info"><i class="fa fa-plus"></i> Nạp</a></div>
		';
	}
	return;
}

/*
 * Gửi email cho web sắp hết hạn
 */
function sendNotificationWebsiteExpired(){
	$getWeb30 = \models\BuilderDomain::orderBy('id', 'DESC')
	->where('package', '>', 0)
	->where('expired', '<', strtotime('+30 days') )
	->where('expiration_notice', 0)
	->limit(5)
	->get()
	->toArray();
	$getWeb5 = \models\BuilderDomain::orderBy('id', 'DESC')
	->whereNull('package')
	->where('expired', '<', strtotime('+5 days') )
	->where('expired', '>', 0 )
	->where('expiration_notice', 0)
	->limit(5)
	->get()
	->toArray();
	$getWeb = array_merge( $getWeb30, $getWeb5 );
	foreach($getWeb as $item){
		$item = (object)$item;
		$mailContent = Storage::setting('mail_notification_expired_30');
		$replace = [
			'domain'       => $item->domain,
			'expired_date' => date('d/m/Y', $item->expired),
			'renew_price'  => number_format($item->renew_price),
			'package'      => WEB_BUILDER["package"][$item->package]["name"] ?? 'Free',
			'user_name'    => user('name', $item->users_id),
			'user_email'   => user('email', $item->users_id),
			'user_phone'   => user('phone', $item->users_id),
		];
		foreach($replace as $from => $to){
			$mailContent = str_replace('${'.$from.'}', $to, $mailContent);
		}
		\models\BuilderDomain::find($item->id)->update([
			'expiration_notice' => 1
		]);
		\mailer\WebMail::send([
			"To"          => [ user('email', $item->users_id) ],
			"Subject"     => "[".DOMAIN."] Website {$item->domain} sắp hết hạn",
			"Body"        => $mailContent,
			"Attachments" => []
		]);
	}
	
}