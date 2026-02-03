@extends("Default")
@php
	define("PAGE", [
		"name"        =>"Tên trang",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"index,follow"
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
