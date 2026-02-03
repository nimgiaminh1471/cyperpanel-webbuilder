<?php
/*
# Tạo file route mẫu
*/
$className=ucwords( str_replace("-", " ", $routeFolder) );
file_put_contents($routeFile,
'<?php
Route::link("/'.$routeFolder.'", "'.str_replace(" ", "", $className).'@index");

//Route::get("Test","ControllerName@function");//Khi có $_GET["Test"]

//Route::post("Test","ControllerName@function");//Khi có $_POST["Test"]
');