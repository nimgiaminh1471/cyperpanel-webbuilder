<?php

/*
 * Nội dung tùy chỉnh bên dưới trang
 */
add_action('wp_footer', 'footerCustom');
function footerCustom(){
	echo <<<HTML
	<style type="text/css">
		#wp-admin-bar-edit>a,
		#wp-admin-bar-edit>a:before{
			color: #DEFF00 !important;
			font-weight: bold
		}
	</style>
	<script>
		(function($) {
			$(document).ready(function(){
				// Thay thế link chỉnh sửa trang
				var outerEl = $('#wp-admin-bar-edit');
				if( outerEl.length > 0 ){
					var editLink = outerEl.find('#wp-admin-bar-edit_uxbuilder>a').attr('href');
					var originalLink = $('#wp-admin-bar-edit>a').attr('href');
					if( typeof editLink != 'undefined' ){
						outerEl.children('a').attr('href', editLink);
						$('#wp-admin-bar-edit_uxbuilder a').attr('href', originalLink).text('Trang chỉnh sửa đầy đủ');
					}
				}
			});
		})( jQuery );
	</script>
	HTML;

	// Thay thế nội dung dưới footer
	if( BUILDER_IS_TEMPLATE ){
		foreach(['theme_mods_flatsome-child', 'theme_mods_kinhdoanhweb-vn'] as $optionName){
			$content                     = get_option($optionName);
			$content['site_logo'] = preg_replace('#https?\:/\/(.+?)\/#i', '/', $content['site_logo']);
			$content['footer_left_text'] = '
                © Bản quyền thuộc về '.get_bloginfo('name').'
                <span class="w2steam">
                    Thiết kế và duy trì bởi
                        <a href="'.BUILDER_HOME.'" rel="nofollow" target="_blank">
                            '.ucwords(BUILDER_DOMAIN).'
                        </a>
                </span>
                <style>
                    /*.w2steam a { color: #FF4D00;}*/
                    .w2steam {padding-left: 5px;margin-left: 5px;border-left: 1px solid;}
                </style>
            ';
			update_option($optionName, $content);
		}
	}
}


/*
 * Nội dung tùy chỉnh bên dưới trang admin
 */
if( !function_exists('custom_admin_footer') ){
	function custom_admin_footer() { 
		echo 'Cảm ơn bạn đã khởi tạo với <a href="'.BUILDER_HOME.'" target="blank">'.BUILDER_DOMAIN.'</a>';
	}
	add_filter('admin_footer_text', 'custom_admin_footer');
}