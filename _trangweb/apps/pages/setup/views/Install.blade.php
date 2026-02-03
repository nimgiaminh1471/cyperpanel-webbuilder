@extends("Default")
@php
	use models\Users;
	$admin=Users::find(1);
	define("PAGE", [
		"name"        =>"Khởi tạo website",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"noindex,nofollow"
	]);
	$backupsFolder=dirname(__DIR__,2)."/backups";


	//Ấn submit
	if( isset($_POST["email"]) ){
		$post=$_POST;
		$installInvalid=Form::invalid($post, true);
		$installMsg=$installInvalid["error"];
		$data=$installInvalid["data"];
		$templateBackup="builder.".(explode("//", HOME)[1]??"");

		//Tiến hành khởi tạo
		if( empty($installMsg) ){
			$backupPart=[];
			if( empty(HOME) && file_exists($backupsFolder."/".$data["restore"]."/1.sql") ){
				//Khôi phục bản lưu từ select
				$backupPart=glob($backupsFolder."/".$data["restore"]."/*.sql");;
			}else if( file_exists($backupsFolder."/".$templateBackup."/1.sql") ){
				//Tự động tìm bản lưu
				$backupPart=glob($backupsFolder."/".$templateBackup."/*.sql");
			}
			$_SESSION["name"]=$data["name"];
			$_SESSION["email"]=$data["email"];
			$_SESSION["password"]=$data["password"];
			Storage::update("restoreBackup", ["path"=>$backupPart]);
			redirect();
		}
		
	}
@endphp



@section("header")
@endsection

@section("main")

	<div class="modal modal-idm">
		<div class="modal-body middle-body" style="max-width:550px">
			<div class="heading heading-block">Khởi tạo website</div>
			@if( is_array( Storage::restoreBackup("path") ) )
				{{-- Khôi phục database --}}
				@php
					if( empty( Storage::restoreBackup("path") ) ){
						if(empty($_SESSION["password"])){
							PrintError("Lỗi khởi tạo, hãy thử lại");
						}
						//Khôi phục xong
						echo '
						<script>
							location.href="/admin";
						</script>
						<div id="setup-status" class="alert-success">success</div>
						';
						Storage::delete("restoreBackup");
						Storage::delete("backuping");
						//Tạo hoặc cập nhật tài khoản Admin
						$loginKey=md5("1".randomString(50)."");
						$user=[
							"id"         => 1,
							"name"       => $_SESSION["name"],
							"email"      => $_SESSION["email"],
							"password"   => passwordCreate($_SESSION["password"]),
							"role"       => 1,
							"created_at" => timestamp(),
							"updated_at" => timestamp(),
							"login_key"  => $loginKey
						];
						setcookie("login_key", $loginKey, time()+3600*24*365, "/");
						Users::where("id", 1)->update($user, true);
						DB::table("storage")->where("key", "backup")->delete();
						DB::table("storage")->where("key", "actived")->update(["value"=>1]);
						require_once( Route::path("SeedsData.php") );//Tạo dữ liệu mẫu
						unset($_SESSION["name"], $_SESSION["email"],$_SESSION["password"]);
					}else{
						//Tiến hành khôi phục từng file sql
						$sqlFile=current( Storage::restoreBackup("path") );
						$sqlData=file($sqlFile, FILE_USE_INCLUDE_PATH);
						DB::table("storage")->where("key", "actived")->delete();
						foreach($sqlData as $query){
							$import=DB::query($query);
							if( strpos($query, "INSERT INTO `storage`")!==false ){
								continue;
							}
							if( !$import ){
								die('
								<div class="panel panel-danger" id="import-error">
									<div class="heading">Lỗi khởi tạo dữ liệu, hãy báo với chúng tôi nhé!</div>
									<div class="panel-body">
										<textarea class="width-100" rows="10">'.htmlEncode($query).'</textarea>
									</div>
								</div>
								');
							}
						}
						DB::table("storage")->where("key", "actived")->delete();
						$sqlList=glob( dirname($sqlFile)."/*.sql");
						echo '
						<div id="setup-status">
							<div class="alert-danger">Đang khôi phục dữ liệu...Vui lòng chờ</div>
							<div class="bg pd-20">
								<progress class="progress-bar width-100" value="'.( count($sqlList)-count( Storage::restoreBackup("path") )+1 ).'" max="'.count($sqlList).'"></progress>
							</div>
						</div>
						'.($importError??'
							<script>
								setInterval(function(){
									$.get("", function(response){
										var data=$(response).find("#setup-status").html();
										if(data=="undefined"){
											location.reload();
										}else if(data=="success"){
											alert("Khởi tạo thành công, đã đăng nhập '.($_SESSION["email"]??'').'");
											location.href="/admin";
										}else{
											$("#setup-status").html(data);
										}
									});
								}, 500);
							</script>
						').'
						';
						$newPath=Storage::restoreBackup("path");
						$newPath=array_slice($newPath, 1);
						Storage::update("restoreBackup", ["path"=>$newPath]);
					}
				@endphp
			@else
				{{-- Nhập thông tin khởi tạo --}}
				<form action="" method="POST" class="bg" style="margin-bottom: 0">
					@php
						$form[]=["type"=>"text", "name"=>"name", "title"=>"Họ tên", "note"=>"Họ và tên", "value"=>REQUEST( "name", ($admin->name??"") ), "attr"=>'', "horizontal"=>30];
						$form[]=["type"=>"text", "name"=>"email", "title"=>"Email Admin", "note"=>"Email đăng nhập", "value"=>REQUEST( "email", ($admin->email??"") ), "attr"=>'', "horizontal"=>30];
						$form[]=["type"=>"password", "name"=>"password", "title"=>"Mật khẩu Admin", "note"=>"Mật khẩu đăng nhập", "value"=>($_POST["password"]??""), "attr"=>'', "horizontal"=>30];
						$form[]=["type"=>"password", "name"=>"password2", "title"=>"Nhập lại mật khẩu", "note"=>"Nhập lại mật khẩu trên", "value"=>($_POST["password2"]??""), "attr"=>'', "horizontal"=>30];
						if( empty(HOME) ){
							$backupsFile[""]="Chọn bản sao lưu";
							$backupsFile[1]="Khởi tạo như website mới";
							foreach(glob("{$backupsFolder}/*", GLOB_ONLYDIR) as $path){
								$folderName=basename($path);
								$backupsFile[$folderName]=$folderName." (".date( "d/m/Y", filectime($path) ).")";
							}
							$form[]=["type"=>"select", "name"=>"restore", "title"=>"Khôi phục data", "option"=>$backupsFile, "value"=>($_POST["restore"] ?? DOMAIN.".sql"), "horizontal"=>30];
						}
						echo Form::create([
							"form"=>$form,
							"function"=>"",
							"prefix"=>"",
							"name"=>"",
							"class"=>"menu",
							"hover"=>true
						]);
					@endphp
					<div class="alert-danger{{empty($installMsg) ? " hidden" : ""}}">
						{{$installMsg??""}}
					</div>
					<div class="menu center">
						<button class="btn-primary" type="submit">Khởi tạo web</button>
					</div>
				</form>
			@endif
		</div>
	</div>

@endsection


@section("script")
@endsection

@section("footer")
@endsection
