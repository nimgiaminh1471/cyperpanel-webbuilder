@php
	use models\Users;
	use models\BuilderDomain;
	global $Schedule;
@endphp
<main>
	<section>
		<div id="website-filter" style="margin-bottom: 5px">
			<section class="section" style="box-shadow: none;">
				<div class="heading">
					<i class="fa fa-globe"></i>
					DANH SÁCH WEBSITE
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
							<select class="width-30 width-49-small" name="web_filter[type]">
								<option value="">Toàn bộ</option>
								<option value="renew">Đang sử dụng</option>
								<option value="test">Dùng thử</option>
								<option value="about_to_expired">Sắp hết hạn</option>
								<option value="expired">Hết hạn</option>
								{{-- @if( permission("website_manager") )
									<option value="forever">Vĩnh viễn</option>
								@endif --}}
							</select>
							@if( permission('website_manager') )
								<select class="width-30 width-49-small" name="web_filter[package]">
									<option value="">Gói gia hạn</option>
									@foreach(WEB_BUILDER["package"] as $id => $item)
										<option value="{{$id}}" {{(isset($filter) && $filter["package"]==$id ? 'selected' : '')}}>{{$item["name"]}} ({{BuilderDomain::where("app_price", 0)->where("package", $id)->total()}})</option>
									@endforeach
								</select>
							@endif
							<select class="width-30 width-100-small" name="web_filter[app]">
								<option value="">Mẫu web</option>
								@foreach(WEB_BUILDER["apps"] as $app=>$item)
									<option value="{{$app}}" {{(isset($filter) && $filter["app"]==$app ? 'selected' : '')}}>{{$item["app_name"]}} ({{BuilderDomain::where("app_price", 0)->where("app", $app)->total()}})</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div id="website-list">
			@php
				$where=[];
				$orderBy=[["builder_domain.id", "DESC"]];
				if(isset($_GET["app"])){
					$where[]=["builder_domain.app", "=", $_GET["app"]];
				}
				if(isset($_GET["web_filter"])){
					$filter=$_GET["web_filter"];
					//Thể loại web
					if(strlen($filter["app"])>0){
						$where[]=["builder_domain.app", "=", $filter["app"]];
					}
					//Gói
					if(strlen($filter["package"] ?? null)>0){
						$where[]=["builder_domain.package", "=", $filter["package"]];
					}
					if($filter["find_by"]=="id"){
						//Tìm theo id
						$where[]=["builder_domain.id", "=", $filter["find_keyword"]];
					}else{
						//Tìm từ khóa
						$where[]=[$filter["find_by"], "LIKE", "%{$filter["find_keyword"]}%"];
					}
					//Website test hoặc gia hạn
					if(strlen($filter["type"])>0){
						if($filter["type"]=="test"){
							$whereNull[]=["builder_domain.package"];
						}else if($filter["type"]=="renew"){
							$whereNotNull[]=["builder_domain.package"];
							$where[]=["builder_domain.expired", ">", time()];
						}else if($filter["type"]=="forever"){
							$where[]=["builder_domain.expired", "=", 0];
						}else if($filter["type"]=="about_to_expired"){
							$whereNotNull[]=["builder_domain.package"];
							$where[]=["builder_domain.expired", "<", time() + 2592000 ];
							$where[]=["builder_domain.expired", ">", time()];
						}else{
							$where[]=["builder_domain.expired", "<", time()];
							$where[]=["builder_domain.expired", ">", 0];
						}
					}
					//Sắp xếp theo
					$orderBy=[["builder_domain.".$filter["orderBy"], $filter["orderType"]]];
				}
				if( !permission("website_manager") ){
					$where[] = ["builder_domain.users_id", "=", user("id")];
				}
				if( isset($_GET["uid"]) ){
					$where[]=["builder_domain.users_id", "=", $_GET["uid"]];
				}
				$where[]=["builder_domain.app_price", "=", 0];
				$website = BuilderDomain::select("builder_domain.*", "users.email as user_email", "users.phone as user_phone")
					->where($where)
					->whereNull($whereNull ?? [])
					->whereNotNull($whereNotNull ?? [])
					->orderBy($orderBy)
					->leftJoin("users", "users.id", "=", "builder_domain.users_id")
					->paginate( ($filter["limit"] ?? 20) );
				if( user('role') == 2 && $website->total() == 1 && empty($_GET['web_filter']) ){
					redirect('/admin/WebsiteManager?id='.$website[0]->id, true);
				}
			@endphp
			<div class="table-responsive">
				<table class="table table-border width-100">
					<tr>
						<th style="text-align: center">ID</th>
						<th>Tên miền</th>
						<th data-sort="expired" data-type="{{isset($filter) && $filter["orderType"]=="DESC" ? 'DESC' : 'ASC'}}" class="website-list-sort link {{isset($filter) && $filter["orderBy"]=="expired" ? ' blue' : ''}}" style="text-align: center">
							Hết hạn <i class="fa fa-sort-{{isset($filter) && $filter["orderType"]=="DESC" ? 'up' : 'down'}} float-right"></i>
						</th>
						<th style="text-align: center">Gói dịch vụ</th>
						<th style="text-align: center">Trạng thái</th>
						<th style="text-align: center">Quản lý</th>
						@if( permission("website_manager") )
							<th style="text-align: center">Tạo bởi</th>
							<th data-sort="suspended" data-type="{{isset($filter) && $filter["orderType"]=="DESC" ? 'DESC' : 'ASC'}}" class="website-list-sort link {{isset($filter) && $filter["orderBy"]=="suspended" ? ' blue' : ''}}" style="text-align: center">
								Đình chỉ <i class="fa fa-sort-{{isset($filter) && $filter["orderType"]=="DESC" ? 'up' : 'down'}} float-right"></i>
							</th>
						@endif
					</tr>
					@foreach($website as $w)
						@php
							$expiredDays = round( ($w->expired-time())/3600/24);
						@endphp
						<tr data-id="{{$w->id}}" class="link">
							<td class="center">{{$w->id}}</td>
							<td>
								<b>
									{{ $w->domain }}
								</b>
							</td>
							<td style="text-align: center">
								{!! ( permission("website_manager", $w->users_id)  ? '
									Vĩnh viễn
								' : '
									<span'.($expiredDays <= 30 ? ' style="color: red"' : '' ).'>
										'.date("H:i - d/m/Y", $w->expired).' (còn <b>'.$expiredDays.'</b> ngày)
									</span>
								' ) !!}
							</td>
							<td style="text-align: center">
								{{ (WEB_BUILDER["package"][$w->package]["name"] ?? "Free") }}
							</td>
							<td style="text-align: center">
								@php
									if(permission("website_manager", $w->users_id)){
										echo '<span class="label-info">Nội bộ</span>';
									}else if( strlen($w->package ?? '') == 0 ){
										echo '<span class="label-warning">Dùng thử</span>';
									}else if($expiredDays <= 0){
										echo '<span class="label-danger">Đã hết hạn</span>';
									}else if($expiredDays <= 30){
										echo '<span class="label-danger">Sắp hết hạn</span>';
									}else{
										echo '<span class="label-success">Đang sử dụng</span>';
									}
								@endphp
							</td>
							<td class="center">
								<a title="Xem website" target="_blank" class="primary-hover pd-5 fa fa-eye" href="http://{{$w->domain}}">
								</a>
								<a title="Quản lý website" class="website-manager-link primary-hover pd-5" href="/admin/WebsiteManager?id={{$w->id}}">
									<i class="fa fa-wrench"></i>
								</a>
							</td>
							@if( permission("website_manager") )
								<td style="text-align: center">
									{!! user("name_color", $w->users_id) !!}
								</td>
								<td style="text-align: center">{{$w->suspended==1 ? 'Có' : 'Không'}}</td>
							@endif
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

<script type="text/javascript">

	//Click xem chi tiết từng web
	$("#website-list").on("click", "tr", function(e){
		var id=$(this).attr("data-id");
		if(typeof id=="undefined"){
			return false;
		}
		var link = $(this).find(".website-manager-link").attr("href");
		$(".table-actived").removeClass("table-actived");
		$(this).addClass("table-actived");
		if( !$(e.target).is("a") ){
			location.href = link;
		}
	});



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
			var filter=$(data).find("#website-list").html();
			$("#website-list").html(filter);
			$("#website-list").css({opacity: ''});
			//console.log(data);
		});
	}

	//Lọc dữ liệu
	var loading = null;
	$("#website-filter, #website-list").on("change keyup", "input,select", function(){
		$("#website-list").css({opacity: '0.5'});
		clearTimeout(loading);
		loading = setTimeout(function(){
			updateWebsiteList(1);
		}, 500);
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

	//Tự động refresh danh sách
	/*setInterval(function(){
		updateWebsiteList();
	}, 2e4);*/
</script>