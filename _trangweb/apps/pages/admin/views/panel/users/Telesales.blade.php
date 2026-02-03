@php
	use models\Users;
	use models\BuilderDomain;
	$gender = [
				0 => "Chưa cập nhật",
				1 => "Nam",
				2 => "Nữ",
				3 => "Khác"
			];
	$contactStatus = [
		'pending'       => 'Chưa liên hệ',
		'calling'       => 'Đang gọi',
		'no_connection' => 'Không liên lạc được',
		'call_later'    => 'Khách báo gọi lại sau',
		'accept'        => 'Khách đồng ý',
		'deny'          => 'Chốt thất bại'
	];
	$contactStatusCss = [
		'pending'       => 'warning',
		'calling'       => 'info',
		'no_connection' => 'danger',
		'call_later'    => 'warning',
		'accept'        => 'success',
		'deny'          => 'default'
	];
	Assets::footer("/assets/form/date-picker.css", "/assets/form/date-picker.js");
@endphp
<div>
	<section class="width-100">
		<div id="users-filter" style="margin-bottom: 5px">
			<section class="section" style="box-shadow: none;">
				<div class="heading">
					<i class="fa fa-headphones"></i>
					TƯ VẤN KHÁCH HÀNG
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
					if($filter["find_by"]=="id"){
						$where[]=["id", "=", $filter["find_keyword"]];
					}else{
						$where[]=[$filter["find_by"], "LIKE", "%{$filter["find_keyword"]}%"];
					}
					$orderBy=[[$filter["orderBy"], $filter["orderType"]]];
				}
				$users=Users::where("role", "=", 2)->where($where)->orderBy($orderBy)->paginate(10);
			@endphp
			<div class="table-responsive">
				<table class="table table-border width-100">
					<tr>
						<th style="width: 200px">Tên khách hàng</th>
						<th style="width: 180px; text-align: center;">Trạng thái</th>
						<th style="min-width: 220px">Người gọi / ghi chú</th>
						<th style="width: 180px">Điện thoại</th>
						<th style="width: 180px">Đăng ký lúc</th>
					</tr>
					@foreach($users as $u)
						<tr data-id="{{$u->id}}" class="link" title="Ấn để chỉnh sửa">
							<td>
								{!! user("name_color", $u->id, false) !!}
								<br>
								<span class="gray">
									{{$u->email}}
								</span>
							</td>
							<td class="center">
								<div>
									@php
										$cStatus = user("contact_status", $u->id) ?? 'pending';
									@endphp
									<span class="label-{{ $contactStatusCss[ $cStatus ] }}" style="font-size: 13px">
										{{ $contactStatus[ $cStatus ] }}
									</span>
								</div>
							</td>
							<td>
								@if( user("contact_user_id", $u->id) )
									<div>
										<i class="fa fa-stick-o"></i>
										{!! user('name_color', user("contact_user_id", $u->id) ) !!}
										<span style="color: gray; font-size: 13px">
											<i class="fa fa-clock-o"></i> {{ dateText( user("contact_time", $u->id) ) }} 
										</span>
									</div>
									@if( user("contact_note", $u->id) )
										<div style="color: green; font-size: 14px; padding: 5px">
											<i class="fa fa-info-circle"></i>
											{{ user("contact_note", $u->id) }}
										</div>
									@endif
								@endif
							</td>
							<td>{{$u->phone}}</td>
							<td>
								{{ dateText( timestamp($u->created_at) ) }}
							</td>
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
		<input type="hidden" name="user[id]" value="'.$user->id.'" />
		<section class="flex flex-large flex-middle" style="margin: 0 5px">
			<div class="width-50">
				<div class="menu bd center" style="margin-bottom: 2px"><img src="'.user("avatar", $user->id).'" /></div>
				<table class="table table-border width-100">
		';
		foreach([
			"id"               => "#ID",
			"phone"            => "Số điện thoại",
			"email"            => "Email",
			"name"             => "Họ tên",
			"gender"           => "Giới tính",
			"birthday"         => "Sinh nhật",
			"created_at"       => "Đăng ký lúc",
			"last_online"      => "Lần online cuối",
			"website"          => "Website đã tạo",
			"facebook"         => "Link Facebook",
			"description"      => "Giới thiệu",
			"company"          => "Tổ chức",
			"address"          => "Địa chỉ",
			"register_message" => "Lời nhắn"
		] as $key=>$label){
			$out=$user->$key??$storage[$key]??null;
			$value=$out;
			switch($key){
				case "phone":
					$numberPhone = $user->phone;
					if( !empty( Storage::option('other_option_prefix_phone') ) ){
						$numberPhone = Storage::option('other_option_prefix_phone').intval($numberPhone);
					}
					$value='
						<div class="flex flex-middle">
							<div style="width: calc(100% - 40px)">
								<a class="label-info" style="color: white" href="tel:'.$numberPhone.'" onclick="userContactCall('.$user->id.', \''.$numberPhone.'\')">
									<i class="fa fa-phone"></i>
									'.$user->$key.'
								</a>
							</div>
							<div style="text-align: right; width: 40px">
								<a  '.(device() == 'desktop' ? 'target="_blank"' : '' ).' href="https://zalo.me/'.$user->phone.'">
									<img src="/assets/images/zalo-icon.png" style="height: 25px">
								</a>
							</div>
						</div>
					';
				break;
				case "website":
					$out = BuilderDomain::where("users_id", $user->id)->total();
					$value='<a target="_blank" href="/admin/WebsiteList?uid='.$user->id.'">'.$out.' (Bấm để xem)</a>';
				break;
				case "created_at":
					$value = date("H:i - d/m/Y", timestamp($out) ).' ('.dateText( timestamp($out) ).')';
				break;
				case "last_online":
					if(!empty($out)){
						$value=date("H:i - d/m/Y", timestamp($out) );
					}
				break;
				case "facebook":
					$value='<a target="_blank" href="'.$out.'">'.$out.'</a>';
				break;
				case "register_message":
					$value='<span class="blue">'.$out.'</span>';
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
		</div>
		<div class="width-50" style="padding-bottom: 20px">
		<div class="pd-5">
			<textarea rows="5" class="width-100" placeholder="Nhập ghi chú" name="user[contact_note]"></textarea>
		</div>
		';
		$modalBody.='<div class="pd-5"><select class="width-100" name="user[contact_status]" onchange="userChangeContactStatus(this)">';
		$modalBody.='<option value="">Chọn trạng thái</option>';
			foreach($contactStatus as $id => $label){
				if( in_array($id, ['pending', 'calling']) ){
					continue;
				}
				$modalBody.='<option value="'.$id.'">'.$label.'</option>';
			}
		$modalBody.='</select></div>';
		$modalBody.='
		<div class="pd-5 hidden user-contact-submit-btn">
			<button class="width-100 btn-primary" type="button">Cập nhật</button>
		</div>
		</section>
		';
		$contactHistory = user('contact_history', $user->id);
		if( !empty($contactHistory) ){
			$modalBody .= '
				<table class="table table-border table-hover width-100">
					<thead>
						<th style="width: 200px">
							Người gọi
						</th>
						<th style="width: 200px">
							Thời gian gọi
						</th>
						<th style="width: 200px">
							Trạng thái
						</th>
						<th>
							Ghi chú
						</th>
					</thead>
					<tbody>
			';
			foreach($contactHistory as $item){
				$modalBody .= '
					<tr>
						<td>
							'.user('name_color', $item['contact_user_id']).'
						</td>
						<td>
							'.date('H:i - d/m/Y', $item['contact_time']).'
						</td>
						<td>
							'.$contactStatus[ $item['contact_status'] ].'
						</td>
						<td>
							'.$item['contact_note'].'
						</td>
				</tr>
				';
			}
			$modalBody .= '
					</tbody>
				</table>
			';
		}
		$modalBody .= '
			<template id="contact-data">
				{
					"user_id": '.user('id').',
					"contact_status": "'.user('contact_status', $user->id).'",
					"contact_user_id": "'.user('contact_user_id', $user->id).'",
					"contact_user_name": "'.user('name', user('contact_user_id', $user->id) ).'"
				}
			</template>
		';
		$modalShow=true;
	}

	// Cập nhật tài khoản
	if(isset($_POST["user"])){
		$data                    = $_POST["user"];
		$data['contact_user_id'] = user('id');
		$data['contact_time']    = time();
		unset($data['id']);
		$data['contact_history'] = user('contact_history', $_POST["user"]["id"]);
		if( !is_array($data['contact_history']) ){
			$data['contact_history'] = [];
		}
		$data['contact_history'][] = $data;
		Users::updateStorage($_POST["user"]["id"], $data);
	}
@endphp
{!!modal($modalTitle??"", '<div class="panel-outer">'.($modalBody??"").'</div>', 'user-detail', '950px', $modalShow??false, true)!!}

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
				var contactData = JSON.parse( $(filter).find('#contact-data').html() );
				if( contactData.contact_status == 'calling' && contactData.contact_user_id != contactData.user_id ){
					alert('Khách hàng này đang được gọi bởi: '+contactData.contact_user_name);
				}else{
					$(".modal-user-detail").show().html(filter);
					inputCurrencyInstall();
					panelClickInstall();
				}
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
		$('.user-contact-submit-btn').hide();
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

	/*
	 * Thay đổi trạng thái gọi
	 */
	function userChangeContactStatus(thisEl){
		var value = $(thisEl).val();
		if( value.length == 0 ){
			$('.user-contact-submit-btn').slideUp();
		}else{
			$('.user-contact-submit-btn').slideDown();
		}
	}

	/*
	 * Click gọi
	 */
	function userContactCall(uid, phone){
		var data = [];
		data.push({
			name: 'user[id]',
			value: uid
		});
		data.push({
			name: 'user[contact_note]',
			value: ''
		});
		data.push({
			name: 'user[contact_status]',
			value: 'calling'
		});
		$.ajax({
			url : '',
			type : 'POST',
			data: data,
			success: function(){
				updateUsersList();
			},
			error: function(){
				userContactCall(uid, phone);
			}
		});
	}
	/*
	 * Load lại danh sách khi chuyển tab
	 */
	window.onfocus = function(){
		updateUsersList();
	}

	setInterval(function(){
		if( document.hasFocus() ){
			updateUsersList();
		}
	}, 20000);
	$(document).on('click', 'tr', function(){
		updateUsersList();
	});
</script>