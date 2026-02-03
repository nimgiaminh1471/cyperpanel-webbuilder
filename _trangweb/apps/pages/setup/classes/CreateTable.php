<?php
/*
# Tạo bảng
*/

/*$dataContents = file( dirname(__DIR__)."/backups/data.sql");
foreach($dataContents as $query){
	DB::query($query);
}
die;*/
function CreateTable($table,$content,$description){
	global $__createTableFirst;
	$__createTableFirst++;
	//Lưu danh sách bảng theo thứ tự để restore backup
	$tableListPath=__DIR__."/TableList.data";
	if($__createTableFirst==1){
		file_put_contents($tableListPath, $table);
	}else{
		file_put_contents($tableListPath, "\n".$table, FILE_APPEND);
	}


	//Tạo bảng
	$check=DB::query("SHOW COLUMNS FROM `$table`");
	$content=trim($content);
	$content=trim($content,",");
	if(!isset($check->num_rows)){
		DB::query("CREATE TABLE `$table` (".$content.")
			CHARACTER SET ".TABLE_CHARACTER." COLLATE ".TABLE_COLLATE."
			");
	}


}
