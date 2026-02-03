<?php
namespace pages\replace\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class Replace{

	public function index(){
		$controllerName = "Controller: Replace";
		$functionName="Function: index";
		view("Replace", compact("controllerName", "functionName"));
	}

}

