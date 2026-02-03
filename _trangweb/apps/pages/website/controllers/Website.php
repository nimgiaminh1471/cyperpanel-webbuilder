<?php
namespace pages\website\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class Website{

	public function index($categories = 'all'){
		view("Website", compact("categories"));
	}

	public function preview($template=''){
		view("Preview", compact("template"));
	}
}

