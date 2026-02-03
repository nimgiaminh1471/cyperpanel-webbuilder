/*
# Xử lý form
*/
$(document).ready(function(){

	//Xóa file đã chọn
	$("form").on("click", ".form-clear-file-btn", function(e){
		if(confirm("Bỏ file đã chọn")){
			$(this).parent().find(".input").val("");
			$(this).parent().find("img").removeAttr("src");
			$(this).parent().find(".form-files-list").html("");
			$(this).hide();
		}
	});

	//Áp dụng chọn màu
	$("form").on("click", ".jscolor", function(e){
		if(!$(this).hasClass("jscolor-active")){
			jscolor.installByClassName("jscolor");
			alert("Ấn lại lần nữa để chọn màu!");
		}
	});

	//Khôi phục màu mặc định
	$("form").on("click", ".form-color-default", function(e) {
		var el = $(this).parent().children(".jscolor");
		var dColor = $(el).attr("data-default");
		$(el).val(dColor);
		$(el).css({"background-color":"#"+dColor+""});
	}); 


	// Lọc dạng nhập số
	$("form").on("change", ".number-input", function() {
		var max = parseInt($(this).attr("max"));
		var min = parseInt($(this).attr("min"));
		if($(this).val() > max){
			$(this).val(max);
		}else if($(this).val() < min){
			$(this).val(min);
		}       
	}); 


	//Chọn icon
	$("body").append('<div id="formLoadIcon"></div>');
	$("form").on("click", ".form-browser-icon", function(e){
		$(".icon--selecting").removeClass("icon--selecting");
		$(this).parent().children(".hidden").addClass("icon--selecting");
		if( $("#formLoadIcon").is(":empty") ) { 
			$("html").addClass("opacity");
			$.ajax({
				url: "/admin",
				type: "POST",
				dataType: "html",
				data: {"settingsIconSelect":1},
				success: function(data) {
					$("#formLoadIcon").html($(data).filter("#formListIcon"));
					$("html").removeClass("opacity");
				},
				error: function() {
					alert("Lỗi kết nối, hãy thử lại");
					location.reload();
				}
			});
		}
		
		$("#formListIcon").show();
		$("body").css({"overflow":"hidden"});
		$("body").addClass("hidden-sb");		
		//Tìm icon
		$("#formLoadIcon").on("keyup", "#formSearchIcon", function(){
			var text = $(this).val().toLowerCase();
			$(".form-icon-item").hide();
			$('.form-icon-item span:contains("'+text+'")').closest(".form-icon-item").show();
		});

		//Đóng
		$("#formLoadIcon").on("click", ".formIconSelectClose", function(){
			$("#formListIcon").hide();
			$("body").css("overflow","auto");
		});


		$("#formLoadIcon").on("click", ".this_icon", function(){
			var addto = $(".icon--selecting");
			var icon = $(this).attr("icon");
			$(addto).val(icon);
			$("#formListIcon").hide();
			$(addto).parent().find("i").removeClass().addClass("fa "+icon+"");
			$(addto).parent().addClass("blue");
			$("body").css("overflow","auto");
		});  

	});

	//Khôi phục sort
	$(".sortable-reset").click(function(e) {
		var gid = $(this).attr("data-id");
		$.ajax({
			url: "",
			type: "POST",
			data: {"settingsSortReset":gid},
			success: function (data) {
				location.reload();
			},
			error: function() {
				alert("Lỗi kết nối, hãy thử lại");
			}
		});
	});

});