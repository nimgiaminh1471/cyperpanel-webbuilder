<?php
namespace pages\lien__he\controllers;
use models\Users;
use mailer\WebMail;
use DB, Form;

//permission("admin", null, "/user/login");

class LienHe{

	public function index(){
		$controllerName = "Controller: LienHe";
		$functionName="Function: index";
		view("LienHe", compact("controllerName", "functionName"));
	}

	/*
	 * Gửi liên hệ
	 */
	public function submit(){
		$data = POST("contact");
		$required = ["name", "email", "phone", "title", "message"];
		foreach($required  as $name) {
			if( isset($data[$name]) ){
				$data[$name] = trim(strip_tags($data[$name]));
			}else{
				$error = "Vui lòng nhập đầy đủ thông tin";
			}
		}
		$dataInvalid = Form::invalid($data, true);
		$error = $dataInvalid["error"];
		if( empty($error) ){
			if( strlen( ($data["message"] ?? null) ) < 5 || strlen( ($data["title"] ?? null) ) > 1000 ){
				$error = "Chỉ cho phép nội dung từ 5 đến 1000 ký tự";
			}
		}
		$data = $dataInvalid["data"];
		if( empty($error) ){
			$error = \models\Users::createTempAccount($data);
		}
		return returnData(["error" => $error ?? ""]);
	}

}

