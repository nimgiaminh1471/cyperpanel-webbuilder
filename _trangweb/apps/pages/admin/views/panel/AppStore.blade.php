@php
	use models\AppStore;
	use models\AppStoreOwned;
	use models\AppStoreCategories;
	$params = $_POST ?? [];
	$apps       = AppStore::getItem($params);
	$categories = AppStoreCategories::all();
	Assets::footer('/assets/general/js/modal.js');
	$appType = [
		''        => 'Chọn loại',
		'plugin'  => 'Plugin',
		'package' => 'Gói mua thêm'
	];
	$appExpired = [
		''        => 'Chu kỳ gia hạn',
		'monthly' => 'Mỗi tháng',
		'yearly'  => 'Mỗi năm',
		'forever' => 'Vĩnh viễn'
	];
	$websiteList = \models\BuilderDomain::where('users_id', user('id'))->get()->pluck('domain');
	$getAppOwnedParams = [
		'user_id' => user('id')
	];
	$getAppOwned = AppStoreOwned::getItem($getAppOwnedParams);
@endphp
<main id="apps-list" class="flex flex-medium">
	<section class="width-25" id="apps-list-categories">
		<h1 class="heading" style="padding: 17px 15px">
			DANH MỤC
		</h1>
		<ul class="section-body">
			@if( permission('website_manager') )
				<li>
					<a onclick="appsListAddCategory()" href="javascript:void(0)" style="color: tomato">
						<i class="fa fa-plus"></i>
						Thêm chuyên mục
					</a>
				</li>
				<li>
					<a href="/admin/AppStoreManager" style="color: blue">
						<i class="fa fa-check"></i>
						Lịch sử mua ứng dụng
					</a>
				</li>
			@else
				<li>
					<a href="?type=owned" style="color: tomato" class="{{ GET('type') == 'owned' ? 'apps-list-category-selected' : '' }}">
						<i class="fa fa-check"></i>
						Ứng dụng đã cài ({{ $getAppOwned->total() }})
					</a>
				</li>
			@endif
			@if( empty($_GET['type']) )
				<li>
					<a data-id="" onclick="appsListFilterByCategory(this)" href="javascript:void(0)" class="apps-list-category-selected">
						<i class="fa fa-angle-right"></i>
						Tất cả ứng dụng
					</a>
				</li>
				@foreach($categories as $cate)
					<li>
						<a data-id="{{ $cate->id }}" onclick="appsListFilterByCategory(this)" href="javascript: void(0)" {!! permission('website_manager') ? 'oncontextmenu="appsListEditCategory(this); return false;"' : '' !!}>
							<i class="fa fa-angle-right"></i>
							{{ $cate->name }}
							<template>{!! json_encode($cate) !!}</template>
						</a>
					</li>
				@endforeach
			@else
				<li>
					<a href="{!! THIS_LINK !!}">
						<i class="fa fa-plus"></i>
						Cài thêm ứng dụng
					</a>
				</li>
			@endif
		</ul>
		@if( permission('website_manager') )
			<div class="pd-20 gray">
				Ấn chuột phải để chỉnh sửa chuyên mục
			</div>
		@endif
	</section>
	<section class="width-75">
		<div class="section" style="background-color: transparent; box-shadow: none; padding: 0">
			<div class="flex flex-large flex-medium flex-middle heading" style="margin: 0 10px 10px 10px; padding: 0px 15px">
				<div class="width-60 pd-10">
					KHO ỨNG DỤNG
				</div>
				<div class="width-40 center pd-10">
					<input onkeyup="refreshAppList()" id="apps-list-search" class="input width-90 width-100-small input-circle" type="search" name="" placeholder="Tìm kiếm">
				</div>
			</div>
			@switch( GET('type') )
				{{-- Danh sách đã mua --}}
				@case('owned')
					<div class="pd-10 paginate-ajax" id="apps-list-items">
						@if( empty($getAppOwned[0]) )
							<div class="alert alert-info">
								Quý khách chưa cài ứng dụng nào!
							</div>
						@else
							<table class="width-100 table table-hover table-border-bottom">
								<tr>
									<th>
										Tên
									</th>
									<th>
										Giá tiền
									</th>
									<th>
										Dùng cho
									</th>
									<th>
										Thời gian cài
									</th>
									<th>
										Hạn sử dụng
									</th>
								</tr>
								@foreach($getAppOwned as $item)
									<tr>
										<td>
											<b>{{ $item->app_name }}</b>
										</td>
										<td>
											{{ number_format($item->app_price) }}
										</td>
										<td>
											{{ $item->domain ?? 'Tất cả website' }}
										</td>
										<td>
											{{ date('H:i - d/m/Y', timestamp($item->created_at) ) }}
										</td>
										<td>
											@if( empty($item->expired) )
												Vĩnh viễn
											@else
												<span class="{{ time() > $item->expired ? 'red' : 'blue' }}">
													{{ date('d/m/Y', $item->expired) }}
												</span>
											@endif
										</td>
									</tr>
								@endforeach
							</table>
							{!! $getAppOwned->links() !!}
						@endif
					</div>
				@break
				{{-- Danh sách kho ứng dụng --}}
				@default
					<ul class="flex flex-medium" id="apps-list-items">
						@if( permission('website_manager') )
							<li class="width-33x">
								<div class="apps-list-item-outer" onclick="appsListItemAdd(this)">
									<div class="apps-list-item-add center">
										<i class="fa fa-plus"></i>
									</div>
								</div>
							</li>
						@endif
						@foreach($apps as $app)
							<li class="width-33x">
								<div class="apps-list-item-outer" data-id="{{ $app->id }}" onclick="appsListItemShowDetail(this)">
									<div class="apps-list-item-image">
										<img src="{{ $app->image }}">
									</div>
									<div class="apps-list-item-text center">
										<div class="text-inline apps-list-item-category pd-10">
											{{ $app->categories->name ?? 'Chưa phân loại' }}
										</div>
										<div class="apps-list-item-name text-inline">
											{{ $app->name }}
										</div>
										<div class="primary-color apps-list-item-price pd-10">
											{{-- Chưa mua --}}
											<span class="red" data-allow-install="1">
												@if( $app->price > 0 )
													{{ number_format($app->price) }} ₫
												@else
													Miễn phí
												@endif
											</span>
										</div>
									</div>
									<div class="apps-list-item-description">
										{{ $app->description }}
									</div>
									<template>{!! json_encode($app) !!}</template>
								</div>
								@if( permission('website_manager') )
									<div class="apps-list-item-edit" onclick="appsListItemEdit(this)">
										<i class="fa fa-pencil"></i>
									</div>
								@endif
							</li>
						@endforeach
					</ul>
				@break
			@endswitch
		</div>
	</section>
</main>

{{-- Xem chi tiết 1 mục --}}
{!! modal('Chi tiết', '<div class="apps-list-item-detail"></div>', 'item-detail', '550px', false, true) !!}

{{-- Click thêm mới --}}
@php
	$selectCategories = ['' => 'Chọn chuyên mục'];
	foreach($categories as $item){
		$selectCategories[$item->id] = $item->name;
	}
	$form = [
		["type"=>"image", "name"=>"image", "title"=>"Ảnh đại diện", "value"=>"", "post"=>0],
		["type"=>"text", "name"=>"name", "title"=>"", "note"=>"Tên ứng dụng", "value"=>"", "attr"=>''],
		["type"=>"currency", "name"=>"price", "title"=>"", "note"=>"Giá tiền", "value"=>"", "attr"=>''],
		["type"=>"text", "name"=>"button_label", "title"=>"", "note"=>"", "value"=>"Cài đặt", "attr"=>''],
		["type"=>"textarea", "name"=>"description", "title"=>"", "note"=>"Mô tả ngắn", "value"=>"", "attr"=>'', "full"=>true],
		["type"=>"textarea", "name"=>"content", "title"=>"", "note"=>"Nội dung giới thiệu", "value"=>"", "attr"=>'', "full"=>true],
		["type"=>"textarea", "name"=>"paid_content", "title"=>"", "note"=>"Ghi chú khi đã mua xong", "value"=>"", "attr"=>'', "full"=>true],
		["type"=>"select", "name"=>"category", "title"=>"", "option" => $selectCategories, "value"=>"", "full" => true],
		["type"=>"select", "name"=>"type", "title"=>"", "option" => $appType, "value"=>"", 'attr' => 'onchange="appsListChangeAppType(this)"', "full" => true],
		["type"=>"file", "name"=>"plugin_file", "title"=>"File plugin (hãy zip cả tên thư mục chứa plugin)", "note"=>"Chọn", "value"=>"", "ext"=>"zip" ,"post"=>0],
		["type"=>"select", "name"=>"renew_type", "title"=>"", "option" => $appExpired, "value"=>"", "full" => true],
		["type"=>"switch", "name"=>"required_domain", "title"=>"Cài cho tên miền", "value"=>1],
	];
	$formAddItem = Form::create([
				"form"     => $form,
				"function" => '',
				"prefix"   => '',
				"name"     => 'app',
				"class"    => '',
				"hover"    => false
			]);
	$formAddItem = <<<HTML
		<form class="form pd-20">
			{$formAddItem}
			<input type="text" class="form-append-id hidden" name="app[id]">
			<div class="submit-notify alert-danger hidden form-mrg"></div>
			<div class="pd-5 center" style="position: relative;">
				<button onclick="appsListItemEditSubmit(this)" type="button" class="btn-primary">Cập nhật</button>
				<a class="red hidden apps-list-item-delete" type="button" onclick="appsListDeleteApp(this)" style="position: absolute; right: 15px; top: 5px">
				Xóa
				</a>
			</div>
		</form>
	HTML;
@endphp
{!! modal('Cập nhật ứng dụng', $formAddItem, 'item-edit', '550px', false, true) !!}

{{-- Thêm chuyên mục --}}
<section class="modal modal-add-category hidden modal-allow-close modal-allow-scroll">
	<div class="modal-body" style="max-width:450px">
		<div class="modal-content">
			<div class="heading modal-heading">
				Thêm chuyên mục mới
				<i class="modal-close link fa"></i>
			</div>
			<div>
				<form class="bg pd-10">
					<div class="form-mrg hidden">
						<input class="width-100" type="text" name="category[id]" value="">
					</div>
					<div class="form-mrg submit-notify hidden alert-danger"></div>
					<div class="form-mrg">
						<input class="width-100" type="text" name="category[name]" placeholder="Tên chuyên mục">
					</div>
					<div class="form-mrg">
						<textarea class="width-100" name="category[description]" placeholder="Mô tả chuyên mục"></textarea>
					</div>
					<div class="form-mrg center" style="position: relative;">
						<button class="btn-primary" type="button" onclick="appsListAddCategorySubmit(this)">
							Lưu lại
						</button>
						<a class="red hidden apps-list-categories-delete" type="button" onclick="appsListDeleteCategory(this)" style="position: absolute; right: 15px; top: 5px">
							Xóa
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

{{-- Thêm chuyên mục --}}
<section class="modal modal-add-category2 hidden modal-allow-close modal-allow-scroll">
	<div class="modal-body" style="max-width:450px">
		<div class="modal-content">
			<div class="heading modal-heading">
				Thêm chuyên mục mới
				<i class="modal-close link fa"></i>
			</div>
			<div>
				<form class="bg website-action-form">
					
				</form>
			</div>
		</div>
	</div>
</section>

{{-- Cài đặt ứng dụng --}}
{!! modalForm('Cài đặt gói', '<form class="pd-20"></form>', 'app-install', '550px', false, true) !!}

<style type="text/css">
	#apps-list>section:last-child{
		padding-left: 20px
	}
	#apps-list ul{
		list-style-type: none;
		padding: 0;
		margin: 0
	}
	#apps-list h2{
		padding: 20px;
		border-bottom: 1px solid #EAEAEA
	}
	#apps-list-categories ul>li>a{
		padding: 10px 20px;
		display: block;
		transition: .3s all
	}
	#apps-list-items>li{
		padding: 10px;
		position: relative;
	}
	#apps-list-items .apps-list-item-outer{
		position: relative;
		border-radius: 5px;
		background-color: white;
		overflow: hidden;
		height: 260px;
		margin: auto;
		cursor: pointer;
	}
	#apps-list-categories ul>li>a:hover,
	.apps-list-category-selected{
		background-color: #F0F7F7;
		color: var(--primary-color) !important
	}
	#apps-list-items .apps-list-item-image{
		border-bottom: 1px solid #EAEAEA;
		overflow: hidden;
		border-radius: 5px 5px 0 0;
		transition: .1s all;
	}
	#apps-list-items .apps-list-item-image>img{
		height: 140px;
		object-fit: cover;
		width: 100%
	}
	#apps-list-items>li:hover .apps-list-item-image{
		margin-top: -140px
	}
	#apps-list-items .apps-list-item-text{
		padding: 5px 0
	}
	.apps-list-item-category{
		color: green
	}
	#apps-list-items .apps-list-item-name{
		font-size: 17px;
		font-weight: bold;
		padding-left: 10px;
		padding-right: 10px
	}
	#apps-list-items .apps-list-item-description{
		border-top: 1px solid #EAEAEA;
		padding: 5px 20px;
		background-color: #e7e7e7;
		height: 140px;
		overflow: hidden;
		text-align: center;
		line-height: 1.4
	}
	.apps-list-item-add>i{
		font-size: 30px;
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}
	.apps-list-item-edit{
		position: absolute;
		text-align: center;
		top: 5px;
		right: 5px;
		height: 30px;
		width: 30px;
		padding: 5px;
		border-radius: 15px;
		background-color: #60BBDD;
		color: white;
		cursor: pointer;
	}
	.modal{
		z-index: 199701 !important;
	}
	@media(max-width: 1023px){
		#apps-list-items>li{
			width: 50% !important
		}
	}
	@media(max-width: 767px){
		#apps-list>section,
		#apps-list>section>div{
			margin-left: 0 !important;
			margin-right: 0 !important;
		}
		#apps-list>section{
			padding: 0 !important;
		}
		#apps-list>section .heading{
			margin-left: 0 !important;
			margin-right: 0 !important
		}
		#apps-list-items>li{
			width: 100% !important
		}
		#apps-list-items .apps-list-item-outer{
			max-width: 95%
		}
	}
</style>

<script type="text/javascript">
	var renewType = {
		monthly: 'Một tháng',
		yearly: 'Một năm',
		forever: 'Vĩnh viễn'
	};
	var websiteList = {!! json_encode($websiteList) !!};
	/*
	 * Click thêm chuyên mục
	 */
	function appsListAddCategory(){
		$('.modal-add-category .submit-notify, .apps-list-categories-delete').hide();
		$('.modal-add-category').find('input,textarea').val('');
		$('.modal-add-category').fadeIn();
	}

	/*
	 * Ấn cập nhật chuyên mục
	 */
	function appsListAddCategorySubmit(thisEl){
		var thisForm = $(thisEl).parents('form');
		$.ajax({
			url: '/api/appStore/updateCategory',
			type: 'POST',
			dataType: 'JSON',
			data: thisForm.serialize(),
			success: function(response){
				if( response.error.length == 0 ){
					refreshCategoryList();
					$('.modal').fadeOut();
				}else{
					thisForm.find(".submit-notify").html(response.error).show();
				}
			},
			error: function(){
				setTimeout(function(){
					appsListAddCategorySubmit(thisEl);
				}, 3e3);
			}
		});
	}

	/*
	 * Load lại danh sách chuyên mục
	 */
	function refreshCategoryList(){
		$.ajax({
			url: '',
			type: 'GET',
			success: function(response){
				var dataEl = $(response).find('#apps-list-categories').html();
				$('#apps-list-categories').html(dataEl);
			},
			error: function(){
				setTimeout(function(){
					refreshCategoryList();
				}, 3e3);
			}
		});
	}

	/*
	 * Click chuột phải để chỉnh menu
	 */
	function appsListEditCategory(thisEl){
		var thisEl = $(thisEl);
		$('.modal-add-category .submit-notify').hide();
		var oldData = JSON.parse( thisEl.find('template').html() );
		$.each(oldData, function(key, value){
			$('.modal-add-category').find('[name="category['+key+']"]').val(value);
		});
		$('.modal-add-category, .apps-list-categories-delete').fadeIn();
	}

	/*
	 * Click xóa chuyên mục
	 */
	function appsListDeleteCategory(thisEl){
		var thisForm = $(thisEl).parents('form');
		if( confirm('Xác nhận xóa chuyên mục?') ){
			$.ajax({
				url: '/api/appStore/deleteCategory',
				type: 'POST',
				dataType: 'JSON',
				data: {id: thisForm.find('input[name="category[id]"]').val()},
				success: function(response){
					if( response.error.length == 0 ){
						refreshCategoryList();
						$('.modal-add-category').fadeOut();
					}else{
						thisForm.find(".submit-notify").html(response.error).show();
					}
				},
				error: function(){
					setTimeout(function(){
						appsListDeleteCategory(thisEl);
					}, 3e3);
				}
			});
		}
	}
	/*
	 * Trỏ chuột để xem trước mô tả
	 */
	function appsListItemShowDetail(thisEl){
		var thisEl = $(thisEl);
		var data = JSON.parse( thisEl.find('template').html() );
		var modalEl = $('.modal-item-detail');
		modalEl.find('.heading>span').text(data.name);
		var priceEl = thisEl.find('.apps-list-item-price');
		if( typeof data.owned.id == 'undefined' ){
			var priceLabel = priceEl.text()+' / '+renewType[data.renew_type];
		}else{
			var priceLabel = priceEl.html();
		}
		var installButton = '';
		var paidContent = (data.paid_content == null || data.paid_content.length == 0  ? '' : '<div class="alert-info pd-20">'+data.paid_content+'</div>');
		if( typeof priceEl.children().attr('data-allow-install') != 'undefined' ){
			// Cho phép cài
			var installButton = `
				<button onclick="appsListInstall(${data.id})" type="button" class="btn btn-circle btn-primary">
					${data.button_label}
				</button>
			`;
			var paidContent = '';
		}else if( data.type == 'plugin' ){
			// Cài lại plugin
			var installButton = `
				<button onclick="appsListReinstall(${data.id})" type="button" class="btn btn-circle btn-primary">
					Cập nhật lại
				</button>
			`;
		}else{
			// Đã cài
			var installButton = `
				<span class="label label-success">
					<i class="fa fa-check"></i>
					Đang sử dụng
				</span>
			`;
		}
		var content = `
			<div class="pd-20 flex flex-middle" style="padding-bottom: 0">
				<div class="width-50">
					${installButton}
				</div>
				<div class="width-50 right">
					<span class="label label-info">
						${priceLabel}
					</span>
				</div>
			</div>
			<div class="pd-20">
				${data.content}
			</div>
			${paidContent}
		`;

		modalEl.find('.apps-list-item-detail').html(content);
		modalEl.fadeIn();
	}

	/*
	 * Ấn nút thêm
	 */
	function appsListItemAdd(){
		var thisEl = $(thisEl);
		var modalEl = $('.modal-item-edit');
		modalEl.find('input[type="text"], textarea, input[name="app[image]"]').val('');
		modalEl.find('input[name="app[image]"]').parents('.form-item').find('img').removeAttr('src');
		modalEl.find('input[name="app[button_label]"]').val('Cài đặt');
		modalEl.find('select option').removeAttr('selected');
		modalEl.find('.apps-list-item-delete').hide();
		modalEl.find('.form-item[data-id="plugin_file"]').hide();
		modalEl.fadeIn();
	}

	/*
	 * Ấn nút lưu thông tin ứng dụng
	 */
	function appsListItemEditSubmit(thisEl){
		var thisForm = $(thisEl).parents('form');
		var data = new FormData(thisForm[0]);
		$('#loading').show();
		$.ajax({
			type: 'POST',
			url: '/api/appStore/updateApp',
			enctype: 'multipart/form-data',
			data: data,
			processData: false,
			contentType: false,
			dataType: 'JSON',
			success: function (response) {
				if( response.error.length == 0 ){
					$('.modal-item-edit').fadeOut();
					refreshAppList();
				}else{
					thisForm.find(".submit-notify").html(response.error).show();
				}
			},
			error: function(){
				setTimeout(function(){
					appsListItemEditSubmit(thisEl);
				}, 2000);
			},
			complete: function(){
				$('#loading').hide();
			}
		});
	}

	/*
	 * Ấn chỉnh sửa ứng dụng
	 */
	function appsListItemEdit(thisEl){
		var thisOuter = $(thisEl).parents('li');
		var data = JSON.parse( thisOuter.find('template').html() );
		var modalEl = $('.modal-item-edit');
		modalEl.find('.form-append-id').val(data.id);
		modalEl.find('select option').removeAttr('selected');
		modalEl.find('.switch input').removeAttr('checked');
		$('.form-item[data-id="plugin_file"]').hide();
		$.each(data, function(key, value){
			modalEl.find('[name="app['+key+']"]').each(function(){
				if( $(this).is('select') ){
					// Selected
					$(this).find('option[value="'+value+'"]').attr('selected', 'selected');
					if( key == 'type' && value == 'plugin' ){
						$('.form-item[data-id="plugin_file"]').show();
					}
				}else if( $(this).is('textarea') || $(this).attr('type') == 'text'){
					// Set val
					$(this).val(value);
				}else if( key == 'image' ){
					$(this).val(value);
					$(this).parents('.form-item').find('img').attr('src', value);
				}else{
					//Checkbox
					if(value == 0){
						$(this).prop('checked', false);
					}else{
						$(this).prop('checked', true);
					}
				}
			});
		});
		modalEl.find('.apps-list-item-delete').show();
		modalEl.find('.submit-notify').hide();
		modalEl.fadeIn();
	}

	/*
	 * Ấn xóa ứng dụng
	 */
	function appsListDeleteApp(thisEl){
		var thisForm = $(thisEl).parents('form');
		var id = thisForm.find('input[name="app[id]"]').val();
		if( confirm('Xác nhận xóa?') ){
			$.ajax({
				url: '/api/appStore/deleteApp',
				type: 'POST',
				dataType: 'JSON',
				data: {id: id},
				success: function(response){
					if( response.error.length == 0 ){
						refreshAppList();
						$('.modal-item-edit').fadeOut();
					}else{
						thisForm.find(".submit-notify").html(response.error).show();
					}
				},
				error: function(){
					setTimeout(function(){
						appsListDeleteApp(thisEl);
					}, 3e3);
				}
			});
		}
	}

	/*
	 * Load lại danh sách ứng dụng
	 */
	function refreshAppList(){
		var data = {
			keyword: $('#apps-list-search').val(),
			category: $('.apps-list-category-selected').attr('data-id')
		};
		$.ajax({
			url: '',
			type: 'POST',
			data: data,
			success: function(response){
				var dataEl = $(response).find('#apps-list-items').html();
				$('#apps-list-items').html(dataEl);
			},
			error: function(){
				setTimeout(function(){
					refreshAppList(thisEl);
				}, 3e3);
			}
		});
	}
	/*
	 * Click lọc theo chuyên mục
	 */
	function appsListFilterByCategory(thisEl){
		var thisEl = $(thisEl);
		$('.apps-list-category-selected').removeClass('apps-list-category-selected');
		thisEl.addClass('apps-list-category-selected');
		refreshAppList();
	}
	/*
	 * Click mua app 
	 */
	function appsListInstall(id){
		var thisEl = $('.apps-list-item-outer[data-id="'+id+'"]');
		var data = thisEl.find('template').html();
		var data = JSON.parse(data);
		var modalEl = $('.modal-app-install');
		modalEl.find('.heading>span').text(data.name);
		var price = thisEl.find('.apps-list-item-price').text();
		var renew_type = renewType[data.renew_type];
		var selectDomain = '';
		if( data.required_domain == 1 ){
			var domainOption = '';
			for(i = 0; i < websiteList.length; i++){
				var isOwned = false;
				$.each( data.owned, function(id, item){
					if( item.domain == websiteList[i] ){
						isOwned = true;
					}
				});
				if( isOwned ){
					continue;
				}
				domainOption += '<option value="'+websiteList[i]+'">'+websiteList[i]+'</option>';
			}
			var selectDomain = `
				<div class="pd-5">
					<select class="width-100" name="app[domain]">
						<option value="">Chọn website muốn cài</option>
						${domainOption}
					</select>
				</div>
			`;
		}
		var content = `
			<div class="flex flex-medium pd-5">
				<div style="width: 100px">
					<i class="fa fa-icon fa-money"></i>
					Giá tiền:
				</div>
				<div style="width: calc(100% - 120px)">
					<b>${price}</b>
				</div>
			</div>
			<div class="flex flex-medium pd-5">
				<div style="width: 100px">
					<i class="fa fa-icon fa-calendar"></i>
					Thời hạn:
				</div>
				<div style="width: calc(100% - 120px)">
					<b>${renew_type}</b>
				</div>
			</div>
			${selectDomain}
			<input type="hidden" name="app[id]" value="${data.id}">
			<div class="pd-5">
				<div class="submit-notify alert-danger hidden form-mrg"></div>
			</div>
			<div class="center pd-10">
				<button onclick="appsListInstallSubmit(this)" type="button" class="btn btn-primary btn-circle">Xác nhận mua gói</button>
			</div>
		`;

		modalEl.find('form').html(content);
		$('.modal').hide();
		modalEl.fadeIn();
	}
	/*
	 * Click cài lại
	 */
	function appsListReinstall(id){
		var thisEl = $('.apps-list-item-outer[data-id="'+id+'"]');
		var data = thisEl.find('template').html();
		var data = JSON.parse(data);
		var modalEl = $('.modal-app-install');
		modalEl.find('.heading>span').text(data.name);
		var selectDomain = '';
		if( data.required_domain == 1 ){
			var domainOption = '';
			for(i = 0; i < websiteList.length; i++){
				domainOption += '<option value="'+websiteList[i]+'">'+websiteList[i]+'</option>';
			}
			var selectDomain = `
				<div class="pd-5">
					<select class="width-100" name="app[domain]">
						<option value="">Chọn website muốn cài</option>
						${domainOption}
					</select>
				</div>
			`;
		}
		var content = `
			${selectDomain}
			<input type="hidden" name="app[id]" value="${data.id}">
			<div class="pd-5">
				<div class="submit-notify alert-danger hidden form-mrg"></div>
			</div>
			<div class="center pd-10">
				<button onclick="appsListInstallSubmit(this)" type="button" class="btn btn-primary btn-circle">Cập nhật</button>
			</div>
		`;

		modalEl.find('form').html(content);
		$('.modal').hide();
		modalEl.fadeIn();
	}
	/*
	 * Click xác nhận mua app 
	 */
	function appsListInstallSubmit(thisEl){
		var thisForm = $(thisEl).parents('form');
		$(thisEl).hide();
		$.ajax({
			url: '/api/appStore/installApp',
			type: 'POST',
			dataType: 'JSON',
			data: thisForm.serialize(),
			success: function(response){
				if( response.error.length == 0 ){
					thisForm.html(`
						<div class="panel panel-info">
							<div class="heading panel-heading">
								Thành công!
							</div>
							<div class="panel-body">
								<div class="pd-5">
									`+response.success_notify+`
								</div>
							</div>
						</div>
						<div class="pd-10 center">
							<a class="btn-primary"  href="/admin/AppStore">Tiếp tục</a>
						</div>
					`);
				}else{
					thisForm.find(".submit-notify").html(response.error).show();
				}
				$(thisEl).show();
			},
			error: function(){
				setTimeout(function(){
					appsListInstallSubmit(thisEl);
				}, 3e3);
			}
		});
	}

	/*
	 * Thay đổi loại app khi thêm mới
	 */
	function appsListChangeAppType(thisEl){
		var thisEl = $(thisEl);
		var type = thisEl.val();
		$('.form-item[data-id="plugin_file"]').hide();
		if( type == 'plugin' ){
			$('.form-item[data-id="plugin_file"]').show();
		}
	}
</script>