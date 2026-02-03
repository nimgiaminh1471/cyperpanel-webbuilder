/*
 * Khởi tạo dữ liệu
 */
function multipleInsertData(){
	$('.multiple-item').each(function(){
		var outerEl = $(this);
		outerEl.find('table tbody').html('');
		var data = JSON.parse( outerEl.find('.multiple-item-data').val() );
		var templateEl = outerEl.find('.multiple-item-template');
		var prefixName = templateEl.attr('data-name');
		var templateEl = templateEl.html();
		$.each(data, function(i, item){
			var id = i;
			var templateElHTML = $('<table><tbody>'+templateEl+'</tbody></table>');
			templateElHTML.find('input, textarea, select').each(function(){
				var name = prefixName+'['+id+']['+$(this).attr('name')+']';
				$(this).attr('data-name', $(this).attr('name')).attr('name', name).attr('data-id', id);
			});
			if( typeof item.default != 'undefined' && item.default == 1 ){
				templateElHTML.find('.multiple-item-delete').remove();
				templateElHTML.find('input, select, textarea').prop('readonly', true);
			}
			outerEl.find('table tbody').append( templateElHTML.children('tbody').html() );
			outerEl.find('input, textarea, select').each(function(){
				// Phục hồi dữ liệu
				if( typeof $(this).attr('data-id') == 'undefined' ){
					return;
				}
				var value = data[$(this).attr('data-id')][$(this).attr('data-name')];
				if( $(this).is('input') || $(this).is('textarea') ){
					$(this).val(value);
				}else{
					$(this).children('option[value="'+value+'"]').prop('selected', true);
				}
			});
		});
	});
}

$(document).ready(function(){

	/*
	 * CLick thêm mới
	 */
	$('.multiple-item').on('click', '.multiple-item-add', function(){
		var outerEl = $(this).parents('.multiple-item');
		var templateEl = outerEl.find('.multiple-item-template');
		var prefixName = templateEl.attr('data-name');
		var templateElHTML = $('<table><tbody>'+templateEl.html()+'</tbody></table>');
		var id = new Date().getTime();
		templateElHTML.find('input, textarea, select').each(function(){
			var name = prefixName+'[id_'+id+']['+$(this).attr('name')+']';
			$(this).attr('data-name', $(this).attr('name')).attr('name', name).attr('data-id', id);
		});
		outerEl.find('table tbody').append( templateElHTML.children('tbody').html() );
	});

	/*
	 * Xóa 
	 */
	$('.multiple-item').on('click', '.multiple-item-delete', function(){
 		var id = $(this).parents('tr').find('input').attr('data-id');
 		$(this).parents('tr').find('.multiple-item-deleted').val(1);
 		$(this).parents('tr').hide();
	});
});