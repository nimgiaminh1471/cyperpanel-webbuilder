//Thao tác navbar menu
if($(document).width()>1024){
	//Màn hình lớn
	$(".blog-navbar>ul>li").on("click mouseover", function(e){
		$(this).children("div").stop(true).slideDown(150);
	}).on("mouseleave", function(){
		$(".blog-navbar li>div").slideUp(0);
	});
}else{
	//Màn hình nhỏ
	$(".blog-navbar>ul>li").on("click", function(e){
		var subEl=$(this).children("div");
		$(".blog-navbar").find(".navbar-item-opened").not($(this)).removeClass("navbar-item-opened");
		$(this).toggleClass("navbar-item-opened");
		$(".blog-navbar li>div").not(subEl).slideUp(0);
		subEl.stop(true).slideToggle();
	});
}

$(".blog-nav-icon-mobile").click(function(){
	$(".blog-navbar").slideToggle(0);
});