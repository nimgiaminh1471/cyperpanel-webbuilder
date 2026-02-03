<?php
namespace pages\api\controllers;
use models\Users;
use DB,Storage;
use models\Posts;
use models\BuilderDomain;

class Builder{

	public function func(){
		//Kiếm tra có phải admin hay không
		if( !empty($_GET["check_admin"]) ){
			$getUser = Users::where("check_admin", $_GET["check_admin"])->first()->id ?? null;
			if( permission("website_template_master", $getUser) ){
				return "true";
			}else{
				return "false";
			}
		}
	}

	/*
	 * Hiện thông báo website hết hạn
	 */
	public function expiredNotify($domain){
		$domain = str_replace("www.", "", $domain);
		$web = BuilderDomain::where("domain", $domain)->first();
		if( strlen($web->package) == 0 ){
			return;
		}
		$user = Users::find($web->users_id);
		if($user->role == 2){
			$user = Users::where("role", 1)->orderBy("id", "ASC")->first();
		}
		echo '
		<div class="builder-notify">
			Website hiện đang tạm khóa, vui lòng liên hệ bộ phận hỗ trợ:
			<a href="tel:'.$user->phone.'" style="color: blue">
				<b>'.$user->phone.'</b>
			</a>
			-
			<a href="mailto:'.$user->email.'" style="color: blue">
				<b>'.$user->email.'</b>
			</a>
		</div>
		<div class="builder-notify-fixed"></div>
		'; 
		echo '
		<style>
			.builder-notify{
				height: 80px;
				line-height: 80px;
				background: white;
				color: red;
				text-align: center;
				position: fixed;
				top: 0;
				width: 100%;
				z-index: 1402199719977777;
			}
			.builder-notify-fixed{
				height: 80px;
			}
		</style>
		';
	}
}