<?php
namespace pages\khach__hang\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class KhachHang{

	public function index(){
		$controllerName = "Controller: KhachHang";
		$functionName="Function: index";
		view("KhachHang", compact("controllerName", "functionName"));
	}

}

