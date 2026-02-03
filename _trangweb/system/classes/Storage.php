<?php
/*
# Thao tác dữ liệu lưu trữ
*/

class Storage{
	private static $cache=[];

	//Lấy dữ liệu
	public function _getStorage($name, $subKey="", $default=""){
		$data=self::_getData($name);
		if(!empty($subKey)){
			if(isset($data[$subKey])){
				$data=$data[$subKey]=="" ? $default : $data[$subKey];
			}else{
				$data=$default;
			}
		}
		return $data;
	}

	//Cập nhật dữ liệu
	public static function update($name, $newData, $update=true){
		if(is_null($update)){
			$data=[];
		}else{
			$data=self::_getData($name);
		}
		if(!is_array($newData)){
			return;
		}
		foreach($newData as $key=>$value){
			if(is_array($value) && $update){
				$newValue=[];
				foreach($value as $subKey=>$subValue){
					if( isset($data[$key][$subKey]) && is_array($subValue) ){
						$newValue[$subKey]=array_replace($data[$key][$subKey], $subValue);
					}else{
						$newValue[$subKey]=$subValue;
					}
				}
			}else{
				$newValue=$value;
			}
			$data[$key]=$newValue;
		}
		Storage::_refreshCache($name, $data);
		return DB::table("storage")->where("key", $name)->update(["key"=>$name, "value"=>serialize($data)], true);
	}

	//Xóa dữ liệu
	public static function delete($name, $subKey=""){
		$data=self::_getData($name);
		if(empty($subKey)){
			$data=[];
		}else{
			unset($data[$subKey]);
		}
		Storage::_refreshCache($name, $data??[]);
		if(empty($data)){
			return DB::table("storage")->where("key", $name)->delete();
		}else{
			return DB::table("storage")->where("key", $name)->update(["key"=>$name, "value"=>serialize($data) ]);
		}
	}

	//Lấy dữ liệu
    public static function _getData($name){
    	if(count(self::$cache)>10){
			array_splice(self::$cache,0,-10);
		}
		if(!isset(self::$cache[$name])){
			$data=DB::table("storage")->where("key", $name)->value("value");
			$data=unserialize($data);
			if(!is_array($data)){
				$data=[];
			}
			self::$cache[$name]=$data;
		}
		return self::$cache[$name];
    }

	//Cập nhật lại dữ liệu
    public static function _refreshCache($name, $data){
    	self::$cache[$name]=$data;
    }

	//Gọi method vô danh
	public static function __callStatic($method, $params=""){
		return (new static)->_getStorage($method, ...$params);
	}
}
