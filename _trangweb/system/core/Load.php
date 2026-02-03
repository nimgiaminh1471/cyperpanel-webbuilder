<?php
/*
Khởi tạo các đoạn mã cần thiết
*/
define("PUBLIC_ROOT", $_SERVER['DOCUMENT_ROOT']);//Thư mục chứa dữ liệu công khai
define("DOMAIN", $_SERVER["HTTP_HOST"]);//Tên miền
define("HOME", "http".(empty($_SERVER['HTTPS']) ? "" : "s")."://".DOMAIN);//Trang chủ
define("URI", $_SERVER["REQUEST_URI"]);//URI request
define("THIS_URL", HOME."".URI);//Link hiện tại
define("THIS_LINK", strtok(THIS_URL,'?'));//Link hiện tại(Không có GET)

//Tự động load các class khi cần
spl_autoload_register(function($class){
	$namespace=str_replace(["\\","__"], ["/","-"], $class).".php";
	foreach([APPS_ROOT."/".$namespace, SYSTEM_ROOT."/system/classes/".$namespace] as $path){
		if( file_exists($path) ){
			require_once($path);
			break;
		}
	}
});

//Nhúng các files functions
foreach( array_merge(["PrintError.php","Functions.php"], glob(APPS_ROOT."/functions/*.php")) as $f){
	require($f);
}

//Trang chưa được setup
if(DB::table("storage")->where("key", "actived")->value("value")!=1){
	if(vnStrFilter( strtok(URI, "?"), "")!="setup"){
		redirect("/setup?".$_SERVER['QUERY_STRING']??"");
	}
}

//Hiện thông báo tạm dừng web
if( !empty(CONFIG["SUSPENDED"]) ){
	PrintError(CONFIG["SUSPENDED"], "Thông báo");
}

//Lưu thông tin tài khoản đăng nhập
define("THIS_USER", empty($_COOKIE["login_key"]) ? [] : (array)DB::table("users")->select("id")->where("login_key", $_COOKIE["login_key"])->first(true));
if(isset(THIS_USER["id"])){
	models\Users::where("id", THIS_USER["id"])->update(["last_online"=>timestamp()]);
}

//Cho phép chỉnh trang hay không
if(permission("admin") && Storage::option("enablePageEditor", 0)==1){
	define("PAGE_EDITOR", true);
}else{
	define("PAGE_EDITOR", false);
}

//Giao thức
function protocolFix(){
	$protocol="http://";
	$www=explode("www.", DOMAIN)[1]??false;
	
	//Chuyển sang https
	if(CONFIG["SSL"]){
		if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
			$protocol="https://";
			$link=$protocol.DOMAIN.URI;
		}
	}

	//Chuyển sang WWW
	if(CONFIG["WWW"]){
		if(!$www){
			$link=$protocol."www.".DOMAIN.URI;
		}
	}else if($www){
		$link=$protocol.$www.URI;
	}

	//Chuyển hướng
	if(!empty($link)){
		header('HTTP/1.1 301 Moved Permanently');
		header("Location: $link");
		die;
	}
}
protocolFix();

//Thống kê truy cập
$__device=cookie("device");
if( !empty(device(true)) && in_array($__device, ["mobile", "tablet", "desktop"]) ){
	if( empty($_COOKIE["stats_months"]) ){
		$__stats=Storage::stats("months", []);
		$__stats[date("n/Y")][$__device]=($__stats[date("n/Y")][$__device]??0)+1;
		$__stats=array_slice($__stats, -13);
		Storage::update("stats", ["months"=>$__stats], false);
		setcookie("stats_months", 1, time()+3600*24*30, "/");
	}
	if( empty($_COOKIE["stats_today"]) ){
		$__stats=Storage::stats("days", []);
		$__stats[date("j")]=($__stats[date("j")]??0)+1;
		Storage::update("stats", ["days"=>$__stats]);
		setcookie("stats_today", 1, time()+3600*24, "/");
	}
}

$Schedule = new Schedule;//Khởi tạo object hẹn giờ
session_start();
require("Route.php");//Khởi tạo route
