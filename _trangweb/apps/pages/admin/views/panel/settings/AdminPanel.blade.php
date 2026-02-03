{{-- Thiết lập trang Admin Panel --}}
@php
	use models\Users;
@endphp


{!! 
ST("panel_icon_","Chọn icon",call_user_func(function($n,$out=[]){ 
if(empty($n)){
	$out='<script>location.reload();</script>';
}else{
	foreach($n as $k=>$v) {
		if(!empty($v["title"])){
			$out[]=["type"=>"icon","name"=>$k,"title"=>$v["title"],"value"=>""];
		}
	}
}
return $out;
},Storage::panel_info()), "500")
!!}




{!! 
ST("admin_panel_","Tùy chỉnh giao diện",[
	["type"=>"image", "name"=>"logo", "title"=>"Logo","ext"=>"jpg,jpeg,png,gif", "post"=>0],
	["type"=>"number", "name"=>"css_menu_font_size", "title"=>"Cỡ chữ link menu", "note"=>"", "min"=>0, "max"=>9999, "value"=>"14","attr"=>''],
	["type"=>"text", "name"=>"css_menu_padding", "title"=>"Padding của link menu", "note"=>"", "value"=>"10px"],
	["type"=>"color", "name"=>"css_background", "title"=>"Màu nền chính", "note"=>"", "default"=>"#f1f2f7", "required"=>true],
	["type"=>"select", "name"=>"theme", "title"=>"Giao diện", "option"=>["light"=>"Sáng", "dark"=>"Tối"], "value"=>"light", 'attr' => 'onchange="setTimeout(function(){ location.reload() }, 1e3);"'],
	["type"=>"color", "name"=>"css_actived_background", "title"=>"Màu nền actived", "note"=>"", "default"=>"#2196f3", "required"=>true],

])
!!}


{!! 
ST("mailer_","Cấu hình gửi email",[
	["html"=>'
		<div class="alert-danger">
		Nếu Gmail không chính xác thì sẽ không thể gửi mail đến khách hàng.<br/>
		Các chức năng sẽ không hoạt động: lấy lại mật khẩu...<br/>
		Nên dùng Gmail riêng(Không dùng mail chính)
		</div>
	'],
	["type"=>"text", "name"=>"EmailName", "title"=>"Tên người gửi", "note"=>"", "value"=>""],
	["type"=>"text", "name"=>"EmailAddress", "title"=>"Địa chỉ Gmail", "note"=>"", "value"=>""],
	["type"=>"password", "name"=>"EmailPassword", "title"=>"Mật khẩu Gmail", "note"=>"", "value"=>""],
	["html"=>'
		<div class="alert-warning">
			<b>Lần đầu tiên thêm Gmail, cần thao tác các bước sau:</b><br/>
			<a target="_blank" href="https://myaccount.google.com/lesssecureapps">1. Bật Allow less secure apps</a><br/>
			<a target="_blank" href="https://accounts.google.com/DisplayUnlockCaptcha">2. Bật Display Unlock Captcha</a><br/>
			<a target="_blank" href="https://myaccount.google.com/signinoptions/two-step-verification/enroll-welcome?pli=1">3. Tắt xác minh 2 bước (nếu đã bật)</a>
		</div>
	'],
	["type"=>"textarea", "name"=>"footer", "title"=>"Nội dung dưới chân mail", "note"=>"Hỗ trợ HTML", "value"=>"", "attr"=>'', "full"=>true],
], 500)
!!}


{{-- <div class="pd-10 menu" style="margin-bottom: 20px"><button type="button" class="btn-danger" id="settingsRestore">Khôi phục cài đặt</button></div> --}}
{!!THEME_INFO!!}






<div class="panel panel-default" style="margin-top: 20px">
	<div class="heading link">Demo</div>
	<div class="panel-body hidden"> 
{!! 
ST("","Demo",[

	//Chọn màu
	["type"=>"color", "name"=>"id_1", "title"=>"Color", "default"=>"#FF00CC", "value"=>"#FF00FF", "required"=>true],

	//Chọn file
	["type"=>"file", "name"=>"id_2", "title"=>"File", "note"=>"Ghi chú", "value"=>"", "ext"=>"jpg,png,gif,jpeg", "post"=>0],

	//Chọn ảnh
	["type"=>"image", "name"=>"img_1", "title"=>"Image", "value"=>"", "post"=>0],

	//Chọn icon
	["type"=>"icon", "name"=>"id_3", "title"=>"Icon", "value"=>""],

	//Switch
	["type"=>"switch", "name"=>"id_4", "title"=>"Switch", "value"=>1],

	//Checkbox
	["type"=>"checkbox", "name"=>"id_5", "title"=>"Checkbox", "checkbox"=>["c1"=>"Check 1", "c2"=>"Check 2", "c3"=>"Check 3"], "value"=>["c1","c3"] ],

	//Checkbox
	["type"=>"radio", "name"=>"id_6", "title"=>"Radio", "radio"=>["r1"=>"Check 1", "r2"=>"Check 2", "r3"=>"Check 3"], "value"=>"r2"],

	//Select
	["type"=>"select", "name"=>"id_7", "title"=>"Select", "option"=>["o1"=>"Option 1", "o2"=>"Option 2","o3"=>"Option 3"], "value"=>"o2"],

	//Multiple select
	["type"=>"multipleSelect", "name"=>"id_8", "title"=>"Multiple select", "option"=>["o1"=>"Option 1", "o2"=>"Option 2","o3"=>"Option 3"], "value"=>["o1","o3"] ],

	//Input text
	["type"=>"text", "name"=>"id_9", "title"=>"Text", "note"=>"Ghi chú", "value"=>"", "attr"=>''],

	//Input number
	["type"=>"number", "name"=>"id_10", "title"=>"Number", "note"=>"Ghi chú", "min"=>0, "max"=>9999, "value"=>"20","attr"=>''],

	//Input date
	["type"=>"date", "name"=>"id_date0", "title"=>"Chọn ngày đầy đủ", "note"=>"Ấn để chọn ngày", "value"=>"", "attr"=>'', "position"=>"bottom",
		"format"=>"hour day/month/year",
		"config"=>[
			"allow"=>[
				"hours"=>[ [6,11], [13,17], [20,23] ], "minutes"=>"5-50", "requiredHour"=>true,
				"days"=>"1-31", "months"=>"1-12", "weekDay"=>[],
				"min"=>["y"=>1997, "m"=>2, "d"=>14], "max"=>["y"=>2020, "m"=>3, "d"=>15]
			],
			"value"=>["day"=>date("d"), "month"=>date("m"), "year"=>date("Y") ]
		]
	],
	["type"=>"date", "name"=>"id_date1", "title"=>"Chọn ngày", "note"=>"Ấn để chọn ngày", "value"=>"", "attr"=>'', "position"=>"bottom",
		"format"=>"day/month/year",
		"config"=>[
			"allow"=>[
				"hours"=>[], "minutes"=>"", "requiredHour"=>false,
				"days"=>[1,3,5,14], "months"=>[3,5,7], "weekDay"=>["mon", "tue", "wed", "thu", "fri"],
				"min"=>["y"=>2019, "m"=>2, "d"=>14], "max"=>["y"=>2023, "m"=>2, "d"=>14]
			],
			"value"=>["day"=>date("d"), "month"=>date("m"), "year"=>date("Y")]
		]
	],

	//Textarea
	["type"=>"textarea", "name"=>"id_11", "title"=>"Textarea", "note"=>"Ghi chú", "value"=>"", "attr"=>'', "full"=>true],

	//Editor
	["type"=>"editor", "name"=>"id_13x", "title"=>"Trình soạn thảo HTML", "value"=>"Nội dung mặc định"],

	//Sắp xếp - OP
	["type"=>"sort", "name"=>"sort_1", "title"=>"Sắp xếp", "value"=>["i1"=>"Item 1", "i2"=>"Item 2","i3"=>"Item 3","i4"=>"Item 4","i11"=>"Item 1", "i12"=>"Item 2","i13"=>"Item 3","i14"=>"Item 4","i21"=>"Item 1", "i22"=>"Item 2","i23"=>"Item 3","i24"=>"Item 4"], "edit"=>true],

	//Sắp xếp nhiều - OP
	["type"=>"multipleSort", "name"=>"sort_3", "title"=>"Sắp xếp nhiều mục", "value"=>[
		["title"=>"Tiêu đề 1", "item"=>array("i1"=>"Item 1 - 1", "i2"=>"Item 1 - 2", "i3"=>"Item 1 - 3")],
		["title"=>"Tiêu đề 2", "item"=>array("i1"=>"Item 2 - 1 ", "i2"=>"Item 2 - 2")],
		["title"=>"Tiêu đề 3", "item"=>array("i1"=>"Item 3 - 1", "i2"=>"Item 3 - 2", "i3"=>"Item 3 -3")],
	], "edit"=>true],


])
!!}




{!! 
ST("test_id","Tiêu đề ẩn",[
	["html"=>'Nội dung bị ẩn']
],"500")
!!}













<textarea class="width-100" rows="15">
echo ST("","Demo",[
	ST("","Demo",[

	//Chọn màu
	["type"=>"color", "name"=>"id_1", "title"=>"Color", "default"=>"#FF00CC", "value"=>"#FF00FF", "required"=>true],

	//Chọn file
	["type"=>"file", "name"=>"id_2", "title"=>"File", "note"=>"Ghi chú", "value"=>"", "ext"=>"jpg,png,gif,jpeg" ,"post"=>0],

	//Chọn ảnh
	["type"=>"image", "name"=>"img_1", "title"=>"Image", "value"=>"", "post"=>0],

	//Chọn icon
	["type"=>"icon", "name"=>"id_3", "title"=>"Icon", "value"=>""],

	//Switch
	["type"=>"switch", "name"=>"id_4", "title"=>"Switch", "value"=>1],

	//Checkbox
	["type"=>"checkbox", "name"=>"id_5", "title"=>"Checkbox", "checkbox"=>["c1"=>"Check 1", "c2"=>"Check 2", "c3"=>"Check 3"], "value"=>["c1","c3"] ],

	//Checkbox
	["type"=>"radio", "name"=>"id_6", "title"=>"Radio", "radio"=>["r1"=>"Check 1", "r2"=>"Check 2", "r3"=>"Check 3"], "value"=>"r2"],

	//Select
	["type"=>"select", "name"=>"id_7", "title"=>"Select", "option"=>["o1"=>"Option 1", "o2"=>"Option 2","o3"=>"Option 3"], "value"=>"o2"],

	//Multiple select
	["type"=>"multipleSelect", "name"=>"id_8", "title"=>"Multiple select", "option"=>["o1"=>"Option 1", "o2"=>"Option 2","o3"=>"Option 3"], "value"=>["o1","o3"] ],

	//Input text
	["type"=>"text", "name"=>"id_9", "title"=>"Text", "note"=>"Ghi chú", "value"=>"", "attr"=>''],

	//Input date
	["type"=>"date", "name"=>"id_date0", "title"=>"Chọn ngày đầy đủ", "note"=>"Ấn để chọn ngày", "value"=>"", "attr"=>'', "position"=>"bottom",
		"format"=>"hour day/month/year",
		"config"=>[
			"allow"=>[
				"hours"=>[ [6,11], [13,17], [20,23] ], "minutes"=>true,
				"days"=>"1-31", "months"=>"1-12", "weekDay"=>[],
				"min"=>["y"=>1997, "m"=>2, "d"=>14], "max"=>["y"=>2020, "m"=>3, "d"=>15]
			],
			"value"=>["day"=>date("d"), "month"=>date("m"), "year"=>date("Y") ]
		]
	],
	["type"=>"date", "name"=>"id_date1", "title"=>"Chọn ngày", "note"=>"Ấn để chọn ngày", "value"=>"", "attr"=>'', "position"=>"bottom",
		"format"=>"day/month/year",
		"config"=>[
			"allow"=>[
				"days"=>[1,3,5,14], "months"=>[3,5,7], "weekDay"=>["mon", "tue", "wed", "thu", "fri"],
				"min"=>["y"=>2019, "m"=>2, "d"=>14], "max"=>["y"=>2023, "m"=>2, "d"=>14]
			],
			"value"=>["day"=>date("d"), "month"=>date("m"), "year"=>date("Y")]
		]
	],
	
	//Input number
	["type"=>"number", "name"=>"id_10", "title"=>"Number", "note"=>"Ghi chú", "min"=>0, "max"=>9999, "value"=>"20","attr"=>''],

	//Textarea
	["type"=>"textarea", "name"=>"id_11", "title"=>"Textarea", "note"=>"Ghi chú", "value"=>"", "attr"=>'', "full"=>true],

	//Editor
	["type"=>"editor", "name"=>"id_13x", "title"=>"Trình soạn thảo HTML", "value"=>"Nội dung mặc định"],
	
	//Sắp xếp - OP
	["type"=>"sort", "name"=>"sort_1", "title"=>"Sắp xếp", "value"=>["i1"=>"Item 1", "i2"=>"Item 2","i3"=>"Item 3","i4"=>"Item 4","i11"=>"Item 1", "i12"=>"Item 2","i13"=>"Item 3","i14"=>"Item 4","i21"=>"Item 1", "i22"=>"Item 2","i23"=>"Item 3","i24"=>"Item 4"], "edit"=>true],

	//Sắp xếp nhiều - OP
	["type"=>"multipleSort", "name"=>"sort_3", "title"=>"Sắp xếp nhiều mục", "value"=>[
		["title"=>"Tiêu đề 1", "item"=>array("i1"=>"Item 1 - 1", "i2"=>"Item 1 - 2", "i3"=>"Item 1 - 3")],
		["title"=>"Tiêu đề 2", "item"=>array("i1"=>"Item 2 - 1 ", "i2"=>"Item 2 - 2")],
		["title"=>"Tiêu đề 3", "item"=>array("i1"=>"Item 3 - 1", "i2"=>"Item 3 - 2", "i3"=>"Item 3 -3")],
	], "edit"=>true],

]);





echo ST("test_id","Tiêu đề ẩn",[
	["html"=>'Nội dung bị ẩn']
],"500");
</textarea>




	
	
	<div class="heading-basic">Menu mặc định</div>
	<textarea style="width:100%" rows="5">
//Menu bình thường
$option[]=[
"title"=>"Tab title",
"role"=>9,
"type"=>"default",
"panel"=>"file"//File chứa nội dung
]; 
	</textarea>
	<div style="padding:10px"></div>
	
	
	<div class="heading-basic">Menu đa cấp</div>
<textarea style="width:100%" rows="5">
$option[]=[
"title"=>"Sub Panel",
"role"=>9,
"icon"=>"fa-cogs",
"type"=>"sub",
"panel"=>"subfolder",
"sub"=> array(


	//Sub 1
	[
		"title"=>"Sub 1",
		"role"=>9,
		"icon"=>"fa-wrench",
		"panel"=>"subfolder/sub-1"
	],


	//Sub 2
	[
		"title"=>"Sub 2",
		"role"=>9,
		"icon"=>"fa-subscript",
		"panel"=>"subfolder/sub-2"
	],

	//Mở trong trang mới
	[
		"title"=>"Sub 3 link",
		"role"=>9,
		"icon"=>"fa-subscript",
		"type"=>"link",
		//"link"=>"http://newpage",
		"panel"=>"subfolder/sub-3"
	],



),




"header"=>'<form action="" method="POST" id="form">',
"footer"=>"</form>"

];
</textarea>

<div style="padding:10px"></div>
</div>
</div>