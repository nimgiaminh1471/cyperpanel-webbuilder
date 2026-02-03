<?php
/*
# Giới thiệu dịch vụ
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget;
class GuestList{

	//Thông số mặc định
	private static $option=[
		"title"=>"DANH SÁCH KHÁCH HÀNG TIÊU BIỂU",
		"data"=>[],
		"background"=>"",
		"slogan"=>"",
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Danh sách khách hàng",
			"icon"  => "fa-users",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out='
		<script src="/assets/slider/script.js"></script>
		<link href="/assets/slider/style__complete.css" rel="stylesheet" />
		<div class="guest-list main-layout">
		<div class="center heading-basic">
			<h2 class="center heading-basic">
				'.$title.'
			</h2>
			<div>'.$slogan.'</div>
		</div>
		';
		if( !is_array($data) ){
			$data=[];
		}
		$out.='
			<div class="slider" style="width:100%;max-height: 550px">
				<ul class="slider-basic" data-autoplay="5">
			';
			foreach(array_chunk($data, 4 ) as $items){
				$out.='<li><div class="guest-list-item flex">';
				foreach($items as $item){
					$out.='
					<div class="width-25">
						<span>
							<img src="'.$item["logo"].'">
						</span>
					</div>
					';
				}
				$out.='</div></li>';
			}
						
			$out.='</ul>
				<i class="slider-btn-prev"></i>
				<i class="slider-btn-next"></i>
			</div>
			';
		$out.='
		</div>

		';
		if(PAGE_EDITOR){
			$style='
			.guest-list{
				padding: 30px 0 50px 0;
			}
			.guest-list>h2{
				padding: 30px 30px 10px 30px;
				font-size: 25px
			}
			.guest-list-item>div{
				padding: 10px
			}
			.guest-list-item>div>span{
				background: white;
				display: block;
				padding: 15px;
				border-radius: 15px;
				text-align: center
			}
			.guest-list-item img{
				max-width: 100%;
				max-height: 60px
			}
			.guest-list-slogan{
				padding-bottom: 10px;
				font-size: 18px
			}
			@media(max-width: 767px){
				.guest-list-item>div{
					width: 50%
				}
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
			["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=>$title, "attr"=>''],
			["type"=>"text", "name"=>"slogan", "title"=>"Khẩu hiệu", "note"=>"", "value"=>$slogan, "attr"=>''],
			["html"=>
			Form::itemManager([
				"data"=>$data,
				"name"=>"{$prefixName}[data][data]",
				"sortable"=>false,
				"max"=>100,
				"form"=>[
					["type"=>"image", "name"=>"logo", "title"=>"Logo", "value"=>"", "post"=>0],
				]
			])
		],
			["html"=>'<div class="alert-danger">Tắt chế độ sửa trang để hiển thị chính xác</div>']
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