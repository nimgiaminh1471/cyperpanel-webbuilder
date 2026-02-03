@extends("Default")
@php
	define("PAGE", [
		"name"        =>"Trang tìm kiếm",
		"title"       =>__("Tìm kiếm: ")."".ucfirst( GET("keyword") ),
		"description" =>__("Tìm kiếm: ")."".ucfirst( GET("keyword") ),
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"noindex,follow"
	]);
@endphp


@section("main")
	@parent
@endsection


@section("script")
@endsection


@section("footer")
	@parent
@endsection
