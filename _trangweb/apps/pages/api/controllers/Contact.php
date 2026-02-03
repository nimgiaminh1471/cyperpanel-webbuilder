<?php
namespace pages\api\controllers;
use models\Users;
use mailer\WebMail;
use DB, Form;

class Contact{

	//Tạo ảnh mã xác minh
	public function submit(){
		$data = POST("contact");
		$required = ["phone", "message"];
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
			// Tạo tài khoản nếu không có lỗi
			$error = \models\Users::createTempAccount($data);
		}
		return returnData(["error" => $error ?? ""]);
	}

}

