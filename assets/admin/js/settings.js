$(document).ready(function(){


//Lưu thiết lập
function settingsSave(formPart){
	if(formPart.length<1){
		var formPart=$("#settingsForm .admin-container:visible");
	}
	var formPartId=formPart.attr("data-id");
	var data=formPart.find("input,select,textarea").serialize();
	if(data.length>0){
		var mergeData="&formPart="+formPartId;
		$.ajax({
			url: "",
			type: "POST",
			data: data+"&settingsSave"+encodeURI(mergeData),
			success: function (data) {
				if(data.indexOf("LoKiem") !=-1 && data.indexOf("<head>") ==-1) {
					$("link[data-id='"+formPartId+"']").each(function(){
						this.disabled=true;
					});
					$("#ApplyCSS-"+formPartId).html('<style>'+data+'</style>');
				}else if(data.length>0){
					console.log(data);
				}
				$("#adminSaveStatus").hide();
			},
			error: function(e){
				$("#adminSaveStatus").html('<span class="label-danger"><i class="fa fa-times" aria-hidden="true"></i> Lỗi lưu thiết lập</span>').show();
				setTimeout(function(){
					settingsSave("");
				}, 2e3);
				$("link[data-id='"+formPartId+"']").each(function(){
					this.disabled=false;
				});
				console.log(e);
			}
		});
	}
	
}
var settingsSaveTime=null;
$("#settingsForm, .gallery-manager, #formLoadIcon").on("click change keyup mouseup touchend drag", function(e){
	clearTimeout(settingsSaveTime);
	var timer=e.type=="keyup" ? 1000 : 500;
	settingsSaveTime=setTimeout(function(){
		settingsSave("");
		setTimeout(function(){
			settingsSave("");
		},5e3);
	},timer);
});

//Setup giao diện
$("#adminPanelPendingSetup").filter(function(){
	var thisEl=$(this);
	var formPartList=$("#settingsForm .admin-container");
	thisEl.children("i:last-child").text(formPartList.length);
	var _formPartCount=0;
	formPartList.each(function(i){
		var formPart=$(this);
		(function(i){
			setTimeout(function(){
				settingsSave(formPart);
				_formPartCount++;
				thisEl.children("i:first-child").text(_formPartCount);
				if((i+1)==formPartList.length){
					setTimeout(function(){
						alert("Khởi tạo thành công!");
						location.reload();
					},5e3);
				}
			}, i*5e3);
		})(i);
	});
});

//Tạo element test style
$("#settingsForm .admin-container").each(function(){
	$("body").append('<div id="ApplyCSS-'+$(this).attr("data-id")+'"></div>');
});



//Khôi phục cài đặt
$("#settingsRestore").click(function(e) {
	if(confirm("Khôi phục lại toàn bộ cài đặt?")){
		$("#settingsForm").remove();
		$.ajax({
			url: "",
			type: "POST",
			data: {"settingsRestore":1},
			success: function (data) {
				location.reload();
			},
			error: function() {
				alert("Lỗi kết nối, hãy thử lại");
			}
		});
	}
}); 


});