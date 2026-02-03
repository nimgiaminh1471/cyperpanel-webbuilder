<?php
/*
# Class xử lý đường dẫn
*/
class Route{
	private static $route,$folder;


	//Thư mục chứa route
	public static function folder($folder=""){
		if(empty($folder)){
			return self::$folder;
		}else{
			self::$folder=strtolower($folder);
		}
	}
	public static function nameSpace($folder=""){
		if(empty($folder)){
			$folder=self::$folder;
		}
		return str_replace("-", "__", $folder);
	}



	//Path đến thư mục route
	public static function path($path=""){
		return APPS_ROOT."/pages/".self::$folder."/".$path;
	}




	//Route phương thức GET
	public static function get($get,$action,$folder=""){
		if(isset($_GET[$get])){
			if(empty($folder)){ $folder=self::$folder; }
			self::$route=["path"=>$get, "folder"=>$folder, "action"=>$action, "params"=>[]];
		}
	}




	//Route phương thức POST
	public static function post($post,$action,$folder=""){
		if(isset($_POST[$post])){
			if(empty($folder)){ $folder=self::$folder; }
			self::$route=["path"=>$post, "folder"=>$folder, "action"=>$action, "params"=>[]];
		}
	}




	//Route URL
	public static function link($path, $action, $folder=""){
		if(empty($folder)){ $folder=self::$folder; }
		if(empty(self::$route)){
			$pathSplit = explode("/", $path);
			$runThis=0;
			if(count($pathSplit)>=count(URI_SPLIT)){
				foreach($pathSplit as $k=>$v){
					if( strpos($v,"}")===FALSE && isset(URI_SPLIT[$k]) && URI_SPLIT[$k]==$v || strpos($v,"}")!==FALSE && isset(URI_SPLIT[$k]) ||strpos($v,"?}")!==FALSE ){
						//Nếu phần này khớp với link
						if(strpos($v,"}")!==FALSE && isset(URI_SPLIT[$k])){
							preg_match("#{(.+?)([?]*)}#is", $v,$key);
							$params[$key[1]]=URI_SPLIT[$k];//Lưu các tham số
						}
						$runThis++;
					}else{
						//Route không khớp với link
						break;
					}
				}
			}
			if(count($pathSplit)==$runThis || $path=="*"){
				self::$route=["path"=>$path, "folder"=>$folder, "action"=>$action, "params"=>(isset($params) ? $params : [])];
			}
		}

		if($path=="*"){
			self::launch();//Khởi tạo
		}
	}





	// Tạo thư mục & file cho route mới
	private static function createFolder($controller="",$params=""){
		$folderList=["views", "controllers", "widgets"];
		foreach($folderList as $name){
			$sourceFolder=self::path($name);
			//Tạo các thư mục
			if(!file_exists($sourceFolder)){
    			mkdir($sourceFolder, 0755);
    		}
        	switch($name){

        		//Tạo file controller mẫu
        		case "controllers":
					$path=$sourceFolder."/".$controller[0].".php";
					if(!file_exists($path)){
		    			$param="";$pi=0;
		    			foreach ($params as $key=>$value) {
		    				$param.=''.($pi==0 ? '' : ', ').'$'.$key.'=""';
		    				$pi++;
		    			}
						require_once(SYSTEM_ROOT."/system/template/Controller.php");
					}
        		break;

        		//Tạo file widget mẫu
        		case "widgets":
        			$path=$sourceFolder."/Example.php";
					if(!file_exists($path)){
						require_once(SYSTEM_ROOT."/system/template/Widget.php");
					}
        		break;

        	}
		}
	}









	// Chạy Route
	public static function launch(){
		$route=self::$route;
		self::folder($route["folder"]);
		if(is_callable($route["action"])) {
			//Nếu action là function
			echo call_user_func_array($route["action"], $route["params"]);
		}else{
			//Nếu action gọi controller
			$controller = explode('@', $route["action"]);
			if(count($controller) == 2) {
				$className = $controller[0];
				$method     = $controller[1];
				self::createFolder([$className,$method],$route["params"]);
				$classSpace="pages\\".self::nameSpace()."\\controllers\\$className";
				if(class_exists($classSpace)){
					$class      = new $classSpace;
					echo call_user_func_array(array($class, $method), $route["params"]);
				}else{
					printError("<b>Trang không tồn tại</b>: {$classSpace}");
				}
			}
		}
	}










}//</Class>



//Tách URI để so sánh
$uriSplit = DOMAIN=="localhost" ? preg_replace("/\/(.+)public([_html]*)/","",URI) : URI;
define("URI_SPLIT", explode("/", rtrim(strtok($uriSplit,'?'), "/")) );

//Load file route tương ứng với URI
$routeFolder = empty(URI_SPLIT[1]) ? "" : URI_SPLIT[1];
$routePath 	 = APPS_ROOT."/pages/".$routeFolder;
if( $routeFolder!="_general" && file_exists($routePath)){
	$routeFile = $routePath."/@Route.php";
	Route::folder($routeFolder);
	if(!file_exists($routeFile)){
		require(SYSTEM_ROOT."/system/template/Route.php");
	}
	require($routeFile);
}
require_once(APPS_ROOT."/pages/@Route.php");

//Trang không tồn tại
Route::link("*", function(){
	return pageNotfound();
});