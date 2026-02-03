<?php
namespace pages\blog\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class Blog{

	public function index(){
		$controllerName = "Controller: Blog";
		$functionName="Function: index";
		view("Blog", compact("controllerName", "functionName"));
	}

}

