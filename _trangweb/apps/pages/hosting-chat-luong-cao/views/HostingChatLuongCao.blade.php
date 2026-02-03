@extends("Default")
@php
	define("PAGE", [
		"name"        =>"Hosting chất lượng",
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
	{{-- {!!
		headingBanner2([
			"main_title" => 'DỊCH VỤ HOSTING CHẤT LƯỢNG CAO CHỈ VỚI
55.000Đ / THÁNG
			',
			"main_advantages" => 'Tốc độ truy xuất vượt trội
An toàn, bảo mật dữ liệu
Khởi tạo nhanh, dễ dàng nâng cấp
Backup dữ liệu định kỳ
',
			"main_description" => 'Đăng ký ngay hosting cho thương hiệu của bạn chỉ với 1 click:',
			"button_label" => "Đăng ký ngay",
			"button_link" => 'javascript: showContactForm()',
			"heading_title"=> "Bảng giá hosting",
			"heading_description" => "Hosting là nơi để chứa nội dung mã lập trình cũng như dữ liệu của website. Mỗi website cần có hosting để có thể hoạt động. Hosting giá rẻ là giải pháp phù hợp cho các cá nhân hoặc doanh nghiệp muốn có một website giới thiệu, giao dịch thương mại trên Internet một cách hiệu quả và tiết kiệm chi phí.",
			"background_image" => 'http://htwebsite.com/template/frontend/resources/img/upload/hero-illustration-2.png'
		])
	!!} --}}
	@parent
	
@endsection


@section("script")
	
@endsection


@section("footer")
	@parent
@endsection
