<?php
namespace pages\api\controllers;
use models\Users;
use DB,Storage;
use models\Posts;


class AdsCount{

	//Quảng cáo popup
	public function popup(){
		$adsID=POST("id");
		$postID=POST("post");
		$storageName="ads_popup";
		if(empty($postID)){
			//Quảng cáo toàn trang
			$data=Storage::option($storageName);
			if(isset($data[$adsID])){
				$data[$adsID]["views"]=($data[$adsID]["views"]??0)+1;
				Storage::update("option", [$storageName=>$data]);
				setcookie("adsPopup", COOKIE("adsPopup")."-".$adsID, time()+Storage::option("ads_popup_off")*3600, "/");
			}
		}else{
			//Quảng cáo trong bài viết
			$data=Posts::get($postID)->storage[$storageName] ?? "";
			if(isset($data[$adsID])){
				$data[$adsID]["views"]=($data[$adsID]["views"]??0)+1;
				Posts::updateStorage($postID, [$storageName=>$data]);
			}
		}
	}
	
}