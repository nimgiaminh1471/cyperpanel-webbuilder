<?php
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
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="language" content="Vietnamese" /><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo htmlEncode( $_Config['title'] ); ?></title>
<?php echo Assets::show("/assets/general/css/style__complete.css", "/assets/general/css/widgets/".Route::folder().".css", "/assets/library/jquery-3.3.1.min.js", "/assets/library/font-awesome-4.7.0/css/font-awesome.min.css"); ?>
<link rel="shortcut icon" type="image/png" href="<?php echo htmlEncode(Storage::option("theme_header_favicon")); ?>" />
<link rel="icon" type="image/png" href="<?php echo htmlEncode(Storage::option("theme_header_favicon")); ?>" />
	<?php echo  Assets::show("/assets/admin/css/style__complete.css", "/assets/admin/js/main.js") ; ?>
<?php echo  htmlCustom('inner_header') ; ?>
</head>
<body>
<?php echo  htmlCustom('outer_header') ; ?>
<?php echo htmlEncode( deleteWebsiteExpired() ); ?>
<?php
	global $Schedule;
	$Schedule->d( function(){
		sendNotificationWebsiteExpired();
	});
?>
<?php if($_Config['loading']): ?>
<div id="loading" class="hidden">
	<div>
		<div>
			<i class="fa fa-spinner fa-pulse fa-4x fa-fw primary-color"></i>
		</div>
	</div>
</div>
<?php endif; ?>
<?php
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
?>
<link rel="stylesheet"
		  href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
<div class="admin admin-theme-<?php echo htmlEncode( Storage::setting('admin_panel_theme', 'light') ); ?>">
<div class="admin-header flex flex-middle">
	<div class="admin-header-left">
		<div class="flex flex-middle">
			<a class="admin-collapse-icon center hidden-large" onclick="adminLeftCollapse()" style="width: 50px">
				<i class="fa fa-navicon"></i>
			</a>
			<a class="logo-outer" href="/" style="width: calc(100% - 50px)">
				<?php echo  (empty(Storage::setting("admin_panel_logo")) ? ucwords(DOMAIN) : '<img class="logo" src="'.Storage::setting("admin_panel_logo").'" alt="'.ucwords(DOMAIN).'">') ; ?>
			</a>
			<a class="admin-collapse-icon center hidden-small hidden-medium" onclick="adminLeftCollapse()" style="width: 50px">
				<i class="fa fa-navicon"></i>
			</a>
		</div>
	</div>
	<div style="width: 50%">
		<div class="flex flex-middle" style="justify-content: flex-end;">
			<div class="header-notify-icon">
				
				<a onclick="headerShowNotify()" data-id="notify">
					<i class="fa fa-envelope-o"></i>
					<sub>10</sub>
				</a>
			</div>
			<div class="admin-header-user">
				<span style="padding: 10px">
					<span class="user-avatar"><img src="<?php echo  user("avatar") ; ?>" /></span>
					<span class="hidden-small"><?php echo htmlEncode(user("name")); ?></span>
					<i class="fa fa-angle-down"></i>
				</span>
				<nav>
					<?php if( permission("website_manager|admin|accountant") ): ?>
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
					<?php else: ?>
						<a href="/user/profile">
							<i class="fa fa-icon fa-user"></i>
							Thông tin cá nhân
						</a>
						<a href="/admin/Recharge" title="Số tiền" style="position: relative;">
							<i class="fa fa-icon fa-dollar"></i>
							Quỹ: 
							<b><?php echo htmlEncode(number_format( user("money") )); ?></b> ₫ <span class="right-icon label-info" style="right: 5px"><i class="fa fa-plus"></i></span>
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
					<?php endif; ?>
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
<div class="admin-left <?php echo htmlEncode( ( empty($_COOKIE['admin_left_menu_min'] ?? true) ? 'admin-min' : '') ); ?>">
<div class="admin-scrollbar">
<?php
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
?>
<?php foreach($option as $id=>$tab): ?>
	<?php if($tab['type']=='sub'): ?>
		<!--Sub menu-->
		<?php echo  createPanelLink( array_merge(["tag"=>"nav","class"=>"admin-has-sub admin-arrow-right ".(GET('panelShow')==$tab['panel'] || $panelParent==$tab['panel'] ? 'admin-arrow-down' : '').""],$tab) ) ; ?>
		<ol id="adminSub-<?php echo htmlEncode($tab['panel']); ?>" style="display: <?php echo htmlEncode( (GET('panelShow')==$tab['panel'] || $panelParent==$tab['panel'] ? 'block' : '') ); ?>">
		<?php foreach($tab['sub'] as $sid=>$sub): ?>
			<?php if(isset($sub["type"])): ?>
				<?php echo  createPanelLink( array_merge(["tag"=>"a","parent"=>$tab['panel'],"class"=>(PANEL_NAME==$sub['panel'] ? 'admin-item admin-actived' : 'admin-item')],$sub) ) ; ?>
			<?php else: ?>
				<?php echo  createPanelLink( array_merge(["tag"=>"li","class"=>"admin-item"],$sub) ) ; ?>
			<?php endif; ?>
			<?php $opname[$sub['panel']]=["title"=>$sub['title'],"parent"=>$tab["panel"]]; ?>
		<?php endforeach; ?>
		</ol>	
	<?php elseif($tab["type"]=="link"): ?>
		<!--Ấn sang trang mới-->
		<?php echo  createPanelLink( array_merge(["tag"=>"a","class"=>(PANEL_NAME==$tab['panel'] ? 'admin-actived' : '')],$tab) ) ; ?>
	<?php else: ?>
		<!--Menu mặc định-->
		<?php echo  createPanelLink( array_merge(["tag"=>"nav","class"=>($id==0 && empty(PANEL_NAME) && empty($_GET) || GET("panelShow", "n")==$tab['panel'] ? 'admin-actived' : '')],$tab) ) ; ?>
	<?php endif; ?>
<?php $opname[$tab['panel']]=["title"=>$tab['title'],"parent"=>""]; ?>
<?php endforeach; ?>
<?php
	Storage::update('panel_info', $opname);
?>
	
<?php if( empty($_COOKIE['admin_left_menu_min'] ?? true) ): ?>
	<div class="admin-collapse hidden-medium hidden-small" onclick="adminLeftPin()">
		<i class="fa fa-expand"></i>
		<span>Cố định menu</span>
	</div>
<?php else: ?>
	<div class="admin-collapse hidden-medium hidden-small" onclick="adminLeftPin()">
		<i class="fa fa-compress"></i>
		<span>Thu gọn menu</span>
	</div>
<?php endif; ?>
<div class="admin-collapse hidden-large" onclick="adminLeftCollapse()">
	<i class="fa fa-compress"></i>
	<span>Thu gọn menu</span>
</div>
</div>
</div>
<!--/Menu bên trái-->
 
 
 
 
 
 
 
<?php
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
?>
<!--Nội dung bên phải-->
<div class="admin-right <?php echo htmlEncode( ( empty($_COOKIE['admin_left_menu_min'] ?? true) ? 'admin-min' : '') ); ?>">
<?php foreach($option as $id=>$tab): ?>
	<?php if($tab['type']=='sub'): ?>
		<?php if(empty(PANEL_NAME) || $panelParent==$tab["panel"] ): ?>
			<section class="admin-section" style="display: <?php echo htmlEncode(empty(PANEL_NAME)  ? 'none' : 'block'); ?>">
				<?php echo $tab['header'] ?? ""; ?>
				<?php foreach($tab['sub'] as $sid=>$sub): ?>
					<?php echo createPanelBody($sub,"none",$tab["panel"], $sid); ?>
				<?php endforeach; ?>
				<?php echo $tab['footer'] ?? ""; ?>
	 		</section>
 		<?php endif; ?>
	<?php else: ?>
		<?php echo createPanelBody($tab,($id==0 && empty($_GET) ? 'block' : 'none')); ?>
 	<?php endif; ?>
<?php endforeach; ?>
</div>
<!--/Nội dung bên phải-->
</div>
<div class="clear"></div>
<?php echo Assets::show("/assets/general/js/script.js"); ?>
<style type="text/css">
	.admin-collapse{
		z-index: 199721 !important
	}
</style>
<?php echo  htmlCustom('footer') ; ?>
<?php echo classes\FixedButton::show(false); ?>
<?php echo Assets::show(); ?>
<section class="alert alert-success" id="alert-fixed" onclick="$(this).hide()"></section>
<script type="text/javascript">
	<?php if( !empty( SESSION('notify')['msg'] ) ): ?>
		showNotify("<?php echo htmlEncode( SESSION('notify')['status'] ); ?>", "<?php echo htmlEncode( SESSION('notify')['msg'] ); ?>");
		<?php
			unset($_SESSION['notify']);
		?>
	<?php endif; ?>
	<?php if( !empty( user('notify_to_manager') ) ): ?>
		$.get('/user/sendNotifyToManager'); // Gửi thông báo tài khoản mới tới quản lý
	<?php endif; ?>
</script>
<?php
if( permission("admin") ){
	//Ghi css 
	echo Widget::css("", true);
}
if( permission("member") && empty( user("check_admin") ) ){
	\models\Users::where("id", user("id") )->update(["check_admin"=>md5( randomString(20) )]); // Cập nhật key đăng nhập web con
}
?>
</body>
</html>