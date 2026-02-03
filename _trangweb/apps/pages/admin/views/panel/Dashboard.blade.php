@php
	use models\Posts;
	use models\Users;
	use models\Files;
	use models\PostsComments;
	use models\PaymentHistory;
	use models\AppStoreOwned;
	Assets::footer("/assets/chart/progress.js", "/assets/chart/progress.css");
@endphp
 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<main class="flex flex-large">
		<section class="width-60 flex-margin">
			@php
				$warningMsg=[];
				if( permission("admin") ){
					if( empty(Storage::setting("mailer_EmailPassword")) ){
						$warningMsg[]="Website chưa thiết lập thông tin email, ấn cài đặt ->AdminPanel->Cấu hình gửi email để thêm";
					}
				}
				$postsNew=Posts::orderBy("updated_at", "DESC")->limit(5)->get();
				$users=Users::where( "id", "!=", user("id") )->orderBy("last_updated", "DESC")->limit(20)->get();
				$leftSection=[
					"warningMsg"     => ["title"=>"Thông báo quan trọng", "permission"=>"member", "count"=>count($warningMsg)],
					"newPosts"       => ["title"=>"Tin tức/ Thông báo", "permission"=>"member", "count"=>$postsNew->total()],
					"services"       => ["title"=>"Sản phẩm/dịch vụ", "permission"=>"member", "count" => 1],
					"usersNewUpdate" => ["title"=>"Thành viên mới cập nhật thông tin", "permission"=>"users_manager", "count"=>$users->total()],
					"stats"          => ["title"=>"Thống kê truy cập", "permission"=>"admin", "count"=>1]
				];
			@endphp
			@foreach($leftSection as $type=>$item)
				@if( !permission($item["permission"]) || $item["count"]==0 )
					@continue
				@endif
				<div class="section" style="margin-bottom: 20px">
					<div class="heading">{{$item["title"]}}</div>
					<div class="section-body" style="padding: 0">
				@switch($type)


					{{-- Cảnh báo --}}
					@case("warningMsg")
						@foreach($warningMsg as $msg)
							<div class="alert-warning">{!!$msg!!}</div>
						@endforeach
					@break;


					{{-- Bài viết mới --}}
					@case("newPosts")
						<section class="pd-20">
							@foreach($postsNew as $p)
								<div class="menu bd-bottom pd-15" style="position: relative;">
									<div><a class="block" target="_blank" href="/{{$p->link}}">{{$p->title}}</a></div>
									<div class="gray">
										<i class="fa fa-clock-o"></i> {{ dateText(timestamp($p->updated_at) ) }}
										{{-- <span><i class="fa fa-user"></i> {!! user("name_color", $p->users_id) !!}</span> --}}
									</div>
								</div>
							@endforeach
						</section>
					@break;

					{{-- Dịch vụ --}}
					@case("services")
						@php
							$getAppOwned = AppStoreOwned::where('user_id', user('id'))->count();
							$getWebOwned = models\BuilderDomain::where('users_id', user('id'))->count();
							$getWebExpired = models\BuilderDomain::where('users_id', user('id'))->where('expired', '<', strtotime('+7 days') )->count();
							$report = [
								[
									'count'  => $getWebOwned,
									'title'  => 'Website đang sử dụng',
									'icon'   => 'fa-globe',
									'color1' => '#fe5d70',
									'color2' => '#fe909d',
									'note' => '
										(
											<a target="blank" href="/admin/WebsiteList" style="font-size: 14px; color: white">
												<span class="fa fa-warning"></span>
												Sắp hết hạn: '.$getWebExpired.'
											</a>
										)
									'
								],
								[
									'count' => $getAppOwned,
									'title' => 'Ứng dụng đã mua',
									'icon'  => 'fa-plug',
									'color1' => '#01a9ac',
									'color2' => '#01dbdf'
								]
							];
						@endphp
						<style type="text/css">
							.dashboard-report-count{
								color: #FAFAFA;
								padding: 40px 10px;
								/*border: 1px solid #EAEAEA;*/
								/*box-shadow: 2px 3px 10px 0px rgba(119,119,119,0.1);*/
								border-radius: 5px;
								transition: .2s all;
								position: relative;
							}
							.dashboard-report-count:hover{
								opacity: .9;
							}
							.dashboard-report-count i{
								position: absolute;
								top: 50%;
								transform: translate(0, -50%);
								padding: 10px;
								display: inline-block;
								font-size: 30px;
								height: 70px;
								right: 10px;
								opacity: .6
							}
							.dashboard-report-count-right>span>i{
								position: absolute;
								top: 50%;
								left: 50%;
								transform: translate(-50%, -50%);
							}
							.dashboard-report-count-left{
								padding: 0 10px;
								position: relative;
							}
							.dashboard-report-count-left>.more-tooltip{
								position: absolute;
								right: 0;
								top: 0;
								color: gray
							}
							.chart svg{
								font-family: inherit !important;
							}
							.chart text{
								line-height: 1.6
							}
						</style>
						<section class="pd-20">
							<div class="flex flex-center flex-large">
								@foreach($report as $item)
								<div class="width-50 pd-10">
									<div class="dashboard-report-count" style="background-image: linear-gradient(135deg, {{ $item['color1'] }} 0%, {{ $item['color2'] }} 100%);">
										<div class="dashboard-report-count-left" style="width: calc(100% - 10px)">
											<div>
												{{ $item['title'] }}
											</div>
											<div style="font-size: 20px">
												<b>
													{{ $item['count'] }}
												</b>
												{!! ($item['note'] ?? null) !!}	
											</div>
										</div>
										<span>
											<i class="fa {{ $item['icon'] }}"></i>
										</span>
									</div>
								</div>
								@endforeach
							</div>
						</section>
					@break;

					{{-- Thành viên mới cập nhật thông tin --}}
					@case("usersNewUpdate")
						<div style="max-height: 320px;overflow: auto;">
							<table class="table table-border width-100">
								@foreach($users as $u)
									<tr>
										<td>
											<span class="user-avatar">
												<img src="{{user("avatar", $u->id)}}" />
											</span>
											{!! user("name_color", $u->id) !!}
										</td>
										<td>{{$u->email}}</td>
									</tr>
								@endforeach
							</table>
						</div>
					@break;

					{{-- Thống kê truy cập--}}
					@case("stats")
						@php
							$daysCount=Storage::stats("days", []);
							$daysChart="";
						@endphp
						@for($i=1; $i<=date("d"); $i++)
							@php
								$daysChart.="['$i', ".($daysCount[$i]??0)."],";
							@endphp
						@endfor
						<script type="text/javascript">
							google.charts.load('current', {'packages':['corechart']});
							google.charts.setOnLoadCallback(drawChart);

							function drawChart() {
								var data = google.visualization.arrayToDataTable([
									['Ngày', 'Số người'],
									{!! $daysChart !!}
								]);

								var options = {
									title: 'Lượt truy cập các ngày trong tháng',
									hAxis: {title: 'Hôm nay: {{$daysCount[date("j")]??0}}',  titleTextStyle: {color: '#333'}},
									vAxis: {minValue: 0}
								};

								var chart = new google.visualization.AreaChart(document.getElementById('chart-days'));
								chart.draw(data, options);
							}
						</script>
						<div style="width: 100%" id="chart-days"></div>
						<div class="panel-list">
						@foreach( Storage::stats("months", []) as $date=>$item )
							@php $total=0; @endphp
							<div class="panel panel-default">
								<div class="heading link">{{$date}}</div>
								<div class="panel-body hidden">
									@foreach($item as $os=>$count)
										@php $total+=$count; @endphp
									@endforeach
									<script type="text/javascript">
										google.charts.load('current', {'packages':['corechart']});
										google.charts.setOnLoadCallback(drawAnthonyChart{{vnStrFilter($date,"_")}}i);

										function drawAnthonyChart{{vnStrFilter($date,"_")}}i() {
											var data = new google.visualization.DataTable();
											data.addColumn('string', 'Topping');
											data.addColumn('number', 'Slices');
											data.addRows([
												['Điện thoại ({{$item["mobile"]??0}})', {{$item["mobile"]??0}}],
												['Máy tính bảng ({{$item["tablet"]??0}})', {{$item["tablet"]??0}}],
												['Máy tính ({{$item["desktop"]??0}})',  {{$item["desktop"]??0}}]
											]);
											var options = {
												title:'Tổng số: {{$total}}',
												width:500,
												height:200
											};

											var chart = new google.visualization.PieChart(document.getElementById('piechart-{{vnStrFilter($date,"_")}}'));
											chart.draw(data, options);
										}
								    </script>
								    <div id="piechart-{{vnStrFilter($date,"_")}}"></div>
								</div>
							</div>
						@endforeach
						</div>
					@break;

				@endswitch
					</div>
				</div>
			@endforeach

		</section>
		<section class="width-40 flex-margin">
			@php
				$rightSection = [
					"wallet"         => ["title"=>"Quản lý tài chính", "permission"=>"recharge", "count" => 1],
					"loginHistory" => ["title"=>"Lịch sử đăng nhập", "permission"=>"member"],
					"stats"        => ["title"=>"Thống kê dữ liệu", "permission"=>"admin"],
					"loginHistory" => ["title"=>"Lịch sử đăng nhập", "permission"=>"member"],
				];
				
			@endphp
			@foreach($rightSection as $type=>$item)
				@if( !permission($item["permission"]) )
					@continue
				@endif
				<div class="section" style="margin-bottom: 20px">
					<div class="heading">{{$item["title"]}}</div>
					<div class="section-body" style="padding: 0">
				@switch($type)

					{{-- Lịch sử đăng nhập --}}
					@case("loginHistory")
						<table class="table width-100">
							@foreach( (array)array_reverse( (array)user("login_history") ) as $time=>$browser)
							<tr>
								<td title="Thời gian">{{$time}}</td>
								<td title="Thiết bị">{{$browser}}{!!$browser==device(false, true) ? ' <span style="color: green">[Máy hiện tại]<span>' : ''!!}</td>
							</tr>
							@endforeach
						</table>
						<div class="menu-bg bd-top">Thiết bị của bạn: <b>{{device(false, true)}}</b></div>
					@break;

					{{-- Quản lý tài chính --}}
					@case("wallet")
						<section style="padding: 20px 15px">
							<div class="pd-15 menu bd-bottom">
								<b>
									Số dư hiện tại:
									<span style="color: tomato">
										{{ number_format( user('money') ) }} ₫
									</span>
								</b>
							</div>
							<div class="">
								<div class="pd-15">
									<div class="flex flex-middle">
										<div class="pd-5" style="width: 50px">
											<img src="/assets/admin/images/today.png">
										</div>
										<div class="pd-5" style="width: calc(100% - 50px)">
											<div>
												Tổng tiền giao dịch trong ngày
											</div>
											<div style="color: tomato; font-weight: bold;">
												@php
													$amountToday = PaymentHistory::where("users_id", user("id"))
														->where('amount', '<', 0)
														->where('created_at', '>', date('Y-m-d 00:00:00'))
														->where('created_at', '<', date('Y-m-d 23:59:59'))
														->sum('amount');
												@endphp
												{{ number_format($amountToday) }}
												₫
											</div>
										</div>
									</div>
								</div>
								<div class="pd-15">
									<div class="flex flex-middle">
										<div class="pd-5" style="width: 50px">
											<img src="/assets/admin/images/month.png">
										</div>
										<div class="pd-5" style="width: calc(100% - 50px)">
											<div>
												Tổng tiền giao dịch tháng {{ date('m') }}
											</div>
											<div style="color: tomato; font-weight: bold;">
												@php
													$amountMonth = PaymentHistory::where("users_id", user("id"))
														->where('amount', '<', 0)
														->where('created_at', '>', date('Y-m-01 00:00:00'))
														->where('created_at', '<', date('Y-m-d 23:59:59'))
														->sum('amount');
												@endphp
												{{ number_format($amountMonth) }}
												₫
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					@break;

					{{-- Thống kê --}}
					@case("stats")
					@php
						$diskUsed=folderSize(PUBLIC_ROOT);
						$percent=$diskUsed/mb2Bytes(CONFIG["MAXDISK"])*100;
						$percent=round($percent);

					@endphp
					<div class="progress-pie" data-color="" data-value="{{$percent}}" data-size="250">
						<p>
							<i style="font-size: 40px" class="fa fa-floppy-o"></i><br/>
							<b>Lưu trữ</b><br/>
							{{bytesConvert($diskUsed)}} / {{bytesConvert(mb2Bytes(CONFIG["MAXDISK"]))}}<br/>
							Còn trống {{100-$percent}}%
						</p>
					</div>
					@if( (100-$percent) <= 10 )
						<div class="alert-danger">Vui lòng xóa bớt tệp tin hoặc nâng cấp thêm dung lượng!</div>
					@endif
						<table class="width-100 table table-border">
							<tr>
								<td class="width-40 center">
									<span class="label-success">Bài viết</span>
								</td>
								<td class="width-60">
									Tổng số bài: <b>{{Posts::where("id",">",0)->total()}}</b><br />
									Đã công khai: <b>{{Posts::where("status", "public")->total()}}</b><br />
									Bản lưu nháp: <b>{{Posts::where("status", "draft")->total()}}</b><br />
									Trong thùng rác: <b>{{Posts::where("status", "trash")->total()}}</b><br />
									Đăng tháng {{date("n")}}/{{date("Y")}}: <b>{{Posts::whereMonth("created_at", date("m"))->whereYear("created_at", date("Y"))->total()}}</b><br />
									Đăng năm {{date("Y")}}: <b>{{Posts::whereYear("created_at", date("Y"))->total()}}</b><br />
									Bài của bạn: <b>{{Posts::where("users_id", user("id") )->total()}}</b><br />
								</td>
							</tr>
							<tr>
								<td class="width-40 center">
									<span class="label-success">Thành viên</span>
								</td>
								<td class="width-60">
									Tổng số: <b>{{Users::where("id",">",0)->total()}}</b><br />
									@foreach( Users::role() as $level=>$label)
										{{$label}}: <b>{{Users::where("role","=",$level)->total()}}</b><br />
									@endforeach
								</td>
							</tr>
							<tr>
								<td class="width-40 center">
									<span class="label-success">Tệp tin</span>
								</td>
								<td class="width-60">
									Tổng số: <b>{{Files::where("id",">",0)->total()}}</b><br/>
									@foreach( ["image"=>"Hình ảnh", "video"=>"Video", "audio"=>"Âm thanh", "document"=>"Tài liệu", "file"=>"Khác"] as $type=>$label)
										{{$label}}: <b>{{Files::where("type",$type)->total()}}</b><br />
									@endforeach
									Files của bạn: <b>{{Files::where("users_id", user("id") )->total()}}</b><br />
								</td>
							</tr>
						</table>
					@break;

					{{-- Cập nhật hệ thống --}}
					@case("update")
						@php
							function gitUpdateOuput($text){
								$out = '<div>';
								$lines = explode(PHP_EOL, $text);
								foreach( $lines as $line){
									if( empty( trim($line) ) ){
										continue;
									}
									$line = preg_replace_callback('/\<date\>(.+?)\<\/date\>/', function($data){
										return '<small class="gray">('.date('H:i - d/m/Y', strtotime($data[1]) ).')</small>';
									}, $line);
									$line = str_replace(
										[
											'Host key verification failed.',
										],
										[
											'<div class="red">Chưa thiết lập SSH key cho git</div>'
										]
									, $line);
									$out .= '<div class="pd-5">'.$line.'</div>';
								}
								$out .= '</div>';
								return $out;
							}
						@endphp
						<div class="menu bd" id="update-system">
							@if( function_exists('shell_exec') )
								@if( empty($_POST['pull']) )
									{!! gitUpdateOuput( shell_exec('git log -3 --no-merges --pretty=format:"<div><i class=\'fa fa-user\'></i> %an <date>%ad</date></div> <div style=\'padding: 6px 0; border-bottom: 1px solid #EAEAEA\'><i class=\'fa fa-info-circle\'></i> %s</div>"') ) !!}
								@endif
								@if( isset($_POST['updateSystem']) && $_POST['pull'] )
									<div class="pd-5">
										<b>Đã cập nhật phiên bản mới nhất!</b>
									</div>
									@php
										echo gitUpdateOuput( shell_exec("cd ../ && git pull ".Storage::setting('update_system_git_ssh_link')." dev 2>&1") );
									@endphp
								@endif
								<div class="pd-5 center">
									<button type="button" class="btn-primary" onclick="updateSystem(1)">
										<i class="fa fa-arrow-circle-up"></i>
										Cập nhật hệ thống
									</button>
								</div>
							@else
								<span class="red">
									Không thể cập nhật hệ thống do hàm <b>shell_exec</b> bị tắt
								</span>
							@endif
						</div>
						<script type="text/javascript">
							function updateSystem(pull){
								if( pull ){
									$('#update-system').html('<div class="center"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></div>');
								}
								$.ajax({
									url: '',
									type: 'POST',
									data: {updateSystem: 1, pull: pull},
									success: function(response){
										var dataEl = $(response).find('#update-system').html();
										$('#update-system').html(dataEl);
										if( pull ){
											setTimeout(function(){
												updateSystem(0);
											}, 2000);
										}
									},
									error: function(){
										updateSystem(pull);
									}
								});
							}
						</script>
					@break;

				@endswitch
					</div>
				</div>
			@endforeach
		</section>
</main>

{{-- Hỗ trợ bên dưới trang --}}
@if( permission('recharge') )
	@php
		$supportUserId = user('support_user_id');
		if( empty($supportUserId) ){
			$supportUserId = 1;
		}
	@endphp
	<section class="footer-support">
		<div class="main-layout">
			<div class="flex flex-middle flex-medium">
				<div class="width-0">
					
				</div>
				<div class="width-100">
					<div class="flex flex-middle" style="justify-content: flex-end;">
						<div class="pd-5 hidden-small" style="width: 60px">
							<span class="user-avatar">
								<img src="{{ user('avatar', $supportUserId) }}">
							</span>
						</div>
						<div class="pd-5">
							<div>
								Liên hệ tư vấn
							</div>
							<div class="footer-support-user-name">
								{{ user('name', $supportUserId) }}
							</div>
							<div style="font-size: 13px; opacity: .8">
								Chuyên viên tư vấn
							</div>
						</div>
						<div class="pd-5">
							<a class="btn-primary" href="tel:{{ user('phone', $supportUserId) }}" style="margin-left: 10px">
								<img src="/assets/admin/images/phone-call.png">
								<span class="hidden-small">
									{{ preg_replace('~^.{3}|.{4}(?!$)~', '$0 ',  user('phone', $supportUserId) )}}
								</span>
							</a>
							<a class="btn-gradient" href="https://zalo.me/{{ user('phone', $supportUserId) }}" style="margin-left: 10px">
								<img src="/assets/admin/images/zalo.png">
								<span class="hidden-small">
									Chat ZALO
								</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<style type="text/css">
		.footer-support{
			position: fixed;
			background: rgba(0,0,0,.75);
			bottom: 0;
			width: 100%;
			padding: 5px;
			left: 0;
			color: white
		}
		.footer-support .main-layout{
			margin: auto;
			max-width: 1200px
		}
		.footer-support a>img{
			height: 30px
		}
		.footer-support-user-name{
			color: var(--secondary-color);
			font-size: 16px;
			font-weight: bold;
		}
		.admin-container{
			padding-bottom: 120px
		}
	</style>
@endif