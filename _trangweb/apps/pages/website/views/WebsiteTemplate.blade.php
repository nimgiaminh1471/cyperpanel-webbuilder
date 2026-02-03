@php
	use models\BuilderDomain;
	use models\Users;
	use mailer\WebMail;
	use classes\WebBuilder;
	$categories = empty($categories) ? GET("_website_template_categories") : $categories;
	if(empty($categories)){
		$categories = "all";
	}
@endphp
{{-- Upload ảnh cho web mẫu --}}
@if( isset( $_FILES["website_template_image"] ) && permission("website_template_manager") )
	@php
		move_uploaded_file($_FILES["website_template_image"]["tmp_name"], PUBLIC_ROOT."/files/builder/images/template/".POST("app").".jpg");
	@endphp
@endif

@if( $websiteTemplateSearch )
	
@endif
@if( !empty($templateTitle) )
	<div class="center heading-basic" id="website-template-outer">
		<h2>
			{!! __($templateTitle) !!}
		</h2>
		@if( !empty($templateDescription) )
			<div>
				{!! $templateDescription !!}
			</div>
		@endif
	</div>
@endif
{!! Assets::show("/assets/loading-bar/progress-circle.css") !!}
@if( !empty(WEB_BUILDER["categories"][$categories]["description"]) )
	<div class="main-layout pd-20">
		<div style="line-height: 1.7;
	    background: white;
	    color: #666565;
	    padding: 30px;
	    font-size: 16px;
	    border-radius: 5px;
	    box-shadow: -4px -1px 20px 0 rgba(25, 42, 70, 0.13);">
			{!! WEB_BUILDER["categories"][$categories]["description"] !!}
		</div>
	</div>
@endif
<section class="main-layout">
	<div id="website-categories-list">
		@if( empty($templateTitle) )
			<div class="website-setup-step">
				<div class="flex flex-middle">
					<div class="width-20 center website-setup-step-item website-setup-step-active">
						<div class="progress-circle" data-value="20">
							<div>
								<span>
									<i>BƯỚC 1</i>
									<br>
									CHỌN GIAO DIỆN
								</span>
							</div>
						</div>
					</div>
					<div class="width-20 center">
						<div class="website-setup-step-line">
						</div>
					</div>
					<div class="width-20 center website-setup-step-item">
						<div class="progress-circle" data-value="80">
							<div>
								<span>
									<i>BƯỚC 2</i>
									<br>
									TẠO WEBSITE
								</span>
							</div>
						</div>
					</div>
					<div class="width-20 center">
						<div class="website-setup-step-line">
						</div>
					</div>
					<div class="width-20 center website-setup-step-item">
						<div class="progress-circle" data-bg="red" data-value="100">
							<div>
								<span>
									<i>BƯỚC 3</i>
									<br>
									HOÀN TẤT
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<style type="text/css">
				.website-setup-step{
					background: white;
					padding: 5px 5px;
					color: #B2B2B2;
					margin-bottom: 15px;
					border-radius: 10px
				}
				.website-setup-step-line{
					width: 80%;
					height: 3px;
					background: #B2B2B2;
					margin: auto
				}

				.website-setup-step .progress-circle[data-value="80"],
				.website-setup-step .progress-circle[data-value="100"] {
					background-image: -webkit-linear-gradient(left, transparent 50%, #B2B2B2 0);
					background-image: linear-gradient(to right, transparent 50%, #B2B2B2 0);
				}
				.website-setup-step .progress-circle[data-value="80"]:before,
				.website-setup-step .progress-circle[data-value="100"]:before {
					background-color: #B2B2B2 !important
				}
				.website-setup-step-active{
					color: #4CC9D8;
				}
				.website-setup-step-active .progress-circle {
					background-image: -webkit-linear-gradient(left, transparent 50%, #4CC9D8 0);
					background-image: linear-gradient(to right, transparent 50%, #4CC9D8 0);
				}
				/*.website-setup-step-active .progress-circle:before {
					background-color: #4CC9D8 !important
				}*/
				.website-setup-step-active .website-setup-step-line{
					background: #4CC9D8;
				}
				.website-setup-step-item>.flex>div{
					padding: 0 5px;
				}
			</style>
		@endif
		<div class="flex flex-middle flex-center">
			@php
				$categoriesAll["all"] = [
					"name"   => "Tất cả",
					"color1" => "#5089D7",
					"color2" => "#588DD5",
					"icon"   => 'fa-globe'
				];
				$categoriesList = WEB_BUILDER["categories"];
				$categoriesList = array_replace($categoriesAll, $categoriesList)
			@endphp
			@foreach($categoriesList as $name=>$item)
				<style type="text/css">
					.website-categories-actived:after{
						border-bottom: 1px solid {{$item["color1"]}} !important;
						width: 100%
					}
				</style>
				<a data-aos="zoom-in" data-aos-anchor-placement="top-bottom" data-aos-duration="1500" class="website-categories-list-item-{{$name}} website-categories-title" style="color: {{$item["color1"]}};"{!! isset($templateLink) ? ' href="/website/'.($name == "all" ? '' : $name).'#website-template-search"' : '' !!}>
					<span class="line-run{{ $name == $categories ? " website-categories-actived" : "" }}" data-id="{{$name}}">
						<span class="website-categories-icon" style="background-color: {{$item["color1"]}};
			background-image: linear-gradient(45deg, {{$item["color1"]}} 0%, {{$item["color2"]}} 100%);">
							<i class="fa {{$item["icon"]}}"></i>
						</span>
						<span class="website-categories-title text-inline">{{$item["name"]}}</span>
					</span>
				</a>
			@endforeach
		</div>
		@if( $websiteTemplateSearch )
			<div class="center pd-20">
				<input class="input width-60" type="search" id="website-template-search" placeholder="Tìm kiếm theo tên, mã giao diện…" style="border-radius: 30px">
			</div>
		@endif
	</div>
	@php
		$websiteTemplateLimit = 12;
		$webTemplate=BuilderDomain::where("id", 0);
		if( !empty( $_GET["_website_template_search"] ) && GET("_website_template_search")!="undefined" ){
			$keyword = str_replace('#', '', GET("_website_template_search"));
			if( is_numeric($keyword) ){
				$webTemplate = $webTemplate->orWhere("app_name", "!=", "")
					->where( ($_GET['search_by'] ?? 'app_id'), $keyword);
			}else{
				foreach(["app_name", "app_description"] as $column){
					$webTemplate = $webTemplate->orWhere("app_name", "!=", "")
						->where( $column, "like", "%".$keyword."%" );
				}
			}
		}else{
			$webTemplate = $webTemplate->orWhere("app_name", "!=", "");
			if( !empty( $categories ) && $categories != "all" ){
				$webTemplate = $webTemplate->where( "app_categories", $categories );
			}
		}
		$webTemplate=$webTemplate->orderBy("sort", "DESC")->paginate($websiteTemplateLimit);
	@endphp
	<div class="modal-click-outer" id="website-template" data-total="{{ $webTemplate->total() }}" style="padding-top: 20px">

		@if( isset($websitePending->id) )
			<div class="menu center pd-20" id="website-delay-create">
				Để tạo web mới, vui lòng chờ sau <b>{{ timestamp($websitePending->created_at)-time()+$delayCreate }}</b> giây.
			</div>
			<script>
				_createWebsiteDelay=setInterval(function(){
					var secEl=$("#website-delay-create>b");
					var sec=secEl.text()-1;
					if(sec>0){
						secEl.text(sec);
					}else{
						secEl.parent().html('<a class="btn-info" href="/website">Tạo web mới</a>');
						clearInterval(_createWebsiteDelay);
					}
				}, 1e3);
			</script>
		@else
			<div class="flex">
				@foreach($webTemplate as $w)
					@if( empty($w->app_price) && !permission("website_template_manager") )
						@continue;
					@endif
					<style type="text/css">
						#website-apps-item-{{$w->id}} .website-apps-item-price:before,
						#website-apps-item-{{$w->id}} .website-apps-item-price:after{
							background-color: {{ $categoriesList[$w->app_categories]["color1"] ?? "red" }}
						}
					</style>
					<div class="width-33x website-apps-item">
						<div class="website-apps-item-card" id="website-apps-item-{{$w->id}}" data-keep="1">
							<span class="website-apps-item-img" style="background-image: url(/files/builder/images/template/{{$w->app}}.jpg);">
								<span>
									<span class="website-apps-item-btn width-100">
										<span class="website-apps-item-title">
											{{ $w->app_name }}
										</span>
										<span>
											<a class="btn-primary" target="_blank" rel="nofollow" href="/website/preview/{{$w->app}}">
												<i class="fa fa-eye"></i>
												{{ __('Xem thực tế') }}
											</a>
											@if( permission("member") )
												<a class="btn-gradient modal-click" href="javascript:void(0)" data-modal="create-website-{{ $w->id }}">
													{!! __('<i class="fa fa-plus"></i> Tạo website') !!}
												</a>
											@else
												<a  class="btn-gradient modal-click" href="javascript:void(0)" data-modal="register-notify" data-template="{{$w->app}}">
													<i class="fa fa-plus"></i>
													Dùng thử
												</a>
											@endif
										</span>
									</span>
								</span>
								<span class="website-apps-item-black"></span>
								@if( Storage::setting('builder_setting_show_price') )
									<span class="website-apps-item-price-text">
										{{number_format($w->app_price)}} ₫
									</span>
									<span class="website-apps-item-price-outer" style="background: #EAEAEA">
										<span class="website-apps-item-price">
										</span>
									</span>
								@endif
							</span>
							<span class="website-apps-item-text flex-middle" style="display: flex">
								<i class="width-50" style="color: #999">
									<i class="fa {{ ($categoriesList[$w->app_categories]["icon"] ?? "") }}"></i>
									{{ ($categoriesList[$w->app_categories]["name"] ?? "") }}
								</i>
								<i class="width-50 right" style="color: #999">
									#{{ $w->app_id }}
								</i>
							</span>
						</div>
						@if( permission("website_template_manager") )
							<div class="website-apps-item-option">
								{{-- Cài đặt web --}}
								<a target="_blank" href="/admin/WebsiteManager?id={{$w->id}}" title="Ấn để đến trang quản lý web">
									<i class="fa fa-eye"></i>
								</a>
								<a title="Cài đặt" data-modal="website-template-option{{$w->id}}" class="pd-5 modal-click" data-keep="1">
									<i class="fa fa-cog"></i>
								</a>
								{!! modalForm("Thiết lập web mẫu: ".$w->domain, '
									<form class="pd-20 website-action-form">
										<div class="pd-5" style="margin-bottom: 10px">
											Tên theme: <b>'.$w->app.'</b>
										</div>
										<div style="margin-bottom: 10px">
											<input class="input width-100" placeholder="ID mẫu" type="text" name="website[config][app_id]" value="'.$w->app_id.'" />
										</div>
										<div style="margin-bottom: 10px">
											<input class="input width-100" placeholder="Tiêu đề web mẫu" type="text" name="website[config][app_name]" value="'.$w->app_name.'" />
										</div>
										<div style="margin-bottom: 10px">
											<select class="select width-100" name="website[config][app_categories]" value="'.$w->app_name.'">
												'.call_user_func(function($w, $categoriesList){
													$out='';
													foreach($categoriesList as $name=>$item){
														$out.='<option value="'.$name.'"'.($w->app_categories==$name ? ' selected' : '').'>'.$item["name"].'</option>';
													}
													return $out;
												}, $w, $categoriesList).'
											</select>
										</div>
										<div style="margin-bottom: 10px">
											<input class="input width-100 input-currency" placeholder="Giá bán" type="text" name="website[config][app_price]" value="'.number_format($w->app_price).'" />
										</div>
										<div style="margin-bottom: 10px">
											<textarea rows="10" placeholder="Từ khóa tìm kiếm (xuống dòng mỗi từ)" class="width-100" name="website[config][app_description]">'.htmlEncode($w->app_description).'</textarea>
										</div>
										<div class="pd-10 center">
											<button class="btn-primary website-action width-100" type="button" data-action="template" data-id="'.$w->id.'" data-domain="'.$w->domain.'">Lưu lại</button>
										</div>
									</form>
								', "website-template-option".$w->id, '600px', false, true, true) !!}
								{{-- Upload ảnh --}}
								<a title="Đổi ảnh" data-modal="website-template-image{{$w->id}}" class="pd-5 modal-click" data-keep="1">
									<i class="fa fa-image"></i>
								</a>
								{!! modalForm("Đổi ảnh web mẫu: ".$w->domain, '
									<form class="pd-20" method="POST" enctype="multipart/form-data">
										<input type="hidden" name="app" value="'.$w->app.'">
										<div class="pd-10">
											<input type="file" name="website_template_image" accept=".jpg,jpeg">
										</div>
										<div class="pd-10 center">
											<button class="btn-primary width-100" type="submit">Đổi ảnh</button>
										</div>
									</form>
								', "website-template-image".$w->id, '600px', false, true, true) !!}
							</div>
						@endif
					</div>
					{!! modalForm('Nhập thông tin website để khởi tạo', '
						<form class="form create-webiste-form" action="/admin/WebsiteManager" method="POST" style="padding: 20px">
							<div style="display:none">
								<input type="password" tabindex="-1"/>
								<input type="username" tabindex="-1"/>
							</div>
							<input type="hidden" name="template" value="'.$w->id.'" />
							<div class="flex flex-middle">
								<input type="text" class="input width-70 rm-radius" name="domain" placeholder="Nhập tên website của bạn. VD: websieudep" style="border-radius: 20px 0 0 20px !important" />
								<input type="text" class="input width-30 rm-radius" value=".'.DOMAIN.'" style="border-radius: 0 20px 20px 0 !important" readonly/>
							</div>
							<div class="pd-5">
								Sau khi tạo xong, bạn có thể đổi sang tên miền riêng tùy ý (Ví dụ: websieudep.vn)
							</div>
							<div>
								<input type="text" class="input width-100 rm-radius" name="user_login" placeholder="Email đăng nhập website" value="'.user("email").'" autocomplete="off" />
							</div>
							<div>
								<input type="password" class="input width-100 rm-radius" name="password" placeholder="Mật khẩu (Dùng để đăng nhập website đã tạo)" autocomplete="off" />
							</div>
							<div>
								<input type="password" class="input width-100 rm-radius" name="password2" placeholder="Nhập lại mật khẩu" autocomplete="off" />
							</div>
							<div class="hidden create-website-msg form-mrg"></div>
							<div class="center">
								<button type="button" class="btn-primary width-50">KHỞI TẠO WEBSITE</button>
							</div>
						</form>
						', 'create-website-'.$w->id, '600px', false, true, true) !!}
				@endforeach
			</div>
		@endif
		@if( $webTemplate->total()==0 )
			<div class="pd-10" style="padding: 20px 0;text-align: center; font-size: 20px">Không có mẫu website nào!</div>
		@endif
		@if( $webTemplate->total() > $websiteTemplateLimit )
			<div class="center" style="margin-bottom: 30px">
				<span class="link btn-primary" id="website-template-load-more" data-nextpage="1" style="border-radius: 30px; font-size: 16px; padding: 10px 25px">Xem thêm giao diện <i class="fa fa-arrow-circle-right"></i></span>
			</div>
		@endif
	</div>
	{!!
		modalForm('', '
			<div class="center" style="padding: 30px 10px; line-height: 1.5">
				<div class="pd-5">
					Quý khách chưa đăng ký tài khoản, vui lòng đăng ký và đăng nhập tài khoản để có thể tạo trang web!
				</div>
				<div class="pd-5">
					<a style="border-radius: 30px; padding: 10px 20px" href="javascript:void(0)" class="btn btn-primary modal-click" href="javascript:void(0)" data-modal="register-box">Click để đăng ký tài khoản</a>
				</div>
			</div>
		', 'register-notify', '600px', false, true, true)
	!!}

</div>

{{-- Tạo mẫu web --}}
	@if( permission("website_template_manager") && isset($allowCreateWebsiteTemplate) )
		{{Assets::footer('/assets/gallery/style.css')}}
		<section class="panel panel-default" style="margin-top: 20px">
			<div class="heading link">Tạo web khách hàng dùng code riêng</div>
			<div class="panel-body hidden">
				<form class="form create-webiste-form-admin" action="/website" method="POST" style="padding: 20px 10px; max-width: 650px; margin: auto" enctype="multipart/form-data">
					<input type="hidden" name="template" value="0" />
					<input type="hidden" name="customer" value="1" />
					<div>Tên miền, VD: shopbanhang</div>
					<div class="flex flex-middle">
						<input type="text" class="input width-70 rm-radius" name="domain" placeholder="Tên miền, VD: shopbanhang" />
						<input type="text" class="input width-30 rm-radius" value=".{{DOMAIN}}" readonly/>
					</div>
					<div>
						<input type="password" class="input width-100 rm-radius" name="password" placeholder="Mật khẩu (Dùng để đăng nhập website đã tạo)" />
					</div>
					<div>
						Mã nguồn (.zip)
						<input class="width-100" type="file" name="source" accept=".zip" />
					</div>
					<div>
						Database (.sql)
						<input class="width-100" type="file" name="database" accept=".sql" />
					</div>
					<div class="alert-info create-website-msg form-mrg hidden"></div>
					<div class="center gallery form-mrg">
						<progress value="0" max="100" class="progress width-100" style="display: none;"></progress>
					</div>
					<div class="center">
						<button type="button" class="btn-primary">TẠO WEB</button>
					</div>
				</form>
			</div>
		</section>

		<section class="panel panel-info">
			<div class="heading link">Tạo web mẫu</div>
			<div class="panel-body hidden">
				<form class="form create-webiste-form-admin" action="/website" method="POST" style="padding: 20px 10px; max-width: 650px; margin: auto" enctype="multipart/form-data">
					<input type="hidden" name="template" value="0" />
					<div>Tên miền, VD: shopbanhang</div>
					<div class="flex flex-middle">
						<input type="text" class="input width-70 rm-radius" name="domain" placeholder="Tên miền, VD: shopbanhang" />
						<input type="text" class="input width-30 rm-radius" value=".{{DOMAIN}}" readonly/>
					</div>
					<div>
						<input type="password" class="input width-100 rm-radius" name="password" placeholder="Mật khẩu (Dùng để đăng nhập website đã tạo)" />
					</div>
					<div>
						Mã nguồn (.zip)
						<input class="width-100" type="file" name="source" accept=".zip" />
					</div>
					<div>
						Database (.sql)
						<input class="width-100" type="file" name="database" accept=".sql" />
					</div>
					<div class="alert-info create-website-msg form-mrg hidden"></div>
					<div class="center gallery form-mrg">
						<progress value="0" max="100" class="progress width-100" style="display: none;"></progress>
					</div>
					<div class="center">
						<button type="button" class="btn-primary">TẠO WEB MẪU</button>
					</div>
				</form>
			</div>
		</section>
	@endif

</section>

@if( Storage::setting('builder_categories_promotion') && empty($disablePromotion) )
	{!!
		PromotionTrial([
			"title"       => __("Bạn vẫn chưa tìm được giao diện website vừa ý?"),
			"description" => __("Bạn có thể gợi ý cho chúng tôi về giao diện bạn mong muốn. Chúng tôi sẽ chọn lọc hoặc thiết kế riêng mẫu phù hợp với bạn."),
			"button"      => __("Tư vấn thiết kế"),
			"link"        => __("javascript:void(Tawk_API.toggle())"),
			"column"      => true
		])
	!!}
@endif

{{-- CSS --}}
@if( Storage::setting('builder_categories_style') == 1 )
	<style type="text/css">
		.website-terms-body{
			line-height: 1.8
		}
		#website-categories-list{
			padding: 10px
		}
		#website-categories-list a{
			text-align: center;
			padding: 5px;
			width: 16.6666%
		}
		#website-categories-list a>span{
			display: block;
		}
		#website-categories-list a>span>span{
			display: block;
			text-align: center;
			margin: auto;
		}
		.website-categories-icon{
			width: 80px;
			height: 80px;
			border-radius: 50%;
			position: relative;
		}
		.website-categories-active>.website-categories-icon,
		#website-categories-list a:hover .website-categories-icon{
			animation: spin 1s;
		}
		.website-categories-icon>i{
			font-size: 40px;
			color: white;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%,-50%);
		}
		.website-categories-title{
			padding: 8px;
		}
	</style>
@else
	<style type="text/css">
		.website-terms-body{
			line-height: 1.8
		}
		#website-categories-list{
			padding: 10px;
		}
		#website-categories-list a{
			padding: 10px;
			width: 16.6666%
		}
		#website-categories-list a>span{
			display: block;
			text-align: center;
			border: 1px solid #eee;
			background: white;
			border-radius: 5px;
			padding: 10px;
			box-shadow: 0px 7px 20px 0px rgba(0,0,0,0.08);
			transition: .3s;
		}
		#website-categories-list a>span>span{
			display: block;
			text-align: center;
			margin: auto;
		}
		.website-categories-icon{
			width: 80px;
			height: 80px;
			border-radius: 50%;
			position: relative;
			background-color: transparent !important;
			background-image: none !important; 
		}
		.website-categories-active>.website-categories-icon,
		#website-categories-list a:hover .website-categories-icon{
			animation: spin 1s;
		}
		.website-categories-icon>i{
			font-size: 40px;
			color: var(--primary-color);
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%,-50%);
		}
		.website-categories-title{
			padding: 8px;
			color: #313131 !important
		}
		.website-categories-actived:after{
			border-bottom: 5px solid var(--primary-color) !important; 
		}
		.website-categories-actived{
			
		}
	</style>
@endif
<style type="text/css">
	.website-apps-item{
		padding: 10px 30px;
		position: relative;
		margin-bottom: 20px
	}
	.website-apps-item span{
		display: block
	}
	.website-apps-item>.website-apps-item-card{
		display: block;
		position: relative;
		background: white;
		border-radius: 10px;
		overflow: hidden;
		box-shadow: 0 10px 15px -10px rgba(30,45,62,.21), 0 5px 40px -10px rgba(31,44,60,.1);
		border: 1px solid #EAEAEA;
	}
	.website-apps-item-img{
		padding-top: 20px;
		height: 300px;
		transition: background-position 2s ease-in-out;
		width: 100%;
		background-position: top center;
		background-size: 100% auto!important;
		background-repeat: no-repeat;
		width: 100%;
	}
	.website-apps-item-img:hover{
		background-position: bottom center!important;
		transition: background-position 10s linear 0s;
	}
	.website-apps-item-black{
		width: 100%;
		height: 100%;
		opacity: .5;
		top: 0;
		position: absolute;
	}
	.website-apps-item-img:hover .website-apps-item-black{
		background: black;
	}
	.website-apps-item-title{
		padding: 15px 10px;
		font-size: 16px;
		font-weight: bold;
		color: white !important
	}
	.website-apps-item-text{
		padding: 15px 10px
	}
	.website-apps-item-text i{
		font-style: normal;
	}
	.website-apps-item-img{
		position: relative;
		background: #fff;
		overflow: hidden;
	}
	.website-apps-item>div:hover .website-apps-item-btn{
		opacity: 1;
		z-index: 1
	}
	.website-apps-item:hover img{
		opacity: .5;
	}
	.website-apps-item-btn{
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%,-50%);
		opacity: 0;
		z-index:-1;
		color: white;
		text-align: center;
	}
	.website-apps-item-btn>i{
		font-style: normal;
	}
	.website-apps-item-btn a{
		border-radius: 25px;
		margin: 2px
	}
	.website-apps-item-option{
		position: absolute;
		top: 30px;
		right: 40px
	}
	.website-apps-item-option a{
		color: tomato !important
	}
	.website-apps-item-price-outer{
		position: absolute;
		bottom: 0;
		right: 0;
	}
	.website-apps-item-price-text{
		position: absolute;
		bottom: 0;
		right: 0;
		height: 25px;
		line-height: 25px;
		z-index: 11;
		color: #FAFAFA;
		text-align: center;
		font-size: 13px;
		width: 102px;
		transition: .6s all;
	}
	.website-apps-item-price{
		position: relative;
	}

	.website-apps-item-price:before,
	.website-apps-item-price:after{
		transition: .6s all;
		width: 105px;
		right: 0;
		content: "";
		height: 26px;
		z-index: 10;
		bottom: 0;
		position: absolute;
	}
	.website-apps-item-price:before {
		transform: skewX(-30deg);
	}
	.website-apps-item-price:after {
		transform: skewX(30deg);
	}
	.website-apps-item-price:hover {
		background: #07b;
	}
	#website-template .modal .input,
	#website-template .modal .select,
	#website-template .modal button{
		border-radius: 20px !important;
		margin-top: 10px
	}
	@media(max-width: 767px) {
		.website-terms-body ul{
			padding-left: 10px 
		}
		.website-categories-icon{
			width: 50px;
			height: 50px;
		}
		.website-apps-item{
			width: 100%
		}
		#website-categories-list{
			padding: 10px 0
		}
		.website-categories-icon{
			width: 50px;
			height: 50px;
		}
		.website-categories-icon>i{
			font-size: 25px
		}
		.website-apps-item{
			width: 100%
		}
		#website-categories-list a{
			width: 33.3333%
		}
		#website-template-search{
			width: 90% !important
		}
		.website-categories-title{
			font-size: 13px
		}
		.website-setup-step{
			display: none
		}
	}

	@media(max-width: 380px) {
		#website-categories-list a{
			width: 50%
		}
	}
	
	@media(min-width: 768px) and (max-width: 1023px) {
		.website-apps-item{
			width: 50%
		}
		#website-categories-list a{
			width: 25%
		}
	}
</style>


<script type="text/javascript">
	$(document).ready(function(){
		
		//Click nút thao tác
		$("#website-template").on("click", ".website-action", function(){
			var form=$(this).parents(".website-action-form");
			var action=$(this).attr("data-action");
			var thisID=$(this).attr("data-id");
			var thisEl=$(this).parent();
			thisEl.hide();
			var actionSuccessMsg={
				delete: "Đã xóa website",
				changeDomain: "Đổi tên miền thành công!",
				upgradeDisk: "Nâng cấp thành công!",
				renew: "Gia hạn website thành công!",
				ssl: "Cài đặt chứng chỉ SSL thành công, liên hệ nếu gặp lỗi",
				manager: "Đã lưu thiết lập",
				template: "Đã lưu thiết lập",
				backup: "Mua dịch vụ thành công"
			};
			form.find(".website-action-msg-"+thisID).html("Đang kết nối, vui lòng chờ...").show();
			$.ajax({
				"url"  : "/admin/WebsiteManager?id="+thisID,
				"data" : "website[action]="+action+"&website[id]="+thisID+"&"+form.serialize(),
				"type" : "POST",
				success: function(response){
					console.log(response);
					var data=$(response).find(".website-action-msg-"+thisID).html();
					if(typeof data=="undefined"){
						form.find(".website-action-msg-"+thisID).removeClass("alert-danger").addClass("alert-success").html('<i class="fa fa-check"></i> '+actionSuccessMsg[action]);
						$(".modal").click(function(){
							$("#loading").show();
							location.reload();
						});
						if(action=="template"){
							location.reload();
						}
						setTimeout(function(){
							location.reload();
						},7e3);
					}else{
						form.find(".website-action-msg-"+thisID).html(data).show();
						thisEl.show();
					}
				},
				error: function(error){
					alert("Lỗi kết nối, hãy thử lại");
					console.log(error);
					thisEl.show();
					form.find(".website-action-msg-"+thisID).html("Lỗi kết nối, vui lòng ấn lại");
				}
			});
		});


		function number_format( number, decimals, dec_point, thousands_sep ) {                    
			var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
			var d = dec_point == undefined ? "," : dec_point;
			var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
			var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
			return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
		}

		//Click tạo website
		function createWebsiteSubmit(el){
			var form=$(el).parents("form");
			form.find("button").hide();
			form.find(".create-website-msg").addClass("alert-info").removeClass("alert-danger").html("Đang tiến hành đăng ký website...").show();
			$.ajax({
				"url"  : "/admin/WebsiteManager",
				"data" : "create=1&"+form.serialize(),
				"type" : "POST",
				success: function(response){
					var msg=$(response).find("#create-website-error").html();
					if( $(response).find(".error-message").length > 0 ){
						var msg = $(response).find(".error-message").text();
					}
					if(typeof msg=="undefined"){
						location.href="/admin/WebsiteManager?id="+form.find("input[name='domain']").val()+".{{ DOMAIN }}";
					}else{
						console.log(response);
						form.find(".create-website-msg").addClass("alert-danger").removeClass("alert-info").html(msg).show();
						form.find("button").show();
					}
				},
				complete: function(){
					//form.find("input[name='domain']").focus();
				},
				error: function(error){
					$("#loading").hide();
					form.find("button").show();
					var msg = "Lỗi kết nối, vui lòng ấn lại lần nữa";
					form.find(".create-website-msg").addClass("alert-danger").removeClass("alert-info").html(msg).show();
					alert(msg);
				}
			});
		}
		$("#website-template").on("click", ".create-webiste-form button", function(){
			createWebsiteSubmit(this);
		});
		$("#website-template").on("keyup", ".create-webiste-form input", function(e){
			if(e.keyCode==13){
				createWebsiteSubmit(this);
			}
		});
		
		@if( permission("website_template_manager") )
			//Tạo web mẫu
			$(".create-webiste-form-admin").on("click", "button", function(){
				var form = $(this).parents("form");
				var wrong = false;
				form.find("input,select").each(function(){
					if( $(this).val().length == 0 ){
						wrong = true;
					}
				});
				if( wrong ){
					alert("Vui lòng nhập đầy đủ thông tin");
					return false;
				}
				var formData = new FormData( $(this).parents("form")[0] );
				formData.append("create", 1);
				$.ajax({
					"url"  : "/admin/WebsiteManager",
					data: formData,
					processData: false,
					contentType: false,
					type: "POST",
					xhr: function () {
						var xhr = new window.XMLHttpRequest();
						xhr.upload.addEventListener("progress", function (e) {
							if (e.lengthComputable) {
								var stt = e.loaded/e.total;
								stt = parseInt(stt*100);
								form.find('progress').show().val(stt);
							}
						}, false);
						return xhr;
					},
					success: function(response){
						$("#loading").hide();
						console.log(response);
						var msg = $(response).find("#create-website-error").html();
						if( $(response).find(".error-message").length > 0 ){
							var msg = $(response).find(".error-message").text();
						}
						if(typeof msg=="undefined"){
							location.reload();
						}else{
							form.find(".create-website-msg").addClass("alert-danger").removeClass("alert-info").html(msg).show();
						}
					},
					complete: function(){
						form.find("input[name='domain']").focus();
					},
					error: function(error){
						$("#loading").hide();
						alert("Lỗi kết nối, hãy ấn lại lần nữa");
					}
				});
			});
		@endif
		
		$(".modal").on("click", ".modal-click", function(){
			setTimeout(function(){
				$(".modal:visible").find("input[name='domain']").focus();
			}, 200);
		});

		$("#website-template").on("click", ".modal-click[data-modal='register-notify']", function(){
			setCookie("continue_link", "/website/preview/"+$(this).attr("data-template")+"?create", 1);
		});

		//Click chọn thể loại web
		$("#website-categories-list a>span").on("click", function(){
			var link = $(this).parent().attr('href');
			if( typeof link != 'undefined' ){
				return;
			}
			$(".website-categories-actived").removeClass("website-categories-actived");
			$(this).addClass("website-categories-actived");
			refreshWebsiteTemplate({
				append: false,
				loading: true
			});
		});

		//Click xem thêm mẫu
		$("#website-template").on("click", "#website-template-load-more", function(){
			refreshWebsiteTemplate({
				append: true,
				loading: false
			});
		});

		//Tìm kiếm giao diện
		$("#website-template-search").on("change keyup", function(){
			refreshWebsiteTemplate({
				append: false,
				loading: false
			});
		});
		//Tải lại danh sách web mẫu
		function refreshWebsiteTemplate(config){
			if( config.loading ){
				$("#loading").show();
			}
			if( config.append ){
				var nextpage = $("#website-template-load-more").attr("data-nextpage");
				var nextpage = parseInt(nextpage) + 1;
				$("#website-template-load-more").attr("data-nextpage", nextpage)
			}else{
				var nextpage = 1;
			}
			$.ajax({
				"url"  : "",
				"data" : {
					_website_template_categories: $(".website-categories-actived").attr("data-id"),
					_website_template_search: $("#website-template-search").val(),
					page: nextpage
				},
				"type" : "GET",
				success: function(response){
					var contents = $(response).find("#website-template").html();
					var total = $("#website-template").attr("data-total");
					if(typeof contents!="undefined"){
						if( config.append ){
							var items = $(response).find("#website-template>.flex");
							$("#website-template>.flex").append( items.html() );
							if( $("#website-template .website-apps-item").length >= total ){
								$("#website-template-load-more").hide();
							}
						}else{
							$("#website-template").html(contents);
						}
					}
					if(typeof AOS != 'undefined'){
						AOS.refreshHard(); // Fix hiệu ứng động
					}
					if( config.loading ){
						$('html,body').animate({
							scrollTop: $('#website-template').offset().top - 120
						}, 200);
					}
				},
				complete: function(){
					$("#loading").hide();
				},
				error: function(error){
					setTimeout(function(){
						refreshWebsiteTemplate();
					}, 4e3);
				}
			});
		}
	});
</script>