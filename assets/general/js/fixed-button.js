/*
# Nút bên dưới trang
*/
//Hiện danh sách menu con
$("#fixed-button-bottom").on("click", function(){
	$(this).children("nav").slideToggle();
});


//Load lại nút
if( $("#fixed-button-bottom").attr('data-refresh').length > 0){
	setInterval(function(){
		var navShow=true;
		if($("#fixed-button-bottom nav").is(":hidden")){
			var navShow=false;
		}
		$.get("/",function(data){
			$("#fixed-button-bottom").html( $(data).filter("#fixed-button-bottom").html() );
			if(navShow){
				$("#fixed-button-bottom nav").show();
			}
		});
	},2e4);
}
//Click load nội dung modal
$("#fixed-button-bottom").on("click", "nav>.modal-click", function(){
	var modalID=$(this).attr("data-modal");
	$(".modal-"+modalID).hide();
	$.post("", {_readedNotify: 1}, function(response){
		var data=$(response).filter(".modal-"+modalID).html();
		$(".modal-"+modalID).html(data);
		$(".modal-"+modalID).show();
		panelClickInstall();
	});
});

/*
 * Đánh dấu thông báo đã đọc
 */
function FixedButtonReaded(){
	$.get('/?_deleteMessage', function(){
		$('.modal').hide();
		$('.header-notify-icon a[data-id="notify"] sub').hide();
	});
}