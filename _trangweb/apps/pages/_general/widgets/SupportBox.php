<?php
/*
# Mục hỗ trợ
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget, Storage;
class SupportBox{

	//Thông số mặc định
	private static $option=[
		"title"            => "Các kênh hỗ trợ",
		"description"      => null,
		"links"            => [],
		"background_image" => null,
		"icon"             => null,

	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Mục link hỗ trợ",
			"icon"  => "fa-support",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$primaryBG = Storage::setting("theme__primary_background");
		$background = empty($background_image) ? "background-color: {$primaryBG}" : "background-image: url({$background_image})";
		$description = nl2br($description);
		$linksHTML = "";
		if( is_array($links) ){
			foreach($links as $item){
				$linksHTML .= '
					<div class="center support-box-link" style="width: '.(100 / count($links)).'%">
						<a href="'.(empty($item['link']) ? 'javascript:void(0)' : $item['link']).'" class="block" style="color: white !important">
							<span class="pd-5 block">
								<img src="'.$item['icon'].'" alt="'.$item['title'].'" style="max-height: 60px">
							</span>
							<span class="pd-5 block">
								'.$item['title'].'
							</span>
						</a>
					</div>
				';
			}
		}
		return <<<HTML
					<style type="text/css">
						@media(max-width: 767px){
							.support-box-link{
								margin-top: 5px;
								width: 50% !important
							}
						}
					</style>
					<section style="{$background}; color: #FAFAFA; padding: 30px 0">
						<div class="main-layout support-box">
							<div class="flex flex-middle" style="padding: 10px">
								<div class="center" style="width: 60px;">
									<img src="{$icon}" alt="." style="max-width: 90%">
								</div>
								<div style="width: calc(100 - 60px)">
									<div style="font-size: 26px;">
										{$title}
									</div>
									<div style="font-size: 16px; line-height: 26px;margin-top: 10px">
										{$description}
									</div>
								</div>
							</div>
							<div class="flex flex-middle">
								{$linksHTML}
							</div>
						</div>
					</section>
				HTML;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"image", "name"=>"background_image", "title"=>"Ảnh nền", "value"=>$background_image, "post"=>0],
			["type"=>"image", "name"=>"icon", "title"=>"Ảnh icon tiêu đều", "value"=>$icon, "post"=>0],
			["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=> $title, "attr"=>''],
			["type"=>"textarea", "name"=>"description", "title"=>"Giới thiệu", "note"=>"", "value"=> $description, "attr"=>'', "full"=>true],
			["html"=>
				Form::itemManager([
					"data"     => $links,
					"name"     => "{$prefixName}[data][links]",
					"sortable" => true,
					"max"      => 10,
					"form"     => [
						["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=> null, "attr"=>''],
						["type"=>"text", "name"=>"link", "title"=>"Link", "note"=>"", "value"=> null, "attr"=>''],
						["type"=>"image", "name"=>"icon", "title"=>"Ảnh icon", "value"=>null, "post"=>0],
					]
				])
			],
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