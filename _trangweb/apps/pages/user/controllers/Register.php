<?php
namespace pages\user\controllers;
use models\Users;
use DB, Storage, Form, PushNotifications;
//permission("member", null, "/user/login");

class Register{
	//Trang đăng ký
	public function index(){
		if(user("id")>0 || Storage::option("register", 1)!=1){
			redirect("/website");
		}
		view("Register");
	}


	//Ấn đăng ký
	public function registerSubmit(){
		if(user("id")>0 || Storage::option("register", 1)!=1){
			redirect("/website");
		}
		$redirect = $_GET["continue"] ?? $_COOKIE["continue_link"] ?? "/admin/WebsiteTemplate";
		setcookie("continue_link", null, time() -1 , "/");
		$data = POST("register");
		foreach($data as $name=>$value) {
			$value=trim(strip_tags($value));
			$data[$name]=$value;
		}

		//Họ tên
		$dataInvalid=Form::invalid($data, true);
		$wrong=$dataInvalid["error"];
		$data=$dataInvalid["data"];
		if(Users::where("email", $data["email"])->exists()){
			$wrong="Email đã tồn tại trong hệ thống";
		}
		if(Users::where("phone", $data["phone"])->exists()){
			$wrong="Số điện thoại đã tồn tại trong hệ thống";
		}
		if( empty($data["terms"]) ){
			$wrong = "Vui lòng đọc và đồng ý điều khoản của chúng tôi";
		}
		//Cho phép đăng ký nếu không có lỗi
		if(empty($wrong)){
			unset($data["terms"]);
			Users::createTempAccount($data);
		}
		returnData(["redirect"=>urldecode($redirect ?? "/"), "wrong"=>$wrong ?? ""]);
	}

	/*
	 * Gửi thông báo thành viên mới đăng ký tài khoản
	 */
	public function sendNotifyToManager(){
		$data = user('notify_to_manager');
		if( empty($data) ){
			return;
		}
		PushNotifications::sendToManager($data['browser'], $data['email']);
		Users::updateStorage( user('id'), ['notify_to_manager' => '']);
	}
}

