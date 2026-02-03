<?php
namespace pages\hinh__thuc__thanh__toan\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class HinhThucThanhToan{

	public function index(){
		$controllerName = "Controller: HinhThucThanhToan";
		$functionName="Function: index";
		view("HinhThucThanhToan", compact("controllerName", "functionName"));
	}

}

