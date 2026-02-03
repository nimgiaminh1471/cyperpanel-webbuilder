$(document).ready(function(){
	var _itemHeading='<a class="menu-bg bd block sortable-header"><i class="fa fa-pencil"></i> <span></span> <i title="Xóa" class="fa fa-times item-manager-delete" style="float: right;font-size: 20px"></i></a>';

	//Click thêm mới
	$(".item-manager").on("click", ".item-manager-add", function(){
		var wrap=$(this).parent();
		var max=wrap.children("template").attr("data-max");
		var template=$('<div>'+wrap.children("template").html()+'</div>');
		if(wrap.children("ol").children("li").length>=max){
			alert("Chỉ cho phép tối đa: "+max)
		}else{
			var itemIndex=(new Date()).getTime();
			template.find("input,select,textarea").each(function(){
				var name=$(this).attr("data-name");
				if(typeof name!="undefined"){
					$(this).attr("name", $(this).attr("data-name").replace(/_i_/,itemIndex)+"").removeAttr("data-name");
				}
			});
			var item='<li>';
			item+=_itemHeading;
			item+='<div class="hidden">'+template.html()+'</div>';
			item+='</li>';
			wrap.children("ol").append(item);
			wrap.find(".item-manager-empty").show();
			$(".sortable").sortable();
			jscolor.installByClassName("jscolor");
		}
	});

	//Xóa
	$(".item-manager>ol").on("click", ".item-manager-delete", function(){
		if(confirm("Loại bỏ mục này?")){
			var itemList=$(this).parents("ol");
			if(itemList.children("li").length<2){
				itemList.parent().find(".item-manager-empty").hide();
			}
			$(this).parents("li").remove();
		}
	});

	//Click xóa toàn bộ
	$(".item-manager").on("click", ".item-manager-empty", function(){
		if(confirm("Xóa toàn bộ danh sách?")){
			var itemList=$(this).parent().children("ol");
			itemList.html("");
			$(this).hide();
		}
	});

	//Click hiện nội dung
	$(".item-manager>ol").on("click", "a.block", function(){
		var hideEl=$(this).parent().children(".hidden");
		$(".item-manager>ol").find(".hidden").not(hideEl).slideUp();
		hideEl.slideToggle();
	});

	//Lọc lại danh sách
	$(".item-manager>ol").filter(function(){
		var itemList=$(this).children("li");
		itemList.each(function(){
			var itemHeading=$(_itemHeading);
			itemHeading.children("span").text($(this).attr("data-title"));
			$(this).prepend(itemHeading[0].outerHTML);
		});
		$(this).before('<button type="button" class="btn-info item-manager-add" style="margin: 8px"><i class="fa fa-plus"></i> Thêm</button><button style="margin: 8px" type="button" class="'+(itemList.length>1 ? '' : 'hidden')+' btn-danger item-manager-empty"><i class="fa fa-times"></i> Xóa hết</button>');
	});

	//Nhập tên
	$(".item-manager>ol").on("keyup change", "li .form-section>div:first-child input", function(){
		$(this).parents("li").find("a>span").text( $(this).val() );
	});
});