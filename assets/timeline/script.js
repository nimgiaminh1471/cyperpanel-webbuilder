$(document).ready(function(){
	$(".timeline-title").click(function(){
		if($(this).parents(".timeline").attr("data-noinstallscript")=="1"){
			return false;
		}
		$(".timeline").find(".timeline-body").not($(this).parent().find(".timeline-body")).hide();
		$(".timeline-title-active").removeClass("timeline-title-active");
		$(this).addClass("timeline-title-active");
		$(this).parent().find(".timeline-body").show();
		$("html").animate({
			scrollTop: $(this).parents(".timeline-content").offset().top - 120
		}, 0);
	});
	$(".timeline-see-all").click(function(){
		if($(this).hasClass("blue")){
			$(".timeline").find(".timeline-body").hide();
		}else{
			$(".timeline").find(".timeline-body").show();
		}
		$(this).toggleClass("blue");
	});
});