@extends("Default")
@php
	use models\Posts;
	use models\Users;
	use models\PostsComments;

	define("PAGE", [
		"name"        =>"Trang cá nhân",
		"title"       =>__("Trang cá nhân")." : ".user("name"),
		"description" =>"",
		"loading"     =>500,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"index,follow",
		"breadcrumb"  => [
			THIS_LINK=>__("Trang cá nhân")
		]
	]);
@endphp


@section("main")
	<main id="main">
		@php
			$avatarFolder=PUBLIC_ROOT."/files/users/avatars";
			$avatarPath=$avatarFolder."/".user("id").".png";
			//Tải lên avatar
			if(isset($_FILES["avatar"])){
				$uploadStt=Gallery::upload([
					"form"=>"avatar",//Tên form chứa file
					"folder"=>$avatarFolder,//Thư mục lưu
					"name"=>user("id").".png",//Tên file (để trống sẽ tự đặt)
					"ext"=>["jpg", "jpeg", "png"],//Cho phép đuôi
					"overwrite"=>true,//Ghi đè file đã tồn tại
					"maxSize"=>9000, //Cỡ tối đa (Kb)
				]);
				if(is_null($uploadStt)){
					Users::find( user("id") )->update(["last_updated"=>timestamp()]);
					Image::resize($avatarPath, $avatarPath, 180, 0, 100);
				}else{
					echo '<div id="account-avatar-upload-msg">'.$uploadStt.'</div>';
				}
			}

			//Xoay avatar
			if(isset($_POST["avatarRotate"])){
				Image::rotate($avatarPath, 90);
			}

			//Tạo phần nội dung
			$posts=Posts::where( "users_id", user("id") )->orderBy("updated_at", "DESC")->paginate(5);
			$comments=PostsComments::where( "users_id", user("id") )->orderBy("created_at", "DESC")->paginate(5);
			$section=[
				"info"=>["title"=>"Thông tin cá nhân", "icon"=>"fa-user"],
				"edit"=>["title"=>"Sửa thông tin", "icon"=>"fa-pencil"],
				"password"=>["title"=>"Đổi mật khẩu", "icon"=>"fa-lock"],
				"avatar"=>["title"=>"Đổi ảnh đại điện", "icon"=>"fa-image"],
			];
			$section["logout"]=["title"=>"Đăng xuất", "icon"=>"fa-sign-out", "confirm"=>"Bạn có muốn thoát tài khoản"];
		@endphp
		<div class="flex flex-large">
			<section class="flex-margin main-mid medium-margin-bottom">
				@foreach($section as $type=>$item)
					@if($tab!=$type)
						@continue
					@endif
					@php
						$padding="pd-10 ";
						if( in_array($type, ["info", "comments", "posts"]) ){
							$padding="";
						}
					@endphp
					<div class="form show-item-body">
						<div class="heading-simple">{{$item["title"]}}</div>
						<form class="{{$padding}} bg form form-{{$type}}">
					@switch($type)

						{{-- Thông tin cá nhân --}}
						@case("info")
							<table class="table width-100">
								@foreach(["email"=>"Email", "phone"=>"Số điện thoại", "name"=>"Họ tên", "created_at"=>"Ngày đăng ký", "posts_count"=>"Bài viết", "comments"=>"Bình luận"] as $key=>$label)
									@php
										$value=user($key);
										switch($key){
											case "created_at":
												$value=date("d/m/Y", timestamp($value) );
											break;
											case "comments":
												$value=PostsComments::where( "users_id", user("id") )->total();
											break;
											case "posts_count":
												if( !permission("post") ){
													$value=null;
												}
											break;
										}
									@endphp
									@if( !is_null($value) )
										<tr>
											<td class="width-35"><span style="padding-left: 10px">{{$label}}</span></td>
											<td class="width-65"><b>{{$value}}</b></td>
										</tr>
									@endif
								@endforeach
							</table>
							<div class="pd-10 center"><a class="see-more" href="/user/profile/edit">Sửa thông tin</a></div>

							@if($posts->total()>0)
								<div class="panel panel-info">
									<div class="heading link"><i class="fa fa-book"></i> Bài viết của tôi <span class="badge right-icon" style="right: 30px">{{$posts->total()}}</span></div>
									<div class="panel-body hidden pd-0">
										<div class="paginate-ajax" id="my-posts">
											@foreach($posts as $p)
												<div class="menu bd-bottom" style="position: relative;">
													<div><a class="block" target="_blank" href="/{{$p->link}}"><b>{{$p->title}}</b></a></div>
													<div class="gray">
														<i class="fa fa-clock-o"></i> {{ date("H:i - d/m/Y", timestamp($p->updated_at) ) }}
													</div>
												</div>
											@endforeach
											{!! $posts->links() !!}
										</div>
									</div>
								</div>
							@endif
							@if($comments->total()>0)
								<div class="panel panel-info panel-last">
									<div class="heading link"><i class="fa fa-comments"></i> Bình luận của tôi <span class="badge right-icon" style="right: 30px">{{$comments->total()}}</span></div>
									<div class="panel-body hidden pd-0">
										<div class="paginate-ajax" id="my-comments">
											@foreach($comments as $cmt)
												<a class="block menu bd-bottom" target="_blank" href="/{{Posts::where("id", $cmt->posts_id)->value("link")}}#comments-item-outer-{{($cmt->parent==0 ? $cmt->id : $cmt->parent)}}">
													<span class="block" style="margin: 8px 0">{!! nl2br($cmt->content) !!}</span>
													<span class="label-{{ $cmt->status=="pending" ? "danger" : "success" }}">{{ $cmt->status=="pending" ? "Chờ duyệt" : "Đã duyệt" }}</span>
													<span class="gray"><i class="fa fa-clock-o"></i> {{ dateText(timestamp($cmt->updated_at) ) }}</span>
												</a>
											@endforeach
											{!!$comments->links()!!}
										</div>
									</div>
								</div>
							@endif

						@break;

						{{-- Sửa thông tin --}}
						@case("edit")
							@php
								$editorTemplate=["email"=>"Email", "name"=>"Họ tên", "birthday"=>"Sinh nhật", "phone"=>"Số điện thoại", "gender"=>"Giới tính", "facebook"=>"Link Facebook", "company"=>"Tổ chức (Công ty)", "address"=>"Địa chỉ"];
							@endphp
							@foreach($editorTemplate as $key=>$label)
								@php
									$formType="text";
									switch($key){
										case "address":
										case "company":
											$formType="textarea";
										break;
									}
								@endphp
								<div class="tooltip tooltip-top block">
									@switch($key)
										@case("gender")
											@php
												$editForm[]=["type"=>"select", "name"=>$key, "title"=>$label, "option"=>["1"=>"Nam", "2"=>"Nữ","3"=>"Khác"], "value"=>user($key), "horizontal"=>20];
											@endphp
										@break;
										@case("birthday")
											@php
												$editForm[] = ["type"=>"date", "name"=>$key, "title"=>"Sinh nhật", "note"=>"Ấn để chọn ngày", "value"=>user($key), "attr"=>'', "position"=>"bottom",
													"format"=>"day/month/year",
													"config"=>[
														"allow"=>[
															"days"=>"", "months"=>"", "weekDay"=>["mon", "tue", "wed", "thu", "fri", "sat", "sun"],
															"min"=>["y"=>date("Y")-100, "m"=>1, "d"=>1], "max"=>["y"=>date("Y")-10, "m"=>2, "d"=>14]
														],
														"value"=>["day"=>date("d"), "month"=>date("m"), "year"=>date("Y")]
													]
												];
											@endphp
										@break
										@default
											@php
												$editForm[]=["type"=>$formType, "name"=>$key, "title"=>$label, "note"=>$label, "value"=>user($key), "attr"=>'', "horizontal"=>20];
											@endphp
									@endswitch
									<span class="tooltip-body">{{$label}}</span>
								</div>
							@endforeach
							{!!Form::create([
								"form"=>$editForm,
								"function"=>"",
								"prefix"=>"",
								"name"=>"edit_user",
								"class"=>"menu",
								"hover"=>false
							])!!}
							<div class="edit-user-msg" style="margin-bottom: 5px">
								@php
									if(isset($_POST["edit_user"])){
										extract($_POST["edit_user"]);
										if( Users::where("email", $email)->where("id", "!=", user("id"))->exists() ){
											$msg="Email đã tồn tại trên hệ thống";
										}
										if( Users::where("phone", $phone)->where("id", "!=", user("id"))->exists() ){
											$msg="Số điện thoại đã tồn tại trên hệ thống";
										}
										if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
											$msg="Email không hợp lệ";
										}
										$required=["email", "name", "gender", "phone"];
										$storage=[];
										foreach($editorTemplate as $name=>$label){
											$value=$_POST["edit_user"][$name];
											if(empty($value) && in_array($name, $required) ){
												$msg=$label." Không được để trống";
											}else{
												if(in_array($name, $required)){
													$data[$name]=strip_tags($value);
												}else{
													$storage[$name]=strip_tags($value);
												}
												switch($name){
													case "phone":
														if( !preg_match('/^\+?\d+$/', $value) || strlen($value)>20 || strlen($value)<5 ){
															$msg=$label." không hợp lệ";
														}
													break;

													case "name":
														if(strlen($value)>30 || strlen($value)<1 || preg_match('/([^\pL\.\ ]+)/u', $value) || substr_count($value, " ")>4 ){
															$msg="Tên không hợp lệ";
														}
													break;
												}
											}
										}
										Users::updateStorage( user("id"), $storage );
										if(empty($msg)){
											$data["name"]=ucwords($data["name"]);
											$data["last_updated"]=timestamp();
											Users::find( user("id") )->update($data);
											$msg='<div class="alert-success">Đã cập nhật thông tin tài khoản</div>';
										}else{
											$msg='<div class="alert-danger">'.$msg.'</div>';
										}
										echo $msg;
									}
								@endphp
							</div>
							<button type="button" class="btn-primary width-100">Cập nhật thông tin</button>
						@break;

						{{-- Đổi mật khẩu --}}
						@case("password")
							<input class="input width-100" placeholder="Nhập mật khẩu cũ" type="password" name="change_password[old]" />
							<input class="input width-100" placeholder="Mật khẩu mới" type="password" name="change_password[new]" />
							<input class="input width-100" placeholder="Nhập lại mật khẩu mới" type="password" name="change_password[confirm]" />
							<div class="change-password-msg" style="margin-bottom: 5px">
								@php
									if(isset($_POST["change_password"])){
										if($_POST["change_password"]["new"]!=$_POST["change_password"]["confirm"]){
											$msg="Mật khẩu nhập lại không khớp";
										}
										if(strlen($_POST["change_password"]["new"])<6){
											$msg="Mật khẩu phải dài ít nhất 6 kí tự";
										}
										if(!passwordCheck($_POST["change_password"]["old"], user("password"))){
											$msg="Mật khẩu cũ không chính xác";
										}
										if(empty($msg)){
											Users::find( user("id") )->update(["login_key"=>"", "password"=>passwordCreate($_POST["change_password"]["new"])]);
											$msg='<div class="alert-success">Đổi mật khẩu thành công</div>';
										}else{
											$msg='<div class="alert-danger">'.$msg.'</div>';
										}
										echo $msg;
									}
								@endphp
							</div>
							<button type="button" class="btn-primary width-100">Đổi mật khẩu</button>
						@break;

						{{-- Đổi avatar --}}
						@case("avatar")
							<div class="pd-10 center">
								<div class="pd-5"><img class="account-avatar-img" src="{!! user("avatar") !!}?t={{time()}}" /></div>
								<div class="pd-5">
									<button type="button" class="btn-info account-avatar-rotate"><i class="fa fa-undo"></i> Xoay</button>
									<button type="button" class="btn-info account-avatar-btn">Đổi ảnh <i class="fa fa-image"></i></button>
									<input type="file" class="hidden account-avatar-input" name="avatar" />
								</div>
							</div>
						@break;

						{{-- Đăng xuất --}}
						@case("logout")
							@php
								setcookie("login_key", "", time()-1, "/");
								setcookie("notificationsPlayerID", "", time()-1, "/");
								Users::find(user("id"))->update([
									"login_key"                => null,
									"check_admin"              => null,
									"notifications_player_ids" => null
								]);
							@endphp
							<script>
								alert("Đăng xuất thành công, xin hẹn gặp lại");
								location.href="/";
							</script>
						@break;
					@endswitch
					</form>
				</div>
				@endforeach
					
			</section>

			<section class="flex-margin main-right">
				<div class="heading-simple">
					<div class="flex flex-middle">
						<div style="width: 60px">
							<span class="user-avatar"><img src="{!! user("avatar") !!}?t={{time()}}" /></span>
						</div>
						<div>
							<div>{!!user("name")!!}</div>
							<div style="font-size: 14px">{!!user("email")!!}</div>
						</div>
					</div>
				</div>
				<div class="list show-item-nav">
					@foreach($section as $type=>$item)
						<a {!! isset($item["confirm"]) ? 'onclick="return confirm(\''.$item["confirm"].'\')"' : '' !!} href="/user/profile/{{ $type }}" class="{{$tab==$type ? 'list-actived' : ''}}"><i class="fa-icon fa {{$item["icon"]}}"></i> {{$item["title"]}}{!! empty($item["count"]) ? '' : '<span class="badge">'.$item["count"].'</span>' !!}</a>
					@endforeach
				</div>
			</section>
		</div>

		<script type="text/javascript">

			//Cập nhật thông tin
			$(".form-edit").on("click", "button", function(){
				var thisForm=$(this).parent();
				var data=thisForm.serializeArray();
				$.post("", data, function(data){
					var msg=$(data).find(".edit-user-msg");
					if(msg.children(".alert-success").length>0){
						alert(msg.children().text());
						location.reload();
					}else{
						thisForm.children(".edit-user-msg").html(msg.html());
					}
				});
			});

			//Đổi mật khẩu
			$(".form-password").on("click", "button", function(){
				var thisForm=$(this).parent();
				var data=thisForm.serializeArray();
				$.post("", data, function(data){
					var msg=$(data).find(".change-password-msg");
					if(msg.children(".alert-success").length>0){
						alert(msg.children().text());
						location.reload();
					}else{
						thisForm.children(".change-password-msg").html(msg.html());
					}
				});
			});

			//Click nút đổi avatar
			$(".account-avatar-btn").click(function(){
				$(this).next()[0].click();
			});

			//Tiến hành upload
			$(".account-avatar-input").change(function(){
				$("#loading").show();
				var formData = new FormData();
				formData.append("avatar", $(this)[0].files[0]);
				$.ajax({
					url : "",
					type : "POST",
					data : formData,
					processData: false,
					contentType: false,
					success : function(response) {
						var msg=$(response).find("#account-avatar-upload-msg");
						if(msg.length>0){
							alert(msg.text());
						}else{
							alert("Cập nhật avatar thành công!");
							location.reload();
						}
					},
					error: function(){
						alert("Lỗi kết nối, vui lòng thử lại");
					},
					complete: function(){
						$("#loading").hide();
					}
				});
			});

			//Xoay avatar
			$(".account-avatar-rotate").click(function(){
				$("#loading").show();
				$.ajax({
					url : "",
					type : "POST",
					data : {avatarRotate: 90},
					success : function() {
						$(".account-avatar-img").attr("src", $(".account-avatar-img").attr("src")+"?t="+(new Date()));
					},
					error: function(){
						alert("Lỗi kết nối, vui lòng thử lại");
					},
					complete: function(){
						$("#loading").hide();
					}
				});
			});
		</script>
	</main>
@endsection


@section("script")
	
@endsection


@section("footer")
	@parent
@endsection
