<?php
/*
# Thông báo trang không tồn tại
*/
namespace pages\not__found\widgets;
use Form, Storage;
class Notify{

	//Thông số mặc định
	private static $option=[
		"title"           => "Trang không tồn tại",
		"background"      => "",
		"content"         => "Liên kết này không tồn tại",
		"button"          => "Trở về trang chủ",
		"title_font_size" => "30",
		"color"           => "#FFFFFF"
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"     => "Thông báo trang không tồn tại",
			"icon"     => "fa-code",
			"color"    =>"tomato"
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$content = nl2br($content);
		$out = "";
		$primaryColor = Storage::setting("theme__primary_background");
		if(PAGE_EDITOR){
$out .= <<<HTML
	<style>
		header{
			position: relative !important;
		}
	</style>
HTML;
		}
$out .= <<<HTML
	<style type="text/css">
		header{
			position: fixed;
			background: transparent !important;
		}
		.header-fixed{
			display: none
		}
		.section{
			height: 100vh;
			min-width: 100vw;
			position: relative;
			margin: 0;
		}
		.section:before{
			content: "";
			width: 100%;
			height: 100%;
			opacity: .5;
			position: absolute;
			background-image: url({$background});
			background-repeat: no-repeat;
			object-fit: cover;
			background-size: cover;
		}
		.section>div{
			position: absolute;
			top: 40%;
			left: 50%;
			transform: translate(-50%, -50%);
			text-align: center;
			padding-top: 100px;
			width: 80%
		}
		.section-description{
			color: {$color};
			line-height: 1.6;
			font-size: {$title_font_size}px
		}
		.section-description a{
			margin-top: 30px;
			padding: 15px 30px;
			color: white;
			border: 2px solid white;
			display: inline-block;
			font-size: 18px;
			border-radius: 40px
		}
		.section-description a:hover{
			opacity: .7
		}
		.section-description h2>span{
			background: {$primaryColor};
			color: #fff;
			padding: 0 15px;
			box-shadow: 0px 0px 10px 1px #000;
			font-size: 90px
		}
		.section-description>h2{
			margin: 20px 0
		}
		.section-description>div{
			border: 5px solid {$primaryColor};
			padding: 20px;
		}
		@media(max-width: 768px){
			.section-description{
				font-size: 16px
			}
			.section-description>div{
				padding: 25px 10px;
			}
			.section>div{
				top: 40%;
				width: 90%
			}
			.section-description h2>span{
				font-size: 40px
			}
		}
	</style>
	<section class="section">
		<div>
			<div class="section-description">
				<h2>
					<span>4</span>
					<span><i class="fa fa-circle-o-notch" aria-hidden="true"></i></span>
					<span>4</span>
				</h2>
				<div>
					<div>
						{$content}
					</div>
					<div>
						<a href="/" class="btn-gradient">
							<i class="fa fa-icon fa-home"></i>
							{$button}
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>
HTML;
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"text", "name"=>"button", "title"=>"Tiêu đề nút", "note"=>"", "value"=>$button, "attr"=>'', "horizontal"=>35],
			["type"=>"color", "name"=>"color", "title"=>"Màu chữ", "default"=>"#FFFFFF", "value"=>$color, "required"=>true],
			["type"=>"image", "name"=>"background", "title"=>"Ảnh nền", "value"=>$background, "post"=>0],
			["type"=>"textarea", "name"=>"content", "title"=>"Nội dung", "note"=>"", "value"=>$content, "attr"=>'', "full"=>true],
			["type"=>"number", "name"=>"title_font_size", "title"=>"Cỡ chữ", "note"=>"", "min"=>0, "max"=>9999, "value"=>$title_font_size,"attr"=>''],
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