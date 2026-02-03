<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget;
class ServiceIntroClassic{

	//Thông số mặc định
	private static $option=[
		"color"                       => "#FFFFFF",
		"font_size_large"             => 36,
		"font_size_small"             => 26,
		"description_font_size_large" => 18,
		"description_font_size_small" => 16,
		"banner_height_large"         => '500px',
		"banner_height_small"         => '450px',
		"items"                       => []
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Phần giới thiệu đầu trang (banner TW)",
			"icon"  => "fa-calendar-check-o",
			"color" => ""
		];
		return $info[$key];
	}



	// Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out = Assets::show('/assets/cycle-slider/style.css');
		Assets::footer('/assets/cycle-slider/scripts.js');
		$out .= '
		<section class="slider-outer">
			<div class="cycle-slider">
				<div class="slider-slides cycle-slideshow cycle-paused" data-cycle-pause-on-hover="true" data-cycle-slides=".slide" data-cycle-prev=".slider-prev" data-cycle-next=".slider-next" data-cycle-pager=".slider-pages" data-cycle-timeout="30000" data-cycle-speed="500" data-cycle-fx="fade" style="position: relative;">
		';
		$i = 0;
		foreach($items as $item){
			$i++;
			$out .= '
					<style>
						.header-slider-item-'.$i.'{
							background-image: url('.$item['background_image'].')
						}
						@media(max-width: 767px){
							.header-slider-item-'.$i.'{
								background-image: url('.($item['background_image_small'] ?? '').')
							}
						}
					</style>
					<div class="slide slide-left light cycle-slide header-slider-item-'.$i.' '.($item['center'] ? 'slide-center' : '').'">
						<div class="slide-body">
							<div class="main-layout">
								<div class="slide-caption">
								
									<h1 class="slide-title">
										'.$item['title'].'
									</h1>

									<div class="slide-content">
										<p>
											'.$item['description'].'
										</p>
									</div>

									<div class="header-slider-btn-group '.($item['center'] ? '' : 'flex flex-middle').'">
										'.( empty($item['button_1_link']) ? '' : '
											<a href="'.$item['button_1_link'].'" class="fx-btn-blick btn-primary '.( empty($item['button_2_link']) ? 'btn-outline' : '').'">
												'.$item['button_1_title'].'
											</a>
										').'
										'.( empty($item['button_2_link']) ? '' : '
											<a class="btn-info btn-outline" href="'.$item['button_2_link'].'">
												'.$item['button_2_title'].'
											</a>
										').'
									</div>
								</div>
								'.( empty($item['slide_image']) ? '' : '
									<div class="slide-image">
										<img src="'.$item['slide_image'].'">
									</div>
								').'
								
							</div>
						</div>
					</div>
			';
		}
					
		if( count($items) > 1 ){
			$out .= '
				<span class="slider-prev" data-cycle-cmd="pause"></span>
				<span class="slider-next" data-cycle-cmd="pause"></span>
			';
		}
		$out .= '
			   </div>
		   </div>
	   </section>
		';
		if(PAGE_EDITOR){
			$style='
				.slider-slides {
					height: '.$banner_height_large.';
				}
				.slide-body{
					color: '.$color.' !important
				}
				.header-slider-btn-group .btn-outline{
					color: '.$color.' !important;
					border: 1px solid '.$color.';
				}
				.slide-title{
					font-size: '.$font_size_large.'px;
					text-transform: uppercase;
				}
				.slide-content{
					font-size: '.$description_font_size_large.'px;
				}
				.header-slider-btn-group a{
					font-size: 14px
				}
				.slider-slides .slide-title:after {
					background: '.$color.';
				}
				@media(max-width: 767px){
					.slide-title{
						font-size: '.$font_size_small.'px
					}
					.slide-content{
						font-size: '.$description_font_size_small.'px;
					}
					.slider-slides {
						height: '.$banner_height_small.';
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
		$form = [
			["html"=>
				Form::itemManager([
					"data"     => $items,
					"name"     => "{$prefixName}[data][items]",
					"sortable" => false,
					"max"      => 20,
					"form"     => [
						["type"=>"switch", "name"=>"center", "title"=>"Căn giữa", "value"=>1],
						["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"textarea", "name"=>"description", "title"=>"Mô tả", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
						["type"=>"image", "name"=>"background_image", "title"=>"Ảnh nền (máy tính)", "value"=>"", "post"=>0],
						["type"=>"image", "name"=>"background_image_small", "title"=>"Ảnh nền (điện thoại)", "value"=>"", "post"=>0],
						["type"=>"text", "name"=>"button_1_title", "title"=>"Tiêu đề (nút 1)", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"button_1_link", "title"=>"Link (nút 1)", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"button_2_title", "title"=>"Tiêu đề (nút 2)", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"button_2_link", "title"=>"Link (nút 2)", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"image", "name"=>"slide_image", "title"=>"Ảnh slide", "value"=>"", "post"=>0],
					]
				])
			],
			["type"=>"color", "name"=>"color", "title"=>"Màu chữ", "default"=>"#FFFFFF", "value"=>$color, "required"=>true],
			["type"=>"text", "name"=>"banner_height_large", "title"=>"Chiều cao (máy tính)", "note"=>"", "value" => $banner_height_large,"attr"=>''],
			["type"=>"text", "name"=>"banner_height_small", "title"=>"Chiều cao (điện thoại)", "note"=>"", "value" => $banner_height_small,"attr"=>''],
			["type"=>"number", "name"=>"font_size_large", "title"=>"Cỡ chữ tiêu đề (máy tính)", "note"=>"", "min"=>10, "max"=>9999, "value" => $font_size_large,"attr"=>''],
			["type"=>"number", "name"=>"font_size_small", "title"=>"Cỡ chữ tiêu đề (điện thoại)", "note"=>"", "min"=>10, "max"=>9999, "value" => $font_size_small,"attr"=>''],
			["type"=>"number", "name"=>"description_font_size_large", "title"=>"Cỡ chữ mô tả (máy tính)", "note"=>"", "min"=>10, "max"=>9999, "value" => $description_font_size_large,"attr"=>''],
			["type"=>"number", "name"=>"description_font_size_small", "title"=>"Cỡ chữ mô tả (điện thoại)", "note"=>"", "min"=>10, "max"=>9999, "value" => $description_font_size_small,"attr"=>''],
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