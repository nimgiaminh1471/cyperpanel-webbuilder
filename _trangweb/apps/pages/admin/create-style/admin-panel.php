<?php


if(POST("formPart")=="AdminPanel"){
	foreach($_POST["storage"]["setting"] as $key => $value){
		$css[$key]=$value;
	}
	extract($css);

$content='
@charset "UTF-8";
:root{
	--menu-link-color: #cad6e2;
	--menu-background: #202d3f;
	--submenu-background: #121c2a;
}
html {
	background-image: none;
	background-color: '.$admin_panel_css_background.' !important;
	-webkit-font-smoothing: antialiased;
}


.admin{
	margin:0 auto;
	overflow-y:auto
}

.admin-header{
	width: 100%;
	position: fixed;
	z-index: 98;
	height: 65px
}
.admin-header>div:last-child{
	position: absolute;
	top: 50%;
	right: 0;
	transform: translate(0,-50%);
}
.admin-header-user{
	cursor: pointer;
	position: relative;
}

.admin-header .user-avatar>img{
	height: 40px !important;
	width: 40px !important;
	border-radius: 50%;
	object-fit: cover;
}
.admin-header-user>nav{
	display: none;
	position: fixed;
	min-width: 220px;
	top: calc(100% + 10px);
	right: 20px;
	box-shadow: 0 0 20px 0 rgba(0,0,0,.3);
	padding: 10px
}
.admin-header-user>nav:before{
	content: "";
	border: 10px solid transparent;
	position: absolute;
	border-radius: 3px;
	top: -8px;
	right: 20px;
	transform: rotate(45deg);
	z-index: 98;
}
.admin-header-user>nav>a{
	display: block;
	padding: 10px;
	font-size: 14px;
	transition: .3s all
}
.admin-left{
	position: fixed;
	width: 250px;
	top: 0;
	bottom: 0;
	left: 0;
	overflow: hidden;
	z-index: 97;
	box-shadow: 0 0 35px 0 rgba(154,161,171,.15);
	transition: width 0.1s;
}
.admin .logo-outer{
	font-size: 20px;
	text-transform: uppercase;
	display: block;
	text-align: left;
	padding: 0 15px
}
.admin .logo{
	max-height: 50px;
	max-width: 200px;
}
.admin-header-left{
	width: 250px;
}
.admin-scrollbar{
	top: 67px;
	position: absolute;
	bottom: 0px;
	left: 0px;
	right: -25px;
	overflow-y: scroll;
	overflow-x: hidden;
}
.admin-left>div{
	padding-bottom: 20px;
}

.admin-left.admin-min{width:50px;}
.admin-left.admin-min .admin-collapse span,
.admin-left.admin-min nav span,
.admin-left.admin-min a span,
.admin-left.admin-min ol li span,
.admin-left.admin-min .admin-arrow-right::after{
	display: none !important
}
.admin-right.admin-min{
	margin-left:100px !important;
}
.admin-right{
	padding-top:50px;
	margin-left:300px;
	margin-right:50px;
}


.admin-container{
	animation: fadein .8s;
	padding: 40px 0 40px 0;
	margin: auto;
}

.admin-left ol{
	list-style-type: none;
	margin: 0;
	padding: 0;
	display: none;
	position: relative;
}
.admin-left nav,
.admin-left ol li,
.admin-left>div a,
.admin-left .admin-collapse{
	margin: 0;
	list-style-type: none;
	user-select: none;
	position: relative;
	padding: '.$admin_panel_css_menu_padding.';
	border: none;
	display: block;
	cursor: pointer;
	overflow: hidden;
	font-size: '.$admin_panel_css_menu_font_size.'px;
	transition: .2s all
}
.admin-collapse-icon{
	cursor: pointer;
	display: block;
	padding: 5px
}
.admin-left ol li,
.admin-left .admin-item{
	padding-left: 50px
}
.admin-left.admin-min .admin-item{
	padding: 13px 20px;
	text-align: center;
}
.admin-left ol i{
	display: none;
}
.admin-left.admin-min ol i{
	display: inline-block !important;
}
.admin-left small{
	float:right;
	padding:3px;
	margin-right: 18px;
	background-color: var(--primary-color);
	border-radius: 50%;
	min-width: 22px;
	text-align: center;
	color: white;
	font-size: 11px
}

.header-notify-icon{
	margin-right: 5px
}
.header-notify-icon>a{
	padding: 5px 8px;
	position: relative;
}
.header-notify-icon>a>i,
.admin-collapse-icon>i{
	font-size: 18px
}
.header-notify-icon>a>sub{
	position: absolute;
	right: -5px;
	top: -2px;
	display: inline-block;
	text-align: center;
	font-size: 10px;
	background-color: tomato;
	color: white;
	min-width: 15px;
	padding: 2px;
	border-radius: 50%;
	display: none;
}



/* Light theme */
.admin-theme-light .admin-header{
	color: #72777a;
	background: white;
	box-shadow: 0 2px 4px 0 rgba(43,43,43,.1);
}
.admin-theme-light .admin-header{
	color: #72777a !important
}
.admin-theme-light .admin-header-user>nav{
	background: white;
}
.admin-theme-light .admin-header-user>nav:before{
	border-left-color: #fff;
	border-top-color: #fff;
}
.admin-theme-light .admin-header-user>nav>a{
	color: #72777a;
}
.admin-theme-light .admin-left{
	background: white;
	color: #72777a;
}
.admin-theme-light .admin-left>div a{
	color: #72777a;
}
.admin-theme-light .admin-left .admin-item{
	background-color: white;
}
.admin-theme-light .admin-left ol a{
	background: #fafafa !important;
}
.admin-theme-light .admin-left .admin-actived,
.admin-theme-light .admin-left>div a:hover,
.admin-theme-light .admin-left nav:hover,
.admin-theme-light .admin-left ol li:hover,
.admin-theme-light .admin-left .admin-collapse:hover{
	color: white !important;
	background-color: '.$admin_panel_css_actived_background.' !important
}
.admin-theme-light .admin-header-user>nav>:hover{
	color: var(--primary-color) !important
}
.admin-theme-light .admin-actived-color{
	/*color: var(--primary-color) !important*/
}
.admin-theme-light .admin-left>div a.admin-actived:after{
	content: "";
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
}
.admin-theme-light .admin-left:hover .admin-actived{
	opacity: .6;
}


/* Dark theme */
.admin-theme-dark .admin-header{
	color: var(--menu-link-color);
	background: var(--menu-background);
	border-bottom: 1px solid #444F5F;
}
.admin-theme-dark .admin-header{
	color: var(--menu-link-color) !important
}
.admin-theme-dark .admin-header-user>nav{
	background: var(--menu-background);
}
.admin-theme-dark .admin-header-user>nav:before{
	border-left-color: var(--menu-background);
	border-top-color: var(--menu-background);
}
.admin-theme-dark .admin-header-user>nav>a{
	color: var(--menu-link-color);
}
.admin-theme-dark .admin-left{
	background: var(--menu-background);
	color: var(--menu-link-color);
}
.admin-theme-dark .admin-left>div a{
	color: var(--menu-link-color);
}
.admin-theme-dark .admin-left .admin-item{
	background-color: var(--menu-background);
}
.admin-theme-dark .admin-left ol a{
	background: var(--submenu-background) !important;
}
.admin-theme-dark .admin-left .admin-actived{
	position: relative;
	color: white !important;
	background-color: '.$admin_panel_css_actived_background.' !important
}
.admin-theme-dark .admin-left>div a.admin-actived:after{
	content: "";
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
}
.admin-theme-dark .admin-left>div a:hover,
.admin-theme-dark .admin-left nav:hover,
.admin-theme-dark .admin-left ol li:hover,
.admin-theme-dark .admin-left .admin-collapse:hover{
	color: white !important;
	background-color: '.$admin_panel_css_actived_background.' !important
}
.admin-theme-dark .admin-header-user>nav>:hover{
	color: white !important
}
.admin-theme-dark .admin-left:hover .admin-actived{
	opacity: .6;
}
.admin-theme-dark .admin-scrollbar{
	top: 65px !important
}

@keyframes rotate { 100% {transform:rotateY(360deg); } }

@keyframes Gradient{
	0% {background-position: 0% 50%}
	50% {background-position: 100% 50%}
	100% {background-position: 0% 50%}
}



.admin-left .admin-arrow-right:after {
	pointer-events: none;
	border: none;
	border-right-color: none;
	border-width: 0px;
	margin-top: 0px;
	content: "\f054";
	display: inline-block;
	font-family: "FontAwesome";
	font-size: 10px;
	line-height: 1;
	position: absolute;
	right: 25px;
	top: 50%;
	margin-top: -4px;
	transform: rotate(0);
	transition: transform 0.2s;
	opacity: .6

}
.admin-left .admin-arrow-down:after {
	transform: rotate(90deg);
}







.admin-left .fa{
	width: 25px;
	margin-right: 4px;
	text-align: left;
	font-size: 17px
}




@keyframes animatetop{from{top:1px;opacity:0} to{top:2;opacity:1}}



.admin .form-item{
	background-color: white
}
.admin .ul{
	list-style-type: none
}
.admin-save-status{
	display:block;
	position:fixed;
	right:20px;
	top:10px;
	z-index:197
}
#adminSaveStatus{
	position: relative;
}
#adminSaveStatus>span{
	position: absolute;
	top: 0;
	right: 5px;
	white-space: nowrap;
}











#alert-fixed{
	position: fixed;
	bottom: 20px;
	z-index: 99999999;
	min-width: 250px;
	padding: 30px 40px;
	transition: 1s all;
	right: -100%;
	text-align: center;
	cursor: pointer;
	background-color: #1AC2CD;
	border-color: transparent !important;
	color: white;
	font-size: 15px;
	border-radius: 5px
}
#alert-fixed:after{
	position: absolute;
	content: "\f2d3";
	display: inline-block;
	font-family: "FontAwesome";
	top: 8px;
	right: 8px;
	opacity: .7
}







@media (max-width: 1200px) {


	.admin{
		margin-top: 0px;
		margin-right: 0px;
		box-shadow:none
	}

	.admin-scrollbar{
		margin-right: 15px
	}


}

@media (min-width: 1200px) {



}

@media (max-width: 768px) {
	.admin-right,
	.admin-right.admin-min{
		margin-left:0 !important;
		margin-right:0
	}

	.admin-left{
		display: none
	}

	.admin-save-btn{padding:2px}

	.section .flex-medium input,
	.section .flex-medium select,
	.section .flex-medium textarea,
	.section .flex-medium button{
		margin: 3px 0 3px 0 !important
	}
	.section .flex-medium .right{
		text-align: left !important
	}
	.admin-header-left{
		width: auto
	}
	.admin .logo-outer{
		padding: 0 5px
	}
}


section.column{padding: 0 20px 0 20px}







';

$content=cssMinifier($content);
$path=PUBLIC_ROOT."/assets/admin/css";
if (!is_dir($path)) {
	mkdir($path, 0755, true);
}
file_put_contents($path."/style__complete.css", $content);//Tạo file CSS
echo $content;
}