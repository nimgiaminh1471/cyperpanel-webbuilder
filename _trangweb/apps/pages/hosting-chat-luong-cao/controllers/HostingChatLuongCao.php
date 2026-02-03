<?php
namespace pages\hosting__chat__luong__cao\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class HostingChatLuongCao{

	public function index(){
		$controllerName = "Controller: HostingChatLuongCao";
		$functionName="Function: index";
		view("HostingChatLuongCao", compact("controllerName", "functionName"));
	}

}

