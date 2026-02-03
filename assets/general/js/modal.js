/*
# Hộp thông báo
*/

$(document).ready(function(){
	//Click hiện
	$(".modal-click").off("click");
	$(".modal-click").on("click", function(){
		if( typeof $(this).attr("data-keep")=="undefined" ){
			$(".modal.hidden:visible").hide();
		}
		$(".modal-"+$(this).attr("data-modal")+"").show();
	});

	//Click vùng trống để đóng
	$(".modal.modal-allow-close").on("mousedown touchstart", function(e){
		if($(e.target).hasClass("modal")){
			var thisEl=$(this);
			thisEl.fadeOut(200);
			//alert( thisEl.css("animation").split(" ")[0] );
			return false;
		}
	});

	//Click đóng
	$(".modal").off("click", ".modal-close");
	$(".modal").on("click", ".modal-close", function(e){
		$(this).parents(".modal").fadeOut(200);
	});



	//Click hiện
	$(".modal-click, .modal-click-outer").on("click", ".modal-click", function(){
		if( typeof $(this).attr("data-keep")=="undefined" ){
			$(".modal.hidden:visible").hide();
		}
		$(".modal-"+$(this).attr("data-modal")+"").show();
	});

	//Click vùng trống để đóng
	$(".modal, .modal-click-outer").on("mousedown touchstart", ".modal-allow-close", function(e){
		if($(e.target).hasClass("modal")){
			var thisEl=$(this);
			thisEl.fadeOut(200);
			//alert( thisEl.css("animation").split(" ")[0] );
			return false;
		}
	});

	//Click đóng
	$(".modal, .modal-click-outer").on("click", ".modal-close", function(e){
		$(this).parents(".modal").fadeOut(200);
	});
});