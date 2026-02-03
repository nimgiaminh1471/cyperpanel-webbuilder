<?php
namespace pages\user\controllers;
use models\Users;
use models\BuilderDomain;
use DB;

class Login{

	//Trang đăng nhập
	public function index(){
		$controllerName="Đăng Nhập";
		if(user("id")>0){ redirect($_COOKIE["ref_link"]??"/"); }//Nếu đã đăng nhập
		if( isset($_GET["login_key"]) ){
			setcookie("login_key", $_GET["login_key"], time()+3600*24*365, "/");
			redirect("/admin");
		}
		view("Login");
	}

 
	//Ấn đăng nhập
	public function loginSubmit(){
		$success=0;
		$user=Users::where("email",POST("username"))->orWhere( "phone",POST("username") )->first(true);
		if( isset($user->email) && !empty(POST("username")) ){
			//Tên tài khoản đúng
			if(user("login_failed", $user->id)>9){
				if(time()>user("login_failed_time", $user->id) ){
					Users::updateStorage($user->id, ["login_failed"=>0]);
					$msg='Đã mở khóa đăng nhập,Hãy đăng nhập lại';
				}else{
					$msg='Đăng nhập sai quá 10 lần,hãy đăng nhập lại sau <b>'.(user("login_failed_time", $user->id)-time()).'</b> giây';
				}
			}else{
				if(passwordCheck(POST("password"), $user->password)){
					$msg='Đăng nhập thành công!';
					$success=1;
					$loginKey=empty($user->login_key) ? md5("".$user->email."".randomString(50)."") : $user->login_key;
					if( BuilderDomain::where('users_id', $user->id)->total() > 0 ){
						$redirect = '/admin/WebsiteList';
					}else{
						$redirect=$_GET["continue"] ?? $_COOKIE["ref_link"] ?? "/admin/WebsiteTemplate";
					}
					setcookie("login_key", $loginKey, time()+3600*24*365, "/");
					$loginHistory=user("login_history", $user->id);
					if(!is_array($loginHistory)){
						$loginHistory=[];
					}

					$loginTime=date("H:i - d/m/Y");
					$loginHistory[$loginTime]=device(false, true);
					$loginHistory=array_slice($loginHistory, -3);
					Users::updateStorage($user->id, ["login_history"=>$loginHistory, "login_failed"=>0]);
					Users::find($user->id)->update([
						"login_key"=>$loginKey
					]);
				}else{
					$msg='Thông tin đăng nhập không hợp lệ!';
					Users::updateStorage($user->id, ["login_failed"=>(user("login_failed", $user->id)+1), "login_failed_time"=>time()+900] );
				}
			}
		}else{
			//Tên đăng nhập không đúng
			$msg='Thông tin đăng nhập không hợp lệ!';
		}
		returnData(["success"=>$success, "redirect"=>urldecode($redirect ?? "/"), "msg"=>$msg ?? "Lỗi không xác định"]);
	}

}//</Class>