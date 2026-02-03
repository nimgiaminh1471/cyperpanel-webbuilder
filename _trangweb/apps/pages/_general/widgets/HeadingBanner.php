<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget;
class HeadingBanner{

	//Thông số mặc định
	private static $option=[
		"type"                   => 1,
		"background_image"       => "",
		"background_opacity"     => 0.5,
		"background_color1"      => "#dbf6c8",
		"background_color2"      => "#1cafc6",
		"background_color3"      => "#012690",
		"search"                 => false,
		"b1_title"               => "Tiêu đề phụ",
		"b1_heading_title"       => "Tiêu đề chính",
		"b1_heading_description" => "Giới thiệu",
		"b2_banner_image"        => "",
		"b2_main_title"          => "Tiêu đề chính",
		"b2_main_advantages"     => "Các ưu điểm",
		"b2_main_description"    => "Mô tả nút",
		"b2_button_label"        => "Tiêu đề nút",
		"b2_button_link"         => "link",
		"b2_heading_title"       => "Tiêu đề phụ",
		"b2_heading_description" => "Mô tả phụ"
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Banner đầu trang",
			"icon"  => "fa-map-o",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out = "";
		if(PAGE_EDITOR){
			$out .= '
			<style>
				#header{
					position: relative !important
				}
			</style>
			';
		}
		switch($type){
			// Kiểu 1
			case 1:
				$out .= headingBanner([
					"title"               => $b1_title,
					"heading_title"       => $b1_heading_title,
					"heading_description" => nl2br($b1_heading_description),
					"background_image"    => $background_image,
					"background_color1"   => $background_color1,
					"background_color2"   => $background_color2,
					"background_color3"   => $background_color3,
					"background_opacity"  => $background_opacity,
					"search"              => $search
				]);
			break;

			// Kiểu 2
			case 2:
				$out .= headingBanner2([
					"main_title"          => $b2_main_title,
					"main_advantages"     => $b2_main_advantages,
					"main_description"    => $b2_main_description,
					"button_label"        => $b2_button_label,
					"button_link"         => $b2_button_link,
					"heading_title"       => $b2_heading_title,
					"heading_description" => $b2_heading_description,
					"banner_image"        => $b2_banner_image,
					"background_image"    => $background_image,
					"background_color1"   => $background_color1,
					"background_color2"   => $background_color2,
					"background_color3"   => $background_color3,
					"background_opacity"  => $background_opacity,
				]);
			break;
		}
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"select", "name"=>"type", "title"=>"Kiểu banner", "option"=>["1"=>"Kiểu 1", "2"=>"Kiểu 2"], "value"=>$type, "attr"=>' data-id="postsListType" '],
			["type"=>"image", "name"=>"background_image", "title"=>"Ảnh nền", "value"=>$background_image, "post"=>0],
			["type"=>"number", "name"=>"background_opacity", "title"=>"Độ trong suốt ảnh nền", "note"=>"", "min"=>0, "max"=>1, "value"=>$background_opacity,"attr"=>' step="0.1"'],
			["type"=>"color", "name"=>"background_color1", "title"=>"Màu nền 1", "default"=>"", "value"=>$background_color1, "required"=>true],
			["type"=>"color", "name"=>"background_color2", "title"=>"Màu nền 2", "default"=>"", "value"=>$background_color2, "required"=>true],
			["type"=>"color", "name"=>"background_color3", "title"=>"Màu nền 3", "default"=>"", "value"=>$background_color3, "required"=>true],
			["type"=>"switch", "name"=>"search", "title"=>"Thanh tìm kiếm", "value"=>$search],
			["html"=>'<div class="posts-list-type '.($type == 1 ? "" : "hidden").'" data-id="1">'],
				["type"=>"text", "name"=>"b1_heading_title", "title"=>"Tiêu đề chính", "note"=>"", "value"=>$b1_heading_title, "attr"=>''],
				["type"=>"textarea", "name"=>"b1_heading_description", "title"=>"Mô tả", "note"=>"", "value"=>$b1_heading_description, "attr"=>'', "full"=>true],
				["type"=>"text", "name"=>"b1_title", "title"=>"Tiêu đề phụ", "note"=>"", "value"=>$b1_title, "attr"=>''],
			["html"=>'</div>'],

			["html"=>'<div class="posts-list-type '.($type == 2 ? "" : "hidden").'" data-id="2">'],
				["type"=>"image", "name"=>"b2_banner_image", "title"=>"Ảnh đại diện", "value"=>$b2_banner_image, "post"=>0],
				["type"=>"textarea", "name"=>"b2_main_title", "title"=>"Tiêu đề chính", "note"=>"", "value"=>$b2_main_title, "attr"=>'', "full"=>true],
				["type"=>"textarea", "name"=>"b2_main_advantages", "title"=>"Ưu điểm", "note"=>"", "value"=>$b2_main_advantages, "attr"=>'', "full"=>true],
				["type"=>"text", "name"=>"b2_main_description", "title"=>"Mô tả nút", "note"=>"", "value"=>$b2_main_description, "attr"=>''],
				["type"=>"text", "name"=>"b2_button_label", "title"=>"Tiêu đề nút", "note"=>"", "value"=>$b2_button_label, "attr"=>''],
				["type"=>"text", "name"=>"b2_button_link", "title"=>"Link nút", "note"=>"", "value"=>$b2_button_link, "attr"=>''],
				["type"=>"text", "name"=>"b2_heading_title", "title"=>"Tiêu đề phụ", "note"=>"", "value"=>$b2_heading_title, "attr"=>''],
				["type"=>"textarea", "name"=>"b2_heading_description", "title"=>"Mô tả phụ", "note"=>"", "value"=>$b2_heading_description, "attr"=>'', "full"=>true],
			["html"=>'</div>'],
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