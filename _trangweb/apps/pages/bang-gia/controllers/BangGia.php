<?php
namespace pages\bang__gia\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class BangGia{

	public function index(){
		$controllerName = "Controller: BangGia";
		$functionName="Function: index";
		view("BangGia", compact("controllerName", "functionName"));
	}

}

