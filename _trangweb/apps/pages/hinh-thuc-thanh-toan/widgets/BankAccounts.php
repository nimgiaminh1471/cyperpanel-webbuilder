<?php
/*
# Widget
*/
namespace pages\hinh__thuc__thanh__toan\widgets;
use Form, Storage;
class BankAccounts{

	//Thông số mặc định
	private static $option=[
		"content"=>"<p>Test</p>"
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Tài khoản ngân hàng",//Đặt tên để kích hoạt
			"icon"  => "fa-code",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$out = '
		<div class="bg">
			<section class="main-layout pd-15" style="line-height: 1.6">
				<div style="font-size: 26px" class="primary-color">
					<i class="fa fa-university"></i>
					Chuyển khoản qua ngân hàng, Internet Banking hoặc máy ATM
				</div>

		';
		$out .='<div class="flex flex-medium">';
		foreach(Storage::setting("banks") as $bank=>$item){
			$out .='
				'.call_user_func(function($item){
					$out = '
					<div class="width-50 pd-10">
						<div style="background: #EFF6F7;padding: 10px; border-radius: 10px">
						<div class="center pd-5">
							<img style="max-height: 60px" src="'.$item["image"].'">
						</div>
					';
					$infoLabel=[
						"name"   =>"Ngân hàng",
						"user"   =>"Chủ TK",
						"number" =>"Số TK",
						"office" =>"Chi nhánh"
					];
					foreach($item as $key=>$info){
						if( isset($infoLabel[$key]) ){
							$out .= '
								<div class="flex pd-5">
									<div style="width: 120px">'.$infoLabel[$key].':</div> <div><b>'.$info.'</b></div>
								</div>
							';
						}
					}
					return $out;
				}, $item).'
				</div>
			</div>
			';	
		}
		$out .= '</div>';
		$out .= '</section></div>';
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			[],
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
