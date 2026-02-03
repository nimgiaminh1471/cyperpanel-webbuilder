<?php
namespace pages\admin\controllers;
use models\PostsCategories;
use models\Posts;
use DB;

permission("post", null, "/user/login");

class PostEditor{

	//Đăng bài viết
	public function postSubmit(){
		Posts::change($_POST["post"]);
	}

	//Tạo link bài viết
	public function postCreateLink(){
		$link=vnStrFilter($_POST["postCreateLink"] ?? "");
		if(Posts::where("link", $link)->exists()){
			$link=$link."-".time();
		}
		returnData(["link"=>$link]);
	}

}

