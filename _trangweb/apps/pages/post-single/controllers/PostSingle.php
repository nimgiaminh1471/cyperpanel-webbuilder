<?php
/*
# Trang bài đăng
*/
namespace pages\post__single\controllers;
use models\Posts;
use DB;

class PostSingle{

	public function main($link=""){
		$getPost=Posts::select("id")->where("link", $link)->first(true);
		if( empty($getPost->id) || is_numeric($link) ){
			pageNotfound();
		}else{
			$post=Posts::get($getPost->id, true);
			if(SESSION("post_count_id")!=$post->id){
				Posts::where("id", $post->id)->update(["count"=>($post->count+1)]);//Cộng lượt xem
			}
			$_SESSION["post_count_id"]=$post->id;
			if(!permission("post") && $post->status!="public"){
				redirect("/");
			}
			view("PostSingle", compact("post"));
		}
	}

}

