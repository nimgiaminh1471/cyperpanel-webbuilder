
$("#header, .navbar li>ul").css({background: "transparent", "visibility": "visible"});
$(window).on("scroll", function(e){
	var ScrollTop = $(window).scrollTop();
	if (ScrollTop > 100){
		var bg = "";
		var pos = "fixed";
		$(".header-fixed").show();
	}else{
		var bg = "transparent";
		var pos = "";
		$(".header-fixed").hide();
	}
	if( $("#header").attr("data-toggle")!=bg ){
		$("#header").hide().delay(100).fadeIn();
		$("#header").attr("data-toggle", bg)
	}
	$("#header, .navbar li>ul").css({background: bg, position: pos});
});