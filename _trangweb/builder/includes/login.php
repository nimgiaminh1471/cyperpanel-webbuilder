<?php

/*
 * Đổi đường dẫn logo đăng nhập
 */
if( !function_exists('wpc_url_login') ){
	function wpc_url_login(){
		return '/';
	}
	add_filter('login_headerurl', 'wpc_url_login');
}
/*
 * Thêm CSS tùy chỉnh
 */
if( !function_exists('login_css') ){
	function login_css(){
		echo '
			<style>
				#login h1 a {
					background: url('.flatsome_option('site_logo').') no-repeat !important;
				}
			</style>
		';
		wp_enqueue_style( 'login_css', BUILDER_HOME.'/web-builder/assets/login/style.css' ); // duong dan den file css moi
	}
	add_action('login_head', 'login_css');
}