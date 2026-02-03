@extends("Default")
@php
	define("PAGE", [
		"name"        =>"Chứng chỉ SSL",
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
	<style type="text/css">
		table td,
		table th{
			padding: 15px !important;
		}
	</style>
@endsection


@section("script")
	
@endsection


@section("footer")
	@parent
@endsection
