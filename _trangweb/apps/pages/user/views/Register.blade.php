@extends("Default")
@php
	define("PAGE", [
		"name"        =>"Đăng ký tài khoản tạo website",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>Storage::setting("theme__general_background_image"),
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"noindex,nofollow"
	]);
	Assets::footer("/assets/account/register.js");
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
		<div class="modal-body" style="max-width: 450px">
			<div class="modal-content" style="background: transparent;">
				<div class="modal-form bg">
					<div class="heading">
						<div class="form-logo">
							<img src="{{ Storage::option("theme_header_favicon") }}">
						</div>
						ĐĂNG KÝ DÙNG THỬ
						<a href="/"><i class="modal-close fa"></i></a>
					</div>
					<form class="form pd-15" method="POST" action="" id="account-register-form">
						<div class="center" style="margin-bottom: 10px">Trải nghiệm toàn bộ tính năng web với 15 ngày miễn phí</div>
						<div class="modal-input">
							<input class="width-100 input" placeholder="Họ tên" type="text" name="register[name]" />
							<i class="fa fa-user"></i>
						</div>
						<div class="modal-input">
							<input class="width-100 input" placeholder="Số điện thoại" type="text" name="register[phone]"/>
							<i class="fa fa-phone"></i>
						</div>
						<div class="modal-input">
							<input class="width-100 input" placeholder="Email" type="email" name="register[email]"/>
							<i class="fa fa-envelope-o"></i>
						</div>
						<div class="modal-input">
							<input class="width-100 input" placeholder="Mật khẩu" type="password" name="register[password]"/>
							<i class="fa fa-lock"></i>
						</div>
						<div class="modal-input">
							<input class="width-100 input" placeholder="Nhập lại mật khẩu" type="password" id="account-register-password2"/>
							<i class="fa fa-lock"></i>
						</div>
						<div class="flex flex-middle">
							<div class="width-80">
								<input class="input width-100" name="register[captcha]" placeholder="Nhập mã xác minh" type="text"/>
							</div>
							<div class="width-20 center">
								<img id="account-register-captcha" src="/api/captcha.png?t={{time()}}" />
							</div>
						</div>
						<label class="check radio" style="margin-bottom: 5px">
							<input name="register[terms]" type="checkbox" value="1" checked="checked">
							<s></s>
							Tôi đồng ý với <a class="primary-color" target="_blank" href="/quy-dinh-su-dung">Quy định sử dụng</a> và <a class="primary-color" target="_blank" href="/chinh-sach-bao-mat">Chính sách bảo mật</a>
						</label>
						<div class="form-mrg hidden alert-danger" id="account-register-msg"></div>
						<div class="center">
							<input class="btn-primary width-100" id="account-register-submit" type="button" value="ĐĂNG KÝ"/>
						</div>
						<div class="pd-10 center">
							Đã có tài khoản ?
							<a style="margin-left: 10px" class="modal-click primary-color" href="/user/login">Đăng nhập</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection


@section("script")


</script>
@endsection


@section("footer")
@endsection
