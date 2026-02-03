<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget;
class DescriptionV3{
	private static $in = 0; 
	//Thông số mặc định
	private static $option=[
		"large_column"     => 3,
		"medium_column"    => 3,
		"small_column"     => 2,
		"icon_size"        => 50,
		"icon_radius"      => 50,
		"features"         => [],
		"features_title"   => null,
		"features_caption" => null
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Giới thiệu dịch vụ (V3)",
			"icon"  => "fa-image",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		self::$in++;
		extract( array_replace(self::$option, $option) );
		$out='';

		$out .= '
		<section class="features features-'.self::$in.'">
			<div class="center heading-basic">
				<h2>
					'.$features_title.'
				</h2>
				<div>
					'.$features_caption.'
				</div>
			</div>
		';
		$out .= '
		<div class="main-layout">
		<div class="flex flex-center">
		';
		if( !is_array($features) ){
			$features = [];
		}
		foreach($features as $item){
			$out .= '
			<div class="features-item">
				<div>
					<div class="features-item-icon" '.(empty($item["background_color1"]) ? '' : 'style="padding: 10px; background-image: linear-gradient(40deg,'.$item["background_color1"].' 0,'.$item["background_color2"].' 100%);"').'>
						<img src="'.$item["icon"].'" alt="'.$item["title"].'">
					</div>
					<div class="features-item-title">
						'.$item["title"].'
					</div>
					<div class="features-item-description">
						'.$item["description"].'
					</div>
					'.(empty($item["link"]) ? '' : '
						<div class="features-item-link">
							<a '.( strpos($item['link'], '://') === false ? '' : 'target="_blank"').' class="see-more" href="'.$item["link"].'">
								'.($item['link_title'] ?? '').'
							</a>
						</div>
						<div class="features-item-link-padding"></div>
					').'
				</div>
			</div>
			';
		}
		$out .= '</diV></div>';
		$out .= '</section>';
		
		if(PAGE_EDITOR){
			$style='
			.features{
				padding: 40px 10px 0 10px;
			}
			.features>h2{
				text-align: center;
				font-weight: bold
			}
			.features>.features-caption{
				text-align: center;
				padding: 20px 10px;
				font-size: 16px
			}
			.features-'.self::$in.' .features-item{
				padding: 20px;
				width: '.(100 / $large_column).'%;
			}
			.features .features-item>div{
				background: white;
				display: block;
				padding: 30px 20px 40px 20px;
				box-shadow: 0 1px 30px 0 rgba(55,125,162,.2);
				position: relative;
				height: 100%;
				transition: .3s all;
			}
			.features .features-item>div:hover{
				color: inherit !important;
				box-shadow: 0 1px 30px 0 rgba(55,125,162,.4);
			}
			.features-'.self::$in.' .features-item-icon{
				transition: .3s all;
				position: absolute;
				top: 10px;
				right: 15px;
				width: '.$icon_size.'px;
				height: '.$icon_size.'px;
				border-radius: '.$icon_radius.'px
			}
			.features .features-item:hover .features-item-icon{
				transform: scale(1.08);
			}
			.features-item-link{
				position: absolute;
				bottom: 20px;
				right: 15px;
			}
			.features-item-link-padding{
				height: 30px;
			}
			.features-item-link>a{
				font-size: 14px
			}
			.features .features-item-title{
				display: block;
				padding: 10px 0;
				font-weight: bold;
				font-size: 20px
			}
			.features .features-item-description{
				display: block;
				line-height: 1.5;
				font-size: 16px
			}

			@media(max-width: 767px){
				.features-'.self::$in.' .features-item{
					width: '.(100 / $small_column).'%;
				}
			}
			@media(max-height: 600px){
				
			}

			@media(min-width: 768px) and (max-width: 1023px){
				.features-'.self::$in.' .features-item{
					width: '.(100 / $medium_column).'%;
				}
			}

			@media(min-width: 1024px){

			}
			';
			$out .= Widget::css($style);
		}
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"number", "name"=>"icon_size", "title"=>"Cỡ ảnh biểu tượng", "note"=>"", "min"=>30, "max"=>9999, "value"=>$icon_size,"attr"=>''],
			["type"=>"number", "name"=>"icon_radius", "title"=>"Vo góc biểu tượng", "note"=>"", "min"=>0, "max"=>9999, "value"=>$icon_radius,"attr"=>''],
			["type"=>"number", "name"=>"large_column", "title"=>"Số cột (máy tính)", "note"=>"", "min"=>1, "max"=>10, "value"=>$large_column,"attr"=>''],
			["type"=>"number", "name"=>"medium_column", "title"=>"Số cột (máy tính bảng)", "note"=>"", "min"=>1, "max"=>10, "value"=>$medium_column,"attr"=>''],
			["type"=>"number", "name"=>"small_column", "title"=>"Số cột (điện thoại)", "note"=>"", "min"=>1, "max"=>10, "value"=>$small_column,"attr"=>''],
			["html"=>'<div class="alert-info">Giới thiệu tính năng</div>'],
			["type"=>"text", "name"=>"features_title", "title"=>"Tiêu đề", "note"=>"", "value"=>$features_title, "attr"=>''],
			["type"=>"textarea", "name"=>"features_caption", "title"=>"Mô tả", "note"=>"", "value"=>$features_caption, "attr"=>'', "full"=>true],
			["html"=>
				Form::itemManager([
					"data"=>$features,
					"name"=>"{$prefixName}[data][features]",
					"sortable"=>true,
					"max"=>10,
					"form"=>[
						["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"textarea", "name"=>"description", "title"=>"Mô tả", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
						["type"=>"image", "name"=>"icon", "title"=>"Icon", "value"=>"", "post"=>0],
						["type"=>"color", "name"=>"background_color1", "title"=>"Màu nền 1", "default"=>"", "value"=>"", "required"=>false],
						["type"=>"color", "name"=>"background_color2", "title"=>"Màu nền 2", "default"=>"", "value"=>"", "required"=>false],
						["type"=>"text", "name"=>"link", "title"=>"Liên kết", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"link_title", "title"=>"Tiêu đề liên kết", "note"=>"", "value"=>"", "attr"=>''],
					]
				])
			]
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