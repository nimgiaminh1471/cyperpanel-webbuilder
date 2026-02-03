<?php
/*
# Giới thiệu dịch vụ
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget, Storage;
class PriceList{

	//Thông số mặc định
	private static $option=[
		"item_column_lg"    => 4,
		"item_column_md"    => 2,
		"item_column_sm"    => 1,
		"description"       => null,
		"packages"          => [],
		"note_BG_color"     => "#FFFFFF",
		"description_image" => null,
		"register_title"    => "",
		"bottom_height"     => 200
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Bảng giá",
			"icon"  => "fa-list",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$package = '';
		$trialLink = permission("member") ? '/admin/WebsiteTemplate' : 'javascript: showRegisterForm()';
		$isWebPackagePrice = empty($packages) ? true : false;
		if( $isWebPackagePrice ){
			$packages = Storage::builder('package');
		}
		if( !is_array($packages) ){
			$packages = [];
		}
		foreach($packages as $item){
			if( $isWebPackagePrice ){
				$renewMonthly = Storage::setting('builder_package_price_renew_monthly');
				$price               = [
					$item["price"],
					$renewMonthly ? 'tháng' : 'năm'
				];
				$priceDisplay = $renewMonthly ? number_format( round( (toNumber($item["price"]) / 12), -4) ) : $item["price"];
				$item["title"]       = $item['name'];
				$item['best_price']  = $item['suggest'];
				if( !in_array(2, $item['roles']) ){
					continue;
				}
			}else{
				$price = explode("/", $item["price"] ?? null);
				$priceDisplay = $item["price"];
			}
			$package .= '
			<div class="price-list-package-item">
				<div>
					<div class="price-list-package-item-header" style="background-image: linear-gradient(-45deg, '.($item["background_color1"] ?? "white").', '.($item["background_color2"] ?? "white").'); color: '.($item["color"] ?? "#ffffff").'">
						'.(empty($item["best_price"]) ? '' : '<img src="/files/uploads/2019/08/best-price.png">').'
						<div class="price-list-package-item-title">
							<div>
								<span style="color: '.($item["ribbon_bg_color"] ?? "#fff").'">
									'.$item["title"].'
								</span>
							</div>
						</div>
						'.( empty($item["label"]) ? '' : '
							<div class="price-list-package-item-label">
								<span style="color: '.($item["ribbon_bg_color"] ?? "#fff").'">
									'.$item["label"].'
								</span>
							</div>
						').'
						<div class="price-list-package-item-price" style="color: '.($item["ribbon_bg_color"] ?? "#fff").'">
							<span>'.($priceDisplay ?? "").'</span>
							'.(empty($price[1]) ? '' : '
								/ <span>'.($price[1] ?? "năm").'</span>
							').'
						</div>
					</div>
					<div class="price-list-package-item-info">
						<ul class="price-list-package-item-include">
							'.call_user_func(function($include){
								$out = '';
								if( empty($include) ){
									return;
								}
								foreach( explode(PHP_EOL, $include) as $text){
									$out .= '
										<li class="flex">
											<div style="width: 25px">
												<i class="fa fa-icon fa-check"></i>
											</div>
											<div style="width: calc(100% - 25px)">
												'.$text.'
											</div>
										</li>
									';
								}
								return $out;
							}, ($item["include"] ?? null) ).'
						</ul>
						<ul class="price-list-package-item-exclude">
							'.call_user_func(function($exclude){
								$out = '';
								if( empty($exclude) ){
									return;
								}
								foreach( explode(PHP_EOL, $exclude) as $text){
									$out .= '
										<li class="flex">
											<div style="width: 25px">
												<i class="fa fa-icon fa-times"></i>
											</div>
											<div style="width: calc(100% - 25px)">
												'.$text.'
											</div>
										</li>
									';
								}
								return $out;
							}, ($item["exclude"] ?? null) ).'
						</ul>
						'.(empty($item['renew_price']) ? '' : '
							<ul class="price-list-package-renew" style="border-top: 1px dashed '.($item["background_color1"] ?? "white").'">
								'.call_user_func(function($renewPrice){
									$out = '';
									foreach( explode(PHP_EOL, $renewPrice) as $item){
										if( empty( trim($item) ) ){
											continue;
										}
										$out .= '
											<li class="flex text-inline" style="border-bottom: none">
												<div style="width: 20px">
													<i class="fa fa-icon fa-calendar-check-o"></i>
												</div>
												<div style="width: calc(100% - 20px)">
													'.str_replace(['<span>'], ['<span class="label label-warning">'], $item).'
												</div>
											</li>
										';
									}
									return $out;
								}, $item['renew_price']).'
							</ul>
						').'

						'.($isWebPackagePrice ? '
							<ul class="price-list-package-renew" style="border-top: 1px dashed '.($item["background_color1"] ?? "white").'">
								'.call_user_func(function($price){
									$out = '';
									if( empty(WEB_BUILDER["expiry_date"]) ){
										return;
									}
									foreach( WEB_BUILDER["expiry_date"] as $item){
										$newPrice     = toNumber($price[0]) / 365 * toNumber($item['days']) ;
										$priceSaleOff = round(toNumber($newPrice) / 100) * (100 - (int)$item['sale_offer']);
										$out .= '
											<li class="flex text-inline" style="border-bottom: none">
												<div style="width: 20px">
													<i class="fa fa-icon fa-calendar-check-o"></i>
												</div>
												<div style="width: calc(100% - 20px)">
													'.$item['name'].':
													'.($newPrice == $priceSaleOff ? '' : '<s style="color: gray; font-size: 12px">'.number_format($newPrice).'</s>').'
													<span>
														<b>
															'.number_format( $priceSaleOff ).'
														</b>
													</span>
													'.($item['sale_offer'] > 0 ? '
														<span class="label label-warning" style="font-size: 12px">-'.$item['sale_offer'].'%</span>
													' : '
														
													').'
												</div>
											</li>
										';
									}
									return $out;
								}, $price).'
							</ul>
						' : '').'
					</div>
					'.( empty($register_title) ? '' : '
						<div class="price-list-package-item-button center pd-5">
							<a href="'.( empty($item["register_link"]) ? $trialLink : $item["register_link"] ).'" class="btn-primary" style="background: '.($item["button_BG_color"] ?? "#FFFFFF").' !important; border-color: '.($item["button_border_color"] ?? "#FFFFFF").' !important; color: '.($item["button_color"] ?? "#313131").' !important">
								'.$register_title.'
							</a>
						</div>
					').'
				</div>
			</div>
			';
		}
		if( !empty($description) ){
			$desc = "";
			foreach( explode(PHP_EOL, $description) as $txt ){
				$desc .= '
				<p>
					<i class="fa fa-circle fa-icon" style="font-size: 12px"></i>
					'.$txt.'
				</p>
				';
			}
			$description = '
			<div class="price-list-description">
				<div class="flex flex-medium flex-middle">
					<div class="width-20 center">
						<h2>
							<img src="'.$description_image.'" style="max-width: 80%">
						</h2>
					</div>
					<div class="width-80">
						'.$desc.'
					</div>
				</div>
			</div>
			';
		}
$out = <<<HTML
	<section class="price-list">
		<div class="flex">
			{$package}
		</div>
		<div>
			{$description}
		</div>
	</section>
HTML;
		if(PAGE_EDITOR){
			$style='
			.price-list>.flex{
				justify-content: center;
				align-items: stretch;
				margin-bottom: 20px
			}
			.price-list-package-item{
				width: '.(100 / $item_column_lg).'%;
				padding: 10px
			}
			.price-list-package-item-header{
				border-radius: 15px 15px 0 0;
				padding: 15px 10px;
				position: relative;
			}
			.price-list-package-item-info{
				padding: 25px 15px '.$bottom_height.'px 15px;
				word-spacing: 1px;
				font-size: 14px
			}
			.price-list-package-item>div{
				box-shadow: 0 0 20px -2px rgba(0,0,0,0.25);
				border-radius: 15px;
				height: 100%;
				position: relative;
				transition: .5s all;
				background-color: white
			}
			.price-list-package-item>div:hover{
				transform: scale(1.03);
			}
			.price-list-package-item-header>img{
				position: absolute;
				top: 0;
				left: 50%;
				transform: translate(-50%, -50%)
			}
			.price-list-package-item-title{
				text-align: center;
				font-weight: bold;
				padding: 10px 0;
				position: relative;
				text-transform: uppercase;
				font-size: 25px
			}
			.price-list-package-item-label{
				opacity: .7;
				padding: 5px 0;
				text-align: center
			}
			.price-list-package-item-price{
				text-align: center;
				position: relative;
				padding: 10px 0 15px 0;
				margin-bottom: 15px;
			}
			.price-list-package-item-price:after{
				position: absolute;
				width: 80px;
				height: 2px;
				content: "";
				background-color: white;
				bottom: 0;
				left: 50%;
				-webkit-transform: translateX(-50%);
				transform: translateX(-50%);
			}
			.price-list-package-item-price>span:first-child{
				font-size: 25px;
			}
			.price-list-package-item-info>ul{
				list-style-type: none;
				padding: 0;
				margin: 0
			}
			.price-list-package-item-info>ul li{
				padding: 8px 2px;
				border-bottom: 1px solid #EAEAEA;
			}
			.price-list-package-item-exclude>li{
				text-decoration: line-through;
				opacity: .6
			}
			.price-list-package-item-info>ul li i{
				opacity: .7
			}
			.price-list-package-item-button{
				position: absolute;
				bottom: 20px;
				left: 50%;
				transform: translate(-50%, 0);
			}
			.price-list-package-item-button>a{
				border-radius: 35px;
				padding: 10px 25px
			}
			.price-list-package-item-button>a:hover{
				opacity: .8
			}
			.price-list-package-renew{
				position: absolute;
				bottom: 20px;
				left: 50%;
				transform: translate(-50%, 0);
				width: 100%;
				padding: 10px !important;
				'.( empty($register_title) ? '' : 'padding-bottom: 60px !important;').'
				margin-top: 20px;
			}
			.price-list-package-renew s{
				color: gray;
				font-size: 12px
			}
			.price-list-package-renew .label{
				font-size: 12px
			}
			.price-list-description{
				line-height: 1.6;
				background: '.$note_BG_color.';
				border: 1px solid #ededed;
				border-radius: 7px;
				box-shadow: 0 20px 43px 0 rgba(20,42,87,.06);
				padding: 25px;
				margin: 10px
			}
			.price-list-description>h2{
				text-align: center;
			}
			.price-list-description p{
				margin: 4px 0
			}
			.ribbon{
				position: absolute;
				left: 0px;
				top: 0;
			}
			/* common */
			.ribbon>div{
			  width: 150px;
			  height: 150px;
			  overflow: hidden;
			  position: absolute;
			}
			.ribbon>div span {
			  position: absolute;
			  display: block;
			  width: 244px;
			  padding: 10px 0;
			  box-shadow: 0 5px 10px rgba(0,0,0,.1);
			  color: #fff;
			  text-shadow: 0 1px 1px rgba(0,0,0,.2);
			  text-transform: uppercase;
			  text-align: center;
			}

			/* top left*/
			.ribbon-top-left div {
			  top: 0;
			  left: 0;
			}
			.ribbon-top-left div::before,
			.ribbon-top-left div::after {
			  border-top-color: transparent;
			  border-left-color: transparent;
			}
			.ribbon-top-left div::before {
			  top: 0;
			  right: 0;
			}
			.ribbon-top-left div::after {
			  bottom: 0;
			  left: 0;
			}
			.ribbon-top-left div span {
			  right: -25px;
			  top: 30px;
			  transform: rotate(-45deg);
			}

			/* top right*/
			.ribbon-top-right {
			  top: -10px;
			  right: -10px;
			}
			.ribbon-top-right::before,
			.ribbon-top-right::after {
			  border-top-color: transparent;
			  border-right-color: transparent;
			}
			.ribbon-top-right::before {
			  top: 0;
			  left: 0;
			}
			.ribbon-top-right::after {
			  bottom: 0;
			  right: 0;
			}
			.ribbon-top-right span {
			  left: -25px;
			  top: 30px;
			  transform: rotate(45deg);
			}

			/* bottom left*/
			.ribbon-bottom-left {
			  bottom: -10px;
			  left: -10px;
			}
			.ribbon-bottom-left::before,
			.ribbon-bottom-left::after {
			  border-bottom-color: transparent;
			  border-left-color: transparent;
			}
			.ribbon-bottom-left::before {
			  bottom: 0;
			  right: 0;
			}
			.ribbon-bottom-left::after {
			  top: 0;
			  left: 0;
			}
			.ribbon-bottom-left span {
			  right: -25px;
			  bottom: 30px;
			  transform: rotate(225deg);
			}

			/* bottom right*/
			.ribbon-bottom-right {
			  bottom: -10px;
			  right: -10px;
			}
			.ribbon-bottom-right::before,
			.ribbon-bottom-right::after {
			  border-bottom-color: transparent;
			  border-right-color: transparent;
			}
			.ribbon-bottom-right::before {
			  bottom: 0;
			  left: 0;
			}
			.ribbon-bottom-right::after {
			  top: 0;
			  right: 0;
			}
			.ribbon-bottom-right span {
			  left: -25px;
			  bottom: 30px;
			  transform: rotate(-225deg);
			}
			@media(max-width: 767px){
				.price-list-package-item{
					width: '.(100 / $item_column_sm).'%
				}
			}

			@media(min-width: 768px) and (max-width: 1023px){
				.price-list-package-item{
					width: '.(100 / $item_column_md).'%
				}
			}

			@media(min-width: 1024px){
				
			}
		';
			$out.=Widget::css($style);
		}
		if( strtok($_SERVER["REQUEST_URI"],'?') == '/thiet-ke-website'){
			ob_start();
			echo '<section style="margin-top: 20px">';
			echo view(
				"WebsiteTemplate",
				[
					"websiteTemplateSearch" => false,
					"templateTitle"         => 'Kho giao diện',
					"templateDescription"   => '',
					"disablePromotion" => true
				],
				"website");
			echo '</section>';
			$out .= ob_get_contents();
			ob_end_clean();
		}
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"number", "name"=>"item_column_lg", "title"=>"Số gói/hàng (Máy tính)", "note"=>"", "min"=>0, "max"=>9999, "value"=>$item_column_lg,"attr"=>''],
			["type"=>"number", "name"=>"item_column_md", "title"=>"Số gói/hàng (Tablet)", "note"=>"", "min"=>0, "max"=>9999, "value"=>$item_column_md,"attr"=>''],
			["type"=>"number", "name"=>"item_column_sm", "title"=>"Số gói/hàng (Điện thoại)", "note"=>"Ghi chú", "min"=>0, "max"=>9999, "value"=>$item_column_sm,"attr"=>''],
			["type"=>"color", "name"=>"note_BG_color", "title"=>"Màu nền ghi chú", "default"=>"#FFFFFF", "value"=>$note_BG_color, "required"=>true],
			["html"=>
				Form::itemManager([
					"data"=>$packages,
					"name"=>"{$prefixName}[data][packages]",
					"sortable"=>true,
					"max"=>200,
					"form"=>[
						["type"=>"switch", "name"=>"best_price", "title"=>"Hiện huy hiệu", "value" => null],
						["type"=>"text", "name"=>"title", "title"=>"Tên gói", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"label", "title"=>"Nhãn", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"price", "title"=>"Giá", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"textarea", "name"=>"include", "title"=>"Bao gồm", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
						["type"=>"textarea", "name"=>"exclude", "title"=>"Không bao gồm", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
						["type"=>"textarea", "name"=>"renew_price", "title"=>"Giá gia hạn các năm", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
						["type"=>"color", "name"=>"color", "title"=>"Màu chữ", "default"=>"#000000", "value"=>null, "required"=>true],
						["type"=>"color", "name"=>"background_color1", "title"=>"Màu Nền 1", "default"=>"#FFFFFF", "value"=>null, "required"=>true],
						["type"=>"color", "name"=>"background_color2", "title"=>"Màu Nền 2", "default"=>"#FFFFFF", "value"=>null, "required"=>true],
						["type"=>"color", "name"=>"ribbon_bg_color", "title"=>"Màu nền tên gói", "default"=>"#3498db", "value"=>null, "required"=>true],
						["type"=>"color", "name"=>"button_BG_color", "title"=>"Màu Nền nút", "default"=>"#FFFFFF", "value"=>null, "required"=>true],
						["type"=>"color", "name"=>"button_color", "title"=>"Màu chữ nút", "default"=>"#313131", "value"=>null, "required"=>true],
						["type"=>"color", "name"=>"button_border_color", "title"=>"Màu viền nút", "default"=>"#FFFFFF", "value"=>null, "required"=>true],
					]
				])
			],
			["type"=>"text", "name"=>"register_title", "title"=>"Tiêu đề nút", "note"=>"", "value"=>$register_title, "attr"=>''],
			["type"=>"image", "name"=>"description_image", "title"=>"Ảnh ghi chú", "value"=>$description_image, "post"=>0],
			["type"=>"textarea", "name"=>"description", "title"=>"Giới thiệu", "note"=>"", "value"=>$description, "attr"=>'', "full"=>true],
			["type"=>"number", "name"=>"bottom_height", "title"=>"Chiều cao phần bên dưới gói", "note"=>"", "min"=>0, "max"=>9999, "value"=>$bottom_height,"attr"=>''],
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