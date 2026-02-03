@php
	use models\BuilderDomain;
	if( isset($_POST['sort']) ){
		foreach( array_reverse($_POST['sort']) as $sort => $id){
			BuilderDomain::find($id)->update(['sort' => $sort]);
		}
		echo '<script>alert("Đã cập nhật")</script>';
	}
	$getWeb = BuilderDomain::where('app_price', '>', 0)->orderBy('sort', 'DESC')->get();
@endphp
<main>
	<section>
		<form method="POST" id="website-filter" style="margin-bottom: 5px">
			<section class="section" style="box-shadow: none;">
				<div class="heading flex flex-middle">
					<div class="width-50">
						SẮP XẾP WEB MẪU
					</div>
					<div class="width-50 right">
						<select class="width-100-small" onchange="filterByCategory(this)">
							<option value="">Tất cả</option>
							@foreach(WEB_BUILDER["categories"] as $id => $item)
								<option value="{{ $id }}">{{ $item["name"] }} ({{ BuilderDomain::where('app_categories', $id)->total() }})</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="section-body">
					<div class="sortable-section">
						<ul class="sortable">
							@foreach($getWeb as $item)
								<li draggable="true" class="web-item" data-category="{{ $item->app_categories }}">
									<div class="sortable-header">
										<a href="http://{{ $item->domain }}" target="_blank">
											<i class="fa fa-eye"></i>
										</a>
										<span>
											{{ $item->app_name }}
										</span>
										<sup>
											({{ (WEB_BUILDER["categories"][$item->app_categories]["name"] ?? "") }})
										</sup>
										<input type="hidden" name="sort[]" value="{{ $item->id }}">
										<span style="float:right"><i class="fa fa-arrows-v"></i></span>
									</div>
								</li>
							@endforeach
						</ul>
					</div>
					<div class="center pd-20">
						<input class="btn-primary" type="submit" name="" value="Lưu lại">
					</div>
				</div>
			</section>
		</form>
	</section>
</main>
<link href="/assets/sortable/style.css" rel="stylesheet">
<script src="/assets/sortable/script.js"></script>
<script type="text/javascript">
	function filterByCategory(self) {
		var catId = $(self).val();
		if( catId.length == 0 ){
			$('.web-item').show();
			return;
		}
		$('.web-item').hide();
		$('.web-item[data-category="'+catId+'"]').show();
	}
</script>