<?php
/*
# Danh sách video
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget;
class VideoList{

	//Thông số mặc định
	private static $option=[
		"limit"        => 4,
		"itemColumnLg" => 3,
		"itemColumnMd" => 2,
		"itemColumnSm" => 1,
		"bannerHeight" => 250,
		"videosData"   => [],
		"type"         => "slider"
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Danh sách video",
			"icon"  => "fa-file-video-o",
			"color" => ""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		Assets::footer(
			"https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css",
			"https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js",
			"/assets/widgets/video-list/script.js"
		);
		if( !is_array($videosData) ){
			return;
		}
		$out = $videos = $seeMore = '';
		if( device()=="desktop" ){
				$itemColumn = $itemColumnLg;
			}else if( device()=="tablet" ){
				$itemColumn = $itemColumnMd;
			}else{
				$itemColumn = $itemColumnSm;
			}
			if($type == "slider"){
				$videos.='
				<div class="slider" style="width:100%;max-height: 550px">
					<ul class="slider-basic" data-autoplay="0">
				';
				foreach(array_chunk($videosData, $itemColumn ) as $items){
					$videos.='<li><div class="flex videos-list-item">';
					foreach($items as $item){
						$videos.='
						<div class="pd-10" style="width: '.(100 / $itemColumn).'%">
							<a data-fancybox="gallery" href="'.$item["link"].'">
								<img src="'.$item["img"].'">
								<span>'.$item["title"].'</span>
								<i class="fa fa-play-circle-o"></i>
							</a>
						</div>
						';
					}
					$videos.='</div></li>';
				}
							
				$videos.='</ul>
					<i class="slider-btn-prev"></i>
					<i class="slider-btn-next"></i>
				</div>
				';
			}else{
				$videos.='<div class="flex flex-medium videos-list-item">';
				$i = 0;
				foreach($videosData as $item){
					$i++;
					$videos.='
						<div class="pd-10'.($i > 4 ? ' hidden' : '').'" style="width: '.(100 / $itemColumn).'%">
							<a data-fancybox="gallery" href="'.$item["link"].'">
								<img src="'.$item["img"].'">
								<span>'.$item["title"].'</span>
								<i class="fa fa-play-circle-o"></i>
							</a>
						</div>
					';
				}
				$videos.='</div>';
				if( count($videosData) > 4 ){
					$videos.='
						<div class="center">
							<button type="button" class="btn-primary btn-circle" onclick="videoList__showAll(this)">
							<i class="fa fa-plus"></i>
								XEM THÊM
							</button>
						</div>
					';
				}
			}
			Assets::footer("/assets/slider/script.js","/assets/slider/style__complete.css");
		$title = $title ?? null;
		if( !empty($title) ){
			$title = <<<HTML
				<h2 style="padding: 20px 10px 10px 10px ">
					<i class="fa fa-video-camera"></i>
					{$title}
				</h2>
			HTML;
		}
		$out .= <<<HTML
			<section class="videos-list">
				{$title}
				<div>
					{$videos}
				</div>
			</section>
		HTML;
		if(PAGE_EDITOR){
			$style='
			.fancybox-container{
				z-index: 9999999999 !important
			}
			.videos-list-item>div>a>img{
				width: 100%;
				object-fit: cover;
				height: '.$bannerHeight.'px;
				opacity: .6;
				transition: .5s all
			}
			.videos-list-item>div>a{
				position: relative;
				display: block;
				color: white;
				font-size: 17px;
				text-align: center;
				background: black;
			}
			.videos-list-item>div>a:hover img{
				opacity: .4
			}
			.videos-list-item>div>a:hover >i{
				font-size: 70px
			}
			.videos-list-item>div>a>span{
				position: absolute;
				bottom: 30px;
				width: 100%;
				display: block;
			}
			.videos-list-item>div>a>i{
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				display: block;
				text-align: center;
				font-size: 60px;
				transition: .2s all
			}
			.videos-list .slider-btn-next{
				opacity: .9;
				right: 10px
			}
			.videos-list .slider-btn-prev{
				opacity: .9;
				left: 10px
			}
			.videos-list .slider-btn-next:after,
			.videos-list .slider-btn-prev:after{
				background: white;
				display: inline-block;
				padding: 20px;
				color: black;
				border-radius: 5px
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
			["type"=>"number", "name"=>"limit", "title"=>"Số lượng", "note"=>"", "min"=>1, "max"=>9999, "value"=> $limit, "attr"=>''],
			["type"=>"number", "name"=>"itemColumnLg", "title"=>"Số cột (máy tính)", "note"=>"", "min"=>1, "max"=>9999, "value"=> $itemColumnLg, "attr"=>''],
			["type"=>"number", "name"=>"itemColumnMd", "title"=>"Số cột (máy tính bảng)", "note"=>"", "min"=>1, "max"=>9999, "value"=> $itemColumnMd, "attr"=>''],
			["type"=>"number", "name"=>"itemColumnSm", "title"=>"Số cột (điện thoại)", "note"=>"", "min"=>1, "max"=>9999, "value"=> $itemColumnSm, "attr"=>''],
			["type"=>"number", "name"=>"bannerHeight", "title"=>"Chiều cao ảnh", "note"=>"", "min"=>50, "max"=>9999, "value"=> $bannerHeight, "attr"=>''],
			["type"=>"select", "name"=>"type", "title"=>"Kiểu hiển thị", "option"=>["slider"=>"Slider", "list"=>"Danh sách"], "value"=>$type],
			["html"=>
			Form::itemManager([
				"data"     => $videosData,
				"name"     => "{$prefixName}[data][videosData]",
				"sortable" => false,
				"max"      => 20,
				"form"     => [
					["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=>"", "attr"=>''],
					["type"=>"text", "name"=>"link", "title"=>"Link video", "note"=>"", "value"=>"", "attr"=>''],
					["type"=>"image", "name"=>"img", "title"=>"Ảnh nền", "value"=>"", "post"=>0],
				]
			])
		],
		];
		$out.=Form::create([
			"form"     => $form,
			"function" => "",
			"prefix"   => "",
			"name"     => "{$prefixName}[data]",
			"class"    => "menu",
			"hover"    => false
		]);
		return $out;
	}




}
//</Class>