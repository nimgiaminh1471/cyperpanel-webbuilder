$(document).ready(function(){

	//Click hiện danh sách
	$(".widget").on("click", ".widget-add-btn", function(){
		var wrap=$(this).parent();
		var group=$(this).attr("data-group");
		var all=$(this).attr("data-all");
		wrap.find(".widget-list .panel").attr("data-group", group).attr("data-all", all);
		wrap.find(".widget-list").show();	
	});

	//Click thêm 1 widget
	$(".widget").on("click", ".widget-list .panel", function(){
		$(this).parents(".modal").hide();
		var type=$(this).attr("data-type");
		var group=$(this).attr("data-group");
		var all=$(this).attr("data-all");
		$.ajax({
			"url": "",
			"type": "POST",
			"data": {"_widgetManager": "add", "type": type, "group": group, "allPage": all},
			success: function(data){
				location.reload();
			},
			error: function(error){
				console.log(error);
				alert("Lỗi kết nối, hãy thử lại");
			}
		});
	});

	//Đóng danh sách
	$(".widget").on("click", ".modal-close", function(){
		$(this).parents(".modal").hide();
	});

	//Click nút tác vụ
	$(".widget").on("click", ".widget-item-action", function(){
		var action=$(this).attr("data-action");
		var wrap=$(this).parents(".widget");
		var group=wrap.attr("data-group");
		var thisItem=$(this).parents(".widget-item");
		switch(action){

			//Xóa bỏ
			case "delete":
				if(confirm("Loại bỏ mục này?")){
					$("#loading").show();
					$.ajax({
						"url": "",
						"type": "POST",
						"data": {"_widgetManager": "delete", "id": $(this).attr("data-id"), "location": $(this).attr("data-location"), "group": group},
						success: function(data){
							location.reload();
						},
						error: function(error){
							console.log(error);
							$("#loading").hide();
							alert("Lỗi kết nối, hãy thử lại");
						}
					});
				}
			break;

			//Chỉnh sửa
			case "editor":
				var showEl=$(this).parent().next();
				if(showEl.is(":hidden")){
					$(".widget-toolbar").hide();
					$(".fixed-button").hide();
				}else{
					$(".widget-toolbar").show();
					$(".fixed-button").show();
				}
				$(".widget-manager.hidden").not(showEl).slideUp();
				showEl.slideToggle();
			break;

			//Chuyển lên - xuống
			case "move-up":
			case "move-down":
				if(action=="move-up"){
					var nearEl=thisItem.prev();
				}else{
					var nearEl=thisItem.next();
				}
				if(nearEl.hasClass("widget-item")){
					$.get("", function(data){
						var newItem=$(data).find('.widget-item[data-id="'+thisItem.attr("data-id")+'"]');
						if(typeof newItem!="undefined" && newItem.length>0){
							$("#loading").show();
							if(action=="move-up"){
								nearEl.before(newItem);
							}else{
								nearEl.after(newItem);
							}
							thisItem.remove();
							updateWidget(wrap.find(".widget-item").first(), true);
						}
					});
				}
			break;
		}
	});

	//Ấn nút cập nhật
	$(".widget").on("click", ".widget-manager-save", function(){
		var el=$(this).parents(".widget-item");
		$(".widget-manager").hide();
		$(".widget-toolbar,.fixed-button").show();
		setTimeout(function(){
			updateWidget(el, false);
		},300);
	});

	//Lưu thiết lập
	var _widgetPreview;
	function updateWidget(el, refresh){
		$("link[data-id='widgets-style']").remove();
		var bodyEl=$(el).children(".widget-item-body");
		var id=el.attr("data-id");
		bodyEl.css({"opacity": ".2"});
		clearTimeout(_widgetPreview);
		_widgetPreview=setTimeout(function(){
			$.ajax({
				"url": "",
				"type": "POST",
				"data": "_widgetManager=update&"+el.parents(".widget").find("form").serialize(),
				success: function(data){
					if(refresh){
						location.reload();
					}
					var widgetItem=$(data).find('.widget-item[data-id="'+id+'"]');
					var content=widgetItem.children('.widget-item-body').html();
					if($('<div>'+content+'</div>').find("script").length==0){
						$(el).attr("style", widgetItem.attr("style")).attr("class", widgetItem.attr("class"));
					}else{
						var content='<div class="alert-danger"><a target="_blank" href="">Nội dung chứa mã Javascript, click để xem trước</a></div>';
					}
					bodyEl.html( content );
					bodyEl.css({"opacity": ""});
					panelClickInstall();
					sliderInstall();
				},
				error: function(){
					setTimeout(function(){
						updateWidget(el, refresh)
					}, 3e3);
				}
			});
		}, 500);
	}
	$(".widget").on("click keyup change",".widget-manager", function(){
		var el=$(this).parents(".widget-item");
		updateWidget(el, false);
	});

	//Di chuyển phần thiết lập
	$(".widget").on("mousedown touchstart", ".widget-manager-drag", function(e){
			var thisEl=$(this).parent()[0];
			var pos=positionType(e);
			var oLeft =pos.pageX;
			var oTop  =pos.pageY;
			$("body").on("mousemove touchmove",function(e){
				var pos=positionType(e);
				var nLeft=oLeft-pos.pageX;
				var nTop=oTop-pos.pageY;
				oLeft = pos.pageX;
				oTop = pos.pageY;
				$(".widget").find(".widget-manager").css({"left":(thisEl.offsetLeft-nLeft), "top":(thisEl.offsetTop-nTop), "right": "", "bottom": ""});
			});
	}).on("mouseup touchend",function(){
		$("body").off("mousemove touchmove");
	});

	//Trỏ chuột vào nút sửa widget
	$(".widget").on("mouseenter touchstart", ".widget-toolbar", function(){
		$(this).parents(".widget-item").children(".widget-item-body").css({"background":"skyblue", "opacity": ".5"});
	}).on("mouseleave touchend", ".widget-toolbar", function(){
		$(".widget-item>.widget-item-body").css({"background":"", "opacity": ""});
	});

	//Vị trí event
	function positionType(e){
		if(e.type == 'touchstart' || e.type == 'touchmove' || e.type == 'touchend' || e.type == 'touchcancel'){
			var position = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		}else{
			var position = e;
		}
		return position;
	}

	//Đổi kiểu hiển thị bài viết
	$("select[data-id=\"postsListType\"]").on("change", function(){
		var id=$(this).val();
		$(".widget .posts-list-type").slideUp();
		$(this).parents(".widget-manager").find(".posts-list-type[data-id=\""+id+"\"]").slideDown();
	});
});