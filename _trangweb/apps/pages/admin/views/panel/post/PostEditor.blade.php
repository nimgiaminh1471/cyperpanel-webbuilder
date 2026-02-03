@php
	use models\Posts;
	use models\PostsCategories;
	use models\Users;
	function getPost($column){
		$data=Posts::find(GET("id"));
		return $data->$column ?? "";
	}
	function postStorage($id, $default=""){
		$id=str_replace(["[", "]"], "", $id);
		return Posts::get(GET("id"))->storage[$id] ?? $default;
	}
	if(isset($_GET["id"]) && empty(getPost("id"))){
		redirect("/admin", true);
	}
	if(!empty(getPost("title"))){
		echo '<script>document.title="Sửa bài viết: '.getPost("title").'";</script>';
	}


	//Nội dung bên phải
	$rightForm["Thiết lập bài viết"]=[
		["type"=>"text", "name"=>"name", "title"=>"Tiêu đề ngắn", "note"=>"", "attr"=>''],
		["type"=>"color", "name"=>"linkColor", "title"=>"Màu link bài viết", "default"=>"", "required"=>false],
		["type"=>"switch", "name"=>"commentsEnable", "title"=>"Cho phép bình luận", "value"=>1],
		["type"=>"switch", "name"=>"post[pin]", "title"=>"Ưu tiên hiển thị", "value"=>getPost("pin")],
		["type"=>"image", "name"=>"posterPath", "title"=>"Ảnh đại diện", "value"=>"", "ext"=>"jpg,png,jpeg,gif", "post"=>GET("id", 0), "column"=>"posts_id"],
		["type"=>"image", "name"=>"background", "title"=>"Ảnh nền bài viết", "value"=>"", "ext"=>"jpg,png,jpeg,gif", "post"=>GET("id", 0), "column"=>"posts_id"],
	];

	//Nội dung bên dưới
	$bottomForm["Quảng cáo popup"]=[
		["html"=>
			Form::itemManager([
				"data"=>postStorage("ads_popup"),
				"name"=>"postStorage[ads_popup]",
				"sortable"=>true,
				"max"=>5,
				"form"=>[
					["html"=>'<div class="alert-info">Lượt hiển thị: <b>[%s1]</b></div>', "name"=>"views", "value"=>"Chưa thống kê"],
					["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "value"=>""],
					["type"=>"file", "name"=>"src", "title"=>"Chọn file", "value"=>"", "ext"=>"jpg,png,gif,jpeg,mp4", "post"=>GET("id", 0), "column"=>"posts_id"],
					["type"=>"number", "name"=>"timer", "title"=>"Hiện sau (s) giây", "value"=>"5", "min"=>0, "max"=>"999"],
					["type"=>"number", "name"=>"countdown", "title"=>"Cho phép đóng sau (s) giây", "value"=>"5", "min"=>0, "max"=>"999"],
					["type"=>"text", "name"=>"link", "title"=>"Liên kết", "value"=>""],
					["type"=>"textarea", "name"=>"html", "title"=>"Thẻ iframe Youtube hoặc mã HTML", "value"=>"", "rows"=>7],	
				]
			])
		]
	];


	//Tạo vùng tùy chọn
	function postDataSection($option){
		$out='';
		foreach($option as $title=>$form){
			$out.='<div class="panel panel-default"><div class="heading link">'.$title.'</div><div class="panel-body hidden">';
			$out.=Form::create([
				"form"=>$form,
				"function"=>"postStorage",
				"name"=>"postStorage",
				"prefix"=>"",
				"class"=>"menu",
				"hover"=>false
			]);
		}
		$out.='</div></div>';
		return $out;
	}

	Assets::footer("/assets/html-editor/style.css", "/assets/html-editor/script.js", "/assets/timeline/style__complete.css");
		
@endphp

<form class="form" action="" method="POST" id="postForm">
	{{-- Left --}}
	<main class="flex flex-large">
		<section class="width-70 flex-margin">
			<div class="hidden form-mrg" id="postNotification"></div>
			<input type="hidden" name="post[id]" value="{!!getPost("id")!!}" />
			<input type="hidden" name="post[gallery_type]" value="posts_id" />
			<div><input type="text" name="post[title]" class="width-100" placeholder="Tiêu đề" value="{!!getPost("title")!!}" /></div>
			<div class="flex flex-medium">
				<div class="width-30"><input type="text" class="width-100 rm-radius" placeholder="{{DOMAIN}}/" disabled="disabled" /></div>
				<div class="width-70"><input type="text" name="post[link]" class="width-100 rm-radius" placeholder="Liên kết" value="{!!getPost("link")!!}" /></div>
			</div>
			<div class="clearfix"></div>
			<div class="form-mrg">
				<textarea name="post[content]" class="editor-textarea hidden">{!!getPost("content")!!}</textarea>
			</div>
			{!!postDataSection($bottomForm)!!}
		</section>


		{{-- Right --}}
		<section class="width-30 flex-margin">
			<input class="btn-primary width-100 post-submit" data-action="save" type="submit" value="Cập nhật bài viết" />
			<input class="btn-danger width-100 post-submit" data-action="delete" type="submit" value="{{getPost("status")=="trash" ? "Khôi phục bài viết" : "Bỏ vào thùng rác"}}"/>
			<div class="panel panel-default">
				<div class="link heading"><span>Chuyên mục</span></div>
				<div class="panel-body hidden posts-parent" style="height:320px;overflow: auto">
					@php
						//Chuyên mục của bài viết
						foreach(Posts::find( getPost("id") )->categories()->select("posts_categories.id")->get(true) as $cate){
							$cateId[]=$cate->id;
						}
					@endphp
					@foreach(PostsCategories::select("id")->where("parent",0)->get() as $parent)
						{!!PostsCategories::checkboxChildren($parent->id, "post[parents][]", $cateId??[0], "post[parent]", getPost("parent") )!!}
					@endforeach
				</div>
			</div>
			@if( getPost("id")>0 )
				<div class="panel panel-default">
					<div class="link heading">Thông tin bài viết</div>
					<div class="panel-body hidden" style="padding: 2px 0 0 0;">
						<table class="table width-100">
							<tr>
								<td>Trạng thái: </td>
								<td>
									@switch(getPost("status"))
										@case("public")
											<span class="label-success">Công khai</span>
										@break
										@case("trash")
											<span class="label-danger">Trong thùng rác</span>
										@break
										@case("draft")
											<span class="label-warning">Bản nháp</span>
										@break
									@endswitch
								</td>
							</tr>
							<tr><td>Đăng bởi:</td> <td><a target="_blank" href="/admin/UsersList?id={{getPost("users_id")}}">{{ Users::find( getPost("users_id") )->name }}</a></td></tr>
							<tr><td>Đăng lúc:</td> <td>{{date("H:i - d/m/Y", timestamp(getPost("created_at")) )}}</td></tr>
							<tr><td>Sửa lần cuối:</td> <td> {{date("H:i - d/m/Y", timestamp(getPost("updated_at")) )}}</td></tr>
							<tr><td>Sửa bởi:</td> <td><a target="_blank" href="/admin/UsersList?id={{postStorage("last_editor", 1)}}">{{ Users::find( postStorage("last_editor", 1) )->name }}</a></td>
							</tr>
						</table>
					</div>
				</div>
			@endif
			{!!postDataSection($rightForm)!!}
		</section>
	</main>
</form>






<script>
	//Tạo link bài viết
	$("input[name='post[title]']").on("change", function(){
		if($("textarea[name='post[content]']").val()<10){
			$.ajax({
				"url":"",
				"type":"POST",
				"dataType":"json",
				"data": {"postCreateLink":$(this).val()},
				success: function(info){
					$("input[name='post[link]']").val(info["link"]);
					autoSave();
				}
			});
		}
	});

	//Click đăng bài viết
	$(".post-submit").click(function(e){
		e.preventDefault();
		var action=$(this).attr("data-action");
		$.ajax({
			"url":"",
			"type":"POST",
			"dataType":"json",
			"data": $("#postForm").serialize()+"&postSubmit="+action,
			success: function(info){
				if(info["error"].length>0){
					$("#postNotification").show().html('<div class="alert-danger">'+info["error"]+'</div>');
					$("html").animate({ scrollTop: $("html").offset().top }, 0);
				}else{
					location.href=info["link"];
				}
			},
			error: function(e){
				console.log(e);
				alert("Lỗi kết nối, hãy thử lại");
			}

		});
	});

	//Tự động lưu bài viết
	function autoSave(){
		$.ajax({
			"url":"",
			"type":"POST",
			"dataType":"json",
			"data": $("#postForm").serialize()+"&postSubmit=autoSave",
			success: function(data){
				if($("input[name='post[id]']").val().length==0 && typeof data["id"]=="number"){
					location.href="/admin/PostEditor?id="+data.id;
				}
				var d=new Date;
				$("#postForm .editor-info>span").text("Tự động lưu: "+d.getHours()+":"+d.getMinutes()+":"+("0"+d.getSeconds()).slice(-2));
			},
			error: function(e){
				console.log(e);
				setTimeout(function(){
					autoSave();
				},2e3);
			}
		});
	}
	var setAutoSave=null;
	$("#postForm").on("keyup change click", function(e){
		var time=e.type=="keyup" ? 2e3 : 1e4;
		clearTimeout(setAutoSave);
		setAutoSave=setTimeout(function(){
			autoSave();
		},time);
	});

	//Click chọn chuyên mục
	$(".posts-parent").on("click", ".checkbox", function(){
		var parentEl=$(this).parent();
		$(".posts-parent").find(".radio>input").prop("checked", false);
		parentEl.find(".radio").hide();
		parentEl.find(".check").each(function(){
			if($(this).children("input").is(":checked")){
				$(this).next().show();
			}
		});
		if(parentEl.find(".checkbox>input").is(":checked")){
			parentEl.find(".radio>input").prop("checked", true);
		}
	});
</script>