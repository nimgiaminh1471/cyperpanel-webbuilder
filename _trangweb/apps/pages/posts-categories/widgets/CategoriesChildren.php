<?php
/*
# Chuyên mục con
*/
namespace pages\posts__categories\widgets;
use Form;
use models\PostsCategories;
use models\Posts;
class CategoriesChildren{

	//Thông số mặc định
	private static $option=[
		"postsCount"=>1,
		"linkSize"=>15
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Danh sách chuyên mục con",
			"icon"  => "fa-code",
			"color" =>"tomato"
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		extract(WIDGET_DATA);
		$categoriesChildren=PostsCategories::whereIn("id", PostsCategories::children($categories["id"]) )->get(true);
		$out="";
		if(!empty($categoriesChildren)){
		$out.='
			<div class="list">
				<div class="'.$titleClass.' '.$titleAlign.'"><span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span></div>';
				foreach($categoriesChildren as $cate){
					$icon='';
					$storage=unserialize($cate->storage);
					if( !empty($storage["icon"]) ){
						$icon='<i class="fa-icon fa '.$storage["icon"].'"></i> ';
					}
					$out.='<a style="font-size: '.$linkSize.'px" href="/posts-categories/'.$cate->link.'">'.$icon.''.$cate->title.''.($postsCount==1 ? ' <span class="badge">'.Posts::whereIn("parent", PostsCategories::children($cate->id, true) )->where("status", "public")->total() : '').'</span></a>';
				}
		$out.='</div>';
		}
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"switch", "name"=>"postsCount", "title"=>"Đếm số bài viết", "value"=>$postsCount],
			["type"=>"number", "name"=>"linkSize", "title"=>"Cỡ link", "note"=>"", "min"=>0, "max"=>9999, "value"=>$linkSize,"attr"=>''],
		];
		$out.=Form::create([
			"form"=>$form,
			"function"=>"",
			"prefix"=>"",
			"name"=>"{$prefixName}[data]",
			"class"=>"menu",
			"hover"=>false
		]);
		return $out;
	}




}
//</Class>