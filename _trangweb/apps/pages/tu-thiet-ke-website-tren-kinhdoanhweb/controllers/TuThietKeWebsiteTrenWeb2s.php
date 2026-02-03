<?php
namespace pages\tu__thiet__ke__website__tren__kinhdoanhweb\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class TuThietKeWebsiteTrenKinhdoanhweb{

	public function index(){
		$controllerName = "Controller: TuThietKeWebsiteTrenKinhdoanhweb";
		$functionName="Function: index";
		view("TuThietKeWebsiteTrenKinhdoanhweb", compact("controllerName", "functionName"));
	}

}

