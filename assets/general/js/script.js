/*
# General
*/

//Phân biệt thiết bị
function device(){
	var width=$(window).width();
	if(width>=1024){
		return "desktop";
	}else if(width>=768){
		return "tablet";
	}else if(width>=128){
		return "mobile";
	}else{
		return "";
	}
}

//Set Cookie
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(name) {
 	var value = "; " + document.cookie;
 	var parts = value.split("; " + name + "=");
 	if (parts.length == 2) return parts.pop().split(";").shift();
}

//Click ẩn & hiện nội dung panel
panelClickInstall=function(){
	$(".panel>.link").off("click");
	$(".panel>.link").on("click", function(e){
		var thisEl=$(this).parent();
		thisEl.parent().find(".panel-actived").not($(this)).removeClass("panel-actived");
		thisEl.parent().children(".panel").children(".hidden").not(thisEl.children(".hidden")).slideUp();
		thisEl.children(".hidden").slideToggle();
		$(this).toggleClass("panel-actived");
	});
}


//Thao tác navbar menu
if($(document).width()>1024){
	//Màn hình lớn
	$(".navbar>ul>li").on("click mouseover", function(e){
		$(this).children("div").stop(true).slideDown(150);
	}).on("mouseleave", function(){
		$(".navbar li>div").slideUp(0);
	});
}else{
	//Màn hình nhỏ
	$(".navbar>ul>li").on("click", function(e){
		var subEl=$(this).children("div");
		$(".navbar").find(".navbar-item-opened").not($(this)).removeClass("navbar-item-opened");
		$(this).toggleClass("navbar-item-opened");
		$(".navbar li>div").not(subEl).slideUp();
		subEl.stop(true).slideToggle();
	});
}

$(".nav-icon-mobile").click(function(){
	var thisEl=this;
	var show=$(this).attr("data-show");
	var showEl=$("#header ."+show);
	var thisIcon=$(".nav-icon-mobile>i");
	thisIcon.removeClass("spin-effect").toggleClass("navbar-icon-actived");
	setTimeout(function(){
		thisIcon.addClass("spin-effect");
	},50);

	if(show == "navbar"){
		//Mở menu
		if(showEl.css("width")=="0px"){
			$("body").css({"overflow": "hidden"});
			showEl.css({"width": "100%"});
			thisIcon.removeClass("fa-bars").addClass("fa-times");
			$(".overlay-menu").show();
		}else{
			$("body").css({"overflow": ""});
			showEl.css({"width": "0"});
			thisIcon.removeClass("fa-times").addClass("fa-bars");
			$(".overlay-menu").hide();
		}
		showEl.children().one("click", function(e){
			if($(e.target).is("ul")){
				thisIcon.removeClass("navbar-icon-actived");
				$("body").css({"overflow": ""});
				showEl.css({"width": "0"});
				thisIcon.removeClass("fa-times").addClass("fa-bars");
			}
		});
	}else{
		//Thanh tìm kiếm
		$(".header-left").css({"visibility": "visible"});
		if($(".header-left").children().width()+320>$(document).width() && showEl.is(":hidden")){
			$(".header-left").css({"visibility": "hidden"});
		}
		showEl.toggle();
		var input=showEl.children("input");
		input.focus();
		input.one("focusout", function(){
			setTimeout(function(){
				thisIcon.removeClass("navbar-icon-actived");
				showEl.hide();
				$(thisEl).show();
				$(".header-left").css({"visibility": "visible"});
			}, input.val().length>0 ? 500 : 0);
		});
	}
	$(".navbar li>a").one("click", function(){
		$(".nav-icon-mobile")[0].click();
	});
});

$(".overlay-menu").click(function(){
	$(".nav-icon-mobile")[0].click();
});
//Tooltip
function tooltipInstall(el){
	var pos=el.attr("data-pos");
	var title=el.attr("title");
	if(typeof title=="undefined"){
		return;
	}
	if(typeof pos=="undefined"){
		var pos="top";
	}
	var reFix=false;
	if(pos=="top" || pos=="bottom"){
		var reFix=true;
	}
	el.append('<span class="tooltip-body">'+title+'</span>');
	var body=el.children(".tooltip-body");
	var offset=el.offset();
	if(offset.left<body.width()){
		body.css({"left": "0px", "transform": "translate(0)"}).addClass("tooltip-hidden-arrow");
	}else if( reFix && body.width()>($(window).width()-offset.left) ){
		body.css({"left": "auto", "right": "0px", "transform": "translate(0)"}).addClass("tooltip-hidden-arrow");
	}
	el.addClass("tooltip-"+pos).removeAttr("title").attr("data-title", title);
}
$(".tooltip").each(function(){
	tooltipInstall($(this));
});
$(".tooltip-outer").on("mouseenter touchstart", function(){
	$(this).find(".tooltip").each(function(){
		tooltipInstall($(this));
	});
});

//Nhập số tiền
function inputCurrency(thisEl){
	var val=$(thisEl).val().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	$(thisEl).val(val);
}
inputCurrencyInstall=function(){
	$(".input-currency").on("keyup change", function(){
		var val=$(this).val().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		$(this).val(val);
	});
}

//Hiện hộp thông báo
function showModal(id){
	$("#online-chat").css({left:"50%"}).addClass("online-chat-actived").show();
	$("#online-chat .online-chat-body").slideDown();
	setTimeout(function(){
		$("#online-chat").css({left:"50%"}).animate({"left":"0"}, "slow");
	},500);
}

/*
 * Input label
 */
function inputLabelInit(){
	$('.input-label').each(function(){
		var hasContent = false;
		if( $(this).find('input, select, textarea').val().length == 0 ){
			$(this).find('span').removeClass('input-label-has-content');
		}else{
			$(this).find('span').addClass('input-label-has-content').css({color: ''});
		}
	});
}
function inputLabelOnFocus(thisEl){
	$(thisEl).parent().find('span').addClass('input-label-has-content input-label-focus');
}
function inputLabelOutFocus(thisEl){
	var outerEl = $(thisEl).parent();
	if( $(thisEl).val().length == 0 ){
		outerEl.find('span').removeClass('input-label-has-content');
	}else{
		outerEl.find('span').addClass('input-label-has-content').css({color: ''});
	}
	outerEl.find('span').removeClass('input-label-focus');
}

/*
 * Chuyển số sang dạng tiền tệ
 */
function numberFormat(nStr){
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

$(window).on("scroll", function(e){
	var ScrollTop = $(window).scrollTop();
	if (ScrollTop > 40){
		$('#header').css({marginTop: '0px'});
	}else{
		$('#header').css({marginTop: ''});
	}
});

$(document).ready(function(){
	panelClickInstall();
	inputCurrencyInstall();
});