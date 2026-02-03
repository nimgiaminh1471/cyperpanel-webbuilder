<?php
Route::link("/website/recharge", "Recharge@index");//Trang nạp tiền
Route::link("/website/{categories?}", "Website@index");//Trang quản lý web

Route::link("/website/preview/{template}", "Website@preview");//Xem trước web

//Route::get("Test","ControllerName@function");//Khi có $_GET["Test"]

//Route::post("Test","ControllerName@function");//Khi có $_POST["Test"]
