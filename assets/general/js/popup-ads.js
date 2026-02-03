/*
# Quảng cáo popup
*/
popupAds=function(cog, post){
	// Lấy đuôi file
	function fileExt(filename){
		var ext = /^.+\.([^.]+)$/.exec(filename);
		return ext == null ? "" : ext[1];
	}
	$("body").append('<div id="popupAds"></div>');
	var _duplicatedTime=[];
	Object.keys(cog).forEach(function(i) {
		(function(i){
			if(_duplicatedTime.indexOf(cog[i].timer)>-1){
				return;
			}
			_duplicatedTime.push(cog[i].timer);
			setTimeout(function(){
				if(cog[i].html.length>3){
					var content=cog[i].html;
				}else if(fileExt(cog[i].src)=="mp4"){
					var content='<video autoplay="" controls=""><source src="'+cog[i].src+'" type="video/mp4" /></video>';
				}else{
					var content='<a href="'+cog[i].link+'" target="_blank"><img src="'+cog[i].src+'" /></a>';
				}
				if(cog[i].title.length>0){
					var title='<div class="heading heading-block">'+cog[i].title+' <i class="link right-icon fa"></i></div>';
				}else{
					var title='<i class="link fa"></i>';
				}
				$("#popupAds").html('<div class="modal"><div class="modal-body">'+title+' <div>'+content+'</div> </div></div>');
				var _countdown=cog[i].countdown;
				var _countdownRepeat=setInterval(function(){
					_countdown-=1;
					if(_countdown>0){
						$("#popupAds").find(".link").text(_countdown);
					}else{
						clearInterval(_countdownRepeat);
						$("#popupAds").find(".link").addClass("fa-times").text("");
						//Count
						$.post("/api", {"AdsCount_popup":1, "post": post, "id":i});
					}
				}, 1e3);
			}, cog[i].timer*1e3);
		})(i);
	});

	$("#popupAds").on("click", ".link", function(){
		if($(this).text().length==0){
			$("#popupAds").html("");
		}
	});
}