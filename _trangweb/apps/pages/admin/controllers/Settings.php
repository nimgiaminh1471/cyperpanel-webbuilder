<?php
namespace pages\admin\controllers;
use models\User;
use DB, Storage, Route;

permission("admin", null, "/user/login");

class Settings{

	//Cập nhật các file thiết lập
	public function update(){

		//Cập nhật giao diện
		foreach(glob(Route::path("/create-style")."/*.php") as $f){
			require($f);
		}
		die;
	}
	
	//Lưu cài đặt
	public function settingsSave(){
		//Lưu cài đặt toàn trang
		$storageAllow=["setting", "option", "media_player_broken_link", "builder"];//Cho phép lưu
		foreach($storageAllow as $name){
			$data=$_POST['storage'][$name] ?? [];
			if( count($data)>0 ){
				Storage::update($name, $data);
			}
		}
		foreach($_POST["deleteStorage"] ?? [] as $name){
			Storage::delete($name);
		}
		$this->update();
	}

	//Reset lại cài đặt
	public function settingsRestore(){
		Storage::delete("option");
		Storage::delete("setting");
	}

	//Reset lại phần sắp xếp
	public function settingsSortReset(){
		Storage::delete("option", "settingsSortReset");
		Storage::delete("setting", "settingsSortReset");
	}

	//Chọn FA icon
	public function settingsIconSelect(){
		$gicon=file_get_contents('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');//FILE_USE_INCLUDE_PATH
		preg_match_all("#.([a-zA-Z0-9-]+):before#",$gicon,$icon);
		echo '
		<div id="formListIcon" style="top:0;position:fixed;z-index:142199999997;width:100%;height:100%;overflow:auto;left:0;background-color:rgba(0,0,0,0.5);">
			<div style="max-width:650px;margin:auto;padding-top:50px;display:block;">
				<div class="menu-bg bd" style="text-align: center;">
					<input type="text" id="formSearchIcon" placeholder="Search icon"/>
					<span style="float:right;font-size:20px" class="link formIconSelectClose">
					<i class="fa fa-times-circle-o""></i></span>
				</div>
			<div class="bg form-icon-item" style="text-align: center;">
				<i style="margin: 8px" class="menu bd this_icon link fa fa-times" icon=""> Xóa bỏ icon đã chọn</i>
				<span style="display:none"></span>
			</div>
		';
		foreach($icon[1] as $i=>$ic){
			echo '
			<div class="bg form-icon-item" style="float: left;text-align: center; width:'.(device()=='mobile' ? '20%' : '10%').'">
				<i style="margin: 8px" title="'.$ic.'" class="menu bd this_icon link fa '.$ic.'" icon="'.$ic.'">'.($i==0 ? '' : '').'</i>
				<span style="display:none">'.$ic.'</span>
			</div>
			';
		}
		echo '</div></div>';
}


}

