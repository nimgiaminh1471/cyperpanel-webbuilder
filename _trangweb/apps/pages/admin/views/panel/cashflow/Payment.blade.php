@php
	use models\CashFlow;
	use models\CashFlowCategory;
	use models\Users;
	$cashFlowType = 1;
	$getCashFlowCategory = CashFlowCategory::where('type', $cashFlowType)->get()->keyBy('id');
	$getBookkeeperUsers = Users::select('users.id', 'users.name', 'roles.label AS role_label')
		->leftJoin('roles_permissions', 'users.role', '=', 'roles_permissions.role_id')
		->leftJoin('roles', 'users.role', '=', 'roles.id')
		->where('permission_name', 'accountant')
		->get();
	$bookkeeperUsers = [];
	foreach($getBookkeeperUsers as $id => $item){
		$bookkeeperUsers[$item->role_label][$id] = (array)$item;
	}
	//dd($bookkeeperUsers);
@endphp
<script src="/assets/general/js/modal.js"></script>
<script src="/assets/form/multiple-item.js"></script>
<script src="/assets/autocomplete/jquery.autocomplete.min.js"></script>
<link rel="stylesheet" href="/assets/autocomplete/jquery.autocomplete.css" type="text/css">
<main class="flex flex-large">
	<form method="POST" class="width-100 flex-margin" id="cash_flow-form" style="margin-bottom: 5px; min-height: 350px">
		@php
			CashFlow::whereYear( "created_at", date("Y")-5 )->delete();// Xóa đơn 5 năm trước
			// Lấy danh sách các đơn nạp tiền
			$getCashFlow = CashFlow::select(
				"cash_flow.*",
				"users.name as users_name",
				"users.money as user_money",
				"users.phone as users_phone",
				"users.id as customer_id",
				"users.email as users_email")
				->where("cash_flow.id", 0);
			$where = $whereNull = $whereNotNull = [];
			$where[] = ["cash_flow.id", ">", 0];
			$where[] = ["cash_flow.type", "=", $cashFlowType];
			$whereNull[] = ["cash_flow.deleted_at"];
			$filter = $_GET["filter"] ?? [];
			$getCashFlow = $getCashFlow->leftJoin("users", "cash_flow.customer_id", "=", "users.id");
			// Phân loại
			if( !empty($filter["category_id"]) ){
				$where[] = ["cash_flow.category_id", "=", $filter["category_id"]];
			}
			// Người tạo
			if( !empty($filter["creator_id"]) ){
				$where[] = ["cash_flow.creator_id", "=", $filter["creator_id"]];
			}
			// Lọc theo trạng thái
			if( strlen($filter["status"] ?? null) > 0 ){
				if( $filter["status"] == 10 ){
					$whereNull = [];
					$whereNotNull[] = ['cash_flow.deleted_at'];
				}else{
					$where[] = ["cash_flow.status", "=", $filter["status"] ];
				}
			}
			// Từ ngày
			$filter['date_from'] = $filter['date_from'] ?? date('01/m/Y');
			$dateFrom = explode( "/", $filter["date_from"] );
			$where[] = ["cash_flow.created_at", ">=", "{$dateFrom[2]}-{$dateFrom[1]}-{$dateFrom[0]} 00:00:00"];
			// Đến ngày
			if( !empty($filter["date_to"]) ){
				$dateTo = explode( "/", $filter["date_to"] );
				$where[] = ["cash_flow.created_at", "<=", "{$dateTo[2]}-{$dateTo[1]}-{$dateTo[0]} 23:59:59"];
			}
			if( empty($filter["search"]) ){
				$getCashFlow = $getCashFlow->orWhere($where)->whereNull($whereNull)->whereNotNull($whereNotNull);
			}else{
				// Có tìm kiếm
				$getCashFlow = $getCashFlow->orWhere("cash_flow.note", "like", "%{$filter["search"]}%")->whereNull($whereNull)
					->whereNotNull($whereNotNull)
					->where($where);
			}
			$getCashFlow = $getCashFlow->orderBy("cash_flow.id", "DESC")->paginate( ($filter["limit"] ?? 20) );
			Assets::footer("/assets/form/date-picker.css", "/assets/form/date-picker.js");
		@endphp
		<section class="section">
			<div class="heading heading-small">
				<div class="flex flex-medium flex-middle">
					<div class="width-40 pd-10">
						<i class="fa fa-calendar-minus-o"></i>
						PHIẾU CHI
					</div>
					<div class="width-60 pd-10 right">
						<button type="button" class="btn btn-info width-49-small" onclick="cashFlowCategory()" style="margin-right: 10px">
							Phân loại
						</button>
						<button type="button" class="btn btn-primary width-49-small" onclick="cashFlowcreateInvoice()">
							<i class="fa fa-plus"></i>
							Tạo phiếu chi
						</button>
					</div>
				</div>
			</div>
			<div class="section-body" style="margin-bottom: 10px">
				<div class="flex flex-medium">
					<div class="width-15 pd-5">
						<input type="text" name="filter[search]" placeholder="Tìm theo ghi chú" class="width-100">
					</div>
					<div class="width-40">
						<div class="flex flex-medium">
							<div class="width-50 pd-5">
								<div class="form-date-wrap" data-format="day/month/year">
									<div class="form-date-picker form-date-picker-bottom hidden"></div>
									<div class="input-icon">
										<i class="fa fa-calendar"></i>
										<input class="input width-100" placeholder="Từ ngày" type="text" name="filter[date_from]" value="{{ date('01/m/Y') }}" readonly=""/>
									</div>
									<code>{"allow":{"hours":[],"minutes":"","requiredHour":false,"days":"","months":"","weekDay":["mon","tue","wed","thu","fri","sat","sun"],"min":{"y":{{ (date("Y") - 5) }},"m":2,"d":14},"max":{"y":{{ (date("Y") + 1) }},"m":2,"d":14}},"value":{"day":"{{ date("d") }}","month":"{{ date("m") }}","year":"{{ date("Y") }}"}}</code>
								</div>
							</div>
							<div class="width-50 pd-5">
								<div class="form-date-wrap" data-format="day/month/year">
									<div class="form-date-picker form-date-picker-bottom hidden"></div>
									<div class="input-icon">
										<i class="fa fa-calendar"></i>
										<input class="input width-100" placeholder="Đến ngày" type="text" name="filter[date_to]" value="{{ date('d/m/Y') }}" readonly=""/>
									</div>
									<code>{"allow":{"hours":[],"minutes":"","requiredHour":false,"days":"","months":"","weekDay":["mon","tue","wed","thu","fri","sat","sun"],"min":{"y":{{ (date("Y") - 5) }},"m":2,"d":14},"max":{"y":{{ (date("Y") + 1) }},"m":2,"d":14}},"value":{"day":"{{ date("d") }}","month":"{{ date("m") }}","year":"{{ date("Y") }}"}}</code>
								</div>
							</div>
						</div>
					</div>
					<div class="width-45 pd-5 right">
						<select name="filter[status]" class="width-49-small">
							<option value="">Trạng thái</option>
							<option value="0">Chờ duyệt</option>
							<option value="1">Đã duyệt</option>
							<option value="10">Đã xóa</option>
						</select>
						<select name="filter[category_id]" class="width-49-small" style="margin-left: 10px; max-width: 150px">
							<option value="">Phân loại</option>
							@foreach($getCashFlowCategory as $id => $item)
	                        	<option value="{{ $id }}">
	                        		{{ $item->name }}
	                        	</option>
	                        @endforeach
						</select>
						<select name="filter[creator_id]" class="width-100-small" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)" style="margin-left: 10px; max-width: 150px">
							<option value="">Người tạo</option>
							@foreach($bookkeeperUsers as $role_label => $items)
								<optgroup label="{{ $role_label }}">
									@foreach($items as $item)
										<option value="{{ $item['id'] }}">
											{{ $item['name'] }}
										</option>
									@endforeach
								</optgroup>
							@endforeach
						</select>
					</div>
				</div>
			</div>
		</section>
		<div class="alert-success hidden" id="cash_flow-manager-msg">
			{!!
				($approvalMsg ?? "")
			!!}
		</div>
		<section id="cash_flow-body" class="tooltip-outer">
			<div class="table-responsive">
				<table class="table table-border width-100">
					<tr>
						<th style="width: 50px; text-align: center;">STT</th>
						<th style="text-align: right; width: 150px">Số tiền</th>
						<th style="width: 150px">Phân loại</th>
						<th>Ghi chú</th>
						<th style="width: 160px">Thời gian/Tạo bởi</th>
						<th style="width: 100px; text-align: center;">Quản lý</th>
					</tr>
					@php
						$i = $getCashFlow->count() + 1;
						$amountTotal = 0;
					@endphp
					@foreach($getCashFlow as $r)
						<tr>
							@php
								$i--;
								$amountTotal += $r->amount;
							@endphp
							<td class="center">{{ $i }}</td>
							<td style="text-align: right;">
								<div>
									<b>{{ number_format($r->amount) }}</b>
								</div>
								<div style="color: gray; font-size: 12px">
									{!! ucwords( str_replace("-", " ", $r->method) ) !!}
								</div>
							</td>
							<td>
								<div style="font-size: 13px; color: gray">
									{!! $getCashFlowCategory[$r->category_id]->name ?? '<span style="color: red">Chưa phân loại</span>' !!}
								</div>
							</td>
							<td>
								<div>
									{{ $r->note }}
								</div>
							</td>
							<td>
								<div>
									{{ date("H:i - d/m/Y", timestamp($r->created_at) ) }}
								</div>
								<div style="color: gray; font-size: 13px">
									{!! user('name', $r->creator_id) !!}
								</div>
							</td>
							<td class="center">
								@if( empty($r->deleted_at) )
									<div class="cash_flow-manager" data-id="{{ $r->id }}">
										@php
											if( empty($r->users_phone) ){
												$r->customer = '';
											}else{
												$r->customer = "{$r->users_name}|{$r->users_phone}";
											}
											$r->amount   = number_format($r->amount);
										@endphp
										@if($r->status == 0)
											<span class="link btn-primary btn-sm" title="Duyệt" data-action="accept">
												<i class="fa fa-check"></i>
											</span>
										@endif
										<template>
											{!! json_encode($r) !!}
										</template>
										<span class="link btn-info btn-sm" onclick="CashFlowEditInvoice(this)">
											<i class="fa fa-edit"></i>
										</span>
										@if( permission('admin') )
											<span class="link btn-danger btn-sm" title="Xóa vĩnh viễn phiếu chi?" data-action="delete">
												<i class="fa fa-trash"></i>
											</span>
										@endif
									</div>
								@endif
							</td>
						</tr>
					@endforeach
					<tr class="center">
						<th></th>
						<th style="text-align: right;">{{ number_format($amountTotal) }}</th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</table>
			</div>
			{!! $getCashFlow->links() !!}
		</section>
		<div class="pd-10 right">
			<select name="filter[limit]">
				@foreach([20,30,50,500,10000] as $limit)
					<option value="{{ $limit }}">{{ $limit }} đơn/trang</option>
				@endforeach
			</select>
		</div>
	</form>
</main>


<!-- Thêm, chỉnh sửa phiếu -->
<section>
	<div class="modal modal-edit-invoice hidden modal-allow-close modal-allow-scroll">
		<div class="modal-body" style="max-width: 450px">
			<div class="modal-content">
				<div class="heading modal-heading">
					<span>Cập nhật phiếu chi</span>
					<i class="modal-close link fa"></i>
				</div>
				<div>
					<form class="bg pd-10 ajax-form">
						<div class="form-section">
							<input type="hidden" name="invoice[type]" value="{{ $cashFlowType }}">
							<input type="hidden" name="invoice[id]" value="" class="form-field">
							<div class="pd-5" style="padding-top: 10px">
								<div class="input-label no-mrg">
									<span class="">Phân loại</span>
									<select name="invoice[category_id]" class="width-100 form-field" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)" onchange="CashFlowChangeCategory(this)">
										<option value="">Chọn</option>
										@foreach($getCashFlowCategory as $id => $item)
		                                	<option value="{{ $id }}">
		                                		{{ $item->name }}
		                                	</option>
		                                @endforeach
									</select>
								</div>
                            </div>
							<div class="pd-5 invoice-customer hidden">
								<div class="input-label">
									<span class="">Chọn khách hàng nộp tiền</span>
									<input type="text" id="search-customer" class="width-100 form-field" style="border: 1px solid skyblue" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)" autocomplete="off" name="invoice[customer]">
								</div>
							</div>
							<div class="flex">
								<div class="pd-5 width-50" style="padding-top: 10px">
	                                <div class="input-label">
	                                    <span class="">Số tiền</span>
	                                    <input class="width-100 form-field" type="text" name="invoice[amount]" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)" onkeyup="inputCurrency(this)" onchange="inputCurrency(this)">
	                                </div>
	                            </div>
								<div class="pd-5 width-50" style="padding-top: 10px">
									<div class="input-label no-mrg">
										<span class="">Phương thức thanh toán</span>
										<select name="invoice[method]" class="width-100 form-field" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)">
											<option value="">Chọn</option>
											<option value="Tiền mặt">Tiền mặt</option>
											@foreach(Storage::setting("banks") as $bank => $item)
			                                	<option value="{{ $bank }}">
			                                		{{ ucwords($bank) }}
			                                	</option>
			                                @endforeach
			                                <option value="Khác">Khác</option>
										</select>
									</div>
	                            </div>
	                        </div>
	                        <div class="pd-5">
	                        	<div class="input-label input-label-textarea">
	                        		<span class="">Ghi chú</span>
	                        		<textarea class="width-100 form-field" rows="4" name="invoice[note]" onfocusin="inputLabelOnFocus(this)" onfocusout="inputLabelOutFocus(this)"></textarea>
	                        	</div>
	                        </div>
						</div>
						<div class="form-notify hidden form-mrg" style="display: none;"></div>
						<div class="form-mrg center">
							<button class="btn-primary form-submit-button" onclick="editInvoiceSubmit(this)" type="button">Lưu lại</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- /Thêm, chỉnh sửa phiếu -->

<!-- Phân loại phiếu -->
<section>
	<div class="modal modal-cashflow-category hidden modal-allow-close modal-allow-scroll">
		<div class="modal-body" style="max-width: 450px">
			<div class="modal-content">
				<div class="heading modal-heading">
					<span>Phân loại phiếu chi</span>
					<i class="modal-close link fa"></i>
				</div>
				<div>
					<form class="bg pd-10 ajax-form multiple-item">
						<div class="form-section">
							<div class="table-responsive">
			                    <table class="table table-bordered table-hover width-100">
			                        <thead>
			                            <tr>
			                                <th>Tên phân loại</th>
			                                <th style="text-align: center;">Xóa</th>
			                            </tr>
			                        </thead>
			                        <tbody>

			                        </tbody>
			                    </table>
			                </div>
			                <div class="right" style="padding-top: 5px">
			                    <button type="button" class="btn-primary multiple-item-add btn-sm">
			                        <i class="fa fa-plus"></i>
			                        Thêm mới
			                    </button>
			                </div>
			                <template class="multiple-item-template" data-name="cash_flow_category">
			                    <tr>
			                        <td>
			                            <input type="text" name="name" class="form-control width-100">
			                            <input type="hidden" name="type" value="{{ $cashFlowType }}">
			                            <input class="multiple-item-deleted" type="hidden" name="_deleted">
			                        </td>
			                        <td class="center">
			                            <button type="button" class="btn btn-sm btn-danger multiple-item-delete">
			                                <i class="fa fa-trash"></i>
			                            </button>
			                        </td>
			                    </tr>
			                </template>
			                <textarea class="multiple-item-data hidden">
			                    {!! json_encode( $getCashFlowCategory ) !!}
			                </textarea>
						</div>
						<div class="form-notify hidden form-mrg" style="display: none;"></div>
						<div class="form-mrg center">
							<button class="btn-primary form-submit-button" onclick="cashFlowCategorySubmit(this)" type="button">Lưu lại</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- /Phân loại phiếu -->

<style type="text/css">

</style>
<script type="text/javascript" src="/assets/admin/js/cashflow.js"></script>