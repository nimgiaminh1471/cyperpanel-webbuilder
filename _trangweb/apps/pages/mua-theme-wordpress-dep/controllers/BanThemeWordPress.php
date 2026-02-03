<?php
namespace pages\mua__theme__wordpress__dep\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class MuaThemeWordpressDep{

	public function index(){
		$controllerName = "Controller: MuaThemeWordpressDep";
		$functionName="Function: index";
		view("MuaThemeWordpressDep", compact("controllerName", "functionName"));
	}

}