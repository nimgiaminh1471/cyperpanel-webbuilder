<?php
namespace pages\search\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class Search{

	public function index(){
		$controllerName = "Controller: Search";
		$functionName="Function: index";
		view("Search", compact("controllerName", "functionName"));
	}

}

