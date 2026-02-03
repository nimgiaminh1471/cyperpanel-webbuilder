<?php
Route::link("/lien-he", "LienHe@index");

//Route::get("Test","ControllerName@function");//Khi có $_GET["Test"]

Route::post("contact","LienHe@submit"); // Gửi liên hệ
