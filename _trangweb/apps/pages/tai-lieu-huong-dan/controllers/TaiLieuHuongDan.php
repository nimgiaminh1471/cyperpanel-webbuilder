<?php
namespace pages\tai__lieu__huong__dan\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class TaiLieuHuongDan{

	public function index(){
		$controllerName = "Controller: TaiLieuHuongDan";
		$functionName="Function: index";
		view("TaiLieuHuongDan", compact("controllerName", "functionName"));
	}

}

