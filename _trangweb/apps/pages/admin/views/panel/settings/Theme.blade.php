@php
	$fontList="Roboto,Noto Serif TC,Noto Serif SC,Open Sans,Montserrat,Roboto Condensed,Oswald,Source Sans Pro,Merriweather,Roboto Slab,Noto Sans,Playfair Display,Roboto Mono,Lora,Muli,Fira Sans,Arimo,Nunito,Noto Serif,Inconsolata,Charmonman,Quicksand,Bungee,Cabin,Josefin Sans,Anton,Nunito Sans,Lobster,Pacifico,Varela Round,Dancing Script,Yanone Kaffeesatz,Exo,Asap,Encode Sans Condensed,Comfortaa,Kanit,Amatic SC,EB Garamond,Maven Pro,Play,Francois One,Cuprum,Rokkitt,Patrick Hand,Alegreya,Fira Sans Condensed,Vollkorn,Noto Sans SC,Old Standard TT,Saira Semi Condensed,Alegreya Sans,Cormorant Garamond,Prompt,Noticia Text,Tinos,Alfa Slab One,Playfair Display SC,KoHo,Fira Sans Extra Condensed,Cabin Condensed,VT323,Paytone One,Philosopher,IBM Plex Sans,Bangers,M PLUS 1p,Prata,Saira Extra Condensed,K2D,Taviraj,Niramit,Bai Jamjuree,Mali,Krub,Kodchasan,Fahkwang,Chakra Petch,Montserrat Alternates,Lalezar,Jura,Sigmar One,Bungee Inline,Archivo,Srisakdi,Baloo,Bevan,Alegreya Sans SC,Saira,Cormorant,Pridi,Cousine,Mitr,Spectral,IBM Plex Serif,Saira Condensed,Pangolin,Space Mono,Sawarabi Gothic,Yeseva One,Judson,Alegreya SC,Arsenal,Itim,Markazi Text,Pattaya,M PLUS Rounded 1c,Arima Madurai,IBM Plex Mono,Encode Sans,Asap Condensed,IBM Plex Sans Condensed,Maitree,Andika,Baloo Bhaina,Trirong,Lemonada,Baloo Bhaijaan,Cormorant SC,Sedgwick Ave,Athiti,Sriracha,Baloo Paaji,Patrick Hand SC,Cormorant Upright,Podkova,Faustina,Cormorant Infant,Baloo Tamma,Chonburi,David Libre,Encode Sans Semi Condensed,Bungee Shade,Encode Sans Semi Expanded,Spectral SC,Baloo Chettan,Farsan,Encode Sans Expanded,Baloo Thambi,Baloo Bhai,Coiny,Manuale,Baloo Tammudu,Cormorant Unicase,Vollkorn SC,Sedgwick Ave Display,Baloo Da,Bungee Outline,Bungee Hairline,";

	foreach(glob(PUBLIC_ROOT."/assets/general/fonts/*.ttf") as $path){
	    $fontList.=pathinfo($path)["filename"].",";
	}

	$i=0;
	foreach(explode(",", rtrim($fontList,",") ) as $name) {
		$i++;
		$fontFamily[$name]=$i.". ".$name;
	}
@endphp


{!!
	ST("theme__general_","Thiết lập chung",[
		["type"=>"select","name"=>"font_family","title"=>'Font chữ', "option"=>$fontFamily],
		["type"=>"file", "name"=>"my_font_family", "title"=>"Chọn font chữ riêng", "note"=>"Hỗ trợ đuôi .ttf", "value"=>"", "ext"=>"ttf" ,"post"=>0],
		["type"=>"switch","name"=>"font_family_input","title"=>"Dùng font cho cả input,select","value"=>1],
		["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ toàn trang","min"=>"5","max"=>"100","value"=>"15"],
		["type"=>"color", "required"=>true,"name"=>"color","title"=>"Màu chữ","default"=>"#333333"],
		["type"=>"color", "required"=>true,"name"=>"link_color","title"=>"Màu link","default"=>"#444444"],
		["type"=>"color", "required"=>true,"name"=>"background_color","title"=>"Màu nền chính","default"=>"#EAEAEA"],
		["type"=>"color", "required"=>false,"name"=>"background_color_2","title"=>"Kết hợp với nền","default"=>""],
		["type"=>"image","name"=>"background_image","title"=>"Ảnh nền toàn trang","ext"=>"jpg,png,gif", "post"=>0],
		["type"=>"switch","name"=>"background_switch","title"=>"Dùng ảnh nền (ảnh nền luôn được dùng tại trang đăng nhập, đăng ký)","value"=>"0"],
		["type"=>"image","name"=>"social_share_poster","title"=>"Ảnh chia sẻ Facebook","ext"=>"jpg", "post"=>0],
	])
!!}

@php
	$primaryBG="#39B5C9";
	$primaryHover="#3FC8DE";
	$primaryColor="#FFFFFF";
@endphp

{!!
	ST("theme__primary_","Màu chủ đạo",[
		["type"=>"color", "required"=>true,"name"=>"background","title"=>"Màu nền","default"=>$primaryBG],
		["type"=>"color", "required"=>true,"name"=>"hover","title"=>"Màu hover","default"=>$primaryHover],
		["type"=>"color", "required"=>true,"name"=>"color","title"=>"Màu chữ","default"=>$primaryColor],
	])
!!}



{!!
	OP("theme_header_","Đầu trang",[
		["type"=>"image", "name"=>"logo", "title"=>"Logo","ext"=>"jpg,jpeg,png,gif", "post"=>0],
		["type"=>"image", "name"=>"favicon", "title"=>"Biểu tượng (cỡ 48px*48px)","ext"=>"png", "post"=>0],
		["type"=>"color", "required"=>false,"name"=>"bg","title"=>"Màu nền đầu trang","default"=>"#313131", "default"=>""],
		["type"=>"switch", "name"=>"shadow","title"=>"Đổ bóng","value"=>"0"],
		["type"=>"number", "name"=>"height_dk","title"=>"Chiều cao đầu trang (máy tính)","min"=>"5","max"=>"900","value"=>"80"],
		["type"=>"number", "name"=>"height_mb","title"=>"Chiều cao đầu trang (điện thoại)","min"=>"5","max"=>"900","value"=>"60"],
		["type"=>"switch", "name"=>"fixed","title"=>"Luôn treo theo màn hình","value"=>"0"],
		["type"=>"switch","name"=>"search","title"=>"Hiện thanh tìm kiếm","value"=>1]
	])
!!}

{!!
	ST("theme__navbar_","Thanh navbar menu",[
		[
			"type"   => "select",
			"name"   => "hover_type",
			"title"  => "Hiệu ứng trỏ chuột",
			"option" => [
				"default"   => "Mặc định",
				"underline" => "Gạch dưới chữ",
			],
			"value" => "underline"
		],
		["type"=>"color", "required"=>true,"name"=>"color","title"=>"Màu chữ","default"=>"#f4f2f2"],
		["type"=>"color", "required"=>true,"name"=>"hover","title"=>"Màu trỏ chuột","default"=>$primaryHover],
		["type"=>"color", "required"=>false,"name"=>"border_color","title"=>"Màu viền dưới trên điện thoại","default"=>"#F5F5F5"],
		["type"=>"switch","name"=>"shadow","title"=>"Đổ bóng","value"=>1],
		["type"=>"switch","name"=>"bg_white","title"=>"Dùng nền trắng trên điện thoại","value"=>1],
		["type"=>"color", "required"=>true,"name"=>"sub_background_color","title"=>"Màu nền menu trong","default"=>"#f4f2f2"],
		["type"=>"number", "name"=>"sub_background_opacity","title"=>"Độ trong suốt nền menu trong","min"=>"0","max"=>"1", 'attr' => 'step="0.1"', "value"=>"1"],
		["type"=>"color", "required"=>true,"name"=>"sub_color","title"=>"Màu chữ menu trong","default"=>"#313131"],
		["type"=>"color", "required"=>true,"name"=>"sub_hover","title"=>"Màu chữ (hover) menu trong","default"=>"#313131"],
		["type"=>"color", "required"=>true,"name"=>"search_bg","title"=>"Màu nền thanh tìm kiếm","default"=>"#ffffff"],
		["type"=>"color", "required"=>true,"name"=>"search_color","title"=>"Màu chữ thanh tìm kiếm","default"=>"#919191"],
		["type"=>"color", "required"=>true,"name"=>"search_border","title"=>"Màu viền thanh tìm kiếm","default"=>"#D5D5D5"],
		["type"=>"color", "required"=>true,"name"=>"search_focus","title"=>"Màu viền thanh tìm kiếm khi ấn","default"=>$primaryHover],
	])
!!}

{!!
	ST("theme__footer_","Cuối trang",[
		["type"=>"color", "required"=>false,"name"=>"bg","title"=>"Màu nền cuối trang","default"=>"#313131"],
		["type"=>"color", "required"=>true,"name"=>"color","title"=>"Màu chữ cuối trang","default"=>"#f4f2f2"],
		["type"=>"color", "required"=>true,"name"=>"link_color","title"=>"Màu link cuối trang","default"=>"#f4f2f2"],
		["type"=>"color", "required"=>true,"name"=>"link_hover","title"=>"Màu link cuối trang(trỏ chuột)","default"=>"#f4f2f2"],
		["type"=>"color", "required"=>false,"name"=>"border","title"=>"Màu viền cuối trang","default"=>$primaryBG],
	])
!!}


{!!
	ST("theme__heading_","Heading",[
		["type"=>"image","name"=>"heading_banner","title"=>"Ảnh nền heading","ext"=>"jpg,png,gif", "post"=>0],
		["type"=>"switch","name"=>"uppercase","title"=>"In hoa tiêu đề","value"=>"0"],
		["type"=>"switch","name"=>"bold","title"=>"Chữ đậm","value"=>"0"],

		["html"=>'<div style="margin-left: 30px">'],

		["html"=>'
		<div class="pd-10"></div>
		<div class="heading-basic"><span>Heading basic</span></div> 
		'],
		["type"=>"number","name"=>"basic_padding","title"=>"Padding","min"=>"1","max"=>"100","value"=>"10"],
		["type"=>"number","name"=>"basic_font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>"25"],
		["type"=>"switch","name"=>"basic_color","title"=>"Dùng màu","value"=>"1"],
		["type"=>"image","name"=>"basic_icon","title"=>"Icon dưới tiêu đề","ext"=>"jpg,png,gif", "post"=>0],

		["html"=>'
		<div class="pd-10"></div>
		<div class="heading-simple"><span>Heading simple</span></div> 
		'],
		["type"=>"number","name"=>"simple_padding","title"=>"Padding","min"=>"1","max"=>"100","value"=>"8"],
		["type"=>"number","name"=>"simple_font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>"22"],


		["html"=>'
		<div class="pd-10"></div>
		<div class="heading-block"><span>Heading block</span></div> 
		'],
		["type"=>"number","name"=>"block_padding","title"=>"Padding","min"=>"1","max"=>"100","value"=>"8"],
		["type"=>"number","name"=>"block_font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>"20"],

		["html"=>'
		<div class="pd-10"></div>
		<div class="heading-line"><span>Heading line</span></div> 
		'],
		["type"=>"number","name"=>"line_padding","title"=>"Padding","min"=>"1","max"=>"100","value"=>"8"],
		["type"=>"number","name"=>"line_font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>"20"],
		["type"=>"number","name"=>"line_radius","title"=>"Độ bo cạnh","min"=>"0","max"=>"100","value"=>"20"],
		["type"=>"number","name"=>"line_line_height","title"=>"Độ lớn của vạch","min"=>"0","max"=>"100","value"=>"2"],
		["type"=>"select","name"=>"line_line_position","title"=>"Vị trí vạch","option"=>["50%"=>"Giữa","0px"=>"Dưới"]],

		["html"=>'
		<div class="pd-10"></div>
		<div class="heading-sharp"><span>Heading sharp</span></div> 
		'],
		["type"=>"number","name"=>"sharp_height","title"=>"Kích cỡ","min"=>"1","max"=>"100","value"=>"50"],
		["type"=>"number","name"=>"sharp_font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>"20"],
		["type"=>"number","name"=>"sharp_inclined","title"=>"Độ nghiêng","min"=>"0","max"=>"100","value"=>"20"],
		["type"=>"number","name"=>"sharp_line","title"=>"Độ lớn của vạch","min"=>"0","max"=>"100","value"=>"3"],

		["html"=>'
		</div>
		'],

	],"",false)
!!}


{!!
	ST("theme__modal_","Modal - popup thông báo",[
		["type"=>"number","name"=>"padding","title"=>"Padding","min"=>"1","max"=>"100","value"=>"12"],
		["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>"22"],
		["type"=>"number","name"=>"fadein","title"=>"Hiệu ứng hiện dần","min"=>"0","max"=>"2","value"=>"0.5", "attr"=>'step="0.1"'],
		["type"=>"select", "name"=>"heading_type", "title"=>"Kiểu tiêu đề", "option"=>["block"=>"Block", "simple"=>"Simple"], "value"=>"block"],
		["type"=>"select", "name"=>"close_icon", "title"=>"Kiểu nút đóng", "option"=>["f00d"=>"Kiểu 1","f05c"=>"Kiểu 2","f2d4"=>"Kiểu 3","f00c"=>"Kiểu 4","f070"=>"Kiểu 5", "f2d1"=>"Kiểu 6"], "value"=>"f00d"],
		["html"=>'
		<a data-modal="demo" class="menu block modal-click">Click để hiện hộp thông báo</a>
		'.modal('demo', '<i class="fa-icon fa fa-info-circle"></i> Tiêu đề thông báo', '<div class="pd-10">Nội dung thông báo</div>','750px', false, true, true).'
		']
	],"",false)
!!}




{!!
	ST("theme__alert_","Các thông báo trạng thái",[
	["type"=>"number","name"=>"padding","title"=>"Padding","min"=>"1","max"=>"100","value"=>9],
	["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>"14"],

	["html"=>'<div style="padding:10px"> <div class="admin-theme-section alert-success link">Hoàn thành (ấn để chỉnh màu)</div> <div class="hidden">'],
	["type"=>"color", "required"=>true,"name"=>"success_color","title"=>"Màu chữ","default"=>"#3C763D"],
	["type"=>"color", "required"=>true,"name"=>"success_background","title"=>"Màu nền","default"=>"#BBF2CF"],
	["type"=>"color", "required"=>true,"name"=>"success_border","title"=>"Màu viền","default"=>"#AFE2C1"],
	["html"=>'</div></div>'],

	["html"=>'<div style="padding:10px"> <div class="admin-theme-section alert-info link">Thông tin (ấn để chỉnh màu)</div> <div class="hidden">'],
	["type"=>"color", "required"=>true,"name"=>"info_color","title"=>"Màu chữ","default"=>"#31708f"],
	["type"=>"color", "required"=>true,"name"=>"info_background","title"=>"Màu nền","default"=>"#C5EEFC"],
	["type"=>"color", "required"=>true,"name"=>"info_border","title"=>"Màu viền","default"=>"#B9DFED"],
	["html"=>'</div></div>'],

	["html"=>'<div style="padding:10px"> <div class="admin-theme-section alert-warning link">Cảnh báo (ấn để chỉnh màu)</div> <div class="hidden">'],
	["type"=>"color", "required"=>true,"name"=>"warning_color","title"=>"Màu chữ","default"=>"#8a6d3b"],
	["type"=>"color", "required"=>true,"name"=>"warning_background","title"=>"Màu nền","default"=>"#FAF2D1"],
	["type"=>"color", "required"=>true,"name"=>"warning_border","title"=>"Màu viền","default"=>"#EDE5C6"],
	["html"=>'</div></div>'],

	["html"=>'<div style="padding:10px"> <div class="admin-theme-section alert-danger link">Có lỗi (ấn để chỉnh màu)</div> <div class="hidden">'],
	["type"=>"color", "required"=>true,"name"=>"danger_color","title"=>"Màu chữ","default"=>"#a94442"],
	["type"=>"color", "required"=>true,"name"=>"danger_background","title"=>"Màu nền","default"=>"#ebccd1"],
	["type"=>"color", "required"=>true,"name"=>"danger_border","title"=>"Màu viền","default"=>"#DEC1C5"],
	["html"=>'</div></div>'],

	])
!!}



{!!
	ST("theme__panel_","Bảng thông báo",[
		["type"=>"color", "required"=>true,"name"=>"border_color","title"=>"Màu viền","default"=>"#E2E2E2"],
		["type"=>"color", "required"=>true,"name"=>"background","title"=>"Màu nền tiêu đề","default"=>"#F1F1F1"],
		["type"=>"color", "required"=>true,"name"=>"color","title"=>"Màu chữ","default"=>"#313131"],
		["type"=>"select","name"=>"arrow","title"=>"Kiểu icon mũi tên","option"=>["f054 f078"=>"Kiểu 1","f138 f13a"=>"Kiểu 2","f105 f107"=>"Kiểu 3","f0da f0d7"=>"Kiểu 4","f06e f070"=>"Kiểu 5"]],
		["html"=>'
		<div class="panel panel-default">
			<div class="heading link">Tiêu đề mục</div>
			<div class="panel-body hidden">Nội dung bị ẩn</div>
		</div>
		<div class="panel panel-primary">
			<div class="heading link">Tiêu đề mục</div>
			<div class="panel-body hidden">Nội dung bị ẩn</div>
		</div>
		<div class="panel panel-success">
			<div class="heading link">Tiêu đề mục</div>
			<div class="panel-body hidden">Nội dung bị ẩn</div>
		</div>
		<div class="panel panel-info">
			<div class="heading link">Tiêu đề mục</div>
			<div class="panel-body hidden">Nội dung bị ẩn</div>
		</div>
		<div class="panel panel-warning">
			<div class="heading">Tiêu đề thông báo</div>
			<div class="panel-body">Nội dung thông báo </div>
		</div>
		<div class="panel panel-danger">
			<div class="heading">Tiêu đề thông báo</div>
			<div class="panel-body">Nội dung thông báo</div>
		</div>
		']


	])
!!}




{!!
	ST("theme__form_","Nút nút button & input",[
	["type"=>"number","name"=>"margin","title"=>"Khoảng cách giữa các phần nhập","min"=>"0","max"=>"100","value"=>"5"],
	["type"=>"number","name"=>"padding_vertical","title"=>"Padding dọc","min"=>"1","max"=>"100","value"=>10],
	["type"=>"number","name"=>"padding_horizontal","title"=>"Padding ngang","min"=>"1","max"=>"100","value"=>"12"],
	["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ","min"=>"5","max"=>"100","value"=>"14"],
	["type"=>"number","name"=>"btn_radius","title"=>"Độ bo cạnh","min"=>"0","max"=>"100","value"=>"3"],
	["type"=>"switch","name"=>"btn_uppercase","title"=>"In hoa toàn bộ button","value"=>"0"],

	["html"=>'<div class="pd-5"> <button type="button" class="admin-theme-section btn-primary">Nút button chính</button></div>'],

	["html"=>'<div class="pd-5"> <button type="button" class="admin-theme-section btn-gradient">Nút gradient (ấn để chỉnh màu)</button> <div class="hidden">'],
	["type"=>"color", "required"=>true,"name"=>"gradient_color","title"=>"Màu chữ","default"=>"#ffffff"],
	["type"=>"color", "required"=>true,"name"=>"gradient_background1","title"=>"Màu nền 1","default"=>"#468FD8"],
	["type"=>"color", "required"=>true,"name"=>"gradient_background2","title"=>"Màu nền 2","default"=>"#468FD8"],
	["type"=>"color", "required"=>true,"name"=>"gradient_hover","title"=>"Màu nền hover","default"=>"#4C9BEB"],
	["html"=>'</div></div>'],

	["html"=>'<div class="pd-5"> <button type="button" class="admin-theme-section btn-info">Nút info (ấn để chỉnh màu)</button> <div class="hidden">'],
	["type"=>"color", "required"=>true,"name"=>"info_color","title"=>"Màu chữ","default"=>"#ffffff"],
	["type"=>"color", "required"=>true,"name"=>"info_background","title"=>"Màu nền","default"=>"#468FD8"],
	["type"=>"color", "required"=>true,"name"=>"info_hover","title"=>"Màu nền hover","default"=>"#4C9BEB"],
	["html"=>'</div></div>'],

	["html"=>'<div class="pd-5"> <button type="button" class="admin-theme-section btn-danger">Nút cảnh báo (ấn để chỉnh màu)</button> <div class="hidden">'],
	["type"=>"color", "required"=>true,"name"=>"danger_color","title"=>"Màu chữ","default"=>"#ffffff"],
	["type"=>"color", "required"=>true,"name"=>"danger_background","title"=>"Màu nền","default"=>"#d9534f"],
	["type"=>"color", "required"=>true,"name"=>"danger_hover","title"=>"Màu nền hover","default"=>"#F54943"],
	["html"=>'</div></div>'],

	["html"=>'<div class="pd-5"> <button type="button" class="admin-theme-section btn-disabled">Nút disabled (ấn để chỉnh màu)</button> <div class="hidden">'],
	["type"=>"color", "required"=>true,"name"=>"disable_color","title"=>"Màu chữ","default"=>"#979797"],
	["type"=>"color", "required"=>true,"name"=>"disable_background","title"=>"Màu nền","default"=>"#E1E1E1"],
	["html"=>'</div></div>'],


	["html"=>'<div class="pd-20"> <div class="input-icon"><i class="fa fa-cog"></i> <input class="input" placeholder="Tùy chỉnh Input"/></div> </div>'],
	["type"=>"color", "required"=>true,"name"=>"border","title"=>"Màu viền","default"=>"#DDDDDD"],
	["type"=>"color", "required"=>true,"name"=>"border_focus","title"=>"Màu viền khi ấn","default"=>"#4AB9D9"],
	["type"=>"number","name"=>"input_radius","title"=>"Độ bo cạnh","min"=>"0","max"=>"100","value"=>"3"],
	["type"=>"number","name"=>"input_icon_padding","title"=>"Khoảng cách giữa icon","min"=>"0","max"=>"100","value"=>"25"],
	["type"=>"color", "required"=>true,"name"=>"hover","title"=>"Màu hover form","default"=>"#E9FDFF"],

	])
!!}







{!!
	ST("theme__checkbox_","Checkbox & Switch",[
	["html"=>'<div class="pd-20">
		<div><label class="check"><input type="checkbox" /><s></s> Checkbox 1</label></div>
		<div><label class="check"><input type="checkbox" checked /><s></s> Checkbox 2</label></div>
		<div><label class="check radio"><input type="radio" /><s></s> Radio</label></div>
	</div>'],
	["type"=>"color", "required"=>true,"name"=>"checkbox_border","title"=>"Màu viền","default"=>"#DDDDDD"],
	["type"=>"color", "required"=>true,"name"=>"checkbox_background","title"=>"Màu nền","default"=>"#F8F6F6"],
	["type"=>"number","name"=>"checkbox_size","title"=>"Kích cỡ","min"=>"0","max"=>"100","value"=>"30"],
	["type"=>"number","name"=>"checkbox_line_height","title"=>"Căn chữ","min"=>"0","max"=>"100","value"=>"30"],
	["type"=>"number","name"=>"checkbox_width","title"=>"Độ rộng check","min"=>"0","max"=>"100","value"=>6],
	["type"=>"number","name"=>"checkbox_font_size","title"=>"Cỡ chữ","min"=>"0","max"=>"100","value"=>"16"],
	["type"=>"number","name"=>"checkbox_margin","title"=>"Khoảng cách với chữ","min"=>"0","max"=>"100","value"=>"12"],
	["type"=>"number","name"=>"checkbox_top","title"=>"Vị trí icon checked(trên)","min"=>"0","max"=>"100","value"=>10, "attr"=>' step="5"'],
	["type"=>"number","name"=>"checkbox_left","title"=>"Vị trí icon checked(trái)","min"=>"0","max"=>"100","value"=>42, "attr"=>' step="5"'],

	["html"=>'<div class="pd-20">
		<div><label class="switch"><input type="checkbox" /><s></s> Switch toggle</label></div>
		<div><label class="switch"><input type="checkbox" checked/><s></s> Switch toggle</label></div>
	</div>'],
	["type"=>"color", "required"=>true,"name"=>"switch_background","title"=>"Màu nền (không tick)","default"=>"#DFDDDD"],
	["type"=>"color", "required"=>true,"name"=>"switch_round","title"=>"Màu nút tròn","default"=>"#FFFFFF"],
	["type"=>"number","name"=>"switch_height","title"=>"Cao","min"=>"0","max"=>"150","value"=>"35"],
	["type"=>"number","name"=>"switch_width","title"=>"Rộng","min"=>"0","max"=>"150","value"=>"60"],
	["type"=>"number","name"=>"switch_font_size","title"=>"Cỡ chữ","min"=>"0","max"=>"100","value"=>16],
	["type"=>"number","name"=>"switch_margin","title"=>"Khoảng cách nút tròn","min"=>"0","max"=>"150","value"=>"15"],
	["type"=>"number","name"=>"switch_margin_text","title"=>"Khoảng cách với chữ","min"=>"0","max"=>"150","value"=>"45"],

	])
!!}








{!!
	ST("theme__label_","Các nhãn trạng thái",[
	["type"=>"number","name"=>"padding","title"=>"Padding","min"=>"0","max"=>"100","value"=>"2"],
	["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>"14"],
	["type"=>"number","name"=>"radius","title"=>"Độ bo cạnh","min"=>"0","max"=>"100","value"=>"14"],

	["html"=>'
		<div class="pd-5">
			<span class="label-default">Default</span>
			<span class="label-success">Success</span>
			<span class="label-info">Info</span>
			<span class="label-warning">Warning</span>
			<span class="label-danger">Danger</span>
		</div>
		']


	])
!!}




{!!
	ST("theme__paginate_","Phân trang",[
		["type"=>"number","name"=>"padding","title"=>"Padding","min"=>"0","max"=>"100","value"=>"7"],
		["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ","min"=>"0","max"=>"100","value"=>"14"],
		["type"=>"number","name"=>"margin","title"=>"Khoảng cách giữa các nút","min"=>"0","max"=>"100","value"=>"2"],
		["type"=>"number","name"=>"radius","title"=>"Độ bo cạnh","min"=>"0","max"=>"100","value"=>"25"],
		["html"=>'
		<nav class="paginate">
			<a class="paginate-next" data-page="1" href="#1"><i class="fa fa-arrow-left"></i></a>
			<span class="paginate-current">2</span>
			<a class="paginate-last" data-page="9" href="#9">9</a>
			<a class="paginate-next" data-page="2" href="#2"><i class="fa fa-arrow-right"></i></a>
		</nav>']
	])
!!}




{!!
	ST("theme__tooltip_","Tooltip",[
		["type"=>"color", "required"=>true,"name"=>"background","title"=>"Màu nền","default"=>$primaryBG],
		["type"=>"color", "required"=>true,"name"=>"color","title"=>"Màu chữ","default"=>$primaryColor],
		["type"=>"number","name"=>"padding","title"=>"Padding","min"=>"0","max"=>"100","value"=>"7"],
		["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ","min"=>"0","max"=>"100","value"=>"14"],
		["type"=>"number","name"=>"margin","title"=>"Khoảng cách","min"=>"0","max"=>"100","value"=>"5"],
		["type"=>"number","name"=>"radius","title"=>"Độ bo cạnh","min"=>"0","max"=>"100","value"=>"5"],
		["html"=>'<div class="menu bd pd-20" style="margin-bottom: 20px">
			<span title="Mô tả" data-pos="top" class="tooltip"> <i style="background: #EAEAEA;">Tooltip top</i> </span>
			<span title="Mô tả" data-pos="left" class="tooltip"> <i style="background: #EAEAEA;">Tooltip left</i> </span>
			<span title="Mô tả" data-pos="right" class="tooltip"> <i style="background: #EAEAEA;">Tooltip right</i> </span>
			<span title="Mô tả" data-pos="bottom" class="tooltip"> <i style="background: #EAEAEA;">Tooltip bottom</i> </span>
		</div>
		']
	])
!!}




{!!
	ST("theme__list_","Danh sách",[
		["type"=>"color", "required"=>true,"name"=>"border_color","title"=>"Màu viền","default"=>"#F4F4F4"],
		["type"=>"color", "required"=>true,"name"=>"background","title"=>"Màu nền","default"=>"#FFFFFF"],
		["type"=>"color", "required"=>true,"name"=>"active","title"=>"Màu active & hover","default"=>"#DBF9FF"],
		["type"=>"color", "required"=>true,"name"=>"link","title"=>"Màu link","default"=>$primaryBG],
		["type"=>"number","name"=>"padding","title"=>"Padding","min"=>"0","max"=>"100","value"=>9],
		["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>16],
		["type"=>"switch","name"=>"border","title"=>"Dùng viền","value"=>0],

		["html"=>'
		<div class="pd-10">
			<div class="list">
				<div class="heading-block"><span>Danh sách liên kết</span></div>
				<a href="#1">Item 1 <span class="badge">1</span></a>
				<a class="list-actived" href="#2">Item 2 <span class="badge">1</span></a>
				<a href="#3">Item 3 <span class="badge">1</span></a>
			</div>
			<div class="list">
				<div class="heading-sharp"><span>Danh sách văn bản</span></div>
				<span>Item 1 <span class="badge">1</span></span>
				<span>Item 2 <span class="badge">123</span></span>
				<span>Item 3 <span class="badge">12345</span></span>
			</div>
		</div>
		']


	])
!!}



{!!
	ST("theme__menu_","Menu đơn giản",[



		["type"=>"color", "required"=>true,"name"=>"border_color","title"=>"Màu viền","default"=>"#E7E7E7"],
		["type"=>"color", "required"=>true,"name"=>"background","title"=>"Màu nền chính","default"=>"#FFFFFF"],
		["type"=>"color", "required"=>true,"name"=>"background_bg","title"=>"Màu nền (có nền)","default"=>"#F5F5F5"],
		["type"=>"number","name"=>"padding","title"=>"Padding","min"=>"0","max"=>"100","value"=>"8"],
		["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>"15"],

		["html"=>'
		<div class="pd-10">
			<div class="menu bd">Menu đơn giản</div>
			<div class="pd-5"></div>
			<div class="menu-bg bd">Menu đơn giản (có nền)</div>
		</div>
		']


	])
!!}





{!!
	ST("theme__seeMore_","Nút xem thêm",[

		["type"=>"number","name"=>"padding","title"=>"Padding","min"=>"0","max"=>"100","value"=>"5"],
		["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ","min"=>"1","max"=>"100","value"=>"16"],
		["type"=>"number","name"=>"radius","title"=>"Độ bo cạnh","min"=>"0","max"=>"100","value"=>"20"],

		["html"=>'
		<div class="pd-10 center">
			<a href="javascript:void(0)" class="see-more">Xem thêm nhiều hơn</a>
		</div>
		']


	])
!!}





{!!
	ST("theme__table_","Bảng",[
		["type"=>"color", "required"=>true,"name"=>"border_color","title"=>"Màu viền","default"=>"#EDEDED"],
		["type"=>"color", "required"=>true,"name"=>"th_color","title"=>"Màu chữ (tiêu đề)","default"=>"#000"],
		["type"=>"color", "required"=>true,"name"=>"th_background","title"=>"Màu nền (tiêu đề)","default"=>"#A6DBE1"],
		["type"=>"color", "required"=>true,"name"=>"td1_background","title"=>"Màu nền 1(nội dung)","default"=>"#FFFFFF"],
		["type"=>"color", "required"=>true,"name"=>"td2_background","title"=>"Màu nền 2(nội dung)","default"=>"#FAFAFA"],
		["type"=>"color", "required"=>true,"name"=>"hover","title"=>"Màu hover","default"=>"#DBF9FF"],
		["type"=>"number","name"=>"padding","title"=>"Padding","min"=>"0","max"=>"100","value"=>"8"],


		["html"=>'
		<table class="table table-border width-70">
			<tr> <th>Họ & tên</th> <th>Số điện thoại</th> <th>Địa chỉ</th> </tr>
			<tr><td>Lò Văn Ngố</td> <td>0123456789</td> <td>Lai Châu</td></tr>
			<tr><td>Lò Vi Sóng</td> <td>19001000</td> <td>Sơn La</td></tr>
			<tr><td>Lò Nướng</td> <td>0167888888</td> <td>Điện Biên</td></tr>
			<tr><td>Hà Văn Giang</td> <td>011112222</td> <td>Sapa</td></tr>
		</table>
		']


	])
!!}




{!!
	ST("theme__section_","Khu vực",[
		["type"=>"color", "required"=>true,"name"=>"background","title"=>"Màu nền","default"=>"#FFFFFF"],
		["type"=>"number","name"=>"padding","title"=>"Padding","min"=>"0","max"=>"100","value"=>"8"],
		["type"=>"number","name"=>"font_size","title"=>"Cỡ chữ (tiêu đề)","min"=>"0","max"=>"100","value"=>"20"],
		["type"=>"color", "required"=>true,"name"=>"heading_background","title"=>"Màu nền tiêu đề","default"=>"#FFFFFF"],
		["type"=>"switch","name"=>"color","title"=>"Màu tiêu đề","value"=>"1"],
		["type"=>"switch","name"=>"border","title"=>"Dùng viền tiêu đề","value"=>"1"],
		["type"=>"number","name"=>"opacity","title"=>"Độ mờ viền bóng","min"=>"0","max"=>"9","value"=>"2"],
		["type"=>"number","name"=>"margin","title"=>"Khoảng cách bên dưới","min"=>"0","max"=>"100","value"=>"20"],


		["html"=>'
		<section class="section">
			<div class="heading"><h2>Khu vực 1</h2></div>
			<div class="section-body">
				Khu vực chứa các phần nội dung chính<br/>
				Section<br/>
				Text
			</div>
		</section>

		<section class="section">
			<div class="heading"><h2>Khu vực 2</h2></div>
			<div class="section-body">
				Khu vực chứa các phần nội dung chính<br/>
				Section<br/>
				Text
			</div>
		</section>
		']


	],"",false)
!!}



{!!
	ST("theme__slider_", "Slider ảnh", [
		["type"=>"color", "required"=>true,"name"=>"color","title"=>"Màu chữ", "default"=>$primaryColor],
		["type"=>"color", "required"=>true,"name"=>"hover","title"=>"Màu chữ hover", "default"=>$primaryBG],
		["type"=>"number","name"=>"opacity","title"=>"Độ trong suốt khi chưa hover","min"=>"0","max"=>"9","value"=>"6"],
		["type"=>"select","name"=>"arrow","title"=>"Kiểu icon",
			"option"=>["f104 f105"=>"Kiểu 1", "f190 f18e"=>"Kiểu 2", "f060 f061"=>"Kiểu 3","f0d9 f0da"=>"Kiểu 4","f137 f138"=>"Kiểu 5"]
		],
		["type"=>"number","name"=>"icon_size","title"=>"Kích cỡ icon chuyển","min"=>"0","max"=>"197","value"=>"30"],
		["type"=>"number","name"=>"circle_size","title"=>"Kích cỡ icon tròn","min"=>"0","max"=>"197","value"=>"15"],
		["html"=>'
		<div class="slider" style="width:100%;max-height: 320px">
			<ul class="slider-basic" data-autoplay="2">
				<li>
					<img src="https://dummyimage.com/1920x600/825382/ffffff.jpg">
				</li>
				<li>
					<img src="https://dummyimage.com/1920x600/93c4c9/ffffff.jpg">
				</li>
				<li>
					<img src="https://dummyimage.com/1920x600/824824/ffffff.jpg">
				</li>
			</ul>
			<div></div>
			<i class="slider-btn-prev"></i>
			<i class="slider-btn-next"></i>
		</div>
		<script>
			$(document).ready(function(){
				$(".admin-item").click(function(){
					sliderInstall();
				});
			});
		</script>
		']


	])
!!}
{{Assets::footer("/assets/slider/style__complete.css", "/assets/slider/script.js")}}



{!!
	ST("theme__fixed_button_", "Nút treo cuối trang", [
		["type"=>"number","name"=>"bottom","title"=>"Khoảng cách với phần dưới","min"=>"0","max"=>"197","value"=>"30"],
		["type"=>"number","name"=>"right","title"=>"Khoảng cách với phần bên phải","min"=>"0","max"=>"197","value"=>"20"],
		["type"=>"color", "required"=>true,"name"=>"linear_from","title"=>"Dải màu nền 1","default"=>"#DF4AD0"],
		["type"=>"color", "required"=>true,"name"=>"linear_to","title"=>"Dải màu nền 2","default"=>"#23C4FF"],
		["type"=>"color", "required"=>true,"name"=>"color","title"=>"Màu chữ nút","default"=>"#FFFFFF"],


	])
!!}


{!! ST("","CSS tùy ý",[["type"=>"textarea", "name"=>"theme__cssCustom", "title"=>"Textarea", "desc"=>"Mô tả", "note"=>"a{color: #fff}", "value"=>"", "attr"=>'', "full"=>true],]) !!}




<script>
$(document).ready(function(){
	//Click hiện từng phần
	$(".admin-theme-section").click(function(){
		var thisEl=$(this).parent().children(".hidden");
		$(".admin-theme-section").parent().children(".hidden").not(thisEl).hide();
		thisEl.slideToggle();
	});

});
</script>