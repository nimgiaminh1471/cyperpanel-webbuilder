function refreshList(page){
	var form = $("#cash_flow-form");
	$.ajax({
		"url"  : "",
		"data" : form.serialize()+"&page="+page,
		"type" : "GET",
		success: function(response){
			var el = $(response).find("#cash_flow-body").html();
			if(typeof el != "undefined"){
				form.find("#cash_flow-body").html(el);
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
// Click quản lý đơn nạp tiền
$("#cash_flow-body").on("click", ".cash_flow-manager>span", function(){
	var action = $(this).attr("data-action");
	if( typeof action == 'undefined' ){
		return;
	}
	var id = $(this).parent().attr("data-id");
	if( confirm( "Xác nhận: "+$(this).attr("title") ) ){
		$("#loading").show();
		$.ajax({
			url  : "/api/CashFlowAPI/acceptInvoice",
			data : {id: id, action: action},
			type : 'POST',
			dataType: 'JSON',
			success: function(data){
				if( data.error.length == 0 ){
					refreshList(1);
					var msg = data.msg;
					$("#cash_flow-manager-msg").html(msg).show();
				}
			},
			complete: function(){
				$("#loading").hide();
			},
			error: function(error){
				alert("Lỗi kết nối, vui lòng ấn thử lại!");
			}
		});
	}
});
// Click chuyển trang
$("#cash_flow-body").on("click", ".paginate>a", function(){
	$("#loading").show();
	refreshList( $(this).attr("data-page") );
	return false;
});
// Lọc
$("#cash_flow-form").on("keyup change", "input, select", function(){
	refreshList(1);
});
$("#cash_flow-form").on("click", ".form-date-day td", function(){
	refreshList(1);
});

/*
 * Ấn tạo phiếu
 */
function cashFlowcreateInvoice(){
	$('input.form-field, textarea.form-field').val('');
	$('select.form-field').children('option[value=""]').prop('selected', true);
	$('.invoice-customer').hide();
	inputLabelInit();
	$('.modal-edit-invoice').show();
}

/*
 * Ấn nút sửa phiếu
 */
function CashFlowEditInvoice(thisEl){
	var data = JSON.parse( $(thisEl).prev().html() );
	$('input.form-field, textarea.form-field').each(function(){
		var name = $(this).attr('name').match(/^(.+?)\[(.+?)\]/)[2];
		$(this).val( data[name] );
	});
	$('select.form-field').each(function(){
		var name = $(this).attr('name').match(/^(.+?)\[(.+?)\]/)[2];
		$(this).children('option[value="'+data[name]+'"]').prop('selected', true);
	});
	if( data['customer'].length > 0 ){
		$('.invoice-customer').show();
	}else{
		$('.invoice-customer').hide();
	}
	inputLabelInit();
	$('.modal-edit-invoice').show();
}

/*
 * Chọn khách hàng
 */
function searchStylistInit(){
    $('#search-customer').devbridgeAutocomplete({
        minChars: 0,
        lookup: function (query, done) {
        	$.ajax({
		        url: '/api/CashFlowAPI/getCustomer',
		        data: {keyword: query},
		        type: 'POST',
		        dataType: 'json',
		        success: function (data) {
		        	var listItems = data;
                    var result = {}
                    result.suggestions = [];
                    $.each(listItems, function(i, item){
                    	if( $('#stylist-list tr[data-id="'+item.id+'"]').length == 0 && item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 ){
                    		var itemData = item;
                    		itemData.value = item.name+'|'+item.phone;
                    		result.suggestions.push(itemData);
                    	}
                    });
                done(result);
            },
            error: function(){
		            setTimeout(function(){
		                searchStylistInit();
		            }, 5e3);
		        }
		    });
        },
        onSelect: function (item) {
        	$('#search-customer').val(item.value);
        	inputLabelInit();
            $('.autocomplete-suggestions').css({'display': 'none'});
            $('#search-customer').focusout();
        }
    });
}

/*
	 * Lưu phiếu
 */
function editInvoiceSubmit(thisEl){
	var form = $(thisEl).parents(".ajax-form");
	form.find(".form-notify").hide();
	$("#loading").show();
	$.ajax({
		"type" : "POST",
		"dataType" : "JSON",
		"url" : "/api/CashFlowAPI/updateInvoice",
		"data" : form.find("input, select, textarea").serialize(),
		success: function(response){
			form.find(".form-notify").removeClass("alert-danger alert-success");
			if(typeof response["error"] == "undefined"){
				// Lỗi
				form.find(".form-notify").addClass("alert-danger").html("Đã xảy ra lỗi, vui lòng thử lại").show();
			}else if(response["error"] == null){
				showNotify('success', "Cập nhật thành công");
				refreshList(1);
				$(".modal-edit-invoice").fadeOut();
			}else{
				form.find(".form-notify").addClass("alert-danger").html(response["error"]).show();
			}
		},
		complete: function(){
			$("#loading").hide();
		},
		error: function(){
			setTimeout(function(){
				editInvoiceSubmit(thisEl);
			}, 2e3);
		}
	});
}

/*
 * Phân loại phiếu
 */
function cashFlowCategory(){
	multipleInsertData();
	$('.modal-cashflow-category').show();
}

/*
	 * Lưu phân loại
 */
function cashFlowCategorySubmit(thisEl){
	var form = $(thisEl).parents(".ajax-form");
	form.find(".form-notify").hide();
	$("#loading").show();
	$.ajax({
		"type" : "POST",
		"dataType" : "JSON",
		"url" : "/api/CashFlowAPI/updateCategory",
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
				cashFlowCategorySubmit(thisEl);
			}, 2e3);
		}
	});
}

/*
 * Chọn phân loại khi tạo phiếu
 */
function CashFlowChangeCategory(thisEl){
	var value = $(thisEl).val();
	if( value == 1 ){
		$('.invoice-customer').show();
	}else{
		$('.invoice-customer').hide();
	}
}