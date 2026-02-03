<?php
namespace pages\admin\controllers;
use models\User;
use DB,Route;

permission("member", null, "/user/profile");

class Main{



	//Trang quản trị
	public function index($panelName=""){
		define("PANEL_NAME", $panelName);
		view("Admin");
	}



}

