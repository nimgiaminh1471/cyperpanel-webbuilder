<?php
namespace pages\posts__categories\controllers;
use models\PostsCategories;
use models\Posts;
use DB;

class CategoriesMain{

	public function main($link=""){
		$categories=PostsCategories::where("link",$link)->first(true);
		if(empty($link) || empty($categories->id)){
			//Chuyên mục không tồn tại
			pageNotfound();
		}else{
			//Chuyên mục tồn tại
			view("CategoriesMain", compact("categories"));
		}
	}

}

