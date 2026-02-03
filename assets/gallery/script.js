$(document).ready(function(){
	var total_file = 0;
	// Click button
	$(".gallery-upload-btn").click(function(){
		$(this).children(".gallery-upload-file")[0].click();
	});

	//Upload File
	$(".gallery-upload-file").on("change", function(e){
		var this_form    = $(this).parents(".gallery-form");
		var input_file   = $(this);
		var this_file    = $(this)[0].files;
		var number_file  = this_file.length;

		function upload_stt(stt,txt,fid){
			this_form.find('#'+fid+'').attr("data-stt",stt).children('.menu').html(txt);
			if(stt!="pending"){ this_form.find('#'+fid+'').hide(); }
			
			var success = this_form.find("[data-stt='success']").length;
			var failed  = "";
			this_form.find("[data-stt='failed']").each(function(){
				failed+='<li class="pd-5">'+$(this).html()+'</li>';
			});
			
			this_form.find('.gallery-upload-list').html('<div class="alert-success">Đã Upload: <b>'+success+'/'+total_file+'</b></div> '+(failed.length>0 ? '<div class="error" style="height:180px;overflow:auto"><ol>'+failed+'</ol></div>' : '')+' ');
		}
		
		
		

		if(number_file>0){
			total_file+=number_file;
			function send_file(fi){
				var i=fi-1;
				var formData = new FormData();
				formData.append("gallery", this_file[i]);
				formData.append("galleryUploadPhotoStamp", $("#galleryUploadPhotoStamp:checked").val());
				formData.append("_galleryAction", "upload");
				formData.append("parent_id", $('.gallery-option[data-option="parent_id"]').val());
				formData.append("parent_column", $('.gallery-option[data-option="parent_column"]').val());
				var fid='file-'+Date.now()+'';
				this_form.find(".gallery-upload-list").before('<div id="'+fid+'" data-stt="pending"><div class="alert-danger">'+this_file[i].name+'</div><div class="menu red"></div></div>');
				upload_stt('pending','<progress value="0" max="100"></progress>',fid);

				if(this_file[i].size>this_form.find(".gallery-max-upload").attr("data-bytes")){
					upload_stt("failed","Tệp tin lớn hơn mức cho phép là "+this_form.find(".gallery-max-upload").attr("data-size")+" ",fid);
				}else{
					$.ajax({
						url: "",
						data: formData,
						processData: false,
						contentType: false,
						type: "POST",
						xhr: function () {
							var xhr = new window.XMLHttpRequest();
							xhr.upload.addEventListener("progress", function (e) {
								if (e.lengthComputable) {
									var stt = e.loaded/e.total;
									stt = parseInt(stt*100);
									this_form.find('#'+fid+' progress').val(stt);
								}
							}, false);
							return xhr;
						},
						complete: function (response) {
							var data=$(response.responseText).find("#galleryUploadMsg").val();
							if(typeof data === 'undefined'){
								upload_stt('failed','Lỗi không xác định',fid);
								console.log(response.responseText);
							}
							var inew=fi-1;
							if(i>0){ send_file(inew); }else{ input_file.val(""); }
						},
						success: function (response){
							var response=$(response).find("#galleryUploadMsg").val();
							if(typeof response !== "undefined"){
								var data=JSON.parse(response);
								if(data.error==0){
									upload_stt("success",data.link,fid);
								}else{
									upload_stt("failed",data.error,fid);
								}
							}
							reload_file_manager();
						},
						error: function (e) {
							upload_stt("failed","Lỗi kết nối",fid);
						},
					});

				}
			}
			send_file(number_file);
		}



	});

	//Click tab
	$(".gallery-tab span").click(function(){
		var show=$(this).attr("data-show");

		if(show=="hidden"){
			$("#galleryWrap").hide();
		}else{
			$(this).parent().find("span").removeClass();
			$(".gallery-tab-body").hide();
			$(this).addClass("gallery-tab-actived");
			$("."+show+"").show();
			reload_file_manager();
		}

	});

	function reload_file_manager(){
		var op=$(".gallery-option");
		var option={page: $(".gallery-manager .paginate-current").text()};
		for(var i=0;i<op.length;i++){
			option[$(op[i]).attr("data-option")]=$(op[i]).val();
		}
		$.post("", option, function(data){
			$("#galleryFilesList").html( $(data).find("#galleryFilesList").html() );
		}).fail(function(){
			setTimeout(function(){
				reload_file_manager();
			}, 2e3);
		});
	}

	// Click file
	$(".gallery-manager").on("click", ".gallery-item", function(){
		var this_file=$(this);
		if($(".gallery-multiple-select").hasClass("gallery-has-selected")){
			this_file.toggleClass("gallery-selected");
			if($(".gallery-selected").length>0){
				$(".gallery-multiple-delete").show().text("Xóa ("+$(".gallery-selected").length+") file");
				$(".gallery-multiple-insert").text("Chèn ("+$(".gallery-selected").length+") file");
			}else{
				$(".gallery-multiple-delete,.gallery-multiple-insert").hide();
			}
		}else{
			$("#loading").show();
			$(".gallery-item").removeClass("gallery-selected");
			this_file.addClass("gallery-selected");
			$.post("", {_galleryAction: "detail", id: this_file.attr("data-id")}, function(data){
				var newData=$(data).find("#galleryDetail").html();
				this_file.before('<div class="gallery-modal gallery-fileActionBox" id=""><div class="gallery-modal-body"> <div class="gallery-tab heading heading-block"> <span> <i class="'+this_file.children('i').attr('class')+'"></i>  #'+this_file.attr('data-id')+'</span> <span class="gallery-fileActionX" style="float:right"><i class="fa fa-times-circle-o"></i></span></div> '+newData+' </div></div>');
				if($(".file--selecting").length>0){
					$(".gallery-insert-file").show();
				}
			}).done(function(){
				$("#loading").hide();
			});
		}

	});





	// Chọn nhiều file
	$(".gallery-manager").on("click",".gallery-multiple-select", function(){
		if($(this).hasClass("gallery-has-selected")){
			reload_file_manager();
		}
		$(this).toggleClass("gallery-has-selected");
		$(this).text("Hủy chọn");
		$(".gallery-multiple-delete, .gallery-check-all, .gallery-multiple-copy").show();
		if($(".file--selecting").length>0){
			$(".gallery-multiple-insert").show();
		}
	});

	// Xóa nhiều file
	$(".gallery-manager").on("click", ".gallery-multiple-delete", function(){
		function delete_file(){
			var div_id = $(".gallery-selected").eq(0);
			var id = div_id.attr("data-id");
			$.ajax({
				data: {_galleryAction: "delete", id: id},
				type: "POST",
				success: function() {
					if($(".gallery-selected").length>0){
						delete_file();
						div_id.remove();
						$(".gallery-multiple-delete").css({"color":"yellow"}).text("Đang xóa: "+$(".gallery-selected").length+" file");
					}else{
						reload_file_manager();
					}
				},
				error: function() {
					delete_file();
				}
			});

		}
		


		if($(".gallery-selected").length>0){
			if(confirm("Xóa vĩnh viễn "+$(".gallery-selected").length+" file")){
				delete_file();
			}
		}

	});

	// Click chọn tất cả
	$(".gallery-manager").on("change", ".gallery-check-all", function(){
		$(".gallery-item").toggleClass("gallery-selected");
		if($(".gallery-selected").length>0){ $(".gallery-multiple-select").show(); }
	});

	// Xóa 1 file
	$(".gallery-manager").on("click",".gallery-delete-file", function(){
		if(confirm("Xóa vĩnh viễn file này")){
			$.post("", {_galleryAction: "delete", id: $(this).attr("data-id")}, function(){
				if($(".gallery-option[data-option='type']").val()=="deleted"){
					$(".gallery-option[data-option='type']").val("");
				}
				reload_file_manager();
			});
		}

	});


	// Copy link
	function list_link(){
		var file_link='<textarea style="width:100%" rows="15">';
		$(".gallery-selected").each(function(){
			
			if($('#gallery-copyLinkTag').prop('checked')){
				file_link+='<a href="'+($('#gallery-copyLinkDomain').prop('checked') ? location.origin : '')+''+$(this).find('.gallery-filePath').text()+'">'+$(this).attr('title')+'</a>\n\n';
			}else{
				file_link+=''+($('#gallery-copyLinkDomain').prop('checked') ? location.origin : '')+''+$(this).find('.gallery-filePath').text()+'\n\n';
			}
			
		});
		file_link+='</textarea>';
		return file_link;

	}

	$(".gallery-manager").on("click",".gallery-multiple-copy", function(){
		$(".gallery-manager").append('<div class="gallery-modal gallery-fileActionBox"><div class="gallery-modal-body"> <div class="gallery-tab heading heading-block"> <span> <i></i>Copy link</span> <span class="gallery-fileActionX" style="float:right"><i class="fa fa-times-circle-o"></i></span></div>  <div id="gallery-boxCopyLink"></div>  <div class="pd-10"><label class="check gallery-copySetting"><input id="gallery-copyLinkTag" type="checkbox" value="1"/><s></s> Cho Vào thẻ a</label><br/><label class="check gallery-copySetting"><input id="gallery-copyLinkDomain" type="checkbox" value="1"/><s></s> Full domain</label></div>   </div></div>');

		$("#gallery-boxCopyLink").html(list_link());
	});
	$(".gallery-manager").on("click",".gallery-copySetting", function(){
		$("#gallery-boxCopyLink").html(list_link());
	});

	// Tải lên file
	$(".gallery-manager").on("click",".gallery-update-file", function(){
		$(this).hide();
		$.post("", {_galleryAction: "update", id: $(this).attr("data-id"), file_name: $(".gallery-file-info-name").val(), file_desc: $(".gallery-file-info-description").val()}, function(d){
			reload_file_manager();
		});
	});

	// Đóng chi tiết file
	$(".gallery-manager").on("click",".gallery-fileActionX", function(){
		$(".gallery-fileActionBox").remove();
	});

	// Thay đổi cài đặt
	$(".gallery-modal-body").on("change keyup", ".gallery-option", function(){
		$(".gallery-manager .paginate-current").text(1);
		if($(".gallery-option[data-option='type']").val()=="deleted"){
			$("#galleryFilesList>section").html('<div class="alert-danger">Đang kiểm tra file bị xóa...</div>');
			var checking = setInterval(function(){
				$.post("", {checking: 1}, function(data){
					var count=$(data).find("#galleryCheckingCount").text();
					if(count=="0"){
						clearInterval(checking);
					}else{
						reload_file_manager();
					}
				});
				
			},2000);
		}else{
			reload_file_manager();
		}
	});

	// <Chuyển trang>
	$(".gallery-manager").on("click", ".paginate a", function(){
		$(".gallery .paginate-current").text($(this).attr("data-page"));
		reload_file_manager();
		return false;
	});

	// Lấy tên file
	function baseName(str){
		var base = new String(str).substring(str.lastIndexOf('/') + 1); 
		return base;
	}

	// Lấy đuôi file
	function fileExt(filename){
		var ext = /^.+\.([^.]+)$/.exec(filename);
		return ext == null ? "" : ext[1];
	}

	// Chọn file
	$("form").on("click", ".form-file-select-custom", function(e){
		$(".file--selecting").removeClass("file--selecting");
		$(this).addClass("file--selecting");
		var parentID=parseInt( $(this).attr("data-parent") );
		var parentColumn=$(this).attr("data-column");
		if(parentID>0){
			$('.gallery-option[data-option="type"]').val("parent");
		}
		$('.gallery-option[data-option="parent_column"]').val(parentColumn);
		$('.gallery-option[data-option="parent_id"]').val(parentID);
		reload_file_manager();
	});

	$("form").on("click", ".form-file-select-btn", function(e){
		$(".file--selecting").removeClass("file--selecting");
		$(this).parent().find(".input").addClass("file--selecting");
		$("#galleryWrap").show();
		var parentID=parseInt( $(this).attr("data-parent") );
		var parentColumn=$(this).attr("data-column");
		if(parentID>0){
			$('.gallery-option[data-option="type"]').val("parent");
		}
		$('.gallery-option[data-option="parent_column"]').val(parentColumn);
		$('.gallery-option[data-option="parent_id"]').val(parentID);
		reload_file_manager();
		$(".gallery-manager").off("click", ".gallery-insert-file,.gallery-multiple-insert");
		$(".gallery-manager").on("click",".gallery-insert-file,.gallery-multiple-insert", function(){
			var file_selected = $(".gallery .gallery-selected");
			var selecting=$(".file--selecting");
			var wrong="";
			if( file_selected.length>selecting.attr("data-max") ){
				var wrong="Chỉ được chọn "+selecting.attr("data-max")+" tệp tin";
			}
			var ext_allow = selecting.attr("data-ext");
			var path="", pathLi="";
			file_selected.find(".gallery-filePath").each(function(i){
				if(i>0){
					path+='|';
				}
				path+=$(this).text();
				pathLi+='<li>'+baseName($(this).text())+'</li>';
				if(ext_allow.indexOf( fileExt($(this).text()) )<0 && ext_allow.length>0){
					wrong="Chỉ cho phép file đuôi: "+ext_allow+" ";
				}
			});

			if(wrong.length>0){
				alert(wrong);
			}else{
				if(selecting.attr("data-max")>1){
					selecting.parent().find(".form-files-list").html(pathLi);
				}else{
					var path=file_selected.find(".gallery-filePath").text()
					if(file_selected.attr("data-type")=="image"){
						selecting.parent().find("img").show().attr("src",path);
					}else{
						selecting.parent().find("img").hide();	
					}
				}
				selecting.val(path);
				selecting.parent().find(".form-clear-file-btn").show();
				$(".file--selecting").removeClass("file--selecting");
				$("#galleryWrap").hide();
				reload_file_manager();
			}
		});
	});

	//Xoay ảnh
	$(".gallery-manager").on("click", ".gallery-rotate-image", function(){
		var thisBtn=$(this);
		thisBtn.hide();
		$.ajax({
			url : "",
			type : "POST",
			data : {_galleryAction: "rotate", id: $(this).attr("data-id")},
			success : function() {
				$(".gallery-image-src").attr("src", $(".gallery-image-src").attr("src")+"?t="+(new Date()));
			},
			error: function(e){
				alert("Lỗi kết nối, vui lòng thử lại");
			},
			complete: function(){
				setTimeout(function(){
					thisBtn.show();
				},5e3);
			}
		});
	});

});
