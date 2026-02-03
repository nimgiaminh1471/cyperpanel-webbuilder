<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
class Editor{

	//Thông số mặc định
	private static $option=[
		"content"=>"<p>Nội dung tùy ý</p>",
		"content2"=>"",
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Nội dung tùy ý",
			"icon"  => "fa-code",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out="";
		if(!empty($title)){
			$out.='<div class="'.$titleClass.' '.$titleAlign.'"><span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span></div>';
		}
		$out.=htmlEditorOutput($content);
		$out.=$content2;
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["html"=>'<div class="pd-10 bg"></div>'],
			["type"=>"editor", "name"=>"content", "title"=>"", "value"=>$content],
			["type"=>"textarea", "name"=>"content2", "title"=>"Văn bản, mã HTML,JavaScript thuần", "note"=>"Hoặc văn bản, mã HTML,JavaScript thuần", "value"=>htmlEncode($content2), "attr"=>'rows="15"', "full"=>true],
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