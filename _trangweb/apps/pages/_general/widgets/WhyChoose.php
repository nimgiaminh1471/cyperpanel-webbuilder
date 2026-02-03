<?php
/*
# Tại sao chọn chúng tôi
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget;
class WhyChoose{

	//Thông số mặc định
	private static $option=[
		"image"       => '',
		"description" => ''
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Tại sao chọn chúng tôi",
			"icon"  => "fa-question",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out='
		<div class="center heading-basic" style="padding-top: 60px">
			<h2>CÙNG BẠN XÂY DỰNG THƯƠNG HIỆU</h2>
		</div>
		';
		$out .= '<section class="flex flex-middle why-choose main-layout">';
		$out .= '
			<div class="width-50">
				<img src="'.$image.'" alt="Why choose">
			</div>
		';
		$out .= '<div class="width-50"><ul>';
		foreach(explode(PHP_EOL, $description) as $item){
			$out .= '
				<li class="flex">
					<div class="center" style="width: 50px">
						<img class="why-choose-check-icon" src="/assets/images/checked.png" alt=".">
					</div>
					<div style="width: calc(100% - 50px)">
						'.$item.'
					</div>
				</li>
			';
		}
		$out .= '</ul></div>';
		$out .= '</section>';
		if(PAGE_EDITOR){
			$style='
			.why-choose{
				padding: 50px 10px
			}
			.why-choose-check-icon{
				width: 26px;
				height: 26px;
			}
			.why-choose ul{
				list-style: none;
				padding: 0;
				margin: 0
			}
			.why-choose ul>li{
				padding: 10px;
				line-height: 1.6;
				font-size: 18px
			}
			@media(max-width: 767px){
				.why-choose>div{
					width: 100% !important;
				}
				.why-choose ul>li{
					padding: 6px 2px;
					font-size: 16px
				}
			}

			@media(min-width: 768px) and (max-width: 1023px){
				
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
			["type"=>"textarea", "name"=>"description", "title"=>"Giới thiệu", "note"=>"", "value"=>$description, "attr"=>'', "full"=>true],
			["type"=>"image", "name"=>"image", "title"=>"Ảnh biểu tượng", "value" => $image, "post"=>0, "image" => $image],
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