@php
	use models\AppStoreOwned;
	$getAppOwnedParams = [
		'user_id' => GET('user_id')
	];
	$getAppOwned = AppStoreOwned::getItem($getAppOwnedParams);
@endphp
<main class="pd-10 paginate-ajax table-responsive" id="apps-list-items">
	@if( empty($getAppOwned[0]) )
		<div class="alert alert-info">
			Không có lượt cài ứng dụng nào
		</div>
	@else
		<table class="width-100 table table-hover table-border-bottom">
			<tr>
				<th>
					Người mua
				</th>
				<th>
					Tên ứng dụng
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
						{!! user('name_color', $item->user->id) !!}
					</td>
					<td>
						<b>{{ $item->app_name }}</b>
						({{ number_format($item->app_price) }} ₫)
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
</main>