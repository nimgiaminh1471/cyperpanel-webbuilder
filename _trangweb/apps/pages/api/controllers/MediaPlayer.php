<?php
namespace pages\api\controllers;
use models\Users;
use DB,Storage;


class MediaPlayer{

	public function func(){
		switch( POST("action") ){

			//Lưu liên kết bị lỗi
			case "brokenLink":
				$name="media_player_broken_link";
				$data=call_user_func("Storage::$name");
				$data[md5(POST("link"))]=POST("link");
				Storage::update($name, $data);
			break;

			//Thống kê lượt xem quảng cáo
			case "count":
				$adsID=POST("id");
				$optionName="ads_video_".POST("type");
				$data=Storage::option($optionName);
				if(isset($data[$adsID])){
					$fileName=md5(POST("name"));
					$data[$adsID][$fileName]=($data[$adsID][$fileName]??0)+1;
					Storage::update("option", [$optionName=>$data]);
				}
			break;
		}
	}

}

