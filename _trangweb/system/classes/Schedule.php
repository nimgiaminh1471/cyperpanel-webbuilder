<?php
/*
# Hẹn giờ thực hiện tác vụ
*/
class Schedule{
	private $format=[];
	//Chạy lệnh
	private function run($format, $params){
		$format=md5(Route::path().THIS_LINK)."_".$format;
		$this->format[]=$format;
		if( Storage::schedule($format)!=date( explode("_", $format)[1] ) ){
			call_user_func_array($params[0], $params[1]??[]);
		}
	}

	//Gọi các phương thức ảo
	public function __call($method, $params){
		$this->run($method, $params);
	}

	//Đã chạy hết toàn bộ lệnh
	public function __destruct(){
		foreach( array_unique($this->format) as $format){
			$schedule[$format]=date( explode("_", $format)[1] );
		}
		if( !empty($schedule) ){
			Storage::update("schedule", $schedule);
		}
	}
}
