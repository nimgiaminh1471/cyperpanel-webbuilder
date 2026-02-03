/*
# Chữ hiện dần trên đầu trang
*/
$("#header, .navbar li>ul").css({background: "transparent", "visibility": "visible"});
$(".intro").css({"visibility": "visible"});
$(".intro-outer").each(function(){
	var outer=$(this);
	outer.css({"height":  $(outer).height() });
	outer.children().hide();
	$(this).children().each(function(i){
		var thisItem=$(this);
		(function(i){
			setTimeout(function(){
				thisItem.fadeIn(1000);
			},i*1e3);
		})(i);
	});
});
$(window).on("scroll", function(){
	var ScrollTop = $(window).scrollTop();
	if (ScrollTop > 100){
		var bg="";
	}else{
		var bg="transparent";
	}
	if( $("#header").attr("data-toggle")!=bg ){
		$("#header").hide().delay(100).fadeIn();
		$("#header").attr("data-toggle", bg)
	}
	$("#header, .navbar li>ul").css({background: bg});
});
$(".intro-outer>button").click(function(){
	$("html").animate({ scrollTop: $(window).height()-$("#header").height()+1 }, 0);
});