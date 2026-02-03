function positionType(e){
	if(e.type == 'touchstart' || e.type == 'touchmove' || e.type == 'touchend' || e.type == 'touchcancel'){
		var position = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
	}else{
		var position = e;
		e.preventDefault();
	}
	return position;
}

$(".fixedButton").on("mousedown touchstart",function(e){
	var thisEl=this;
	var pos=positionType(e);
	var oLeft =pos.pageX;
	var oTop  =pos.pageY;
	
	$("body").on("mousemove touchmove",function(e){
		var pos=positionType(e);
		var nLeft=oLeft-pos.pageX;
		var nTop=oTop-pos.pageY;
		oLeft = pos.pageX;
		oTop = pos.pageY;
		$(".fixedButton").css({"left":(thisEl.offsetLeft-nLeft),"top":(thisEl.offsetTop-nTop)});
	});
	
}).on("mouseup touchend",function(){
	$("body").off("mousemove touchmove");
});
