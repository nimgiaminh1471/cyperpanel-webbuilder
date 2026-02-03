<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget;
class ServiceIntroBanner{

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
			"name"  => "Phần giới thiệu đầu trang(banner)",
			"icon"  => "fa-calendar-check-o",
			"color" => ""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out = Assets::show('/assets/header-slider/style.css');
		Assets::footer("/assets/slider/script.js","/assets/slider/style__complete.css");
		$out .= '
		<div class="slider header-slider" style="width:100%;">
			<ul class="slider-basic" data-autoplay="20" data-disable-swipe="1" style="cursor: default">
		';
		$i = 0;
		$items = is_array($items) ? $items : [];
		foreach($items as $item){
			$i++;
			$out .= '
				<li>
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
					<div class="header-slider-item header-slider-item-'.$i.'">
						<div class="header-slider-item-body">
							<div class="block main-layout">
								<div class="header-slider-content">
									<div class="header-slider-heading">
										<div class="header-slider-title text-inline">
											<strong>
												'.$item['title'].'
											</strong>
										</div>
										<div class="header-slider-caption text-inline zoomin">
											'.$item['caption'].'
										</div>
									</div>
									<div class="header-slider-check-list">
										<ul class="fadeInDown">
											'.call_user_func(function($description){
												$out = '';
												foreach(explode(PHP_EOL, $description) as $row){
													$out .= '<li>'.$row.'</li>';
												}
												return $out;
											}, $item['description']).'
										</ul>
									</div>
									<div class="header-slider-btn-group flex flex-middle">
										'.( empty($item['button_1_link']) ? '' : '
											<a href="'.$item['button_1_link'].'" class="fx-btn-blick btn-gradient">
												'.$item['button_1_title'].'
											</a>
										').'
										'.( empty($item['button_2_link']) ? '' : '
											<a class="btn-info" href="'.$item['button_2_link'].'">
												'.$item['button_2_title'].'
											</a>
										').'
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>
			';
		}
					
		$out .= '</ul>
			<i class="slider-btn-prev"></i>
			<i class="slider-btn-next"></i>
		</div>
		';
		if(PAGE_EDITOR){
			$style='
				.header-slider-item-body{
					color: '.$color.' !important
				}
				.header-slider-btn-group .btn-info{
					color: '.$color.' !important;
					border: 1px solid '.$color.';
				}
				.header-slider-heading{
					font-size: '.$font_size_large.'px;
					text-transform: uppercase;
				}
				.header-slider-check-list,
				.header-slider-check-list li:before{
					font-size: '.$description_font_size_large.'px
				}
				.header-slider-btn-group a{
					font-size: 14px
				}
				.header-slider-item{
					height: '.$banner_height_large.';
				}
				@media(max-width: 767px){
					.header-slider-heading{
						font-size: '.$font_size_small.'px
					}
					.header-slider-check-list,
					.header-slider-check-list li:before{
						font-size: '.$description_font_size_small.'px
					}
					.header-slider-item{
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
						["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"caption", "title"=>"Mô tả", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"image", "name"=>"background_image", "title"=>"Ảnh nền (máy tính)", "value"=>"", "post"=>0],
						["type"=>"image", "name"=>"background_image_small", "title"=>"Ảnh nền (điện thoại)", "value"=>"", "post"=>0],
						["type"=>"textarea", "name"=>"description", "title"=>"Mô tả", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
						["type"=>"text", "name"=>"button_1_title", "title"=>"Tiêu đề (nút 1)", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"button_1_link", "title"=>"Link (nút 1)", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"button_2_title", "title"=>"Tiêu đề (nút 2)", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"button_2_link", "title"=>"Link (nút 2)", "note"=>"", "value"=>"", "attr"=>''],
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