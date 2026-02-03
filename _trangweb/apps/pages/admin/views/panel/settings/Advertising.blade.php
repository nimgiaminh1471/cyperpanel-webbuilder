
{!!
OP("ads_popup_","Quảng cáo popup",[
	["html"=>
		Form::itemManager([
			"data"=>Storage::option("ads_popup"),
			"name"=>"storage[option][ads_popup]",
			"sortable"=>true,
			"max"=>20,
			"form"=>[
				["html"=>'<div class="alert-info">Lượt hiển thị: <b>[%s1]</b></div>', "name"=>"views", "value"=>"Chưa thống kê"],
				["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"Có thể bỏ trống", "value"=>"", "attr"=>''],
				["type"=>"file", "name"=>"src", "title"=>"Chọn ảnh hoặc video", "note"=>"Chưa chọn file", "value"=>"", "ext"=>"jpg,png,gif,jpeg,mp4", "post"=>0],
				["type"=>"number", "name"=>"timer", "title"=>"Hiện sau (s) giây", "note"=>"", "min"=>0, "max"=>9999, "value"=>"5","attr"=>''],
				["type"=>"number", "name"=>"countdown", "title"=>"Cho phép đóng sau (s) giây", "note"=>"", "min"=>0, "max"=>9999, "value"=>"5","attr"=>''],
				["type"=>"text", "name"=>"link", "title"=>"Liên kết", "note"=>"http://", "value"=>"", "attr"=>''],
				["type"=>"textarea", "name"=>"html", "title"=>"hoặc mã tùy chỉnh", "note"=>"Thẻ iframe Youtube hoặc mã HTML", "value"=>"", "attr"=>'', "full"=>true],
			]
		])
	],
	["type"=>"number", "name"=>"off", "title"=>"Hiện lại sau (h) tiếng <br/>[quảng cáo sẽ tự ẩn trong lần tải trang tiếp theo]", "note"=>"", "min"=>1, "max"=>999, "value"=>12,"attr"=>''],
])
!!}

{!!
OP("google_ads_","Mã chuyển đổi Google Ads",[
	["type"=>"text", "name"=>"conversion", "title"=>"Nhập mã", "note"=>"Dạng: AW-xxxxx/xxxxxxxxx", "value"=>"", "attr"=>'']
])
!!}