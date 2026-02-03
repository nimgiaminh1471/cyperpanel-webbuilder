@php
	use models\Users;
	use models\BuilderDomain;
	global $Schedule;
	$Schedule->W( function(){
		// Cập nhật key check admin mỗi tuần
		Users::where("id", user("id") )->update(["check_admin"=>md5( randomString(20) )]);
	});
@endphp
<main>
	<section>
		<div id="website-filter" style="margin-bottom: 5px">
			<section class="section" style="box-shadow: none;">
				<div class="heading flex flex-middle">
					<div class="width-80">
						<i class="fa fa-dashboard"></i>
						DANH SÁCH WEB MẪU
					</div>
					<div class="width-20 right">
						<a href="/admin/WebsiteTemplateSort" class="btn-primary">Sắp xếp</a>
					</div>
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
							<select class="width-100-small" name="web_filter[app_categories]">
								<option value="">Tất cả</option>
								@foreach(WEB_BUILDER["categories"] as $id => $item)
									<option value="{{ $id }}">{{ $item["name"] }} ({{ BuilderDomain::where('app_categories', $id)->total() }})</option>
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
					if( !empty($filter["app_categories"]) ){
						$where[]=["builder_domain.app_categories", "=", $filter["app_categories"]];
					}
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
				if( !permission("website_manager") ){
					$where[] = ["builder_domain.users_id", "=", user("id")];
				}
				if( isset($_GET["uid"]) ){
					$where[]=["builder_domain.users_id", "=", $_GET["uid"]];
				}
				$where[]=["builder_domain.app_price", ">", 0];
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
						<th style="text-align: center">Tên giao diện</th>
						<th style="text-align: center">Thể loại</th>
						<th style="text-align: center">Ngày tạo</th>
						<th style="text-align: center;">Quản lý</th>
					</tr>
					@foreach($website as $w)
						@php
							$expiredDays = round( ($w->expired-time())/3600/24);
						@endphp
						<tr data-id="{{$w->id}}" class="link">
							<td class="center">{{$w->id}}</td>
							<td>
								<b>
									{{$w->domain}}
								</b>
							</td>
							<td style="text-align: center">{{$w->app_name}}</td>
							<td style="text-align: center">{{ WEB_BUILDER["categories"][$w->app_categories]["name"] ?? null }}</td>
							<td style="text-align: center">{{ date("H:i - d/m/Y", timestamp($w->created_at) ) }}</td>
							<td class="center">
								<a title="Xem website" target="_blank" class="primary-hover pd-5 fa fa-eye" href="http://{{$w->domain}}">
								</a>
								<a class="website-manager-link primary-hover pd-5" href="/admin/WebsiteManager?id={{$w->id}}">
									<i class="fa fa-wrench"></i>
								</a>
								<a target="_blank" class="primary-hover pd-5 fa fa-cog" href="/website?_website_template_search={{$w->app_id}}">
								</a>
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
				<button class="btn-primary" onclick="updateTemplate(); updateTemplate(true); $('.popup-notify').show(); $(this).hide()">
					Cập nhật website mẫu
				</button>
			</div>
		</div>
	</section>
</main>

{{-- Tự cập nhật toàn bộ dữ liệu web mẫu --}}
<div class="popup-notify hidden">
	<a target="_blank">
		<span class="flex flex-middle">
			<span style="width: 45px; font-size: 30px">
				<i class="fa fa-cog fa-spin"></i>
			</span>
			<span style="width: calc(100% - 45px)">
				<span class="block popup-notify-title">
					<b>
						CẬP NHẬT DATA WEB MẪU
						<sup class="popup-notify-count"></sup>
					</b>
				</span>
				<span class="block popup-notify-content">
					...
				</span>
				<span class="block popup-notify-content-rev">
					...
				</span>
			</span>
		</span>
	</a>
</div>

<script type="text/javascript">
	/*
	 * Click xem chi tiết từng web
	 */
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

	/*
	 * Cập nhật dữ liệu web mẫu
	 */
	var _web = {!! json_encode( BuilderDomain::select("id", "domain")->whereColumn("domain", "=", "default_domain")->orderBy('app_price', 'ASC')->get()->keyBy('id') ) !!};
	var webArr = Object.values(_web);
	var _webTotal = webArr.length;
	 function updateTemplateStart(id, rev) {
		 $.ajax({
			 url: "/admin/WebsiteManager?id="+id,
			 data: {setup: 1},
			 type: 'POST',
			 success: function(response){
				 var checkInstall = $(response).find('.modal-setup').length;
				 var reinstall = {{ $_GET['reinstall'] ?? 0 }};
				 if( $(response).find('#website-disk-stats').length > 0 && checkInstall == 0 ){
					 $.ajax({
						 url: "/api/websiteManager/updateWebTemplate",
						 type: "POST",
						 dataType: "JSON",
						 data: {onlyDatabase: true, id: id, reinstall: reinstall},
						 success: function(response){
							 delete _web[id];
						 },
						 complete: function(){
							 setTimeout(function(){
								 updateTemplate(rev);
							 }, 1000);
						 }
					 });
				 }else{
					 setTimeout(function(){
						 updateTemplate(rev);
					 }, 3000);
				 }
			 },
			 error: function(){
				 setTimeout(function(){
					 updateTemplate(rev);
				 }, 500);
			 }
		 });
	 }
	 function updateTemplate(rev = false){
		 if (_web.length === 0) {
			 $(".popup-notify").hide();
			 return;
		 }
		 let webArr = Object.values(_web);
		 let getWeb = rev ? webArr[webArr.length - 1] : webArr[0];
		 let id = getWeb.id;
		 if (typeof id == "undefined") {
			 $(".popup-notify").hide();
			 return;
		 }
		 $(".popup-notify>a").attr("href", "/admin/WebsiteManager?id=" + id);
		 if (rev) {
			 $(".popup-notify .popup-notify-content-rev").html(getWeb.domain);
		 } else {
			 $(".popup-notify .popup-notify-content").html(getWeb.domain);
		 }
		 $(".popup-notify .popup-notify-count").html("("+(_webTotal - webArr.length) + " / " + _webTotal + ")");
		 updateTemplateStart(id, rev);
		
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
			var filter=$(data).find("#website-list").html();
			$("#website-list").html(filter);
			//console.log(data);
			$("#website-list").css({opacity: ''});
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