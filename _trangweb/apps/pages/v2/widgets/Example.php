<?php
/*
# Widget
*/
namespace pages\v2\widgets;
use classes\Form;
class Example{

	//Thông số mặc định
	private static $option=[
		"content"=>"<p>Test</p>"
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "",//Đặt tên để kích hoạt
			"icon"  => "fa-code",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out=htmlEditorOutput($content);
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"editor", "name"=>"content", "title"=>"", "value"=>$content],
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
