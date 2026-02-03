/*
# Slider by LoKiem
*/
function sliderInstall(){
	//Lấy danh sách slider
	$(".slider").filter(function(){
		var thisEl=$(this);
		if(thisEl.attr("data-noinstallscript")=="1"){
			return false;
		}
		var installed = thisEl.hasClass("slider-installed") ? true : false;
		var mainEl=thisEl.children("ul");
		var disabledSwipe = typeof mainEl.attr("data-disable-swipe") == 'undefined' ? false : true;
		thisEl.css({'visibility': 'visible'});
		if(mainEl.hasClass("slider-basic")){
			var mainElWidth=thisEl.width();
			var item=mainEl.children("li");
			mainEl.css({width: mainElWidth*item.length});
			item.css({width: mainElWidth});
			if(installed){
				mainEl.css({"transform": "translate3d(-"+mainElWidth*mainEl.children(".slider-actived").index()+"px, 0px, 0px)", "transition": ""});
			}
		}
		if(installed){
			return false;
		}
		thisEl.addClass("slider-installed");
		var config={"autoPlay": (mainEl.attr("data-autoplay")*1000), "effect":mainEl.attr("class")};
		var itemList=mainEl.children();
		var lastHover=0;
		$.each(itemList, function(i){
			if(i==0){$(this).addClass("slider-actived");}
			thisEl.children("div").append('<i'+(i==0 ? ' class="slider-circle-actived"' : '')+'></i>');
		});
		//Chuyển tiếp
		function navigate(type, next, last){
			var active=mainEl.children(".slider-actived");
			if(next.length==0){
				if(type=="next"){
					//Tiếp
					var next=active.next();
					if(next.length==0){
						var next=last ? active : itemList.first();
					}
				}else{
					//Trước
					var next=active.prev();
					if(next.length==0){
						var next=last ? active : itemList.last();
					}
				}
			}
			
			thisEl.find(".slider-circle-actived").removeClass();
			
			if(config.effect=="slider-basic"){
				//Hiệu ứng trái-phải
				mainEl.css({"transform": "translate3d(-"+next.width()*next.index()+"px, 0px, 0px)", "transition": "all 800ms ease 0s"});
			}
			active.removeClass();
			next.removeClass().addClass('slider-actived');
			thisEl.children("div").find("i").eq(next.index()).addClass("slider-circle-actived");
		}

		//ấn nút tiếp
		thisEl.on("click",".slider-btn-next", function(){
			navigate("next", "", false);
		});

		//ấn nút trước
		thisEl.on("click",".slider-btn-prev", function(){
			navigate("prev", "", false);
		});

		//ấn chấm
		thisEl.on("click","div i", function(){
			navigate("next", itemList.eq($(this).index()), false);
		});

		//Vuốt để chuyển
		mainEl.on("mousedown touchstart", function(e){
			if( disabledSwipe ){
				return;
			}
			mainEl.find("li a").off("click");
			var mainElWidth=thisEl.width();
			var active=mainEl.children(".slider-actived");
			var index=active.index();
			var startPos=positionType(e).pageX;
			var _navType="", _dist=0;
			mainEl.on("mousemove touchmove", function(e){
				_endPos=positionType(e).pageX;
				if(startPos>_endPos){
					_dist=(startPos-_endPos);
					_navType="next";
					var newPos="-"+(mainElWidth*index+_dist);
				}else{
					_dist=(_endPos-startPos);
					_navType="prev";
					var newPos="-"+(mainElWidth*index-_dist);
					if(index==0){
						var newPos=_dist;
					}
				}
				if(_dist>5){
					mainEl.find("li a").on("click", function(){
						return false;
					});
				}
				if(mainEl.hasClass("slider-basic")){
					mainEl.css({"transform": "translate3d("+newPos+"px, 0px, 0px)", "transition": ""});
				}
			}).on("mouseup mouseleave touchend touchcancel", function(){
				if(_navType.length>0){
					var activeEL=_dist>40 ? "" : active;
					var last=mainEl.hasClass("slider-basic") ? true : false;
					navigate(_navType, activeEL, last);
				}
				mainEl.off("mousemove mouseup mouseleave touchend touchmove");
			});
		});

		//Hover chuột trên slider
		if( !thisEl.hasClass("slider-autoplay-hover") ){
			thisEl.on("touchstart mousemove click",function(){
				lastHover = (new Date()).getTime();
			});
		}

		//Tự động chuyển
		if(config.autoPlay>0){
			setInterval(function(){
				if(document.hidden===false && (new Date()).getTime()>lastHover+10e3){ navigate("next", "", false); }
			},config.autoPlay);
		}

	});


	function positionType(e){
		if(e.type == 'touchstart' || e.type == 'touchmove' || e.type == 'touchend' || e.type == 'touchcancel'){
			var position = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		}else{
			var position = e;
			e.preventDefault();
		}
		return position;
	}
}
$(document).ready(function(){
	sliderInstall();
	$(window).resize(function(){
		sliderInstall();
	});
});