<?php
namespace pages\dang__ky__domain__gia__re\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class DangKyDomainGiaRe{

	public function index(){
		$controllerName = "Controller: DangKyDomainGiaRe";
		$functionName="Function: index";
		view("DangKyDomainGiaRe", compact("controllerName", "functionName"));
	}
	/*
	 * Kiểm tra tên miền đã đăng ký hay chưa
	 */
	public function checkDomain(){
		$domain = $_POST["check_domain"].$_POST["domain_ext"];
		$checkDomain = checkDomainIsRegistered($domain);
		if( isset($checkDomain["error"]) ){
			return '<div class="alert-danger">'.$checkDomain["error"].'</div>';
		}
		if( is_null( $checkDomain["success"] ?? null) ){
			$out = '<div class="alert-success">Tên miền <b>'.$domain.'</b> chưa có người đăng ký</div>';
		}else{
			$out = '<div class="alert-danger">Tên miền <b>'.$domain.'</b> đã có người đăng ký</div>';
		}
		return $out;
	}
}

