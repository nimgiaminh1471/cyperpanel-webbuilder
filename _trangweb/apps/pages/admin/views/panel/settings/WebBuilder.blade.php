{!!
ST("builder_parameters_", "Thiết lập thông số", [
	["type"=>"number", "name"=>"disk", "title"=>"Dung lượng cho web mới tạo(Mb)", "note"=>"", "min"=>50, "max"=>99999, "value"=>100,"attr"=>''],
	["type"=>"number", "name"=>"expired", "title"=>"Cho phép dùng thử(ngày)", "note"=>"", "min"=>1, "max"=>99999, "value"=>3,"attr"=>''],
	["type"=>"number", "name"=>"expired_delete_delay", "title"=>"Xóa web hết hạn sau(ngày)", "note"=>"", "min"=>1, "max"=>99999, "value"=>3,"attr"=>''],
	["type"=>"number", "name"=>"quantity_website", "title"=>"Số web tối đa/user", "note"=>"", "min"=>1, "max"=>99999, "value"=>5,"attr"=>''],
	["type"=>"number", "name"=>"delay_create", "title"=>"Số giây mỗi cho phép tạo web tiếp", "note"=>"", "min"=>1, "max"=>99999, "value"=>60,"attr"=>''],
	["type"=>"currency", "name"=>"ssl_price", "title"=>"Giá tiền nâng cấp SSL", "note"=>"", "min"=>0, "max"=>99999, "value"=>"100,000","attr"=>''],
	["type"=>"currency", "name"=>"backup_price", "title"=>"Giá tiền tải code", "note"=>"", "min"=>0, "max"=>99999, "value"=>"500,000","attr"=>''],
	["type"=>"currency", "name"=>"upgrade_disk_price", "title"=>"Giá tiền/Mb dung lượng mua thêm", "note"=>"", "min"=>0, "max"=>99999, "value"=>"2,000","attr"=>''],
])
!!}

{!!
ST("builder_categories_", "Thể loại web", [
	["html"=>
		Form::itemManager([
			"data"=>Storage::builder("categories"),
			"name"=>"storage[builder][categories]",
			"sortable"=>true,
			"max"=>20,
			"form"=>[
				["type"=>"text", "name"=>"name", "title"=>"Tên danh mục", "note"=>"VD: Bán hàng", "value"=>"", "attr"=>''],
				["type"=>"icon", "name"=>"icon", "title"=>"Icon", "value"=>""],
				["type"=>"color", "name"=>"color1", "title"=>"Màu 1", "default"=>"#FF00CC", "value"=>"#FF00FF", "required"=>true],
				["type"=>"color", "name"=>"color2", "title"=>"Màu 2", "default"=>"#FF00CC", "value"=>"#FF00FF", "required"=>true],
				["type"=>"text", "name"=>"title", "title"=>"Tiêu đề SEO", "note"=>"VD:Các mẫu web bán hàng", "value"=>"", "attr"=>''],
				["type"=>"textarea", "name"=>"description", "title"=>"Giới thiệu", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
			],
			"setKeyFromName"=>true
		])
	],
	[
		"type"   => "select",
		"name"   => "style",
		"title"  => "Giao diện",
		"option" => [
			"1" => "Kiểu 1",
			"2" => "Kiểu 2",
		],
		"value" => "1"
	],
	["type"=>"switch", "name"=>"promotion", "title"=>"Hiện mục yêu cầu tư vấn", "value"=>0],
])
!!}

@php
	$roles = models\Users::role();
@endphp

{!!
ST("builder_package_", "Gói gia hạn", [
	["html"=>
		Form::itemManager([
			"data"=>Storage::builder("package"),
			"name"=>"storage[builder][package]",
			"sortable"=>true,
			"max"=>20,
			"form"=>[
				["type"=>"text", "name"=>"name", "title"=>"Tên gói", "note"=>"VD: Gói 1", "value"=>"", "attr"=>''],
				["type"=>"currency", "name"=>"price", "title"=>"Giá tiền / năm", "note"=>"", "value"=>"", "attr"=>''],
				["type"=>"number", "name"=>"disk", "title"=>"Dung lượng", "note"=>"", "value"=>"", "attr"=>'', "min"=>150, "max"=>99999999999],
				["type"=>"textarea", "name"=>"description", "title"=>"Mô tả gói", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
				["type"=>"checkbox", "name"=>"roles", "title"=>"Dành cho", "checkbox"=>$roles, "value"=>["c1"] ],
				["type"=>"color", "name"=>"background_color1", "title"=>"Màu nền tên 1", "default"=>"#FF00CC", "value"=>"", "required"=>true],
				["type"=>"color", "name"=>"background_color2", "title"=>"Màu nền tên 2", "default"=>"#FF00CC", "value"=>"", "required"=>true],
				["type"=>"switch", "name"=>"suggest", "title"=>"Nổi bật", "value"=>0],
				["type"=>"text", "name"=>"label", "title"=>"Nhãn gói", "note"=>"VD: Không phát sinh thêm chi phí", "value"=>"", "attr"=>''],
				["type"=>"textarea", "name"=>"include", "title"=>"Bao gồm", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
				["type"=>"textarea", "name"=>"exclude", "title"=>"Không bao gồm", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
			],

		])
	],
	["type"=>"switch", "name"=>"price_renew_monthly", "title"=>"Hiện giá gói theo tháng", "value"=>0],
])
!!}


{!!
ST("builder_expiry_date_", "Số ngày gia hạn", [
	["html"=>
		Form::itemManager([
			"data"=>Storage::builder("expiry_date"),
			"name"=>"storage[builder][expiry_date]",
			"sortable"=>true,
			"max"=>20,
			"form"=>[
				["type"=>"text", "name"=>"name", "title"=>"Chữ hiển thị", "note"=>"VD: Gói 1", "value"=>"", "attr"=>''],
				["type"=>"number", "name"=>"days", "title"=>"Số ngày", "note"=>"", "value"=>"", "attr"=>'', "min"=>1, "max"=>99999999999],
				["type"=>"number", "name"=>"sale_offer", "title"=>"Giảm giá %", "note"=>"", "value"=>"", "attr"=>'', "min"=>0, "max"=>90],
				["type"=>"checkbox", "name"=>"roles", "title"=>"Giảm giá cho", "checkbox"=>$roles, "value"=>["c1"] ],
			],
			"setKeyFromName"=>true
		])
	],
	["type"=>"number", "name"=>"days", "title"=>"Số ngày trong danh sách web sắp hết hạn", "note"=>"", "min"=>1, "max"=>90, "value"=>15,"attr"=>''],
])
!!}


{!!
ST("builder_discount_", "Chiết khấu", [
	["type"=>"number", "name"=>"seller", "title"=>"% chiết khấu cho cộng tác viên", "note"=>"", "min"=>5, "max"=>90, "value"=>20,"attr"=>''],
	["type"=>"number", "name"=>"agency", "title"=>"% chiết khấu cho đại lý", "note"=>"", "min"=>10, "max"=>90, "value"=>40,"attr"=>''],
])
!!}


{!!
ST("builder_ssl_", "Chứng chỉ SSL tên miền *.".DOMAIN, [
	["type"=>"textarea", "name"=>"private_key", "title"=>"Private key", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
	["type"=>"textarea", "name"=>"certificate", "title"=>"Certificate", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
	["type"=>"textarea", "name"=>"cacert", "title"=>"CA Certificate", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
])
!!}

{!!
ST("mail_notification_", "Email thông báo", [
	["type"=>"editor", "name"=>"expired_30", "title"=>'
	<b>Thông báo website sắp hết hạn</b>
	<div>
	<textarea rows="5" style="width: 100%">
${domain} => Tên miền
${user_name} => Tên khách hàng
${user_email} => Email KH
${user_phone} => SĐT KH
${expired_date} => Ngày hết hạn
${renew_price} => Phí gia hạn
${package} => Tên gói
	</textarea>
	</div>
	', "value"=>""],
])
!!}

{!!
ST("builder_setting_", "Thiết lập khác", [
	["type"=>"image", "name"=>"setup_image", "title"=>"Icon loading khởi tạo website", "value"=>"", "post"=>0],
	["type"=>"switch", "name"=>"show_price", "title"=>"Hiển thị giá website", "value"=>0],
])
!!}