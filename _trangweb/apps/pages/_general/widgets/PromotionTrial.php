<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget, Storage;
class PromotionTrial{

	//Thông số mặc định
	private static $option=[
		"title"            => null,
		"description"      => null,
		"link"             => null,
		"button"           => null,
		"background_image" => null,
		"column"           => true
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Nút quảng cáo dùng thử",
			"icon"  => "fa-buysellads",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		return PromotionTrial([
			"title"            => $title,
			"description"      => $description,
			"button"           => $button,
			"link"             => $link,
			"background_image" => $background_image,
			"column"           => $column
		]);
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"image", "name"=>"background_image", "title"=>"Ảnh nền", "value"=>$background_image, "post"=>0],
			["type"=>"switch", "name"=>"column", "title"=>"Chia cột", "value" => $column],
			["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=> $title, "attr"=>''],
			["type"=>"textarea", "name"=>"description", "title"=>"Giới thiệu", "note"=>"", "value"=> $description, "attr"=>'', "full"=>true],
			["type"=>"text", "name"=>"link", "title"=>"link", "note"=>"", "value"=> $link, "attr"=>''],
			["type"=>"text", "name"=>"button", "title"=>"Tiêu đề nút", "note"=>"", "value"=> $button, "attr"=>''],
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