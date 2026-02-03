@php
	use models\Task;
	use models\TaskCategory;
	use models\Users;
	use models\Role;
	$getTaskCategory = TaskCategory::get()->keyBy('id');
	$getTaskManagerUsers = Users::select('users.id', 'users.name', 'roles.label AS role_label')
		->leftJoin('roles_permissions', 'users.role', '=', 'roles_permissions.role_id')
		->leftJoin('roles', 'users.role', '=', 'roles.id')
		->where('permission_name', 'work')
		->get();
	$taskManagerUsers = [];
	foreach($getTaskManagerUsers as $item){
		$taskManagerUsers[$item->role_label][] = $item;
	}

	// Lấy danh sách công việc
	$getTasks = Task::select("task.*")
		->where("task.id", 0);
	$where = $whereNull = $whereNotNull = [];
	$where[] = ["task.assign_user_id", "=", user('id')];
	$filter = $_GET["filter"] ?? [];
	// Phân loại
	if( !empty($filter["category_id"]) ){
		$where[] = ["task.category_id", "=", $filter["category_id"]];
	}
	// Lọc theo trạng thái
	$where[] = ["task.status", "=", $filter["status"] ?? 0 ];
	// Ngày tạo
	if( !empty($filter["created_at"]) ){
		$dateTo = explode( "/", $filter["created_at"] );
		$where[] = ["task.created_at", "<=", "{$dateTo[2]}-{$dateTo[1]}-{$dateTo[0]} 23:59:59"];
	}
	$getTasks = $getTasks->orWhere($where)->whereNull($whereNull)->whereNotNull($whereNotNull);
	$getTasks = $getTasks->orderBy("task.id", "DESC")->paginate( ($filter["limit"] ?? 20) );
	Assets::footer("/assets/form/date-picker.css", "/assets/form/date-picker.js");
@endphp
<script src="/assets/general/js/modal.js"></script>
<script src="/assets/form/multiple-item.js"></script>

<main>
	<form id="task-form">
		<section class="section" style="box-shadow: none;">
			<div class="heading heading-small">
				<div class="flex flex-middle flex-medium">
					<div class="width-50 pd-10">
						<i class="fa fa-check-square-o"></i>
						DANH SÁCH VIỆC CẦN XỬ LÝ
					</div>
					<div class="width-50 pd-10 right">
						<select class="width-49-small" name="filter[category_id]" onchange="refreshList(1)" style="margin: 5px">
							<option value="">Phân loại</option>
							@foreach($getTaskCategory as $id => $item)
								<option value="{{ $id }}" {{ ($_GET['filter']['category_id'] ?? null) == $id ? 'selected' : '' }}>
									{{ $item->name }} ({{ Task::where('status', $_GET['filter']['status'] ?? 0)->where('assign_user_id', user('id') )->where('category_id', $id)->total() }})
								</option>
							@endforeach
						</select>
						<select class="width-49-small" name="filter[status]" onchange="refreshList(1)" style="margin: 5px">
							<option value="0" {{ ($_GET['filter']['status'] ?? 0) == 0 ? 'selected' : '' }}>Chưa xử lý ({{ Task::where('status', 0)->where('assign_user_id', user('id') )->total() }})</option>
							<option value="1" {{ ($_GET['filter']['status'] ?? null) == 1 ? 'selected' : '' }}>Đang xử lý ({{ Task::where('status', 1)->where('assign_user_id', user('id') )->total() }})</option>
							<option value="2" {{ ($_GET['filter']['status'] ?? null) == 2 ? 'selected' : '' }}>Đã hoàn thành ({{ Task::where('status', 2)->where('assign_user_id', user('id') )->total() }})</option>
						</select>
					</div>
				</div>
			</div>
		</section>
		<section id="task-body" class="table-responsive">
			<table class="table table-border width-100" style="min-width: 900px">
				<thead>
					<tr>
						<th style="width: 60px; text-align: center;">STT</th>
						<th style="width: 180px">Tạo bởi / Thời gian tạo</th>
						<th style="width: 180px">Phân loại</th>
						<th>Mô tả công việc</th>
						<th style="width: 180px">Thời hạn</th>
						<th style="width: 80px">Thao tác</th>
					</tr>
				</thead>
				<tbody>
					@php
						$i = 0;
					@endphp
					@foreach($getTasks as $item)
						@php
							$i++;
						@endphp
						<tr>
							<td style="width: 60px; text-align: center;">
								{{ $i }}
							</td>
							<td>
								<div>
									{!! user('name_color', $item->creator_id) !!}
								</div>
								<div style="color: gray; font-size: 13px; padding-top: 5px">
									{{ date('H:i - d/m/Y', timestamp($item->created_at) ) }}
								</div>
							</td>
							<td>
								<div style="color: gray; font-size: 14px">
									{{ $getTaskCategory[ $item->category_id ]->name ?? null }}
								</div>
							</td>
							<td>
								{{ cutwords($item->note, 20) }}...
							</td>
							<td>
								<div style="color: gray; font-size: 13px; padding-top: 5px">
									{{ date('H:i - d/m/Y', timestamp($item->duration) ) }}
									@if( $item->status != 2 )
										<br>
										@php
											$durationExpiredHour = round( (timestamp($item->duration) - time()) / 3600);
										@endphp
										@if( $durationExpiredHour >= 0 )
											<i style="color: green">
												Còn <b>{{ $durationExpiredHour }}</b> tiếng xử lý
											</i>
										@else
											<i style="color: red">
												Đã quá hạn <b>{{ $durationExpiredHour }}</b> tiếng
											</i>
										@endif
									@endif
								</div>
							</td>
							<td class="center">
								<textarea class="hidden">
									@php
										$item->duration = date('H:i d/m/Y', timestamp($item->duration) );
									@endphp
									{!! json_encode($item) !!}
								</textarea>
								<span class="link btn-info btn-sm" onclick="taskDetail(this)">
									Chi tiết
								</span>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			<div class="center">
				{!! $getTasks->links() !!}
			</div>
			<div class="menu-bg right">
				Tổng số: <b>{!! $getTasks->total() !!}</b>
			</div>
		</section>
	</form>
</main>


<!-- Thêm, chỉnh sửa phiếu -->
<section>
	<div class="modal modal-edit-invoice hidden modal-allow-close modal-allow-scroll">
		<div class="modal-body" style="max-width: 850px">
			<div class="modal-content">
				<div class="heading modal-heading">
					<span>Cập nhật công việc</span>
					<i class="modal-close link fa"></i>
				</div>
				<div>
					<form class="bg pd-10 ajax-form">
						<div class="form-section">
							<input type="hidden" name="invoice[id]" value="" class="form-field">
							<div class="pd-15">
								<div class="panel panel-default">
									<div class="heading">
										Thời hạn hoàn thành: <b data-textid="duration"></b>
									</div>
									<div class="panel-body" data-textid="note">
										
									</div>
								</div>
							</div>
							<div class="pd-15">
								<div class="flex flex-middle flex-medium menu-bg bd">
									<div class="width-70 pd-5 center">
										@foreach([0 => 'Chưa xử lý', 1 => 'Đang xử lý', 2 => 'Đã hoàn thành'] as $status => $label)
											<label class="check radio" style="margin-left: 10px">
												<input class="form-field" type="radio" name="invoice[status]" value="{{ $status }}">
												<s></s>
												{{ $label }}
											</label>
										@endforeach
									</div>
									<div class="width-30 pd-5">
										<button class="btn-primary form-submit-button width-100" onclick="updateTaskStatusSubmit(this)" type="button">Cập nhật trạng thái</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- /Thêm, chỉnh sửa phiếu -->
<style type="text/css">
	.modal-edit-invoice label s{
		background-color: #d3d3d3
	}
</style>
<script type="text/javascript" src="/assets/admin/js/task.js"></script>