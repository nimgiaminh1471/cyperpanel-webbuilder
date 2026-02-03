<?php
/*
# Thiết lập nhanh cho từng trang
*/
class PageOption{
	private static $language=[];
	//Box thiết lập trang
	public static function main(){
		if(!permission("admin")){
			return;
		}
		//Lưu thiết lập nhanh
		if(isset($_POST["pageOption"])){
			foreach($_POST["pageOption"] as $sName=>$data){
				Storage::update($sName, $data);
			}
			redirect("", true);
		}

		//Form thiết lập nhanh
		$name="pageOption[option][".Route::folder()."]";
		$storage=Storage::option(Route::folder());
		$out='<form action="" method="POST">';
		$form[]=["html"=>'<section class="panel-list">'];
		//Thông tin trang
		if(empty(PAGE["title"])){
			$form[]=["html"=>'
			<div class="panel panel-default">
				<div class="heading link">Thông tin '.PAGE["name"].'</div>
				<div class="panel-body hidden">
			'];
				$form[]=["type"=>"text", "name"=>"{$name}[title]", "title"=>"Tiêu đề", "note"=>"", "value"=>$storage["title"]??PAGE["name"], "attr"=>''];
				$form[]=["type"=>"textarea", "name"=>"{$name}[description]", "title"=>"Mô tả", "note"=>"", "value"=>$storage["description"]??PAGE["name"], "attr"=>' maxlength="300" style="width: 100%"', "full"=>true];
			$form[]=["html"=>'</div></div>'];
		}

		//Bố cục trang
		$form[]=["html"=>'
		<div class="panel panel-default">
			<div class="heading link">Bố cục '.PAGE["name"].'</div>
			<div class="panel-body hidden">
		'];
			$form[]=["type"=>"number", "name"=>"{$name}[layout_main]", "title"=>"Chiều rộng tối đa khung", "note"=>"", "min"=>0, "max"=>99999, "value"=>$storage["layout_main"]??1100,"attr"=>'step="50"'];
			$form[]=["type"=>"number", "name"=>"{$name}[layout_left]", "title"=>"Chiều rộng cột trái (%)", "note"=>"", "min"=>0, "max"=>100, "value"=>$storage["layout_left"]??0,"attr"=>'step="5"'];
			$form[]=["type"=>"number", "name"=>"{$name}[layout_mid]", "title"=>"Chiều rộng cột giữa (%)", "note"=>"", "min"=>0, "max"=>100, "value"=>$storage["layout_mid"]??70,"attr"=>'step="5"'];
			$form[]=["type"=>"number", "name"=>"{$name}[layout_right]", "title"=>"Chiều rộng cột phải (%)", "note"=>"", "min"=>0, "max"=>100, "value"=>$storage["layout_right"]??30,"attr"=>'step="5"'];
		$form[]=["html"=>'</div></div>'];
		
		//Ngôn ngữ trang
		if(!empty(self::$language)){
			$form[]=["html"=>'
			<div class="panel panel-default">
				<div class="heading link">Văn bản hiển thị</div>
				<div class="panel-body hidden">
			'];
			foreach(self::$language as $key=>$value){
				if(strlen($value)>30){
					$input="textarea";
				}else{
					$input="text";
				}
				$form[]=["type"=>$input, "name"=>"pageOption[language][$key]", "title"=>"", "note"=>$value, "value"=>$value, "attr"=>''];
			}
			$form[]=["html"=>'</div></div>'];
		}
		$form[]=["html"=>'</section>'];
		$form[]=["html"=>'<div class="center pd-5"><input class="btn-primary" type="submit" name="pageOptionSave" value="Lưu lại" /></div>'];
		$out.=Form::create([
			"form"=>$form,
			"function"=>"",
			"prefix"=>"",
			"name"=>"",
			"class"=>"menu",
			"hover"=>false
		]);
		$out.='</form>';
		return $out;
	}

	//Lưu ngôn ngữ từng trang
	public static function language($lng){
		self::$language=array_replace(self::$language, $lng);
	}
}