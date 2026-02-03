<?php
namespace pages\user\controllers;
use models\Users;
use DB;
use mailer\WebMail;
//permission("member", null, "/user/login");

class Forget{
	//Trang quên mật khẩu
	public function index(){
		$user=Users::where("forget_key",GET("forget_key","none"))->first();
		view("Forget", compact("user"));
	}

	//Ấn khôi phục
	public function passwordForgetSubmit(){
		if(empty($_POST["forget_key"])){
			if( !captchaCorrect($_POST["captcha"]) ){
				$msg="Mã xác minh không hợp lệ";
			}else{
				//Nhập email để lấy mật khẩu
				$user=Users::where("email",POST("email"))->first();
				if(empty($user->email)){
					//Email không tồn tại
					$msg="Email <b>".POST("email")."</b> không tồn tại";
				}else{
					//Gửi mã xác nhận
					$key=rand(100000,999999);
					$sendMail = WebMail::send([
						"To"          => [$user->email],
						"Subject"     => "Lấy lại mật khẩu tài khoản: {$user->name}",
						"Body"        => "Xin chào {$user->name}<br/>Bạn đã gửi yêu cầu lấy lại mật khẩu lúc: ".date("H:m - d/m/Y")."<br>
						Hãy nhập mã sau để đổi mật khẩu: <span style=\"color:red;font-size: 20px; text-align: center;\">{$key}</span>
						",
						"Attachments" => []
					]);
					if(empty($sendMail)){
						Users::find($user->id)->update(["forget_key"=>$key]);
						$msg="sent";
					}else{
						$msg="Lỗi gửi email, vui lòng liên hệ Admin";
					}
				}
			}
		}else{
			//Đổi mật khẩu mới
			$user=Users::where("email",POST("email"))->where("forget_key",POST("forget_key","none"))->first();
			$thisUser=Users::where("email", POST("email"))->first();
			if(empty($user->id)){
				Users::where("id",$thisUser->id)->update(["forget_expired"=>($thisUser->forget_expired+1)]);
				if($thisUser->forget_expired>5 || strlen($thisUser->forget_key)>10){
					Users::where("id",$thisUser->id)->update(["forget_expired"=>0, "forget_key"=>passwordCreate(randomString(50))]);
					$msg="Bạn đã nhập sai mã quá nhiều lần";
				}else{
					$msg="Mã xác nhận không hợp lệ";
				}
			}

			if(strlen(POST("password"))<6){
				$msg="Mật khẩu phải trên 6 ký tự";
			}

			if(POST("password")!=POST("rePassword")){
				$msg="Mật khẩu nhập lại không khớp nhau";
			}

			if(empty($msg)){
				Users::where("id",$user->id)->update(["password"=>passwordCreate($_POST["password"]), "forget_key"=>"", "login_key"=>""]);
				$msg="changed";
			}
		}
		returnData(["msg"=>$msg??"Lỗi không xác định"]);
	}
}

