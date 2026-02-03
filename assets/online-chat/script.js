/*
# Chat online
*/

//Click đóng mở hộp chat
$("#online-chat").on("click", ".heading", function(){
	$(this).parent().toggleClass("online-chat-actived");
	$(this).find("i:last-child").toggleClass("fa-angle-down");
	$("#online-chat>.online-chat-body").slideToggle();
	onlineChatRefresh(true);
});

//Ấn nút bắt đầu trò chuyện
function onlineChatStart(){
	$('#loading').show();
	$.ajax({
		url: "/api/online-chat",
		type: "POST",
		data: $("#online-chat form").serialize(),
		success: function(data){
			var el = $(data).find(".online-chat-msg").html();
			if(typeof el=="undefined"){
				console.log(data);
				alert("Lỗi kết nối, vui lòng ấn lại");
			}else if(el.length==0){
				var phone = $("input[name='online_chat[phone]']").val();
				$(".online-chat-msg").hide();
				$(".online-chat-write").slideDown();
				$(".online-chat-content textarea").val( $('.online-chat-register textarea').val() );
				$(".online-chat-register").html("");
				$(".online-chat-content").after('<div class="alert-success">Hệ thống đã tự tạo tài khoản cho quý khách <br>Tên đăng nhập: <b>'+phone+'</b><br>Mật khẩu: <b>'+phone+'</b><br><a href="/user/profile/password">Ấn vào đây để đổi mật khẩu</a></div>');
				$(".online-chat-content textarea").focus();
				setTimeout(function(){
					onineChatSubmit();
				}, 200);
				setTimeout(function(){
					$.get('/user/sendNotifyToManager'); // Gửi thông báo tài khoản mới tới quản lý
				}, 500);
			}else{
				$(".online-chat-msg").html(el).show();
			}
			$('#loading').hide();
		},
		error: function(e){
			setTimeout(function(){
				onlineChatStart();
			}, 3000);
		}
	});
}
$("#online-chat").on("click", ".online-chat-start", function(){
	onlineChatStart();
});
$("#online-chat input, #online-chat textarea").on("keypress", function(e){
	if( $('.online-chat-start:visible').length == 0 ){
		return;
	}
	if(e.keyCode==13){
		e.preventDefault();
		$('#online-chat .online-chat-start').click();
	}
});

function onineChatSubmit(){
	$.ajax({
		url: "/api/online-chat",
		type: "POST",
		data: $("#online-chat form").serialize(),
		success: function(data){
			var el=$(data).find(".online-chat-msg").html();
			if(typeof el=="undefined"){
				//Lỗi gửi
				console.log(data);
				alert("Lỗi kết nối, vui lòng ấn lại");
			}else if(el.length==0){
				//Gửi thành công
				onlineChatRefresh(true);
				$(".online-chat-content textarea").val("");
			}else{
				//Thông báo lỗi từ server
				$(".online-chat-msg").text(el).show();
			}
		},
		error: function(e){
			setTimeout(function(){
				onineChatSubmit();
			}, 3000);
		}
	});
}

// Ấn nút gửi nội dung
$("#online-chat").on("click", ".online-chat-submit", function(){
	onineChatSubmit();
});

$("#online-chat").on("keypress", ".online-chat-content>textarea", function(e){
	if (e.which == 13 && e.shiftKey){

	}else if( e.keyCode == 13 ){
		onineChatSubmit();
	}
});

//Chuyển cuộc trò chuyện
$("#online-chat").on("click", ".primary-hover", function(){
	$("input[name='onlineChatConversationID']").val( $(this).attr("data-id") );
	$("#online-chat .online-chat-conversation-right").html( '<div class="center">'+$("#loading").html()+'</div>' );
	onlineChatRefresh(true);
});

//Đang nhập nội dung
$("#online-chat").on("click focus", ".online-chat-content textarea", function(){
	$.post("/api/online-chat", {"onlineChatConversationID": $("input[name='onlineChatConversationID']").val(), "onlineChatManager": "typingOn", "onlineChatManagerUsersID": $(this).attr("data-uid")}, function(data){
		onlineChatRefresh(true);
	});
});
$("#online-chat").on("blur", ".online-chat-content textarea", function(){
	$.post("/api/online-chat", {"onlineChatConversationID": $("input[name='onlineChatConversationID']").val(), "onlineChatManager": "typingOff", "onlineChatManagerUsersID": $(this).attr("data-uid")}, function(data){
		onlineChatRefresh(true);
	});
});

//Click quản lý
$("#online-chat").on("click", ".online-chat-manager", function(){
	var action=$(this).attr("data-action");
	var confirmed=true;
	if(action=="delete"){
		var confirmed=confirm("Xóa vĩnh viễn cuộc trò chuyện này?");
	}
	if(confirmed){
		$.ajax({
			url: "/api/online-chat",
			type: "POST",
			data: {"onlineChatConversationID": $("input[name='onlineChatConversationID']").val(), "onlineChatManager": action, "id": $(this).attr("data-id")},
			success: function(data){
				onlineChatRefresh(true);
			},
			error: function(e){
				console.log(e);
				onlineChatRefresh(true);
				alert("Lỗi kết nối, vui lòng thử lại");
			}
		});
	}
});

//Bật-tắt âm
$("#online-chat").on("click", ".online-chat-sound", function(){
	$(this).toggleClass("blue");
});

//Đính kèm tệp tin
$("#online-chat").on("click", ".online-chat-attachment", function(){
	$(this).children("input")[0].click();
});
$("#online-chat").on("change", ".online-chat-attachment>input", function(){
	var formData = new FormData();
	var thisEl=$(this);
	formData.append("online_chat", 1);
	formData.append("onlineChatConversationID", $("input[name='onlineChatConversationID']").val());
	$.each(thisEl[0].files, function(i, file) {
		formData.append(i, file);
	});
	$("#loading").show();
	$.ajax({
		url: "/api/online-chat",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			console.log(data);
			var el=$(data).find(".online-chat-msg").html();
			if(typeof el=="undefined"){
				//Lỗi gửi
				alert("Lỗi kết nối, vui lòng ấn lại");
			}else if(el.length==0){
				//Gửi thành công
				onlineChatRefresh(true);
			}else{
				//Thông báo lỗi từ server
				$(".online-chat-msg").text(el).show();
			}
		},
		error: function(e){
			console.log(e);
			alert("Lỗi kết nối, vui lòng thử lại");
		},
		complete: function(){
			thisEl.val("");
			$("#loading").hide();
		}
	});
});

//Load lại nội dung
function onlineChatRefresh(scrollToBottom){
	if( $("#online-chat-disabled").length > 0 ){
		return;
	}
	var unreadCount=$(".online-chat-unread>sup").text();
	if( $("#online-chat .online-chat-register input").length==0 ){
		var lastUpdated=$("#online-chat .online-chat-last-updated").val();
		var scroll=$("#online-chat .online-chat-conversation-right")[0].scrollTop;
		if( $("#online-chat .online-chat-body").is(":hidden") ){
			var maskAsRead=0;
		}else{
			var maskAsRead=1;
		}
		$.post("/api/online-chat", {onlineChatConversationID: $("input[name='onlineChatConversationID']").val(), onlineChatMaskAsRead: maskAsRead}, function(data){
			var el=$(data).find(".online-chat-conversation");
			if(el.length==0){
				return;
			}
			var newID=$(data).find("input[name='onlineChatConversationID']").val();
			$("input[name='onlineChatConversationID']").val(newID);
			var typing=$(data).find(".online-chat-typing").html();
			$(".online-chat-typing").html(typing);
			var unread=$(data).find(".online-chat-unread");
			$(".online-chat-unread").html( unread.html() );
			var lastUpdatedNew=el.find(".online-chat-last-updated").val();
			if( $(".online-chat-sound").hasClass("blue") ){
				if( unread.children().text()>0 && unreadCount != unread.children().text() || unread.children().text()>0 && lastUpdatedNew>lastUpdated ){
					$.stopSound();
					$.playSound("/assets/online-chat/sound.mp3");
				}
			}
			if(scrollToBottom || typeof lastUpdated=="undefined" || lastUpdatedNew>lastUpdated ){
				$("#online-chat .online-chat-conversation").html( el.html() );
				$("#online-chat .online-chat-conversation-right")[0].scrollTop=$("#online-chat .online-chat-conversation-right")[0].scrollHeight;
			}else{
				$("#online-chat .online-chat-conversation-left").html( el.find(".online-chat-conversation-left").html() );
				$("#online-chat .online-chat-conversation-right")[0].scrollTop=scroll;
			}
		}).done(function(){
			$("#loading").hide();
		});
	}
}

//Trỏ chuột vào phần chat
/*$("#online-chat").on("mouseenter", function(){
	$("body").css({"overflow": "hidden"});
}).on("mouseleave", function(){
	$("body").css({"overflow": ""});
});*/

/*
 * Hiện hợp chat online
 */
function showOnlineChat(){
	$("#online-chat>.heading")[0].click();
	setTimeout(function(){
		$("#online-chat").css({left:"50%"}).animate({"left":"0"}, "slow");
	},50);
}

setInterval(function(){
	onlineChatRefresh(false);
}, 5e3);

//Ẩn khi cuộn xuống cuối trang
/*if( $("#footer").length>0 ){
	$(document).on('scroll', function() {
			if( $(this).scrollTop()+$(window).height() >= $('#footer').position().top ){
				$("#online-chat").slideUp();
			}else{
				$("#online-chat").slideDown();
			}
	});
}*/