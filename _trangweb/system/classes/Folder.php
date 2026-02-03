<?php
/*
# Class xử lý thư mục
*/
class Folder{
	//Xóa toàn bộ file,thư mục
	public static function clear($dir, $reCreate=true) {
		if( !is_dir($dir) ){
			mkdir($dir, 0755, true);
		}
		$dir = rtrim($dir, "/");
		$dirFix = str_replace(["[", "]"], ["\[", "\]"], $dir);
		$structure=array_merge(glob($dir."/*"), glob($dirFix."/{,.}*", GLOB_BRACE));
		$structure=array_unique($structure);
		if (is_array($structure)) {
			foreach($structure as $file) {
				if( in_array(basename($file), [".","..","..."]) ){
					continue;
				}
				if( is_file($file) || is_link($file) ){
					unlink($file);
				}else if(is_dir($file) ){
					self::clear($file, false);
				}
			}
		}
		if( !$reCreate ){
			rmdir($dir);
		}
	}

	//Copy toàn bộ nội dung trong thư mục
	public static function copy($src, $dst, $excluded=[]) {
		if( !file_exists($src) ){
			return;
		}
		$dir = @opendir($src);
		if (!is_dir($dst)){
			mkdir($dst, 0755, true);
		}
		while (false !== ($file = readdir($dir))) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if( in_array($dst.'/'.$file, $excluded) ){
					continue;
				}
				if ( is_dir($src . '/' . $file) ){
					self::copy($src . '/' . $file, $dst . '/' . $file, $excluded);
				}else{
					copy($src.'/'.$file, $dst.'/'.$file);
				}
			}
		}
		closedir($dir); 
	}

	//Lấy toàn bộ danh sách thư mục
	public static function list($path){
	    $dir_paths = array();
	    foreach (glob($path . "/*", GLOB_ONLYDIR) as $filename){
	        $dir_paths[] = $filename;
	        $a = glob("$filename/*", GLOB_ONLYDIR);
	        if( is_array( $a ) )
	        {
	            $b = self::list( "$filename/*");
	            foreach( $b as $c )
	            {
	                $dir_paths[] = $c;
	            }
	        }
	    }
	    return $dir_paths;
	}
}