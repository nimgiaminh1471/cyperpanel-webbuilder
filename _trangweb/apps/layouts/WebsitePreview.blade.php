@php
	ob_start();
	define("PAGE_OPTION", Storage::option( Route::folder() ));
	$pageConfig=PAGE;
	foreach($pageConfig as $key=>$value){
		if(empty($value)){
			$emptyValue=$key=="title" || $key=="description" ? PAGE["name"] : "";
			$value=PAGE_OPTION[$key]??$emptyValue;
			$pageConfig[$key]=$value;
		}
	}
	$pageConfig["title"]=cutWords($pageConfig["title"], 20).''.(GET("page")>1 ? ' '.__("- Trang").' '.GET("page").'' : '');
	$pageConfig["description"]=cutWords($pageConfig["description"], 35);
	$pageConfig["image"]=empty($pageConfig["image"]) ? HOME."".Storage::option("theme_header_logo") : $pageConfig["image"];
	if( Storage::option("website_robots", 1)==0 ){
		$pageConfig["robots"]="noindex,nofollow";
	}
	deleteWebsiteExpired();
@endphp
{{--Head--}}
<html xmlns="http://www.w3.org/1999/xhtml" lang="vi"{!!empty($pageConfig["background"]) ? '' : ' style="background-image: url('.$pageConfig["background"].')"'!!}>
<head>
	<meta name="language" content="Vietnamese" />
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>{!! $pageConfig["title"] !!}</title>
	<meta name="description" content="{!!$pageConfig["description"]!!}" />
	<meta property="og:title" content="{!!$pageConfig["title"]!!}"/>
	<meta property="og:description" content="{!!$pageConfig["description"]!!}" />
	<meta property="og:url" content="{!! empty($pageConfig["canonical"]) ? THIS_URL : $pageConfig["canonical"] !!}">
	<meta property="og:type" content="article">
	<meta property="og:image" content="{!!$pageConfig["image"]!!}" />
	{!! empty( Storage::option("facebook_appID") ) ? '' : '<meta property="fb:app_id" content="'.Storage::option("facebook_appID").'" />' !!}
	{!! empty($pageConfig["canonical"]) ? '' : '<link rel="canonical" href="'.$pageConfig["canonical"].'" />' !!}

	<meta name="robots" content="{{$pageConfig["robots"]??"index,follow"}}" />
	{!! Assets::show("/assets/general/css/style__complete.css", "/assets/library/jquery-3.3.1.min.js", "/assets/library/font-awesome-4.7.0/css/font-awesome.min.css") !!}
	<link rel="shortcut icon" type="image/png" href="{{Storage::option("theme_header_favicon")}}" />
	<link rel="icon" type="image/png" href="{{Storage::option("theme_header_favicon")}}" />
	@yield("head-tag")
</head>
<body style="overflow: hidden;">


{{--Hiện ảnh loading--}}
<div id="loading">
	<div>
		<div>
			<i class="fa fa-spinner fa-pulse fa-4x fa-fw primary-color"></i>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		setTimeout(function(){
			$("#loading").hide();
		}, 1e3);
	});
</script>

<script>
	@if( !empty( user('notify_to_manager') ) )
		$.get('/user/sendNotifyToManager'); // Gửi thông báo tài khoản mới tới quản lý
	@endif
</script>

{{-- Main --}}
@section("main")
@show



{!!Assets::show()!!}

@yield("script")

</body>
</html>