/*
 * Phân loại
 */
function taskCategory(){
	multipleInsertData();
	$('.modal-task-category').show();
}

/*
 * Lưu phân loại
 */
function taskCategorySubmit(thisEl){
	var form = $(thisEl).parents(".ajax-form");
	form.find(".form-notify").hide();
	$("#loading").show();
	$.ajax({
		"type" : "POST",
		"dataType" : "JSON",
		"url" : "/api/TaskAPI/updateCategory",
		"data" : form.find("input, select, textarea").serialize(),
		success: function(response){
			showNotify('success', "Cập nhật thành công");
			location.reload();
		},
		complete: function(){
			$("#loading").hide();
		},
		error: function(){
			setTimeout(function(){
				taskCategorySubmit(thisEl);
			}, 2e3);
		}
	});
}

/*
 * Ấn tạo phiếu
 */
function createTask(){
	$('input.form-field, textarea.form-field').each(function(){
		var value = $(this).attr('data-value');
		$(this).val( value );
	});
	$('.editor-textarea-clone').html('<p></p>');
	$('select.form-field').children('option[value=""]').prop('selected', true);
	$('.invoice-customer').hide();
	inputLabelInit();
	$('.modal-edit-invoice').show();
}

/*
 * Ấn nút sửa phiếu
 */
function editTask(thisEl){
	var data = JSON.parse( $(thisEl).prev().val() );
	$('input.form-field, textarea.form-field').each(function(){
		var name = $(this).attr('name').match(/^(.+?)\[(.+?)\]/)[2];
		var value = data[name];
		if( value.length == 0 ){
			var value = $(this).attr('data-value');
		}
		$(this).val( value );
		if( name == 'note' ){
			$('.editor-textarea-clone').html(value);
		}
	});
	$('select.form-field').each(function(){
		var name = $(this).attr('name').match(/^(.+?)\[(.+?)\]/)[2];
		$(this).find('option[value="'+data[name]+'"]').prop('selected', true);
	});
	inputLabelInit();
	$('.modal-edit-invoice').show();
}

/*
 * Lưu công việc
 */
function editTaskSubmit(thisEl){
	var form = $(thisEl).parents(".ajax-form");
	form.find(".form-notify").hide();
	$("#loading").show();
	$.ajax({
		"type" : "POST",
		"dataType" : "JSON",
		"url" : "/api/TaskAPI/updateTask",
		"data" : form.find("input, select, textarea").serialize(),
		success: function(response){
			form.find(".form-notify").removeClass("alert-danger alert-success");
			if(typeof response["error"] == "undefined"){
				// Lỗi
				form.find(".form-notify").addClass("alert-danger").html("Đã xảy ra lỗi, vui lòng thử lại").show();
			}else if(response["error"] == null){
				location.reload();
			}else{
				form.find(".form-notify").addClass("alert-danger").html(response["error"]).show();
			}
		},
		complete: function(){
			$("#loading").hide();
		},
		error: function(){
			setTimeout(function(){
				editTaskSubmit(thisEl);
			}, 2e3);
		}
	});
}

/*
 * Ấn nút xem chi tiết phiếu
 */
function taskDetail(thisEl){
	var data = JSON.parse( $(thisEl).prev().val() );
	$('div[data-textid="note"]').html(data.note);
	$('b[data-textid="duration"]').html(data.duration.replace(' ', ' - ') );
	$('select.form-field').each(function(){
		var name = $(this).attr('name').match(/^(.+?)\[(.+?)\]/)[2];
		$(this).find('option[value="'+data[name]+'"]').prop('selected', true);
	});
	$('input.form-field, textarea.form-field').each(function(){
		var name = $(this).attr('name').match(/^(.+?)\[(.+?)\]/)[2];
		var value = data[name];
		if( value.length == 0 ){
			var value = $(this).attr('data-value');
		}
		if( $(this).attr('type') == 'radio' ){
			if( $(this).val() == value ){
				$(this).prop('checked', true);
			}
		}else{
			$(this).val( value );
		}
	});
	inputLabelInit();
	$('.modal-edit-invoice').show();
}
/*
 * Lưu công việc
 */
function updateTaskStatusSubmit(thisEl){
	var form = $(thisEl).parents(".ajax-form");
	$("#loading").show();
	$.ajax({
		"type" : "POST",
		"dataType" : "JSON",
		"url" : "/api/TaskAPI/updateTaskStatus",
		"data" : form.find("input, select, textarea").serialize(),
		success: function(response){
			showNotify('success', "Cập nhật thành công");
			refreshList(1);
			$(".modal-edit-invoice").fadeOut();
		},
		complete: function(){
			$("#loading").hide();
		},
		error: function(){
			setTimeout(function(){
				updateTaskStatusSubmit(thisEl);
			}, 2e3);
		}
	});
}


function refreshList(page){
	var form = $("#task-form");
	$.ajax({
		"url"  : "",
		"data" : form.serialize()+"&page="+page,
		"type" : "GET",
		success: function(response){
			var el = $(response).find("#task-form").html();
			if(typeof el != "undefined"){
				$("#task-form").html(el);
			}
		},
		complete: function(){
			$("#loading").hide();
		},
		error: function(error){
			setTimeout(function(){
				refreshList(page)
			}, 3e3);
		}
	});
}

//$('.gallery-option[data-option="parent_id"]').val("-1");