<?php
Route::link("/dang-ky-domain-gia-re", "DangKyDomainGiaRe@index");

//Route::get("Test","ControllerName@function");//Khi có $_GET["Test"]

Route::post("check_domain","DangKyDomainGiaRe@checkDomain");// Kiểm tra tên miền đã đăng ký hay chưa
