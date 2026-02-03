<?php
namespace pages\reinstall\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class Reinstall{

	public function index(){
		$controllerName = "Controller: Reinstall";
		$functionName="Function: index";
		view("Reinstall", compact("controllerName", "functionName"));
	}

}

