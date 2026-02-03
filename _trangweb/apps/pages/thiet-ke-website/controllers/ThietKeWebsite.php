<?php
namespace pages\thiet__ke__website\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class ThietKeWebsite{

	public function index(){
		$controllerName = "Controller: ThietKeWebsite";
		$functionName="Function: index";
		view("ThietKeWebsite", compact("controllerName", "functionName"));
	}

}

