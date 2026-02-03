/*
 * Hiện thông báo
 */
function showNotify(status, msg){
	setTimeout(function(){
		$('#alert-fixed').html('<i class="fa fa-check"></i> '+msg).css({right: '30px'});
	}, 100);
	setTimeout(function(){
	   $("#alert-fixed").css({right: "-100%"});
   }, 10000);
}




/*
 * Menu
 */
function adminLeftMin(first){
	if( first && $('.admin-left.admin-min').length == 0 ){
		return;
	}
	$('.admin-left').off('mouseenter mouseleave');
	$('.admin-left').on('mouseenter', function(){
		$(this).removeClass('admin-min');
	}).on('mouseleave', function(){
		$(this).addClass('admin-min');
	});
}

/*
 * Ấn nút cố định menu
 */
function adminLeftPin(){
	if( getCookie('admin_left_menu_min') == 0 ){
		// Mở rộng menu
		setCookie('admin_left_menu_min', 1, 365);
		$(".admin-left, .admin-right").removeClass('admin-min');
		$('.admin-left').off('mouseenter mouseleave');
		$('.admin-collapse>span').text('Thu gọn menu');
		$('.admin-collapse>i').attr('class', 'fa fa-compress');
	}else{
		// Thu gọn menu
		setCookie('admin_left_menu_min', 0, 365);
		$(".admin-left, .admin-right").addClass('admin-min');
		$('.admin-collapse>span').text('Mở rộng menu');
		$('.admin-collapse>i').attr('class', 'fa fa-expand');
		adminLeftMin(false);
	}
}

//Thu gọn menu
function adminLeftCollapse(){
	$(".admin-left").removeClass("admin-min");
	$(".admin-right").removeClass("admin-min");
	if($(window).width() > 768){
		$(".admin-left .admin-collapse").find("i").toggleClass("fa-chevron-circle-right");
		adminLeftPin();
	}else{
		$(".admin-left").toggle();
		$(".admin-right").toggleClass("width-100");
	}
}

$(document).ready(function(){
	adminLeftMin(true);
	function showadminBody(gid){
		$(".admin-container,.admin-section").hide();
		//$(".admin-left .admin-actived").removeClass("admin-actived");
		var body=$("#adminContainer-"+gid+"");
		body.show();
		body.parents(".admin-section").show();
		$("html").animate({ scrollTop: $("html").offset().top }, 0);
	}

	//Click hiện nội dung
	$(".admin-left nav,.admin-left ol li").click(function(e){
		if($(this).hasClass("admin-has-sub")){
			//$(".admin-left .admin-has-sub.admin-actived").removeClass("admin-actived");
		}else{
			showadminBody($(this).attr("data-id"));
			if(!$(".admin-left.admin-min").length>0 && $(window).width()<768){
				adminLeftCollapse();
			}
		}
		//$(this).addClass("admin-actived");
	});


	//Click hiện sub menu
	$(".admin-left .admin-has-sub").click(function(e){
		var sub = $(".admin-left #adminSub-"+$(this).attr("data-id")+"");
		$(".admin-left .admin-has-sub").not( $(this) ).removeClass("admin-arrow-down");
		$(".admin-scrollbar>ol").not( sub ).slideUp(300);
		$(this).toggleClass("admin-arrow-down");
		sub.slideToggle(300);
	});

	// Click avatar
	if(device()=="desktop"){
		$(".admin-header-user").on("mouseenter", function(e){
			$(this).children("nav").slideDown(100);
		});
	}else{
		$(".admin-header-user").on("click", function(e){
			$(this).children("nav").slideToggle(100);
		});
	}
	$(".admin-header").on("mouseleave", function(){
		$(this).find(".admin-header-user>nav").slideUp(100);
	});

	$('.admin-actived').parent().prev('nav').addClass('admin-actived-color');

	// Đếm số thông báo
	var notifyCount = $('#fixed-button-bottom nav a[data-id="notify"] sup').text().replace('(', '').replace(')', '');
	if( notifyCount > 0 ){
		$('.header-notify-icon a[data-id="notify"] sub').text(notifyCount).show();
	}
});

/*
 * Ấn nút xem thông báo
 */
function headerShowNotify(){
	$('#fixed-button-bottom nav a[data-id="notify"]').click();
	setTimeout(function(){
		$.get('', function(response){
			var el = $(response).find('.header-notify-icon').html();
			$('.header-notify-icon').html(el);
		});
	}, 4e3);
}