<?php
namespace pages\admin\controllers;
use models\PostsCategories;
use models\Posts;
use DB;

permission("post", null, "/user/login");

class PostsList{

	//Thao tác bài viết
	public static function checkPostAction(){
		$postsID=isset($_POST["deletePostsById"]) ? [$_POST["deletePostsById"]] : $_POST["postsCheck"];
		foreach($postsID as $id){
			if( Posts::find($id)->users_id!=user("id") && !permission("post_manager") ){
				return "Bạn không thể thao tác bài của người khác!";
			}
		}
		switch(POST("checkPostAction")){
			//Xóa vĩnh viễn
			case "deletePosts":
				Posts::deletePosts($postsID);
			break;

			//Di chuyển vào thùng rác
			case "moveToBinTrash":
				Posts::whereIn("id", $postsID)->update(["status"=>"trash"]);
			break;

			//Khôi phục từ thùng rác
			case "restoreFromBinTrash":
				Posts::whereIn("id", $postsID)->update(["status"=>"public"]);
			break;
		}
		
	}

}

