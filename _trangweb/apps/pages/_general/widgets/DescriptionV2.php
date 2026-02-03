<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget;
class DescriptionV2{

	//Thông số mặc định
	private static $option=[
		"service"       => [],
		"iconSize"      => 70,
		"large_column"  => 3,
		"medium_column" => 3,
		"small_column"  => 2,
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Giới thiệu dịch vụ (V2)",
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
		<section style="overflow: hidden">
		<div class="main-layout service-description-v2">
		<div class="heading-basic" data-aos="zoom-in" data-aos-anchor-placement="top-bottom" data-aos-duration="1000">
			'.($title??"").'
		</div>
		<div class="flex flex-center">
		';
		if( is_array($service) ){
			$i = 0;
			foreach($service as $item){
				$i++;
				$out.='
				<div data-aos="fade-'.($i > 2 ? 'left' : 'right').'" data-aos-anchor-placement="top-bottom" data-aos-duration="2000">
					<div class="service-description-v2-item">
						<span class="service-description-v2-item-text">
							<span class="service-v2-item-icon">
								<span>
									<img src="'.$item["src"].'" alt="BG" />
								</span>
							</span>
							<span class="pd-5 service-description-v2-item-description">
								'.$item["text"].'
							</span>
						</span>
					</div>
				</div>
				';
			}
		}
		$out.='</div></div>';
		$out.='
			<form class="main-layout hidden" style="margin-bottom: 60px" method="GET" action="/dang-ky-domain-gia-re">
				<div class="flex pd-10" style="max-width: 550px; margin: auto">
					<div style="width: calc(100% - 110px)">
						<input name="domain" class="input width-100" type="search" placeholder="Nhập tên miền để kiểm tra, VD: tenmien.com" style="border-radius: 30px 0 0 30px; padding: 15px">
					</div>
					<div style="width: 110px">
						<input class="btn-gradient width-100" type="submit" value="KIỂM TRA" style="border-radius: 0 30px 30px 0; padding: 15px">
					</div>
				</div>
			</form>
		';
		$out .='</section>';
		if(PAGE_EDITOR){
			$style='
			.service-description-v2{
				padding: 50px 10px;
				color: white;
			}
			.service-description-v2 .heading-basic{
				color: white;
				text-align: center;
				margin-bottom: 20px
			}
			.service-description-v2>.flex{
				justify-content: center;
			}
			.service-description-v2>.flex>div{
				width: '.(100 / $large_column).'%;
				padding: 10px;
				text-align: center;
				transition: .2s all;
				opacity: .8;
			}
			.service-description-v2>.flex>div:hover{
				transform: scale(1.02)
			}
			.service-description-v2-item{
				display: block;
				padding: 5px;
				height: 210px;
				width: 210px;
				position: relative;
				border-radius: 50%;
				background: rgb(255,255,255,0.3);
				margin: auto;
				transition: .3s all
			}
			.service-description-v2-item:hover{
				background: rgb(255,255,255,0.4);
			}
			.service-description-v2-item-text{
				position: absolute;
				top: 10px;
				left: 50%;
				transform: translate(-50%, 0);
				width: 100%;
				padding: 0 10px 10px 10px;
			}
			.service-v2-item-icon{
				/*position: absolute;
				top: 0;
				left: 50%;
				transform: translate(-50%, 0);
				opacity: .8;*/
			}
			.service-description-v2-item-title,
			.service-description-v2-item-description{
				display: block;
				color: white
			}
			.service-description-v2-item-description{
				line-height: 1.5;
				letter-spacing: 1px
			}
			.service-description-v2 .service-v2-item-icon{
				display: block;
				text-align: center;
			}
			.service-description-v2 .service-v2-item-icon>span{
				width: '.$iconSize.'px;
				height: '.$iconSize.'px;
				border-radius: 50%;
				display: inline-block;
				position: relative;
			}
			.service-description-v2 .service-v2-item-icon>span>img{
				max-width: '.($iconSize-10).'px;
				max-height: '.($iconSize-10).'px;
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
			}
			@media(max-width: 767px){
				.service-description-v2>.flex>div{
					width: '.(100 / $small_column).'%;
				}
				.service-description-v2-item{
					height: 150px;
					width: 150px;
				}
				.service-description-v2-item-description{
					font-size: 80%
				}
				.service-description-v2 .service-v2-item-icon>span{
					width: '.($iconSize-30).'px;
					height: '.($iconSize-30).'px;
				}
				.service-description-v2 .service-v2-item-icon>span>img{
					max-width: '.($iconSize-35).'px;
					max-height: '.($iconSize-35).'px;
				}
			}

			@media(min-width: 768px) and (max-width: 1023px){
				.service-description-v2>.flex>div{
					width: '.(100 / $medium_column).'%;
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