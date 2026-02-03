@php
	use models\CashFlow;
	use models\BuilderDomain;
	$dateFrom = explode('/', $_POST['filter']['date_from'] ?? date('01/m/Y') );
	$dateFrom = "{$dateFrom[2]}-{$dateFrom[1]}-{$dateFrom[0]} 00:00:00";
	$dateTo = explode('/', $_POST['filter']['date_to'] ?? date('d/m/Y') );
	$dateTo = "{$dateTo[2]}-{$dateTo[1]}-{$dateTo[0]} 23:59:59";
	$reportYear = $_POST['filter']['report_year'] ?? date('Y');

	// Lấy số tiền tổng thu
	$receiptsTotal = CashFlow::select('SUM(amount) as amount_sum')
		->where('type', 0)
		->where('status', 1)
		->whereNull('deleted_at')
		->where('created_at', '>=', $dateFrom)
		->where('created_at', '<=', $dateTo)
		->first()->amount_sum;

	// Lấy số tiền tổng chi
	$paymentTotal = CashFlow::select('SUM(amount) as amount_sum')
		->where('type', 1)
		->where('status', 1)
		->whereNull('deleted_at')
		->where('created_at', '>=', $dateFrom)
		->where('created_at', '<=', $dateTo)
		->first()->amount_sum;

	// Lấy tổng khách hàng đã mua
	$customerPaidTotal = CashFlow::select('COUNT(DISTINCT customer_id) as amount_sum')
		->where('type', 0)
		->where('customer_id', '>', 0)
		->where('status', 1)
		->whereNull('deleted_at')
		->where('created_at', '>=', $dateFrom)
		->where('created_at', '<=', $dateTo)
		->first()->amount_sum;
	$report = [
		[
			'count'  => number_format($receiptsTotal),
			'title'  => 'Tổng thu',
			'icon'   => 'fa-plus',
			'color1' => '#fe5d70',
			'color2' => '#fe909d'
		],
		[
			'count' => number_format($paymentTotal),
			'title' => 'Tổng chi',
			'icon'  => 'fa-minus',
			'color1' => '#01a9ac',
			'color2' => '#01dbdf'
		],
		[
			'count' => number_format($receiptsTotal - $paymentTotal),
			'title' => 'Lãi tạm thời',
			'icon'  => 'fa-check-circle-o',
			'color1' => '#fe9365',
			'color2' => '#feb798'
		],
		[
			'count' => $customerPaidTotal,
			'title' => 'Khách mua gói',
			'icon'  => 'fa-users',
			'color1' => '#0ac282',
			'color2' => '#0df3a3'
		]
	];

	// Doanh thu theo tháng
	$reportYearData = [];
	for($mth = 1; $mth <= 12; $mth++){
		$month = $mth;
		if($month < 10){
			$month = '0'.$month;
		}
		$getInvoice = CashFlow::select('type', 'SUM(amount) AS amount_total')
			->whereNull('deleted_at')
			->where('status', 1)
			->where('created_at', '>=', "{$reportYear}-{$month}-01 00:00:00")
			->where('created_at', '<=', "{$reportYear}-{$month}-31 23:59:59")
			->groupBy('type')
			->get();
		$amount = 0;
		foreach($getInvoice as $item){
			if( $item->type == 0 ){
				$amount += $item->amount_total;
			}else{
				$amount -= $item->amount_total;
			}
		}
		$reportYearData[] = $amount;
	}

	// Số liệu tỉ lệ dùng gói
	$reportPackageData = [];
	$getWebsite = BuilderDomain::select('package', 'COUNT(package) AS package_count')
		->groupBy('package')
		->where('app_price', 0)
		->where('created_at', '>=', $dateFrom)
		->where('created_at', '<=', $dateTo)
		->get();
	foreach($getWebsite as $item){
		if( empty($item->package) ){
			continue;
		}
		$reportPackageData[] = [
			'name' => (WEB_BUILDER['package'][$item->package]['name'] ?? 'Khác').' ('.$item->package_count.')',
			'y'    => (int)$item->package_count
		];
	}

	// Tính tổng ngân sách
	$budget = (CashFlow::select('SUM(amount) as amount_sum')
		->where('type', 0)
		->where('status', 1)
		->whereNull('deleted_at')
		->first()->amount_sum - CashFlow::select('SUM(amount) as amount_sum')
		->where('type', 1)
		->where('status', 1)
		->whereNull('deleted_at')
		->first()->amount_sum);
@endphp
{!! Assets::show("/assets/form/date-picker.css", "/assets/form/date-picker.js") !!}
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
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

<section class="section" id="sales-report">
	<div class="flex flex-medium flex-middle heading heading-small">
		<div class="pd-10" style="width: 250px">
			<i class="fa fa-pie-chart"></i>
			BÁO CÁO DOANH SỐ
		</div>
		<div class="pd-10 right" style="font-size: initial; color: initial; width: calc(100% - 250px)">
			<form>
				<div class="flex">
					<div style="width: calc(100% - 600px)"></div>
					<div class="pd-5" style="width: 300px">
						<div class="form-date-wrap" data-format="day/month/year">
							<div class="form-date-picker form-date-picker-bottom hidden"></div>
							<div class="input-icon">
								<i class="fa fa-calendar"></i>
								<input class="input width-100" placeholder="Từ ngày" type="text" name="filter[date_from]" value="{{ date('01/m/Y') }}" readonly=""/>
							</div>
							<code>{"allow":{"hours":[],"minutes":"","requiredHour":false,"days":"","months":"","weekDay":["mon","tue","wed","thu","fri","sat","sun"],"min":{"y":{{ (date("Y") - 5) }},"m":2,"d":14},"max":{"y":{{ (date("Y") + 1) }},"m":2,"d":14}},"value":{"day":"{{ date("d") }}","month":"{{ date("m") }}","year":"{{ date("Y") }}"}}</code>
						</div>
					</div>
					<div class="pd-5" style="width: 300px">
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
			</form>
		</div>
	</div>
	<div class="section-body">
		<div class="flex flex-center flex-large">
			@foreach($report as $item)
				<div class="width-25 pd-10">
					<div class="dashboard-report-count" style="background-image: linear-gradient(135deg, {{ $item['color1'] }} 0%, {{ $item['color2'] }} 100%);">
						<div class="dashboard-report-count-left" style="width: calc(100% - 80px)">
							<div>
								{{ $item['title'] }}
							</div>
							<div style="font-size: 25px">
								<b>
									{{ $item['count'] }}
								</b>
							</div>
						</div>
						<span>
							<i class="fa {{ $item['icon'] }}"></i>
						</span>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>

<section class="section" id="sales-year-report">
	<div class="flex flex-medium flex-middle heading">
		<div class="width-30">
			<i class="fa fa-bar-chart"></i>
			Doanh số theo tháng
		</div>
		<div class="width-70 pd-5 right" style="font-size: initial; color: initial">
			<form>
				<select name="filter[report_year]" onchange="refreshReportSection('#sales-year-report')">
					@for($i = 0; $i < 5; $i++)
						<option value="{{ date('Y') - $i }}">
							Năm {{ date('Y') - $i }}
						</option>
					@endfor
				</select>
			</form>
		</div>
	</div>
	<div class="section-body">
		<div class="chart bg" style="padding: 20px">
			<div id="sale-report-chart" style="min-width: 310px; height: 400px; margin: 0 auto">
				<script>
					Highcharts.chart('sale-report-chart', {
					    title: {
					        text: 'Doanh thu năm: {{ $reportYear }}'
					    },
					    chart: {
					    	type: 'column'
					    },
					    subtitle: {
					        text: ''
					    },
					    xAxis: {
					        categories: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"]
					    },
					    yAxis: {
					        title: {
					            text: ''
					        },
					        labels: {
					        	formatter: function() {
					        		return this.value / 1000000 + ' triệu';
					        	}
					        }
					    },
					    plotOptions: {
					        line: {
					            dataLabels: {
					                enabled: false
					            },
					            enableMouseTracking: true
					        }
					    },
					    series: [{
					        name: 'Doanh thu',
					        data: {!! json_encode($reportYearData) !!}
					    }],
					    lang: {
					        viewFullscreen: 'Xem toàn màn hình',
					        printChart: 'In biểu đồ',
					        numericSymbols: [null, "M", "G", "T", "P", "E"]
					    }
					});
				</script>
			</div>
		</div>
	</div>
</section>

<section class="section" id="sales-package-report">
	<div class="flex flex-medium flex-middle heading">
		<div class="width-30">
			<i class="fa fa-bar-chart"></i>
			Thống kê gói website
		</div>
		<div class="width-70 pd-5 right" style="font-size: initial; color: initial">
			<form>
				<div class="flex">
					<div style="width: calc(100% - 600px)"></div>
					<div class="pd-5" style="width: 300px">
						<div class="form-date-wrap" data-format="day/month/year">
							<div class="form-date-picker form-date-picker-bottom hidden"></div>
							<div class="input-icon">
								<i class="fa fa-calendar"></i>
								<input class="input width-100" placeholder="Từ ngày" type="text" name="filter[date_from]" value="{{ date('01/m/Y') }}" readonly=""/>
							</div>
							<code>{"allow":{"hours":[],"minutes":"","requiredHour":false,"days":"","months":"","weekDay":["mon","tue","wed","thu","fri","sat","sun"],"min":{"y":{{ (date("Y") - 5) }},"m":2,"d":14},"max":{"y":{{ (date("Y") + 1) }},"m":2,"d":14}},"value":{"day":"{{ date("d") }}","month":"{{ date("m") }}","year":"{{ date("Y") }}"}}</code>
						</div>
					</div>
					<div class="pd-5" style="width: 300px">
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
			</form>
		</div>
	</div>
	<div class="section-body">
		<div class="chart bg" style="padding: 20px">
			<div id="sale-package-chart" style="min-width: 310px; height: 400px; margin: 0 auto">
				<script>
					Highcharts.chart('sale-package-chart', {
					    title: {
					        text: 'Tỉ lệ dùng các gói website'
					    },
					    chart: {
					    	type: 'pie'
					    },
					    subtitle: {
					        text: ''
					    },
					    yAxis: {
					        title: {
					            text: ''
					        }
					    },
					    tooltip: {
					    	pointFormat: 'Tỉ lệ: <b>{point.percentage:.1f}%</b>'
					    },
					    accessibility: {
					    	point: {
					    		valueSuffix: '%'
					    	}
					    },
					    plotOptions: {
					        line: {
					            dataLabels: {
					                enabled: false
					            },
					            enableMouseTracking: true
					        }
					    },
					    series: [{
                            name: 'Số website',
                            colorByPoint: true,
                            data: {!! json_encode($reportPackageData) !!}
                        }],
					    lang: {
					        viewFullscreen: 'Xem toàn màn hình',
					        printChart: 'In biểu đồ',
					        numericSymbols: [null, "M", "G", "T", "P", "E"]
					    }
					});
				</script>
			</div>
		</div>
	</div>
</section>

<section class="menu bd pd-20">
	<i class="fa fa-bar-chart"></i>
	Tổng doanh thu
	<small>
		({{ date('d/m/Y', timestamp(CashFlow::whereNull('deleted_at')->where('status', 1)->first()->created_at ?? date('Y-m-d 00:00:00') ) ) }} -> {{ date('d/m/Y') }} )
	</small>:
	<span style="color: {{ $budget > 0 ? 'blue' : 'red' }}">
		<b>
			{{ number_format($budget) }}
		</b>
	</span>
</section>

<script type="text/javascript">
	/*
	 * Làm mới phần báo cáo
	 */
	function refreshReportSection(element){
		$('#loading').show();
		$.ajax({
			url: '',
			type: 'POST',
			data: $(element).find('form').serialize(),
			success: function(response){
				var el = $(response).find(element+' .section-body').html();
				$(element).find('.section-body').html( el );
			},
			complete: function(){
				$('#loading').hide();
			},
			error: function(){
				setTimeout(function(){
					refreshReportSection(element);
				}, 2000);
			}
		})
	}
	$('#sales-report').on('click', '.primary-hover', function(){
		refreshReportSection('#sales-report');
	});
	$('#sales-package-report').on('click', '.primary-hover', function(){
		refreshReportSection('#sales-package-report');
	});
</script>