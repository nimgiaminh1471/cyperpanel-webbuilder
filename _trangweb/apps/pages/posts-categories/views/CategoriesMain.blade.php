@extends("Default")
@php
	define("WIDGET_DATA", ["categories"=>(array)$categories]);
	define("PAGE", [
		"name"        =>"Chuyên mục",
		"title"       =>$categories->title,
		"description" =>unserialize($categories->storage)["description"],
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>HOME."/posts-categories/".$categories->link??"",
		"robots"      =>"index,follow"
	]);

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
