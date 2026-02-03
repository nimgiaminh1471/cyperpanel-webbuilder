<?php
namespace pages\quy__dinh__su__dung\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class QuyDinhSuDung{

	public function index(){
		$controllerName = "Controller: QuyDinhSuDung";
		$functionName="Function: index";
		view("QuyDinhSuDung", compact("controllerName", "functionName"));
	}

}

