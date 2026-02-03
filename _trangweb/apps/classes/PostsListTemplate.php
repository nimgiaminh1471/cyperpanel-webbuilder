<?php
/*
# Danh sách bài viết
*/
namespace classes;
use Storage, Widget, Assets;
class PostsListTemplate{
	public static $count=0;

	//Kiểu cổ điển 
	public static function classic($posts, $config=[]){
		self::$count++;
		extract($config);
		$out="";
		$class="posts-classic-".self::$count;
		if(permission("admin")){
			//Tạo style
			$style='
				.'.$class.'{
					display: block;
					background: '.(empty($itemBackground) ? 'transparent' : $itemBackground).';
					padding: '.$itemPadding.';
					'.(empty($itemBorder) ? '' : 'border-bottom: 1px solid '.$itemBorder.';').'
					font-size: '.$itemFontSize.'px;
					'.(empty($itemColor) ? '' : 'color: '.$itemColor.' !important;').'
				}
				.'.$class.':hover{
					'.(empty($itemHover) ? '' : 'color: '.$itemHover.' !important;').'
				}
				.'.$class.'-title-basic{
					font-weight: initial;
					color: '.(empty($titleColor) ? Storage::setting("theme__primary_background") : $titleColor).';
				}
				.'.$class.'-title-basic-line{
					height: 1px;
					background: '.(empty($titleColor) ? Storage::setting("theme__primary_background") : $titleColor).';
					width: 100%;
					flex: 1 1;
					margin-left: 5px
				}
			';
			$out.=Widget::css($style);
		}
		$out.=$empty??$header??"";
		if(empty($posts[0]) && empty($info)){
			return empty($out) ? '<div class="alert-info">Hiện tại chuyên mục này chưa có nội dung, xin vui lòng đăng bài viết</div>' : $out;
		}

		//Tiêu đề
		if(!empty($title)){
			if($titleClass=="heading-basic"){
				$out.='
				<h2 style="align-items: baseline" class="flex flex-middle '.$class.'-title-basic '.($titleClass??'heading-simple').' '.($titleAlign??'left').'">
					<span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span>
					<span class="'.$class.'-title-basic-line"></span>
				</h2>
				';
			}else{
				$out.='
				<h2 class="'.$class.'-title '.($titleClass??'heading-simple').' '.($titleAlign??'left').'">
					<span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span>
				</h2>
				';
			}
		}
		$out.=$info??"";
		$out.=$description??"";

		//Danh sách bài viết
		foreach($posts as $p){
			$storage=unserialize($p->storage);
			$out.='<a title="'.$p->title.'" style="'.(empty($storage["linkColor"]) ? '' : 'color: '.$storage["linkColor"]).'" class="'.$class.' text-inline" href="/'.$p->link.'">'.(empty($postIcon) ? '' : '<i class="fa '.$postIcon.'"></i> ').''.(empty($storage["name"]) ? $p->title : $storage["name"]).'</a>';
		}

		//Nút xem thêm
		if(!empty($seeMoreTitle)){
			$out.='
			<div class="center '.$class.'">
				<a href="/posts-categories/'.$seeMoreLink.'" class="see-more">'.$seeMoreTitle.'</a>
			</div>
			';
		}else if($paginate==1){
			//Phân trang
			$out.=$posts->links([
				"class"=>"center $class"
			]);
		}
		$out.=''.(empty($breadcrumb) ? '' : '<div style="'.(empty($itemBorder) || empty($posts->links()) ? '' : 'border-top: 1px solid '.$itemBorder.';').'"></div><div style="padding: 5px 10px; background: '.$breadcrumbBG.'">'.$breadcrumb.'</div>');
		return '<section class="'.$class.'-outer '.($ajaxLoad==1 ? 'paginate-ajax' : '').'" id="'.$class.'-outer">'.$out.'</section>';
	}

	//Dạng lưới
	public static function flex($posts, $config=[]){
		self::$count++;
		extract($config);
		$out="";
		$class="posts-flex-".self::$count;
		if(permission("admin")){
			//Tạo tyle
			$style='';
			$style.='
			.'.$class.'{
				'.($itemColumn==1 ? 'display: block !important;' : '').'
				max-height: '.($itemMaxHeight ?? 'auto').';
				overflow: auto
			}
			.'.$class.'::-webkit-scrollbar-track {
				-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
				background-color: #f5f5f5;
				border-radius: 3px;
			}

			.'.$class.'::-webkit-scrollbar {
				width: 6px;
				background-color: #f5f5f5;
			}

			.'.$class.'::-webkit-scrollbar-thumb {
				background-color: '.Storage::setting("theme__primary_background").';
				border-radius: 3px;
			}
			.'.$class.'>div:hover a{
				'.(empty($itemHover) ? '' : 'color: '.$itemHover.' !important;').';
			}
			.'.$class.'>div{
				'.($itemColumn==1 ? 'width: auto !important' : '').'
			}
			.'.$class.' .post-flex-item{
				'.(empty($itemBorder) ? '' : 'border-bottom: 1px solid '.$itemBorder.';').'
				background: '.(empty($itemBackground) ? 'transparent' : $itemBackground).';
				height: '.($flexImgHeight+20).'px;
			}
			.'.$class.' .flex{
				padding: '.$itemPadding.'
			}
			.'.$class.' .posts-flex-title{
				font-weight: bold;
				line-height: 1.6
			}
			.'.$class.' .posts-flex-poster{
				width: '.$flexImgWidth.'px;
				height: '.$flexImgHeight.'px
			}
			.'.$class.' .posts-flex-text{
				width: calc(100% - '.$flexImgWidth.'px);
			}
			.'.$class.' .flex>a{
				'.(empty($itemColor) ? '' : 'color: '.$itemColor.' !important;').'
				font-size: '.$itemFontSize.'px;
			}
			.'.$class.' .flex>a>img{
				border-radius: '.$itemImgRadius.'%;
			}
			.'.$class.' .posts-flex-desc,
			.'.$class.' .posts-flex-time{
				font-size: '.$itemDescSize.'px;
				color: '.$itemDescColor.';
				padding-top: 2px;
			}
			@media(max-width: 767px){
				.'.$class.'>div{
					height: '.($flexImgHeight+20).'px;
				}
				.'.$class.' .posts-flex-poster{
					width: '.($flexImgWidth-20).'px;
					height: '.($flexImgHeight-20).'px
				}
				.'.$class.' .posts-flex-text{
					width: calc(100% - '.($flexImgWidth-20).'px);
				}
			}
			';
			$out.=Widget::css($style);
		}
		$out.=$empty??$header??"";
		if(empty($posts[0]) && empty($info)){
			return empty($out) ? '<div class="alert-info">Hiện tại chuyên mục này chưa có nội dung, xin vui lòng đăng bài viết</div>' : $out;
		}

		//Tiêu đề
		if(!empty($title)){
			$out.='
			<h2 class="'.$class.'-title '.($titleClass??'heading-simple').' '.($titleAlign??'left').'">
				<span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span>
			</h2>
			';
		}
		$out.=$info??"";
		$out.=$description??"";


		//Danh sách bài viết
		$out.='<div class="flex flex-medium posts-flex '.$class.'">';
		foreach($posts as $p){
			$storage=unserialize($p->storage);
			$out.='
			<div class="width-50">
				<div class="post-flex-item">
					<div'.($itemTitleInline==1 ? ' title="'.$p->title.'"' : '').' class="flex flex-middle">
						<a href="/'.$p->link.'" class="posts-flex-poster">
							<img src="'.(empty($storage["poster"]["small"]) ? '/assets/general/images/no-image.png' : $storage["poster"]["small"]).'" alt="'.$p->title.'" />
						</a>
						<a href="/'.$p->link.'" class="posts-flex-text">
							<span class="posts-flex-title'.($itemTitleInline==1 ? ' text-inline' : '').'" style="'.(empty($storage["linkColor"]) ? '' : 'color: '.$storage["linkColor"]).'">'.(empty($storage["name"]) ? $p->title : $storage["name"]).'</span>
							'.($itemTime==1 ? '<span class="text-inline posts-flex-time">'.$cate[$p->parent].' - '.dateText(timestamp($p->updated_at)).'</span>' : '').'
							'.($itemDescLeng>0 ? '<span class="posts-flex-desc'.($itemDescInline==1 ? ' text-inline' : '').'">'.cutWords( $storage["description"]??$p->content, $itemDescLeng, "...").'</span>' : '').'
						</a>
					</div>
				</div>
			</div>
			';
		}
		$out.='</div>';

		//Nút xem thêm
		if(!empty($seeMoreTitle)){
			$out.='
			<div class="pd-10 center '.$class.'">
				<a href="/posts-categories/'.$seeMoreLink.'" class="see-more">'.$seeMoreTitle.'</a>
			</div>
			';
		}else if($paginate==1){
			//Phân trang
			$out.=$posts->links([
				"class"=>"center $class"
			]);
		}

		//Link tới chuyên mục
		$out.=''.(empty($breadcrumb) ? '' : '<div style="'.(empty($itemBorder) || empty($posts->links()) ? '' : 'border-top: 1px solid '.$itemBorder.';').'"></div><div style="padding: 5px 10px; background: '.$breadcrumbBG.'">'.$breadcrumb.'</div>');
		return '<section class="'.$class.'-outer '.($ajaxLoad==1 ? 'paginate-ajax' : '').'" id="'.$class.'-outer">'.$out.'</section>';
	}

	//Tin tức(nổi bật bài đầu tiên)
	public static function news($posts, $config=[]){
		self::$count++;
		extract($config);
		$out="";
		$class="posts-news-".self::$count;
		if(permission("admin")){
			//Tạo tyle
			$style='';
			$style.='
			.'.$class.'-title-basic{
				font-weight: initial;
				margin: 0 5px;
				color: '.(empty($titleColor) ? Storage::setting("theme__primary_background") : $titleColor).';
			}
			.'.$class.'-title-basic>a{
				color: '.(empty($titleColor) ? Storage::setting("theme__primary_background") : $titleColor).';
			}
			.'.$class.'-title-basic-line{
				height: 1px;
				background: '.(empty($titleColor) ? Storage::setting("theme__primary_background") : $titleColor).';
				width: 100%;
				flex: 1 1;
				margin-left: 5px
			}
			.'.$class.'-first>div{
				margin: 5px
			}
			.'.$class.'-first>div>a{
				position: relative;
				overflow: hidden;
				display: block;
				color: '.(empty($itemColor) ? '' : ' '.$itemColor.'').' !important;
				background: '.(empty($itemBackground) ? 'transparent' : $itemBackground).';
				'.(empty($itemBorder) ? '' : 'box-shadow: 0 2px 3px '.$itemBorder.';').'
			}
			.'.$class.'-first>div>a:hover{
				'.(empty($itemHover) ? '' : 'color: '.$itemHover.' !important;').';
			}
			.'.$class.'-first-poster{
				display: block;
				overflow: hidden;
				width: 100%;
				height: '.$newsLargePosterHeight.'px;
			}
			.'.$class.'-first>div>a img{
				object-fit: cover;
				width: 100%;
				height: 100%;
			}
			.'.$class.'-first-title{
				display: block;
				padding: 15px;
				'.($gridDescLeng>0 ? 'padding-bottom: 0' : '').';
				font-size: '.$itemFontSize.'px;
				font-weight: bold;
				background: '.(empty($itemBackground) ? 'transparent' : $itemBackground).';
			}
			.'.$class.'-first-description{
				display: block;
				padding: 10px 15px;
				background: '.(empty($itemBackground) ? 'transparent' : $itemBackground).';
				line-height: 1.5;
				overflow: hidden;
				font-size: '.$newsDescSize.'px;
				color: '.$newsDescColor.'
			}

			.'.$class.'>div:hover a{
				'.(empty($itemHover) ? '' : 'color: '.$itemHover.' !important;').';
			}

			.'.$class.' .posts-news-last>div{
				margin: 5px;
				'.(empty($itemBorder) ? '' : 'box-shadow: 0 2px 3px '.$itemBorder.';').'
				background: '.(empty($itemBackground) ? 'transparent' : $itemBackground).';
				height: '.($newsImgHeight+20).'px;
			}
			.'.$class.' .flex{
				padding: '.$itemPadding.'
			}
			.'.$class.' .posts-news-title{
				font-weight: bold
			}
			.'.$class.' .posts-news-text{
				width: calc(100% - '.$newsImgWidth.'px);
				padding-left: 8px;
			}
			.'.$class.' .flex>a{
				'.(empty($itemColor) ? '' : 'color: '.$itemColor.' !important;').'
				font-size: '.$itemFontSize.'px;
			}
			.'.$class.' .posts-news-poster>img{
				border-radius: '.$newsImgRadius.'%;
				width: '.$newsImgWidth.'px;
				height: '.$newsImgHeight.'px;
				object-fit: cover;
			}
			.'.$class.' .posts-news-desc,
			.'.$class.' .posts-news-time{
				font-size: '.$newsDescSize.'px;
				color: '.$newsDescColor.';
				display: block;
			}
			.'.$class.' .posts-news-time{
				padding-bottom: 1px;
			}
			@media(max-width: 767px){
				.'.$class.' .posts-news-last>div{
					height: '.($newsImgHeight+20).'px;
				}
				.'.$class.' .posts-news-poster{
					width: '.$newsImgWidth.'px;
					height: '.$newsImgHeight.'px
				}
				.'.$class.' .posts-news-text{
					width: calc(100% - '.$newsImgWidth.'px);
				}
			}
			';
			$out.=Widget::css($style);
		}
		$out.=$empty??$header??"";
		if(empty($posts[0]) && empty($info)){
			return empty($out) ? '<div class="alert-info">Hiện tại chuyên mục này chưa có nội dung, xin vui lòng đăng bài viết</div>' : $out;
		}

		//Tiêu đề
		if(!empty($title)){
			if($titleClass=="heading-basic"){
				$out.='
				<h2 style="align-items: baseline" class="flex flex-middle '.$class.'-title-basic '.($titleClass??'heading-simple').' '.($titleAlign??'left').'">
					'.(empty($seeMoreLink) ? '' : '<a href="/posts-categories/'.$seeMoreLink.'">').'
					<span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span>
					'.(empty($seeMoreLink) ? '' : '</a>').'
					<span class="'.$class.'-title-basic-line"></span>
				</h2>
				';
			}else{
				$out.='
				<h2 class="'.$class.'-title '.($titleClass??'heading-simple').' '.($titleAlign??'left').'">
					'.(empty($seeMoreLink) ? '' : '<a href="/posts-categories/'.$seeMoreLink.'">').'
					<span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span>
					'.(empty($seeMoreLink) ? '' : '</a>').'
				</h2>
				';
			}
		}
		$out.=$info??"";
		$out.=$description??"";


		//Danh sách bài viết
		$out.='<div class="flex flex-medium posts-news '.$class.'">';
		$out.='<div class="width-50">';
		$firstPost=$posts[0]??[];
		if( !empty($firstPost) ){
			$storage=unserialize($firstPost->storage);
			$out.='
			<div class="'.$class.'-first">
				<div>
					<a style="" href="/'.$firstPost->link.'">
						<span class="'.$class.'-first-poster">
							<img src="'.(empty($storage["poster"]["small"]) ? '/assets/general/images/no-image.png' : $storage["poster"]["small"]).'" alt="'.$firstPost->title.'" />
						</span>
						<span class="'.$class.'-first-title" style="'.(empty($storage["linkColor"]) ? '' : 'color: '.$storage["linkColor"]).'">'.(empty($storage["name"]) ? $firstPost->title : $storage["name"]).'</span>
						<span class="text-inline '.$class.'-first-description">'.$cate[$firstPost->parent].' - '.dateText(timestamp($firstPost->updated_at)).'</span>
					</a>
				</div>
			</div>
			';
		}
		$out.='</div>';
		$out.='<div class="width-50 posts-news-last">';
		unset($posts[0]);
		foreach($posts as $p){
			$storage=unserialize($p->storage);
			$out.='
			<div>
				<div'.($itemTitleInline==1 ? ' title="'.$p->title.'"' : '').' class="flex flex-middle">
					<a href="/'.$p->link.'" class="posts-news-poster">
						<img src="'.(empty($storage["poster"]["small"]) ? '/assets/general/images/no-image.png' : $storage["poster"]["small"]).'" alt="'.$p->title.'" />
					</a>
					<a href="/'.$p->link.'" class="posts-news-text">
						<span class="posts-news-title" style="'.(empty($storage["linkColor"]) ? '' : 'color: '.$storage["linkColor"]).'">'.(empty($storage["name"]) ? $p->title : $storage["name"]).'</span>
						'.($itemTime==1 ? '<span class="text-inline posts-news-time">'.$cate[$p->parent].' - '.dateText(timestamp($p->updated_at)).'</span>' : '').'
					</a>
				</div>
			</div>
			';
		}
		$out.='</div>';
		$out.='</div>';

		//Nút xem thêm
		if($paginate==1){
			//Phân trang
			$out.=$posts->links([
				"class"=>"center $class"
			]);
		}

		//Link tới chuyên mục
		$out.=''.(empty($breadcrumb) ? '' : '<div style="'.(empty($itemBorder) || empty($posts->links()) ? '' : 'border-top: 1px solid '.$itemBorder.';').'"></div><div style="padding: 5px 10px; background: '.$breadcrumbBG.'">'.$breadcrumb.'</div>');
		return '<section class="'.$class.'-outer '.($ajaxLoad==1 ? 'paginate-ajax' : '').'" id="'.$class.'-outer">'.$out.'</section>';
	}

	//Kiểu album
	public static function album($posts, $config=[]){
		self::$count++;
		extract($config);
		$out="";
		$class="posts-album-".self::$count;
		if(permission("admin")){
			//Tạo style
			$textBG=hex2rgb($textBG);
			$style='
				.'.$class.'{
					display: block;
					background: transparent;
					overflow: hidden;
					text-align: justify;
					'.($albumType=="default" ? 'padding-bottom: '.$itemPadding.';' : '').'
					padding: 40px 10px;
					background: white;
				}
				.'.$class.'-title-basic{
					font-weight: initial;
					color: '.(empty($titleColor) ? Storage::setting("theme__primary_background") : $titleColor).';
				}
				.'.$class.'-title-basic-line{
					height: 1px;
					background: '.(empty($titleColor) ? Storage::setting("theme__primary_background") : $titleColor).';
					width: 100%;
					flex: 1 1;
					margin-left: 5px
				}
				.'.$class.'>a{
					position: relative;
					width: 100%;
					height: '.$albumItemHeightLg.'px;
					overflow: hidden;
					display: block;
					font-size: '.$itemFontSize.'px;
					color: '.(empty($itemColor) ? 'white' : ' '.$itemColor.'').' !important;
					background-color: rgba('.$textBG["r"].','.$textBG["g"].','.$textBG["b"].','.$textBGOpacity.');
					border-radius: 10px
				}
				.'.$class.'>a:hover{
					'.(empty($itemHover) ? '' : 'color: '.$itemHover.' !important;').';
					background-color: black !important;
				}
				.'.$class.'>a:hover img{
					opacity: '.$albumHoverOpacity.';
					'.($albumHoverZoom==1 ? 'transform: scale(1.1);' : '').'
				}
				.'.$class.'>a>img{
					object-fit: cover;
					width: 100%;
					height: 100%;
					transition: .5s all;
				}
				.'.$class.'>a>span{
					position: absolute;
					bottom: 0;
					left: 0;
					width: 100%;
					background-color: rgba('.$textBG["r"].','.$textBG["g"].','.$textBG["b"].','.$textBGOpacity.');
					padding: 10px
				}
				.'.$class.'-outer .slider-btn-next{
					opacity: .9;
					right: 10px
				}
				.'.$class.'-outer .slider-btn-prev{
					opacity: .9;
					left: 10px
				}
				.'.$class.'-outer .slider-btn-next:after,
				.'.$class.'-outer .slider-btn-prev:after{
					background: white;
					display: inline-block;
					padding: 20px;
					color: black;
					border-radius: 5px
				}
				@media(min-width: 1024px){
					.'.$class.'{
						width: '.(100/$columnAmountLg).'%;
					}
				}
				@media(min-width: 768px) and (max-width: 1023px){
					.'.$class.'{
						width: '.(100/$columnAmountMd).'%;
					}
					.'.$class.'>a{
						height: '.$albumItemHeightMd.'px !important;
					}
				}
				@media(max-width: 767px){
					.'.$class.'{
						width: '.(100/$columnAmountSm).'%;
						'.($columnAmountSm==1 ? 'padding-bottom: 1px' : '').'
					}
					.'.$class.'>a{
						height: '.$albumItemHeightSm.'px !important;
					}
				}
			';
			$out.=Widget::css($style);
		}
		$out.=$empty??$header??"";
		if(empty($posts[0]) && empty($info)){
			return empty($out) ? '<div class="alert-info">Hiện tại chuyên mục này chưa có nội dung, xin vui lòng đăng bài viết</div>' : $out;
		}

		//Tiêu đề
		if(!empty($title)){
			if($titleClass=="heading-basic"){
				$out.='
				<h2 style="align-items: baseline" class="flex flex-middle '.$class.'-title-basic '.($titleClass??'heading-simple').' '.($titleAlign??'left').'">
					<span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span>
					<span class="'.$class.'-title-basic-line"></span>
				</h2>
				';
			}else{
				$out.='
				<h2 class="'.$class.'-title '.($titleClass??'heading-simple').' '.($titleAlign??'left').'">
					<span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span>
				</h2>
				';
			}
		}
		$out.=$info??"";
		$out.=$description??"";
		
		if($albumType=="default"){
			//Hiện dạng danh sách
			$out.='<div class="flex" style="margin-bottom:-'.$itemPadding.'">';
			$i=0;
			foreach($posts as $p){
				$storage=unserialize($p->storage);
				$out.='
				<div class="'.$class.'">
					<a style="'.(empty($storage["linkColor"]) ? '' : 'color: '.$storage["linkColor"]).'" href="/'.$p->link.'">
						<img src="'.(empty($storage["poster"]["small"]) ? '/assets/general/images/no-image.png' : $storage["poster"]["small"]).'" alt="'.$p->title.'" />
						<span>'.(empty($storage["name"]) ? $p->title : $storage["name"]).'</span>
					</a>
				</div>
				';
				$i++;
			}
			$out.='</div>';
		}else if(is_array($posts)){
			//Hiện dạng slider ảnh
			if( device()=="desktop" ){
				$columnAmount=$columnAmountLg;
			}else if( device()=="tablet" ){
				$columnAmount=$columnAmountMd;
			}else{
				$columnAmount=$columnAmountSm;
			}
			$out.='
			<div class="slider" style="width:100%;max-height: 550px">
				<ul class="slider-basic" data-autoplay="'.$albumSliderPlay.'">';
				$i=0;
				foreach(array_chunk($posts, $columnAmount ) as $items){
					$out.='<li><div class="flex">';
					foreach($items as $p){
						$storage=unserialize($p->storage);
						$out.='
						<div class="'.$class.'">
							<a style="'.(empty($storage["linkColor"]) ? '' : 'color: '.$storage["linkColor"]).'" href="/'.$p->link.'">
								<img src="'.(empty($storage["poster"]["small"]) ? '/assets/general/images/no-image.png' : $storage["poster"]["small"]).'" alt="'.$p->title.'" />
								<span>'.(empty($storage["name"]) ? $p->title : $storage["name"]).'</span>
							</a>
						</div>';
					}
					$out.='</div></li>';
				}
						
				$out.='</ul>
					<i class="slider-btn-prev"></i>
					<i class="slider-btn-next"></i>
				</div>
			';
			Assets::footer("/assets/slider/script.js","/assets/slider/style__complete.css");
		}
		//Nút xem thêm
		if(!empty($seeMoreTitle)){
			$out.='
			<div class="center pd-10">
				<a href="/posts-categories/'.$seeMoreLink.'" class="see-more">'.$seeMoreTitle.'</a>
			</div>
			';
		}else if($paginate==1){
			//Phân trang
			$out.=$posts->links([
				"class"=>"center"
			]);
		}

		//Link tới chuyên mục
		$out.=''.(empty($breadcrumb) ? '' : '<div style="'.(empty($itemBorder) || empty($posts->links()) ? '' : 'border-top: 1px solid '.$itemBorder.';').'"></div><div style="padding: 5px 10px; background: '.$breadcrumbBG.'">'.$breadcrumb.'</div>');
		return '<section class="'.$class.'-outer '.($ajaxLoad==1 ? 'paginate-ajax' : '').'" id="'.$class.'-outer">'.$out.'</section>';
	}

	//Kiểu danh sách lưới
	public static function grid($posts, $config=[]){
		self::$count++;
		extract($config);
		$out="";
		$class="posts-grid-".self::$count;
		if(permission("admin")){
			//Tạo style
			$textBG=hex2rgb($textBG);
			$style='
				.'.$class.'{
					display: block;
					overflow: hidden;
					padding-bottom: '.$itemPadding.';
					padding-left: '.$itemPadding.';
				}
				.'.$class.'-title-basic{
					font-weight: initial;
					color: '.(empty($titleColor) ? Storage::setting("theme__primary_background") : $titleColor).';
				}
				.'.$class.'-title-basic-line{
					height: 1px;
					background: '.(empty($titleColor) ? Storage::setting("theme__primary_background") : $titleColor).';
					width: 100%;
					flex: 1 1;
					margin-left: 5px
				}
				.'.$class.'>a{
					position: relative;
					width: 100%;
					overflow: hidden;
					display: block;
					border: 1px solid #e6e5e5;
					border-radius: 5px;
					color: '.(empty($itemColor) ? 'black' : ' '.$itemColor.'').' !important;
					height: '.$gridDescHeight.'px;
					background: '.(empty($itemBackground) ? 'transparent' : $itemBackground).';
				}
				.'.$class.'>a:hover{
					'.(empty($itemHover) ? '' : 'color: '.$itemHover.' !important;').';
				}
				.'.$class.'>a:hover img{
					'.($gridHoverZoom==1 ? 'transform: scale(1.1);' : '').'
				}
				.'.$class.'-poster{
					display: block;
					overflow: hidden;
					width: 100%;
					height: '.$gridItemHeightLg.'px;
				}
				.'.$class.'>a img{
					object-fit: cover;
					width: 100%;
					height: 100%;
					transition: .5s all;
				}
				.'.$class.'>a>.'.$class.'-title{
					display: block;
					position: relative;
					padding: 20px 15px 5px 15px;
					font-size: '.$itemFontSize.'px;
					font-weight: bold;
					border-top: 3px solid '.Storage::setting("theme__primary_background").';
					background: '.(empty($itemBackground) ? 'transparent' : $itemBackground).';
					line-height: 1.6
				}
				.'.$class.'>a>.'.$class.'-title>span{
					position: absolute;
					top: -15px;
					padding: 3px 15px;
					border-radius: 15px;
					background: '.Storage::setting("theme__primary_background").';
					display: inline-block;
					color: white;
					font-size: 14px

				}
				.'.$class.'>a>.'.$class.'-description{
					display: block;
					padding: 5px 15px;
					line-height: 1.5;
					overflow: hidden;
					font-size: '.$gridDescSize.'px;
					color: '.$gridDescColor.'
				}
				@media(min-width: 1024px){
					.'.$class.'{
						width: '.(100/$gridColumnAmountLg).'%;
					}
					.'.$class.':nth-child('.$gridColumnAmountLg.'n + 1){
						padding-left: 0
					}
					.'.$class.':nth-child('.$gridColumnAmountLg.'n + '.$gridColumnAmountLg.'){
						padding-left: '.($gridColumnAmountLg==1 ? 0 : $itemPadding).'
					}
				}
				@media(min-width: 768px) and (max-width: 1023px){
					.'.$class.'{
						width: '.(100/$gridColumnAmountMd).'%;
					}
					.'.$class.':nth-child('.$gridColumnAmountMd.'n + 1){
						padding-left: 0
					}
					.'.$class.':nth-child('.$gridColumnAmountMd.'n + '.$gridColumnAmountMd.'){
						padding-left: '.$itemPadding.'
					}
					.'.$class.'-poster{
						height: '.$gridItemHeightMd.'px !important;
					}
				}
				@media(max-width: 767px){
					.'.$class.'{
						width: '.(100/$gridColumnAmountSm).'%;
					}
					.'.$class.':nth-child('.$gridColumnAmountSm.'n + 1){
						padding-left: 0
					}
					.'.$class.':nth-child('.$gridColumnAmountSm.'n + '.$gridColumnAmountSm.'){
						padding-left: '.($gridColumnAmountSm==1 ? 0 : $itemPadding).'
					}
					.'.$class.':last-child{
						margin-bottom: '.$itemPadding.';
					}
					.'.$class.'-poster{
						height: '.$gridItemHeightSm.'px !important;
					}
					.'.$class.'>a>.'.$class.'-title{
						font-size: '.($itemFontSize - 3).'px;
					}
					.'.$class.'>a>.'.$class.'-description{
						font-size: '.($gridDescSize - 3).'px;
					}
				}
			';
			$out.=Widget::css($style);
		}
		$out.=$empty??$header??"";
		if(empty($posts[0]) && empty($info)){
			return empty($out) ? '<div class="alert-info">Hiện tại chuyên mục này chưa có nội dung, xin vui lòng đăng bài viết</div>' : $out;
		}

		//Tiêu đề
		if(!empty($title)){
			if($titleClass=="heading-basic"){
				$out.='
				<h2 style="align-items: baseline" class="flex flex-middle '.$class.'-title-basic '.($titleClass??'heading-simple').' '.($titleAlign??'left').'">
					<span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span>
					<span class="'.$class.'-title-basic-line"></span>
				</h2>
				';
			}else{
				$out.='
				<h2 class="'.$class.'-title '.($titleClass??'heading-simple').' '.($titleAlign??'left').'">
					<span>'.(empty($titleIcon) ? '' : '<i class="fa '.$titleIcon.'"></i> ').''.$title.'</span>
				</h2>
				';
			}
		}
		$out.=$info??"";
		$out.=$description??"";
		

		//Hiện dạng danh sách
		$out.='<div class="flex" style="margin-bottom:-'.$itemPadding.'; margin-top: 5px">';
		$i=0;
		foreach($posts as $p){
			$storage=unserialize($p->storage);
			$out.='
			<div class="'.$class.'">
				<a style="'.(empty($storage["linkColor"]) ? '' : 'color: '.$storage["linkColor"]).'" href="/'.$p->link.'">
					<span class="'.$class.'-poster"><img src="'.(empty($storage["poster"]["small"]) ? '/assets/general/images/no-image.png' : $storage["poster"]["small"]).'" alt="'.$p->title.'" /></span>
					<span class="'.$class.'-title">
						<span>'.$cate[$p->parent].'</span>
						'.(empty($storage["name"]) ? $p->title : $storage["name"]).'
					</span>
					'.($gridDescLeng>0 ? '<span class="'.$class.'-description">'.cutWords( $storage["description"]??$p->content, $gridDescLeng, "...").'</span>' : '').'
				</a>
			</div>
			';
			$i++;
		}
		$out.='</div>';

		//Nút xem thêm
		if(!empty($seeMoreTitle)){
			$out.='
			<div class="center pd-10">
				<a href="/posts-categories/'.$seeMoreLink.'" class="see-more">'.$seeMoreTitle.'</a>
			</div>
			';
		}else if($paginate==1){
			//Phân trang
			$out.=$posts->links([
				"class"=>"center"
			]);
		}

		//Link tới chuyên mục
		$out.=''.(empty($breadcrumb) ? '' : '<div style="'.(empty($itemBorder) || empty($posts->links()) ? '' : 'border-top: 1px solid '.$itemBorder.';').'"></div><div style="padding: 5px 10px; background: '.$breadcrumbBG.'">'.$breadcrumb.'</div>');
		return '<section class="'.$class.'-outer '.($ajaxLoad==1 ? 'paginate-ajax' : '').'" id="'.$class.'-outer">'.$out.'</section>';
	}

	public static function __callStatic($c,$m){
		return;
	}
}