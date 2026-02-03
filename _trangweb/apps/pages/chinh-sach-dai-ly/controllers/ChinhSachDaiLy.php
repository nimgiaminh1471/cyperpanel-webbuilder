<?php
namespace pages\chinh__sach__dai__ly\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class ChinhSachDaiLy{

	public function index(){
		$controllerName = "Controller: ChinhSachDaiLy";
		$functionName="Function: index";
		view("ChinhSachDaiLy", compact("controllerName", "functionName"));
	}

}

