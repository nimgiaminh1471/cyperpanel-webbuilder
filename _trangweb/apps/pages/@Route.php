<?php
/*
# Các Route trong file này sẽ hoạt động trên toàn bộ hệ thống (@route trong các thư mục khác chỉ hoạt động với thư mục đó)
*/

//Trang chủ
Route::link("", "Home@index", "home");

//Trang bài viết
Route::link("/{link?}", "PostSingle@main", "post-single");
