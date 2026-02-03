{{-- Quản lý chuyên mục --}}
@php
use models\PostsCategories;
use models\Post;
@endphp


<style>
	.categoriesItem{
		margin: 0;
		position: relative;
		user-select: none
	}
	.categoriesItem p a:hover,
	.categories-active{
		background-color: {{Storage::setting("theme__list_active", "#DBEFF4")}} !important
	}
	.categoriesItem p{
		display: none;
		list-style-type: none;
		position: absolute;
		z-index: 100;
		box-shadow: 0 2px 8px 0 rgba(0,0,0,.2);
	}
	.categoriesItem p a{
		padding:7px;
		width:140px;
		display: block;
		background: white;
		user-select: none
	}

</style>
<div id="categoriesWrap">
@php
//Danh sách chuyên mục
function categoriesList($gid,$first=true){
	if($first){
		$children=PostsCategories::multilevelChildren($gid, true);
	}else{
		$children=$gid;
	}

	foreach($children as $id => $child){
		$cate=PostsCategories::get($id);
		echo '
		<div class="link categoriesItem menu bd-bottom" style="margin-left:'.(count(PostsCategories::grandparents($id))*3).'0px">
			<span><i class="fa '.($cate->storage["icon"]??"").'"></i> '.$cate->title.'</span>
			<p>
				<a href="/posts-categories/'.$cate->link.'" target="_blank" class="fa fa-eye"> Xem</a>
				<a data-parent="'.$id.'" data-name="'.$cate->title.'" data-type="addChild" class="addCategories fa fa-plus-circle link"> Thêm mục con</a>
				<a data-id="'.$id.'" data-type="edit" class="editCategories fa fa-edit link"> Chỉnh sửa</a>
				<a data-id="'.$id.'" class="deleteCategories fa fa-trash link"> Xóa</a>
			</p>
			<div class="hidden">
				<div class="heading"><i class="fa '.($cate->storage["icon"]??"").'"></i> '.$cate->title.'</div>
				<div class="panel-body">
					<div class="menu bd-bottom">'.$cate->storage["description"].'</div>
					<div class="menu bd-bottom">Chuyên mục con: <b>'.count( PostsCategories::multilevelChildren($id) ).'</b></div>
					<div class="menu bd-bottom">Bài viết: <b>'.PostsCategories::find( PostsCategories::allChildren($cate->id, true) )->posts()->total().'</b></div>
					<div class="menu bd-bottom">Ngày tạo: <b>'.date("d/m/Y", timestamp($cate->created_at)).'</b></div>
				</div>
			</div>
		</div>
		';
		if(!empty($child)){ categoriesList($child,false); }
	}
}
pages\admin\controllers\CategoriesManager::updateRelationship();
@endphp
<section class="section">
	<div class="heading">
		<i class="fa fa-server"></i>
		QUẢN LÝ CHUYÊN MỤC
	</div>
</section>
<main class="flex flex-large">
	<section class="width-60 flex-margin">
		<div id="categoriesList" style="max-height: 450px;overflow: auto" class="bg">
			<a data-type="add" class="addCategories btn-primary" style="margin: 10px">
				<i class="fa fa-plus-circle"></i>
				Thêm chuyên mục
			</a>
			@foreach(PostsCategories::select("id")->where("parent",0)->get() as $parent)
				{!!categoriesList($parent->id)!!}
			@endforeach
		</div>
		<div style="height: 100px"></div>
	</section>
	<section class="width-40 flex-margin">
		<div id="categoriesInfo" class="panel panel-info"></div>
	</section>
</main>

@php
	//Form mẫu
	$cat=PostsCategories::get( POST("id") );
	$categoriesParent='<input type="hidden" name="categories[parent]" value="0">';
	if(isset($cat->id)){
		$categoriesParent='
		<select class="width-100" name="categories[parent]">
			<option value="0">Mục cha</option>
		';
		foreach(PostsCategories::select("id")->where("parent",0)->get() as $parent){
			$categoriesParent.=PostsCategories::selectChildren($parent->id, $cat->parent, false, $cat->id);
		}
		$categoriesParent.='</select>';
	}
	echo '
	<template class="hidden categories-form-template">
		<div class="alert-info hidden" style="margin-bottom: 5px"></div>
		<div class="pd-5">
			'.$categoriesParent.'
			'.Form::create([
				"form"=>[
					["type"=>"hidden", "name"=>"id", "value"=>($cat->id??"")],
					["type"=>"icon", "name"=>"categories[storage][icon]", "title"=>"Biểu tượng", "value"=>($cat->storage["icon"]??'')],
					["type"=>"text", "name"=>"title", "title"=>"", "note"=>"Tên chuyên mục", "value"=>($cat->title??''), "attr"=>''],
					["type"=>"textarea", "name"=>"categories[storage][description]", "title"=>"", "note"=>"Mô tả chuyên mục", "value"=>($cat->storage["description"]??''), "attr"=>'', "full"=>true],
				],
				"function"=>"",
				"name"=>"categories",
				"prefix"=>"",
				"class"=>"",
				"hover"=>false
			]).'
			<div class="center"><input class="btn-primary categories-submit-btn" value="Lưu lại" type="submit" /></div>
		</div>
	</template>
	';
@endphp

{{-- Thêm chuyên mục --}}
{!!modal('categoriesForm', 'Thêm chuyên mục', '
	<form id="categoriesForm" class="form"></form>
','450px', false, true)!!}


</div>


<script type="text/javascript">
	//Click thanh tác vụ
	$("#categoriesList").on("click", ".categoriesItem", function(e){
		$(".categories-active").removeClass("categories-active");
		$(this).addClass("categories-active");
		$(".categoriesItem>p").not($(this).children("p")).hide();
		$("#categoriesInfo").html($(this).children(".hidden").html());
		var thisSub=$(this).children("p");
		thisSub.css({"position": "fixed", "top": e.pageY, "left": e.pageX});
		thisSub.toggle();

	});
	if($("#categoriesList>.categoriesItem").length>0){
		$("#categoriesList>.categoriesItem")[0].click();
		$("#categoriesList>.categoriesItem")[0].click();
	}
	//Click thêm & sửa chuyên mục
	$("#categoriesWrap").on("click", ".editCategories, .addCategories", function(){
		$(".modal-categoriesForm").show();
		var form=$("#categoriesForm");
		form.html('<div class="alert-danger">Đang tải...</div>');
		var template=$(".categories-form-template").html();
		switch( $(this).attr("data-type") ){

			//Click thêm mới
			case "add":
				form.html(template);
			break;

			//Click thêm mục con
			case "addChild":
				form.html(template);
				console.log($(this).attr("data-parent"));
				form.find("[name='categories[parent]']").val($(this).attr("data-parent"));
				form.find(".alert-info").html("Mục cha: <b>"+$(this).attr("data-name")+"</b>").show();
			break;

			//Sửa chuyên mục
			case "edit":
				$.ajax({
					"url":"",
					"type":"POST",
					"data": {"id":$(this).attr("data-id")},
					success: function(data){
						form.html($(data).find(".categories-form-template").html());
					},
					error: function(e){
						alert("Lỗi kết nối, hãy thử lại");
					}

				});
			break;
		}
		
	});

	//Lưu chuyên mục
	$("#categoriesForm").on("click", ".categories-submit-btn", function(e){
		e.preventDefault();
		$.ajax({
			"url":"",
			"type":"POST",
			"dataType":"json",
			"data": $("form").serialize()+"&categoriesUpdateSubmit",
			success: function(info){
				if(info["error"].length>0){
					alert(info["error"]);
				}else{
					refreshCategories();
					$(".modal-categoriesForm").hide();
				}
			},
			error: function(e){
				alert("Lỗi kết nối, hãy thử lại");
				console.log(e);
			}

		});
	});


	//Load lại danh sách chuyên mục
	function refreshCategories(){
		$.get("", function(data){
			$("#categoriesList").html( $(data).find("#categoriesList").html() );
		});
	}

	//Xóa chuyên mục
	$("#categoriesList").on("click",".deleteCategories", function(){
		if(confirm("Xóa vĩnh viễn chuyên mục?")){
			$.ajax({
			"url":"",
			"type":"POST",
			"dataType":"json",
			"data": {"deleteCategories":1,"id":$(this).attr("data-id")},
			success: function(info){
				if(info["error"].length>0){
					alert(info["error"]);
				}else{
					alert("Đã xóa chuyên mục");
					refreshCategories();
				}
			},
			error: function(){
				alert("Lỗi kết nối, hãy thử lại");
			}

		});
		}
	});
	

	

</script>