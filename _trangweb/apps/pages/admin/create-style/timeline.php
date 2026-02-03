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
.timeline {
	margin: 0;
	padding: 0;
	list-style: none;
	position: relative;
} 

.timeline li{
	position: relative;
} 



/* Right content */
.timeline-heading{
	margin-bottom: 15px
}
.timeline-content {
	margin: 0 5px 15px 85px;
	background:'.$panel_background.';
	color: #313131;
	font-size: 15px;
	position: relative;
	border-radius: 5px;
}
.timeline-title{
	position: relative;
	font-size:14px;
	font-weight:bold;
	cursor:pointer;
	user-select: none;
	display:block;
	padding: 15px;
}
.timeline-title-active{
	color: '.$primary_background.';
	border-bottom: 1px solid '.$primary_background.';
}
.timeline-title:after{
	content: "\\'.explode(" ",$panel_arrow)[0].'";
    font-family: FontAwesome;
	position: absolute;
	right: 5px;
	top: 50%;
	transform: translate(0,-50%);
}
.timeline-title-active:after{
	content: "\\'.explode(" ",$panel_arrow)[1].'";
}
.timeline-title>.fa,
.timeline-see-all{
	float: right;
	cursor: pointer;
}

/* The arrow */
.timeline-content:after {
	right: 100%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
	border-right-color: '.$panel_background.';
	border-width: 10px;
	top: 20px;

}



.timeline-body{
	display:none;
	padding: 15px;
}

.timeline-content img{
height:240px;
object-fit: cover;
width:100%
}



/* The line */
.timeline:before {
	content: "";
	position: absolute;
	top: 0;
	bottom: 0;
	width: 5px;
	background: '.$primary_background.';
	left: 45px;
	margin-left: -10px;
}




/* The left icon */
.timeline-icon {
	width: 60px;
	height: 60px;
	font-size: 12px;
	line-height: 60px;
	position: absolute;
	color: '.$primary_color.';
	background: '.$primary_background.';
	border-radius: 50%;
	/*box-shadow: 0 0 0 4px '.$primary_hover.';*/
	text-align: center;
	left: 35px;
	margin: 0 0 0 -25px;
	top:0px;

}







@media(max-width: 768px) {/*is Mobile*/

}



@media(max-width: 992px) {/*is Mobile and Table*/
.timeline-content img{
	height:auto;
	max-width:100%
}
}



.timeline-h{
	overflow-x:hidden;
	padding:20px 0
}
.timeline-h ol{
	width:100%;
	transition:all 1s;
	margin:0;
	padding:0;
	display:flex;
	justify-content:space-between;
	flex-flow: row wrap;
}
.timeline-h ol li{
	list-style:none;
	position:relative;
	text-align:center;
	flex-grow:1;
	flex-basis:0;
	padding:0 5px
}
.timeline-h ol li:before{
	content:"";
	width:10px;
	height:10px;
	display:block;
	border-radius:50%;
	background:'.$primary_background.';
	margin:0 auto 5px
}
.timeline-h ol li:not(:last-child)::after{
	content:"";
	width:calc(100% - 14px);
	height:2px;
	display:block;
	background:'.$primary_background.';
	margin:0;
	position:absolute;
	top:4px;
	left:calc(50% + 7px)
}

';


$content=cssMinifier($content);
$path=PUBLIC_ROOT."/assets/timeline";
if (!is_dir($path)) {
  mkdir($path, 0755, true);
}
file_put_contents($path."/style__complete.css", $content);//Tạo file CSS
echo $content;

}