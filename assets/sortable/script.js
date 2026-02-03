/*
 * HTML5 Sortable jQuery Plugin
 * http://farhadi.ir/projects/html5sortable
 *
 * Copyright 2012, Ali Farhadi
 * Released under the MIT license.
 */
 (function($) {
	var dragging, placeholders = $();
	$.fn.sortable = function(options) {
		var method = String(options);
		options = $.extend({
			connectWith: false,
			items: ":not(.disabled)"
		}, options);
		return this.each(function() {
			if (/^(enable|disable|destroy)$/.test(method)) {
				var items = $(this).children($(this).data('items')).attr('draggable', method == 'enable');
				if (method == 'destroy') {
					items.add(this).removeData('connectWith items')
						.off('dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s');
				}
				return;
			}
			var isHandle, index, items = $(this).children(options.items);
			var placeholder = $('<li class="sortable-placeholder">.</li>');
			items.find(options.handle).mousedown(function() {
				isHandle = true;
			}).mouseup(function() {
				isHandle = false;
			});
			$(this).data('items', options.items)
			placeholders = placeholders.add(placeholder);
			if (options.connectWith) {
				$(options.connectWith).add(this).data('connectWith', options.connectWith);
			}
			items.attr('draggable', 'true').on('dragstart.h5s', function(e) {
				if (options.handle && !isHandle) {
					return false;
				}
				isHandle = false;
				var dt = e.originalEvent.dataTransfer;
				dt.effectAllowed = 'move';
				dt.setData('Text', 'dummy');
				index = (dragging = $(this)).addClass('sortable-dragging').index();
			}).on('dragend.h5s', function() {
				if (!dragging) {
					return;
				}
				dragging.removeClass('sortable-dragging').show();
				placeholders.detach();
				if (index != dragging.index()) {
					dragging.parent().trigger('sortupdate', {item: dragging});
				}
				dragging = null;
			}).not('a[href], img').on('selectstart.h5s', function() {
				this.dragDrop && this.dragDrop();
				return false;
			}).end().add([this, placeholder]).on('dragover.h5s dragenter.h5s drop.h5s', function(e) {
				if (!items.is(dragging) && options.connectWith !== $(dragging).parent().data('connectWith')) {
					return true;
				}
				if (e.type == 'drop') {
					e.stopPropagation();
					placeholders.filter(':visible').after(dragging);
					dragging.trigger('dragend.h5s');
					return false;
				}
				e.preventDefault();
				e.originalEvent.dataTransfer.dropEffect = 'move';
				if (items.is(this)) {
					if (options.forcePlaceholderSize) {
						placeholder.height(dragging.outerHeight());
					}
					dragging.hide();
					$(this)[placeholder.index() < $(this).index() ? 'after' : 'before'](placeholder);
					placeholders.not(placeholder).detach();
				} else if (!placeholders.is(this) && !$(this).children(options.items).length) {
					placeholders.detach();
					$(this).append(placeholder);
				}
				return false;
			});
		});
	};
})($);
$(document).ready(function(){
	
	$(".sortable").sortable({ handle: ".sortable-header" });
	$('.sortable').keydown(function(e){
		if (e.keyCode == 65 && e.ctrlKey) {
			e.target.select()
		}
	})

	//Chỉnh sửa
	$(".sortable-edit").click(function(e){
		var gid = $(this).attr("data-id");
		$(".sortableLabel_"+gid+",.sortableInput_"+gid+"").toggle();
		var getVal=$(".sortableInput_"+gid+"");
		var getEl=$(".sortableLabel_"+gid+"");
		for(var i=0;i<getVal.length;i++){
			$(getEl[i]).text($(getVal[i]).val());
		}
	});


	//Sắp xếp mục lớn
	$(".sortable-section").on("click",".sortable-item", function(e){
		var thisElement=$(this);
		//Chuyển lên
		if($(e.target).hasClass("sortableUp")){
			if(thisElement.index()>0){
				thisElement.prev().before(thisElement.clone());
				thisElement.remove();
			}
		}

		//Chuyển xuống
		if($(e.target).hasClass("sortableDown")){
			if(thisElement.index()<=thisElement.parent().length){
				thisElement.next().after(thisElement.clone());
				thisElement.remove();
			}
		}
		$(".sortable").sortable();
	});

});