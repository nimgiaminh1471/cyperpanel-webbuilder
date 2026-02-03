{{--Head--}}
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="language" content="Vietnamese" /><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>{{ $_Config['title'] }}</title>
{!!Assets::show("/assets/general/css/style__complete.css", "/assets/general/css/widgets/".Route::folder().".css", "/assets/library/jquery-3.3.1.min.js", "/assets/library/font-awesome-4.7.0/css/font-awesome.min.css")!!}
<link rel="shortcut icon" type="image/png" href="{{Storage::option("theme_header_favicon")}}" />
<link rel="icon" type="image/png" href="{{Storage::option("theme_header_favicon")}}" />
@yield("head-tag")
{!! htmlCustom('inner_header') !!}
</head>
<body>
{!! htmlCustom('outer_header') !!}
{{ deleteWebsiteExpired() }}
@php
	global $Schedule;
	$Schedule->d( function(){
		sendNotificationWebsiteExpired();
	});
@endphp
{{--Hiện ảnh loading--}}
@if($_Config['loading'])
<div id="loading" class="hidden">
	<div>
		<div>
			<i class="fa fa-spinner fa-pulse fa-4x fa-fw primary-color"></i>
		</div>
	</div>
</div>

@endif


@section("container")
@show

{!!Assets::show("/assets/general/js/script.js")!!}
@yield("script")
<style type="text/css">
	.admin-collapse{
		z-index: 199721 !important
	}
</style>
{{-- {!!OnlineChat::setup()!!} --}}


{!! htmlCustom('footer') !!}
{!!classes\FixedButton::show(false)!!}
{!!Assets::show()!!}

<section class="alert alert-success" id="alert-fixed" onclick="$(this).hide()"></section>
<script type="text/javascript">
	@if( !empty( SESSION('notify')['msg'] ) )
		showNotify("{{ SESSION('notify')['status'] }}", "{{ SESSION('notify')['msg'] }}");
		@php
			unset($_SESSION['notify']);
		@endphp
	@endif
	@if( !empty( user('notify_to_manager') ) )
		$.get('/user/sendNotifyToManager'); // Gửi thông báo tài khoản mới tới quản lý
	@endif
</script>
@section("footer")
@show
@php
if( permission("admin") ){
	//Ghi css 
	echo Widget::css("", true);
}
if( permission("member") && empty( user("check_admin") ) ){
	\models\Users::where("id", user("id") )->update(["check_admin"=>md5( randomString(20) )]); // Cập nhật key đăng nhập web con
}
@endphp
</body>
</html>