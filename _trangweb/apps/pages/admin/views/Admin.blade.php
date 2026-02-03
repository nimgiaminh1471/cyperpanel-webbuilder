@php
ob_start();
define("PAGE", [
	"name"        => "Admin Panel",
	"title"       => "",
	"description" => "",
	"loading"     => 0,
	"background"  => "",
	"image"       => "",
	"canonical"   => null,
	"robots"      => "noindex,nofollow"
]);
define("THEME_INFO", '
	<section class="section" style="margin-bottom: 0">
		<div class="heading">Thông tin giao diện</div>
		<div class="section-body ">
			<div class="flex pd-5">
				<div style="width: 80px">Copyright</div>
				<div><b>'.ucfirst(DOMAIN).'</b></div>
			</div>
			<div class="flex pd-5">
				<div style="width: 80px">Developer</div>
				<div><b>Kinhdoanhweb.net</b></div>
			</div>
		</div>
	</section>
');

$panelInfo   = Storage::panel_info();
$panelParent = $panelInfo[PANEL_NAME]["parent"]??"";

$_Config=[
	"title"   => $panelInfo[PANEL_NAME]["title"] ?? "Admin Control Panel",
	"loading" => 2
];


@endphp

@extends("Admin")









@section("head-tag")
	{!! Assets::show("/assets/admin/css/style__complete.css", "/assets/admin/js/main.js") !!}
@endsection




@section("container")
@php
	//Dữ liệu option dùng cho toàn trang
	function OP($prefix, $heading, $form, $maxWidth="", $bg=true, $name="option"){
		$op=Form::create([
			"form"=>$form,
			"function"=>"Storage::$name",
			"prefix"=>$prefix,
			"name"=>"storage[".$name."]",
			"class"=>"form-padding",
			"hover"=>true
		]);
		
		//Header
		$out='<div class="'.($bg ? 'section': 'section" style="background-color: transparent').'" id="adminBody_'.$prefix.'">';
		
		if($maxWidth>50){
			//Nội dung ẩn
			$out.='<div data-modal="adminBody_'.$prefix.'" class="heading link modal-click">'.$heading.' <i class="right-icon fa fa-chevron-right"></i></div>';
			$out.=modal($heading, '<div class="bg">'.$op.'</div>', 'adminBody_'.$prefix, $maxWidth.'px');
		}else{
			//Nội dung hiện
			$out.='
			<div class="heading">'.$heading.'</div>
			<div>'.$op.'</div>
			';
		}

		$out.='</div>';
		//Footer

		return $out;
	}

	//Dữ liệu setting chỉ dùng cho trang cài đặt
	function ST($prefix, $heading, $form, $maxWidth="", $bg=true){
		return OP($prefix, $heading, $form, $maxWidth, $bg, "setting");
	}


	require_once(Route::path("/views/Config.php"));

	//Setup
	if( empty(Storage::option()) ){
		$setupContent='
		'.THEME_INFO.'
		<div class="center rm-radius width-100 alert-info" id="adminPanelPendingSetup">Đang tiến hành khởi tạo: <i></i>/<i></i></div>
		';
		echo modal('', $setupContent, 'setup', '600px', true, false, true);
	}
@endphp

<link rel="stylesheet"
		  href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">


<div class="admin admin-theme-{{ Storage::setting('admin_panel_theme', 'light') }}">
<div class="admin-header flex flex-middle">
	<div class="admin-header-left">
		<div class="flex flex-middle">
			<a class="admin-collapse-icon center hidden-large" onclick="adminLeftCollapse()" style="width: 50px">
				<i class="fa fa-navicon"></i>
			</a>
			<a class="logo-outer" href="/" style="width: calc(100% - 50px)">
				{!! (empty(Storage::setting("admin_panel_logo")) ? ucwords(DOMAIN) : '<img class="logo" src="'.Storage::setting("admin_panel_logo").'" alt="'.ucwords(DOMAIN).'">') !!}
			</a>
			<a class="admin-collapse-icon center hidden-small hidden-medium" onclick="adminLeftCollapse()" style="width: 50px">
				<i class="fa fa-navicon"></i>
			</a>
		</div>
	</div>
	<div style="width: 50%">
		<div class="flex flex-middle" style="justify-content: flex-end;">
			<div class="header-notify-icon">
				{{-- <a>
					<i class="fa fa-bell-o"></i>
				</a> --}}
				<a onclick="headerShowNotify()" data-id="notify">
					<i class="fa fa-envelope-o"></i>
					<sub>10</sub>
				</a>
			</div>
			<div class="admin-header-user">
				<span style="padding: 10px">
					<span class="user-avatar"><img src="{!! user("avatar") !!}" /></span>
					<span class="hidden-small">{{user("name")}}</span>
					<i class="fa fa-angle-down"></i>
				</span>
				<nav>
					@if( permission("website_manager|admin|accountant") )
						<a href="/user/profile">
							<i class="fa fa-icon fa-user"></i>
							Thông tin cá nhân
						</a>
						<a href="/user/profile/edit">
							<i class="fa fa-icon fa-edit"></i>
							Sửa thông tin
						</a>
						<a href="/user/profile/password">
							<i class="fa fa-icon fa-lock"></i>
							Đổi mật khẩu
						</a>
						<a href="/user/profile/avatar">
							<i class="fa fa-icon fa-image"></i>
							Đổi ảnh đại diện
						</a>
					@else
						<a href="/user/profile">
							<i class="fa fa-icon fa-user"></i>
							Thông tin cá nhân
						</a>
						<a href="/admin/Recharge" title="Số tiền" style="position: relative;">
							<i class="fa fa-icon fa-dollar"></i>
							Quỹ: 
							<b>{{number_format( user("money") )}}</b> ₫ <span class="right-icon label-info" style="right: 5px"><i class="fa fa-plus"></i></span>
						</a>
						<a href="/admin/PaymentHistory">
							<i class="fa fa-icon fa-history"></i>
							Lịch sử giao dịch
						</a>
						<a href="javascript:showModal('send-request')">
							<i class="fa fa-icon fa-comments-o"></i>
							Gửi yêu cầu
						</a>
						<a href="/tai-lieu-huong-dan" target="_blank">
							<i class="fa fa-icon fa-question"></i>
							Trợ giúp
						</a>
					@endif
					<a onclick="return confirm('Bạn có muốn thoát tài khoản?');" href="/user/profile/logout">
						<i class="fa fa-icon fa-sign-out"></i>
						Đăng xuất
					</a>
				</nav>
			</div>
		</div>
	</div>
</div>






<!--Menu bên trái-->
<div class="admin-left {{ ( empty($_COOKIE['admin_left_menu_min'] ?? true) ? 'admin-min' : '') }}">
<div class="admin-scrollbar">

@php
function createPanelLink($args){
	extract($args);
	if( !permission($permission) ){ return; }
	if( GET("panelShow")==$panel ){
		echo '<script>window.history.replaceState({}, document.title, "'.THIS_LINK.'");</script>';
	}
	$active="";

	if($tag=="a"){
		if(isset($link)){
			$href='target="_blank" href="'.$link.'"';
		}else{
			$href='href="/admin/'.$panel.'"';
		}
	}else if( $panel == 'Dashboard'){
		$tag="a";
		$href='href="/admin"';
	}
	if(isset($hidden)){
		return;
	}
	return '<'.$tag.' '.($href ?? '').' data-id="'.$panel.'" class="'.$class.' '.($active ?? '').'" style="'.(empty($color) ? '' : 'color:'.$color.';').'">
		<i class="fa '.Storage::setting('panel_icon_'.$panel.'',(empty($icon) ? '' : $icon)).'"></i>
		<span>'.$title.''.(!empty($count) ? '<small '.( isset($count_style) ? 'style="background: tomato"' : '' ).'>'.$count.'</small>' : '').'</span>
		</'.$tag.'>';
}
@endphp
@foreach($option as $id=>$tab)

	@if($tab['type']=='sub')
		<!--Sub menu-->
		{!! createPanelLink( array_merge(["tag"=>"nav","class"=>"admin-has-sub admin-arrow-right ".(GET('panelShow')==$tab['panel'] || $panelParent==$tab['panel'] ? 'admin-arrow-down' : '').""],$tab) ) !!}

		<ol id="adminSub-{{$tab['panel']}}" style="display: {{ (GET('panelShow')==$tab['panel'] || $panelParent==$tab['panel'] ? 'block' : '') }}">
		@foreach($tab['sub'] as $sid=>$sub)
			@if(isset($sub["type"]))
				{!! createPanelLink( array_merge(["tag"=>"a","parent"=>$tab['panel'],"class"=>(PANEL_NAME==$sub['panel'] ? 'admin-item admin-actived' : 'admin-item')],$sub) ) !!}
			@else
				{!! createPanelLink( array_merge(["tag"=>"li","class"=>"admin-item"],$sub) ) !!}
			@endif
			@php $opname[$sub['panel']]=["title"=>$sub['title'],"parent"=>$tab["panel"]]; @endphp
		@endforeach
		</ol>	

	@elseif($tab["type"]=="link")
		<!--Ấn sang trang mới-->
		{!! createPanelLink( array_merge(["tag"=>"a","class"=>(PANEL_NAME==$tab['panel'] ? 'admin-actived' : '')],$tab) ) !!}
	@else
		<!--Menu mặc định-->
		{!! createPanelLink( array_merge(["tag"=>"nav","class"=>($id==0 && empty(PANEL_NAME) && empty($_GET) || GET("panelShow", "n")==$tab['panel'] ? 'admin-actived' : '')],$tab) ) !!}
	@endif

@php $opname[$tab['panel']]=["title"=>$tab['title'],"parent"=>""]; @endphp
@endforeach
@php
	Storage::update('panel_info', $opname);
@endphp
	
@if( empty($_COOKIE['admin_left_menu_min'] ?? true) )
	<div class="admin-collapse hidden-medium hidden-small" onclick="adminLeftPin()">
		<i class="fa fa-expand"></i>
		<span>Cố định menu</span>
	</div>
@else
	<div class="admin-collapse hidden-medium hidden-small" onclick="adminLeftPin()">
		<i class="fa fa-compress"></i>
		<span>Thu gọn menu</span>
	</div>
@endif
<div class="admin-collapse hidden-large" onclick="adminLeftCollapse()">
	<i class="fa fa-compress"></i>
	<span>Thu gọn menu</span>
</div>
</div>
</div>

<!--/Menu bên trái-->
 
 
 
 
 
 
 
@php
function createPanelBody($_args,$display,$panelParent="", $sid=""){
	if( !permission($_args["permission"]) ){ return; }
	if(empty($_args["type"])){ $_args["type"]="default"; }
	if( GET("panelShow")==$_args["panel"] ){
		$display="block";
	}
	
	if( PANEL_NAME==$_args["panel"] ){
		$display="block";
		$_out=1;
	}else if(empty( PANEL_NAME ) && $_args["type"]!="link"){
		$_out=1;
	}
	if(empty($panelParent)){
		$panel=$_args["panel"];
	}else{
		$panel="$panelParent/{$_args["panel"]}";
	}
	if(isset($_out)){
		ob_start();
		echo '<div class="admin-container" data-id="'.$_args["panel"].'" id="adminContainer-'.$_args["panel"].'" style="display:'.$display.'">';
       	view("panel/$panel");
        echo '</div>';
   		return ob_get_clean();
	}
}
if( !empty(GET('panelShow')) ){
	echo '
	<script>
		//$("#loading").show();
		$(document).ready(function(){
			setTimeout(function(){
				$("#loading").hide();
				var el=$(".admin-left ol>li[data-id=\''.GET('panelShow').'\']");
				if(el.length>0){
					el[0].click();
				}
			}, 500);
		});
	</script>
	';
}
@endphp
<!--Nội dung bên phải-->


<div class="admin-right {{ ( empty($_COOKIE['admin_left_menu_min'] ?? true) ? 'admin-min' : '') }}">
@foreach($option as $id=>$tab)
	@if($tab['type']=='sub')
		@if(empty(PANEL_NAME) || $panelParent==$tab["panel"] )
			<section class="admin-section" style="display: {{empty(PANEL_NAME)  ? 'none' : 'block'}}">
				{!!$tab['header'] ?? ""!!}
				@foreach($tab['sub'] as $sid=>$sub)
					{!!createPanelBody($sub,"none",$tab["panel"], $sid)!!}
				@endforeach
				{!!$tab['footer'] ?? ""!!}
	 		</section>
 		@endif
	@else
		{!!createPanelBody($tab,($id==0 && empty($_GET) ? 'block' : 'none'))!!}
 	@endif
@endforeach

</div>

<!--/Nội dung bên phải-->

</div>
<div class="clear"></div>

@endsection