<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget;
class ServiceIntro{

	//Thông số mặc định
	private static $option=[
		"background"        => "",
		"background_color1" => "#dbf6c8",
		"background_color2" => "#1cafc6",
		"background_color3" => "#012690",
		"icon"              => [],
		"features"          => [],
		"features_title"    => null,
		"features_caption"  => null,
		"videoLink"         => ""
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Phần giới thiệu đầu trang(sơ đồ)",
			"icon"  => "fa-google-wallet",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$serviceIntro=[
			"top"=>[
				["title"=>"TỰ ĐỘNG", "text"=>"Chỉ vài click chuột là sở hữu ngay website", "icon"=>"fa-cogs", "animate"=>"fade-right", "duration"=>1500],
				["title"=>"DỄ DÀNG", "text"=>"Dễ dàng quản lý, giao diện web kéo - thả", "icon"=>"fa-hand-peace-o", "animate"=>"fade-down", "duration"=>1000],
				["title"=>"TÊN MIỀN RIÊNG", "text"=>"Hỗ trợ dùng tên miền riêng (.com,.vn...)", "icon"=>"fa-globe", "animate"=>"fade-right", "duration"=>1500],
			],
			"bottom"=>[
				["title"=>"TIẾT KIỆM", "text"=>"Phí gia hạn website cực rẻ chỉ từ 500k/năm", "icon"=>"fa-dollar", "animate"=>"fade-left", "duration"=>1500],
				["title"=>"SAO LƯU DỮ LIỆU", "text"=>"Được tải về toàn bộ mã nguồn dữ liệu website khi cần", "icon"=>"fa-cloud-download", "animate"=>"fade-up", "duration"=>1000],
				["title"=>"350+ MẪU WEB", "text"=>"Kho giao diện đẹp, chuẩn, đủ mọi ngành nghề", "icon"=>"fa-wordpress", "animate"=>"fade-left", "duration"=>1500],
			],
		];
		Assets::footer(
			"https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css",
			"https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"
		);
		$out='
		<script>
		$(document).ready(function() {
			$(".fancybox-media").fancybox({
				openEffect  : "none",
				closeEffect : "none",
				helpers : {
					media : {}
				}
			});
		});
		</script>
		<section class="service">
			<div class="service-bg">
				<div class="service-wrap main-layout">
				<h1 class="heading" data-aos="zoom-in" data-aos-duration="1500"  data-aos="top-bottom">NỀN TẢNG THIẾT KẾ WEBSITE TỰ ĐỘNG</h1>
				<h2 class="hidden-small" data-aos="zoom-in" data-aos-duration="1500"  data-aos="top-bottom">
					Giúp bạn khởi tạo website dễ dàng với đầy đủ tính năng, không cần code, giao diện đẹp hoàn thiện và thiết lập website chỉ trong 30 phút!
				</h2>
		';
		foreach($serviceIntro as $pos=>$items){
			$out.='<div class="service-'.$pos.' flex">';
				foreach($items as $item){
					$out.='
					<div class="service-item" data-aos="'.$item["animate"].'" data-aos-duration="'.$item["duration"].'" data-aos="top-bottom"  data-aos-anchor=".service">
						<div class="service-item-body">
					';
					if($pos=="top"){
						$out.='
						<div class="service-item-text">
							<div class="service-item-desc">
								'.$item["text"].'
							</div>
							<div class="service-item-title text-inline">
								'.$item["title"].'
							</div>
						</div>
						<div class="service-item-icon">
							<i class="fa '.$item["icon"].'"></i>
						</div>
						<div class="service-item-icon-vertical"></div>
						';
					}else{
						$out.='
						<div class="service-item-icon-vertical"></div>
						<div class="service-item-icon">
							<i class="fa '.$item["icon"].'"></i>
						</div>
						<div class="service-item-text">
							<div class="service-item-title text-inline">
								'.$item["title"].'
							</div>
							<div class="service-item-desc">
								'.$item["text"].'
							</div>
						</div>
						';
					}
					$out.='</div>
				</div>';
				}
				if($pos=="top"){
					$out.='<div class="service-line"></div>';
				}
			$out.='</div>';
		}
		$out.='
		<div class="center pd-20" data-aos="fade-in" data-aos-duration="3000" data-aos="top-bottom"  data-aos-anchor=".service">
			<div class="service-button">
				'.(permission("member") ? '
					<a href="/admin/WebsiteTemplate" class="btn-gradient"><i class="fa fa-wrench"></i> TRANG QUẢN LÝ WEBSITE</a>
				' : '
					<button type="button" class="btn-gradient modal-click fx-btn-blick" data-modal="register-box">
						<i class="fa fa-paper-plane-o"></i>
						DÙNG THỬ MIỄN PHÍ
					</button>
				').'
				
				<a class="fancybox-media btn-danger" href="'.$videoLink.'"><i class="fa fa-play"></i> XEM VIDEO HƯỚNG DẪN</a>
				
			</div>
		</div>
		';
		$out.='
			</div>
			</div>
		';
		if(!PAGE_EDITOR){
			$out.='<ul class="circles">';
			foreach($icon as $item){
				$out.='<li><img src="'.($item["src"]??'').'" alt="." /></li>';
			}
			$out.='</ul>';
		}
		$out.='
		</section>
		';

		$out .= '
		<section class="features">
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
		<div class="flex flex-medium flex-center">
		';
		foreach($features as $item){
			$out .= '
			<div class="features-item" style="width: '.(100/count($features)).'%">
				<a href="'.(empty($item["link"]) ? 'javascript: void(0)' : $item["link"]).'">
					<span class="features-item-icon" style="background-image: linear-gradient(40deg,'.($item["background_color1"] ?? 'blue').' 0,'.($item["background_color2"] ?? 'red').' 100%);">
						<img src="'.$item["icon"].'" alt=".">
					</span>
					<span class="features-item-title">
						'.$item["title"].'
					</span>
					<span class="features-item-description">
						'.$item["description"].'
					</span>
					<span class="features-item-image pd-5">
						<img src="'.$item["image"].'" alt=".">
					</span>
				</a>
			</div>
			';
		}
		$out .= '</diV></div>';
		$out .= '</section>';
		if(PAGE_EDITOR){
			echo '
			<style>
			#header{
				position: relative !important;
			}</style>
			';
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
			.features .features-item{
				padding: 20px;
			}
			.features .features-item>a{
				background: white;
				display: block;
				padding: 30px 20px 150px 20px;
				box-shadow: 0 1px 30px 0 rgba(55,125,162,.2);
				position: relative;
				height: 100%
			}
			.features .features-item>a:hover{
				color: inherit !important;
			}
			.features .features-item-icon{
				position: absolute;
				top: 5px;
				right: 10px;
				width: 50px;
				height: 50px;
				border-radius: 50%;
				padding: 12px
			}
			.features .features-item-image{
				text-align: right;
				display: block;
				position: absolute;
				bottom: 0px;
				right: 0
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
			.fancybox-overlay,
			#fancybox-thumbs{
				z-index: 199709 !important
			}
			.service{
				background-color: '.$background_color1.';
				background-image: radial-gradient(ellipse farthest-side at 100% 100%, '.$background_color1.' 20%, '.$background_color2.' 50%, '.$background_color3.' 110%); 
				overflow: hidden
			}
			.service-bg{
				background-image: url('.$background.');
				background-position: top;
				background-size: cover;
				background-repeat: no-repeat;
				background-position: 0 0;
				height: 100vh;
			}
			.service-button>a{
				margin-left: 10px;
			}
			.service-button>*{
				border-radius: 35px;
				padding: 15px 50px;
				font-size: 18px
			}
			@keyframes slide {
				from { background-position: 0 0; }
				to { background-position: -400px 0; }
				0%   {background-position: 0 0;}
				25%  {background-position: -100px 0;}
				50%  {background-position: -200px 0;}
				75%  {background-position: -100px 0;}
				100% {background-position: 0 0;}
			}
			.header-fixed{
				height: 0 !important
			}
			#header, .navbar li>ul{
				visibility: hidden;
				animation: fadein .5s
			}
			.service-top{
				align-items: flex-end;
			}
			.service-bottom{
				align-items: flex-start;
			}
			.service-wrap{
				position: absolute;
				color: #FAFAFA;
				top: 50%;
				left: 50%;
				transform: translate(-50%,-50%);
				width: 100%;
				overflow: hidden;
				z-index: 97
			}
			.service .heading{
				text-align: center;
				font-size: 27px;
				padding: 50px 20px 20px 20px;
				font-weight: bold;
			}
			.service h2{
				text-align: center;
				font-size: 16px;
				padding: 0 10px 40px 10px
			}
			.service-line{
				height: 3px;
				max-width: 100%;
				background:  #FAFAFA;
				animation: width-animate 1.5s forwards;
				margin: auto
			}
			@keyframes width-animate{
				from {
					width: 10%;
				}
				to {
					width: 100%;
				}
			}
			.service-item{
				width: 33.333%;
				cursor: pointer;
			}
			.service-item-body{
				position: relative;
			}
			.service-top .service-item-body{
				margin-right: 80px
			}
			.service-bottom .service-item-body{
				margin-left: 0px
			}
			.service-item-body:hover{
				opacity: .7
			}
			.service-item-text{
				text-align: center;
				padding: 10px
			}
			.service-item-title{
				font-size: 18px;
				font-weight: bold;
				margin-bottom: 5px 
			}
			.service-top .service-item-title{
				margin-top: 5px 
			}
			.service-bottom .service-item-title{
				margin-bottom: 5px 
			}
			.service-item-desc{
				font-size: 15px;
			}
			.service-item-icon{
				height: 95px;
				width: 95px;
				position: relative;
				border: 3px solid  #FAFAFA;
				margin: auto;
				border-radius: 50%
			}
			.service-item-icon-vertical{
				width: 3px;
				height: 20px;
				margin: auto;
				background:  #FAFAFA
			}
			.service-item-icon>i{
				top: 50%;
				left: 50%;
				position: absolute;
				font-size: 40px;
				transform: translate(-50%, -50%);
			}
			@media(max-width: 767px){
				.service-wrap{
					padding-top: 30px
				}
				.service-item-icon{
					height: 55px;
					width: 55px;
				}
				.service-item-body{
					margin: 0px !important
				}
				.service-item-title{
					font-size: 14px
				}
				.service-item-desc{
					font-size: 13px
				}
				.service .heading{
					text-align: center;
					font-size: 18px;
					padding: 10px
				}
				.service-button>a{
					margin-left: 0;
					margin-top: 5px
				}
				.service-button>*{
					display: block;
					width: 100%;
					padding: 12px 20px;
					font-size: 15px
				}
			}
			@media(max-height: 600px){
				.service-item-desc{
					display: none
				}
			}

			@media(min-width: 768px) and (max-width: 1023px){
				.service-item-icon{
					height: 65px;
					width: 65px;
				}
				.service-item-title{
					font-size: 16px
				}
				.features .flex>div{
					width: 50% !important
				}
			}

			@media(min-width: 1024px){

			}

.circles{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.circles li{
    position: absolute;
    display: block;
    list-style: none;
    width: 20px;
    height: 20px;
    animation: animate 25s linear infinite;
    bottom: -150px;
    opacity: .7
    
}

.circles li:nth-child(1){
    left: 25%;
    width: 80px;
    height: 80px;
    animation-delay: 5s;
}


.circles li:nth-child(2){
    left: 10%;
    width: 20px;
    height: 20px;
    animation-delay: 2s;
    animation-duration: 12s;
}

.circles li:nth-child(3){
    left: 70%;
    width: 20px;
    height: 20px;
    animation-delay: 4s;
}

.circles li:nth-child(4){
    left: 40%;
    width: 60px;
    height: 60px;
    animation-delay: 0s;
    animation-duration: 18s;
}

.circles li:nth-child(5){
    left: 65%;
    width: 20px;
    height: 20px;
    animation-delay: 0s;
}

.circles li:nth-child(6){
	animation: animate2 25s linear infinite;
    left: 75%;
    width: 90px;
    height: 90px;
    animation-delay: 3s;
}

.circles li:nth-child(7){
    left: 35%;
    width: 100px;
    height: 100px;
    animation-delay: 7s;
}

.circles li:nth-child(8){
    left: 50%;
    width: 25px;
    height: 25px;
    animation-delay: 15s;
    animation-duration: 45s;
}

.circles li:nth-child(9){
    left: 20%;
    width: 35px;
    height: 35px;
    animation-delay: 2s;
    animation-duration: 35s;
}

.circles li:nth-child(10){
    left: 85%;
    width: 90px;
    height: 90px;
    animation-delay: 1s;
    animation-duration: 38s;
}



@keyframes animate {

	0%{
		transform: translateY(0) rotate(0deg);
		opacity: 1;
		border-radius: 0;
	}

	100%{
		transform: translateY(-1100px) rotate(720deg);
		opacity: 0;
		border-radius: 50%;
	}

}
@keyframes animate2 {

	0%{
		transform: translateY(0);
		opacity: 1;
		border-radius: 0;
	}

	100%{
		transform: translateY(-1100px);
		opacity: 0;
		border-radius: 50%;
	}

}
			';
			Widget::css($style);
		}
		$out.=Assets::show("/assets/intro/service-diagram.js");
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"image", "name"=>"background", "title"=>"Ảnh nền", "value"=>$background, "post"=>0],
			["type"=>"color", "name"=>"background_color1", "title"=>"Màu nền 1", "default"=>"", "value"=>$background_color1, "required"=>true],
			["type"=>"color", "name"=>"background_color2", "title"=>"Màu nền 2", "default"=>"", "value"=>$background_color2, "required"=>true],
			["type"=>"color", "name"=>"background_color3", "title"=>"Màu nền 3", "default"=>"", "value"=>$background_color3, "required"=>true],
			["type"=>"text", "name"=>"videoLink", "title"=>"Link video giới thiệu", "note"=>"", "value"=>$videoLink, "attr"=>''],
			["html"=>'<div class="alert-info">Icon chạy lên</div>'],
			["html"=>
				Form::itemManager([
					"data"=>$icon,
					"name"=>"{$prefixName}[data][icon]",
					"sortable"=>true,
					"max"=>10,
					"form"=>[
						["type"=>"image", "name"=>"src", "title"=>"Icon", "value"=>"", "post"=>0],
					]
				])
			],
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
						["type"=>"color", "name"=>"background_color1", "title"=>"Màu nền 1", "default"=>"", "value"=>"", "required"=>true],
						["type"=>"color", "name"=>"background_color2", "title"=>"Màu nền 2", "default"=>"", "value"=>"", "required"=>true],
						["type"=>"image", "name"=>"image", "title"=>"Ảnh", "value"=>"", "post"=>0],
						["type"=>"text", "name"=>"link", "title"=>"Liên kết", "note"=>"", "value"=>"", "attr"=>''],
					]
				])
			],
		["html"=>'<div class="alert-danger">Tắt chế độ sửa trang để hiển thị chính xác</div>'],
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