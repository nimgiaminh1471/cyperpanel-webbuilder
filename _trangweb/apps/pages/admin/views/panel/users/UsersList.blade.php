@php
	use models\Posts;
	use models\Users;
	use models\PostsComments;
	use models\BuilderDomain;
	use models\CashFlow;
	use models\OnlineChatTable;
	use models\PaymentHistory;
	$gender = [
				0 => "Chưa cập nhật",
				1 => "Nam",
				2 => "Nữ",
				3 => "Khác"
			];
	$getTaskManagerUsers = Users::select('users.id', 'users.name', 'roles.label AS role_label')
		->leftJoin('roles_permissions', 'users.role', '=', 'roles_permissions.role_id')
		->leftJoin('roles', 'users.role', '=', 'roles.id')
		->where('permission_name', 'telesales')
		->get();
	$taskManagerUsers = [];
	foreach($getTaskManagerUsers as $id => $item){
		$taskManagerUsers[$item->role_label][$id] = (array)$item;
	}
	Assets::footer("/assets/form/date-picker.css", "/assets/form/date-picker.js");
@endphp
<div>
	<section class="width-100">
		<div id="users-filter" style="margin-bottom: 5px">
			<section class="section" style="box-shadow: none;">
				<div class="heading">
					<i class="fa fa-users"></i>
					DANH SÁCH NGƯỜI DÙNG
				</div>
				<div class="section-body">
					<div class="flex flex-middle flex-medium">
						<div class="width-50">
							<select class="width-49-small" name="user_filter[find_by]">
								<option value="name">Tên khách hàng</option>
								<option value="email">Email</option>
								<option value="id">ID</option>
							</select>
							<input class="width-49-small" placeholder="Tìm kiếm" type="text" name="user_filter[find_keyword]" />
						</div>
						<div class="width-50 right">
							<select class="width-49-small" name="user_filter[gender]">
								<option value="">Giới tính</option>
								<option value="1">Nam</option>
								<option value="2">Nữ</option>
								<option value="3">Khác</option>
							</select>
							<select class="width-49-small" name="user_filter[role]">
								<option value="">Chức vụ</option>
								@foreach(models\Role::all() as $role)
									<option value="{{ $role->id }}" {{(isset($filter) && $filter["role"] == $role->id ? 'selected' : '')}}>{{$role->label}}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div id="users-list">
			@php
				$where=[];
				$orderBy=[["id", "DESC"]];
				if(isset($_GET["user_filter"])){
					$filter=$_GET["user_filter"];
					if($filter["gender"]>0){
						$where[]=["gender", "=", $filter["gender"]];
					}
					if($filter["role"]>0){
						$where[]=["role", "=", $filter["role"]];
					}
					if($filter["find_by"]=="id"){
						$where[]=["id", "=", $filter["find_keyword"]];
					}else{
						$where[]=[$filter["find_by"], "LIKE", "%{$filter["find_keyword"]}%"];
					}
					$orderBy=[[$filter["orderBy"], $filter["orderType"]]];
				}
				$users=Users::where("id", "!=", user("id"))->where($where)->orderBy($orderBy)->paginate(10);
			@endphp
			<div class="table-responsive">
				<table class="table table-border width-100" style="min-width: 900px">
					<tr>
						<th style="text-align: center">ID</th>
						<th>Tên khách hàng</th>
						<th>Email</th>
						<th>Điện thoại</th>
						<th>Ngày tạo</th>
						<th>Chức vụ</th>
						<th style="text-align: center;">Website</th>
					</tr>
					@foreach($users as $u)
						<tr data-id="{{$u->id}}" class="link" title="Ấn để chỉnh sửa">
							<td class="center">{{$u->id}}</td>
							<td>
								<span class="user-avatar">
									<img src="{{user("avatar", $u->id)}}" />
								</span>
								{!! user("name_color", $u->id, false) !!}
							</td>
							<td>{{$u->email}}</td>
							<td>{{$u->phone}}</td>
							<td>{{ date("H:i - d/m/Y", timestamp($u->created_at) ) }}</td>
							<td>{{Users::role($u->role)}}</td>
							<td class="center">{{ BuilderDomain::where("users_id", $u->id)->total() }}</td>
						</tr>
						@php
						if( empty($u->login_key) ){
							$loginKey = md5("".$u->email."".randomString(50)."");
							Users::find($u->id)->update([
								"login_key"=>$loginKey
							]);
						}
						@endphp
					@endforeach
				</table>
			</div>
			<div class="alert-info">Tổng số thành viên: <b>{{$users->total()}}</b></div>
			{!!$users->links([
				"ajaxLoad"=>""
			])!!}
		</div>
	</section>

	<section class="flex flex-large flex-margin" style="margin-top: 20px">
		@if( permission("add_user") )
			<form class="form users-add-form width-40 flex-margin">
				<div class="panel panel-warning">
					<div class="heading link">Thêm thành viên</div>
					<div class="panel-body hidden">
						@php
							$addTemplate=[
								"name"=>"Tên khách hàng",
								"gender"=>"Giới tính",
								"birthday"=>"Sinh nhật",
								"email"=>"Email",
								"phone"=>"Điện thoại",
								"password"=>"Mật khẩu",
								"role"=>"Chức vụ",
								"company"=>"Tổ chức",
								"address"=>"Địa chỉ",
							];
						@endphp
						@foreach($addTemplate as $key=>$label)
							@switch($key)
								@case("gender")
									<select class="width-100-small" name="add_user[gender]">
										<option value="">Giới tính</option>
										<option value="1">Nam</option>
										<option value="2">Nữ</option>
										<option value="3">Khác</option>
									</select>
								@break
								@case("role")
									<select class="width-100" name="add_user[role]">
										<option value="">Chức vụ</option>
										@foreach(models\Role::all() as $role)
											@if($role->id != 1)
												<option value="{{ $role->id }}">{{$role->label}}</option>
											@endif
										@endforeach
									</select>
								@break
								@case("birthday")
									<div class="form-date-wrap" data-format="day/month/year">
										<div class="form-date-picker form-date-picker-bottom hidden"></div>
										<div class="input-icon">
											<i class="fa fa-calendar"></i>
											<input class="input width-100 input-disabled" placeholder="Sinh nhật" type="text" name="add_user[birthday]" value="" readonly=""/>
										</div>
										<code>{"allow":{"hours":[],"minutes":"","requiredHour":false,"days":"","months":"","weekDay":["mon","tue","wed","thu","fri","sat","sun"],"min":{"y":{{ (date("Y") - 100) }},"m":1,"d":1},"max":{"y":{{ (date("Y") - 10) }},"m":2,"d":14}},"value":{"day":"{{ date("d") }}","month":"{{ date("m") }}","year":"{{ date("Y") }}"}}</code>
									</div>
								@break
								@case("company")
								@case("address")
									<textarea class="width-100" placeholder="{{$label}}" name="add_user[{{$key}}]"></textarea>
								@break
								@default
									<input class="width-100" placeholder="{{$label}}" type="text" name="add_user[{{$key}}]" />
							@endswitch
						@endforeach
						<div class="users-add-msg" style="margin-bottom: 5px">
							@php
								if(isset($_POST["add_user"])){
									$required = [
										"name",
										"gender",
										"email",
										"phone",
										"password",
										"role"
									];
									$data = $storage = [];
									extract($_POST["add_user"]);
									if( Users::where("email", $email)->exists() ){
										$msg="Email đã tồn tại trên hệ thống";
									}
									if( Users::where("phone", $phone)->exists() ){
										$msg="SĐT đã tồn tại trên hệ thống";
									}
									if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
										$msg="Email không hợp lệ";
									}
									if( $_POST["add_user"]["role"] == 1 ){
										$msg = "Không được đặt chức vụ admin";
									}
									foreach($addTemplate as $name=>$label){
										$value = $_POST["add_user"][$name];
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
									if(empty($msg)){
										$data["password"]=passwordCreate($data["password"]);
										$data["name"]=ucwords($data["name"]);
										$data["gender"]=1;
										$data["storage"] = serialize($storage);
										Users::create($data);
										$msg='<div class="alert-success">Đã thêm tài khoản</div>';
									}else{
										$msg='<div class="alert-danger">'.$msg.'</div>';
									}
									echo $msg;
								}
							@endphp
						</div>
						<button class="width-100 btn-primary" type="button">Thêm</button>
						@php
							$form[]=["type"=>"switch", "name"=>"register", "title"=>"Cho phép đăng ký thành viên", "value"=>1];
							echo Form::create([
								"form"=>$form,
								"function"=>"Storage::option",
								"prefix"=>"",
								"name"=>"storage[option]",
								"class"=>"menu",
								"hover"=>false
							]);
						@endphp
					</div>
				</div>
			</form>
		@endif
		<div class="width-60 flex-margin">
		@php
			//Gửi thông báo
			if(isset($_POST["notify"])){
				$notify=Storage::option("notify",[]);
				switch($_POST["action"]??"add"){

					//Xóa
					case "delete":
						unset($notify[ $_POST["id"] ]);
					break;

					//Cập nhật
					case "update":
						if( empty($_POST["notify"]["role"]) ){
							unset($notify[ $_POST["id"] ]);
						}else{
							$notify[ $_POST["id"] ]=$_POST["notify"];
						}
					break;

					//Thêm
					default:
						if( !empty($_POST["notify"]["title"]) ){
							$notify=array_replace_recursive([time()=>$_POST["notify"]], $notify);
						}
				}
				$notify=array_slice($notify, 0, 5, true);
				Storage::update("option", ["notify"=>$notify]);
			}

			//Form
			echo '
			<div class="panel panel-warning '.(permission('send_notification') ? '' : 'hidden').'">
				<div class="heading link">Gửi thông báo tới thành viên</div>
				<div class="panel-body hidden">
			';
			$form=[];
			$form[]=["type"=>"text", "name"=>"title", "title"=>"Tiêu đề thông báo", "note"=>"", "value"=>"", "attr"=>''];
			$form[]=["type"=>"editor", "name"=>"content", "title"=>"Nội dung thông báo", "value"=>"<p>Xin chào [@name] </p>", "post"=>0];
			$form[]=["type"=>"checkbox", "name"=>"role", "title"=>"Đối tượng nhận", "checkbox"=>models\Users::role() ];
			$form[]=["html"=>'<div class="menu center"><button type="button" class="notify-add btn-primary">Thêm thông báo</button></div>'];
			echo '<form id="notify-add-form">';
				echo Form::create([
					"form"=>$form,
					"function"=>"",
					"prefix"=>"",
					"name"=>"notify",
					"class"=>"menu",
					"hover"=>false
				]);
			echo '</form>';
			$notifyData=Storage::option("notify", []);
			if( !empty($notifyData) ){
				echo '<div class="heading-simple" style="margin-top: 10px">Danh sách thông báo</div>';
			}
			foreach($notifyData as $time=>$item){
				echo '
				<div class="panel panel-danger notify-item" data-id="'.$time.'">
					<div class="heading link">
						<i class="fa fa-info-circle"></i> '.$item["title"].'
						<input type="hidden" name="notify[title]" value="'.$item["title"].'" />
					</div>
					<div class="panel-body hidden">
						'.str_replace(["[@name]"], [user("name")], $item["content"]).'
						<textarea class="hidden" name="notify[content]">'.$item["content"].'</textarea>
					';
					$form=[];
					$form[]=["type"=>"checkbox", "name"=>"role", "title"=>"Đối tượng nhận", "value"=>$item["role"]??[], "checkbox"=>models\Users::role() ];
					echo Form::create([
						"form"=>$form,
						"function"=>"",
						"prefix"=>"",
						"name"=>"notify",
						"class"=>"menu",
						"hover"=>false
					]);
					echo'
						<div class="menu center">
							<button type="button" data-action="update" class="btn-primary">Cập nhật</button>
							<button type="button" data-action="delete" class="btn-danger">Xóa</button>
						</div>
					</div>
				</div>
				';
			}
			echo '</div></div>';
			echo '
			<script>

				//Thêm mới
				$("#notify-add-form .notify-add").on("click", function(){
					var checkedNum = $(this).parents("form").find("[name=\"notify[role][]\"]:checked").length;
					if( !checkedNum){
						var wrong="Vui lòng chọn đối tượng nhận thông báo";
					}
					if( $(this).parents("form").find("[name=\"notify[title]\"]").val().length==0 ){
						var wrong="Vui lòng nhập tiêu đề";
					}
					if(typeof wrong=="undefined"){
						$(this).hide();
						$.ajax({
							type: "POST",
							url: "",
							data: $("#notify-add-form").serialize(),
							success: function(data){
								location.reload();
							}
						});
					}else{
						alert(wrong);
					}
				});

				//Click xóa - cập nhật
				$(".notify-item button").on("click", function(){
					var outer=$(this).parents(".notify-item");
					$.ajax({
						type: "POST",
						url: "",
						data: outer.find("input,textarea").serialize()+"&action="+$(this).attr("data-action")+"&id="+outer.attr("data-id"),
						success: function(data){
							location.reload();
						}
					});
				});
			</script>
			';
		@endphp
	</div>
	</section>
</div>
@php
	//Chi tiết tài khoản
	$user=Users::find($_GET["id"]??0);
	if(isset($user->id)){
		$storage=unserialize($user->storage);
		if($user->id==user("id")){
			echo '<script>location.href="/admin/UsersList";</script>';
			die;
		}
		$modalTitle=$user->name;
		$modalBody='
		<input type="hidden" name="edit_user[id]" value="'.$user->id.'" />
		<section class="flex flex-large flex-middle" style="margin: 0 5px">
			<div class="width-50">
				<div class="menu bd center" style="margin-bottom: 2px"><img src="'.user("avatar", $user->id).'" /></div>
				<table class="table table-border width-100">
		';
		foreach([
			"id"             => "#ID",
			"phone"          => "Số điện thoại",
			"email"          => "Email",
			"name"           => "Họ tên",
			"gender"         => "Giới tính",
			"birthday"       => "Sinh nhật",
			"created_at"     => "Ngày đăng ký",
			"last_online"    => "Lần online cuối",
			"website"        => "Website đã tạo",
			"posts_count"    => "Bài viết đã đăng",
			"posts_comments" => "Bình luận",
			"facebook"       => "Link Facebook",
			"description"    => "Giới thiệu",
			"company"        => "Tổ chức",
			"address"        => "Địa chỉ",
			"apps"           => "Ứng dụng đã mua"
		] as $key=>$label){
			$out=$user->$key??$storage[$key]??null;
			$value=$out;
			switch($key){
				case "phone":
					$value='<a href="tel:'.$user->$key.'">'.$user->$key.'</a>';
				break;
				case "apps":
					$out = \models\AppStoreOwned::where("user_id", $user->id)->total();
					$value = '<a target="_blank" href="/admin/AppStoreManager?user_id='.$user->id.'">'.$out.' (Bấm để xem)</a>';
				break;
				case "website":
					$out=BuilderDomain::where("users_id", $user->id)->total();
					$value='<a target="_blank" href="/admin/WebsiteList?uid='.$user->id.'">'.$out.' (Bấm để xem)</a>';
				break;
				case "posts_count":
					$value='<a target="_blank" href="/admin/PostsList?uid='.$user->id.'">'.$user->$key.' (Bấm để xem)</a>';
				break;
				case "posts_comments":
					$out=PostsComments::where("users_id", $user->id)->total();
					$value='<a target="_blank" href="/admin/PostsComments?uid='.$user->id.'">'.$out.' (Bấm để xem)</a>';
				break;
				case "created_at":
					$value=date("d/m/Y", timestamp($out) );
				break;
				case "last_online":
					if(!empty($out)){
						$value=date("H:i - d/m/Y", timestamp($out) );
					}
				break;
				case "facebook":
					$value='<a target="_blank" href="'.$out.'">'.$out.'</a>';
				break;
				case "gender":
					$value = $gender[$value];
				break;
			}
			if( !empty($out) ){
				$modalBody.='
				<tr>
					'.(empty($label) ? '' : '
					<td class="width-40 menu">'.$label.'</td>
					').'
					<td class="width-'.(empty($label) ? '100' : '60').' menu">'.$value.'</td>
				</tr>
				';
			}
		}
		$modalBody.='
			</table>
			<div class="menu">
				Link đăng nhập nhanh:
				<br>
				<input style="margin-top: 2px" class="width-100" placeholder="Link đăng nhập nhanh" type="text" value="'.HOME.'/user/login/?login_key='.user("login_key", $user->id).'" />
			</div>
		</div>
		<div class="width-50">
			'.( permission("accountant") ? '
				<div class="pd-5">
					<div style="padding: 10px 0">
						Số dư trong ví (<a href="/admin/PaymentHistory?uid='.$user->id.'" target="_blank">Xem lịch sử GD</a>):
					</div>
					<input class="width-100 input-currency" placeholder="Số tiền" type="text" name="edit_user[money]" value="'.number_format(user("money", $user->id)).'" disabled>
				</div>

				<form method="POST" class="pd-5">
					<div class="panel panel-default">
						<div class="heading link">Tạo đơn thu tiền</div>
						<div class="panel-body hidden">
							<input type="hidden" name="payment[users_id]" value="'.$user->id.'">
							<div class="pd-5">
								<input class="input-currency width-100 input" name="payment[amount]" placeholder="Số tiền thanh toán">
							</div>
							<div class="pd-5">
								<textarea class="width-100" name="payment[note]" placeholder="Lý do thu tiền"></textarea>
							</div>
							<div class="pd-5">
								<input class="btn-info" type="submit" value="Xác nhận">
							</div>
						</div>
					</div>
				</form>
			' : '
				<div class="pd-5">
					Số dư trong ví: <b>'.number_format( user("money", $user->id) ).'</b>
				</div>
			').'
		
		<div class="pd-5">
			<textarea rows="5" class="width-100" placeholder="Gửi thông báo đến tài khoản" name="edit_user[message]">'.user("message", $user->id).'</textarea>
		</div>
		';

		$modalBody .= '
		<div class="pd-5">
			<div class="input-label" style="margin-top: 20px">
				<span>Người phụ trách</span>
				<select name="edit_user[support_user_id]" class="width-100 form-field" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
					<option value="">Chọn</option>
		';
				foreach($taskManagerUsers as $role_label => $items){
					$modalBody .= '<optgroup label="'.$role_label.'">';
						foreach($items as $item){
							$modalBody .= '<option value="'.$item['id'].'" '.(user("support_user_id", $user->id) == $item['id'] ? 'selected' : '').'>
								'.$item['name'].'
							</option>';
						}
					$modalBody .= '</optgroup>';
				}
		$modalBody .= '
				</select>
			</div>
		</div>
		';

		if( permission('change_user_password') ){
			$modalBody .='<div class="pd-5"><input class="width-100" placeholder="Đổi mật khẩu mới" type="text" name="edit_user[password]" /></div>';
		}
		if( permission("admin") ){
			$modalBody.='<div class="pd-5"><select class="width-100" name="edit_user[role]">';
			foreach(Users::role() as $level=>$label){
				$modalBody.='<option value="'.$level.'" '.($user->role==$level ? 'selected' : '').'>'.$label.'</option>';
			}
			$modalBody.='</select></div>';
		}
		if( permission('online_chat_manager', $user->id) ){
			$modalBody.='
				<div class="pd-5">
					<select class="width-100" name="edit_user[email_notify]">
						<option value="0">Chỉ nhận thông báo qua trình duyệt</option>
						<option value="1" '.($user->email_notify == 1 ? 'selected' : '').'>Nhận qua trình duyệt & email</option>
					</select>
				</div>
			';
		}
		$modalBody.='
		<div class="pd-5"><button class="width-100 btn-primary" type="button">Cập nhật</button></div>
		'.(permission('admin') ? '
			<div class="pd-5">
				'.( BuilderDomain::where("users_id", $user->id)->exists() ? '<div class="alert-danger">Để xóa tài khoản này, hãy xóa website trước</div>' : '<button data-id="'.$user->id.'" class="width-100 btn-danger" type="button">Xóa tài khoản & bài viết</button>').'
			</div>
		' : '').'
		</section>
		';
		 $modalShow=true;
	}

	// Cập nhật tài khoản
	if(isset($_POST["edit_user"])){
		$data=[];
		$data["role"]=$_POST["edit_user"]["role"];
		Users::updateStorage($_POST["edit_user"]["id"], [
			"message"         => $_POST["edit_user"]["message"],
			"support_user_id" => $_POST["edit_user"]["support_user_id"]
		]);
		if(!empty($_POST["edit_user"]["password"])){
			$data["password"]=passwordCreate($_POST["edit_user"]["password"]);
		}
		if( isset($_POST["edit_user"]["email_notify"]) ){
			$data["email_notify"] = $_POST["edit_user"]["email_notify"];
		}
		if( isset($_POST["edit_user"]["money"]) ){
			$data["money"] = vnStrFilter($_POST["edit_user"]["money"], "");
		}

		Users::find($_POST["edit_user"]["id"])->update($data);
	}

	// Tạo đơn thanh toán
	if( isset($_POST["payment"]) && $_POST["payment"]["amount"] > 0 ){
		$data             = $_POST["payment"];
		$data["amount"]   = intval( vnStrFilter($data["amount"]??0, "") );
		if( user('money', $data['users_id']) >= $data["amount"] ){
			userPayment($data["amount"], $data['users_id']);
			userPaymentHistory([
				"name"     => "Thanh toán",
				"amount"   => -$data["amount"],
				"note"     => $data['note'],
				"users_id" => $data['users_id']
			]);
			redirect('/admin/PaymentHistory?uid='.$data['users_id'], true);
		}else{
			redirect(THIS_URL, true, 'Số dư của tài khoản này không đủ: '.number_format($data["amount"]) );
		}
		
	}


	// Xóa tài khoản
	if(isset($_POST["delete_user"])){
		$uid=$_POST["delete_user"];
		$postsByUser=[];
		foreach(Posts::where("users_id", $uid)->get() as $p){
			$postsByUser[]=$p->id;
		}
		Gallery::deleteByUserId($uid);
		Posts::deletePosts($postsByUser);
		$avatarPath=PUBLIC_ROOT."/files/users/avatars/".$uid.".png";
		if( file_exists($avatarPath) ){
			unlink($avatarPath);
		}
		PostsComments::where("users_id", $uid)->orWhere("reply_user", $uid)->delete();
		CashFlow::where("users_id", $uid)->delete();
		OnlineChatTable::deleteConversation($uid);
		PaymentHistory::where("users_id", $uid)->delete();
		Users::destroy($uid);
	}
@endphp
{!!modal('user-detail', $modalTitle??"", '<div class="panel-outer">'.($modalBody??"").'</div>','950px', $modalShow??false, true)!!}

<script type="text/javascript">
	// Thêm tài khoản
	$(".users-add-form").on("click", "button", function(){
		var thisForm=$(".users-add-form");
		var data=thisForm.serializeArray();
		$.post("", data, function(data){
			var msg=$(data).find(".users-add-msg");
			if(msg.children(".alert-success").length>0){
				location.reload();
			}else{
				thisForm.find(".users-add-msg").html(msg.html());
			}
		});
	});

	// Click xem chi tiết từng tài khoản
	function showUserDetail(id){
		$.ajax({
			url: '',
			data: {id: id},
			success: function(data){
				var filter = $(data).find(".modal-user-detail").html();
				$(".modal-user-detail").show().html(filter);
				inputCurrencyInstall();
				panelClickInstall();
				inputLabelInit();
			},
			complete: function(){
				$('#loading').hide();
			},
			error: function(){
				setTimeout(function(){
					showUserDetail(id);
				}, 1000);
			}
		});
	}
	$("#users-list").on("click", "tr", function(){
		$('#loading').show();
		var id = $(this).attr("data-id");
		if(typeof id=="undefined"){
			return false;
		}
		$(".table-actived").removeClass("table-actived");
		$(this).addClass("table-actived");
		showUserDetail(id);
	});

	// Click cập nhật thông tin tài khoản
	$(".modal-user-detail").on("click", ".btn-primary", function(){
		var data=$(".modal-user-detail").find("input,select,textarea").serializeArray();
		console.log(data);
		$.post("", data, function(data){
			$(".modal-user-detail").hide();
			updateUsersList();
		});
	});

	// Click xóa tài khoản
	$(".modal-user-detail").on("click", ".btn-danger", function(){
		if(confirm("Nếu xóa nick, bài viết cũng sẽ bị xóa, xác nhận?")){
			$.post("", {delete_user: $(this).attr("data-id")}, function(data){
				$(".modal-user-detail").hide();
				updateUsersList();
			});
		}
	});

	// Cập nhật lại danh sách thành viên
	function updateUsersList(){
		var data=$("#users-filter").find("input,select").serializeArray();
		var orderEl=$(".users-list-sort.blue");
		if(typeof orderEl.attr("data-sort")=="undefined"){
			var orderBy="id";
			var orderType="DESC";
		}else{
			var orderBy=orderEl.attr("data-sort");
			var orderType=orderEl.attr("data-type");
		}
		var mergeData=[
			{name: "user_filter[orderBy]", value: orderBy},
			{name: "user_filter[orderType]", value: orderType},
			{name: "page", value: $("#users-list .paginate-current").text()}
		];
		var data=data.concat(mergeData);
		$.get("", data ,function(data){
			var filter=$(data).find("#users-list").html();
			$("#users-list").html(filter);
		});
	}

	// Lọc dữ liệu
	$("#users-filter").on("change keyup", "input,select", function(){
		updateUsersList();
	});

	// Click sắp xếp
	$("#users-list").on("click", ".users-list-sort", function(){
		$(".users-list-sort").removeClass("blue");
		$(this).addClass("blue");
		var type=$(this).attr("data-type");
		var type=(type=="ASC" ? "DESC" : "ASC");
		$(this).attr("data-type", type);
		updateUsersList();
	});

	// Click chuyển trang
	$("#users-list").on("click", ".paginate>li>a", function(){
		$("#users-list").find(".paginate-current").text($(this).attr("data-page"));
		updateUsersList();
	});

	// Lưu cài đặt
	$(".users-add-form").on("change", function(){
		var data=$(this).serializeArray();
		data.push({name: "settingsSave", value: 1});
		$.post("/admin", data, function(data){
			
		});
	});
</script>