@extends("Default")
@php
	define("PAGE", [
		"name"        =>"Blog",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"index,follow"
	]);
	Assets::footer("/assets/general/js/blog.js");
@endphp


@section("main")
	{!! Widget::show("blog_header") !!}
	<div class="blog-navbar-wrap bg">
		<section class="main-layout">
			<div class="flex flex-middle">
				<div class="width-70">
					<div data-show="blog_navbar" class="hidden-large blog-nav-icon-mobile">
						<i class="fa fa-bars"></i> Danh mục
					</div>
					{!! createBlogNavbar( Storage::option("blog_navbar", []), false, "flex flex-middle") !!}
				</div>
				<div class="width-30 right">
					<div class="width-100">
						<form medthod="GET" action="/search" class="input-search">
							<input placeholder="{{__("Tìm kiếm")}}" class="input" type="search" name="keyword" value="{{GET("keyword")}}" required="" style="width: 100%">
							<button type="submit"><i class="fa fa-search"></i></button>
						</form>
					</div>
				</div>
			</div>
		</section>
	</div>
	@parent
@endsection


@section("script")
	<style>
		html{
			background: #EAEAEA !important
		}
	</style>
@endsection


@section("footer")
	@parent
@endsection
