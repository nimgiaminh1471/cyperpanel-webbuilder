<?php
namespace pages\setup__he__thong__tao__website\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class SetupHeThongTaoWebsite{

	public function index(){
		$controllerName = "Controller: SetupHeThongTaoWebsite";
		$functionName="Function: index";
		view("SetupHeThongTaoWebsite", compact("controllerName", "functionName"));
	}

}

