@extends("Default")
@php
	$title = WEB_BUILDER["categories"][$categories]["title"] ?? "KHO GIAO DIỆN";
	$description = WEB_BUILDER["categories"][$categories]["description"] ?? "Hãy chọn cho mình một mẫu website ưng ý nhé!";
	define("PAGE", [
		"name"        =>"Website",
		"title"       =>$title,
		"description" =>$description,
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>THIS_LINK,
		"robots"      =>"index,follow"
	]);

	use models\BuilderDomain;
	use models\Users;
	use mailer\WebMail;
	use classes\WebBuilder;
@endphp


@section("main")
{!!Widget::show("website_header")!!}
{{-- Các mẫu website --}}


<div>
	{!! view("WebsiteTemplate", ["templateTitle" => $title, "websiteTemplateSearch" => true, "templateLink" => true, "categories" => $categories], "website") !!}
</div>
@endsection



@section("script")
	
@endsection


@section("footer")
	@parent
@endsection
