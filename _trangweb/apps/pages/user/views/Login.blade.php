@extends("Default")
@php
	define("PAGE", [
		"name"        =>"Tài khoản",
		"title"       =>"Login",
		"description" =>"",
		"loading"     =>0,
		"background"  =>Storage::setting("theme__general_background_image"),
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"noindex,nofollow"
	]);
@endphp

@section("header")
	@parent
	<style type="text/css">
		#header{
			background: transparent !important;
		}
		#header .header-right{
			display: none;
		}
	</style>
	{!!
		Assets::show("/assets/account/style.css")
	!!}
@endsection


@section("main")
<div class="modal account-outer">
	<div class="modal-body">
		<div class="modal-content" style="background: transparent;">
			<div class="modal-form bg">
				<div class="heading">
					<div class="form-logo">
						<img src="{{ Storage::option("theme_header_favicon") }}">
					</div>
					ĐĂNG NHẬP TÀI KHOẢN
					<a href="/"><i class="modal-close fa"></i></a>
				</div>
				<form class="form pd-15" method="POST" action="" id="account-register-form">
					<div class="modal-input">
						<i class="fa fa-user"></i>
						<input class="input input-circle" placeholder="Email hoặc số điện thoại" style="width:100%" type="text" name="username"/>
					</div>
					<div class="modal-input">
						<i class="fa fa-lock"></i>
						<input class="input input-circle" placeholder="Mật khẩu" style="width:100%" type="password" name="password"/>
					</div>
					<div style="padding: 10px 0;position: relative;">
						<label class="check radio">
							<input type="checkbox" checked>
							<s></s>
							Lưu đăng nhập
						</label>
						<a href="/user/forget" class="right-icon">
							<i class="fa fa-lock"></i>
							Quên mật khẩu?
						</a>
					</div>
					<div class="hidden alert-danger" id="loginMsg"></div>
					<div class="center">
						<input class="btn-primary input-circle width-100" id="loginSubmit" name="loginSubmit" type="button" value="ĐĂNG NHẬP" />
					</div>
					<div class="pd-10 center">
						Chưa có tài khoản ?
						<a style="margin-left: 10px" class="modal-click primary-color" href="/user/register">Đăng ký</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection


@section("script")
<script>
	//Đăng nhập
	function submit(){
		$("#loading").show();
		$.ajax({
			url: "",
			type: "POST",
			data: $("form").serialize()+"&loginSubmit",
			dataType: "json",
			success: function(data){
				if(data["success"]==1){
					$("#loginMsg").attr("class", "alert-success")
					location.href=data["redirect"];
				}
				$("#loginMsg").show().html(data["msg"]);
			},
			complete: function(){
				$("#loading").hide();
			},
			error: function(e){
				alert("Lỗi kết nối, vui lòng thử lại");
				console.log(e);
			}
		});
	}

	$("#loginSubmit").click(function(){
		submit();
	});
	$("form").on("keypress", function(e){
		if(e.keyCode==13){
			e.preventDefault();
			submit();
		}
	});
</script>
@endsection


@section("footer")
@endsection
