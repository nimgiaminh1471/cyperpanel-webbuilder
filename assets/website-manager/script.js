$(document).ready(function(){
	
	//Click nút thao tác
	$(".website-action").click(function(){
		var form=$(this).parents(".website-action-form");
		var action=$(this).attr("data-action");
		var thisID=$(this).attr("data-id");
		var thisEl=$(this).parent();
		thisEl.hide();
		form.find(".website-action-msg-"+thisID).html("Đang kết nối, vui lòng chờ...").show();
		$.ajax({
			"url"  : "",
			"data" : "website[action]="+action+"&website[id]="+thisID+"&"+form.serialize(),
			"type" : "POST",
			success: function(response){
				console.log(response);
				var data=$(response).find(".website-action-msg-"+thisID).html();
				if(typeof data=="undefined"){
						location.reload();
				}else{
					form.find(".website-action-msg-"+thisID).html(data).show();
					thisEl.show();
				}
				$("#delete-website-captcha").attr("src", $("#delete-website-captcha").attr("src") + "?" + (new Date) );
			},
			error: function(error){
				alert("Lỗi kết nối, hãy thử lại");
				console.log(error);
				thisEl.show();
				form.find(".website-action-msg-"+thisID).html("Lỗi kết nối, vui lòng ấn lại");
			}
		});
	});

	$('.website-action-form').on("keypress", function (e) {
		var code = e.keyCode || e.which;
		if (code == 13) {
			$(this).find('.website-action').click();
			e.preventDefault();
			return false;
		}
	});
	//Nhập dung lượng để nâng cấp
	$(".website-upgrade-disk").on("keyup change click", function(){
		var outer=$(this).parent();
		var msgEl=outer.next();
		var price=$(this).attr("data-price");
		var size=$(this).val();
		msgEl.children("i").text(""+size+" Mb");
		msgEl.children("span").html("<b>"+number_format(size*price,0)+"</b> ₫/1 năm");
		if(size>0){
			msgEl.show();
		}else{
			msgEl.hide();
		}
	});
	function number_format( number, decimals, dec_point, thousands_sep ) {                    
		var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
		var d = dec_point == undefined ? "," : dec_point;
		var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
		var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
		return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	}

	//Cài đặt SSL
	$(".website-action-ssl input").on("click change", function(){
		var val=$(this).val();
		$(this).parents(".website-action-ssl-outer").find(".hidden").hide();
		$(this).parents(".website-action-ssl").find(".hidden[data-id='"+val+"']").show();
	});

	$(".create-webiste-form").on("click", "button", function(){
		createWebsiteSubmit(this);
	});
	$(".create-webiste-form").on("keyup", "input", function(e){
		if(e.keyCode==13){
			createWebsiteSubmit(this);
		}
	});

	
	$(".modal").on("click", ".modal-click", function(){
		setTimeout(function(){
			$(".modal:visible").find("input[name='domain']").focus();
		}, 200);
	});

	// Chọn các gói để gia hạn
	function changePackage(thisEl){
		$("#loading").show();
		$.post("", $(thisEl).parents("#website-renew").find("select, input").serialize(), function(response){
			console.log(response);
			var el = $(response).find("#website-renew-package").html();
			$("#website-renew-package").html(el);
			$("#loading").hide();
			$("#website-renew").next().hide();
		}).fail(function(err){
			console.log(err);
			setTimeout(function(){
				changePackage(thisEl);
			}, 2000);
			$("#loading").hide();
		});
	}
	$("#website-renew").on("change", "select,input", function(){
		changePackage(this);
	});

	// Click đổi gói
	$("#website-renew .flex>div>div").on("click", function(){
		$("#website-renew").find(".website-renew-package-active").removeClass("website-renew-package-active");
		$(this).addClass("website-renew-package-active");
		$(this).find("input").prop("checked", true).change();
	});

});

/*
 * Tải file backup
 */
function downloadWebsiteBackup(id){
	$('#loading').show();
	$.ajax({
		url: "/api/websiteManager/createBackup",
		type: "POST",
		dataType: "JSON",
		data: {download: true, id: id},
		success: function(res){
			if( typeof res.data == 'undefined' ){
				alert('Lỗi kết nối, vui lòng thử lại');
			}else{
				location.href = res.data;
			}
		},
		error: function(){
			setTimeout(function(){
				downloadWebsiteBackup(id);
			}, 1000);
		},
		complete: function(){
			$('#loading').hide();
		}
	});
}