<?php
namespace pages\user\controllers;
use models\Users;
use DB;

permission("member", null, "/user/login");

class Profile{

	public function index($tab="info"){
		view("Profile", compact("tab"));
	}

}

