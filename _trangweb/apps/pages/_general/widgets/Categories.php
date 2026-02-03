<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
use models\PostsCategories;
use models\Posts;
class Categories{

	//Thông số mặc định
	private static $option=[
		"cateIcon"   =>0,
		"postsCount"=>1,
		"showSub"=>0,
		"panelClass"=>"default"
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  =>"Danh sách chuyên mục",
			"icon"  =>"fa-list-ul",
			"color" =>""			
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		if(empty($categories)){
			return;
		}
		$out="";
		if(!empty($title)){
			$out.='<div class="'.$titleClass.' '.$titleAlign.'"><span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span></div>';
		}
		$out.='<div class="list panel-list">';
		$cate=PostsCategories::whereIn("id", $categories)->get(true);
		foreach($cate as $cat){
			$storage=unserialize($cat->storage);
			if($showSub==1){
				$out.='
				<div class="panel panel-'.$panelClass.' panel-no-border">
					<div class="heading link">'.($cateIcon==1 && !empty($storage["icon"]) ? '<i class="fa fa-icon '.$storage["icon"].'"></i> ' : '').''.$cat->title.'</div>
					<div class="panel-body hidden list" style="padding: 0; margin: 0">
				';
				foreach(PostsCategories::where("parent", $cat->id)->get(true) as $c){
					$storageC=unserialize($c->storage);
					$out.='
					<a href="/posts-categories/'.$c->link.'">
						'.($cateIcon==1 && !empty($storageC["icon"]) ? '<i class="fa fa-icon '.$storageC["icon"].'"></i> ' : '').''.$c->title.'
						'.($postsCount==1 ? ' <span class="badge">'.Posts::whereIn("parent", PostsCategories::children($c->id, true) )->where("status", "public")->total() : '').'
					</a>';
				}
				$out.='
					</div>
				</div>
				';
			}else{
				$out.='
				<a href="/posts-categories/'.$cat->link.'">
					'.($cateIcon==1 && !empty($storage["icon"]) ? '<i class="fa fa-icon '.$storage["icon"].'"></i> ' : '').''.$cat->title.'
					'.($postsCount==1 ? ' <span class="badge">'.Posts::whereIn("parent", PostsCategories::children($cat->id, true) )->where("status", "public")->total() : '').'
				</a>
				';
			}
		}
		$out.='</div>';
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"switch", "name"=>"cateIcon", "title"=>"Dùng icon chuyên mục", "value"=>$cateIcon],
			["type"=>"switch", "name"=>"postsCount", "title"=>"Đếm số bài viết", "value"=>$postsCount],
			["type"=>"switch", "name"=>"showSub", "title"=>"Hiện dạng sub", "value"=>$showSub],
			["type"=>"select", "name"=>"panelClass", "title"=>"Kiểu tiêu đề", "option"=>
				["default"=>"Default", "primary"=>"Primary", "success"=>"Success", "info"=>"Info", "warning"=>"Warning", "danger"=>"Danger"],
				"value"=>$panelClass, "horizontal"=>35
			],
			["html"=>'
				'.call_user_func(function($prefixName, $categories){
					$out="";
					foreach(PostsCategories::select("id")->where("parent",0)->get() as $parent){
						$out.=PostsCategories::checkboxChildren($parent->id,"{$prefixName}[data][categories][]",$categories??[0]);
					}
					return $out;
				}, $prefixName, $categories??[]).'
			'],
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