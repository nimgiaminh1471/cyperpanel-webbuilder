@php
	use models\BuilderDomain;
	use classes\WebBuilder;
	$getWeb = BuilderDomain::orderBy('app_price', 'DESC')->orderBy('id', 'DESC')->limit(500)->get();
	$wpCoreFiles = [
		'wp-admin',
		'wp-includes',
		'wp-config.php'
	];
@endphp

<form class="pd-10">
	@php
		$wpCorePath = SYSTEM_ROOT.'/builder/wordpress-core';

		// Tải file WP gốc vể server
		if( isset($_GET['download']) ){
			$wpFileData = file_get_contents('https://wordpress.org/latest.zip');
			if( strlen($wpFileData) > 10000 ){
				file_put_contents(
					$wpCorePath.'/WP.zip',
					$wpFileData
				);
				if( file_exists($wpCorePath.'/WP.zip') ){
					// Giải nén file
					Folder::clear($wpCorePath.'/wordpress');
					$zip = new ZipArchive;
					$res = $zip->open($wpCorePath.'/WP.zip');
					if ($res === TRUE) {
						$zip->extractTo($wpCorePath);
						$zip->close();
						Folder::clear($wpCorePath.'/wordpress/wp-content', false);
					}
					unlink($wpCorePath.'/WP.zip');
				}
			}
			echo '
				<script>
					location.href = "/admin/UpdateWP";
				</script>
			';
		}

		// Cập nhật WP cho từng web
		if( isset($_GET['update']) ){
			$webPublic = WebBuilder::userPublic($_GET['domain']);
			Folder::clear($webPublic.'/wp-admin', false);
			Folder::clear($webPublic.'/wp-includes', false);
			$wpCoreFiles = [
				'wp-config.php'
			];
			foreach(glob($wpCorePath.'/wordpress/*.php') as $file){
				$wpCoreFiles[] = basename($file);
			}
			foreach(glob($webPublic.'/*.php') as $file){
				if( in_array(basename($file), $wpCoreFiles) ){
					/*unlink($file);*/
					$originalFile = $wpCorePath.'/wordpress/'.basename($file);
					if( file_exists($originalFile) ){
						copy($originalFile, $file);
					}
				}else{
					rename($file, $file.'.bak');
				}
			}
			Folder::copy($wpCorePath.'/wordpress/wp-admin', $webPublic.'/wp-admin');
			Folder::copy($wpCorePath.'/wordpress/wp-includes', $webPublic.'/wp-includes');
		}
	@endphp
	<div class="menu pd-20 center">
		<a href="?download" class="btn-sm btn-primary">
			Tải bản WP mới nhất về server
		</a>
		@if( file_exists($wpCorePath.'/wordpress/wp-includes/version.php') )
			@php
				include($wpCorePath.'/wordpress/wp-includes/version.php');
			@endphp
			<div class="pd-5"></div>
			Phiên bản WP trên hệ thống:
			<b>
				{{ $wp_version }}
			</b>
			<div class="pd-5"></div>
			<button data-modal="multiple-update" class="btn-sm btn-info center modal-click" type="button">
				<i class="fa fa-chevron-up"></i>
				Update hàng loạt
			</button>
			<div class="modal modal-multiple-update hidden modal-allow-close modal-allow-scroll">
				<div class="modal-body bg" style="max-width: 750px; text-align: left;">
					<form class="modal-content">
						<div class="heading modal-heading">
							<span>Cập nhật phiên bản WP</span>
							<i class="modal-close link fa"></i>
						</div>
						<div class="pd-20" style="max-height: 450px; overflow: auto;">
							<table class="table width-100" id="ml-update">
								<thead>
									<tr>
										<th style="width: 50px">
											#
										</th>
										<th>
											Domain
										</th>
										<th>
											WP version
										</th>
									</tr>
								</thead>
								<tbody>
									@foreach($getWeb as $i => $item)
									@php
									$publicPath = WebBuilder::userPublic($item->domain);
									$getWPVersionPath = $publicPath.'/wp-includes/version.php';
									if( !file_exists($getWPVersionPath) ){
										continue;
									}
									include($getWPVersionPath);
									@endphp
									<tr onclick="itemList.clickItem(this)" data-domain="{{ $item->domain }}">
										<td>
											<label class="check">
												<input type="checkbox" name="ml_update_check[]" value="{{ $item->domain }}" onchange="mlUpdate.toggleItem()" class="ml-update-toggle-item" data-quickselect="{{ $item->app_price ? 'template' : 'live'  }}">
												<s style="border: 1px solid #b0a9a9;"></s>
											</label>
										</td>
										<td>
											<a target="_blank" href="http://{{ $item->domain }}" style="{{ $item->app_price ? 'font-weight: bold' : ''  }}">
												{{ $item->domain }}
											</a>
										</td>
										<td>
											<div class="flex flex-middle">
												<div style="width: 50px">
													{{ $wp_version }}
												</div>
											</div>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<div class="pd-20">
							<button type="button" class="btn-primary ml-update-submit hidden" onclick="mlUpdate.submit()">
								Cập nhật
								<i></i>
							</button>
							<button type="button" class="btn-info" onclick="mlUpdate.quickSelect('template')">
								Chọn tất cả web mẫu
								<i></i>
							</button>
							<button type="button" class="btn-info" onclick="mlUpdate.quickSelect('live')">
								Chọn tất cả web khách
								<i></i>
							</button>
						</div>
					</form>
				</div>
			</div>
		@endif
	</div>
</form>
<table class="table width-100" id="website-list">
	<thead>
		<tr>
			<th style="width: 50px">
				#
			</th>
			<th>
				Domain
			</th>
			<th>
				WP version
			</th>
			<th style="text-align: center;">
				Backup
			</th>
		</tr>
	</thead>
	<tbody>
		@foreach($getWeb as $i => $item)
			@php
				$publicPath = WebBuilder::userPublic($item->domain);
				$getWPVersionPath = $publicPath.'/wp-includes/version.php';
				if( !file_exists($getWPVersionPath) ){
					continue;
				}
				include($getWPVersionPath);
			@endphp
			<tr onclick="itemList.clickItem(this)" data-domain="{{ $item->domain }}">
				<td>
					{{ $i + 1 }}
				</td>
				<td>
					<a target="_blank" href="http://{{ $item->domain }}" style="{{ $item->app_price ? 'font-weight: bold' : ''  }}">
						{{ $item->domain }}
					</a>
				</td>
				<td>
					<div class="flex flex-middle">
						<div style="width: 50px">
							{{ $wp_version }}
						</div>
						<div>
							@if( empty($_GET['update']) )
								<button class="btn-sm btn-info center" onclick="itemList.updateWP(this, '{{ $item->domain }}')">
									<i class="fa fa-chevron-up"></i>
								</button>
							@endif
						</div>
					</div>
				</td>
				<td style="text-align: center;">
					<button class="btn-sm btn-primary" onclick="itemList.downloadWebsiteBackup({{ $item->id }})">
						<i class="fa fa-download"></i>
					</button>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>

<section class="section" style="margin-top: 50px" id="malware-scan">
	<div class="section-heading">
		Scan file mới chỉnh sửa
	</div>
	<div class="section-body">
		<div style="max-height: 500px; overflow: auto;">
			<ul class="malware-scan-body hidden">
				
			</ul>
			<ul class="malware-scan-body-sc" style="list-style: none;padding: 0; margin: 0">

			</ul>
		</div>
		<div class="center">
			<button type="button" class="btn-primary" onclick="malwareScan.scan(); $(this).hide()">
				Bắt đầu quét
			</button>
		</div>
		<div class="malware-scan-status alert alert-warning hidden"></div>
	</div>
</section>
<?php
	// Đánh dấu file an toàn
	if( isset($_GET['mark_as_safe']) ){
		$exclude   = Storage::malware_scan_exclude();
		$exclude[] = $_GET['path'];
		Storage::update('malware_scan_exclude', $exclude);
	}

	// Xóa khỏi danh sách an toàn
	if( isset($_GET['remove_from_safe_list']) ){
		$exclude   = Storage::malware_scan_exclude();
		unset($exclude[ $_GET['id'] ]);
		Storage::delete('malware_scan_exclude');
		Storage::update('malware_scan_exclude', $exclude);
	}
?>
@if( isset($_GET['scan']) )
	@php
		if( !function_exists('glob_recursive') ){
			function glob_recursive($pattern, $flags = 0){
				$files = glob($pattern, $flags);
				foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
				{
					$files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
				}
				return $files;
			}
		}
		$scanBody = '';
		$phpFiles = glob_recursive( dirname( WebBuilder::userPublic($_GET['domain']) ).'/*.php');
		usort( $phpFiles, function( $a, $b ) { return filemtime($a) - filemtime($b); } );
		$phpFiles = array_reverse($phpFiles);
		$phpFiles = array_splice($phpFiles, 0, 10);
		foreach($phpFiles as $item){
		    $last_mod = filemtime($item);
			$itemPath = str_replace( '', '', $item);
			$content = file_get_contents($item, FILE_USE_INCLUDE_PATH);
			$scanBody .= '
				<div class="flex menu bd-bottom file-path-item" data-mtime="'.$last_mod.'">
					<div style="width: 100px; color: '.( strpos(datetext($last_mod), ' ') === FALSE ? 'blue' : 'red').'" >
						'.datetext($last_mod).'
					</div>
					<div style="width: calc(100% - 100px)">
						'.$itemPath.'
					</div>
				</div>
			';
		}
	@endphp
	@if( $scanBody )
		<section id="scan-detail">
			<div class="panel panel-default" style="margin-top: 20px">
				<div class="heading link panel-actived">
					{{ $_GET['domain'] }}
				</div>
				<div class="panel-body hidden" style="display: block;">
					{!! $scanBody !!}
				</div>
			</div>
		</section>
	@endif
@endif
<style type="text/css">
	.table-tr-active>td,
	.file-path-item-active,
	.file-path-item:hover{
		background-color: #cbe3f8 !important
	}
</style>

<script type="text/javascript">
	@php
		$domains = [];
		foreach(glob(SERVER_ROOT.'/domains/*') as $path){
			$domains[] = basename($path);
		}
		//$domains = array_splice($domains, 0, 20);
	@endphp
	var domains = @json( $domains );
	var totalDomains = domains.length;
	const malwareScan = {
		/*
		 * Bắt đầu quét
		 */
		scan: () => {
			var domain = domains[0];
			var scanedCount = totalDomains - domains.length + 1;
			$('#malware-scan .malware-scan-status').html(`Đang quét: ${domain} (${scanedCount}/${totalDomains})`).show();
			if( typeof domain == 'undefined' ){
				$('#malware-scan .malware-scan-status').addClass('alert-warning').addClass('alert-success').html(`Đã quét hoàn tất`).show();
				var items = [];
				$('.malware-scan-body .file-path-item').each(function () {
					items.push([
						$(this).attr('data-mtime'),
						$(this)[0].outerHTML
					]);
				});
				items = items.sort(function(a,b){ return a[0] < b[0] ? 1 : -1; });
				$.each(items, function (i, item) {
					$('.malware-scan-body-sc').append(item[1]);
				});
				return false;
			}
			$.ajax({
				url: "",
				type: "GET",
				dataType: "HTML",
				data: {scan: true, domain: domain},
				success: function(res){
					var body = $(res).find('#scan-detail').html();
					if(typeof body != 'undefined'){
						$('.malware-scan-body').append(`<li>${body}</li>`);
					}
					domains.splice(0, 1);
					setTimeout(function(){
						malwareScan.scan();
					}, 500);
				},
				error: function(){
					setTimeout(function(){
						malwareScan.scan();
					}, 1000);
				}
			});
		},

		/*
		 * Đánh dấu file an toàn
		 */
		markAsSafe: (self) => {
			if( !confirm('Đánh dấu file này an toàn ?') ){
				return false;
			}
			let path = $(self).parent().attr('data-path');
			$('.file-path-item[data-path="'+path+'"]').hide();
			$('.malware-scan-body>li').each(function(){
				if( $(this).find('.file-path-item:visible').length == 0 ){
					$(this).hide();
				}
			});
			$(self).parent().hide();
			$.ajax({
				url: "",
				type: "GET",
				dataType: "HTML",
				data: {mark_as_safe: true, path: path},
				success: function(res){
					
				},
				error: function(){
					setTimeout(function(){
						malwareScan.markAsSafe(self);
					}, 1000);
				}
			});
		},

		/*
		 * Xóa đường dẫn khỏi danh sách an toàn
		 */
		removeFromSafeList: (self, id) => {
			$(self).parent().hide();
			$.ajax({
				url: "",
				type: "GET",
				dataType: "HTML",
				data: {remove_from_safe_list: true, id: id},
				success: function(res){
					
				},
				error: function(){
					setTimeout(function(){
						malwareScan.removeFromSafeList(self, id);
					}, 1000);
				}
			});
		},

		/*
		 * Click item
		 */
		clickItem: (self) => {
			$('.file-path-item-active').removeClass('file-path-item-active');
			$(self).addClass('file-path-item-active');
		}
	};

	const itemList = {
		test: 1,

		/*
		 * Click tr
		 */
		clickItem: (self) => {
			$('.table-tr-active').removeClass('table-tr-active');
			$(self).addClass('table-tr-active');
		},

		/*
		 * Tải bản backup
		 */
		downloadWebsiteBackup: (id) => {
			$('#loading').show();
			$.ajax({
				url: "/api/websiteManager/createBackup",
				type: "POST",
				dataType: "JSON",
				data: {download: true, id: id},
				success: function(res){
					if( typeof res.data == 'undefined' ){
						alert('Lỗi kết nối, vui lòng thử lại');
					}else{
						location.href = res.data;
					}
				},
				error: function(){
					setTimeout(function(){
						itemList.downloadWebsiteBackup(id);
					}, 1000);
				},
				complete: function(){
					$('#loading').hide();
				}
			});
		},

		/*
		 * Nâng cấp bản WP mới nhất
		 */
		updateWP: (self, domain) => {
			$(self).html('<i class="fa fa-spinner fa-spin fa-fw"></i>').prop('disabled', true);
			$.ajax({
				url: "",
				type: "GET",
				dataType: "html",
				data: {update: true, domain: domain},
				success: function(res){
					var trEl = $(res).find('#website-list tr[data-domain="'+domain+'"]').html();
					$(self).parents('tr').html(trEl);
					var win = window.open('http://'+domain, '_blank');
					win.focus();
				},
				error: function(){
					setTimeout(function(){
						itemList.updateWP(self, domain);
					}, 1000);
				},
				complete: function(){
					$(self).html('<i class="fa fa-chevron-up"></i>').prop('disabled', false);
				}
			});
		},
	}

	const mlUpdate = {

		/*
		 * Lựa chọn web
		 */
		toggleItem: () => {
			let itemsSelected = $('.ml-update-toggle-item:checked');
			if( itemsSelected.length > 0 ){
				$('.ml-update-submit>i').html('('+itemsSelected.length+')');
				$('.ml-update-submit').show();
			}else{
				$('.ml-update-submit').hide();
			}
		},

		/*
		 * Ấn nút cập nhật
		 */
		submit: () => {
			let domain = $('.ml-update-toggle-item:checked').val();
			if( typeof domain == 'undefined' ){
				return false;
			}
			$('.ml-update-submit').html('Đang cập nhật: '+domain).prop('disabled', true);
			$.ajax({
				url: "",
				type: "GET",
				dataType: "html",
				data: {update: true, domain: domain},
				success: function(res){
					$('#ml-update tr[data-domain="'+domain+'"]').remove();
					if( $('.ml-update-toggle-item:checked').length > 0 ){
						setTimeout(function(){
							mlUpdate.submit();
						}, 1000);
					}else{
						alert('Đã hoàn tất cập nhật');
						location.reload();
					}
				},
				error: function(){
					setTimeout(function(){
						mlUpdate.submit();
					}, 1000);
				}
			});
		},

		/*
		 * Chọn nhanh web mẫu
		 */
		quickSelect: (type) => {
			let checkBoxes = $('.ml-update-toggle-item[data-quickselect="'+type+'"]');
			checkBoxes.attr('checked',  !checkBoxes.attr("checked"));
			mlUpdate.toggleItem();
		}
	};
</script>