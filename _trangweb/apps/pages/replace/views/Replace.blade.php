@php
	
	foreach( glob(SERVER_ROOT.'/domains/*/public_html/wp-content/themes/kinhdoanhweb-vn/functions.php') as $file){
		$content = file_get_contents($file);

		$content = preg_replace("/[\r\n]+/", "\n", $content);

		$content = preg_replace(
			'#//Tùy chỉnh admin footer(.+?)\'custom_admin_footer\'\)\;#s',
			'',
		$content);

		$content = preg_replace(
			'#function custom_admin_footer()(.+?)\'custom_admin_footer\'\)\;#s',
			'',
		$content);

		$content = preg_replace(
			"#//Ẩn các panel không cần thiết(.+?)\[\'dashboard_recent_drafts\'\]\)\;\n}#s",
			'',
		$content);

		$content = preg_replace(
			"#//Ẩn Welcome Panel(.+?)\'show_welcome_panel\', 0 \)\;\n}#s",
			'',
		$content);

		$content = preg_replace(
			"#//Xóa logo wordpress(.+?)remove_node\( \'wp-logo\' \)\;\n}#s",
			'',
		$content);

		$content = preg_replace(
			"#// Thay doi duong dan logo admin(.+?)add_action\(\'login_head\', \'login_css\'\)\;#s",
			'',
		$content);

		$content = preg_replace(
			"#\/\*Fix lỗi khi tạo sản phẩm mới bị 404(.+?)\'devvn_woo_new_product_post_save\'\)\;#s",
			'',
		$content);

		$content = preg_replace(
			"#/*Sửa lỗi 404 sau khi đã remove slug product hoặc cua-hang(.+?)\'devvn_woo_product_rewrite_rules\'\)\;#s",
			'',
		$content);


		$content = preg_replace(
			"#\/\*\n\* Code Bỏ /san-pham/ hoặc ... có hỗ trợ dạng(.+?)\'devvn_remove_slug\', 10, 2 \)\;#s",
			'',
		$content);

		$content = preg_replace(
			"#// Dịch woocommerce(.+?)// End dich#s",
			'',
		$content);

		$content = preg_replace(
			"#//Ẩn cập nhật woo(.+?)\"return null;\" \) \)\;\n   }#s",
			'',
		$content);

		$content = preg_replace(
			"#/\*\n\* Remove product-category in URL(.+?)endswitch\;\n    return \$url\;\n}#s",
			'',
		$content);

		$content = preg_replace(
			"#\/\*Sửa lỗi khi tạo mới taxomony bị 404(.+?)devvn_product_category_rewrite_rules\(true\)\;\n\}#s",
			'',
		$content);

		echo '
		<div style="margin-top: 30px">
			'.$file.'
			<textarea rows="50" style="width: 100%">'.$content.'</textarea>
		</div>
		';
		file_put_contents($file, $content);
	}


@endphp