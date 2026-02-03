<?php

//Trang chính
Route::link("/admin/{panelName?}", "Main@index");



//Cài đặt
Route::post("settingsSave","Settings@settingsSave");//Lưu cài đặt
Route::post("settingsRestore","Settings@settingsRestore");//Khôi phục cài đặt
Route::post("settingsSortReset","Settings@settingsSortReset");//Reset lại sắp xếp
Route::post("settingsIconSelect","Settings@settingsIconSelect");//Chọn FA icon


//Chuyên mục
Route::post("categoriesUpdateSubmit","CategoriesManager@categoriesUpdateSubmit");//Thêm & sửa chuyên mục mới
Route::post("deleteCategories","CategoriesManager@deleteCategories");//Xóa chuyên mục





//Đăng bài viết
Route::post("postSubmit","PostEditor@postSubmit");//Thêm chuyên mục mới
Route::post("postCreateLink","PostEditor@postCreateLink");//Thêm chuyên mục mới




//Quản lý bài viết
Route::post("checkPostAction","PostsList@checkPostAction");//Thao tác bài viết
