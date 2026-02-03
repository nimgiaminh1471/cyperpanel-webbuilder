@php
	use models\PostsComments;
	use models\Posts;
	if(isset($_GET["accept"])){
		$status="accept";
	}else{
		$status="pending";
	}
	if(isset($_GET["uid"])){
		$comments=PostsComments::where("status", $status)->where("users_id", $_GET["uid"])->orderBy("created_at", "DESC")->paginate(5);
	}else{
		$comments=PostsComments::where("status", $status)->orderBy("created_at", "DESC")->paginate(5);
	}
	
	if(isset($_POST["commentsManager"])){
		$comment=PostsComments::find($_POST["id"]??0);
		switch($_POST["commentsManager"]){

			//Xóa bình luận
			case "delete":
				if($comment->parent==0){
					PostsComments::where("parent", $_POST["id"])->delete();
				}
				PostsComments::where("id", $_POST["id"])->delete();
			break;

			//Duyệt bình luận
			case "accept":
				PostsComments::where("id", $_POST["id"])->update(["status"=>"accept"]);
			break;

			//Lưu dữ liệu lọc bình luận
			case "filter":
				Storage::update("option", ["commentFilter"=>$_POST["data"]]);
			break;
		}
	}
@endphp
<div class="paginate-ajax tooltip-outer table-responsive" id="comments-outer">
	<section class="section">
		<div class="heading">
			<i class="fa fa-comments-o"></i>
			DANH SÁCH BÌNH LUẬN
		</div>
		<div class="section-body">
			<a class="label-danger" href="/admin/PostsComments{{ (isset($_GET["uid"]) ? '?uid='.$_GET["uid"] : '') }}">Đang chờ duyệt ({{PostsComments::where("status", "pending")->total()}})</a>
			<a class="label-success" href="/admin/PostsComments?accept{{ (isset($_GET["uid"]) ? '&uid='.$_GET["uid"] : '') }}">Đã duyệt ({{PostsComments::where("status", "accept")->total()}})</a>
		</div>
	</section>
	<table class="table table-border width-100">
		<tr>
			<th>Đăng bởi</th>
			<th>Thời gian</th>
			<th>Nội dung</th>
			<th style="width: 100px; text-align: center;">Duyệt</th>
		</tr>
		@foreach($comments as $cmt)
			<tr>
				<td>{!! user("name_color", $cmt->users_id) !!}</td>
				<td>{{ dateText( timestamp($cmt->created_at) ) }}</td>
				<td title="{{Posts::where("id", $cmt->posts_id)->value("title")}}" class="tooltip" style="display: table-cell;"><a class="block" target="_blank" href="/{{Posts::where("id", $cmt->posts_id)->value("link")}}">{!! nl2br($cmt->content) !!}</a></td>
				<td class="center comments-action" data-id="{{$cmt->id}}">
					{!! (isset($_GET["accept"]) ? '' : '<i data-action="accept" title="Duyệt" class="tooltip fa fa-check pd-5 link"></i>') !!}
					<i data-action="delete" title="Xóa" class="tooltip fa fa-trash pd-5 link"></i>
				</td>
			</tr>
		@endforeach
	</table>
	@if($comments->count()==0)
		<div class="alert-danger">Không có bình luận nào chờ duyệt</div>
	@endif
	{!!$comments->links()!!}
</div>
<div class="panel panel-warning">
	<div class="heading link">Chặn theo từ khóa</div>
	<div class="panel-body hidden">
		<textarea class="width-100 comments-filter" rows="10" placeholder="Bình luận chứa từ khóa sẽ tự động bị xóa, ngăn cách bằng xuống dòng">{!!Storage::option("commentFilter")!!}</textarea>
	</div>
</div>
<script type="text/javascript">
	function commentsRefresh(){
		setTimeout(function(){
		$.get("", function(response){
			var data=$(response).find("#comments-outer").html();
			$("#comments-outer").html(data);
			$("#loading").hide();
		});
	},500);
	}

	//Click duyệt & xóa bài
	$("#comments-outer").on("click", ".comments-action>i", function(){
		var thisEl=$(this).parent();
		var action=$(this).attr("data-action");
		$("#loading").show();
		$.post("", {commentsManager: action, id: thisEl.attr("data-id") }, function(){
			commentsRefresh();
		});
	});
	$(".comments-filter").keyup(function(){
		$.post("", {commentsManager: "filter", data: $(this).val() }, function(){
		});
	});

</script>