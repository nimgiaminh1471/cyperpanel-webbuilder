@php
	$getRoles = \models\Role::all();
	$getPermissions = \models\Permission::all();
@endphp
<section class="flex flex-large" id="roles-permissions-manager">
	<div class="width-50 flex-margin">
		<h2 class="heading-block">Danh sách chức vụ</h2>
		
		{{-- Danh sách chức vụ --}}
		<div id="roles-list" class="list-item">
			@php
				$rolesListOption = '<option value="">Chuyển chức của thành viên sang</option>';
			@endphp
			@foreach($getRoles as $r)
				@php
					$rolesListOption .= '<option value="'.$r->id.'">'.$r->label.'</option>';
				@endphp
				<div class="flex flex-middle menu pd-20" style=" margin-bottom: 10px">
					<div class="width-50" style="color: {{ $r->color }}; font-weight: bold;">
						{{ $r->label }}
					</div>
					<div class="width-50 right">
						<span onclick="editRole(this)" class="label-info link">
							<i class="fa fa-pencil"></i>
							 Sửa
							<template>
								{!! json_encode($r) !!}
							</template>
						</span>
						<span onclick="setPermissions(this)" data-label="{{ $r->label }}" data-id="{{ $r->id }}" class="label-warning link">
							<i class="fa fa-lock"></i>
							Thiết lập quyền
							<template>
								{!! json_encode( models\Role::getPermissionsByRole($r->id) ) !!}
							</template>
						</span>
						@if( empty($r->default) )
							<span onclick="deleteRole({{ $r->id }}, '{{ $r->label }}')" class="label-danger link">
								<i class="fa fa-trash"></i>
								Xóa
							</span>
						@endif
					</div>
				</div>
			@endforeach
			<template id="roles-list-option">
				{!! $rolesListOption !!}
			</template>
		</div>

		{{-- Chỉnh sửa chức vụ --}}
		{!! modal('edit-role', 'Chỉnh sửa chức vụ', '<section class="bg pd-10 ajax-form"></section>','450px', false, true) !!}

		{{-- Thiết lập quyền --}}
		<div class="modal modal-set-permissions hidden modal-allow-close modal-allow-scroll">
			<div class="modal-body" style="max-width:650px">
				<div class="modal-content">
					<div class="heading modal-heading">Thiết lập quyền: <span class="set-permission-label" style="font-weight: bold;"></span> <i class="modal-close link fa"></i></div>
					<div>
						<section class="bg pd-10 ajax-form">
							<div class="flex flex-middle">
								@foreach($getPermissions as $p)
									@if($p->name == "member")
										<div class="hidden">
											<input type="checkbox" name="set_permission[permissions][]" value="{{ $p->name }}" checked>
										</div>
										@continue
									@endif
									<div class="width-50 pd-2">
										<div class="flex menu bd flex-middle item-hover set-permissions-item">
											<div class="width-70">{{ $p->label }}</div>
											<div class="width-30 right">
												<label class="switch">
													<input type="checkbox" name="set_permission[permissions][]" value="{{ $p->name }}">
													<s></s>
												</label>
											</div>
										</div>
									</div>
								@endforeach
							</div>
							<div class="form-notify hidden form-mrg"></div>
							<div class="hidden form-append-id"></div>
							<div class="form-mrg center pd-10">
								<button class="btn-primary form-submit-button" onclick="setPermissionsSubmit(this)" type="button">Lưu lại</button>
							</div>
						</section>
					</div>
				</div>
			</div>
		</div>

		{{-- Danh sách quyền --}}
		<div class="panel panel-info">
			<div class="heading link">
				<i class="fa fa-ol-list"></i>
				Danh sách quyền
			</div>
			<div class="panel-body hidden">
				@foreach($getPermissions as $p)
					<div class="menu bd-bottom">
						<b>{{ $p->label }}:</b> {{ $p->name }}
					</div>
				@endforeach
			</div>
		</div>

		{{-- Xóa chức vụ --}}
		{!!
			modal('delete-role', 'Xóa chức vụ', '
				<section class="bg pd-10 ajax-form">
					<div class="form-mrg center">
						<select name="role[move_to]">
							'.$rolesListOption.'
						</select>
					</div>
					<div class="form-notify hidden form-mrg"></div>
					<div class="form-mrg center">
						<button class="btn-primary form-submit-button" onclick="deleteRoleSubmit(this)" type="button">Xác nhận xóa</button>
					</div>
					<div class="hidden form-append-id"></div>
				</section>
			','450px', false, true)
		!!}
	</div>


	<div class="width-50 flex-margin">
		<h2 class="heading-block">Thêm chức vụ</h2>

		{{-- Thêm chức vụ --}}
		<section class="ajax-form bg pd-20" id="add-role-form">
			@php
				$form = [
					["type"=>"text", "name"=>"label", "title"=>"Tên chức vụ", "note"=>"VD: Thành viên", "value"=>"", "attr"=>''],
					["type"=>"color", "name"=>"color", "title"=>"Màu nick", "default"=>"#FF00CC", "value"=>"#FF00FF", "required"=>true],
				];
			@endphp
			{!!
				Form::create([
					"form"     => $form,
					"function" => "",
					"prefix"   => "",
					"name"     => "role",
					"class"    => "form-mrg",
					"hover"    => false
				])
			!!}
			<div class="form-notify hidden form-mrg"></div>
			<div class="form-mrg center">
				<button class="btn-primary form-submit-button" onclick="addRoleSubmit(this)" type="button">Thêm</button>
			</div>
		</section>
	</div>
</section>

<script type="text/javascript">
	/*
	 * Thêm chức vụ
	 */
	function addRoleSubmit(thisEl){
		var form = $(thisEl).parents(".ajax-form");
		$("#loading").show();
		$.ajax({
			"type" : "POST",
			"dataType" : "JSON",
			"url" : "/api/addRole",
			"data" : form.find("input, select, textarea").serialize(),
			success: function(response){
				form.find(".form-notify").removeClass("alert-danger alert-success");
				if(typeof response["error"] == "undefined"){
					// Lỗi
					form.find(".form-notify").addClass("alert-danger").html("Đã xảy ra lỗi, vui lòng thử lại").show();
				}else if(response["error"] == null){
					form.find(".form-notify").addClass("alert-success").html("Đã thêm chức vụ thành công").show();
					form.find("input").val("");
					refreshRolesList();
					$(".modal-edit-role").fadeOut();
				}else{
					form.find(".form-notify").addClass("alert-danger").html(response["error"]).show();
				}
			},
			complete: function(){
				$("#loading").hide();
			},
			error: function(){
				setTimeout(function(){
					addRoleSubmit(thisEl);
				}, 5e3);
			}
		});
	}

	/*
	 * Tải lại danh sách chức vụ
	 */
	function refreshRolesList(){
		$.ajax({
			"type" : "GET",
			"url" : "",
			success: function(response){
				var el = $(response).find("#roles-list").html();
				$("#roles-list").html(el);
			},
			error: function(){
				setTimeout(function(){
					refreshRolesList();
				}, 2e3);
			}
		});
	}

	/*
	 * Sửa chức vụ
	 */
	function editRole(thisEl){
		var oldData = JSON.parse( $(thisEl).children("template").html() );
		var newForm = $("<div>"+$("#add-role-form").html()+"</div>");
		$.each(Object.keys(oldData), function(i, key){
			newForm.find("input[name='role["+key+"]']").attr("value", oldData[key]);
		});
		newForm.append('<input type="hidden" name="role[id]" value="'+oldData['id']+'">');
		newForm.find(".form-submit-button").attr("onclick", "editRoleSubmit(this)").html("Lưu lại");
		newForm.find(".form-notify").hide();
		$(".modal-edit-role").fadeIn();
		$(".modal-edit-role section").html( newForm.html() );
	}

	/*
	 * Ấn nút cập nhật danh sách chức vụ
	 */
	function editRoleSubmit(thisEl){
		addRoleSubmit(thisEl);
	}

	/*
	 * Xóa chức vụ
	 */
	function deleteRole(id, name){
		var thisEl = $(".modal-delete-role");
		thisEl.fadeIn();
		thisEl.find(".form-notify").hide();
		thisEl.find("select").html( $("#roles-list-option").html() );
		thisEl.find("select option").prop("disabled", false);
		thisEl.find("select option[value='"+id+"']").prop("disabled", true);
		thisEl.find(".form-append-id").html('<input type="hidden" name="role[id]" value="'+id+'">');
		thisEl.find(".form-submit-button").html("Xác nhận xóa: "+name);
	}

	/*
	 * Ấn nút xóa chức vụ
	 */
	function deleteRoleSubmit(thisEl){
		var form = $(thisEl).parents(".ajax-form");
		$("#loading").show();
		$.ajax({
			"type" : "POST",
			"dataType" : "JSON",
			"url" : "/api/deleteRole",
			"data" : form.find("input, select, textarea").serialize(),
			success: function(response){
				form.find(".form-notify").removeClass("alert-danger alert-success");
				if(typeof response["error"] == "undefined"){
					// Lỗi
					form.find(".form-notify").addClass("alert-danger").html("Đã xảy ra lỗi, vui lòng thử lại").show();
				}else if(response["error"] == null){
					form.find(".form-notify").addClass("alert-success").html("Đã xóa chức vụ thành công").show();
					refreshRolesList();
					$(".modal-delete-role").fadeOut();
				}else{
					form.find(".form-notify").addClass("alert-danger").html(response["error"]).show();
				}
			},
			complete: function(){
				$("#loading").hide();
			},
			error: function(){
				setTimeout(function(){
					deleteRoleSubmit(thisEl);
				}, 5e3);
			}
		});
	}

	/*
	 * Thiết lập quyền cho chức vụ
	 */
	function setPermissions(thisEl){
		var name = $(thisEl).attr("data-label");
		var id   = $(thisEl).attr("data-id");
		var oldData = JSON.parse( $(thisEl).children("template").html() );
		var thisEl = $(".modal-set-permissions");
		thisEl.find(".switch input").prop("checked", false);
		$.each(Object.keys(oldData), function(i, key){
			thisEl.find(".switch input[value='"+key+"']").prop("checked", true);
		});
		thisEl.fadeIn();
		thisEl.find(".form-append-id").html('<input type="hidden" name="set_permission[role_id]" value="'+id+'">');
		thisEl.find(".set-permission-label").text(name);
	}

	/*
	 * Ấn nút set quyền
	 */
	function setPermissionsSubmit(thisEl){
		var form = $(thisEl).parents(".ajax-form");
		$("#loading").show();
		$.ajax({
			"type" : "POST",
			"dataType" : "JSON",
			"url" : "/api/setPermissions",
			"data" : form.find("input, select, textarea").serialize(),
			success: function(response){
					if(typeof response["error"] == "undefined"){
						// Lỗi
						form.find(".form-notify").addClass("alert-danger").html("Đã xảy ra lỗi, vui lòng thử lại").show();
					}else if(response["error"] == null){
						form.find(".form-notify").hide();
						$(".modal-set-permissions").fadeOut();
						refreshRolesList();
					}else{
						form.find(".form-notify").addClass("alert-danger").html(response["error"]).show();
					}
			},
			complete: function(){
				$("#loading").hide();
			},
			error: function(){
				setTimeout(function(){
					setPermissionsSubmit(thisEl);
				}, 5e3);
			}
		});
	}

	/*
	 * Bật - tắt set quyền
	 */
	$(".set-permissions-item").on("click", function(e){
		if( $(e.target).is("div") ){
			$(this).find("input").click();
		}
	});
</script>

<style type="text/css">
	.list-item>div:hover,
	.item-hover:hover{
		background-color: #EED5F8 !important
	}
	.set-permissions-item{
		cursor: pointer;
	}
</style>