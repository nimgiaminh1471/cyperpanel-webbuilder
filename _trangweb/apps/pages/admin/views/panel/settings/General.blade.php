@php
	use models\PostsCategories;
	use models\Posts;
@endphp


@php
	function createFormTemplate($i, $item, $type="navbar", $parent=true, $sid=0){
		if(is_array($item)){
			extract($item);
		}
		$attr=($i==="_i_" ? 'data-name' : 'name');
		$isSub=$parent ? "" : "[sub][$sid]";
		$name=$attr.'="storage[option]['.$type.']['.$i.']'.$isSub;
		$out=$s="";
		if(isset($sub) && is_array($sub)){
			foreach($sub??[] as $is=>$subItem){
				$s.=createFormTemplate($i, $subItem, $type, false, $is);
			}
		}
		$out.='
		<div class="panel panel-info '.($i==="_i_" ? ' hidden navbar-template' : ' navbar-item').'">
			<div class="heading link"><span>'.($title ?? 'Ấn để sửa').'</span> <i data-action="delete" class="navbar-action right-icon fa fa-times"></i></div>
			<div class="panel-body hidden">
				<div><input placeholder="Tiêu đề" class="width-100 form-mrg" type="text" '.$name.'[title]" data-id="title" value="'.($title??'').'" /></div>
				<div><input placeholder="Liên kết http://" class="width-100 form-mrg" type="text" '.$name.'[link]" data-id="link" value="'.($link??'').'" /></div>
				<div class="input-icon">
					<i class="fa '.($icon??'').'"></i>
					<input class="input input-disabled form-browser-icon" placeholder="Biểu tượng" readonly="">
					<input class="hidden" type="text" '.$name.'[icon]" data-id="icon" value="'.($icon??'').'" />
				</div>
				<label class="switch">
					<input data-id="newTab" type="hidden" '.$name.'[newTab]" value="0" />
					<input data-id="newTab" class="checkbox" type="checkbox" '.$name.'[newTab]" value="1" '.( isset($newTab) && $newTab==1 ? 'checked' : '').' />
					<s></s>
					<span class="form-item-title">Mở trong tab mới</span>
				</label>
				<br/>
				<label class="switch">
					<input data-id="onlyMobile" type="hidden" '.$name.'[onlyMobile]" value="0" />
					<input data-id="onlyMobile" class="checkbox" type="checkbox" '.$name.'[onlyMobile]" value="1" '.( isset($onlyMobile) && $onlyMobile==1 ? 'checked' : '').' />
					<s></s>
					<span class="form-item-title">Chỉ hiện trên điện thoại</span>
				</label>
				'.($parent ? '
				<div><input placeholder="Chia cột trên máy tính" class="input width-100 form-mrg" type="number" '.$name.'[column]" data-id="column" value="'.($column??'').'" /></div>
				<div class="sortable-children navbar-sub">
					<input type="hidden" name="storage[option]['.$type.']['.$i.'][sub]" value="" />
					<div class="alert-danger">Ấn thêm link con từ bên trái màn hình</div>
					'.$s.'
				</div>
				' : '').'
			</div>
		</div>
		';
		return $out;
	}
	//Danh sách chuyên mục
	function categoriesList($gid,$first=true){
		$out=[];
		if($first){
			$children=PostsCategories::multilevelChildren($gid, true);
		}else{
			$children=$gid;
		}

		foreach($children as $id => $child){
			$cate=PostsCategories::get($id);
			$title=$cate->title;
			$out[$title]["title"]=$cate->title;
			$out[$title]["link"]="/posts-categories/".$cate->link;
			$out[$title]["icon"]=$cate->option["icon"]??"";
			$out[$title]["margin"]=(count(PostsCategories::grandparents($id))*3);
			if(!empty($child)){
				$out=array_merge($out, categoriesList($child,false));
			}
		}
		return $out;
	}
	function navbarAddItem($title, $show, $params, $id="", $header=""){
		$out='
		<div class="panel panel-default">
			<div class="heading link '.($show ? 'panel-actived' : '').'">'.$title.'</div>
			<div data-id="'.$id.'" class="panel-body hidden" style="'.($show ? 'display: block' : '').'">
			'.$header.'
			<div>
		';
		foreach($params as $title=>$data){
			$out.='
			<div data-link="'.$data["link"].'" class="alert-info link navbar-add"'.(isset($data["margin"]) ? 'style="margin-left: '.$data["margin"].'0px"' : '').'>
				'.$title.' <i class="float-right fa fa-plus"></i>
				<textarea class="hidden">'.json_encode($data).'</textarea>
			</div>
			';
		}
		$out.='
		</div>
		</div>
		</div>
		';
		return $out;
	}
@endphp
{!!
OP("navbar","Navbar menu đầu trang", [
	["html"=>
		call_user_func(function(){
			//Thêm menu
			$out='<section class="width-35 navbar-left" style="float: left">';
			$out.='
			'.navbarAddItem("Thêm nhanh", true, [
				"Liên kết tùy chỉnh"=>["link"=>"", "title"=>"", "icon"=>""],
				"Trang chủ"=>["link"=>"/", "title"=>"Trang chủ", "icon"=>"fa-home"]
			]).'
			';
			$categories=[];
			foreach(PostsCategories::select("id")->where("parent",0)->get() as $parent){
				$categories=array_merge($categories, categoriesList($parent->id));
			}
			$out.=navbarAddItem("Chuyên mục", false, $categories);
			$postsItem=[];
			$findPosts=isset($_POST["findPosts"]) ? [["title", "LIKE", "%{$_POST["findPosts"]}%"]] : "";
			foreach(Posts::where("id", ">", 0)->where($findPosts)->orderBy("id", "DESC")->limit(10)->get() as $p){
				$post[$p->title]["title"]=$p->title;
				$post[$p->title]["link"]="/".$p->link;
				$postsItem=array_merge($postsItem, $post);
			}
			$out.=navbarAddItem("Bài viết", false, $postsItem, "posts", '<input class="width-100 form-mrg navbar-find-posts" type="text" placeholder="Tìm bài viết" />');
			$out.='</section>';

			//Danh sách menu
			$out.='
			<section class="width-65 navbar-right" style="float: left">
				<div class="sortable-parent navbar-manager" data-type="navbar">
			';
			$out.='
			<input type="hidden" name="storage[option][navbar]" value="" />
			';
			$data=Storage::option("navbar");
			if(!is_array($data)){
				$data=[];
			}
			$data["_i_"]="";
			foreach($data as $i=>$item){
				$out.=createFormTemplate($i, $item);
			}
			$out.='</div></section>';
			$out.='
			<div class="clearfix"></div>
			';
			Assets::footer("/assets/sortable/script.js", "/assets/sortable/style.css", "/assets/admin/js/navbar-manager.js");
			$out.='
			<style>
			.navbar-manager,
			.navbar-sub,
			.navbar-categories{
				margin: 0;
				padding: 0;
				list-style-type: none
			}
			.navbar-manager .panel>.heading.link:after{
				display: none !important
			}
			.navbar-manager .navbar-action:hover{
				color: red
			}
			.navbar-sub{
				padding-left: 30px
			}
			</style>
			';
			return $out;
		})
	]
], "980")
!!}

{!!
OP("blog_navbar","Navbar trang blog", [
	["html"=>
		call_user_func(function(){
			//Thêm menu
			$out='<section class="width-35 navbar-left" style="float: left">';
			$out.='
			'.navbarAddItem("Thêm nhanh", true, [
				"Liên kết tùy chỉnh"=>["link"=>"", "title"=>"", "icon"=>""],
				"Trang chủ"=>["link"=>"/", "title"=>"Trang chủ", "icon"=>"fa-home"]
			]).'
			';
			$categories=[];
			foreach(PostsCategories::select("id")->where("parent",0)->get() as $parent){
				$categories=array_merge($categories, categoriesList($parent->id));
			}
			$out.=navbarAddItem("Chuyên mục", false, $categories);
			$postsItem=[];
			$findPosts=isset($_POST["findPosts"]) ? [["title", "LIKE", "%{$_POST["findPosts"]}%"]] : "";
			foreach(Posts::where("id", ">", 0)->where($findPosts)->orderBy("id", "DESC")->limit(10)->get() as $p){
				$post[$p->title]["title"]=$p->title;
				$post[$p->title]["link"]="/".$p->link;
				$postsItem=array_merge($postsItem, $post);
			}
			$out.=navbarAddItem("Bài viết", false, $postsItem, "posts", '<input class="width-100 form-mrg navbar-find-posts" type="text" placeholder="Tìm bài viết" />');
			$out.='</section>';

			//Danh sách menu
			$out.='
			<section class="width-65 navbar-right" style="float: left">
				<div class="sortable-parent navbar-manager" data-type="blog_navbar">
			';
			$out.='
			<input type="hidden" name="storage[option][blog_navbar]" value="" />
			';
			$data=Storage::option("blog_navbar");
			if(!is_array($data)){
				$data=[];
			}
			$data["_i_"]="";
			foreach($data as $i=>$item){
				$out.=createFormTemplate($i, $item, "blog_navbar");
			}
			$out.='</div></section>';
			$out.='
			<div class="clearfix"></div>
			';
			return $out;
		})
	]
], "980")
!!}

@php
	$pageNotFound=[];
	foreach( array_reverse(Storage::pageNotFound()) as $link=>$time){
		$pageNotFound[]=["html"=>'
		<div class="menu bd-bottom" title="'.date("H:i - d/m/Y", $time).'"><a target="_blank" href="'.htmlEncode($link).'">'.htmlEncode($link).'</a></div>
		'];
	}
@endphp
{!!
	ST("pageNotFound","Báo Lỗi trang không tồn tại", $pageNotFound, "500")
!!}

{!!
OP("website_","Thiết lập chung",[
	["type"=>"switch", "name"=>"robots", "title"=>"Cho phép Google bot lập chỉ mục (nên bật)", "value"=>1],
	["type"=>"switch", "name"=>"home_document", "title"=>"Hiện mục tài liệu hướng dẫn tại trang chủ", "value"=>1],
])
!!}

{!!
OP("contact_","Nút liên hệ",[
	["type"=>"switch", "name"=>"contact_form", "title"=>"Form yêu cầu gọi lại", "value"=>1],
	["html" =>
		Form::itemManager([
			"data"     => Storage::setting("contact_button"),
			"name"     => "storage[setting][contact_button]",
			"sortable" => true,
			"max"      => 20,
			"form"     => [
				["type"=>"text", "name"=>"title", "title"=>"Tiêu đề nút", "note"=>"VD: Chat qua Zalo", "value"=>"", "attr"=>''],
				["type"=>"image", "name"=>"icon", "title"=>"Ảnh icon", "value"=>"", "post"=>0],
				["type"=>"text", "name"=>"link", "title"=>"Liên kết", "note"=>"VD: https://zalo.me/xxxx", "value"=>"", "attr"=>''],
				["type"=>"color", "name"=>"background", "title"=>"Màu nền nút", "default"=>"#FF00CC", "value"=>"#FF00FF", "required"=>true],
				["type"=>"switch", "name"=>"newTab", "title"=>"Mở link trang tab mới", "value"=>1],
			]
		])
	]
])
!!}

{!!
OP("header_info_","Thông tin đầu trang",[
	["type"=>"switch", "name"=>"enable", "title"=>"Kích hoạt", "value"=>1],
	["type"=>"text", "name"=>"slogan", "title"=>'Khẩu hiệu', "note"=>"", "value"=>"", "attr"=>''],
	["type"=>"text", "name"=>"phone", "title"=>'Số điện thoại', "note"=>"", "value"=>"", "attr"=>''],
	["type"=>"text", "name"=>"email", "title"=>'Email', "note"=>"", "value"=>"", "attr"=>''],
])
!!}

{!!
OP("online_chat_","Chat online",[
	["type"=>"switch", "name"=>"enable", "title"=>"Kích hoạt", "value"=>1],
	["type"=>"text", "name"=>"one_signal_app_id", "title"=>'App id thông báo (onesignal.com)', "note"=>"", "value"=>"", "attr"=>''],
])
!!}

{!!
OP("facebook_","Facebook Apps",[
	["type"=>"text", "name"=>"appID", "title"=>'<p>Facebook AppID để quản lý bình luận<br/><a target="_blank" href="https://developers.facebook.com/apps/">Tạo & lấy appID</a></p>', "note"=>"", "value"=>"", "attr"=>''],
	["type"=>"text", "name"=>"lang", "title"=>'Mã ngôn ngữ (vi_VN,en_US...)', "note"=>"", "value"=>"vi_VN", "attr"=>''],
	["type"=>"textarea", "name"=>"messenger", "title"=>"Nút gửi tin nhắn messenger", "note"=>"Dán mã vào đây", "value"=>"", "attr"=>'', "full"=>true],
])
!!}

@php
	$htmlTagForm = [
		'inner_header' => '<i class="fa fa-code"></i> Trong thẻ &lt;head&gt;',
		'outer_header' => '<i class="fa fa-level-up"></i> Trên đầu trang',
		'footer'       => '<i class="fa fa-level-down"></i> Cuối trang'
	];
	$htmlTagFormHTML = '';
	foreach($htmlTagForm as $name => $title){
		$htmlTagFormHTML .= '
			<section class="panel panel-default">
				<div class="heading link">
					<b>
						'.$title.'
					</b>
				</div>
				<div class="panel-body hidden">
					'.Form::itemManager([
						"data"     => Storage::option("html_tag_".$name),
						"name"     => "storage[option][html_tag_{$name}]",
						"sortable" => true,
						"max"      => 20,
						"form"     => [
							["type"=>"text", "name"=>"title", "title"=>'Tên dễ nhớ', "note"=>"", "value"=>"", "attr"=>''],
							["type"=>"textarea", "name"=>"content", "title"=>"Mã tùy chỉnh...", "note"=>"Dán mã vào đây", "value"=>"", "attr"=>'style="height: 180px"', "full"=>true],
							[
								"type"   => "select",
								"name"   => "filter_path_type",
								"title"  => "",
								"option" => [
									"excluded" => "Không hiện tại các trang:",
									"included" => "Chỉ hiện tại các trang:",
								],
								"value" => "excluded"
							],
							["type"=>"textarea", "name"=>"filter_path", "title"=>"", "note"=>"/admin*
/trang-1
/trang-2", "value"=>"", "attr"=>'', "full"=>true],
						],
					]).'
				</div>
			</section>
		';
	}
@endphp
{!!
OP("html_tag_","Mã HTML tùy chỉnh",[
	["html" => $htmlTagFormHTML],
])
!!}

{!!
ST("payment_","Phương thức thanh toán",[
	["html" => '<div class="alert-info">Tài khoản ngân hàng</div>'],
	["html" =>
		Form::itemManager([
			"data"=>Storage::setting("banks"),
			"name"=>"storage[setting][banks]",
			"sortable"=>true,
			"max"=>20,
			"form"=>[
				["type"=>"image", "name"=>"image", "title"=>"Logo ngân hàng", "value"=>"", "post"=>0],
				["type"=>"text", "name"=>"name", "title"=>"Tên ngân hàng", "note"=>"Vietinbank", "value"=>"", "attr"=>''],
				["type"=>"text", "name"=>"user", "title"=>"Chủ tài khoản", "note"=>"Họ và tên", "value"=>"", "attr"=>''],
				["type"=>"text", "name"=>"number", "title"=>"Số tài khoản", "note"=>"100000", "value"=>"", "attr"=>''],
				["type"=>"text", "name"=>"office", "title"=>"Chi nhánh", "note"=>"Hoàn Kiếm, Hà Nội", "value"=>"", "attr"=>''],
			],
			"setKeyFromName"=>true
		])
	],
	["html" => '<div class="alert-info">Ví điện tử</div>'],
	["html" =>
		Form::itemManager([
			"data"     => Storage::setting("online_wallet"),
			"name"     => "storage[setting][online_wallet]",
			"sortable" => true,
			"max" => 20,
			"form" =>[
				["type"=>"text", "name"=>"name", "title"=>"Tên ví", "note"=>"VD: Momo", "value"=>"", "attr"=>''],
				["type"=>"image", "name"=>"image", "title"=>"Logo ví", "value"=>"", "post"=>0],
				["type"=>"textarea", "name"=>"content", "title"=>"Nội dung", "note"=>"Nội dung ({user_id} - ID khách hàng)", "value"=>"", "attr"=>'', "full"=>true],
			],
			"setKeyFromName"=>true
		])
	],
	["type"=>"textarea", "name"=>"note", "title"=>"Ghi chú chuyển khoản", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],

])
!!}


{!!
OP("other_option_","Thiết lập khác",[
	["type"=>"text", "name"=>"prefix_phone", "title"=>'Thêm đầu số vào trước SĐT CSKH', "note"=>"Ví dụ: 1599", "value"=>"", "attr"=>''],
])
!!}