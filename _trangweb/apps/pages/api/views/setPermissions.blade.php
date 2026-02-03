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
	<script>
		alert("Đã tự động tạo trang");
	</script>
@endsection


@section("footer")
	@parent
@endsection
