@extends("Default")
@php
	use models\WebsiteGuest;
	define("PAGE", [
		"name"        =>"Trang khách hàng",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"index,follow"
	]);
@endphp


@section("main")
	@parent
	<section class="main-layout">
		<div class="modal-click-outer" id="website-template">
		@php
			/*
			 * Tạo website của khách
			 */
			if( permission("website_manager") && isset($_POST["website_guest"]) ){
				$data = $_POST["website_guest"];
				$data["domain"] = vnStrFilter($data["domain"], ".", false);
				if( empty($data["domain"]) || empty($data["title"]) || empty($_FILES["image"]["name"]) ){
					$error = "Vui lòng điền đầy đủ thông tin";
				}
				if( empty($error) ){
					move_uploaded_file($_FILES["image"]["tmp_name"], PUBLIC_ROOT."/files/builder/images/website-guest/".$data["domain"].".jpg");
					WebsiteGuest::create($data);
					redirect(THIS_LINK, true);
				}else{
					echo '<div class="alert-danger">'.$error.'</div>';
				}
			}

			/*
			 * Xóa website của khách
			 */
			if( permission("website_manager") && isset($_GET["delete_website_guest"]) ){
				WebsiteGuest::where("domain", $_GET["delete_website_guest"])->delete();
				$image = PUBLIC_ROOT."/files/builder/images/website-guest/".$_GET["delete_website_guest"].".jpg";
				if( file_exists($image) ){
					unlink($image);
				}
				redirect(THIS_LINK, true);
			}

			$guestLimit = 6;
			$websiteGuest = WebsiteGuest::orderBy("created_at", "DESC")->paginate($guestLimit);
		@endphp

		@if( !empty($websiteGuest) )
			<div class="flex">
				@foreach($websiteGuest as $w)
					<div class="width-33x website-apps-item">
						<div class="website-apps-item-card" id="website-apps-item-{{$w->id}}" data-keep="1">
							<span class="website-apps-item-img" style="background-image: url(/files/builder/images/website-guest/{{$w->domain}}.jpg?t={{time()}});">
								<span>
								</span>
								<span class="website-apps-item-black"></span>
							</span>
							<span class="website-apps-item-text center">
								<span class="pd-5"><b>{{ $w->title }}</b></span>
								<span class="pd-5 primary-color">
									{{ $w->domain }}
								</span>
							</span>
						</div>
						@if( permission("website_manager") )
							<div class="website-apps-item-option">
								{{-- Cài đặt web --}}
								<a title="Xóa" class="pd-5" href="/khach-hang?delete_website_guest={{ $w->domain }}">
									<i class="fa fa-times"></i>
								</a>
							</div>
						@endif
					</div>
				@endforeach
			</div>
		@endif
		@if( $websiteGuest->total()==0 )
			<div class="pd-10" style="padding: 20px 0;text-align: center; font-size: 20px">Không mẫu website khách hàng nào!</div>
		@endif
		@if( $websiteGuest->total() > $guestLimit )
			<div class="center" style="margin-bottom: 30px">
				<span class="link btn-primary" id="website-template-load-more" data-nextpage="1">Xem thêm <i class="fa fa-arrow-circle-right"></i></span>
			</div>
		@endif
	</div>
	@if( permission("website_manager") )
		<div class="heading-block">Tạo website khách hàng</div>
		<form method="POST" class="form bg" enctype="multipart/form-data">
			<div class="pd-10">
				<input type="text" class="width-100" name="website_guest[domain]" placeholder="Tên miền">
			</div>
			<div class="pd-10">
				<input type="text" class="width-100" name="website_guest[title]" placeholder="Tiêu đề">
			</div>
			<div class="pd-10">
				Ảnh website
				<br>
				<input type="file" class="width-100" name="image" accept=".jpg,jpeg">
			</div>
			<div class="pd-10 center">
				<input type="submit" value="Thêm website khách hàng" class="btn btn-primary">
			</div>
		</form>
	@endif
	</section>
	{!! Widget::show("customer_footer") !!}
@endsection



@section("script")
	{{-- CSS --}}
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
		border-radius: 0;
		overflow: hidden;
		box-shadow: 0 10px 15px -10px rgba(30,45,62,.21), 0 5px 40px -10px rgba(31,44,60,.1);
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
	//Click xem thêm mẫu
	$("#website-template").on("click", "#website-template-load-more", function(){
		refreshWebsiteTemplate({
			append: true,
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
				$("#website-template-load-more").attr("data-nextpage", nextpage + 1)
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
					var contents=$(response).find("#website-template").html();
					if(typeof contents!="undefined"){
						if( config.append ){
							var items = $(response).find("#website-template>.flex");
							if(items.children().length == 0){
								$("#website-template-load-more").hide();
							}
							$("#website-template>.flex").append( items.html() );
						}else{
							$("#website-template").html(contents);
						}
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
</script>
@endsection


@section("footer")
	@parent
@endsection
