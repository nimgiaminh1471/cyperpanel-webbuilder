@extends("Default")
@php
	use models\BuilderDomain;
	define("PAGE", [
		"name"        => "Trang chủ",
		"title"       => "",
		"description" => "",
		"loading"     => 0,
		"background"  => "",
		"image"       => "",
		"canonical"   => HOME."/",
		"robots"      => "index,follow"
	]);
	$templateTitle = __('KHO GIAO DIỆN');
	$templateDescription = __('Bạn chưa có ý tưởng về giao diện website của mình? Không cần lo lắng, Kinhdoanhweb.net đã lọc chọn và đúc kết hàng trăm giao diện web mẫu cho bạn lựa chọn!');
@endphp

@section("head-tag")
	
@endsection

@section("main")
	@parent
	{{-- Danh sách web mẫu --}}
	<section style="margin-top: 20px">
		{!! view(
			"WebsiteTemplate",
			[
				"websiteTemplateSearch" => false,
				"templateTitle"         => $templateTitle,
				"templateDescription"   => $templateDescription
			],
			"website")
		!!}
	</section>
	@if( Storage::option('website_home_document', true) )
		{{-- Tài liệu hướng dẫn --}}
		<section id="website-document" style="padding: 30px 0 20px 0;box-shadow: 0 -5px 2px -5px rgba(0,0,0,0.24);">
			<div class="main-layout">
				<div class="center heading-basic">
					<h2>
						{!! __("TÀI LIỆU HƯỚNG DẪN") !!}
					</h2>
					<div>
						{!! __("Dù bạn là người tạo trang web lần đầu, thì việc tạo một website bán hàng hay website giới thiệu dịch vụ đều trở nên dễ dàng.") !!}
					</div>
				</div>
				<div class="flex flex-large">
					<div class="width-50 flex-margin play-tutorial-video link">
						{!! Widget::show("home_document_left") !!}
					</div>
					<div class="width-50 flex-margin">{!! Widget::show("home_document") !!}</div>
				</div>
				<div class="center" style="margin-top: 10px">
					<a href="/tai-lieu-huong-dan" class="btn-primary btn-circle">
						<i class="fa fa-plus"></i>
						Xem thêm tài liệu hướng dẫn
					</a>
				</div>
			</div>
		</section>
	@endif

	{!!Widget::show("home_footer")!!}
@endsection


@php
$style='
	.website-guest>div{
		padding: 30px 25px;
		text-align: center;
	}
	.website-guest>div>div{
		box-shadow: 0 5px 15px 0 rgba(82,156,219,.17);
		transition: .5s all
	}
	.website-guest>div>div:hover{
		transform: scale(1.1);
	}
	.play-tutorial-video:hover img{
		opacity: .8
	}

	@media(max-width: 767px) {
		.modal-send-request .modal-close{
			right: 5px !important
		}
		.website-terms-body ul{
			padding-left: 10px 
		}
	}

	@media(min-width: 768px) and (max-width: 1023px) {

	}
	';
	Widget::css($style);
@endphp

@section("script")
	
@endsection


@section("footer")
@parent
@endsection
