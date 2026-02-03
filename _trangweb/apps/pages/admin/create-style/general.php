<?php
/*
# Tạo CSS chính
*/

if(POST("formPart")=="Theme"){
	foreach($_POST["storage"]["setting"] as $key => $value){
		$css[str_replace("theme__", "", $key)]=$value;
	}
	extract($css);
	$fontFamily=(empty($general_my_font_family) ? "/assets/general/fonts/".$general_font_family.".ttf" : $general_my_font_family);
	if( file_exists(PUBLIC_ROOT."/".$fontFamily) ){
		$general_font_family=pathinfo($fontFamily, PATHINFO_FILENAME);
	}
$content='@charset "UTF-8";
'.(file_exists(PUBLIC_ROOT."/".$fontFamily) ? '
@font-face {
    font-family: '.$general_font_family.';
	src: url("'.$fontFamily.'");
	font-weight: normal;
	font-style: normal;
}
' : '
@import url("https://fonts.googleapis.com/css?family='.str_replace(' ','+',$general_font_family).':400,700&subset=vietnamese");
').'
:root{
    --primary-color: '.$primary_background.';
    --primary-hover-color: '.$primary_hover.';
    --secondary-color: '.$form_gradient_background1.'
}
html{
	font-size: '.$general_font_size.'px;
	font-family: "'.$general_font_family.'", sans-serif;
	scroll-behavior: smooth;
	background-color: '.$general_background_color.';
	'.(empty($general_background_image) || $general_background_switch<1 ? '' : 'background-image: url('.$general_background_image.'); background-position: top;background-repeat: no-repeat; background-attachment: fixed;').'
	'.( empty($general_background_color_2) ? '' : '
		background: linear-gradient(to bottom, '.$general_background_color.' 0, '.$general_background_color_2.' 100%);
   		background-repeat: no-repeat;
	' ).'
	
}

*, :after, :before{box-sizing:border-box}
	
body{
	margin: auto;
	padding: 0;
	color: '.$general_color.';
	overflow-x: hidden;
}

img{
	width: auto;
	height: auto;
	margin: 0;
	max-width: 100%;
	padding: 0;
	vertical-align: middle;
}

.image-caption{
	display: block;
	font-size: small;
	color: gray;
	padding: 2px 5px 15px 5px
}
.user-avatar{
	border-radius: 50%;
}
.user-avatar>img{
	border-radius: 50%;
	object-fit: cover;
	width: 50px;
	height: 50px;
	box-shadow: 0 0 1px #666;
}
a:link, a:visited, a:active{
	color: '.$general_link_color.';
	text-decoration: none
}
a:hover{
	color: '.$primary_background.';
}
.link,a{cursor: pointer}

.primary-color{
	color: '.$primary_background.' !important;
}

.primary-actived{
	color: '.$primary_background.' !important;
	border-bottom: 1px solid '.$primary_background.' !important;
}

.primary-bg{
	color: '.$primary_color.' !important;
	background-color: '.$primary_background.' !important
}

.primary-hover:hover{
	background-color: '.$primary_hover.' !important;
	color: '.$primary_color.' !important;
}
.primary-border{
	border-bottom: 1px solid '.$primary_background.';
}
#header-info{
	height: 40px;
	position: fixed;
	top: 0;
	width: 100%;
	z-index: 199708
}
#header{
	transition: .5s all;
	'.(empty(Storage::option("header_info_enable")) ? '' : 'margin-top: 40px;').'
	'.(empty(Storage::option("theme_header_bg")) ? '' : 'background: '.Storage::option("theme_header_bg").';').'
	'.(empty(Storage::option("theme_header_shadow")) ? '' : 'box-shadow: 0 0px 5px 0 rgba(0,0,0,.1);').'
	color: '.$navbar_color.';
	height: '.Storage::option("theme_header_height_dk").'px;
	line-height: '.Storage::option("theme_header_height_dk").'px;
	'.(Storage::option("theme_header_fixed")==1 ? '
	position: fixed;
	width: 100%;
	z-index: 199708;
	top: 0;
	' : '').'
}

.header-fixed{
	'.(empty(Storage::option("header_info_enable")) ? '' : 'margin-top: 40px;').'
	'.(Storage::option("theme_header_fixed")==1 ? 'height: '.Storage::option("theme_header_height_dk").'px' : 'display: none').';
}
#header .logo{
	max-height: '.(Storage::option("theme_header_height_dk") - 10).'px
}
.header-right{
	max-width: 90%;
}
/*Navbar*/

.navbar ul{
	list-style-type: none;
	padding: 0;
	margin: 0;
}
.navbar .primary-bg{
	'.(empty(Storage::option("theme_header_bg")) ? '' : 'background: '.Storage::option("theme_header_bg").' !important;').'
}
.navbar>ul>li{
	position: relative;
	margin: 0 10px;
	cursor: pointer;
	word-wrap: break-word;
    white-space: nowrap;
}
.navbar>ul>li:first-child{
	margin: 0 10px 0 0;
}
.navbar>ul>li:last-child{
	margin-right: 0;
}
.navbar li>a,
.navbar li>span{
	display: inline-block;
	color: '.$navbar_color.';
	position: relative;
	font-size: 18px
}
.navbar li>a:hover,
.navbar li>span:hover,
.navbar li:hover span,
.navbar-icon-actived,
.navbar-item-actived{
	color: '.$navbar_hover.' !important;
	'.($navbar_hover_type == 'underline' ? 'text-decoration: underline;' : '').'
}
.navbar li>div{
	display: none;
	position: absolute;
	z-index: 97;
	top: '.Storage::option("theme_header_height_dk").'px;
	left: 50%;
	transform: translate(-50%,0);
	line-height: normal;
	'.($navbar_shadow==1 ? 'box-shadow: 0px 0px 7px 0px rgba(8, 88, 157, 0.2);' : '').';
}
.navbar li>div>ul{
	display: flex;
}


.navbar li>div a{
	min-width: 180px;
	padding: 15px;
}
.navbar-arrow-icon:after{
	content: "\f107";
    font-family: FontAwesome;
    font-style: normal;
    line-height: normal;
    transition: transform 0.2s;
    display: inline-block;
}
.navbar li:hover .navbar-arrow-icon:after{
	transform: rotate(180deg);
}


/*Blog navbar*/
.blog-navbar-wrap{
	border-bottom: 1px solid #dedede;
	border-top: 1px solid #ebebeb;
}
.blog-navbar ul{
	list-style-type: none;
	padding: 0;
	margin: 0;
}
.blog-navbar>ul>li{
	position: relative;
	margin: 0 10px;
	cursor: pointer;
	word-wrap: break-word;
    white-space: nowrap;
    padding: 20px 0
}
.blog-navbar>ul>li:first-child{
	margin: 0 10px 0 0;
}
.blog-navbar>ul>li:last-child{
	margin-right: 0;
}
.blog-navbar li>a,
.blog-navbar li>span{
	display: inline-block;
	color: #313131;
	position: relative;
	font-size: 16px
}
.blog-navbar li>a:hover,
.blog-navbar li>span:hover,
.blog-navbar li:hover span,
.blog-navbar-icon-actived,
.blog-navbar-item-actived{
	color: '.$navbar_sub_hover.' !important;
	'.($navbar_hover_type == 'underline' ? 'text-decoration: underline;' : '').'
}
.blog-navbar li>div{
	display: none;
	position: absolute;
	z-index: 97;
	top: 100%;
	left: 50%;
	transform: translate(-50%,0);
	line-height: normal;
	'.($navbar_shadow==1 ? 'box-shadow: 0px 0px 7px 0px rgba(8, 88, 157, 0.2);' : '').';
}
.blog-navbar li>div>ul{
	display: flex;
	background: white
}


.blog-navbar li>div a{
	min-width: 220px;
	padding: 15px 20px;
	border-bottom: 1px solid #EDEDED
}
.blog-navbar-arrow-icon:after{
	content: "\f107";
    font-family: FontAwesome;
    font-style: normal;
    line-height: normal;
    transition: transform 0.2s;
    display: inline-block;
}
.blog-navbar li:hover .navbar-arrow-icon:after{
	transform: rotate(180deg);
}


#footer{
	'.(empty($footer_bg) ? '' : 'background-color: '.$footer_bg.'').';
	color: '.$footer_color.';
	overflow: hidden;
	'.(empty($footer_border) ? '' : 'border-top: 2px solid '.$footer_border.'').';
	padding: 15px;
	line-height: 1.5
}
#footer a{
	color: '.$footer_link_color.';
}
#footer a:hover{
	color: '.$footer_link_hover.' !important;
}
#main,.main{
	display: block;
	margin: auto;
}


/*Tạo viền*/
.bd{border:1px solid transparent;}
.bd-top{border-top:1px solid transparent;}
.bd-bottom{border-bottom:1px solid transparent;}
.bd-left{border-left:1px solid transparent;}
.bd-right{border-right:1px solid transparent;}


.line-run{position: relative}
.line-run:after{
	display: block;
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	width: 0;
	height: 1px;
	overflow: hidden;
	border-bottom: 1px solid '.$primary_background.';
	content: "";
	transition: all .3s;
	margin: auto
}
.line-run:hover:after{width: 100%}

.ul-none-style{
	list-style-type: none;
	padding: 0;
	margin: 0
}


/*Khu vực chứa nội dung*/
.section{
	box-shadow: 0 2px 8px 0 rgba(0,0,0,.'.$section_opacity.');
	background-color: '.$section_background.';
	margin-bottom: '.$section_margin.'px;
}
.section-body{
	padding: '.$section_padding.'px 15px
}
.section>.heading,
.section-heading{
	padding: '.$section_padding.'px 15px;
	color: '.($section_color==1 ? $primary_background : $general_color).';
	'.($section_border==1 ? 'border-bottom: 1px solid '.($section_color==1 ? $primary_background : $general_color).';' : '').'
	font-size: '.$section_font_size.'px;
	background-color: '.$section_heading_background.';
}
.heading-small{
	padding: 5px 15px !important
}

.heading,
.heading-basic,
.heading-block,
.heading-line,
.heading-sharp,
.heading-simple{
	'.($heading_uppercase==1 ? 'text-transform: uppercase;' : '').'
	font-weight: '.($heading_bold==1 ? 'bold' : 'normal').';
	position: relative;
}


/*Tiêu đề cơ bản*/
.heading-basic{
	text-align: center;
	font-size: '.$heading_basic_font_size.'px;
    padding: '.$heading_basic_padding.'px;
    font-weight: bold;
    color: '.($heading_basic_color==1 ? $primary_background : $general_color).'
}
'.( empty($heading_basic_icon) ? '' : '
	h1.heading-basic:after,
	.heading-basic>h1:after,
	h2.heading-basic:after,
	.heading-basic>h2:after{
		content: "";
		position: absolute;
		bottom: 0;
		left: 50%;
		transform: translate(-50%, 0);
		background-image: url('.$heading_basic_icon.');
		background-repeat: no-repeat;
		background-position: center;
		height: 30px;
		width: 100%;
	}	
' ).'

.heading-basic>h1,
.heading-basic>h2,
.heading-basic>h3{
	position: relative;
	font-size: inherit;
	font-weight: bold;
	text-transform: uppercase;
	'.( empty($heading_basic_icon) ? '' : 'padding-bottom: 30px').'
}
h2.heading-basic{
	'.( empty($heading_basic_icon) ? '' : 'padding-bottom: 30px').'
}
.heading-basic>div{
	font-size: 16px;
	font-weight: normal;
	padding: 10px 0 20px 0;
}
.heading-basic>div>i{
	width: 15px;
	background: #bbc3cc;
	height: 5px;
	margin: 0 1px;
	display: inline-block;
	border-radius: 10px
}
.heading-basic>div>i:nth-child(2){
	width: 30px;
}

/*Tiêu đề đơn giản*/
.heading-simple{
	font-size: '.$heading_simple_font_size.'px;
    padding: '.$heading_simple_padding.'px;
    border-bottom: 1px solid #EAEAEA;
    color: #313131;
    background-color: white
}

/*Tiêu đề khối*/
.heading-block{
	font-size: '.$heading_block_font_size.'px;
    padding: '.$heading_block_padding.'px;
    color: '.$primary_color.';
    background-color: '.$primary_background.';
}


/*Tiêu đề có vạch kẻ*/
.heading-line{
    position: relative;
    color: '.$primary_color.';
}
.heading-line>span:before{
    content: "";
    height: '.$heading_line_line_height.'px;
    width: 100%;
    background: '.$primary_background.';
    bottom: '.$heading_line_line_position.';
    z-index: -1;
    position: absolute;
    left:0
}
.heading-line>span{
    display: inline-block;
    border-radius: '.$heading_line_radius.'px;
    background: '.$primary_background.';
    margin: 0;
    padding: '.$heading_line_padding.'px;
    font-size: '.$heading_line_font_size.'px;
}



/*Tiêu đề có hình nhọn*/
.heading-sharp{
    border-bottom: '.$heading_sharp_line.'px solid '.$primary_background.';
    position: relative;
}
.heading-sharp>span{
    display: inline-block;
    padding: 0 18px;
    line-height: '.$heading_sharp_height.'px;
    position: relative;
    color: '.$primary_color.';
    background: '.$primary_background.';
    font-size: '.$heading_sharp_font_size.'px;
}
.heading-sharp>span:before {
    border-right: '.$heading_sharp_inclined.'px solid transparent;
    border-bottom: '.$heading_sharp_height.'px solid '.$primary_background.';
    content: "";
    display: inline-block;
    height: 0;
    position: absolute;
    right: -'.$heading_sharp_inclined.'px;
    top: 0;
    width: 0;
}

.heading-general{
	padding: 30px;
	background-color: '.Storage::option("theme_header_bg").' ;
	position: relative;
	text-align: center;
	font-size: 25px;
	color: white
}
.heading-general:before{
	content: "";
	position: absolute;
	background-position: top;
	background-image: url(/files/uploads/2019/07/bg.png);
	width: 100%;
	top: -70px;
	bottom: 0;
	left: 0;
	right: 0;
	z-index: 10000000;
	opacity: .1;
	pointer-events: none;
}

/*Input*/
select{cursor: pointer;}

.form input,
.form button,
.form select,
.form textarea,
.form-mrg{
	margin-bottom: '.$form_margin.'px
}
.form-hover>.form-item:hover{
	background-color: '.$form_hover.' !important
}
.input,
select,
textarea,
input[type="text"]{
	padding: '.$form_padding_vertical.'px '.$form_padding_horizontal.'px;
	border-radius: '.$form_input_radius.'px;
	font-size: '.$form_font_size.'px;
	transition: all .2s ease-in-out;
	border: 1px solid '.$form_border.';
	outline: none;
	vertical-align: middle;
}

textarea{border-radius: 3px !important;}

.input-color{
	position: relative;
}
.input-color span,
.input-color i{
	position: absolute;
	color: white;
	top: 50%;
    animation: colours 10s infinite;
    font-style: normal
}
.input-color span{
	pointer-events: none;
	left: 50%;
    transform: translate(-50%,-50%);
}
@keyframes colours {
	0% { color: black; }
	100% { color: white; }
}
.input-color input[type="color"]{
	min-width: 50px;
	min-height: 50px;
	border: 1px solid '.$form_border.';
	outline: none;
	vertical-align: middle;
	cursor: pointer;
	background: none
}

.input:focus,
select:focus,
textarea:focus,
input[type="text"]:focus{
	border: 1px solid '.$form_border_focus.';
	box-shadow: 0 0 5px '.$form_border_focus.';
}

.input-icon{
	position: relative;
	font-size: '.$form_font_size.'px;
}

.input-icon>.fa{
	position: absolute;
	top: 50%;
	right: 10px;
	pointer-events: none;
	transform: translate(0,-50%);
	color: gray;
}
.input-icon>.input{
	width: 100%;
	padding-left: '.$form_input_icon_padding.'px;
}

.input-group .input{
	border-radius: '.$form_input_radius.'px 0 0 '.$form_input_radius.'px !important;
	border: 1px solid '.$form_border.';
	border-right: none
}
.input-group .btn{
	border-radius: 0 '.$form_input_radius.'px '.$form_input_radius.'px 0 !important;
	border: 1px solid '.$form_border.';
	border-left: none
}
.input-group .input:focus{
	box-shadow: none
}

.input-error,
.input-success{
	position: relative;
}
.input-error:after,
.input-success:after{
	position: absolute;
    right: 5px;
    top: 50%;
    transform: translate(0,-50%);
}
.input-error input{
	width: 100%;
	box-shadow: 0 0 5px red;
}
.input-error:after{
	color:red;
	content: "\f00d";
    font-family: FontAwesome;
}
.input-success input{
	width: 100%;
	box-shadow: 0 0 5px green;
}
.input-success:after{
	color:green;
	content: "\f00c";
    font-family: FontAwesome;
}
.input-search>input{
	width: 110px;
	box-sizing: border-box;
	background-color: '.$navbar_search_bg.';
	border: 1px solid '.$navbar_search_border.';
	border-radius: 10em;
	padding-right: 25px;
	transition: all .5s;
	color: '.$navbar_search_color.'
}
.input-search>input:focus {
	width: 140px;
	border: 1px solid '.$navbar_search_focus.';
	box-shadow: 0 0 5px '.$navbar_search_focus.';
}
.input-search>input::placeholder{
	color: '.$navbar_search_color.'
}
.input-search{
	position: relative;
	display: inline-block;
}
.input-search>button{
	position: absolute;
	color: '.$navbar_search_color.';
	top: 50%;
	right: 5px;
	transform: translate(0,-50%);;
	line-height: normal;
	outline: none;
	background: none;
	border: none;
	cursor: pointer;
}
.input-search-full{
	padding: 10px 5px;
	margin: auto;
	display: block;
}
.input-search-full>input{
	width: 100% !important;
}
.input-search-large>input{
	width: 200px;
}
.input-search-large>input:focus {
	width: 230px;
}
/* Input label */
.input-label{
    position: relative;
    margin-bottom: 10px
}
.input-label>input,
.input-label>select,
.button-label{
    padding-top: 10px !important;
    padding-bottom: 10px !important;
}
.heading-large{
    padding-top: 12px !important;
    padding-bottom: 12px !important;
}
.button-label{
    margin-bottom: 10px;
}
.no-mrg{
    margin-bottom: 0
}
.input-label>span{
    position: absolute;
    top: 50%;
    transform: translate(0, -50%);
    left: 10px;
    background-color: white;
    padding: 2px 10px;
    color: gray;
    transition: .2s all;
    pointer-events: none;
    font-size: 13.5px;
    border-radius: 10px;
    white-space: nowrap;
}

.input-label-disabled>span{
    background-color: #eee;
}
.input-label-textarea>span{
    top: 20px;
}
.input-label>.input-label-has-content{
    font-size: 11px;
    top: -2px;
    font-weight: bold;
    color: var(--btn-primary-color);
}
.input-label>.input-label-focus{
    color: var(--btn-primary-color);
}
.form-date,
.form-date-wrap input{
    background-color: white !important;
    cursor: pointer !important;
}
.fa-icon{
	min-width: 20px
}
/*Các nút button*/
.btn,
.btn-primary,
.btn-danger,
.btn-info,
.btn-disabled,
.btn-gradient{
	padding: '.$form_padding_vertical.'px '.$form_padding_horizontal.'px;
	border-radius: '.$form_btn_radius.'px;
	font-size: '.$form_font_size.'px;
	border: 1px solid transparent;
	outline: none;
	text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    line-height: normal;
    display: inline-block;
    '.($form_btn_uppercase==1 ? 'text-transform: uppercase;' : '').'
}
.btn-sm{
	padding: 5px 10px;
    font-size: 12px;
}
.btn-primary{
	background-color: '.$primary_background.';
	border-color: '.$primary_background.';
	color: '.$primary_color.' !important;
}
.btn-primary:hover{
	background-color: '.$primary_hover.' !important;
	border-color: '.$primary_hover.' !important;
}

.btn-danger{
	background-color: '.$form_danger_background.';
	border-color: '.$form_danger_background.';
	color: '.$form_danger_color.' !important;
}
.btn-danger:hover{
	background-color: '.$form_danger_hover.' !important;
	border-color: '.$form_danger_hover.' !important;
}

.btn-info{
	background-color: '.$form_info_background.';
	border-color: '.$form_info_background.';
	color: '.$form_info_color.' !important;
}
.btn-info:hover{
	background-color: '.$form_info_hover.' !important;
	border-color: '.$form_info_hover.' !important;
}

.btn-gradient{
	background-color: '.$form_gradient_background1.';
	background: linear-gradient(135deg,'.$form_gradient_background1.' 30%,'.$form_gradient_background2.' 100%);
	border-color: '.$form_gradient_background1.';
	color: '.$form_gradient_color.' !important;
}
.btn-gradient:hover{
	opacity: .8
}
.btn-circle{
	border-radius: 25px !important;
	padding-left: 30px !important;
	padding-right: 30px !important
}
.btn-disabled{
	background-color:'.$form_disable_background.' !important;
	color: '.$form_disable_color.' !important;
	cursor: not-allowed;
}
.input-disabled,
input:disabled{
	background-color: '.$form_disable_background.';
	color: '.$form_disable_color.';
	user-select: none;
	cursor: not-allowed;
}
.input-disabled{
	cursor: pointer !important;
}
'.($general_font_family_input==1 ? '
input,button,select,textarea{
	font-family: "'.$general_font_family.'", sans-serif;
}
	' : '').'


/*Checkbox & radio*/
.check{
	display: inline-block;
	position: relative;
	padding: 2px 2px 2px '.($checkbox_checkbox_margin+20).'px;
	cursor: pointer;
	user-select: none;
	font-size: '.$checkbox_checkbox_font_size.'px;
	height: '.$checkbox_checkbox_size.'px;
	line-height: '.$checkbox_checkbox_line_height.'px;
}
.check input, .switch input{display:none}
.check s{
	position: absolute;
	left: 0;
	top: 50%;
	height: '.($checkbox_checkbox_size-5).'px;
	width: '.($checkbox_checkbox_size-5).'px;
	background-color: '.$checkbox_checkbox_background.';
	border:1px solid '.$checkbox_checkbox_border.';
	transform: translate(0,-50%);
}
.check s:after{
	content: "";
	position: absolute;
	display: none;
	left: '.$checkbox_checkbox_left.'%;
	top: '.$checkbox_checkbox_top.'%;
	width: '.$checkbox_checkbox_width.'px;
	height:'.($checkbox_checkbox_size/2).'px;
	border: solid '.$primary_color.';
	border-width: 0 2px 2px 0;
	transform: rotate(45deg)
}
.check input:checked ~ s{
	background-color: '.$primary_background.';
	border: 1px solid '.$primary_background.' !important;
}
.check input:checked ~ s:after{display:block}
.radio s{border-radius: 50%}


.switch{
	display: inline-block;
	position: relative;
	padding: 2px 2px 2px '.($checkbox_switch_margin_text+20).'px;
	cursor: pointer;
	user-select: none;
	height: '.$checkbox_switch_height.'px;
	font-size: '.$checkbox_switch_font_size.'px;
	line-height: '.$checkbox_switch_height.'px;
}


.switch>s{
	position: absolute;
	cursor: pointer;
	top: 50%;
	left:0;
	transform: translate(0,-50%);
	width: '.$checkbox_switch_width.'px;
	height: '.($checkbox_switch_height-5).'px;
	background-color: '.$checkbox_switch_background.';
	transition: 0.5s;
	border-radius: 34px;
	transition: .4s;
}

.switch>s:after{
	position: absolute;
	content: "";
	height: '.($checkbox_switch_height-12).'px;
	width: '.($checkbox_switch_height-12).'px;
	left: '.$checkbox_switch_margin.'px;
	top:50%;
	right:1px;
	background-color: '.$checkbox_switch_round.';
	border-radius: 50%;
	transform: translate(-50%,-50%);
	transition: .4s;
}

.switch input:checked ~ s{
	background-color: '.$primary_background.';
}


.switch input:checked ~ s:after{
	left: calc(100% - '.$checkbox_switch_margin.'px);
}










/*Nhãn*/
.label-default,
.label-primary,
.label-success,
.label-info,
.label-warning,
.label-danger{
	padding: '.$label_padding.'px 7px '.$label_padding.'px 7px;
	font-size: '.$label_font_size.'px;
	border-radius: '.$label_radius.'px;
	border: 1px solid transparent;
	display: inline-block;
	text-align: center;
}

.label-default{
	color: white;
	background-color: #777;
	border-color: #777;
}


/*Thông báo trạng thái*/
.alert-primary,
.alert-success,
.alert-info,
.alert-warning,
.alert-danger{
	padding: '.$alert_padding.'px;
	font-size: '.$alert_font_size.'px;
	border: 1px solid transparent;
	word-wrap: break-word
}

/*Bảng thông báo*/
.panel{
	font-size: '.$alert_font_size.'px;
	border: 1px solid transparent;
}
.panel-list>.panel,
.panel-last{
	border-top: none;
}
.panel-list>.panel:first-child{
	border-top: 1px solid transparent;
}
.panel.panel-no-border{
	border-left: none !important;
	border-right: none !important;
}
.panel-default{
	background-color: '.$panel_background.';
	border-color: '.$panel_border_color.' !important;
	color: '.$panel_color.';
}



/*Các loại kiểu thông báo*/
.label-primary,
.alert-primary,
.panel-primary{
	color: '.$primary_color.';
	background-color: '.$primary_background.';
	border-color: '.$primary_background.' !important;
}
.label-success,
.alert-success,
.panel-success{
	color: '.$alert_success_color.';
	background-color: '.$alert_success_background.';
	border-color: '.$alert_success_border.' !important;
}
.label-info,
.alert-info,
.panel-info{
	color: '.$alert_info_color.';
	background-color: '.$alert_info_background.';
	border-color: '.$alert_info_border.' !important;
}
.label-warning,
.alert-warning,
.panel-warning{
	color: '.$alert_warning_color.';
	background-color: '.$alert_warning_background.';
	border-color: '.$alert_warning_border.' !important;
}
.label-danger,
.alert-danger,
.panel-danger{
	color: '.$alert_danger_color.';
	background-color: '.$alert_danger_background.';
	border-color: '.$alert_danger_border.' !important;
}




/*Nội dung bảng thông báo*/
.panel>.heading{
	padding: '.$alert_padding.'px;
	border: 1px solid transparent;
	position: relative;
	user-select: none;
}
.panel>.heading.link:after{
	content: "\\'.explode(" ",$panel_arrow)[0].'";
    font-family: FontAwesome;
	position: absolute;
	right: 10px;
	top: 50%;
	transform: translate(0,-50%);
}
.panel>.heading.panel-actived:after{
	content: "\\'.explode(" ",$panel_arrow)[1].'";
}


.panel-body{
	background-color: #fff;
	padding: '.$alert_padding.'px;
	color: '.$general_color.' !important;
}




/*Nút đếm số lượng*/
.badge{
	padding: '.$label_padding.'px 7px '.$label_padding.'px 7px;
	font-size: '.$label_font_size.'px;
	border-radius: 30px;
	border: 1px solid transparent;
	display: inline;
	text-align: center;
	background-color: '.$primary_background.';
	color: '.$primary_color.';
	min-width: 25px;
}








/*Danh sách*/
ul.list{
	list-style-type: none;
	padding: 0
}
.list>a{
	position: relative;
	display: block;
	transition: .4s;
	color: '.$list_link.' !important;
	border: 1px solid '.$list_border_color.';
	padding: '.$list_padding.'px;
	margin-bottom: -1px;
	background-color: '.$list_background.';
	font-size: '.$list_font_size.'px;
	'.($list_border==1 ? '' : 'border-top: none; border-bottom: none;').'
}
.list>a:hover{
	background-color: '.$list_active.' !important;
}
.list>span,
.list>li{
	display: block;
	border: 1px solid '.$list_border_color.';
	padding: '.$list_padding.'px;
	margin-bottom: -1px;
	background-color: '.$list_background.';
	font-size: '.$list_font_size.'px;
	position: relative
}
.list>span:last-child,
.list>li:last-child,
.list>a:last-child{
	margin-bottom: 1px;
}
.list>.list-actived{
	background-color: '.$list_active.';
}
.list:hover .list-actived{
	background-color: '.$list_background.';
}
.list .badge{
	position: absolute;
	top: 50%;
	right: 5px;
	transform: translate(0, -50%);
}
.menu,
.menu-bg{
	border-color: '.$menu_border_color.';
	padding: '.$menu_padding.'px;
	background-color: '.$menu_background.';
	font-size: '.$menu_font_size.'px;
	word-wrap: break-word;
}
.menu-bg{
	background-color: '.$menu_background_bg.' !important;
}


/*Nút xem thêm*/
.see-more{
	display: inline-block;
	border: 1px solid '.$primary_background.';
	padding: '.$seeMore_padding.'px 15px '.$seeMore_padding.'px 15px;
	font-size: '.$seeMore_font_size.'px;
	border-radius: '.$seeMore_radius.'px;
	color: '.$primary_background.' !important;
	text-align: center;
	transition: .3s;
}

.see-more:hover{
	background-color: '.$primary_hover.';
	color: '.$primary_color.' !important;
	
}

/*Bảng*/
.table,
.table-border,
.table-border-top{
	border-collapse: collapse;
	padding: 0;
	margin: 0 auto;
}
.table th{
	padding: '.$table_padding.'px;
	text-align: inherit;
	background-color: '.$table_th_background.';
	color: '.$table_th_color.'
}
.table td{
	padding: '.$table_padding.'px;
}
.table tr:nth-child(even) td{
	background-color: '.$table_td1_background.';
}
.table tr:nth-child(odd) td{
	background-color: '.$table_td2_background.';
}
.table-border th,
.table-border td{
	border: 1px solid '.$table_border_color.';
}
.table-border-top tr{
	border-top: 1px solid '.$table_border_color.';
}
.table tr:hover td{
	background-color: '.$table_hover.';
}
.table th{
	text-align: inherit;
}
.table tr.table-actived td{
	background-color: '.$table_hover.';
}
.table-responsive{
	display: block;
    width: 100%;
    overflow-x: auto;
}

/*Hộp hiện thông báo*/
.modal{
	top:0;
	position:fixed;
	z-index:199709;
	width:100%;
	height:100%;
	overflow:auto;
	background-color:rgba(0,0,0,0.5);
	left:0;
	bottom:0;
	'.($modal_fadein>0 ? 'animation: fadein '.$modal_fadein.'s;' : '').'
}
.modal .modal-heading{
	padding: '.$modal_padding.'px !important;
	font-size: '.$modal_font_size.'px;
	'.($modal_heading_type=="block" ? '
	color: '.$primary_color.';
    background-color: '.$primary_background.';
	' : '
	border-bottom: 1px solid '.$primary_background.';
    color: '.$primary_background.';
    background-color: white
	').'
	
}

.modal-body{
	position:absolute;
	top:50%;
	left:50%;
	transform: translate(-50%,-50%);
	width: 95%;
	max-height: 100%;
}
.modal-content{
    margin: 20px 0;
    background: white
}
.modal-close,
.right-icon{
	position: absolute;
	right: 10px;
	top: 50%;
	transform: translate(0,-50%);
}
.modal-close:after{
	content: "\\'.$modal_close_icon.'";
    font-family: FontAwesome;
}
.modal-close:hover{
	opacity: .7
}

@keyframes fadein{
	from{
		opacity:.1
	}
	to{
		opacity:1
	}
}


.modal-form{
	border-radius: 15px;
	background: #EAEEF1 !important
}
.modal-form>.heading{
	padding: 30px 30px 2px 30px;
	text-align: center;
	font-size: 20px;
	font-weight: 600;
}
.modal-form>.heading>i{
	top: 25px;
	right: 15px
}
.modal-form>.modal-content{
	background: transparent !important;
}
.modal-form>form input,
.modal-form>form button{
	margin-top: 10px;
	margin-bottom: 10px;
	border-radius: 20px !important
}
.modal-form>form .modal-input{
	position: relative;
}
.modal-form>form .modal-input>i{
	position: absolute;
	top: 50%;
	right: 5px;
	transform: translate(0, -50%);
	background: #eff2f9;
	color: #9ba7ca;
	display: inline-block;
	width: 30px;
	height: 30px;
	border-radius: 50%;
	line-height: 30px;
	text-align: center;
}
.modal-form label{
	font-size: 14px
}
.form-logo{
	position: absolute;
	top: -50%;
	left: 50%;
	transform: translate(-50%, 0);
	background: #EAEEF1;
	border-radius: 50%;
	width: 50px;
	height: 50px;
	line-height: 50px;
	box-shadow: 0 0 0 2.25px #eff2f9;
	vertical-align: middle;
}
.form-logo>img{
	width: 60%;
	height: 60%;
}

/*Phân trang*/
.paginate{
	list-style-type: none;
	margin: 0;
	padding: 10px 0;
}

.paginate>a,
.paginate>span{
	display: inline-block;
	text-align: center;
	border: 1px solid '.$primary_background.';
	color: '.$primary_background.';
	padding: '.$paginate_padding.'px;
	font-size: '.$paginate_font_size.'px;
	margin: '.$paginate_margin.'px;
	min-width: 35px;
	background-color: #fff;
	border-radius: '.$paginate_radius.'px;
}
.paginate>span,
.paginate>a:hover{
	background-color: '.$primary_background.';
	color: '.$primary_color.';
	border: 1px solid '.$primary_background.';
}


/*Hình loading*/
#loading>div{
	top:0;
	left:0;
	padding-top:50px;
	position:fixed;
	z-index:9999999999;
	width:100%;
	height:100%;
	overflow:auto;
	background-color: rgba(0,0,0,0.2);
}
#loading img{
	position:absolute;
	top:0;
	left:0;
	right:0;
	bottom:0;
	margin:auto;
}
#loading>div>div{
	position:absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
}
.loading-ouside{
	width:150px;
	height:150px;
	border-radius:50%;
	position:relative;
	margin:0 auto;
	margin-top:200px;
	overflow:hidden;
	animation:spin 2s linear infinite;
}

@-webkit-keyframes spin{
	0%{
		-transform:rotate(0deg);
	}
	100%{-webkit-transform:rotate(360deg);
		-moz-transform:rotate(360deg);
		-o-transform:rotate(360deg);
		-ms-transform:rotate(360deg);
		-transform:rotate(360deg);

	}
}

.loading-inside{
	width:100%;
	height:50%;
	position:absolute;
	margin-top:50%;
	background:linear-gradient(90deg,'.$form_gradient_background1.',#EAEAEA);
}
.loading-inside:before{
	content: "";
	width:100%;
	height:100%;
	position:absolute;
	margin-top:-50%;
	background:linear-gradient(90deg,'.$form_gradient_background1.',#EAEAEA);
	
}
.loading-inside:after{
	content: "";
	width:80%;
	height:160%;
	position:absolute;
	margin-top:-40%;
	margin-left: 10%;
	background: white;
	border-radius:50%;
}


/*Breadcrumb navigation*/
.breadcrumb{
	list-style-type: none;
	padding: 0;
	margin: 0;
	display: flex;
	flex-wrap: nowrap;
}
.breadcrumb a{
	display: inline-block;
	padding: 15px 0
}
.breadcrumb>li{
	position: relative;
}
.breadcrumb>li:after{
	content: "\f105";
	font-family: FontAwesome;
	padding: 8px;
}
.breadcrumb>li:last-child:after{
	display: none
}


/*Nút cố định cuối trang*/
.fixed-button{
	position: fixed;
	bottom: '.($fixed_button_bottom + 80).'px;
	right: '.$fixed_button_right.'px;
	z-index: 97;
}
.fixed-button>a{
	padding: 5px;
	display: inline-block;
	position: relative;
	border-radius:50%;
	color: '.$fixed_button_color.';
	width: 55px;
	height: 55px;
	background-color: '.$fixed_button_linear_from.';
	background-image: linear-gradient(45deg, '.$fixed_button_linear_from.' 0%, '.$fixed_button_linear_to.' 100%); 
	opacity: 1;
	transition: opacity 0.3s ease-out;
}
.fixed-button>a:hover{
	text-shadow: none;
	opacity: .7
}
.fixed-button>a i,
.fixed-button>a img{
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%,-50%);
	font-size: 30px;
	padding: 6px;
}
.fixed-button>a img{
	border-radius: 50%;
	width: 55px;
	height: 55px;
	object-fit: cover;
}
.fixed-button>span{
	display: inline-block;
	position: absolute;
	top: 0;
	right: -8px;
	background-color: '.$fixed_button_linear_from.';
	background-image: linear-gradient(45deg, '.$fixed_button_linear_to.' 0%, '.$fixed_button_linear_from.' 100%); 
	color: '.$fixed_button_color.';
	border-radius: 50%;
	min-width: 20px;
	min-height: 20px;
	padding: 2px;
	text-align: center;
	font-size: 12px;
	z-index: 1421997
}
.fixed-button>nav{
	display: none;
	position: absolute;
	bottom: 20px;
	right: 60px;
	min-width: 180px;
}

.fixed-button>nav a{
	display: block;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
	margin-top: 1px;
	border-radius: 20px;
	background-color: '.$fixed_button_linear_from.';
	background-image: linear-gradient(45deg, '.$fixed_button_linear_to.' 0%, '.$fixed_button_linear_from.' 100%);
	color: '.$fixed_button_color.';
	padding: 8px 13px;
}
.fixed-button>nav a:hover{
	background-image: linear-gradient(45deg, '.$fixed_button_linear_to.' 30%, '.$fixed_button_linear_from.' 100%);
}

.fixed-button>nav a sup{color: '.$primary_color.';}



/*Nút liên hệ*/
.contact-button{
	position: fixed;
	bottom: '.($fixed_button_bottom).'px;
	right: '.$fixed_button_right.'px;
	z-index: 1402972;
}
.contact-button a{
	position: relative;
	padding: 5px;
	display: inline-block;
	border-radius:50%;
	color: '.$fixed_button_color.';
	width: 55px;
	height: 55px;
	background-color: '.$primary_background.';
	opacity: 1;
	transition: .5s all;
}
.contact-button>a:hover,
.contact-button>nav>a:hover{
	text-shadow: none;
}
.contact-button a i,
.contact-button a img{
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%,-50%);
	font-size: 30px;
	padding: 6px;
}
.contact-button a img{
	border-radius: 50%;
	width: 50px;
	height: 50px;
	object-fit: cover;
}
.contact-button>span{
	display: inline-block;
	position: absolute;
	top: 0;
	right: -8px;
	background-color: '.$primary_background.';
	color: '.$fixed_button_color.';
	border-radius: 50%;
	min-width: 20px;
	min-height: 20px;
	padding: 2px;
	text-align: center;
	font-size: 12px;
	z-index: 1421997
}
.contact-button>nav{
	display: none;
	position: absolute;
	bottom: 60px;
	right: 0;
}
.contact-button>nav a{
	display: block !important;
	margin-bottom: 10px;
	position: relative
}
.contact-button>nav a>span{
	opacity: 0;
	pointer-events: none;
	position: absolute;
	background-color: '.$primary_background.';
	display: inline-block;
	right: calc(100% + 5px);
	top: 50%;
	text-align: center;
	padding: 10px;
	border-radius: 20px;
	min-width: 170px;
	transform: translate(0, -50%);
	transition: .5s all
}
.contact-button>nav a:hover span{
	opacity: 1
}

.pulsing-button{
	display: block;
	width: 22px;
	height: 22px;
	border-radius: 50%;
	background: '.$primary_background.';
	cursor: pointer;
	box-shadow: 0 0 0 '.$primary_background.';
	animation: pulse 2s infinite;
}
@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 '.$primary_background.';
  }
  70% {
      box-shadow: 0 0 0 20px rgba(204,169,44, 0);
  }
  100% {
      box-shadow: 0 0 0 0 rgba(204,169,44, 0);
  }
}

.contact-button-wrap{
	background: rgba(33,33,33,.5);
	width: 100vw;
	position: fixed;
	height: 100%;
	top: 0;
	bottom: 0;
	z-index: 1402971;
}


/*Tooltip title*/
.tooltip{
	position: relative;
	display: inline-block;
}
div.tooltip,
section.tooltip{
	display: block !important;
}
.tooltip-body{
	font-size: '.$tooltip_font_size.'px;
	visibility: hidden;
	background-color: '.$tooltip_background.';
	color: '.$tooltip_color.';
	text-align: center;
	border-radius: '.$tooltip_radius.'px;
	padding: '.$tooltip_padding.'px 8px '.$tooltip_padding.'px 8px;
	position: absolute;
	z-index: 140297;
	display: inline-block;
	width: auto;
	white-space: nowrap;
}

.tooltip-left .tooltip-body{
	top: 50%;
	right: calc(100% + '.$tooltip_margin.'px);
	transform: translate(0,-50%);
}
.tooltip-right .tooltip-body{
	top: 50%;
	left: calc(100% + '.$tooltip_margin.'px);
	transform: translate(0,-50%);
}
.tooltip-bottom .tooltip-body{
	top: calc(100% + '.$tooltip_margin.'px);
	left: 50%;
	transform: translate(-50%);
}
.tooltip-top .tooltip-body{
	bottom: calc(100% + '.$tooltip_margin.'px);
	left: 50%;
	transform: translate(-50%);
}
.tooltip .tooltip-body::after{
	content: "";
	position: absolute;
	border-style: solid;
	border-width: 5px;
}
.tooltip-top .tooltip-body::after,
.input-tooltip>span::after{
	top: 100%;
	left: 50%;
	margin-left: -5px;
	border-color: '.$tooltip_background.' transparent transparent transparent;
}

.tooltip-bottom .tooltip-body::after{
	bottom: 100%;
	left: 50%;
	margin-left: -5px;
	border-color: transparent transparent '.$tooltip_background.' transparent;
}

.tooltip-right .tooltip-body::after{
	top: 50%;
	right: 100%;
	margin-top: -5px;
	border-color: transparent '.$tooltip_background.' transparent transparent;
}

.tooltip-left .tooltip-body::after{
	top: 50%;
	left: 100%;
	margin-top: -5px;
	border-color: transparent transparent transparent '.$tooltip_background.';
}

.tooltip-hidden-arrow:after{
	display: none !important
}

.tooltip:hover .tooltip-body{visibility: visible;}
.tooltip .tooltip-body:hover{visibility: hidden !important;}

/*Trích dẫn*/
.quote{
	border-left: 5px solid '.$primary_background.';
	padding: 8px;
	background-color: #EAEAEA;
	margin: 2px 0 2px 0;
}
.quote>span{
	display: block;
	font-weight: bold
}
.quote>i{
	display: block
}
.quote>i:before {
	content: "“";
	padding-right:5px;
}
.quote>i:after {
	padding-left:5px;
	content: "”";
}

/*Quảng cáo*/
#popupAds .modal-body{
	width: 90%;
	max-width: 900px;
	max-height: calc(100% - 50px);
	text-align: center
}
#popupAds .heading-block>i{
	font-size: 25px;
	color: '.$primary_color.';
}
#popupAds img,
#popupAds video{
	object-fit: cover;
	max-width: 100%;
	max-height: 100%
}
#popupAds .modal-body>.link{
	color: '.$primary_color.';
	font-size: 28px;
	position: absolute;
	top: -25px;
	right: -20px
}
#popupAds .modal-body>.link:hover{
	color: '.$primary_hover.';
}

/*Danh sách bài viết dạng lưới*/
.posts-flex>div{
	overflow: hidden;
	position: relative;
}
.posts-flex .flex{
	position: absolute;
	top: 50%;
	transform: translate(0,-50%);
	width: 100%;
}
.posts-flex-text>span,
.posts-flex .flex>a{
	display: block;
}
.posts-flex .flex>a{
}
.posts-flex .flex>a>img{
	width: 100%;
	height: 100%;
	object-fit: cover;
}
.posts-flex-desc,
.posts-flex-time{
	text-align: justify;
}
.posts-flex-time{
	margin-bottom: 3px
}
.posts-flex-text{
	padding: 0 8px
}
.account-outer{
	animation: none
}
.account-outer .modal-body{
	max-width:420px;
}
.account-outer .modal-content{
	border-radius: 3px;
}

/*Thanh trạng thái*/
.progress-bar {/*BG seek bar*/
  height: 20px;
  background: #e9ecef;
  border:none;
  transition: all 0.3s ease;
  display:block;
}


.progress-bar::-webkit-progress-bar {/*Chrome-Safari BG seek bar*/
  background: #e9ecef;
  border:none
}
.progress-bar::-webkit-progress-value { /*Chrome-Safari value*/
  background: '.$primary_background.';
  border:none
}
.progress-bar::-moz-progress-bar { /*Firefox value*/
  background: '.$primary_background.';
  border:none
}

.progress-bar::-ms-fill { /*IE-MS value*/
  background: '.$primary_background.';
  border:none
}

/* Hiệu ứng click*/
.fx-btn-blick {
	position: relative;
	overflow: hidden;
}
.fx-btn-blick:before {
	content: "";
	background-color: #fff;
	height: 100%;
	width: 3em;
	display: block;
	position: absolute;
	top: 0;
	-webkit-transform: skewX(-45deg) translateX(0);
	transform: skewX(-45deg) translateX(0);
	-webkit-transition: none;
	transition: none;
	opacity: 0;
	-webkit-animation: left-slide 2s infinite;
	animation: left-slide 2s infinite;
}
@keyframes left-slide {
	0% {
		left: -50%;
		opacity: 0.1;
	}
	50%,
	100% {
		left: 150%;
		opacity: 0.75;
	}
}

.overlay-menu{
    position: fixed;
    height: 100%;
    width: 100%;
    z-index: 1;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(0,0,0,.23);
    display: none;
}

/* Popup bên góc phải màn hình */
.block{
	display: block
}
.popup-notify{
	position: fixed;
	display: inline-block;
	bottom: 30px;
	right: 35px;
	background-image: linear-gradient(135deg, '.$primary_background.' 0%, '.$primary_hover.' 100%);
	min-width: 320px;
	border-radius: 5px;
}
.popup-notify>*:hover{
	color: white !important;
	opacity: .7 
}
.popup-notify>a{
	display: block;
	padding: 10px 20px;
	transition: .5s all;
	color: white;
}
.popup-notify>i{
	position: absolute;
	top: -30px;
	right: -20px;
	color: '.$primary_background.';
	padding: 5px;
	cursor: pointer;
	font-size: 25px
}
.popup-notify>i:hover{
	color: '.$primary_background.' !important;
}



/*Các phần khác*/
.hidden{display:none}

.text-inline{
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}
.wall{border-bottom: 1px solid '.$primary_background.';}
.hidden-printing{
	visibility: hidden;
}
@media print {
	.hidden-printing{
		visibility: visible !important;
	}
} 


.red{color:red}
.blue{color:blue}
.skyblue{color:skyblue}
.green{color:green}
.gray{color:gray}
.cyan{color:cyan}

.hover-red:hover{background-color:red}
.hover-blue:hover{background-color:blue}
.hover-skyblue:hover{background-color:skyblue}
.hover-green:hover{background-color:green}
.hover-gray:hover{background-color:gray}
.hover-cyan:hover{background-color:cyan}

.left{text-align: left}
.center{text-align: center}
.right{text-align: right}
.float-right{float: right}


.block{display: block;}
.pd-0{padding: 0px !important}
.pd-2{padding: 2px}
.pd-5{padding: 5px}
.pd-10{padding: 10px}
.pd-15{padding: 15px}
.pd-20{padding: 20px}
.pd-30{padding: 30px}
.pd-50{padding: 50px}

.bg{background-color:#fff}

.rm-radius{border-radius: 0 !important}
.radius{border-radius: 50% !important}
.input-circle{border-radius: 25px !important}

.flex{
	display: flex;
	flex-wrap: wrap;
}
.flex-nowrap{
	display: flex;
	flex-wrap: nowrap;
}
.flex-nowrap>*{
	flex-grow: 1
}
.flex-middle{
	align-items: center;
}
.flex-center{
	justify-content: center;
}

.flex-margin{padding: 0 20px 0 0}
.flex-margin:last-child{padding: 0 !important}

.width-5{width:5%}
.width-10{width:10%}
.width-15{width:15%}
.width-20{width:20%}
.width-25{width:25%}
.width-30{width:30%}
.width-33{width:33%}
.width-33x{width:33.33333%}
.width-34{width:34%}
.width-35{width:35%}
.width-40{width:40%}
.width-45{width:45%}
.width-50{width:50%}
.width-55{width:55%}
.width-60{width:60%}
.width-65{width:65%}
.width-70{width:70%}
.width-75{width:75%}
.width-80{width:80%}
.width-85{width:85%}
.width-90{width:90%}
.width-95{width:95%}
.width-100{width:100%}

.clearfix{clear:both}
.highcharts-credits{
	display: none !important
}

h1,h2,h3,h4,h5,h6{
	margin: 0;
	font-weight: initial;
}


.spin-effect{
	animation: spin 1s;
}

@keyframes spin{
	100% {transform:rotate(360deg);}
}
@keyframes rotate{
	100% {transform:rotateY(360deg);}
}


















/* Responsive */



@media(max-width: 767px){
	/*Màn hình nhỏ*/
	.hidden-small{display: none !important;}
	.flex-medium{
		display: block !important;
	}
	.flex-medium>*{
		width: auto !important
	}
	.width-50-small{width: 50% !important; max-width: 100% !important}
	.width-49-small{width: 49% !important; max-width: 100% !important}
	.width-100-small{width:100% !important; max-width: 100% !important}
	.heading-basic{
		font-size: '.($heading_basic_font_size-5).'px !important;
	}
}

@media(min-width: 768px) and (max-width: 1023px){
	/*Màn hình trung bình*/
	.hidden-medium{display: none !important;}
	.width-50-medium{width:50% !important; max-width: 100% !important}
	.width-100-medium{width:100% !important; max-width: 100% !important}
}

@media(max-width: 1023px){
	/*Màn hình nhỏ & trung bình*/
	.hidden-small-medium{display: none !important;}
	.flex-large{
		display: block !important;
	}
	.flex-large>*{
		width: auto !important
	}
	.flex-margin{
		padding:0 !important;
	}
	.medium-margin-bottom{
		margin-bottom: 10px !important
	}
	#header>.header-body{
	  padding: 0 0 0 15px;
	}
	#header{
		height: '.Storage::option("theme_header_height_mb").'px;
		line-height: '.Storage::option("theme_header_height_mb").'px;
	}
	.header-fixed{
		'.(Storage::option("theme_header_fixed")==1 ? 'height: '.Storage::option("theme_header_height_mb").'px' : 'display: none').';
	}
	#header .logo{
		max-height: '.(Storage::option("theme_header_height_mb")-22).'px
	}
	.width-50-small-medium{width:50% !important}
	.width-100-small-medium{width:100% !important}
	.navbar{
		position: fixed;
		z-index: 97;
		top: 0;
		right: 0;
		bottom: 0;
		width: 0;
		padding-bottom: 20px;
		max-width: 320px;
		transition: all .3s ease-in-out;
		'.($navbar_bg_white==1 ? '
			background: white !important;
		' : 
		(empty(Storage::option("theme_header_bg")) ? 'background: white !important;' : 'background: '.Storage::option("theme_header_bg").' !important;')
		).'
	}
	.navbar>ul{
		overflow-y: auto;
		height: 100%;
		display: block !important;
	}
	.navbar>ul>li{
		float: none;
		margin: 0 !important;
		line-height: normal;
		font-weight: bold;
	}
	.navbar li>a,
	.navbar li>span{
		display: block;
		width: 100% !important;
		padding: 15px;
		position: relative;
		font-size: 16px;
		'.($navbar_bg_white==1 ? '
			color: '.$general_link_color.' !important;
		' : '').';
	}
	'.($navbar_bg_white==1 ? '
		.navbar li>a:hover,
		.navbar li>span:hover,
		.navbar li:hover span{
			color: '.$primary_hover.' !important;
		}
		.navbar .line-run:after{
			display: none;
		}
	' : '').'
	.navbar li>div{
		position: relative;
		top: 2px;
		'.($navbar_bg_white==1 ? 'background: white !important;' : '').';
		box-shadow: none;
		font-weight: normal;
	}
	.navbar li>div>ul{
		display: block;
		margin-left: 15px;
		border-left: 1px solid #dfdfdf;
	}
	.navbar li>div>ul>li{
		width: 100% !important
	}
	.navbar li>div a{
		    padding: 10px;
	}
	.navbar-arrow-icon:after{
		content: "\f105";
		position: absolute;
		right: 8px;
		line-height: normal;
		transform: rotate(0deg) !important;
	}
	.navbar-item-opened .navbar-arrow-icon:after{
		content: "\f107" !important;
	}
	.nav-icon-mobile>i{
		padding: 10px; 
		cursor: pointer;
		font-size: 25px;
		display: inline-block;
		vertical-align: middle;
		text-decoration: none !important
	}

	/* Navbar blog */
	.blog-navbar-wrap{
		padding: 20px 10px
	}
	.blog-navbar-wrap .flex{
		display: block !important;
		position: relative
	}
	.blog-navbar-wrap .flex>div:last-child{
		position: absolute;
		right: 0;
		top: 50%;
		transform: translate(0, -50%);
	}
	.blog-navbar{
		position: absolute;
		top: 100%;
		width: 100%;
		transition: all .3s ease-in-out;
		display: none;
		background: white;
		z-index: 1;
		box-shadow: 0 2px 8px 0 rgba(0,0,0,.2);
	}
	.blog-navbar>ul{
		overflow-y: auto;
		height: 100%;
		display: block !important;
	}
	.blog-navbar>ul>li{
		float: none;
		margin: 0 !important;
		line-height: normal;
		padding: 0
	}
	.blog-navbar li>a,
	.blog-navbar li>span{
		display: block;
		width: 100% !important;
		padding: 15px;
		position: relative;
		font-size: 16px;
		'.($navbar_bg_white==1 ? '
			color: '.$general_link_color.' !important;
		' : '').';
	}
	'.($navbar_bg_white==1 ? '
		.blog-navbar li>a:hover,
		.blog-navbar li>span:hover,
		.blog-navbar li:hover span{
			color: '.$primary_hover.' !important;
		}
		.blog-navbar .line-run:after{
			display: none;
		}
	' : '').'
	.blog-navbar li>div{
		position: relative;
		top: 2px;
		'.($navbar_bg_white==1 ? 'background: white !important;' : '').';
		box-shadow: none;
	}
	.blog-navbar li>div>ul{
		display: block;
		margin-left: 15px;
		border-left: 1px solid #dfdfdf;
	}
	.blog-navbar li>div>ul>li{
		width: 100% !important
	}
	.blog-navbar li>div a{
		    padding: 10px;
	}
	.blog-nav-icon-mobile{
		padding: 10px 0
	}

	#header .input-search{
		display: none
	}
	#header .input-search>input:focus{
		width: 180px
	}
	.breadcrumb-outer{
		padding-left: 5px
	}
}


@media(min-width: 1024px){
	/*Màn hình lớn*/
	.hidden-large{display: none !important;}
	.width-50-large{width:50% !important}
	.width-100-large{width:100% !important}
	.navbar li>div>ul{
		background: rgba('.hexToRGB($navbar_sub_background_color).','.$navbar_sub_background_opacity.');
		color: '.$navbar_sub_color.'
	}
	.navbar li>div>ul a{
		color: '.$navbar_sub_color.' !important;
		font-size: 16px
	}
	.navbar li>div>ul a:hover{
		'.($navbar_hover_type == 'default' ? '
			color: '.$navbar_sub_hover.' !important;
		' : '').'
	}
	.navbar li>div>ul a:hover{
		'.($navbar_hover_type == 'underline' ? 'text-decoration: underline;' : '').'
	}
	'.($navbar_hover_type == 'default' ? '
		.navbar li>div>ul a{
			position: relative;
			display: block
		}
		.navbar li>div>ul a:after{
			display: block;
			position: absolute;
			left: 0;
			right: 0;
			bottom: 0;
			width: 0;
			height: 1px;
			overflow: hidden;
			border-bottom: 1px solid '.$navbar_sub_hover.';
			content: "";
			transition: all .3s;
			margin: auto
		}
		.navbar li>div>ul a:hover:after{width: 100%}
		' : '').'
	.margin-left-sm-30{
		margin-left: 30px !important
	}
}







'.$cssCustom.'

';
$content=cssMinifier($content);
$path=PUBLIC_ROOT."/assets/general/css";
if (!is_dir($path)) {
	mkdir($path, 0755, true);
}
file_put_contents($path."/style__complete.css", $content);//Tạo file CSS
echo $content;
}