<?php
namespace pages\chinh__sach__bao__mat\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class ChinhSachBaoMat{

	public function index(){
		$controllerName = "Controller: ChinhSachBaoMat";
		$functionName="Function: index";
		view("ChinhSachBaoMat", compact("controllerName", "functionName"));
	}

}

