@extends("WebsitePreview")
@php
	if( !permission("member") ){
		setcookie("continue_link", URI."?create", time() + 3600, "/");
	}
	use models\BuilderDomain;
	use models\Users;
	use mailer\WebMail;
	use classes\WebBuilder;

	$w = BuilderDomain::where("app", $template)->first();
	if( empty($w->domain) ){
		redirect("/");
	}
	define("PAGE", [
		"name"        =>"Website",
		"title"       =>"Mẫu web: ".$w->app_name??"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>THIS_LINK,
		"robots"      =>"noindex,nofollow"
	]);

@endphp

@section("header")

@endsection

@section("main")
<style type="text/css">
		#view-demo {
			height: 100%;
			width: 100%;
		}

		.demo-header {
			
		}
		.demo-header>div{
			max-width: 1200px;
			margin: auto
		}
		.demo-header a{
			color: #C0C0C0;
			padding: 5px;
			display: inline-block;
			transition: .5s all
		}
		.demo-header i{
			font-size: 25px;
			vertical-align: middle;
			margin: 0 4px;
		}
		.demo-header a:hover,
		.current{
			color: #EAEAEA !important;
		}
		.demo-header .link{
			color: white !important
		}
		.demo-header .link:hover{
			color: #23527c !important;
		}
		#demo-wrapper {
			position: relative;
			width: 100%;
			height: calc(100% - 40px);
			text-align: center;
			background: #ddd;
		}

		#demo-container {
			-webkit-transition-property: all;
			-moz-transition-property: all;
			transition-property: all;
			-webkit-transition-duration: 300ms;
			-moz-transition-duration: 300ms;
			transition-duration: 300ms;
			-webkit-transition-timing-function: cubic-bezier(0.605, 0.195, 0.175, 1);
			-moz-transition-timing-function: cubic-bezier(0.605, 0.195, 0.175, 1);
			transition-timing-function: cubic-bezier(0.605, 0.195, 0.175, 1);
			max-width: 100%;
			max-height: 100%;
			width: 100%;
			height: 100%;
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			overflow-y: auto;
			-webkit-overflow-scrolling: touch;
			background: #ddd;
		}

		#demo-container iframe {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			width: 100%;
			height: 100%;
			min-width: 100%;
			border: none;
			background: #fff;
		}
		.create-webiste-form input,
		.create-webiste-form button{
			border-radius: 20px !important;
			margin-top: 10px
		}
		@media (max-width: 1023px){
			.demo-header>div>.flex>div:first-child{
				width: 35%
			}
			.demo-header>div>.flex>div:last-child{
				width: 65%
			}
		}
		@media (max-width: 768px){
			.header-nav>div{
				width: 100% !important
			}
		}
	</style>
<main>
	<div id="view-demo">
		<div class="demo-header primary-bg">
			<div>
				<div class="flex flex-middle header-nav">
					<div class="width-30">
						<a class="link" {!! isset($_GET["create"]) ? 'href="/website"' : 'onclick="window.close()"' !!} style="color: white">
							<i class="fa fa-reply"></i>
							<span>Quay lại</span>
						</a>
					</div>
					<div class="width-40 hidden-small hidden-medium">
						<a class="type-item current pd-15" data-type="desktop">
							<i class="fa fa-desktop fa-icon"></i>
							<b>Desktop</b>
						</a>
						<a class="type-item pd-15 fa-icon" data-type="mobile">
							<i class="fa fa-mobile" style="font-size: 35px;"></i>
							<b>Mobile</b>
						</a>
						<a class="type-item pd-15 fa-icon" data-type="tablet">
							<i class="fa fa-tablet fa-rotate-270"></i>
							<b>Tablet</b>
						</a>
					</div>
					<div class="width-30 right hidden-small">
						<div class="flex flex-middle">
							<div style="width: calc(100% - 70px)">
								@if( permission('member') )
									<button class="btn-gradient modal-click pd-10" data-modal="create-website" style="border-radius: 30px; padding: 5px 20px">Sử dụng giao diện này</button>
								@else
									<a href="#" data-modal="register-notify" class="modal-click btn-gradient" style="border-radius: 30px; padding: 5px 20px">Sử dụng giao diện này</a>
								@endif
								
							</div>
							<div style="width: 70px">
								<a class="link" title="Ấn để xem trực tiếp" href="https://{{ $w->domain }}" style="padding-left: 25px; padding-right: 25px; margin-left: 5px">
									<i class="fa fa-times"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		{!! modalForm('Nhập thông tin website để khởi tạo', '
			<form class="form create-webiste-form" action="/admin/WebsiteManager" method="POST" style="padding: 20px">
				<div style="display:none">
					<input type="password" tabindex="-1"/>
					<input type="username" tabindex="-1"/>
				</div>
				<input type="hidden" name="template" value="'.$w->id.'" />
				<div class="flex flex-middle">
					<input type="text" class="input width-70 rm-radius" name="domain" placeholder="Nhập tên website của bạn. VD: websieudep" style="border-radius: 20px 0 0 20px !important" />
					<input type="text" class="input width-30 rm-radius" value=".'.DOMAIN.'" style="border-radius: 0 20px 20px 0 !important" readonly/>
				</div>
				<div class="pd-5">
					Sau khi tạo xong, bạn có thể đổi sang tên miền riêng tùy ý (Ví dụ: websieudep.vn)
				</div>
				<div>
					<input type="text" class="input width-100 rm-radius" name="user_login" placeholder="Email đăng nhập website" value="'.user("email").'" autocomplete="off" />
				</div>
				<div>
					<input type="password" class="input width-100 rm-radius" name="password" placeholder="Mật khẩu (Dùng để đăng nhập website đã tạo)" autocomplete="off" />
				</div>
				<div>
					<input type="password" class="input width-100 rm-radius" name="password2" placeholder="Nhập lại mật khẩu" autocomplete="off" />
				</div>
				<div class="hidden create-website-msg form-mrg"></div>
				<div class="center">
					<button type="button" class="btn-primary width-50">KHỞI TẠO WEBSITE</button>
				</div>
			</form>
			', 'create-website', '600px', false, true, true) !!}
		{!! modalForm('', '
			<div class="center" style="padding: 30px 10px; line-height: 1.5">
				<div class="pd-5">
					Quý khách chưa đăng ký tài khoản, vui lòng đăng ký và đăng nhập tài khoản để có thể tạo trang web!
				</div>
				<div class="pd-5">
					<a style="border-radius: 30px; padding: 10px 20px" href="/user/register" class="btn btn-primary">Click để đăng ký tài khoản</a>
				</div>
			</div>
		', 'register-notify', '600px', false, true, true) !!}
		<div id="demo-wrapper" class="desktop-view">
			<div id="demo-container">
				<iframe id="frame" src="{{($w->ssl_type == 0 ? 'http://' : 'https://')}}{{$w->domain}}"></iframe>     
			</div>
		</div>
	</div>


	</div>


	<script type="text/javascript">
		@if( isset($_GET["create"]) )
			$(document).ready(function(){
				$(".modal-create-website").show();
			});
		@endif
		var url_demo = $("#frame").attr("src");
		$(document).on("click", ".type-item", function (event) {
			$('#demo-container').html('');

			$('.type-item').removeClass('current');
			$(this).addClass('current');

			var type = $(this).attr('data-type');
			var url_iframe = url_demo;
			switch (type) {
				case 'desktop':
					$("#demo-container").attr('style', 'max-width: 100%; max-height: 100%; margin: 0px; top: 0px; left: 0px;');
				break;
				case 'tablet':
					$("#demo-container").attr('style', 'max-width: 849px; max-height: 568px; margin:5% auto;');
				break;
				case 'mobile':
					$("#demo-container").attr('style', 'max-width: 375px; max-height: 568px; margin:5% auto;');
				break;
				
				default:
					$("#demo-container").attr('style', 'max-width: 100%; max-height: 100%; margin: 0px; top: 0px; left: 0px;');
			}

			// reload iframe
			$('#demo-container').html('<iframe id="frame" src="' + url_iframe + '"></iframe>');

		});
		//Click tạo website
		function createWebsiteSubmit(el){
			var form=$(el).parents("form");
			form.find("button").hide();
			form.find(".create-website-msg").addClass("alert-info").removeClass("alert-danger").html("Đang tiến hành đăng ký website...").show();
			$.ajax({
				"url"  : "/admin/WebsiteManager",
				"data" : "create=1&"+form.serialize(),
				"type" : "POST",
				success: function(response){
					var msg=$(response).find("#create-website-error").html();
					if( $(response).find(".error-message").length > 0 ){
						var msg = $(response).find(".error-message").text();
					}
					if(typeof msg=="undefined"){
						location.href="/admin/WebsiteManager?id="+form.find("input[name='domain']").val()+".{{ DOMAIN }}";
					}else{
						form.find(".create-website-msg").addClass("alert-danger").removeClass("alert-info").html(msg).show();
						form.find("button").show();
					}
				},
				complete: function(){
					//form.find("input[name='domain']").focus();
				},
				error: function(error){
					$("#loading").hide();
					form.find("button").show();
					var msg = "Lỗi kết nối, vui lòng ấn lại lần nữa";
					form.find(".create-website-msg").addClass("alert-danger").removeClass("alert-info").html(msg).show();
					alert(msg);
				}
			});
		}
		$(".create-webiste-form").on("click", "button", function(){
			createWebsiteSubmit(this);
		});
		$(".create-webiste-form").on("keyup", "input", function(e){
			if(e.keyCode==13){
				createWebsiteSubmit(this);
			}
		});
	</script>
	
</main>
@endsection

@section("script")
@endsection

@section("footer")
@endsection