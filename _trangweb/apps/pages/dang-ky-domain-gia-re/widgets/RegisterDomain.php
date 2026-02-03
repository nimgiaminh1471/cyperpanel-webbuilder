<?php
/*
# Giới thiệu dịch vụ
*/
namespace pages\dang__ky__domain__gia__re\widgets;
use Form, Storage;
use Assets, Widget;
class RegisterDomain{

	//Thông số mặc định
	private static $option=[
		"domainExt"=>[],
		"checkExt" => "com|net",
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Đăng ký tên miền",
			"icon"  => "fa-global",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out = $domainExtCheck = "";
		$domainExtGroupByPrice = [];
		$domainExtSplit = [];
		foreach($domainExt as $ext){
			$domainExtSplit = array_merge($domainExtSplit, explode("|", $ext["name"]) ); 
		}
		
		$out .= '
		<div class="bg">
			<section class="main-layout" style="padding: 0 15px; max-width: 780px">
				<div id="check-domain-form">
					<div class="flex" style="align-items: flex-end;">
						<div class="width-60" style="margin: 10px 0; line-height: 1.5">
							<h2 class="primary-color">
								<b>Đăng ký tên miền ngay hôm nay</b>
							</h2>
						</div>
						<div class="width-40">
							<img src="/files/uploads/2019/08/kiem-tra-ten-mien.png" alt="Tên miền">
						</div>
					</div>
					<div class="check-domain-input">
						<span class="primary-color">WWW.</span>
						<input id="check-domain-domain" class="input width-100" name="check_domain" placeholder="Nhập tên miền cần kiểm tra" style="font-size: 15px" value="'.GET('domain').'">
						<div>
							<button type="button" class="btn btn-gradient" style="font-size: 15px">KIỂM TRA</button>
						</div>
					</div>
					<div class="check-domain-result hidden"></div>
				</div>
			</section>
		</div>
		';
		$out .= '
		<div class="bg">
			<section class="main-layout pd-5">
				<div class="pd-20 bg">
					<div class="flex">
						<div class="width-40 domain-price-heading" style="background-image: linear-gradient(-45deg, #f0b50f,#bb108e);">
							TÊN MIỀN
						</div>
						<div class="width-30 pd-20 domain-price-heading" style="background-image: linear-gradient(-45deg,#f00f8f,#1094bb);">
							KHỞI TẠO
						</div>
						<div class="width-30 pd-20 domain-price-heading" style="background-image: linear-gradient(-45deg,#c3f00f,#1094bb);">
							GIA HẠN
						</div>
					</div>
					'.call_user_func(function($domainExt){
						$out = '';
						foreach($domainExt as $ext){
							$out .= '
								<div class="flex domain-ext-list">
									<div class="width-40">
										.'.$ext["name"].'
									</div>
									<div class="width-30 center">
										'.number_format( toNumber($ext["setup_price"]) ).' ₫
									</div>
									<div class="width-30 center">
										'.number_format( toNumber($ext["renew_price"]) ).' ₫
									</div>
								</div>
							';
						}
						return $out;
					}, $domainExt).'
				</div>
			</section>
		</div>
		';
		$out .= '
		<script>
			var domainExtList = '.json_encode( explode("|", $checkExt) ).'
			var form = $("#check-domain-form");
			function checkDomainConnect(domain, ext){
				$.ajax({ 
					type: "POST", 
					url: "", 
					data: {
						check_domain: domain,
						domain_ext: "."+ext
					}, 
					success: function(data) { 
						$(".domain-item[data-ext=\'"+ext+"\']").html(data);
					},
					error: function(err){
						setTimeout(function(){
							checkDomainConnect(domain, ext);
						}, 4e3);
					}
				});
			}
			function checkDomain(){
				form.find(".check-domain-result").html("").show();
				var fulldomain = form.find("#check-domain-domain").val();
				var domain = fulldomain.split(".")[0];
				if(domain.length < 3){
					form.find(".check-domain-result").html(\'<div class="alert-danger">Vui lòng nhập tên miền!</div>\');
					return false;
				}
				var ext = fulldomain.split(".").splice(1).join(".")
				var extList = domainExtList;

				if(ext.length > 0){
					var index = extList.indexOf(ext);
					if(index !=-1 ){
						extList.splice(index, 1);
					}
					extList.unshift(ext);
				}
				for(var i = 0; i < extList.length; i++){
					form.find(".check-domain-result").append(\'<div style="margin-top: 20px; vertical-align: middle" class="domain-item" data-ext="\'+extList[i]+\'">\'+domain+\'.\'+extList[i]+\'<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i></div>\').show();
					(function(i){
						setTimeout(function(){
							checkDomainConnect(domain, extList[i]);
						}, i*1000);
					})(i);
				}
			}
			$("#check-domain-form").on("click", "button", function(){
				checkDomain();
			});
			$("#check-domain-form").on("keyup", "input", function(e){
				if(e.keyCode == 13){
					checkDomain();
				}
			});
			if( $(".check-domain-input input").val().length > 0 ){
				$(".check-domain-input button").click();
			}
		</script>
		';
		if(PAGE_EDITOR){
			$style = '
			.domain-ext-list:hover{
				background: #EAEAEA
			}
			.domain-price-heading{
				padding: 20px;
				color: white;
				font-size: 25px;
				text-align: center
			}
			.domain-ext-list>div{
				padding: 20px
			}
			.domain-ext-list:nth-child(odd){
				background: #eeeeee
			}
			.check-domain-input{
				position: relative;
				overflow: hidden
			}
			.check-domain-input input,
			.check-domain-input button{
				padding: 15px;
				font-size: 20px;
			}
			.check-domain-input>div{
				position: absolute;
				right: 0;
				top: 0;
				height: 100%;
			}
			.check-domain-input input{
				padding-left: 65px;
				border: 1px solid '.Storage::setting("theme__form_gradient_background1").';
			}
			.check-domain-input button{
				right: 0;
				height: 100%;
				top: 0;
				background: '.Storage::setting("theme__form_gradient_background1").'
			}
			.check-domain-input button:before{
				position: absolute;
				content: "";
				top: 0;
				height: 100%;
				left: -29px;
				border-right: 30px solid '.Storage::setting("theme__form_gradient_background1").';
				border-bottom: 0px solid transparent;
				border-top: 50px solid transparent;
				border-left: 0 solid transparent;
			}
			.check-domain-input span{
				position: absolute;
				font-weight: bold;
				left: 5px;
				top: 50%;
				transform: translate(0, -50%);
			}
			#check-domain-form .primary-color{
				color: '.Storage::setting("theme__form_gradient_background1").' !important
			}
			@media (max-width: 768px){
				.domain-price-heading{
					font-size: 15px;
					padding: 10px 5px
				}
			}
			';
			Widget::css($style);
		}
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"text", "name"=>"checkExt", "title"=>"Cho phép kiểm tra", "note"=>"com|net|com.vn|net.vn", "value"=>$checkExt, "attr"=>''],
			["html"=>
				'
				<div class="alert-info">Tên miền: </div>'.
				Form::itemManager([
					"data"=>$domainExt,
					"name"=>"{$prefixName}[data][domainExt]",
					"sortable"=>false,
					"max"=>100,
					"form"=>[
						["type"=>"text", "name"=>"name", "title"=>"", "note"=>"Đuôi miền vd: com", "value"=>"", "attr"=>''],
						["type"=>"currency", "name"=>"setup_price", "title"=>"Giá khởi tạo", "note"=>"Giá khởi tạo", "min"=>0, "max"=>99999, "value"=>"","attr"=>''],
						["type"=>"currency", "name"=>"renew_price", "title"=>"Giá gia hạn", "note"=>"Giá gia hạn", "min"=>0, "max"=>99999, "value"=>"","attr"=>''],
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