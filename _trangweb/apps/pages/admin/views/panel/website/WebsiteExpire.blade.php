@php
	use models\Users;
	use models\BuilderDomain;
	global $Schedule;
	$where=[];
	$orderBy=[["builder_domain.id", "DESC"]];
	if(isset($_GET["web_filter"])){
		$filter=$_GET["web_filter"];
		if($filter["find_by"]=="id"){
			//Tìm theo id
			$where[]=["builder_domain.id", "=", $filter["find_keyword"]];
		}else{
			//Tìm từ khóa
			$where[]=[$filter["find_by"], "LIKE", "%{$filter["find_keyword"]}%"];
		}
		//Sắp xếp theo
		$orderBy=[["builder_domain.".$filter["orderBy"], $filter["orderType"]]];
	}
	
	$where[] = ["builder_domain.app_price", "=", 0];
	$where[] = ["builder_domain.expired", ">", 0];
	$where[] = ["builder_domain.expired", "<", strtotime('+'.Storage::setting('builder_expiry_date_days').' days')];
	$whereNotNull = "builder_domain.package";
	$whereCountPending = $whereCountCancel = $where;
	$whereCountPending[] = ["builder_domain.contact_status", "=", 0];
	$websitePendingCount = BuilderDomain::select('builder_domain.id')
		->where( $whereCountPending )
		->whereNull($whereNull ?? [])
		->whereNotNull($whereNotNull ?? [])
		->total();
	$whereCountCancel[] = ["builder_domain.contact_status", "=", 9];
	$websiteCancelCount = BuilderDomain::select('id')
		->where($whereCountCancel)
		->whereNull($whereNull ?? [])
		->whereNotNull($whereNotNull ?? [])
		->total();

	// Trạng thái liên hệ
	$where[]=["builder_domain.contact_status", "=", $filter["contact_status"] ?? 0];
	$website = BuilderDomain::select("builder_domain.*", "users.email as user_email", "users.phone as user_phone")
		->where($where)
		->whereNull($whereNull ?? [])
		->whereNotNull($whereNotNull ?? [])
		->orderBy($orderBy)
		->leftJoin("users", "users.id", "=", "builder_domain.users_id")
		->paginate( ($filter["limit"] ?? 20) );
@endphp
<main>
	<section>
		<div id="website-filter" style="margin-bottom: 5px">
			<section class="section" style="box-shadow: none;">
				<div class="heading">
					<i class="fa fa-calendar-times-o"></i>
					WEBSITE SẮP HẾT HẠN (TRONG {{ Storage::setting('builder_expiry_date_days') }} NGÀY)
				</div>
				<div class="section-body">
					<div class="flex flex-middle flex-medium">
						<div class="width-50">
							<select class="width-49-small" name="web_filter[find_by]">
								<option value="domain">Tên miền</option>
								<option value="id">ID web</option>
								<option value="users.email">Email người tạo</option>
								<option value="users.phone">SĐT người tạo</option>
							</select>
							<input class="width-49-small" placeholder="Tìm kiếm" type="text" name="web_filter[find_keyword]" />
						</div>
						<div class="width-50 right">
							<select class="width-40 width-100-small" name="web_filter[contact_status]">
								<option value="0" {{ ($filter['contact_status'] ?? 0) == 0 ? 'selected' : ''}}>Chưa thoản thuận được ({{ $websitePendingCount }})</option>
								<option value="9" {{ ($filter['contact_status'] ?? 0) == 9 ? 'selected' : ''}}>Khách từ chối gia hạn ({{ $websiteCancelCount }})</option>
							</select>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div id="website-list">
			<div class="table-responsive">
				<table class="table table-border width-100" style="min-width: 750px">
					<tr>
						<th style="text-align: center; width: 60px">
							ID
						</th>
						<th style="width: 250px">Tên miền</th>
						<th style="width: 250px" data-sort="expired" data-type="{{isset($filter) && $filter["orderType"]=="DESC" ? 'DESC' : 'ASC'}}" class="website-list-sort link {{isset($filter) && $filter["orderBy"]=="expired" ? ' blue' : ''}}">
							Hết hạn <i class="fa fa-sort-{{isset($filter) && $filter["orderType"]=="DESC" ? 'up' : 'down'}} float-right"></i>
						</th>
						<th>Chi tiết liên hệ</th>
					</tr>
					@php 
						$i = 0;
					@endphp
					@foreach($website as $w)
						@php
							$i++;
							$expiredDays = round( ($w->expired-time())/3600/24);
							$w->created_at = date("H:i - d/m/Y", timestamp($w->created_at) );
							$w->expired = ( permission("website_manager", $w->users_id)  ? '
									Vĩnh viễn
								' : '
									<span'.($expiredDays <= 30 ? ' style="color: red"' : '' ).'>
										'.date("H:i - d/m/Y", $w->expired).' (còn <b>'.$expiredDays.'</b> ngày)
									</span>
								' );
							$w->package = (WEB_BUILDER["package"][$w->package]["name"] ?? "Free");
							$w->user_name = user('name_color', $w->users_id);
							$w->user_phone_prefix = $w->user_phone;
							if( !empty( Storage::option('other_option_prefix_phone') ) ){
								$w->user_phone_prefix = Storage::option('other_option_prefix_phone').intval($w->user_phone_prefix);
							}
						@endphp
						<tr data-id="{{$w->id}}" class="link" onclick="websiteShowDetail({{$w->id}})">
							<td class="center">
								{{$i}}
							</td>
							<td>
								<b>
									{{ $w->domain }}
								</b>
							</td>
							<td>
								{!! $w->expired !!}
							</td>
							<td>
								@if( empty($w->contact_detail) )
									<span class="label label-danger">Chưa liên hệ</span>
								@else
									@php
										$w->contact_detail         = unserialize($w->contact_detail);
										$w->contact_detail['user'] = user('name', $w->contact_detail['user_id']);
										$w->contact_detail['time'] = date('H:i - d/m/Y', $w->contact_detail['time']);
									@endphp
									<div class="pd-5">
										<i class="fa fa-sticky-note-o"></i> {{ $w->contact_detail['note'] }}
									</div>
									<div class="pd-5" style="font-size: small; color: gray">
										<i class="fa fa-user-o"></i> {!! $w->contact_detail['user'] !!} - <i class="fa fa-clock-o"></i> {{ $w->contact_detail['time'] }}
									</div>
								@endif
								<textarea class="website-item-data hidden">{!! json_encode($w) !!}</textarea>
							</td>
						</tr>
					@endforeach
				</table>
			</div>
			<div class="alert-info">Tổng số website: <b>{{$website->total()}}</b></div>
			{!!$website->links([
				"ajaxLoad"=>""
			])!!}
			<div class="pd-10 right website-filter">
				<select name="web_filter[limit]">
					@foreach([20,30,50] as $limit)
						<option value="{{ $limit }}"{{ ($filter['limit'] ?? 0) == $limit ? ' selected' : '' }}>{{ $limit }} website/trang</option>
					@endforeach
				</select>
			</div>
		</div>
	</section>
</main>
{!! modal('website-detail', 'Chi tiết website', '<div class="panel-outer"></div>','950px', false, true) !!}
<script type="text/javascript">

	//Click xem chi tiết từng web
	function websiteShowDetail(id){
		var outerEl = $('#website-list tr[data-id="'+id+'"]').find('.website-item-data');
		if( outerEl.length == 0 ){
			$('.modal-website-detail').hide();
			return;
		}
		var data = JSON.parse( outerEl.val() );
		var modalTitle = 'Chi tiết website';
		var modalBody = `
			<section class="flex flex-medium">
				<div class="width-50 pd-10 menu bd">
					<div class="menu">
						<a class="btn-primary btn-sm" target="_blank" href="/admin/WebsiteManager?id=${data.id}">
							<i class="fa fa-wrench"></i>
							Đến trang quản lý web
						</a>
					</div>
					<div class="menu bd-bottom flex flex-middle">
						<div style="width: 120px">Tên miền:</div>
						<div style="width: calc(100% - 120px)">
							<a target="_blank" href="//${data.domain}"><b>${data.domain}</b></a>
						</div>
					</div>
					<div class="menu bd-bottom flex flex-middle">
						<div style="width: 120px">Gói đang dùng:</div>
						<div style="width: calc(100% - 120px)">
							<b>${data.package}</b>
						</div>
					</div>
					<div class="menu bd-bottom flex flex-middle">
						<div style="width: 120px">Ngày đăng ký:</div>
						<div style="width: calc(100% - 120px)">
							<b>${data.created_at}</b>
						</div>
					</div>
					<div class="menu bd-bottom flex flex-middle">
						<div style="width: 120px">Ngày hết hạn:</div>
						<div style="width: calc(100% - 120px)">
							<b>${data.expired}</b>
						</div>
					</div>
					<div class="menu bd-bottom flex flex-middle">
						<div style="width: 120px">Giá gia hạn:</div>
						<div style="width: calc(100% - 120px)">
							<b>${numberFormat(data.renew_price)}</b>
						</div>
					</div>
					<div class="menu bd-bottom flex flex-middle">
						<div style="width: 120px">Chủ sở hữu:</div>
						<div style="width: calc(100% - 120px)">
							<div>
								<b>${data.user_name}</b>
							</div>
							<div class="flex flex-middle" style="margin-top: 8px">
								<div style="width: calc(100% - 40px)">
									<a class="label-info" style="color: white" href="tel:${data.user_phone_prefix}">
										<i class="fa fa-phone"></i>
										<b>${data.user_phone}</b>
									</a>
								</div>
								<div style="width: 40px">
									<a ${(device() == 'desktop' ? 'target="_blank"' : '' )} href="https://zalo.me/${data.user_phone}" style="">
										<img src="/assets/images/zalo-icon.png" style="height: 25px">
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="width-50 pd-10 menu bd">
					<form>
						<input type="hidden" name="contact_status[id]" value="${id}">
						<div class="pd-5">
							<textarea rows="5" class="width-100" placeholder="Nhập ghi chú" name="contact_status[note]"></textarea>
						</div>
						<div class="pd-5">
							<select class="width-100" name="contact_status[status]" onchange="userChangeContactStatus(this)">
								<option value="">Chọn trạng thái</option>
								<option value="0">Chưa thỏa thuận được</option>
								<option value="9">Khách từ chối gia hạn</option>
							</select>
						</div>
						<div class="pd-5 website-contact-submit-btn hidden">
							<button class="width-100 btn-primary" type="button" onclick="websiteUpdateContactStatus(this)">Cập nhật</button>
						</div>
						`+( data.contact_detail == null ? `` : `
							<div class="pd-5">
								<div class="panel panel-info">
									<div class="heading panel-heading">Lịch sử liên hệ</div>
									<div class="panel-body">
										<div class="pd-5">
											<i class="fa fa-sticky-note-o"></i>
											${data.contact_detail.note}
										</div>
										<div class="pd-5">
											<i class="fa fa-user-o"></i>
											${data.contact_detail.user}
										</div>
										<div class="pd-5">
											<i class="fa fa-clock-o"></i>
											${data.contact_detail.time}
										</div>
									</div>
								</div>
							</div>
						`)+`
					</form>
				</div>
			</section>
		`;

		$('.modal-website-detail .modal-heading>span').text(modalTitle);
		$('.modal-website-detail .panel-outer').html(modalBody);
		$('.modal-website-detail').attr('data-id', data.id).show();
	}



	//Cập nhật lại danh sách
	function updateWebsiteList(page){
		var data=$("#website-filter, #website-list").find("input,select").serializeArray();
		var orderEl=$(".website-list-sort.blue");
		if(typeof orderEl.attr("data-sort")=="undefined"){
			var orderBy="id";
			var orderType="DESC";
		}else{
			var orderBy=orderEl.attr("data-sort");
			var orderType=orderEl.attr("data-type");
		}
		if( !page ){
			var page = $("#website-list").find(".paginate-current").text();
		}
		var mergeData=[
			{name: "web_filter[orderBy]", value: orderBy},
			{name: "web_filter[orderType]", value: orderType},
			{name: "page", value: page}
		];
		var data=data.concat(mergeData);
		$.get("", data ,function(data){
			var filter = $(data).find("#website-list").html();
			$("#website-list").html(filter);
			$('select[name="web_filter[contact_status]"]').html( $(data).find('select[name="web_filter[contact_status]"]').html() );
			if( $('.modal-website-detail').is(':visible') ){
				websiteShowDetail( $('.modal-website-detail').attr('data-id') );
			}
		});
	}

	//Lọc dữ liệu
	$("#website-filter, #website-list").on("change keyup", "input,select", function(){
		updateWebsiteList(1);
	});

	//Click sắp xếp
	$("#website-list").on("click", ".website-list-sort", function(){
		$(".website-list-sort").removeClass("blue");
		$(this).addClass("blue");
		var type=$(this).attr("data-type");
		var type=(type=="ASC" ? "DESC" : "ASC");
		$(this).attr("data-type", type);
		updateWebsiteList(1);
	});

	//Click chuyển trang
	$("#website-list").on("click", ".paginate a", function(){
		$("#website-list").find(".paginate-current").text($(this).attr("data-page"));
		$("#website-list").find(".paginate").hide();
		updateWebsiteList(false);
		return false;
	});

	/*
	 * Thay đổi trạng thái gọi
	 */
	function userChangeContactStatus(thisEl){
		var value = $(thisEl).val();
		if( value.length == 0 ){
			$('.website-contact-submit-btn').slideUp();
		}else{
			$('.website-contact-submit-btn').slideDown();
		}
	}

	/*
	 * Ấn nút cập nhật trạng thái liên hệ
	 */
	function websiteUpdateContactStatus(thisEl){
		var form = $(thisEl).parents('form');
		$.ajax({
			url: '/api/websiteManager/websiteUpdateContactStatus',
			method: 'POST',
			dataType: 'JSON',
			data: form.serialize(),
			success: function(response){
				updateWebsiteList(1);
				$('.modal-website-detail').hide();
			},
			error: function(){
				setTimeout(function(){
					websiteUpdateContactStatus(thisEl);
				}, 2e3);
			}
		});
	}

	//Tự động refresh danh sách
	/*setInterval(function(){
		updateWebsiteList();
	}, 2e4);*/
	window.onfocus = function(){
		updateWebsiteList(false);
	}
</script>