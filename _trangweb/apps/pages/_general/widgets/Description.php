<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget;
class Description{

	//Thông số mặc định
	private static $option=[
		"service"       => [],
		"iconSize"      => 70,
		"large_column"  => 3,
		"medium_column" => 3,
		"small_column"  => 2,
		"description"   => ""
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Giới thiệu dịch vụ",
			"icon"  => "fa-image",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out='

		';
		$out.='
		<section>
		<div class="main-layout service-description">
		<div class="heading-basic">
			'.($title??"").'
			<div>
				<i></i>
				<i></i>
				<i></i>
			</div>
		</div>
		<div class="center" style="font-size: 20px">'.($description ?? "").'</div>
		<div class="flex flex-center">
		';
		if( is_array($service) ){
			foreach($service as $item){
				$out.='
				<div data-aos="zoom-in" data-aos-anchor-placement="top-bottom" data-aos-duration="2000">
					<a '.( strpos($item['link'], '://') === false ? '' : 'target="_blank"').' href="'.(empty($item['link']) ? 'javascript: void(0)' : $item['link']).'">
						<span class="service-item-icon">
							<span>
								<img src="'.$item["src"].'" alt="BG" />
							</span>
						</span>
						<span class="pd-5" style="font-size: 18px">
							<b>'.$item["title"].'</b>
						</span>
						<span class="pd-5 service-description-item-description">
							'.$item["text"].'
						</span>
					</a>
				</div>
				';
			}
		}
		$out.='</div></div></section>';
		if(PAGE_EDITOR){
			$style='
			.service-description{
				padding: 50px 10px;
			}
			.service-description>.flex{
				justify-content: center;
			}
			.service-description>.flex>div{
				width: '.(100 / $large_column).'%;
				padding: 10px;
				text-align: center;
				transition: .4s all;
			}
			.service-description>.flex>div:hover{
				opacity: .7
			}
			.service-description>.flex>div>a>span{
				display: block;
				line-height: 1.5
			}
			.service-description>.flex>div .service-item-icon{
				transition: .4s all;
			}
			.service-description>.flex>div:hover .service-item-icon{
				transform: scale(1.1);
			}
			.service-description .service-item-icon{
				display: block;
				text-align: center;
			}
			.service-description .service-item-icon>span{
				width: '.$iconSize.'px;
				height: '.$iconSize.'px;
				border-radius: 50%;
				display: inline-block;
				padding: 5px;
				position: relative;
			}
			.service-description .service-item-icon>span>img{
				max-width: '.($iconSize-20).'px;
				max-height: '.($iconSize-20).'px;
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
			}
			.service-description-item-description{
				font-size: 90%;
				line-height: 1.5
			}
			@media(max-width: 767px){
				.service-description>.flex>div{
					width: '.(100 / $small_column).'%;
					padding: 10px;
					text-align: center;
					transition: .4s all;
				}
			}

			@media(min-width: 768px) and (max-width: 1023px){
				.service-description>.flex>div{
					width: '.(100 / $medium_column).'%;
					padding: 10px;
					text-align: center;
					transition: .4s all;
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
			["type"=>"textarea", "name"=>"description", "title"=>"Giới thiệu", "note"=>"", "value"=>$description, "attr"=>'', "full"=>true],
			["type"=>"number", "name"=>"iconSize", "title"=>"Cỡ ảnh biểu tượng", "note"=>"", "min"=>30, "max"=>9999, "value"=>$iconSize,"attr"=>''],
			["type"=>"number", "name"=>"large_column", "title"=>"Số cột (máy tính)", "note"=>"", "min"=>1, "max"=>10, "value"=>$large_column,"attr"=>''],
			["type"=>"number", "name"=>"medium_column", "title"=>"Số cột (máy tính bảng)", "note"=>"", "min"=>1, "max"=>10, "value"=>$medium_column,"attr"=>''],
			["type"=>"number", "name"=>"small_column", "title"=>"Số cột (điện thoại)", "note"=>"", "min"=>1, "max"=>10, "value"=>$small_column,"attr"=>''],
			["html"=>
				Form::itemManager([
					"data"=>$service,
					"name"=>"{$prefixName}[data][service]",
					"sortable"=>true,
					"max"=>50,
					"form"=>[
						["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"link", "title"=>"Link", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"image", "name"=>"src", "title"=>"Ảnh biểu tượng", "value"=>"", "post"=>0],
						["type"=>"textarea", "name"=>"text", "title"=>"Nội dung", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
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