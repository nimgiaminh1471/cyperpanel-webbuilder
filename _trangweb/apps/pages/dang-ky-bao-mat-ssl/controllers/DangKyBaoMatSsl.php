<?php
namespace pages\dang__ky__bao__mat__ssl\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class DangKyBaoMatSsl{

	public function index(){
		$controllerName = "Controller: DangKyBaoMatSsl";
		$functionName="Function: index";
		view("DangKyBaoMatSsl", compact("controllerName", "functionName"));
	}

}

