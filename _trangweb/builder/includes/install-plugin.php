<?php
/*
 * Cài đặt các gói plugin
 */

class WPInstallPlugin{
	public static function install(){
		$pluginPath = ABSPATH.'wp-content/plugins/';
		$file = glob(ABSPATH.'builder/install-plugins/*.zip')[0] ?? null;
		if( empty($file) ){
			return;
		}
		$pluginFolder = pathinfo($file, PATHINFO_FILENAME);
		$pluginFolder = $pluginPath.$pluginFolder;
		self::unpack($pluginPath, $file);
		$activateFile = null;
		foreach(glob($pluginFolder.'/*.php') as $file){
			if( strpos( file_get_contents($file, FILE_USE_INCLUDE_PATH), 'Plugin Name:') !== false ){
				$activateFile = $file;
			}
		}
		if( !empty($activateFile) ){
			$activateFile = plugin_basename(trim($activateFile));
			echo '
				<script>
					alert("Đã kích hoạt plugin: '.dirname($activateFile).'");
					location.href = "/wp-admin/plugins.php?activate_plugin='.urlencode($activateFile).'";
				</script>
			';
			die;
		}
	}
	public static function unpack($pluginPath, $file){
		$zip = new ZipArchive;
		$res = $zip->open($file);
		if ($res === TRUE) {
		  $zip->extractTo($pluginPath);
		  $zip->close();
	  }
		if( file_exists($file) ){
			unlink($file);
		}
	}
	public static function activate($plugin){
		$current = get_option('active_plugins');
		if( !in_array($plugin, $current) ){
			$current[] = $plugin;
			sort($current);
			do_action('activate_plugin', trim($plugin));
			update_option('active_plugins', $current);
			do_action('activate_'.trim($plugin));
			do_action('activated_plugin', trim($plugin));
			return true;
		}
	}
}

if( is_admin() ){
	WPInstallPlugin::install();
	if( isset($_GET['activate_plugin']) ){
	   WPInstallPlugin::activate($_GET['activate_plugin']); 
   }
}