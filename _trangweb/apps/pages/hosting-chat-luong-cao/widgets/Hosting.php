<?php
/*
# Giới thiệu dịch vụ
*/
namespace pages\hosting__chat__luong__cao\widgets;
use Form;
use Assets, Widget;
class Hosting{

	//Thông số mặc định
	private static $option=[
		"packages"=>[],
		"background1" => "#FF00FF",
		"background2" => "#FF00FF",
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Hosting",
			"icon"  => "fa-save",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out = '';
		$out .= '
		<div style="'.(PAGE_EDITOR ? '' : 'margin-top: -180px').'">
			<section class="hosting-packages main-layout">
				<div class="flex flex-medium">
					'.call_user_func(function($packages){
						$out = '';
						foreach($packages as $item){
							$out .= '
							<div class="hosting-packages-item width-50">
								<div class="hosting-packages-item-wrap flex flex-medium">
									<div class="hosting-packages-item-left width-60">
										<ul>
											<li class="primary-color">
												'.$item["name"].'
											</li>
											<li>
												<div class="flex">
													<div class="width-50">
														<i class="fa fa-hdd-o fa-icon"></i>
														Dung lượng
													</div>
													<div class="width-50 right">
														'.$item["disk"].'
													</div>
												</div>
											</li>
											<li>
												<div class="flex">
													<div class="width-50">
														<i class="fa fa-dashboard fa-icon"></i>
														Băng thông
													</div>
													<div class="width-50 right">
														'.$item["bandwidth"].'
													</div>
												</div>
											</li>
											<li>
												<div class="flex">
													<div class="width-50">
														<i class="fa fa-envelope-o fa-icon"></i>
														Email
													</div>
													<div class="width-50 right">
														'.$item["email"].'
													</div>
												</div>
											</li>
											<li>
												<div class="flex">
													<div class="width-50">
														<i class="fa fa-server fa-icon"></i>
														Tài khoản FTP
													</div>
													<div class="width-50 right">
														'.$item["ftp"].'
													</div>
												</div>
											</li>
											<li>
												<div class="flex">
													<div class="width-50">
														<i class="fa fa-database fa-icon"></i>
														SQL
													</div>
													<div class="width-50 right">
														'.$item["sql"].'
													</div>
												</div>
											</li>
											<li>
												<div class="flex">
													<div class="width-50">
														<i class="fa fa-globe fa-icon"></i>
														Addon domain
													</div>
													<div class="width-50 right">
														'.$item["domain"].'
													</div>
												</div>
											</li>

										<ul>
									</div>
									<div class="hosting-packages-item-right width-40">
										<div class="hosting-packages-item-price">
											<label>'.$item["price"].' ₫</label> /năm
										</div>
										<div class="hosting-packages-item-promotion">
											'.nl2br($item["promotion"] ?? null).'
										</div>
										<a href="/lien-he">ĐĂNG KÝ</a>
									</div>
								</div>
							</div>
							';
						}
						return $out;
					}, $packages).'
				</div>
			</section>
		</div>
		';
		$out .= '
		<style>
			.hosting-packages{
				padding: 20px
			}
			.hosting-packages-item{
				padding: 20px
			}
			.hosting-packages-item-left{
				background: #f7f7f7;
				padding: 10px
			}
			.hosting-packages-item-right{
				background-image: linear-gradient(-45deg,'.$background1.','.$background2.');
				padding: 10px;
				color: white;
				position: relative
			}
			.hosting-packages-item-left ul{
				list-style-type: none;
				margin: 0;
				padding: 0
			}
			.hosting-packages-item-left ul>li{
				padding: 15px 10px;
				border-bottom: 1px solid #dddddd;
			}
			.hosting-packages-item-left ul>li:first-child{
				font-size: 30px;
			}
			.hosting-packages-item-left ul>li .right{
				font-weight: bold
			}
			.hosting-packages-item-price{
				text-align: center;
				margin-top: 20px;
				padding-bottom: 10px;
				border-bottom: 1px solid rgba(255,255,255,0.4)
			}
			.hosting-packages-item-price>label{
				font-size: 22px
			}
			.hosting-packages-item-promotion{
				line-height: 2.0;
				padding: 5px
			}
			.hosting-packages-item-right>a{
				border: 1px solid #f7f7f7;
				color: #f7f7f7;
				padding: 10px;
				border-radius: 30px;
				position: absolute;
				bottom: 20px;
				left: 10px;
				right: 10px;
				text-align: center;
			}
			.hosting-packages-item-right>a:hover{
				opacity: .7
			}
			@media (max-width: 768px){
				.hosting-packages{
					padding: 0
				}
				.hosting-packages-item{
					padding: 20px 10px
				}
				.hosting-packages-item-left ul>li:first-child{
					font-size: 18px;
				}
				.hosting-packages-item-left ul>li{
					padding: 10px 0
				}
				.hosting-packages-item-right>a{
					position: relative;
					display: block;
					bottom: auto;
					left: auto;
					right: auto;
					width: 100%
				}
			}
		</style>
		';
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form = [
			["type"=>"color", "name"=>"background1", "title"=>"Màu nền bảng giá 1", "default"=>"#FF00CC", "value"=>$background1, "required"=>true],
			["type"=>"color", "name"=>"background2", "title"=>"Màu nền bảng giá 2", "default"=>"#FF00CC", "value"=>$background2, "required"=>true],
			["html"=>
				'
				<div class="alert-info">Các gói hosting: </div>'.
				Form::itemManager([
					"data"=>$packages,
					"name"=>"{$prefixName}[data][packages]",
					"sortable"=>false,
					"max"=>100,
					"form"=>[
						["type"=>"text", "name"=>"name", "title"=>"", "note"=>"Tên gói", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"price", "title"=>"Giá tiền / 1 năm", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"disk", "title"=>"Dung lượng", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"bandwidth", "title"=>"Băng thông", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"email", "title"=>"Email", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"ftp", "title"=>"Tài khoản FTP", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"sql", "title"=>"SQL", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"text", "name"=>"domain", "title"=>"Addon domain", "note"=>"", "value"=>"", "attr"=>''],
						["type"=>"textarea", "name"=>"promotion", "title"=>"Khuyễn mãi", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
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