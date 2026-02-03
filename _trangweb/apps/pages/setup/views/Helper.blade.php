@extends("Default")
@php
	define("PAGE", [
		"name"        =>"Setup",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"noindex,nofollow"
	]);
	require_once( Route::path("SeedsData.php") );//Tạo dữ liệu mẫu
@endphp





@section("main")







<main id="main">
	<section style="max-width:650px;margin: 0 auto">
		<div class="alert-success">Website đã được cài đặt, xóa hết bảng database để cài lại!</div>
		<div class="heading-simple">Tài liệu hướng dẫn</div>
			@php
require_once( Route::path("views/Document.php") );
Assets::footer("/assets/syntax-highlighter/prism.css", "/assets/syntax-highlighter/prism.js");

			@endphp
			@foreach($document as $title=>$body)
				<div class="panel panel-danger">
					<div class="heading link">{{$title}}</div>
					<div class="panel-body hidden">
						<pre class="language-php"><code><xmp>{!!trim($body)!!}</xmp></code></pre>
					</div>
				</div>
			@endforeach
	</section>
</main>

@endsection


@section("script")
@endsection

@section("footer")
@parent
@endsection
