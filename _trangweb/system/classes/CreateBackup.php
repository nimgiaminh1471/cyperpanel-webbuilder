<?php
/*
# Sao lưu dữ liệu
*/
use classes\WebBuilder;
class CreateBackup{
	//Tạo backup databse
	public static function run($domain, $onlyDatabase = false, $reinstall = false, $download = false){
		$webPublic = WebBuilder::userPublic($domain);
		$databaseFileTemp = $webPublic."/database-".md5( time() ).".sql";
		$getWeb = \models\BuilderDomain::where("domain", $domain)->first();
		if( $onlyDatabase && $getWeb->app_price > 0 ){
			$databaseFileTemp = SYSTEM_ROOT."/builder/database-template/".$getWeb->app.".sql";
		}
		if( empty( webConfig( $domain, "DB_USER" ) ) ){
			return false;
		}
		// Tạo backup bằng CMD
		shell_exec("mysqldump -u ".webConfig( $domain, "DB_USER" )." -p".webConfig( $domain, "DB_PASSWORD" )." ".webConfig( $domain, "DB_NAME" )." > $databaseFileTemp 2>&1");
		if( $onlyDatabase ){
			return $databaseFileTemp;
		}
		// Backup files
		$rootPath = realpath($webPublic);

		// Initialize archive object
		$backupFile="/backups/backup-".$domain."-".date("H-i_d-m-Y").".zip";
		$zip = new ZipArchive();
		$zip->open(PUBLIC_ROOT.$backupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

		// Create recursive directory iterator
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($rootPath),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file)
		{
		    // Skip directories (they would be added automatically)
			if (!$file->isDir())
			{
		        // Get real and relative path for current file
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($rootPath) + 1);

		        // Add current file to archive
		        if($filePath!=$webPublic."/wp-config.php"){
					$zip->addFile($filePath, $relativePath);
		        }
			}
		}

		// Zip archive will be created only after closing object
		$zip->close();
		unlink($databaseFileTemp);
		echo '
		<script>
			location.href="'.$backupFile.'";
			setTimeout(function(){
				window.close();
			}, 500);
		</script>
		';
		return $backupFile;
	}
}