<?php
namespace pages\admin\controllers;
use models\User;
use models\PostsCategories;
use models\Posts;
use DB;

/*permission("admin", null, "/user/login");*/

class CategoriesManager{
	private static $attribute=[];

	//Thêm & sửa chuyên mục
	public function categoriesUpdateSubmit(){
		PostsCategories::change($_POST);
	}

	//Xóa chuyên mục
	public function deleteCategories(){
		$posts=Posts::where("parent", POST("id"));
		if($posts->exists()){
			$error="Vui lòng xóa hết các bài viết trong chuyên mục này";
		}
		if( count(PostsCategories::children(POST("id")))>0 ){
			$error="Vui lòng xóa chuyên mục con trước";
		}
		if(empty($error)){
			PostsCategories::destroy(POST("id"));
		}

		returnData(["error"=>$error ?? ""]);
	}

	//Cập nhật id chuyên mục con
	public static function updateAllChildren($id,$first=true){
		$getChild=PostsCategories::select("id")->where("parent",$id)->get();
		foreach($getChild as $child){
			self::$attribute[]=$child->id;
			self::updateAllChildren($child->id,false);
		}
		if($first){
			PostsCategories::find($id)->update(["children"=>serialize(self::$attribute)]);
		}
	}

	//Cập nhật id chuyên mục cha
	public static function updateGrandparents($id,$first=true){
		$parent=PostsCategories::where("id",$id)->value("parent");
		$getParent=PostsCategories::select("id")->where("id",$parent)->first(true);
		if(!empty($getParent)){
			self::$attribute[]=$getParent->id;
			self::updateGrandparents($getParent->id,false);
		}
		if($first){
			PostsCategories::find($id)->update(["grandparents"=>serialize(self::$attribute)]);
		}
	}
	
	public static function updateRelationship(){
		foreach(PostsCategories::select("id")->get() as $parent){
			self::$attribute=[];
			self::updateAllChildren($parent->id);
			self::$attribute=[];
			self::updateGrandparents($parent->id);
		}
	}

}

