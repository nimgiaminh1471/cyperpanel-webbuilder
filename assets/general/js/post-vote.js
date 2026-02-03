/*
# Đánh giá sao bài viết
*/
$(".post-single-vote").on("mousemove touchstart", ".post-single-vote-item", function(){
	var thisIndex=$(this).index();
	var outer=$(this).parent();
	outer.children().each(function(i){
		if(i<=thisIndex){
			$(this).children("i").removeClass("fa-star-o fa-star-half-o").addClass("fa-star");
		}else{
			$(this).children("i").removeClass("fa-star fa-star-half-o").addClass("fa-star-o");
		}
	});
});

var _refreshStar;
$(".post-single-vote").on("mouseleave touchend", function(){
	var thisEl=$(this);
	clearTimeout(_refreshStar);
	_refreshStar=setTimeout(function(){
		$.get("", function(data){
			thisEl.html($(data).find(".post-single-vote").html());
		});
	}, 3e3);
});

var _zoomStarRepeat;
$(".post-single-vote").on("click", ".post-single-vote-item", function(){
	var thisOuter=$(this).parent();
	var thisEl=$(this);
	var star=$(this).index()+1;
	var zoomStar=0;
	var items=thisOuter.children();
	clearInterval(_zoomStarRepeat);
	_zoomStarRepeat=setInterval(function(){
		thisOuter.children(".post-single-vote-zoom").removeClass("post-single-vote-zoom");
		items.eq(zoomStar).addClass("post-single-vote-zoom");
		if(zoomStar>=items.length){
			zoomStar=0;
		}else{
			zoomStar++;
		}
	}, 150);
	$.post("", {"postVoteStar": star}, function(data){
		setTimeout(function(){
			$.get("", function(){
				thisOuter.html($(data).find(".post-single-vote").html());
			});
		}, 2e3);
	});
});