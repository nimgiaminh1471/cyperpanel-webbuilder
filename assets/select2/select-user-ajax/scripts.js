/*
 * Tìm người dùng
 */
 
$('.select2-select-user-ajax').select2({
	//minimumInputLength: 1,
	//tags: [],
	ajax: {
		url: '/api/get-user',
		dataType: 'json',
		type: "POST",
		quietMillis: 50,
		data: function (params) {
			return {
				keyword: params.term
			};
		},
		processResults: function (data) {
			return {
				results: $.map(data, function (item) {
					return {
						text: item.name+' '+(typeof item.email == 'undefined' ? '' : '('+item.email+')'),
						id: item.id,
						avatar: item.avatar,
						all: item
					}
				})
			};
		}
	},
	templateResult: function (item) {
		if( typeof item.avatar == 'undefined' ){
			return $('<span>'+item.text+'</span>');
		}
		return $('<span><img src="'+item.avatar+'" style="width: 30px; height: 30px"> '+item.text+'</span>');
	},
	width: '100%'
}).on("select2:selecting", function(e) {
	$('#checkout-user-info').show();
	var data = e.params.args.data.all;
	$('#checkout-user-info input, #checkout-user-info select').each(function () {
		if( data.id.length == 0 ){
			var value = '';
		}else{
			var name = $(this).attr('name');
			if( typeof name != 'undefined'){
				var name = name.replace('user[', '').replace(']', '');
			}
			var value = data[name];
			if(typeof value == 'undefined'){
				var value = data.storage[name];
			}
			if(typeof value == 'undefined'){
				var value = '';
			}
			$('#checkout-create-account-body').slideUp();
		}
		if( $(this).is('input') ){
			$(this).val(value);
		}
		if( $(this).is('select') ){
			$(this).children('[value="'+value+'"]').prop('selected', true);
		}
	});
	inputLabelInit();
});