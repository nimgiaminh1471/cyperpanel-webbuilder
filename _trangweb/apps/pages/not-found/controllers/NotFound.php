<?php
namespace pages\not__found\controllers;
use models\Users;
use DB;

permission("admin", null, "/user/login");

class NotFound{

	public function func(){
		$controllerName = "Controller: NotFound";
		$functionName="Function: func";
		view("NotFound", compact("controllerName", "functionName"));
	}

}

