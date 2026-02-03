//Click hiện thông tin & nút gửi
$(".comments-outer").on("click", ".comments-form, .comments-reply-alert", function(){
	var outer=$(this).parents(".comments-outer");
	if(outer.attr("data-role")==0){
		if(confirm("Bạn có muốn đăng ký tài khoản để bình luận?")){
			location.href="/user/register?continue="+outer.attr("data-url");
		}
	}else{
		$(".comments-content-outer>.comments-submit").slideDown();
	}
});

//Load lại danh sách bình luận
function commentsRefresh(){
	setTimeout(function(){
		$.get("", function(response){
			var data=$(response).find("#comments-list").html();
			$("#comments-list").html(data);
		});
	},3e3);
}

//Click đăng bình luận
$(".comments-outer").on("click", ".comments-submit", function(){
	var wrong=false;
	var outer=$(this).parents("form");
	outer.find(".comments-msg").removeClass("alert-success").addClass("alert-danger")
	outer.find("textarea").each(function(){
		if($(this).val().length<2){
			outer.find(".comments-msg").show().html("Không thể đăng nội dung quá ngắn");
			wrong=true;
		}else{
			$(this).parent().removeClass("input-error");
		}
	});
	if(!wrong){
		$.post("", outer.serializeArray(), function(data){
			var msg=$(data).find(".comments-msg").text();
			if(msg==1){
				if(outer.parents(".comments-outer").attr("data-role")>1){
					var msg="Đăng bình luận thành công";
				}else{
					var msg="Bình luận của bạn sẽ hiển thị sau khi được duyệt";
				}
				outer.find(".comments-msg").removeClass("alert-danger").addClass("alert-success").show().text(msg);
				outer.find("textarea").val("");
				commentsRefresh();
			}else{
				outer.find(".comments-msg").show().text(msg);
			}
		});
	}
});

//Click nút tác vụ
$("#comments-list").on("click", ".comments-action>a", function(){
	var action=$(this).attr("data-action");
	var outer=$(this).closest(".comments-item-outer");
	switch(action){

		//Ấn trả lời bình luận
		case "reply":
			$("#comments-list").find(".comments-reply-form").html("");
			var form=$('<div>'+$(".comments-form")[0].outerHTML+'</div>');
			form.find(".comments-info").removeClass("hidden");
			form.find("input[name='comment[parent]']").attr("value", outer.attr("data-id"));
			form.find("input[name='comment[reply_user]']").attr("value", outer.attr("data-user"));
			if(outer.hasClass("comments-reply-item")){
				//Trả lời bình luận
				var name=outer.find(".comments-reply-name b").text();
				form.find("textarea").text("@"+name+": ");
				var formEl=outer.children(".comments-reply-form");
			}else{
				//Bình luận
				var formEl=outer.find(".comments-reply-outer").children(".comments-reply-form");
				setTimeout(function(){
					$("#comments-list").find(".comments-reply-outer").not( outer.find(".comments-reply-outer") ).slideUp();
					outer.find(".comments-reply-outer").slideToggle();
				}, 50);
			}
			if(outer.parents(".comments-outer").attr("data-role")>0){
				form.children().removeClass("pd-10").addClass("form-mrg");
				formEl.html(form.html());
				formEl.find(".comments-msg").hide();
				formEl.find(".comments-submit").show();
				var formTextarea=formEl.find("textarea");
				formTextarea.focus();
				formTextarea[0].setSelectionRange(formTextarea.val().length,formTextarea.val().length);
			}
		break;

		//Xóa bình luận
		case "delete":
			if(confirm("Xóa bình luận ?")){
				$.post("", {deleteComment: $(this).attr("data-id")}, function(){
					outer.remove();
				});
			}
		break;

	}
});

//Click xem thêm bình luận
$(".comments-outer").on("click", ".comments-reply-more", function(){
	var thisEl=$(this);
	var thisID=$(this).attr("data-id");
	var thisOffset=parseInt( $(this).attr("data-offset") );
	thisEl.attr("data-offset", thisOffset+1);
	$("#loading").show();
	$.get("", {page: $(this).attr("data-page"), replyPage: (thisOffset+1)}, function(response){
		var data=$(response).find(".comments-reply-list-"+thisID);
		if(data.text().trim().length>5){
			$(".comments-reply-list-"+thisID).append(data.html());
		}else{
			thisEl.remove();
		}
	}).done(function(){
		$("#loading").hide();
	});
});