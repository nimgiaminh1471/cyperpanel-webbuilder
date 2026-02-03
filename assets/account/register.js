function registerSubmit(){
	var wrong="";
	var password=$("input[name='register[password]']").val();
	var passwordConfirm=$("#account-register-password2").val();
	if(password!=passwordConfirm){
		var wrong="Mật khẩu nhập lại không khớp nhau";
	}
	if(wrong.length<1){
		$("#loading").show();
		$.ajax({
			url: "/user/register",
			type: "POST",
			data: $("#account-register-form").serialize()+"&registerSubmit",
			dataType: "json",
			success: function(data){
				if(data["wrong"].length<1){
					alert("Đăng ký tài khoản thành công!");
					location.href = data["redirect"];
				}else{
					$("#account-register-msg").show().html(data["wrong"]);
					$("#account-register-captcha").attr("src", $("#account-register-captcha").attr("src").split("?")[0]+"?t="+(new Date().getTime()) );
					$("input[name='register[captcha]']").val("");
				}
			},
			complete: function(){
				$("#loading").hide();
			},
			error: function(e){
				setTimeout(function(){
					registerSubmit();
				}, 3000);
			}
		});
	}else{
		$("#account-register-msg").show().html(wrong);
	}
}

$("#account-register-submit").click(function(){
	registerSubmit();
});
$("#account-register-form").on("keypress", function(e){
	if(e.keyCode==13){
		e.preventDefault();
		$("#account-register-submit").click();
	}
});

//Đăng nhập
function submit(){
	$("#loading").show();
	$.ajax({
		url: "/user/login",
		type: "POST",
		data: $("form").serialize()+"&loginSubmit",
		dataType: "json",
		success: function(data){
			if(data["success"]==1){
				$("#loginMsg").attr("class", "alert-success")
				location.href=data["redirect"];
			}
			$("#loginMsg").show().html(data["msg"]);
		},
		complete: function(){
			$("#loading").hide();
		},
		error: function(e){
			setTimeout(function(){
				submit();
			}, 3000);
		}
	});
}

$("#loginSubmit").click(function(){
	submit();
});
$(".modal-login-box").on("keypress", function(e){
	if(e.keyCode==13){
		e.preventDefault();
		submit();
	}
});