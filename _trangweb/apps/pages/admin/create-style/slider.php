<?php

/*
# Tạo CSS
*/

if(POST("formPart")=="Theme"){
	foreach($_POST["storage"]["setting"] as $key => $value){
		$css[str_replace("theme__", "", $key)]=$value;
	}
	extract($css);
$content='
/* Hiệu ứng chuyển mờ dần */
.slider-fadein li{
	animation: slider-fadein 2000ms;
}
@keyframes slider-fadein{
    from { opacity: 0; }
    to   { opacity: 1; }
}


.slider{
	overflow: hidden;
	position: relative;
	margin: auto;
	cursor: move;
	visibility: hidden;
	user-select: none;
}
.slider>ul{
	padding: 0;
	margin: 0;
	list-style-type: none;
}

.slider>ul>li{
	display: none;
}

.slider>ul>li img{
    width: 100%;
    max-height: 100%
}

.slider>.slider-basic{
	overflow: hidden;
	width: 100%;
	position: relative;
	left: 0
}
.slider>.slider-basic:after{
    content: ".";
    display: block;
    clear: both;
    visibility: hidden;
    line-height: 0;
    height: 0;
}
.slider>.slider-basic>li{
	float: left;
	display: block !important;
	position: relative;
	overflow: hidden;
}

/*Nút chuyển trước & tiếp*/
.slider>i{
	position: absolute;
	cursor: pointer;
	top:0;
	height: 100%;
	width:30px;
	color: '.$slider_color.';
	opacity: .'.$slider_opacity.'
}
.slider>i:hover{
	color: '.$slider_hover.' !important;
}
.slider:hover >i,
.slider:hover >div{
	opacity: 1 !important
}
.slider-btn-prev{left:0;}
.slider-btn-next{right:0;}
.slider>i:after{
	position: absolute;
	top: 50%;
	font-size: '.$slider_icon_size.'px;
	transform: translate(0,-50%);
	font-family: FontAwesome;
	font-style: normal;
}
.slider-btn-prev:after{
	left:5px;
	content: "\\'.explode(" ",$slider_arrow)[0].'";
}
.slider-btn-next:after{
	right:5px;
	content: "\\'.explode(" ",$slider_arrow)[1].'";
}

/*icon tròn*/
.slider>div{
	position: absolute;
	cursor: pointer;
	left: 30px;
	right: 30px;
	bottom: 10px;
	padding: 5px;
	text-align: center;
	opacity: .'.$slider_opacity.'
}
.slider>div>i{
	display: inline-block;
	width: '.$slider_circle_size.'px;
	height: '.$slider_circle_size.'px;
	border-radius: 50%;
	margin: 2px;
	background-color: '.$slider_color.';
}
.slider>div>i:hover,
.slider>div>.slider-circle-actived{
	background-color: '.$slider_hover.' !important;
}



.slider>ul>.slider-actived{display: block;}

';
$content=cssMinifier($content);
$path=PUBLIC_ROOT."/assets/slider";
if (!is_dir($path)) {
  mkdir($path, 0755, true);
}
file_put_contents($path."/style__complete.css", $content);//Tạo file CSS
echo $content;
}
