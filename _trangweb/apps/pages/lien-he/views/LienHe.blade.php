@extends("Default")
@php
	define("PAGE", [
		"name"        =>"Tên trang",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"index,follow"
	]);
@endphp


@section("main")
	@parent
	<section class="main-layout modal-form" id="contact-wrap">
		@if( !permission("member") )
			<div class="pd-10">
				<h1>Liên hệ qua email</h1>
				<div style="margin-top: 5px">Mọi thắc mắc của quý khách hàng hãy liên hệ với chúng tôi theo form dưới đây:</div>
			</div>
			<form method="POST" id="contact-form-custom">
				<div class="flex flex-large">
					<div class="width-50 pd-10">
						<div class="contact-input modal-input">
							<i class="fa fa-user"></i>
							<input type="text" class="input width-100" name="contact[name]" placeholder="Họ và tên">
						</div>
						<div class="contact-input modal-input">
							<i class="fa fa-envelope-o"></i>
							<input type="text" class="input width-100" name="contact[email]" placeholder="Địa chỉ email">
						</div>
						<div class="contact-input modal-input">
							<i class="fa fa-phone"></i>
							<input type="text" class="input width-100" name="contact[phone]" placeholder="Số điện thoại">
						</div>
					</div>
					<div class="width-50 pd-10">
						<div class="contact-input">
							<textarea name="contact[message]" class="width-100" placeholder="Nội dung..." rows="8"></textarea>
						</div>
					</div>
				</div>
				<div class="contact-notify alert-danger center hidden">
					
				</div>
				<div class="contact-input center">
					<button type="button" class="btn btn-info" style="font-size: 20px; padding: 12px 30px; border-radius: 40px !important">Gửi liên hệ cho chúng tôi</button>
				</div>
			</form>
		@endif
	</section>
	{!! Widget::show("contact_footer") !!}
@endsection


@section("script")
	<script type="text/javascript">
		$("#contact-form-custom").on("click", "button", function(){
			$('#loading').show();
			var form = $(this).parents("form");
			form.find("button").hide();
			$.ajax({ 
				type: "POST", 
				url: "", 
				data: form.serialize(), 
				dataType: "json",
				success: function(data) { 
					if(data.error.length == 0){
						form.find(".contact-notify").removeClass("alert-danger").addClass("alert-success").html("Gửi liên hệ thành công, chúng tôi sẽ sớm phản hồi lại cho quý khách, xin cảm ơn!").show();
						form.find(".flex").hide();
						form.find("button").hide();
						$.get('/user/sendNotifyToManager'); // Gửi thông báo tài khoản mới tới quản lý
					}else{
						form.find(".contact-notify").html(data.error).show();
						form.find("button").show();
					}
					console.log(data);
				},
				complete: function(){
					$('#loading').hide();
				},
				error: function(err){
					form.find("button").show();
					console.log(err);
					alert("Lỗi kết nối, xin vui lòng thử lại");
				}
			});
		});
	</script>
	<style type="text/css">
		#contact-wrap{
			background: white !important;
			padding: 20px;
			border-radius: 0 !important
		}
		#contact-form .contact-input{
			
		}
		#contact-form input{
			border-radius: 20px
		}
		#contact-form textarea{
			border-radius: 10px !important
		}
		.fx-btn-blick {
			position: relative;
			overflow: hidden;
		}
		.fx-btn-blick:before {
			content: "";
			background-color: #fff;
			height: 100%;
			width: 3em;
			display: block;
			position: absolute;
			top: 0;
			-webkit-transform: skewX(-45deg) translateX(0);
			transform: skewX(-45deg) translateX(0);
			-webkit-transition: none;
			transition: none;
			opacity: 0;
			-webkit-animation: left-slide 2s infinite;
			animation: left-slide 2s infinite;
		}
		@keyframes left-slide {
			0% {
				left: -50%;
				opacity: 0.1;
			}
			50%,
			100% {
				left: 150%;
				opacity: 0.75;
			}
		}
	</style>
@endsection


@section("footer")
	@parent
@endsection
