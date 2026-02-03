<?php
namespace pages\v2\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class V2{

	public function index(){
		$controllerName = "Controller: V2";
		$functionName="Function: index";
		view("V2", compact("controllerName", "functionName"));
	}

}

