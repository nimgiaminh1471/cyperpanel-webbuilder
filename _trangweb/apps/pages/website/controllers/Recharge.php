<?php
namespace pages\website\controllers;
use models\Users;
use DB;

permission("member", null, "/user/login");

class Recharge{

	public function index(){
		$controllerName = "Controller: Recharge";
		$functionName="Function: index";
		view("Recharge", compact("controllerName", "functionName"));
	}

}

