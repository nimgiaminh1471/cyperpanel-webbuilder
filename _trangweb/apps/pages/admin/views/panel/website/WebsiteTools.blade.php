@php
	use models\Users;
	use models\BuilderDomain;
@endphp
<section style="max-width: 650px; margin: auto;">
	<!-- <div class="heading-simple">Domain không có trên database</div>
	@php
		//Xóa website
		if(isset($_REQUEST["delete_website"])){
			$wid = $_REQUEST["delete_website"];
			if( is_numeric($wid) ){
				$getWeb=BuilderDomain::find($wid);
				$domain=$getWeb->domain;
			}else{
				$domain=$wid;
			}
			if($domain==DOMAIN){
				die;
			}
			classes\WebBuilder::delete($domain, $wid);
			BuilderDomain::destroy($wid);
			if( is_dir(SERVER_ROOT."/domains/{$wid}") ){
				Folder::clear(SERVER_ROOT."/domains/{$wid}", false);
			}
			redirect(THIS_LINK, true, "Đã xóa website");
		}
		$i=0;
	@endphp
	@foreach( glob(SERVER_ROOT."/domains/*") as $path)
		@php
			$domain=basename($path);
			if( in_array($domain, [DOMAIN]) || strpos($domain, ".")===false || BuilderDomain::where("domain", $domain)->exists() ){
				continue;
			}
			$i++;
		@endphp
		<div class="menu bd-bottom" style="position: relative;">
			{{$domain}}
			<a class="right-icon" style="color: red" onclick="return confirm('Xóa vĩnh viễn dữ liệu của tên miền {{$domain}}?')" href="/admin/WebsiteTools?delete_website={{$domain}}"><i class="fa fa-times"></i></a>
		</div>
	@endforeach
	<div class="alert-info">Tổng số: {{$i}}</div> -->
	
	{{-- Upload files lên các web nhanh --}}
	<div class="heading-simple" style="margin-top: 2000px">Upload hàng loạt</div>
	@if( isset($_POST["advanced_upload"]) )
		@php
			$uploadFolder=glob($_POST["advanced_upload"]["path"], GLOB_ONLYDIR);
			if( empty($uploadFolder) ){
				echo '<div class="alert-danger">Không tìm thấy thư mục nào</div>';
			}else{
				$uploaded=[];
				if( !empty($_FILES["files"]["name"][0]) ){
					//Tiến hành upload
					for($i=0;$i<count($_FILES["files"]["name"]);$i++){
						$filename = $_FILES['files']['name'][$i];
						$uploadToPath=$uploadFolder[0]."/".$filename;
						$uploaded[]=$uploadToPath;
						move_uploaded_file($_FILES['files']['tmp_name'][$i], $uploadToPath);
					}
					unset($uploadFolder[0]);
				}
				echo '<ol class="bg" style="margin: 0; max-height: 320px; overflow: auto">';
				foreach($uploadFolder as $path){
					foreach($uploaded as $filePath){
						$copyFileTo=$path."/".basename($filePath);
						if( file_exists($copyFileTo) ){
							unlink($copyFileTo);
						}
						copy($filePath, $copyFileTo);
					}
					echo '<li class="menu '.(empty($uploaded) ? 'blue' : 'green').'">'.$path.'</li>';
				}
				echo '</ol>';
			}
		@endphp
	@endif
	<div class="center menu">
		<form action="" method="POST" enctype="multipart/form-data">
			<div class="pd-5">
				<input value="{{ ($_POST["advanced_upload"]["path"] ?? SERVER_ROOT."/domains/*/public_html/thumuccantailen") }}" class="width-100" type="text" name="advanced_upload[path]" placeholder="{{SERVER_ROOT}}/domains/*/public_html" />
			</div>
			@if( !empty($uploadFolder) )
				<div class="pd-5">
					<input class="width-100" type="file" name="files[]" placeholder="Chọn tệp tin" multiple />
				</div>
			@endif
			<div class="pd-5">
				<input class="btn-primary" type="submit" name="advanced_upload[submit]" value="{{ ( empty($uploadFolder) ? "Tìm thư mục" : "Upload" ) }}" />
			</div>
		</form>
	</div>

	{{-- Tạo thư mục hàng loạt --}}
	<div class="heading-simple" style="margin-top: 20px">Tạo thư mục hàng loạt</div>
	@if( isset($_POST["multiple_mkdir"]) )
		@php
			$createFolder=glob($_POST["multiple_mkdir"]["path"], GLOB_ONLYDIR);
			if( empty($createFolder) ){
				echo '<div class="alert-danger">Không tìm thấy thư mục gốc: '.$_POST["multiple_mkdir"]["path"].'</div>';
			}else{
				echo '<div class="alert-success">Tạo thư mục thành công</div>';
				echo '<ol class="bg" style="margin: 0; max-height: 320px; overflow: auto">';
				foreach($createFolder as $path){
					$folder=$path.'/'.$_POST["multiple_mkdir"]["folder"];
					if( !is_dir($folder) ){
						mkdir($folder, 0755, true);
					}
					echo '<li class="menu bd-bottom blue">'.$folder.'</li>';
				}
				echo '</ol>';
			}
		@endphp
	@endif
	<div class="center menu">
		<form action="" method="POST">
			<div class="pd-5">
				<input value="{{ ($_POST["multiple_mkdir"]["path"] ?? SERVER_ROOT."/domains/*/public_html") }}" class="width-100" type="text" name="multiple_mkdir[path]" placeholder="{{SERVER_ROOT}}/domains/*/public_html" />
			</div>
			<div class="pd-5">
				<input value="{{ ($_POST["multiple_mkdir"]["folder"] ?? "") }}" class="width-100" type="text" name="multiple_mkdir[folder]" placeholder="Tên thư mục cần tạo" />
			</div>
			<div class="pd-5">
				<input class="btn-primary" type="submit" name="multiple_mkdir[submit]" value="Tạo thư mục" />
			</div>
		</form>
	</div>

	{{-- Xóa thư mục hàng loạt --}}
	<div class="heading-simple" style="margin-top: 20px">Xóa thư mục & file hàng loạt</div>
	@if( isset($_POST["multiple_delete"]) )
		@php
			$multipleDeleteList=glob($_POST["multiple_delete"]["path"]);
			if( empty($multipleDeleteList) ){
				echo '<div class="alert-danger">Không tìm thấy thư mục hoặc file: '.$_POST["multiple_delete"]["path"].'</div>';
			}else{
				echo '<ol class="bg" style="margin: 0; max-height: 320px; overflow: auto">';
				foreach($multipleDeleteList as $path){
					if( empty($_POST["multiple_delete"]["delete_confirm"]) ){
						echo '<li class="menu bd-bottom blue">'.$path.'</li>';
					}else{
						if( is_dir($path) ){
							Folder::clear($path, false);
						}else{
							unlink($path);
						}
						echo '<li class="menu bd-bottom red">'.$path.'</li>';
					}
				}
				echo '</ol>';
			}
		@endphp
	@endif
	<div class="center menu">
		<form action="" method="POST">
			<div class="pd-5">
				<input value="{{ ($_POST["multiple_delete"]["path"] ?? SERVER_ROOT."/domains/*/public_html/thumuccanxoa") }}" class="width-100" type="text" name="multiple_delete[path]" placeholder="{{SERVER_ROOT}}/domains/*/public_html" />
			</div>
			<div class="pd-5 multiple-delete-search">
				<input class="btn-primary" type="submit" name="multiple_delete[search]" value="Tìm các file hoặc thư mục" />
			</div>
			@if( !empty($multipleDeleteList) && empty($_POST["multiple_delete"]["delete_confirm"]) )
				<div class="pd-5 multiple-delete-confirm">
					<label class="check radio">
						<input type="radio" name="multiple_delete[delete_confirm]" value="1" /> <s></s>
						Xác nhận xóa vĩnh viễn {{count($multipleDeleteList)}} mục
					</label>
				</div>
				<div class="pd-5" style="display: none">
					<input class="btn-danger" type="submit" name="multiple_delete[delete]" value="Bắt đầu xóa" />
				</div>
				<script type="text/javascript">
					$(".multiple-delete-confirm").click(function(){
						$(this).next().show();
						$(".multiple-delete-search").remove();
					});
				</script>
			@endif
		</form>
	</div>
</section>