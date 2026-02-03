<?php
	define("PAGE", [
		"name"        =>"Blog",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"index,follow"
	]);
	Assets::footer("/assets/general/js/blog.js");
?>
<?php
	ob_start();
	define("PAGE_OPTION", Storage::option( Route::folder() ));
	$pageConfig=PAGE;
	foreach($pageConfig as $key=>$value){
		if(empty($value)){
			$emptyValue=$key=="title" || $key=="description" ? PAGE["name"] : "";
			$value=PAGE_OPTION[$key]??$emptyValue;
			$pageConfig[$key]=$value;
		}
	}
	$pageConfig["title"]=cutWords($pageConfig["title"], 20).''.(GET("page")>1 ? ' '.__("- Trang").' '.GET("page").'' : '');
	$pageConfig["description"]=cutWords($pageConfig["description"], 35);
	$pageConfig["image"]=empty($pageConfig["image"]) ? HOME."".Storage::setting("theme__general_social_share_poster") : $pageConfig["image"];
	if( Storage::option("website_robots", 1)==0 ){
		$pageConfig["robots"]="noindex,nofollow";
	}
	deleteWebsiteExpired();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="vi"<?php echo empty($pageConfig["background"]) ? '' : ' style="background-image: url('.$pageConfig["background"].'); background-size: cover;
    background-repeat: no-repeat;
    background-position: 0 0;"'; ?>>
<head>
	<meta name="language" content="Vietnamese" />
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php echo  $pageConfig["title"] ; ?></title>
	<meta name="description" content="<?php echo $pageConfig["description"]; ?>" />
	<meta property="og:title" content="<?php echo $pageConfig["title"]; ?>"/>
	<meta property="og:description" content="<?php echo $pageConfig["description"]; ?>" />
	<meta property="og:url" content="<?php echo  empty($pageConfig["canonical"]) ? THIS_URL : $pageConfig["canonical"] ; ?>">
	<meta property="og:type" content="article">
	<meta property="og:image" content="<?php echo $pageConfig["image"]; ?>" />
	<?php echo  empty( Storage::option("facebook_appID") ) ? '' : '<meta property="fb:app_id" content="'.Storage::option("facebook_appID").'" />' ; ?>
	<?php echo  empty($pageConfig["canonical"]) ? '' : '<link rel="canonical" href="'.$pageConfig["canonical"].'" />' ; ?>
	<meta name="robots" content="<?php echo htmlEncode($pageConfig["robots"]??"index,follow"); ?>" />
	<?php echo  Assets::show("/assets/general/css/style__complete.css", "/assets/general/css/widgets/".Route::folder().".css", "/assets/library/jquery-3.3.1.min.js", "/assets/library/font-awesome-4.7.0/css/font-awesome.min.css") ; ?>
	<link rel="shortcut icon" type="image/png" href="<?php echo htmlEncode(Storage::option("theme_header_favicon")); ?>" />
	<link rel="icon" type="image/png" href="<?php echo htmlEncode(Storage::option("theme_header_favicon")); ?>" />
	
	<?php if( Storage::option('online_chat_enable') ): ?>
		<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
		<script>
			var OneSignal = window.OneSignal || [];
			OneSignal.push(function() {
				OneSignal.init({
					appId: "<?php echo htmlEncode( Storage::option('online_chat_one_signal_app_id') ); ?>",
				});
				OneSignal.on('subscriptionChange', function (isSubscribed) {
					OneSignal.push(function() {
						OneSignal.getUserId(function(userId) {
							if(userId!=null){
								setCookie("notificationsPlayerID", userId, 30);
								$.get("/");
							}
						});
					});
				});
				<?php if( permission("member") ): ?>
					OneSignal.showSlidedownPrompt();
					<?php if( empty($_COOKIE["notificationsPlayerID"]) ): ?>
						OneSignal.getUserId(function(userId) {
							if(userId!=null){
								setCookie("notificationsPlayerID", userId, 30);
								$.get("/");
							}
						});
					<?php else: ?>
						<?php
							$notificationsPlayerIDS=unserialize( user("notifications_player_ids") );
							if( !is_array($notificationsPlayerIDS) ){
								$notificationsPlayerIDS=[];
							}
							if( !in_array($_COOKIE["notificationsPlayerID"], $notificationsPlayerIDS) ){
								array_push($notificationsPlayerIDS, $_COOKIE["notificationsPlayerID"]);
								$notificationsPlayerIDS=array_slice($notificationsPlayerIDS, -3);
								models\Users::where("id", user("id") )->update( ["notifications_player_ids"=>serialize($notificationsPlayerIDS) ]);
							}
						?>
					<?php endif; ?>
				<?php endif; ?>
			});
		</script>
	<?php endif; ?>
	<?php echo  Assets::show(
		"/assets/animate/aos.css",
		"/assets/animate/aos.js",
		"/assets/general/js/modal.js"
		) ; ?>
	<?php if( !permission("member") ): ?>
		<?php echo  Assets::footer(
			"/assets/account/register.js"
			) ; ?>
	<?php endif; ?>
	
	<?php echo  htmlCustom('inner_header') ; ?>
</head>
<body>
	<?php if( Storage::option('header_info_enable') ): ?>
		<div class="pd-10 primary-bg" id="header-info">
			<div class="main-layout">
				<div class="flex flex-medium">
					<div class="width-50 hidden-small">
						<?php echo htmlEncode( Storage::option('header_info_slogan') ); ?>
					</div>
					<div class="width-50 right">
						<?php if( Storage::option('header_info_email') ): ?>
							<a href="mailto:<?php echo htmlEncode( Storage::option('header_info_email') ); ?>" style="color: white; padding-right: 10px">
								<i class="fa fa-icon fa-envelope-o"></i>
								<?php echo htmlEncode( Storage::option('header_info_email') ); ?>
							</a>
						<?php endif; ?>
						<?php if( Storage::option('header_info_phone') ): ?>
							<a class="text-hotline" href="tel:<?php echo htmlEncode( vnStrFilter(Storage::option('header_info_phone'), '') ); ?>" style="color: white; padding-right: 10px">
								<i class="fa fa-icon fa-phone"></i>
								<?php echo htmlEncode( Storage::option('header_info_phone') ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if( !empty( Storage::option("facebook_appID") ) ): ?>
		<!-- Load Facebook SDK for JavaScript -->
		<div id="fb-root"></div>
		<script async defer crossorigin="anonymous" src="https://connect.facebook.net/<?php echo htmlEncode(Storage::option("facebook_lang")); ?>/sdk.js#xfbml=1&version=v3.2&appId=<?php echo htmlEncode(Storage::option("facebook_appID")); ?>&autoLogAppEvents=1"></script>
	<?php endif; ?>
	<header id="header">
		<div class="header-body flex-nowrap flex-middle">
			<div class="header-left">
				<a href="/"><?php echo  (empty(Storage::option("theme_header_logo")) ? ucwords(DOMAIN) : '<img class="logo" src="'.Storage::option("theme_header_logo").'" alt="'.ucwords(DOMAIN).'">') ; ?></a>
			</div>
			<div class="header-right">
				<div class="flex" style="flex-wrap: nowrap; justify-content: flex-end;">
					<div>
						<div class="overlay-menu"></div>
						<?php echo createNavbar( Storage::option("navbar", []) ); ?>
					</div>
					<div style="margin-left: 10px">
						<div class="flex" style="flex-wrap: nowrap;justify-content: flex-end;">
							<?php if(Storage::option("theme_header_search", 1)==1): ?>
								<div>
									<form medthod="GET" action="/search" class="input-search">
										<input placeholder="<?php echo htmlEncode(__("Tìm kiếm")); ?>" class="input" type="search" name="keyword" value="<?php echo htmlEncode(GET("keyword")); ?>" required="" />
										<button type="submit"><i class="fa fa-search"></i></button>
									</form>
								</div>
								<div data-show="input-search" class="hidden-large nav-icon-mobile">
									<i class="fa fa-search"></i>
								</div>
							<?php endif; ?>
							<div data-show="navbar" class="hidden-large nav-icon-mobile">
								<i class="fa fa-bars"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<div class="header-fixed"></div>
	<?php echo  htmlCustom('outer_header') ; ?>
<div id="loading" class="hidden">
	<div>
		<div>
			<i class="fa fa-spinner fa-pulse fa-4x fa-fw primary-color"></i>
		</div>
	</div>
</div>
<?php if($pageConfig["loading"]>0): ?>
<script>
	$(document).ready(function(){
		setTimeout(function(){
			$("#loading").hide();
		}, <?php echo htmlEncode($pageConfig["loading"]); ?>);
	});
</script>
<?php endif; ?>
<?php if( !empty(PAGE["breadcrumb"]) ): ?>
	<div class="main-layout breadcrumb-outer">
		<?php echo  breadcrumb(PAGE["breadcrumb"]) ; ?>
	</div>
<?php endif; ?>
	<?php echo  Widget::show("blog_header") ; ?>
	<div class="blog-navbar-wrap bg">
		<section class="main-layout">
			<div class="flex flex-middle">
				<div class="width-70">
					<div data-show="blog_navbar" class="hidden-large blog-nav-icon-mobile">
						<i class="fa fa-bars"></i> Danh mục
					</div>
					<?php echo  createBlogNavbar( Storage::option("blog_navbar", []), false, "flex flex-middle") ; ?>
				</div>
				<div class="width-30 right">
					<div class="width-100">
						<form medthod="GET" action="/search" class="input-search">
							<input placeholder="<?php echo htmlEncode(__("Tìm kiếm")); ?>" class="input" type="search" name="keyword" value="<?php echo htmlEncode(GET("keyword")); ?>" required="" style="width: 100%">
							<button type="submit"><i class="fa fa-search"></i></button>
						</form>
					</div>
				</div>
			</div>
		</section>
	</div>
	
	<?php echo Widget::show("outline_header", 1); ?>
	<main id="main">
		<?php echo Widget::show("main_header"); ?>
		
		<div class="flex flex-large">
			<?php if( empty(PAGE_OPTION["layout_mid"]) && permission("admin") ): ?>
				<div class="alert-danger width-100">Trang này chưa được thiết lập bố cục.<br/>Ấn nút <i class="fa fa-list-alt"></i> dưới trang->thiết lập nhanh->bố cục trang & lưu lại.</div>
			<?php endif; ?>
			<?php foreach(["left", "mid", "right"] as $section): ?>
				<?php if( (PAGE_OPTION["layout_".$section]??0)!=0 ): ?>
					<div class="flex-margin main-<?php echo htmlEncode($section); ?>">
						<?php echo Widget::show($section); ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<?php echo Widget::show("main_footer"); ?>
		
	</main>
	<?php echo Widget::show("outline_footer", 1); ?>
	<?php
		$adsPopup=Storage::option("ads_popup", []);
		foreach( explode("-", COOKIE("adsPopup") ) as $key){
			unset($adsPopup[$key]);
		}
	?>
	<?php if( !empty($adsPopup) ): ?>
		<?php echo Assets::show("/assets/general/js/popup-ads.js"); ?>
		<script>
			popupAds(<?php echo json_encode($adsPopup); ?>, 0);
		</script>
	<?php endif; ?>
	
	
	<?php if( !permission("member") ): ?>
		<div class="modal hidden account-outer modal-register-box modal-allow-close">
			<div class="modal-body" style="max-width: 450px">
				<div class="modal-content" style="background: transparent;">
					<div class="modal-form bg">
						<div class="heading">
							<div class="form-logo">
								<img src="<?php echo htmlEncode( Storage::option("theme_header_favicon") ); ?>">
							</div>
							ĐĂNG KÝ DÙNG THỬ
							<i class="modal-close link fa"></i>
						</div>
						<form class="form pd-15" method="POST" action="" id="account-register-form">
							<div class="center" style="margin-bottom: 10px">
								Trải nghiệm toàn bộ tính năng web với <?php echo htmlEncode( Storage::setting('builder_parameters_expired') ); ?> ngày miễn phí
							</div>
							<div class="modal-input">
								<input class="width-100 input" placeholder="Họ tên" type="text" name="register[name]" />
								<i class="fa fa-user"></i>
							</div>
							<div class="modal-input">
								<input class="width-100 input" placeholder="Số điện thoại" type="text" name="register[phone]"/>
								<i class="fa fa-phone"></i>
							</div>
							<div class="modal-input">
								<input class="width-100 input" placeholder="Email" type="email" name="register[email]"/>
								<i class="fa fa-envelope-o"></i>
							</div>
							<div class="modal-input">
								<input class="width-100 input" placeholder="Mật khẩu" type="password" name="register[password]"/>
								<i class="fa fa-lock"></i>
							</div>
							<div class="modal-input">
								<input class="width-100 input" placeholder="Nhập lại mật khẩu" type="password" id="account-register-password2"/>
								<i class="fa fa-lock"></i>
							</div>
							<div class="flex flex-middle">
								<div class="width-80">
									<input class="input width-100" name="register[captcha]" placeholder="Nhập mã xác minh" type="text"/>
								</div>
								<div class="width-20 center">
									<img id="account-register-captcha" src="/api/captcha.png?t=<?php echo htmlEncode( time() ); ?>" />
								</div>
							</div>
							<label class="check radio" style="margin-bottom: 5px">
								<input name="register[terms]" type="checkbox" value="1" checked="checked">
								<s></s>
								Tôi đồng ý với <a class="primary-color" target="_blank" href="/quy-dinh-su-dung">Quy định sử dụng</a> và <a class="primary-color" target="_blank" href="/chinh-sach-bao-mat">Chính sách bảo mật</a>
							</label>
							<div class="form-mrg hidden alert-danger" id="account-register-msg"></div>
							<div class="center">
								<input class="btn-primary width-100" id="account-register-submit" type="button" value="ĐĂNG KÝ"/>
							</div>
							<div class="pd-10 center">
								Đã có tài khoản ?
								<a style="margin-left: 10px" class="modal-click primary-color" data-modal="login-box">Đăng nhập</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="modal account-outer hidden modal-login-box modal-allow-close">
			<div class="modal-body">
				<div class="modal-content" style="background: transparent;">
					<div class="modal-form bg">
						<div class="heading">
							<div class="form-logo">
								<img src="<?php echo htmlEncode( Storage::option("theme_header_favicon") ); ?>">
							</div>
							ĐĂNG NHẬP TÀI KHOẢN
							<i class="modal-close link fa"></i>
						</div>
						<form class="form pd-15" method="POST" action="" id="account-login-form">
							<div class="modal-input">
								<i class="fa fa-user"></i>
								<input class="input input-circle" placeholder="Email hoặc số điện thoại" style="width:100%" type="text" name="username"/>
							</div>
							<div class="modal-input">
								<i class="fa fa-lock"></i>
								<input class="input input-circle" placeholder="Mật khẩu" style="width:100%" type="password" name="password"/>
							</div>
							<div style="padding: 10px 0;position: relative;">
								<label class="check radio">
									<input type="checkbox" checked>
									<s></s>
									Lưu đăng nhập
								</label>
								<a href="/user/forget" class="right-icon">
									<i class="fa fa-lock"></i>
									Quên mật khẩu?
								</a>
							</div>
							<div class="hidden alert-danger" id="loginMsg"></div>
							<div class="center">
								<input class="btn-primary input-circle width-100" id="loginSubmit" name="loginSubmit" type="button" value="ĐĂNG NHẬP" />
							</div>
							<div class="pd-10 center">
								Chưa có tài khoản ?
								<a style="margin-left: 10px" class="modal-click primary-color" data-modal="register-box">Đăng ký</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<div id="facebook-messenger-wrap">
		<?php echo  Storage::option("facebook_messenger") ; ?>
	</div>
	<style type="text/css">
		#facebook-messenger-wrap .fb_dialog{
			display: none !important
		}
		#facebook-messenger-wrap .fb-customerchat iframe{
			right: 110px !important;
			max-height: 0
		}
	</style>
	<footer id="footer">
		<div class="footer-body">
			<?php echo Widget::show("footer", 1); ?>
		</div>
	</footer>
	<style>
		html{
			background: #EAEAEA !important
		}
	</style>
	<?php echo Assets::show("/assets/general/js/script.js"); ?>
<script>
	AOS.init();//Hiệu ứng động
	<?php if( !empty( user('notify_to_manager') ) ): ?>
		$.get('/user/sendNotifyToManager'); // Gửi thông báo tài khoản mới tới quản lý
	<?php endif; ?>
</script>
<?php if( empty($_COOKIE["device"]) ): ?>
	<script type="text/javascript">
		setCookie("device", device(), 365);
		setTimeout(function(){
			$.get("");
		}, 5e3);
	</script>
<?php endif; ?>
<?php if( Storage::option('online_chat_enable') ): ?>
	<?php echo  OnlineChat::setup('<div class="alert-info form-mrg">'.__('Quý khách cần thiết kế web theo yêu cầu hoặc cần hỗ trợ, hãy nhắn tin với chúng tôi nhé!').'</div>') ; ?>
<?php endif; ?>
	<?php echo classes\ContactButton::show(); ?>
	<?php echo classes\FixedButton::show(); ?>
<?php echo  htmlCustom('footer') ; ?>
<?php echo  Assets::show() ; ?>
<?php
	//Ghi css từng trang
	if(PAGE_EDITOR){
		$mainStyle='
		main#main,
		#header>.header-body,
		#footer>.footer-body,
		.main-layout{
			max-width: '.(PAGE_OPTION["layout_main"]??1100).'px;
			margin: auto
		}
		.main-left{
			width: '.(PAGE_OPTION["layout_left"]??0).'%
		}
		.main-mid{
			width: '.(PAGE_OPTION["layout_mid"]??70).'%
		}
		.main-right{
			width: '.(PAGE_OPTION["layout_right"]??30).'%
		}
		'.((PAGE_OPTION["layout_mid"]??70)==100 ? '.flex-margin{padding: 0 !important}' : '').'
		';
		echo Widget::css($mainStyle, true);
	}
?>
</body>
</html>