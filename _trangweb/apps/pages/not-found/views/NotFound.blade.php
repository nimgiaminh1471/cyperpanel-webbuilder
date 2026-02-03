@extends("Default")
@php
	if( empty( user("id") ) ){
		http_response_code(404);
	}
	define("PAGE", [
		"name"        =>"Trang không tồn tại",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"noindex,nofollow"
	]);
	//Lưu liên kết lỗi
	if( empty( user("id") ) ){
		$data=Storage::pageNotFound();
		unset($data[THIS_URL]);
		$data[THIS_URL]=time();
		$data=array_slice($data, -20);
		Storage::update("pageNotFound", $data, null);
	}
@endphp


@section("main")
	@parent
@endsection


@section("script")
	<script></script>
@endsection


@section("footer")
	@parent
@endsection
