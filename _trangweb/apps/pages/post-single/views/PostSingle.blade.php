@extends("Default")
@php
	define("WIDGET_DATA", ["post"=>(array)$post]);
	define("PAGE", [
		"name"        => "Trang bài viết",
		"title"       => $post->title,
		"description" => $post->storage["description"]??$post->content,
		"loading"     => 0,
		"background"  => null,
		"image"       => empty($post->storage["poster"]["original"]) ? "" : HOME."".$post->storage["poster"]["original"],
		"canonical"   => HOME."/".$post->link,
		"robots"      => "index,follow"
	]);
@endphp

@section("main")
	@parent
@endsection

@section("ads")
	{{-- Quảng cáo bài viết --}}
	@if(!empty($post->storage["ads_popup"]))
		<script src="/assets/general/js/popup-ads.js"></script>
		<script>
			popupAds({!!json_encode( $post->storage["ads_popup"] )!!}, {!!$post->id!!});
		</script>
	@else
		@parent
	@endif
	
@endsection


@section("footer")
@parent
@endsection
