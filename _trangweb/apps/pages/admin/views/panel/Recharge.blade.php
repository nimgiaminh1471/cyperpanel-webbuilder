@php
	use models\CashFlow;
	use models\Users;

	$bankAccount = Storage::setting("banks");
	$onlineWallet = Storage::setting("online_wallet");
	$myRechargePending=CashFlow::where( "customer_id", user("id") )->where("status", 0)->whereNull('deleted_at')->first(true);
	$myRechargeDeny=CashFlow::where( "customer_id", user("id") )->where("status", 3)->first(true);
	
	//Lưu dữ liệu yêu cầu nạp tiền
	if( isset($_POST["recharge"]) ){
		$post=$_POST["recharge"];
		$data=[];
		$data["amount"]=intval( vnStrFilter($post["amount"]??0, "") );
		//$data["amount_total"]=0;
		$data["method"]=$post["payment_method"]??"";
		$data["customer_id"]=user("id");
		$data["status"]=0;
		$data["type"]=0;
		$data["category_id"]=1;
		if( $data["amount"]<100000 ){
			$rechargeMsg="Số tiền cần nạp phải trên 100.000Đ";
		}else if( empty($bankAccount[$data["method"]]) && empty($onlineWallet[$data["method"]]) ){
			$rechargeMsg="Vui lòng chọn ngân hàng hợp lệ";
		}else if( isset($myRechargePending->id) ){
			$rechargeMsg="Đã có 1 yêu cầu nạp tiền đang chờ duyệt, vui lòng chờ duyệt.";
		}else{
			CashFlow::create($data);
			redirect(THIS_URL);
		}
		if( !empty($rechargeMsg) ){
			redirect(THIS_URL, true, $rechargeMsg);
		}
	}
	echo Assets::show("/assets/tab-panel/style.css", "/assets/tab-panel/script.js");
	$atmCard = [
		[
			"name" => "Vietcombank",
			"image" => "https://manage.matbao.net/Contents/images/bank/vietcom_bank_logo.png"
		],
		[
			"name" => "Vietinbank",
			"image" => "https://manage.matbao.net/Contents/images/bank/viettin_bank_logo.png"
		],
		[
			"name" => "BIDV",
			"image" => "https://manage.matbao.net/Contents/images/bank/bidv.png"
		],
		[
			"name" => "Agribank",
			"image" => "https://manage.matbao.net/Contents/images/bank/agribank_bank_logo.png"
		],
		[
			"name" => "ACBank",
			"image" => "https://manage.matbao.net/Contents/images/bank/acb_bank_logo.png"
		],
		[
			"name" => "Sacombank",
			"image" => "https://manage.matbao.net/Contents/images/bank/sacombank_bank_logo.png"
		],
		[
			"name" => "Techcombank",
			"image" => "https://manage.matbao.net/Contents/images/bank/techcombank_bank_logo.png"
		],
		[
			"name" => "DongABank",
			"image" => "https://manage.matbao.net/Contents/images/bank/donga_bank_logo.png"
		],
		[
			"name" => "Eximbank",
			"image" => "https://manage.matbao.net/Contents/images/bank/exim_bank_logo.png"
		],
		[
			"name" => "VP Bank",
			"image" => "https://manage.matbao.net/Contents/images/bank/vp_bank_logo.png"
		],
		[
			"name" => "MB Bank",
			"image" => "https://manage.matbao.net/Contents/images/bank/mb_bank_logo.png"
		],
	];
	$globalCard = [
		[
			"name" => "Visa",
			"image" => "https://upload.nganluong.vn/public/css/newlanding/img//brand/VISA.png"
		],
		[
			"name" => "Master Card",
			"image" => "https://upload.nganluong.vn/public/css/newlanding/img//brand/MASTER.png"
		],
		[
			"name" => "JCB",
			"image" => "https://upload.nganluong.vn/public/css/newlanding/img//brand/JCB.png"
		],
		[
			"name" => "AMAX",
			"image" => "https://upload.nganluong.vn/public/css/newlanding/img//brand/AMEX.png"
		]
	];
	$bankNote = '
	<div class="center">
		Quý khách lòng chuyển {amount} vào số tài khoản ngân hàng bên dưới:
		<br/>Nội dung chuyển tiền là:
		<div>NAPTIEN {user_id}</div>
		(Trong đó <b>{user_id}</b> là Mã thành viên của bạn, Hệ thống sẽ biết và cộng tiền cho bạn)
	</div>
				';
@endphp
<main class="flex flex-large">
	<form method="POST" class="width-65 flex-margin" style="margin-bottom: 5px;margin-top: 20px">
		<div class="heading-simple">Nạp tiền vào tài khoản</div>
		@if( !isset($myRechargePending->id) && !isset($myRechargeDeny->id) )
			<div class="flex flex-middle flex-small bg" style="padding: 15px">
				<div class="width-35">
					<b>
						Số tiền cần nạp:
					</b>
				</div>
				<div class="width-65">
					<input type="text" name="recharge[amount]" placeholder="Nhập số tiền quý khách muốn nạp" class="width-100 input-currency" />
				</div>
			</div>
			<div class="bg" style="color: red; padding-left: 15px">
				Lưu ý: Số tiền nạp tối thiểu là 100,000 đ và tối đa là 20,000,000 đ
			</div>
			<div class="flex flex-middle flex-small bg" style="padding: 15px">
				<div class="width-35">
					<b>
						Số dư tài khoản chính:
					</b>
				</div>
				<div class="width-65">
					<b style="color: tomato">
						{{ number_format( user('money') ) }} ₫
					</b>
				</div>
			</div>
			<div class="flex flex-middle flex-small bg" style="padding: 15px">
				<div class="width-35">
					<b>
						Tổng tiền sau khi nạp:
					</b>
				</div>
				<div class="width-65">
					<span style="color: tomato; font-weight: bold;" id="amount-if-recharge" data-money="{{ user('money') }}">
						<span>{{ number_format( user('money') ) }}</span> ₫
					</span>
				</div>
			</div>
			<div class="alert-danger recharge-msg">{{$rechargeMsg ?? 'Quý khách vui lòng nhập số tiền & chọn ngân hàng'}}</div>
		@elseif( !isset($myRechargeDeny->id) )
			<input type="hidden" name="recharge[amount]" value="{{ number_format($myRechargePending->amount) }}" />
		@endif
		<section class="menu">
			<div class="tab-panel">
				<ul class="flex">
					<li class="width-25">
						<span data-show="transfer">
							<span>
								<img src="/files/uploads/2019/09/money-transfer-icon.png">
							</span>
							<span>
								CHUYỂN KHOẢN
							</span>
						</span>
					</li>
					<li class="width-25">
						<span data-show="atm-card">
							<span>
								<img src="/files/uploads/2019/09/atm-icon.png">
							</span>
							<span>
								THẺ ATM ONLINE
							</span>
						</span>
					</li>
					<li class="width-25">
						<span data-show="online-wallet">
							<span>
								<img src="/files/uploads/2019/09/skrill-wallet.png">
							</span>
							<span>
								VÍ ĐIỆN TỬ
							</span>
						</span>
					</li>
					<li class="width-25">
						<span data-show="debt-card">
							<span>
								<img src="/files/uploads/2019/09/global-card.png">
							</span>
							<span>
								THẺ QUỐC TẾ
							</span>
						</span>
					</li>
				</ul>
				<div>
					<ul>
						<li data-id="transfer">
							<div class="pd-5">
								<b>Chọn ngân hàng cần chuyển khoản:</b>
							</div>
							<div>
								<div class="flex flex-middle payment-method flex-center">
									@php
										$bankDetail='';
									@endphp
									@foreach(Storage::setting("banks", []) as $bank => $item)
										<div class="width-25 pd-5">
											<div class="payment-method-item pd-5 center">
												<label class="check radio">
													<input type="radio" name="recharge[payment_method]" value="{{ $bank }}">
													<s></s>
												</label>
												<img style="max-height: 50px" src="{{ $item["image"] }}">
											</div>
										</div>
										@php
											$bankDetail.='
											<section class="payment-detail hidden" data-id="'.$bank.'">
												<div class="pd-10">
													'.( str_replace(
														[
															"{user_id}",
															"{wallet}",
															"<div>",
															"{amount}",
														],
														[
															user("id"),
															'<b>'.$item["name"].'</b>',
															'<div style="font-size: 20px; font-weight: bold">',
															'<span class="recharge-amount">số tiền cần nạp</span>'
														], $bankNote)
													).'
												</div>
												<div class="panel panel-info">
													<div class="heading"><b>Thông tin tài khoản ngân hàng</b></div>
													<div class="panel-body">
														'.call_user_func(function($item){
															$out='';
															$infoLabel=[
																"name"   =>"Ngân hàng",
																"user"   =>"Chủ TK",
																"number" =>"Số TK",
																"office" =>"Chi nhánh"
															];
															foreach($item as $key=>$info){
																if( isset($infoLabel[$key]) ){
																	$out.='
																		<div class="flex pd-5">
																			<div style="width: 120px">'.$infoLabel[$key].':</div> <div><b>'.$info.'</b></div>
																		</div>
																	';
																}
															}
															return $out;
														}, $item).'
													</div>
												</div>
											</section>
											';
										@endphp
									@endforeach
								</div>
								{!!$bankDetail!!}
								<div>
									@if( isset($myRechargePending->id) )
										<div class="panel panel-info">
											<div class="heading">Quý khách đang có 1 đơn nạp tiền đang chờ duyệt</div>
											<div class="panel-body">
												<div class="flex pd-5">
													<div style="width: 120px">Số tiền</div>
													<div><b>{{ number_format($myRechargePending->amount) }}</b> Đồng</div>
												</div>
												<div class="flex pd-5">
													<div style="width: 120px">Thời gian</div>
													<div><b>{{ dateText( timestamp($myRechargePending->created_at) ) }}</b></div>
												</div>
												<div class="pd-5" style="color: tomato">
													Nếu chưa chuyển khoản, Quý khách vui lòng chuyển <b>{{ number_format($myRechargePending->amount) }}</b> vào tài khoản trên với nội dung chuyển tiền <b>NAPTIEN {{ user("id") }}</b>
													<br>
													Quý khách vui lòng chờ duyệt, quá trình duyệt sẽ mất khoảng 5 – 15 phút.
												</div>
											</div>
										</div>
									@elseif( isset($myRechargeDeny->id) )
										<div class="alert-danger">Bạn đã bị chặn nạp tiền do tạo đơn nạp tiền mà không chuyển khoản, vui lòng liên hệ hỗ trợ.</div>
									@else
										<div class="pd-10 center hidden recharge-submit">
											<button type="submit" class="btn-info"><i class="fa fa-check"></i> Tôi đã chuyển khoản</button>
										</div>
										<div class="alert-info">
											{!! nl2br( str_replace(
												[
													"{email}",
												],
												[
													strtoupper( vnStrFilter( user("email"), "" ) ),
												], Storage::setting("payment_note") ) )
											!!}
										</div>
									@endif
								</div>
							</div>
						</li>

						<li data-id="atm-card">
							<div class="flex flex-middle payment-method flex-center">
								@foreach($atmCard as $item)
									<div class="width-25 pd-5">
										<div class="payment-method-item pd-5 center">
											<label class="check radio">
												<input type="radio" name="recharge[payment_method]" value="{{ $item["name"] }}">
												<s></s>
											</label>
											<img style="max-height: 50px" src="{{ $item["image"] }}">
										</div>
									</div>
								@endforeach
							</div>
							<div class="alert-warning">
								Phương thức thanh toán này đang nâng cấp, quý khách vui lòng chọn phương thức khác!
							</div>
						</li>

						<li data-id="online-wallet">
							<div class="pd-5">
								<b>Chọn ví điện tử: </b>
							</div>
							<div>
								<div class="flex flex-middle payment-method flex-center">
									@foreach(Storage::setting("online_wallet") as $id => $item)
										<div class="width-25 pd-5">
											<div class="payment-method-item pd-5 center">
												<label class="check radio">
													<input type="radio" name="recharge[payment_method]" value="{{ $id }}">
													<s></s>
												</label>
												<img style="max-height: 50px" src="{{ $item["image"] }}">
											</div>
										</div>
									@endforeach
								</div>
								@foreach(Storage::setting("online_wallet") as $id => $item)
									<section class="payment-detail hidden center" data-id="{{ $id }}" style="padding: 10px 0">
										{!! ( str_replace(
											[
												"{user_id}",
												"{wallet}",
												"<div>",
												"{amount}",
											],
											[
												user("id"),
												'<b>'.$item["name"].'</b>',
												'<div style="font-size: 20px; font-weight: bold">',
												'<span class="recharge-amount">số tiền cần nạp</span>'
											], $item["content"]) ) !!}
									</section>
								@endforeach
								<div>
									@if( isset($myRechargePending->id) )
										<div class="panel panel-info">
											<div class="heading">Quý khách đang có 1 đơn nạp tiền đang chờ duyệt</div>
											<div class="panel-body">
												<div class="flex pd-5">
													<div style="width: 120px">Số tiền</div>
													<div><b>{{ number_format($myRechargePending->amount) }}</b> Đồng</div>
												</div>
												<div class="flex pd-5">
													<div style="width: 120px">Thời gian</div>
													<div><b>{{ dateText( timestamp($myRechargePending->created_at) ) }}</b></div>
												</div>
												<div class="pd-5" style="color: tomato">
													Nếu chưa chuyển khoản, Quý khách vui lòng chuyển <b>{{ number_format($myRechargePending->amount) }}</b> vào tài khoản trên với nội dung chuyển tiền <b>NAPTIEN {{ user("id") }}</b>
													<br>
													Quý khách vui lòng chờ duyệt, quá trình duyệt sẽ mất khoảng 5 – 15 phút.
												</div>
											</div>
										</div>
									@elseif( isset($myRechargeDeny->id) )
										<div class="alert-danger">Bạn đã bị chặn nạp tiền do tạo đơn nạp tiền mà không chuyển khoản, vui lòng liên hệ hỗ trợ.</div>
									@else
										<div class="alert-danger recharge-msg">{{$rechargeMsg ?? 'Quý khách vui lòng nhập số tiền & chọn ví điện tử'}}</div>
										<div class="pd-10 center hidden">
											<button type="submit" class="btn-info"><i class="fa fa-check"></i> Tôi đã chuyển khoản</button>
										</div>
										<div class="alert-info">
											{!! nl2br( str_replace(
												[
													"{email}",
												],
												[
													strtoupper( vnStrFilter( user("email"), "" ) ),
												], Storage::setting("payment_note") ) )
											!!}
										</div>
									@endif
								</div>
							</div>
						</li>

						<li data-id="debt-card">
							<div class="flex flex-middle payment-method flex-center">
								@foreach($globalCard as $item)
									<div class="width-25 pd-5">
										<div class="payment-method-item pd-5 center">
											<label class="check radio">
												<input type="radio" name="recharge[payment_method]" value="{{ $item["name"] }}">
												<s></s>
											</label>
											<img style="max-height: 50px" src="{{ $item["image"] }}">
										</div>
									</div>
								@endforeach
							</div>
							<div class="alert-warning">
								Phương thức thanh toán này đang nâng cấp, quý khách vui lòng chọn phương thức khác!
							</div>
						</li>

					</ul>
				</div>
			</div>
		</section>
		<style type="text/css">
			.payment-method{
				align-items: stretch !important;
			}
			.payment-method-item{
				border: 1px solid #EAEAEA;
				height: 100%;
				cursor: pointer;
				position: relative;
			}
			.payment-method-item>label{
				display: none
			}
			.payment-method-item-actived,
			.payment-method-item:hover{
				border: 1px solid blue;
				background: #D2E5F2
			}
			@media (max-width: 767px){
				.payment-method>div{
					width: 50% !important
				}
			}
		</style>
	</form>
	<section class="width-35 flex-margin" style="margin-top: 20px">
		<div>
			<div class="heading-simple">
				<div class="flex flex-middle">
					<div style="width: 60px">
						<span class="user-avatar"><img src="{!! user("avatar") !!}?t={{time()}}" /></span>
					</div>
					<div style="line-height: 1.5">
						<div>{!!user("name")!!}</div>
						<div style="font-size: 14px">
							<i class="fa fa-envelope-o fa-lg fa-icon"></i>
							{!!user("email")!!}
						</div>
						<div style="font-size: 14px">
							<i class="fa fa-user fa-lg fa-icon"></i>
							ID khách hàng: <b>{!!user("id")!!}</b>
						</div>
					</div>
				</div>
			</div>
			<div class="alert-success pd-15" style="position: relative;">
				<div class="pd-5">
					<i class="fa fa-money fa-icon"></i>
					Số dư tài khoản của bạn là:
				</div>
				<div class="pd-5" style="font-size: 22px">
					<b>{{number_format( user("money") )}}</b>₫
				</div>
			</div>
		</div>
		<div class="list">
			<a href="/admin/RechargeHistory">
				<i class="fa fa-history fa-icon"></i>
				Lịch sử nạp tiền
			</a>
		</div>
	</section>
</main>

<style type="text/css">
	.recharge-item:hover,
	.recharge-item:focus
	{
		background: #FAEDFB
	}
</style>

<script type="text/javascript">
	$("input").on("click change keyup keydown", function(){
		setTimeout(function(){
			var money = $("[name='recharge[amount]']").val();
			var bank = $("[name='recharge[payment_method]']").val();;
			var msg="";
			var msgClass="danger";
			if(money.replace(/,/g ,"")<100000){
				var msg="Số tiền tối thiểu phải trên 100.000Đ";
			}else if( bank.length==0 ){
				var msg="Vui lòng chọn ngân hàng";
			}

			let amountIfRecharge = $('#amount-if-recharge').attr('data-money');
			if(msg.length == 0){
				$(".recharge-amount").html('<b>'+money+'</b>');
				$(".recharge-submit").show();
				$(".recharge-msg").hide();
				$('#amount-if-recharge>span').text( numberFormat( parseInt(amountIfRecharge) + parseInt( money.replace(/,/g ,"") ) ) );
			}else{
				$(".recharge-submit").hide();
				$(".recharge-msg").removeClass("alert-danger alert-info").addClass("alert-"+msgClass).html(msg);
				$('#amount-if-recharge>span').text( numberFormat( parseInt(amountIfRecharge) ) );
			}
		}, 200);
	});
	$(".payment-method-item").on("click", function(){
		var parentEl = $(this).parents(".payment-method");
		$(".payment-method-item-actived").removeClass("payment-method-item-actived");
		$(this).addClass("payment-method-item-actived");
		$(".payment-method-item").find("input").prop("checked", false);
		$(this).find("input").prop("checked", true).change();
		var show = $(this).find("input").val();
		$(".payment-detail").hide();
		$(".payment-detail[data-id='"+show+"']").show();
	});

	/*
	 * Click chuyển phương thức
	*/
	$(".tab-panel>ul>li>span").on("click", function(){
		var show = $(this).attr("data-show");
		var parentEl = $(this).parents(".tab-panel");
		if( parentEl.find("div>ul>li[data-id='"+show+"'] .payment-method-item-actived").length == 0 ){
			$(".recharge-submit").hide();
		}else{
			$(".recharge-submit").show();
		}
	});
	@if( isset($myRechargePending->id) )
		setTimeout(function(){
			var paymentMethod = $(".payment-method-item label input[value='{{ $myRechargePending->method }}']").parents(".payment-method-item");
			paymentMethod.click();
			var paymentMethodParent = paymentMethod.parents("li").attr("data-id");
			$(".tab-panel>ul>li>span[data-show='"+paymentMethodParent+"']").click();
		}, 200)
	@endif
</script>