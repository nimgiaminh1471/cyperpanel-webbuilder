@php
	use models\CashFlow;
	use models\Users;
	use models\PaymentHistory;
@endphp
<main class="flex flex-large">
	<form method="POST" class="width-100 flex-margin" id="recharge-form" style="margin-bottom: 5px; min-height: 350px">
		@php
			// Lấy danh sách lịch sử
			if( permission("admin") && isset($_GET["uid"]) ){
				$paymentHistory = PaymentHistory::where("users_id", $_GET["uid"]);
				$userName = user("name_color", $_GET["uid"]);
				$userMoney = user("money", $_GET["uid"]);
			}else{
				$paymentHistory = PaymentHistory::where("users_id", user("id"));
				$userName = user("name_color");
				$userMoney = user("money");
			}
			$where = [];
			$filter = $_GET["filter"] ?? [];
			// Lọc theo loại
			if( !empty($filter["category"]) ){
				switch($filter["category"]){
					case "plus":
						$where[] = ["amount", ">", 0];
					break;
					case "minus":
						$where[] = ["amount", "<", 0];
					break;
					default:
						$where[] = ["category", "=", $filter["category"] ]; 
					break;
				}
				
			}
			// Từ ngày
			if( !empty($filter["date_from"]) ){
				$dateFrom = explode( "/", $filter["date_from"] );
				$where[] = ["created_at", ">=", "{$dateFrom[2]}-{$dateFrom[1]}-{$dateFrom[0]} 00:00:00"];
			}
			// Đến ngày
			if( !empty($filter["date_to"]) ){
				$dateTo = explode( "/", $filter["date_to"] );
				$where[] = ["created_at", "<=", "{$dateTo[2]}-{$dateTo[1]}-{$dateTo[0]} 23:59:59"];
			}
			$paymentHistory = $paymentHistory->where($where)->orderBy("id", "DESC")->paginate( ($filter["limit"] ?? 10) );
			Assets::footer("/assets/form/date-picker.css", "/assets/form/date-picker.js");
		@endphp
		<h1 class="heading-block">Lịch sử giao dịch</h1>
		<div class="menu bd-bottom">
			Tài khoản: <b>{!! $userName !!}</b>
		</div>
		<div class="menu bd-bottom">
			Số dư hiện tại: <b>{{ number_format($userMoney) }}</b>
		</div>
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
					<select name="filter[category]" class="width-50">
						<option value="">Loại giao dịch</option>
						<optgroup label="Kiểu giao dịch">
							<option value="plus">Cộng tiền</option>
							<option value="minus">Trừ tiền</option>
						</optgroup>
						<optgroup label="Phân loại giao dịch">
							@foreach(DB::table("payment_history_categories")->get() as $c)
								<option value="{{ $c->name }}">{{ $c->label }}</option>
							@endforeach
						</optgroup>
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
						<th style="text-align: left">Loại giao dịch</th>
						<th style="text-align: left">Nội dung giao dịch</th>
						<th>Thời gian</th>
					</tr>
					@php
						$i = $paymentHistory->count() + 1;
						$amountTotal = 0;
					@endphp
					@foreach($paymentHistory as $r)
						<tr class="center">
							@php
								$i--;
								$amountTotal += $r->amount;
							@endphp
							<td>{{ $i }}</td>
							<td>
								<span class="label-{{ $r->amount > 0 ? "success" : "danger" }}">
									<b>{{ $r->amount > 0 ? "+" : "" }}{{ number_format($r->amount) }}</b>
								</span>
							</td>
							<td class="left">{{ DB::table("payment_history_categories")->where("name", $r->category)->first()->label }}</td>
							<td class="left">{!! $r->note !!}</td>
							<td>{{ date("H:i - d/m/Y", timestamp($r->created_at) ) }}</td>
						</tr>
					@endforeach
				</table>
			</div>
			{!! $paymentHistory->links() !!}
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