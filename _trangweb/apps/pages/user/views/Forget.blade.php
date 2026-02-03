@extends("Default")
@php
	define("PAGE", [
		"name"        =>"Tài khoản",
		"title"       =>"Quên mật khẩu",
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
				LẤY LẠI MẬT KHẨU
				<i class="modal-close link fa"></i>
			</div>
			<div class="hidden alert-danger" id="msg"></div>
			<form class="pd-15" action="" method="POST">
				<div class="boxSendMail form-mrg">
					<div class="modal-input">
						<i class="fa fa-envelope-open-o"></i>
						<input class="input" placeholder="Nhập email" style="width:100%" type="text" name="email"/>
					</div>
				</div>
				<div class="hidden" id="boxChangePassword">
					<div class="modal-input">
						<i class="fa fa-key"></i>
						<input class="input" placeholder="Mã đổi mật khẩu" style="width:100%" type="number" name="forget_key"/>
					</div>
					<div class="modal-input">
						<i class="fa fa-lock"></i>
						<input class="input" placeholder="Mật khẩu mới" style="width:100%" type="password" name="password"/>
					</div>
					<div class="modal-input">
						<i class="fa fa-lock"></i>
						<input class="input" placeholder="Nhập lại mật khẩu" style="width:100%" type="password" name="rePassword"/>
					</div>
				</div>
				<div id="captcha-outer" class="flex flex-middle form-mrg">
					<div class="width-80">
						<input class="input width-100" name="captcha" placeholder="Nhập mã xác minh" type="text"/>
					</div>
					<div class="width-20 center">
						<img id="captcha" src="/api/captcha.png?t={{time()}}" />
					</div>
				</div>
				<div class="form-mrg"><input class="btn-primary" style="width:100%" id="passwordForgetSubmit" type="button" value="GỬI YÊU CẦU"/></div>
				<div class="pd-10" style="position: relative;">
					<a  href="login{!! (empty($_GET["continue"]) ? '' : '?continue='.$_GET["continue"]) !!}"><i class="fa fa-sign-in"></i> Đăng nhập</a>
					<a class="right-icon" href="/"><i class="fa fa-home"></i> Trang chủ</a>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection


@section("script")
<script>
	//Quên mật khẩu
	function fsubmit(){
		$("#loading").show();
		$.ajax({
			url: "",
			type: "POST",
			data: $("form").serialize()+"&passwordForgetSubmit",
			dataType: "json",
			success: function(data){
				if(data["msg"]=="changed"){
					alert("Đổi mật khẩu thành công");
					location.href="login";
				}else if(data["msg"]=="sent"){
					$(".boxSendMail input").attr("readonly","readonly");
					$("#boxChangePassword").show();
					$("#passwordForgetSubmit").val("Đổi mật khẩu");
					$("#msg").show().html("Vui lòng nhập mã đổi mật khẩu trong email<br/>Hãy kiểm tra cả trong hộp thư spam");
					$("#captcha-outer").hide();
				}else{
					$("#msg").show().html(data["msg"]);
				}
				$("#captcha").attr("src", $("#captcha").attr("src").split("?")[0]+"?t="+(new Date().getTime()) );
				$("input[name='captcha']").val("");
			},
			error: function(error){
				alert("Lỗi kết nối, vui lòng thử lại");
				console.log(error)
			},
			complete: function(){
				$("#loading").hide();
			}
		});
	}
	$("#passwordForgetSubmit").click(function(e){
		fsubmit();
	});
	$("form").on("keypress", function(e){
		if(e.keyCode==13){
			e.preventDefault();
			fsubmit();
		}
	});
</script>
@endsection


@section("footer")
@endsection
