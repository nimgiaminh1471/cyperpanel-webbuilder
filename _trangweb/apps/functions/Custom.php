<?php
//Thanh navbar
function createNavbar($data, $hoverEff=false, $outerClass="flex-nowrap"){
	$out='<nav class="navbar"><ul class="'.$outerClass.'">';
	$out .= '
	<li class="primary-bg hidden-large" style="height: '.Storage::option("theme_header_height_mb").'px;">
		<span class="block" style="position: relative">
			<a href="/" style="max-height: '.Storage::option("theme_header_height_mb").'px;">
				'.(empty(Storage::option("theme_header_logo")) ? ucwords(DOMAIN) : '<img class="logo" src="'.Storage::option("theme_header_logo").'" alt="'.ucwords(DOMAIN).'">').'
			</a>
			<span class="right-icon nav-icon-mobile" data-show="navbar">
				<i class="fa fa-times" style="color: white"></i>
			</span>
		</span>
	</li>';
	unset($data["_i_"]);
	if(PAGE_EDITOR){
		$data[]=["title"=>"", "link"=>"/admin?panelShow=General", "icon"=>"fa-edit", "onlyMobile"=>0];
	}
	foreach($data as $item){
		$out.='<li>';
		if(isset($item["html"])){
			//HTML
			$out.=$item["html"];
		}else if( isset($item["sub"]) && is_array($item["sub"]) ){
			//Sub
			$out.='<span class="'.($hoverEff ? 'line-run' : '').''.($item["onlyMobile"]==1 ? ' hidden-large' : '').'">'.(empty($item["icon"]) ? '' : '<i class="fa '.$item["icon"].'"></i> ').''.$item["title"].' <i class="navbar-arrow-icon"></i></span>';
			$out.='<div>';
			$itemColumn=empty($item["column"]) ? 1 : (int)$item["column"];
			$subItems=array_chunk($item["sub"], $itemColumn);
			foreach($subItems as $items){
				$out.='<ul>';
				foreach($items as $subItem){
					$out.='<li style="width: '.( 100/$itemColumn ).'%" class="'.($subItem["onlyMobile"]==1 ? ' hidden-large' : '').'"><a '.(empty($subItem["newTab"]) ? '' : 'target="_blank"').' class="'.(URI==$subItem["link"]?'navbar-item-actived ':'').''.($hoverEff ? 'line-run' : '').'" href="'.$subItem["link"].'">'.(empty($subItem["icon"]) ? '' : '<i class="fa '.$subItem["icon"].'"></i> ').''.$subItem["title"].'</a></li>';
				}
				$out.='</ul>';
			}
			$out.='</div>';
		}else if(!empty($item["link"])){
			//Link
			$out.='<a class="'.(URI==$item["link"]?'navbar-item-actived ':'').''.($hoverEff ? 'line-run' : '').''.($item["onlyMobile"]==1 ? ' hidden-large' : '').'" '.(empty($item["newTab"]) ? '' : 'target="_blank"').' href="'.$item["link"].'">'.(empty($item["icon"]) ? '' : '<i class="fa '.$item["icon"].'"></i> ').''.$item["title"].'</a>';
		}
		$out.='</li>';
	}
	if( permission("member") ){
		$out.='
		<li class="margin-left-sm-30"><a href="/admin/WebsiteList" style="font-weight: normal;">Trang quản trị</a></li>
		<li>
			<span>
				<a class="btn-gradient" href="/admin/WebsiteTemplate" style="font-size: 18px; border-radius: 25px; color: white !important;padding: 6px 16px; font-weight: normal;">Tạo website</a>
			</span>
		</li>
		';
	}else{
		$out.='
		<li class="margin-left-sm-30"><a class="modal-click" data-modal="login-box" style="font-weight: normal;">Đăng nhập</a></li>
		<li>
			<span>
				<a class="btn-gradient modal-click" data-modal="register-box" style="font-size: 18px; border-radius: 25px; color: white !important;padding: 6px 16px; font-weight: normal;">Dùng thử</a>
			</span>
		</li>
		';
	}
	$out.='</ul></nav>';
	return $out;
}

//Thanh navbar trang blog
function createBlogNavbar($data, $hoverEff=false, $outerClass="flex-nowrap"){
	$out='<nav class="blog-navbar"><ul class="'.$outerClass.'">';
	unset($data["_i_"]);
	if(PAGE_EDITOR){
		$data[]=["title"=>"", "link"=>"/admin?panelShow=General", "icon"=>"fa-edit", "onlyMobile"=>0];
	}

	foreach($data as $item){
		$out.='<li>';
		if(isset($item["html"])){
			//HTML
			$out.=$item["html"];
		}else if( isset($item["sub"]) && is_array($item["sub"]) ){
			//Sub
			$out.='<span class="'.($hoverEff ? 'line-run' : '').''.($item["onlyMobile"]==1 ? ' hidden-large' : '').'">'.(empty($item["icon"]) ? '' : '<i class="fa '.$item["icon"].'"></i> ').''.$item["title"].' <i class="navbar-arrow-icon"></i></span>';
			$out.='<div>';
			$itemColumn=empty($item["column"]) ? 1 : (int)$item["column"];
			$subItems=array_chunk($item["sub"], $itemColumn);
			foreach($subItems as $items){
				$out.='<ul>';
				foreach($items as $subItem){
					$out.='<li style="width: '.( 100/$itemColumn ).'%" class="'.($subItem["onlyMobile"]==1 ? ' hidden-large' : '').'"><a '.(empty($subItem["newTab"]) ? '' : 'target="_blank"').' class="'.(URI==$subItem["link"]?'':'').''.($hoverEff ? 'line-run' : '').'" href="'.$subItem["link"].'">'.(empty($subItem["icon"]) ? '' : '<i class="fa '.$subItem["icon"].'"></i> ').''.$subItem["title"].'</a></li>';
				}
				$out.='</ul>';
			}
			$out.='</div>';
		}else if(!empty($item["link"])){
			//Link
			$out.='<a class="'.(URI==$item["link"]?' ':'').''.($hoverEff ? 'line-run' : '').''.($item["onlyMobile"]==1 ? ' hidden-large' : '').'" '.(empty($item["newTab"]) ? '' : 'target="_blank"').' href="'.$item["link"].'">'.(empty($item["icon"]) ? '' : '<i class="fa '.$item["icon"].'"></i> ').''.$item["title"].'</a>';
		}
		$out.='</li>';
	}
	$out.='</ul></nav>';
	return $out;
}

function promotionTrial($params){
	extract($params);
	$primaryBG = Storage::setting("theme__primary_background");
	$link = empty($link) ?  (permission("member") ? '/admin/WebsiteList' : '/website') : $link;
	$title = empty($title) ? 'Tạo website chuyên nghiệp với '.ucfirst(DOMAIN) : $title;
	$description = empty($description) ? 'Trải nghiệm toàn bộ tính năng web với '.Storage::setting('builder_parameters_expired').' ngày sử dụng miễn phí.' : $description;
	$button = empty($button) ? 'Dùng thử miễn phí' : $button;
	$background = empty($background_image) ? "background-color: {$primaryBG}" : "background-image: url({$background_image})";
	$mainClass = empty($column) ? "promotion-trial-no-column" : "flex flex-medium flex-middle";
return <<<HTML
	<style type="text/css">
		.promotion-trial-no-column{
			text-align: center;
		}
		.promotion-trial-no-column>div{
			width: 100% !important
		}
		.promotion-trial-no-column>.center{
			padding: 0 10px 20px 10px
		}
	</style>
	<section style="{$background}; color: #FAFAFA; padding: 20px 0">
		<div class="main-layout">
			<div class="{$mainClass}">
				<div class="width-60 pd-20">
					<div style="font-size: 26px;">
						{$title}
					</div>
					<div style="font-size: 16px; line-height: 26px;margin-top: 10px">
						{$description}
					</div>
				</div>
				<div class="width-40 pd-20 center">
					<div>
						<a rel="nofollow" href="{$link}" class="btn-gradient fx-btn-blick" style="border-radius: 40px;margin-right: 5px; font-size: 17px; padding: 12px 25px;">
							{$button}
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>
HTML;
}

/*
 * Chạy lệnh exec
 */
function execPrint($command) {
	$out = '';
	$result = array();
	shell_exec($command, $result);
	foreach ($result as $line) {
		$out .='<div>'.$line.'</div>';
	}
	return $out;
}