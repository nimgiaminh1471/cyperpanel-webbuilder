<?php
/*
# Giới thiệu dịch vụ
*/
namespace pages\tai__lieu__huong__dan\widgets;
use Form;
use Assets, Widget;
class SupportBanner{

	//Thông số mặc định
	private static $option=[
		"title"             => "GIỚI THIỆU",
		"background"        => "",
		"background_height" => 980,
		"background_color"  => "#FFFFFF",
		"text_color"        => "#000000",
		"item_radius"       => 25,
		"item_opacity"      => 1,
		"search"            => 1
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Tìm kiếm hỗ trợ",
			"icon"  => "fa-search",
			"color" => "tomato"
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out='
		<div class="service-description">
		<div class="service-description-outer">
		<h2 class="heading-service center" '.(PAGE_EDITOR ? '' : 'data-aos="fade-down" data-aos-anchor-placement="top-bottom" data-aos-duration="1000" data-aos-anchor=".service-description"').'>
			<span>'.$title.'</span>
		</h2>
		<div class="flex flex-medium" style="align-items: stretch;">
			';
		$i=0;
		$out.='
		</div>
		'.($search==1 ? '
		<div class="center pd-20" '.(PAGE_EDITOR ? '' : 'data-aos="fade-up" data-aos-anchor-placement="top-bottom" data-aos-duration="1000" data-aos-anchor=".service-description"').'>
			<form action="/search" method="GET">
				<div class="input-search width-80">
					<input placeholder="'.__("Bạn cần hỗ trợ gì?").'" class="input" type="search" name="keyword" value="'.GET("keyword").'" required="" id="website-template-search" style="width: 100%;" >
					<button type="submit"><i class="fa fa-search"></i></button>
				</div>
			</form>
		</div>
		' : '').'
		</div>
		</div>

		';
		if(PAGE_EDITOR){
			$style='
			.service-description{
				background-image: url('.$background.');
				background-position: top;
				background-size: cover;
				background-repeat: no-repeat;
				position: relative;
				height: 50vh;
				max-height: '.$background_height.'px;
				overflow: hidden
			}
			.service-description-outer{
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%,-50%);
				padding: 5px;
				width: 100%;
				max-width: 1024px
			}
			.service-description .heading-service{
				font-size: 25px;
				color: '.$background_color.';
				line-height: 2.0;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
			}
			.service-description .flex>div{
				padding: 10px;
			}
			.service-description .flex>div>div{
				background: '.$background_color.';
				color: '.$text_color.';
				height: 100%;
				padding: 10px;
				text-align: center;
				border-radius: '.$item_radius.'px;
				opacity: '.$item_opacity.';
				transition: .2s all;
			}
			.service-description .flex>div>div:hover,
			.service-description .input-search>input:focus{
				opacity: '.($item_opacity+0.1).'
			}
			.service-title{
				font-size: 20px;
				font-weight: bold;
				padding-bottom: 2px;
			}
			.service-description .input-search>input{
				background: '.$background_color.' !important;
				opacity: '.$item_opacity.';
				width: 250px
			}
			@media(max-width: 767px){
			}

			@media(min-width: 768px) and (max-width: 1023px){
				.intro-outer{
					
				}
			}

			@media(min-width: 1024px){
				
			}
		';
			$out.=Widget::css($style);
		}
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"image", "name"=>"background", "title"=>"Ảnh nền", "value"=>$background, "post"=>0],
			["type"=>"number", "name"=>"background_height", "title"=>"Chiều cao tối đa", "value"=>$background_height, "min"=>100, "max"=>1421997, "attr"=>'step="50"'],
			["type"=>"color", "name"=>"text_color", "title"=>"Màu chữ", "default"=>$text_color, "value"=>$text_color, "required"=>true],
			["type"=>"color", "name"=>"background_color", "title"=>"Màu nền", "default"=>$background_color, "value"=>$background_color, "required"=>true],
			["type"=>"number", "name"=>"item_radius", "title"=>"Độ bo góc", "value"=>$item_radius, "min"=>0, "max"=>1997],
			["type"=>"number", "name"=>"item_opacity", "title"=>"Độ trong suốt", "value"=>$item_opacity, "min"=>0.1, "max"=>1, "attr"=>'step="0.1"'],
			["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=>$title, "attr"=>''],
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