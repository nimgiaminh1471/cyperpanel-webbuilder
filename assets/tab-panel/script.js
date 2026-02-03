$(document).ready(function(){
	$(".tab-panel>ul>li>span").on("click", function(){
		var showID = $(this).attr("data-show");
		var parentEl = $(this).closest(".tab-panel");
		parentEl.children("ul").find(".tab-panel-actived").removeClass("tab-panel-actived");
		$(this).addClass("tab-panel-actived");
		parentEl.children("div").find("li").hide();
		parentEl.children("div").find("li[data-id='"+showID+"']").show();
	});
});