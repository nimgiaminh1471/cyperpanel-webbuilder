$(".sortable-parent").sortable();

//Tạo lại ID
function refreshNavbarID(parent){
	if(parent){
		$(".sortable-parent").sortable();
	}else{
		$(".sortable-parent").sortable("destroy");
		$(".sortable-children").sortable();
	}
	$(".navbar-manager>.navbar-item").each(function(parent){
		var type = $(this).parents(".navbar-manager").attr("data-type");
		$(this).children().children().children("input,select,textarea").each(function(sub){
			var name=$(this).attr("data-id");
			var name="storage[option]["+type+"]["+parent+"]["+name+"]";
			$(this).attr("name", name);
		});
		$(this).find(".navbar-sub>.navbar-item").each(function(sub){
			$(this).find("input,select,textarea").each(function(){
				var name=$(this).attr("data-id");
				var name="storage[option]["+type+"]["+parent+"][sub]["+sub+"]["+name+"]";
				$(this).attr("name", name);
			});
		});
		$(this).find(".navbar-sub>input").attr("name", "storage[option]["+type+"]["+parent+"][sub]");
	});
}

//Click thao tác
$(".navbar-manager").on("click", ".navbar-action", function(){
	var action=$(this).attr("data-action");
	switch(action){

		//Xóa
		case "delete":
			var itemEl=$(this).parent().parent();
			var itemParent=itemEl.parent();
			itemEl.remove();
			if(itemParent.hasClass("navbar-manager")){
				refreshNavbarID(true);
			}else{
				refreshNavbarID(false);
			}
		break;
	}
	panelClickInstall();
});
$(".navbar-manager").on("keyup change", "input[data-id=\"title\"]", function(){
	$(this).parent().parent().prev().children("span").text( $(this).val() );
});

//Click mở panel
$(".navbar-manager").on("click", ".panel>.link", function(){
	var thisEL=$(this);
	setTimeout(function(){
		if(thisEL.parent().parent().hasClass("navbar-manager") && thisEL.next().is(":visible") ){
			$(".sortable-parent").sortable("destroy");
			$(".sortable-children").sortable();
		}else if(thisEL.parent().parent().hasClass("navbar-manager")){
			$(".sortable-parent").sortable();
		}
	}, 500);
});

//Thêm link mới
$(".navbar-left").on("click", ".navbar-add", function(){
	var data=JSON.parse( $(this).children(".hidden").text() );
	var template=$("<div>"+$(".navbar-manager>.navbar-template")[0].outerHTML+"</div>");
	template.children().removeClass("hidden navbar-template").addClass("navbar-item");
	Object.keys(data).forEach(function(key){
		if(typeof data[key]!=undefined){
			template.find("[data-id=\""+key+"\"]").attr("value", data[key]);
			if(data["title"].length>0){
				template.find(".heading.link>span").text(data["title"]);
			}
		}
	});
	var subEl=$(".navbar-manager").find(".panel-body:visible");
	if(subEl.length>0){
		template.find(".navbar-sub").remove();
		subEl.children(".navbar-sub").append(template.html());
		refreshNavbarID(false);
	}else{
		$(this).parents(".navbar-left").next().find(".navbar-manager").append(template.html());
		refreshNavbarID(true);
	}
	panelClickInstall();
	if(data.title.length>0){
		$(this).css({"color": "tomato"});
	}
});

//Tìm bài viết
$(".navbar-left").on("keyup change", ".navbar-find-posts", function(){
	var thisEl=$(this);
	$.post("", {"findPosts": $(this).val()}, function(data){
		var find='.navbar-left .panel-body[data-id="posts"]>div';
		var filter=$(data).find(find).html();
		if(filter.length>0){
			thisEl.next().html(filter);
		}
	});
});

//Thêm màu nếu link đã tồn tại
function addColorIfExists(){
	$(".navbar-left .navbar-add").each(function(){
		if( $(this).attr("data-link").length>0 && $('.navbar-manager input[value="'+$(this).attr("data-link")+'"]').length>0 ){
			$(this).css({"color": "tomato"});
		}
	});
}
addColorIfExists();