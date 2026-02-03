@extends("Default")
@php
	define("PAGE", [
		"name"        =>"mua theme wordpress đẹp",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"index,follow"
	]);
	Assets::footer(
		"https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css",
		"https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"
	);
@endphp


@section("main")
	@parent
	<section style="margin-top: 20px">
		{!! view(
			"WebsiteTemplate",
			[
				"websiteTemplateSearch" => true,
				"templateTitle"         => 'Danh sách theme',
				"templateDescription"   => '',
				'disablePromotion'      => true
			],
			"website")
		!!}
	</section>
	{!!Widget::show("theme_footer")!!}
@endsection


@section("script")
	<style type="text/css">
		.service-button>a>svg{
			display: none;
		}
		#header{
			z-index: 12 !important
		}
	</style>
	<script>
		$(document).ready(function() {
			$(".service-button>.btn-gradient").fancybox({
				openEffect  : "none",
				closeEffect : "none",
				helpers : {
					media : {}
				}
			});
		});
	</script>
@endsection


@section("footer")
	@parent
@endsection