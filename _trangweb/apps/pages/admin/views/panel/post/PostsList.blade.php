@php
	use models\PostsCategories;
	use models\Posts;
	//Reset lượt xem mỗi tháng
	global $Schedule;
	$Schedule->m( function(){
		DB::table("posts")->where("id", ">", 0)->update(["count"=>0]);
	} );
	//Lọc dữ liệu
	$filter=$_GET["filter"]??[];
	$filter["status"]=$filter["status"]??"public";
	$where[]=["status","=",$filter["status"]];
	if(isset($_GET["uid"])){
		$where[]=["users_id", "=", $_GET["uid"]];
	}
	foreach($filter as $flr=>$value) {
		switch($flr) {

			//Theo chuyên mục
			case "categories":
				if($value>0){
					$postsItem=PostsCategories::find( PostsCategories::allChildren($value,true) )->posts()->select("posts.id")->get(true);
					if(!empty($postsItem)){
						foreach($postsItem as $p) {
							$postsId[]=$p->id;
						}
					}
					if(empty($postsId)){$postsId=[0];}
					$whereIn[]=["id", $postsId];
				}
			break;

			//Tìm kiếm
			case "find":
				$findBy=$filter["findBy"] ?? "title";
				if($findBy=="date"){
					$findBy="title";
				}
				if($findBy=="users_id" || $findBy=="id"){
					$where[]=[$findBy, "=", $value];
				}else{
					$where[]=[$findBy, "LIKE", "%{$value}%"];
				}
			break;

			//Tìm theo ngày
			case "findDate":
				if(!empty($value["day"])){   $whereDay[]=["created_at",$value["day"]]; }
				if(!empty($value["month"])){ $whereMonth[]=["created_at",$value["month"]]; }
				if(!empty($value["year"])){  $whereYear[]=["created_at",$value["year"]]; }
			break;

		}
	}


	//Thống kê bài viết
	function postsCount($status){
		if(isset($_GET["uid"])){
			return Posts::where("status",$status)->where("users_id", $_GET["uid"])->total();
		}
		return Posts::where("status",$status)->total();
	}

	//Lưu số bài trên 1/trang
	if(isset($filter["limit"])){
		Storage::update("posts_list", ["postsListLimit"=>$filter["limit"]] );
	}

	//Sắp xếp
	$orderBy=[ ["id","DESC"] ];
	if(!empty($filter["orderBy"])){
		$orderBy=[ [$filter["orderBy"],$filter["sort"]] ];
	}

	//Lấy danh sách bài viết
	$posts=Posts::where($where ?? "")
	->whereIn($whereIn ?? "")
	->whereDay($whereDay??"")
	->whereMonth($whereMonth??"")
	->whereYear($whereYear??"")
	->orderBy($orderBy??"")
	->paginate(Storage::posts_list("postsListLimit",10));

@endphp

{{-- Lọc bài viết --}}
<form method="GET" class="posts-list">
	<section class="section">
		<div class="heading">
			<i class="fa fa-list-alt"></i>
			DANH SÁCH BÀI VIẾT
		</div>
		<div class="section-body">
			<span id="postsFilter">
				<select class="posts-filter" name="filter[status]">
					@foreach(["public"=>"Đã đăng", "draft"=>"Bản nháp", "trash"=>"Thùng rác"] as $stt=>$txt)
						<option value="{{$stt}}" {!! ($filter["status"]==$stt ? " selected" : "")!!}>{{$txt}} ({{postsCount($stt)}})</option>
					@endforeach
				</select>
				@php
				$select="";
				foreach(PostsCategories::select("id")->where("parent",0)->get() as $parent){
					$select.=PostsCategories::selectChildren($parent->id,$filter["categories"]??0,true);
				}
				@endphp
				<select class="posts-filter" name="filter[categories]">
					<option value="">Chuyên mục</option>
					{!!$select!!}
				</select>
			</span>
			<span class="float-right" id="postsFind">
				<span class="findDate hidden">
					<select class="posts-filter" name="filter[findDate][day]">
						<option value="">Ngày</option>
						@php
						$i=1;
						for($i;$i<=31;$i++){
							echo '<option value="'.$i.'">'.$i.'</option>';
						}
						@endphp
					</select>
					<select class="posts-filter" name="filter[findDate][month]">
						<option value="">Tháng</option>
						@php
						$i=1;
						for($i;$i<=12;$i++){
							echo '<option value="'.$i.'">'.$i.'</option>';
						}
						@endphp
					</select>
					<input class="posts-filter" size="4" placeholder="Năm" type="text" name="filter[findDate][year]">
				</span>
				<input class="posts-filter" placeholder="Tìm kiếm..." type="text" name="filter[find]">
				<select class="posts-filter" name="filter[findBy]">
					<option value="title">Tìm theo</option>
					@foreach(["title"=>"Tên bài viết","id"=>"ID bài viết","content"=>"Nội dung bài viết","users_id"=>"ID người đăng","date"=>"Ngày đăng"] as $k=>$v)
					<option value="{{$k}}">{{$v}}</option>
					@endforeach
				</select>
			</span>
		</div>
	</section>
{{-- /Lọc bài viết --}}


<div class="pd-5"></div>
<div class="hidden posts-action" style="padding: 15px 0">
	<button type="button" class="btn-danger hidden" id="moveToBinTrash"></button>
	<button type="button" class="btn-danger hidden" id="restoreFromBinTrash"></button>
	<button type="button" class="btn-danger hidden" id="deletePosts"></button>
</div>
<div class="red alert-danger hidden" id="deletePostsStatus"></div>




{{-- Danh sách --}}
<main id="postsList" class="table-responsive">
<table class="table width-100" style="min-width: 900px">
	<tr>
		@php
			$tableHeader=[
				"checkbox"  =>["width"=>"5","txt"=>'<label class="check"><input type="checkbox" name="checkAll"/><s></s></label>'],
				"title"     =>["width"=>"50","txt"=>"Tiêu đề"],
				"categories"  =>["width"=>"20","txt"=>"Chuyên mục"],
				"users_id"   =>["width"=>"10","txt"=>"Tác giả"],
				"created_at"=>["width"=>"10","txt"=>"Đăng lúc"],
				"count"=>["width"=>"15","txt"=>"Lượt xem/tháng"],
			];
		@endphp
		@foreach($tableHeader as $id=>$cog)
			@if( in_array($id, ["checkbox", "categories", "users_id"]) )
				<th class="link">{!!$cog["txt"]!!}</th>
			@else
				<th class="width-{{$cog["width"]}} link postsSort {!!(isset($filter["orderBy"]) && $filter["orderBy"]==$id ? "sortByThis" : "")!!}" data-orderBy="{{$id}}">
				{!!$cog["txt"]!!} <i data-sort="{!!(isset($filter["sort"]) && $filter["sort"]=="ASC" ? "ASC" : "DESC")!!}" class="fa fa-arrow-{!!(isset($filter["sort"]) && $filter["sort"]=="ASC" ? "up" : "down")!!}"></i>
				</th>
			@endif
		@endforeach
	</tr>

@foreach($posts as $p)
	@php
	@endphp
	<tr class="posts-item" id="postsItem-{{$p->id}}">
		<td>
			<label class="check"><input class="postsCheck" type="checkbox" name="postsCheck[]" value="{{$p->id}}" data-id="{{$p->id}}"/><s></s></label>
		</td>
		<td>
			<div style="margin-top: 15px;font-weight: bold">{{$p->title}}</div>
			<span>
				<a target="_blank" href="/{{$p->link}}">Xem</a>
				<a href="/admin/PostEditor?id={{$p->id}}">Chỉnh sửa</a>
				{!! ($filter["status"]=="public" ? "" : '<a data-id="'.$p->id.'" class="deleteOnePosts link red">Xóa</a>') !!}
			</span>
		</td>
		<td>
			@foreach(Posts::find($p->id)->categories as $cate)
				<div># {{$cate->title}}</div>
			@endforeach
		</td>
		<td>
			<a title="{{user("email",$p->users_id)}}" target="_blank" href="/admin/UsersList?user_detail={{user("id",$p->users_id)}}">{{user("name",$p->users_id)}}</a>
		</td>
		<td>
			{{date("H:i:s - d/m/Y", timestamp($p->created_at) )}}
		</td>
		<td>
			{{$p->count}}
		</td>
	</tr>
@endforeach
</table>
{!! ($posts->total()==0 ? '<div class="alert-danger">Không có bài viết nào!</div>' : '') !!}

<div class="menu-bg" style="position: relative;min-height: 40px">
	{!!$posts->links([
		"js"=>false,
		"url"=>"#"
	])!!}
	<div class="pd-5" style="position: absolute; right: 15px;top:50%;transform: translate(0,-50%);">Tổng bài viết: <b>{{$posts->total()}}</b></div>
</div>

</main>
<div class="pd-5"></div>
<select class="posts-filter floatRight" name="filter[limit]">
	@foreach([20,50,100,200,500,1000] as $limit)
		<option value="{{$limit}}" {{(Storage::posts_list("postsListLimit")==$limit ? " selected" : "")}}>{{$limit}} bài/trang</option>
	@endforeach
</select>

</form>
{{-- /Danh sách --}}





{{-- Script --}}
<script type="text/javascript">

	//Lọc bài viết
	$("input.posts-filter").on("keyup", function(){
		postsListRefresh(1);
	});
	$("form").on("change","select.posts-filter", function(){
		if($(this).val()=="date"){
			//Tìm theo ngày tháng
			$(".findDate").show();
		}else{
			if($("[name='filter[findBy]']").val()!="date"){
				$(".findDate").hide();
			}

			postsListRefresh(1);
		}
	});

	//Sắp xếp
	$("#postsList").on("click", ".postsSort", function(){
		$(".sortByThis").removeClass("sortByThis");
		var sort=$(this).children("i");
		if(sort.attr("data-sort")=="DESC"){
			sort.attr("data-sort","ASC");
		}else{
			sort.attr("data-sort","DESC");
		}
		$(this).addClass("sortByThis");
		postsListRefresh(1);
	});



	//Chuyển trang
	$("#postsList").on("click", ".paginate a", function(){
		$(this).text("...");
		postsListRefresh($(this).attr("data-page"));
	});


	//Load lại danh sách
	function postsListRefresh(page){
		$(".posts-list .posts-action").hide();
		var orderBy="";
		if($(".sortByThis").length>0){
			var orderBy="&filter[orderBy]="+$(".sortByThis").attr("data-orderBy")+"&filter[sort]="+$(".sortByThis").children("i").attr("data-sort")+"";
		}

		$("#postsList").html( $("#loading>div").html() );
		$.ajax({
			"url":"",
			"type":"GET",
			"data":""+$(".posts-list .posts-filter").serialize()+"&page="+page+""+orderBy+"",
			success: function(data){
				//console.log(data);
				$("#postsFilter").html( $(data).find("#postsFilter").html() );
				$("#postsList").html( $(data).find("#postsList").html() );
			}
		});
	}

	//Click chọn bài viết
	$("#postsList").on("click","input.postsCheck", function(){
		//ẩn các nút
		$(".posts-list .posts-action>button").hide();
		var status=$("[name='filter[status]']").val();
		switch(status){
			//Đã đăng
			case("public"):
				$("#moveToBinTrash").html('Chuyển vào thùng rác (<i></i> bài viết)').show();
			break;
			//Thùng rác
			case("trash"):
				$("#deletePosts").html('Xóa vĩnh viễn (<i></i> bài viết)').show();
				$("#restoreFromBinTrash").html('Khôi phục (<i></i> bài viết)').show();
			break;
			//Bản nháp
			case("draft"):
				$("#deletePosts").html('Xóa vĩnh viễn (<i></i> bản nháp)').show();
			break;
		}

		var checked=$(".postsCheck:checked").length;
		if(checked>0){
			$(".posts-list .posts-action>button i").text(checked);
			$(".posts-list .posts-action").show();
		}else{
			$(".posts-list .posts-action").hide();
		}
	});
	$("#postsList").on("click","input[name='checkAll']", function(){
		$("input.postsCheck").click();
	});


	//Xóa bài viết
	var totalChecked=0;
	function deletePosts(id){
		var checked=$(".postsCheck:checked");
		if(id.length<1){
			var id=checked.attr("data-id");
		}
		if(id>0){
			$("#deletePostsStatus").html('Đã xóa: <b>'+(totalChecked-checked.length)+'/'+totalChecked+'</b> bài viết<br/><a class="link colorBlue" onclick="location.reload()">Hủy xóa</a>').show();
			$.ajax({
				"url":"",
				"type":"POST",
				"data":{"deletePostsById":id,"checkPostAction":"deletePosts"},
				success: function(){
					$("#postsItem-"+id+"").remove();
				},
				complete: function(){
					setTimeout(function(){ deletePosts(""); },500);
				}
			});

		}else{
			$(".posts-list .posts-action>button").show();
			$("#deletePostsStatus").text("").hide();
			postsListRefresh(1);
		}
	}

	//Click xóa 1 bài viết
	$("#postsList").on("click", ".deleteOnePosts", function(){
		if(confirm("Xóa vĩnh viễn bài viết?")){
			deletePosts($(this).attr("data-id"));
		}
	});

	//Thao tác với bài viết đã chọn
	$(".posts-list .posts-action>button").click(function(){
		var action=$(this).attr("id");

		if(action=="deletePosts"){
			//Xóa vĩnh viễn nhiều bài viết
			if(confirm("Xóa vĩnh viễn các bài viết đã chọn?")){
				$(".posts-list .posts-action>button").hide();
				totalChecked=$(".postsCheck:checked").length;
				deletePosts("");
			}
		}else{
			//Bỏ & khôi phục từ thùng rác
			$.ajax({
				"url":"",
				"type":"POST",
				"data":""+$(".postsCheck:checked").serialize()+"&checkPostAction="+action+"",
				success: function(data){
					if(data.length>1){
						alert(data);
					}
					postsListRefresh(1);
				},
				error: function(){
					alert("Lỗi kết nối, hãy thử lại!");
				}
			});
		}
	});




</script>

{{-- /Script --}}


{{-- CSS --}}
<style type="text/css">
	
@media(max-width: 768px) {/* Width<768 = Mobile*/
#postsFind{float:none;}
.posts-list .posts-action>button,.posts-list .posts-filter{margin-bottom: 5px;width: 99%;display: block}
}
.sortByThis{color: blue;font-weight: bold}
.posts-list .posts-item:hover td{background-color: #DDEEF6}
.posts-list .posts-item td>span{visibility: hidden;display: block}
.posts-list .posts-item:hover td>span{visibility: inherit;}
.posts-list .posts-item td>span a{margin: 5px}
</style>