<?php
function headingBanner($params){
	extract($params);
	$script = Assets::show("/assets/intro/service-diagram.js");
	$background_image = empty($background_image) ? Storage::setting("theme__heading_heading_banner") : $background_image;
	$backgroundImage = "background: url({$background_image})";
	$title = ( empty($title) ? '' : '
		<h1 class="center pd-20" style="padding-top: 30px; font-weight: normal;">
			'.$title.'
		</h1>
		');
	$background_color1 = $background_color1 ?? null;
	$background_color2 = $background_color2 ?? null;
	$background_color3 = $background_color3 ?? null;
	$background_opacity = $background_opacity ?? null;
	$heading_description = $heading_description ?? null;
	$search = $search ?? null;
	if( $search ){
	$heading_description .= '
		<div class="pd-15">
			<form action="/search" method="GET">
				<div class="input-search width-60">
					<input placeholder="'.__("Bạn cần hỗ trợ gì?").'" class="input" type="search" name="keyword" value="'.GET("keyword").'" required="" id="website-template-search" style="width: 100%;" >
					<button type="submit"><i class="fa fa-search"></i></button>
				</div>
			</form>
		</div>
		';
	}
	return <<<HTML
	{$script}
	<style type="text/css">
		html{
			background: white !important
		}
		/*#header{
			position: absolute;
		}*/
		.header-fixed{
			display: none
		}
		.heading-banner{
			position: relative;
			background-image: radial-gradient(ellipse farthest-side at 100% 100%, {$background_color1} 20%, {$background_color2} 50%, {$background_color3} 110%);
			padding: 210px 0 230px 0;
			text-align: center;
		}
		.heading-banner:before{
			content: "";
			{$backgroundImage};
			background-size: cover;
			background-repeat: no-repeat;
			width: 100%;
			height: 100%;
			position: absolute;
			top: 0;
			left: 0;
			opacity: {$background_opacity}
		}

		.heading-banner svg {
			position: absolute;
			right: 0%;
			bottom: -2px;
			width: 100%;
			height: 19.3vw;
			-webkit-transform-origin: bottom center;
			-ms-transform-origin: bottom center;
			transform-origin: bottom center;
			transform: scaleY(0.502926);
		}
		.heading-banner .main-desc{
			font-size: 27px;
			margin: 0 0 20px 0;
			color: #fff;
			text-transform: uppercase;
		}
		.heading-banner .small-desc{
			color: #fff;
			font-size: 18px;
			line-height: 30px;
		}
		@media (max-width: 768px){
			.heading-banner{
				padding: 90px 15px 60px 15px;
			}
			.heading-banner .main-desc{
				font-size: 22px
			}
			.heading-banner .small-desc{
				font-size: 16px
			}
		}
	</style>
	<section class="heading-banner">
		<div class="main-layout">
		<div>
			<h2 class="main-desc" data-aos="fade-right" data-aos-duration="1000">
				{$heading_title}
			</h2>
			<div class="small-desc" data-aos="fade-left" data-aos-duration="1000">
				{$heading_description}
			</div>
		</div>
		</div>
		<svg width="2144px" height="413px" viewBox="0 0 2144 413" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#ffffff" style="transform: scaleY(0.292427);">
		<path d="M783.562,299.622 C1223.094,622.888 1566.88,148.653 1733.15,59.122 C1899.42,-30.408 2024.26,39.5022186 2144,94.3702186 C2144,183.206999 2144,289.416926 2144,413 C714.666667,413 -2.27373675e-13,413 -2.27373675e-13,413 C5.87107918e-05,373.876705 5.87107918e-05,354.314891 -2.01338921e-13,354.314558 C229.353333,102.342186 490.540667,84.1113333 783.562,299.622 Z"></path>
		<path class="js-path-change" data-move="20" data-opacity-from="0.25" data-opacity-to="1" d="M903.562,284.618 C1343.094,607.884 1686.88,133.648 1853.15,44.118 C1967.48045,-17.4452378 2058.65217,-5.65514768 2143.38608,25.5569942 C2143.79668,73.7960674 2144.00134,202.943736 2144.00007,413 C714.666689,413 0,413 0,413 L111.25,348.344 C111.25,348.345 464.031,-38.648 903.562,284.618 Z" opacity="0.25" style="transform: translateX(8.15146%); opacity: 0.55568;"></path>
		<path class="js-path-change" data-move="-20" data-opacity-from="0.1" data-opacity-to="1" d="M615.374,252.382 C1012.234,626.804 1411.25,198 1587.194,129.4 C1763.134,60.8 1871.554,140.214 1983.716,209.265 C2058.496,255.299667 2111.924,323.211333 2144,413 C716.226662,413 2.33999362,413 2.33999362,413 C0.779997875,300.541688 0.779997875,201.778057 2.33999362,116.709108 C155.216449,58.7232948 385.371638,35.383156 615.374,252.382 Z" opacity="0.1" style="transform: translateX(-8.15146%); opacity: 0.466816;"></path>
		</svg>
	</section>
	{$title}
HTML;
}

function headingBanner2($params){
	extract($params);
	$script = Assets::show("/assets/intro/service-diagram.js");
	$buttonColor = Storage::setting("theme__form_gradient_background1");
	$title = ( empty($title) ? '' : '
		<h1 class="center pd-20" style="padding-top: 30px; font-weight: normal;">
			'.$title.'
		</h1>
		');
	$backgroundImage = empty($background_image) ? "" : "background: url({$background_image})";
	$main_title = nl2br($main_title);
	$heading_description = nl2br($heading_description);
	$advantages = '<div class="flex">';
	foreach( explode(PHP_EOL, trim($main_advantages) ) as $item){
		$advantages .= '<div><i class="fa fa-check-circle"></i>  '.$item.'</div>';
	}
	$advantages .= '</div>';
	$main_description = $advantages.'<div style="margin: 40px 0 20px 0">'.$main_description.'</div>';
	return <<<HTML
	{$script}
	<style type="text/css">
		html{
			background: white !important
		}
		/*#header{
			position: absolute;
		}*/
		.header-fixed{
			display: none
		}
		.heading-banner{
			position: relative;
			padding: 100px 0;
			background-image: radial-gradient(ellipse farthest-side at 100% 100%, {$background_color1} 20%, {$background_color2} 50%, {$background_color3} 110%); 
		}
		.heading-banner:before{
			content: "";
			{$backgroundImage};
			background-size: cover;
			background-repeat: no-repeat;
			width: 100%;
			height: 100%;
			position: absolute;
			top: 0;
			left: 0;
			opacity: {$background_opacity}
		}
		.heading-banner:after {
			position: absolute;
			left: -5%;
			right: 0;
			bottom: -80px;
			height: 200px;
			width: 120%;
			transform: rotate(-5deg);
			background: #fff;
			content: "";
		}
		.heading-banner .main-desc-heading{
			font-size: 26px;
			margin: 0 0 30px 0;
			color: #fff;
			text-transform: uppercase;
			font-weight: bold;
		}
		.heading-banner .small-desc{
			color: #fff;
			font-size: 16px;
			line-height: 30px;
		}
		.heading-banner .small-desc>.flex>div{
			width: 50%;
			padding-right: 5px;
		}
		.heading-banner .fa-check-circle{
			min-width: 20px;
			color: #3ECF8E
		}
		.service-button button,
		.service-button a {
		    border-radius: 35px;
		    padding: 12px 25px;
		    font-size: 18px;
		    position: relative;
		    margin-left: 10px;
		    background: {$buttonColor} !important;
		}
		.heading-banner svg {
			vertical-align: middle;
		}
		.service-button button:before,
		.service-button a:before {
		    content: "";
		    display: block;
		    background-color: inherit;
		    border-radius: inherit;
		    position: absolute;
		    z-index: -1;
		    top: 0;
		    right: 0;
		    bottom: 0;
		    left: 0;
		    opacity: 0.75;
		    animation: waves 1s infinite;
		}
		.heading-banner header{
			padding: 80px 0 100px 0;
		}
		.heading-banner h1{
			font-size: 26px;
			font-weight: 700;
			color: #fff;
			text-align: center;
			text-transform: uppercase;
		}
		.heading-banner .heading-description{
			font-size: 16px;
			color: #fff;
			line-height: 24px;
			margin-top: 30px;
		}
		@media (max-width: 768px){
			.heading-banner{
				padding: 90px 15px 60px 15px;
			}
			.heading-banner header h1{
				font-size: 20px
			}
			.heading-banner h2{
				font-size: 18px;
				text-align: center;
			}
			.heading-banner .main-desc-heading{
				font-size: 22px;
			}
			.heading-banner .small-desc{
				font-size: 15px;
			}
			.heading-banner .small-desc>.flex>div{
				width: 100%
			}
		}
		@media (max-width: 1023px){
			.heading-banner:after {
				width: 100%
			}
		}
		@keyframes waves {
		    0% {
		        transform: scale(1);
		    }
		    100% {
		        transform: scale(1.3);
		        opacity: 0;
		    }
		}
	</style>
	<section class="heading-banner">
		<div class="main-layout">
			<div class="flex flex-medium flex-middle">
				<div class="width-55">
					<h2 class="main-desc-heading" data-aos="fade-right" data-aos-duration="1000">
						{$main_title}
					</h2>
					<div class="small-desc" data-aos="fade-left" data-aos-duration="1000">
						{$main_description}
						<div class="service-button">	
							<a href="{$button_link}" class="btn-gradient">
								{$button_label}
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 26 27" fill="none" class="icon icon-pos-right">
									<path d="M25.7447 21.9916L20.57 16.8168L24.2618 14.6845C24.5502 14.5178 24.7181 14.2012 24.6941 13.8689C24.6701 13.5366 24.4584 13.2472 24.149 13.1239L7.68685 6.55708C7.36414 6.42821 6.99565 6.50412 6.74989 6.74978C6.50414 6.99562 6.42823 7.36403 6.55702 7.68674L13.1228 24.1508C13.2462 24.4602 13.5356 24.672 13.8679 24.696C14.2002 24.7201 14.517 24.5522 14.6835 24.2637L16.8157 20.5719L21.99 25.7468C22.153 25.9099 22.3744 26.0015 22.6049 26.0015C22.8356 26.0015 23.0567 25.9099 23.2197 25.7468L25.7448 23.2214C26.0843 22.8818 26.0843 22.3313 25.7447 21.9916Z" fill="white"></path>
									<path d="M3.81648 2.58679C3.4769 2.24748 2.92652 2.24739 2.58677 2.58679C2.24728 2.92637 2.24728 3.47692 2.58677 3.8165L4.51874 5.74847C4.68848 5.91821 4.91093 6.00313 5.13355 6.00313C5.35599 6.00313 5.57853 5.91821 5.74827 5.74847C6.08785 5.40889 6.08785 4.85843 5.74827 4.51885L3.81648 2.58679Z" fill="white"></path>
									<path d="M4.47131 8.59799C4.47131 8.1179 4.08201 7.72852 3.60175 7.72852H0.869557C0.389388 7.72852 0 8.11782 0 8.59799C0 9.07815 0.389301 9.46754 0.869557 9.46754H3.60175C4.08201 9.46746 4.47131 9.07815 4.47131 8.59799Z" fill="white"></path>
									<path d="M4.11886 11.2814L2.18662 13.2135C1.84713 13.5531 1.84713 14.1036 2.18662 14.4431C2.35646 14.6129 2.57899 14.6978 2.80143 14.6978C3.02388 14.6978 3.2465 14.6129 3.41624 14.4431L5.34839 12.5111C5.68797 12.1715 5.68797 11.621 5.34839 11.2814C5.0089 10.9421 4.45861 10.942 4.11886 11.2814Z" fill="white"></path>
									<path d="M8.5975 4.47148C9.07767 4.47148 9.46705 4.08218 9.46705 3.60193V0.86947C9.46705 0.389301 9.07775 0 8.5975 0C8.11742 0 7.72803 0.389213 7.72803 0.86947V3.60201C7.72803 4.08218 8.11733 4.47148 8.5975 4.47148Z" fill="white"></path>
									<path d="M11.8954 5.60351C12.118 5.60351 12.3404 5.51859 12.5102 5.34885L14.4421 3.41687C14.7816 3.0773 14.7816 2.52674 14.4421 2.18717C14.1025 1.84777 13.5519 1.84777 13.2124 2.18717L11.2806 4.11923C10.941 4.4588 10.941 5.00936 11.2806 5.34885C11.4504 5.51859 11.6728 5.60351 11.8954 5.60351Z" fill="white"></path>
								</svg>
							</a>
						</div>
					</div>
				</div>
				<div class="width-45 hidden-small">
					<img src="{$banner_image}">
				</div>
			</div>
			<header data-aos="fade-up" data-aos-duration="2000">
				<h1><span>{$heading_title}</span></h1>
				<div class="heading-description center"><p>{$heading_description}</p>
				</div>
			</header>
		</div>
	</section>
	{$title}
HTML;
}
