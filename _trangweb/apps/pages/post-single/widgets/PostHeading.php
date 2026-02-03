<?php
/*
# Bên trên bài viết
*/
namespace pages\post__single\widgets;
use classes\PostsListTemplate;
use Form;
use models\PostsCategories;
use models\Posts;
use DB;
class PostHeading{

	//Thông số mặc định
	private static $option=[
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => 'Tiêu đề bài viết',
			"icon"  => "fa-list-ol",
			"color" =>"tomato"
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$post = WIDGET_DATA["post"];
		extract($post);
		$date = date("d/m/Y", timestamp($updated_at));
		$background = ($storage["background"] ?? "");
		$bcrumb = "";
		$breadcrumbData["/"] = '<i class="fa fa-home"></i>';
		if($post["parent"] > 0){
			$parents = PostsCategories::grandparents($post["parent"], true);
			$getCate = PostsCategories::select("link", "title")->whereIn("id", $parents)->get();
			foreach($getCate as $cat){
				$link="/posts-categories/".$cat->link;
				$breadcrumbData[$link]=$cat->title;
			}
			if(!empty($breadcrumbData)){
				$bcrumb=breadcrumb($breadcrumbData);
			}
		}
		$out = <<<HTML
			<section class="post-single-heading">
				<div class="post-single-heading-poster">
					<img src="{$background}" alt="{$title}">
				</div>
				<div class="post-single-heading-content">
					<div class="main-layout flex flex-large pd-20">
						<div class="main-left"></div>
						<div class="main-mid">
							<h1>
								<span itemprop="name">
									{$title}
								</span>
							</h1>
							<div class="post-single-heading-line"></div>
							<div class="post-single-heading-meta flex flex-middle flex-medium">
								<div class="width-70">
									{$bcrumb}
								</div>
								<div class="width-30 right">
									<div style="margin-right: 15px">
										<i class="fa fa-clock-o"></i> {$date}
									</div>
								</div>
							</div>
						</div>
						<div class="main-right"></div>
					</div>
				</div>
			</section>
			<style type="text/css">
				html{
					background-color: white !important
				}
				.post-single-heading{
					height: 400px;
					position: relative;
					overflow: hidden;
					background-color: black;
				}
				.post-single-heading-poster>img{
					min-width: 100%;
					height: 100%;
					opacity: .5;
					object-fit: cover;
				}
				.post-single-heading-content{
					position: absolute;
					bottom: 20px;
					width: 100%;
					left: 50%;
					transform: translate(-50%, 0);
					color: white;
				}
				.post-single-heading-content h1{
					font-weight: bold;
					font-size: 38px;
					padding-bottom: 20px
				}
				.post-single-heading-line{
					height: 1px;
					width: 100%;
					max-width: 320px;
					background-color: #EAEAEA
				}
				.post-single-heading-meta{
					padding-top: 20px;
				}
				.post-single-heading-meta a{
					color: white !important
				}
			</style>
		HTML;

		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			
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