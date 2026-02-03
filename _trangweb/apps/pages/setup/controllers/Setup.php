<?php
namespace pages\setup\controllers;
use DB, Storage, Route;
use models\Users;

if( DB::table("storage")->where("key", "actived")->value("value")==1 ){
	//Chỉ admin mới truy cập được
	//permission("admin", null, "/");
}
class Setup{
	public function index(){
		require_once( Route::path("classes/CreateTable.php") );
		require_once( Route::path("CreateTable.php") );
		if( DB::table("storage")->where("key", "actived")->value("value")==1 ){
			//Nếu đã setup
			view("Helper");
		}else{
			//Tiến hành setup
			view("Install");
		}
	}
}
