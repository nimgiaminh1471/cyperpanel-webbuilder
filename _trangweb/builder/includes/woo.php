<?php

//Ẩn cập nhật woo 
//Remove WooCommerce's annoying update message
remove_action( 'admin_notices', 'woothemes_updater_notice' );

// REMOVE THE WORDPRESS UPDATE NOTIFICATION FOR ALL USERS EXCEPT ADMIN
global $user_login;
get_currentuserinfo();
if( !current_user_can('update_plugins') ){
        // checks to see if current user can update plugins
		 add_action( 'init', create_function( '$a', "remove_action( 'init', 'wp_version_check' );" ), 2 );
		 add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );
}

/*
 * Dịch woocommerce
 */
if( !function_exists('ra_change_translate_text') ){
	function ra_change_translate_text( $translated_text ) {
	if ( $translated_text == 'Old Text' ) {
	$translated_text = 'New Translation';
	}
	return $translated_text;
	}
	add_filter( 'gettext', 'ra_change_translate_text', 20 );
}
if( !function_exists('ra_change_translate_text_multiple') ){
	function ra_change_translate_text_multiple( $translated ) {
		$text = array(
			'Continue Shopping' => 'Tiếp tục mua hàng',
			'Update cart'       => 'Cập nhật giỏ hàng',
			'Apply Coupon'      => 'Áp dụng mã ưu đãi',
			'WooCommerce'       => 'Quản lý bán hàng',
		);
		$translated = str_ireplace( array_keys($text), $text, $translated );
		return $translated;
	}
	add_filter( 'gettext', 'ra_change_translate_text_multiple', 20 );
}