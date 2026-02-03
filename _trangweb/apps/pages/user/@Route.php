<?php

//Đăng nhập
Route::link("/user/login","Login@index");//Trang đăng nhập
Route::post("loginSubmit","Login@loginSubmit");//ấn đăng nhập
Route::post("passwordForgetSubmit","Login@passwordForgetSubmit");//ấn quên mật khẩu

//Đăng ký
Route::link("/user/register","Register@index");//Trang đăng ký
Route::post("registerSubmit","Register@registerSubmit");//ấn đăng ký


//Quên mật khẩu
Route::link("/user/forget","Forget@index");//Trang quên mật khẩu
Route::post("passwordForgetSubmit","Forget@passwordForgetSubmit");//ấn quên mật khẩu


//Trang cá nhân
Route::link("/user/profile/{tab?}","Profile@index");//Trang quên mật khẩu

Route::link("/user/sendNotifyToManager","Register@sendNotifyToManager");// Gửi thông báo tài khoản mới đăng ký