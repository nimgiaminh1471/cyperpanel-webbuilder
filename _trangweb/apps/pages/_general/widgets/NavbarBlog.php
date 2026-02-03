<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form, Storage;
class NavbarBlog{

	//Thông số mặc định
	private static $option=[
		
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Navbar blog",
			"icon"  => "fa-ellipsis-h",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		\Assets::footer("/assets/general/js/blog.js");
		$out = '
			<div class="blog-navbar-wrap bg">
				<section class="main-layout">
					<div class="flex flex-middle">
						<div class="width-70">
							<div data-show="blog_navbar" class="hidden-large blog-nav-icon-mobile">
								<i class="fa fa-bars"></i> Danh mục
							</div>
							'.createBlogNavbar( Storage::option("blog_navbar", []), false, "flex flex-middle").'
						</div>
						<div class="width-30 right">
							<div class="width-100">
								<form medthod="GET" action="/search" class="input-search">
									<input placeholder="'.__("Tìm kiếm").'" class="input" type="search" name="keyword" value="'.GET("keyword").'" required="" style="width: 100%">
									<button type="submit"><i class="fa fa-search"></i></button>
								</form>
							</div>
						</div>
					</div>
				</section>
			</div>
		';
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["html"=>'<div class="pd-10 bg"></div>'],
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