@php
	use models\CashFlow;
	use models\Users;
@endphp
<main class="flex flex-large">
	<form method="POST" class="width-100 flex-margin" id="recharge-form" style="margin-bottom: 5px; min-height: 350px">
		@php
			// Lấy danh sách các đơn nạp tiền
			$getRecharge = CashFlow::select("cash_flow.*", "users.name as users_name", "users.phone as users_phone")
				->where("cash_flow.id", 0);
			$where = [];
			$where[] = ["cash_flow.customer_id", "=", user("id")];
			$where[] = ["cash_flow.id", ">", 0];
			$filter = $_GET["filter"] ?? [];
			$getRecharge = $getRecharge->leftJoin("users", "cash_flow.customer_id", "=", "users.id");
			// Lọc theo trạng thái
			if( !empty($filter["status"]) ){
				$where[] = ["cash_flow.status", "=", $filter["status"] ];
			}
			// Từ ngày
			if( !empty($filter["date_from"]) ){
				$dateFrom = explode( "/", $filter["date_from"] );
				$where[] = ["cash_flow.created_at", ">=", "{$dateFrom[2]}-{$dateFrom[1]}-{$dateFrom[0]} 00:00:00"];
			}
			// Đến ngày
			if( !empty($filter["date_to"]) ){
				$dateTo = explode( "/", $filter["date_to"] );
				$where[] = ["cash_flow.created_at", "<=", "{$dateTo[2]}-{$dateTo[1]}-{$dateTo[0]} 23:59:59"];
			}
			if( empty($filter["search"]) ){
				$getRecharge = $getRecharge->orWhere($where);
			}else{
				// Có tìm kiếm
				foreach(["name", "phone"] as $findColumn){
					$getRecharge = $getRecharge->orWhere("users.{$findColumn}", "like", "%{$filter["search"]}%")
						->where($where);
				}
			}
			$getRecharge = $getRecharge->orderBy("cash_flow.id", "DESC")->paginate( ($filter["limit"] ?? 10) );
			Assets::footer("/assets/form/date-picker.css", "/assets/form/date-picker.js");
		@endphp
		<div class="menu center pd-15" style="margin-bottom: 10px">
			<div class="flex flex-medium">
				<div class="width-50">
					<div class="flex">
						<div class="width-50 pd-5">
							<div class="form-date-wrap" data-format="day/month/year">
								<div class="form-date-picker form-date-picker-bottom hidden"></div>
								<div class="input-icon">
									<i class="fa fa-calendar"></i>
									<input class="input width-100 input-disabled" placeholder="Từ ngày" type="text" name="filter[date_from]" value="" readonly=""/>
								</div>
								<code>{"allow":{"hours":[],"minutes":"","requiredHour":false,"days":"","months":"","weekDay":["mon","tue","wed","thu","fri","sat","sun"],"min":{"y":{{ (date("Y") - 5) }},"m":2,"d":14},"max":{"y":{{ (date("Y") + 1) }},"m":2,"d":14}},"value":{"day":"{{ date("d") }}","month":"{{ date("m") }}","year":"{{ date("Y") }}"}}</code>
							</div>
						</div>
						<div class="width-50 pd-5">
							<div class="form-date-wrap" data-format="day/month/year">
								<div class="form-date-picker form-date-picker-bottom hidden"></div>
								<div class="input-icon">
									<i class="fa fa-calendar"></i>
									<input class="input width-100 input-disabled" placeholder="Đến ngày" type="text" name="filter[date_to]" value="" readonly=""/>
								</div>
								<code>{"allow":{"hours":[],"minutes":"","requiredHour":false,"days":"","months":"","weekDay":["mon","tue","wed","thu","fri","sat","sun"],"min":{"y":{{ (date("Y") - 5) }},"m":2,"d":14},"max":{"y":{{ (date("Y") + 1) }},"m":2,"d":14}},"value":{"day":"{{ date("d") }}","month":"{{ date("m") }}","year":"{{ date("Y") }}"}}</code>
							</div>
						</div>
					</div>
				</div>
				<div class="width-50 pd-5 right">
					<select name="filter[status]" class="width-50">
						<option value="">Trạng thái</option>
						<option value="pending">Chưa chuyển khoản</option>
						<option value="success">Đã chuyển khoản</option>
						<option value="deny">Bị từ chối</option>
					</select>
				</div>
			</div>
		</div>
		<div class="alert-success hidden" id="recharge-manager-msg">
			{!!
				($approvalMsg ?? "")
			!!}
		</div>
		<section id="recharge-body" class="tooltip-outer">
			<div class="table-responsive">
				<table class="table table-border width-100">
					<tr class="center">
						<th>STT</th>
						<th>Số tiền</th>
						<th>Thời gian</th>
						<th>Phương thức</th>
						<th>Trạng thái</th>
					</tr>
					@php
						$i = $getRecharge->count() + 1;
						$amountTotal = 0;
					@endphp
					@foreach($getRecharge as $r)
						<tr class="center">
							@php
								$i--;
								$amountTotal += $r->amount;
							@endphp
							<td>{{ $i }}</td>
							<td>
								<b>{{ number_format($r->amount) }}</b>
							</td>
							<td>{{ date("H:i - d/m/Y", timestamp($r->created_at) ) }}</td>
							<td>{!! strtoupper( str_replace("-", " ", $r->method) ) !!}</td>
							<td>
								@if($r->status == 0)
									<span class="label-warning">Chưa chuyển khoản</span>
								@elseif($r->status == 1)
									<span class="label-success">Đã chuyển khoản</span>
								@else
									<span class="label-danger">Chặn chuyển khoản</span>
								@endif
							</td>
						</tr>
					@endforeach
				</table>
			</div>
			{!! $getRecharge->links() !!}
		</section>
	</form>
</main>

<style type="text/css">

</style>

<script type="text/javascript">
	function refreshList(page){
		var form = $("#recharge-form");
		$.ajax({
			"url"  : "",
			"data" : form.serialize()+"&page="+page,
			"type" : "GET",
			success: function(response){
				var el = $(response).find("#recharge-body").html();
				if(typeof el != "undefined"){
					form.find("#recharge-body").html(el);
				}
			},
			complete: function(){
				$("#loading").hide();
			},
			error: function(error){
				setTimeout(function(){
					refreshList(page)
				}, 3e3);
			}
		});
	}
	// Click quản lý đơn nạp tiền
	$("#recharge-body").on("click", ".recharge-manager>span", function(){
		var action = $(this).attr("data-action");
		var id = $(this).parent().attr("data-id");
		if( confirm( "Xác nhận: "+$(this).attr("data-title") ) ){
			$("#loading").show();
			$.ajax({
				"url"  : "",
				"data" : {accountant:1, id: id, action: action},
				"type" : "POST",
				success: function(response){
					refreshList(1);
					var el = $(response).find("#recharge-manager-msg").html();
					$("#recharge-manager-msg").html(el).show();
				},
				complete: function(){
					$("#loading").hide();
				},
				error: function(error){
					alert("Lỗi kết nối, vui lòng ấn thử lại!");
				}
			});
		}
	});
	// Click chuyển trang
	$("#recharge-body").on("click", ".paginate>a", function(){
		$("#loading").show();
		refreshList( $(this).attr("data-page") );
		return false;
	});
	// Lọc
	$("#recharge-form").on("keyup change", "input, select", function(){
		refreshList(1);
	});
	$("#recharge-form").on("click", ".form-date-day td", function(){
		refreshList(1);
	});
</script>