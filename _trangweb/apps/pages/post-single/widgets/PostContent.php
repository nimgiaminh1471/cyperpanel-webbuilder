<?php
/*
# Nội dung bài viết
*/
namespace pages\post__single\widgets;
use Form;
use Widget;
use models\Posts;
use models\PostsComments;
use models\PostsCategories;
use Assets, Storage;
class PostContent{

	//Thông số mặc định
	private static $option=[
		"headingClass"=>"heading-section",
		"mainBG"=>"#FFFFFF",
		"lineBorder"=>"#EAEAEA",
		"contentColor"=>"",
		"infoColor"=>"",
		"titleColor"=>"",
		"titleSize"=>25,
		"contentSize"=>15,
		"contentLineHeight"=>1.5,
		"infoSize"=>14,
		"mainPaddingX"=>10,
		"mainPaddingTop"=>0,
		"mainPaddingBottom"=>10,
		"contentMargin"=>5,
		"mainShadow"=>"",
		"infoShowTime"=>0,
		"infoShowAuthor"=>0,
		"infoShowCount"=>0,
		"breadcrumb"=>"top",
		"postNear"=>1,
		"shareSocial"=>[],
		"postVoteType"=>"star",
		"voteShareSize"=>20,
		"commentsType"=>"my",
		"commentsLimit"=>5,
		"commentsReplyLimit"=>5,
		"commentsItemBorder"=>"#EAEAEA",
		"commentsReplyBG"=>"#EAEAEA",
		"commentsReplyRadius"=>5
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => 'Nội dung bài viết',
			"icon"  => "fa-newspaper-o",
			"color" =>"tomato"
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		extract(WIDGET_DATA);
		$out="";
		if(PAGE_EDITOR){
			$style='
			.post-single{
				position: relative;
				padding: '.$mainPaddingTop.'px '.$mainPaddingX.'px '.$mainPaddingBottom.'px '.$mainPaddingX.'px;
				background-color: '.$mainBG.';
				'.(empty($mainShadow) ? '' : 'box-shadow: 0 0 3px '.$mainShadow.';').'
			}
			.fb-comments{
				background-color: '.$mainBG.';
			}
			.post-single>h1{
				white-space: normal;
				'.(empty($titleColor) ? '' : 'color: '.$titleColor.';').'
				font-size: '.$titleSize.'px;
			}
			.post-single-info>div{
				font-size: '.$infoSize.'px;
				margin: 5px 0;
				'.(empty($infoColor) ? '' : 'color: '.$infoColor.';').'
			}
			.post-single-info>div:first-child{
				margin-top: 10px;
			}
			.post-single-wall{
				margin-top: 5px;
				'.(empty($lineBorder) ? '' : 'border-bottom: 1px solid '.$lineBorder.';').'
			}
			.post-single-body{
				'.(empty($contentColor) ? '' : 'color: '.$contentColor.';').'
				font-size: '.$contentSize.'px;
				margin-top: '.$contentMargin.'px;
				line-height: '.$contentLineHeight.';
				word-wrap: break-word;
			}
			.post-single-near{
				margin: 5px 0
			}
			.post-single-share,
			.post-single-vote{
				margin: 10px 0;
				font-size: '.$voteShareSize.'px;
				width: 50%
			}
			.post-single-share>a{
				padding-right: 10px;
			}
			.post-single-vote{
				text-align: right;
				color: #FFA500
			}
			.post-single-vote>span{
				cursor: pointer;
				padding: 2px
			}
			.post-single-share>a>i{
				transition: all .2s
			}
			.post-single-vote-item:hover i,
			.post-single-vote-zoom,
			.post-single-share>a>i:hover{
				transform: scale(1.3);
			}
			.post-single-voted>i{
				animation: spin 5s infinite;
			}

			.comments-outer{
				margin-top: 10px;
				background-color: '.$mainBG.';
			}
			.comments-item{
				padding: 8px;
				border-bottom: 1px solid '.$commentsItemBorder.';
			}
			.comments-left{
				width: 60px
			}
			.comments-right{
				width: calc(100% - 60px)
			}
			.comments-content{
				padding: 6px 0
			}
			.comments-time{
				color: '.(empty($infoColor) ? 'gray' : $infoColor).';
				padding-left: 10px
			}
			.comments-reply-item{
				background-color: '.$commentsReplyBG.';
				padding: 10px;
				border-radius: '.$commentsReplyRadius.'px;
				margin-bottom: 5px
			}
			.comments-action>a{
				padding-right: 5px;
				color: '.(empty($infoColor) ? 'gray' : $infoColor).';
			}
			.comments-content-outer{
				position: relative
			}
			.comments-submit{
				position: absolute;
				bottom: 2px;
				right: 2px
			}
			';
			$out.=Widget::css($style);
		}

		//Breadcrumb
		$bcrumb="";
		if($breadcrumb!="n" && $post["parent"]>0){
			$parents=PostsCategories::grandparents($post["parent"], true);
			$getCate=PostsCategories::select("link", "title")->whereIn("id", $parents)->get();
			foreach($getCate as $cat){
				$link="/posts-categories/".$cat->link;
				$breadcrumbData[$link]=$cat->title;
			}
			if(!empty($breadcrumbData)){
				$bcrumb=breadcrumb($breadcrumbData);
			}
		}

		//Link bài viết gần
		$near="";
		if($postNear==1){
			$postPrev=Posts::select("link", "title")->where("id", "<", $post["id"])->where("parent", $post["parent"])->last();
			if(isset($postPrev->link)){
				$near.='<div class="post-single-near"><a href="'.$postPrev->link.'"><i class="fa fa-angle-double-left"></i> '.$postPrev->title.'</a></div>';
			}
			$postNext=Posts::select("link", "title")->where("id", ">", $post["id"])->where("parent", $post["parent"])->first();
			if(isset($postNext->link)){
				$near.='<div class="post-single-near"><a href="'.$postNext->link.'"><i class="fa fa-angle-double-right"></i> '.$postNext->title.'</a></div>';
			}
		}

		//Chia sẻ link
		$share='<div class="post-single-share">';
		if(!empty($shareSocial)){
			$social=[
				"Facebook"=>["icon"=>"fa-facebook-square", "color"=>"#3b5998", "link"=>"https://www.facebook.com/sharer/sharer.php?u="],
				"Twitter"=>["icon"=>"fa-twitter-square", "color"=>"#5bc0de", "link"=>"https://twitter.com/intent/tweet?url="],
				"LinkedIn"=>["icon"=>"fa-linkedin-square", "color"=>"#0077B5", "link"=>""],
				"copy"=>["icon"=>"fa-link", "color"=>"inherit", "link"=>""],
				"Print"=>["icon"=>"fa-print", "color"=>"inherit", "link"=>""]
			];
			if(!is_array($shareSocial)){
				$shareSocial=[];
			}
			foreach($shareSocial as $name){
				$info=$social[$name];
				switch($name){

					case "LinkedIn":
						$share.='
						<a title="Chia sẻ lên '.$name.'" class="tooltip" data-pos="top" rel="nofollow" style="color: '.$info["color"].'" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url='.THIS_URL.'&title='.$post["title"].'&summary='.$post["title"].'&source=LinkedIn"><i class="fa '.$info["icon"].'"></i></a>
						</a>
						';
					break;

					case "Print":
						$share.='
						<a title="in nội dung" rel="nofollow" data-pos="top" class="tooltip hidden-small hidden-medium print-element-btn" data-show="" data-hidden=".breadcrumb,.post-single-near,.post-single-share,.post-single-info,.post-single-share-vote" data-element=".post-single" data-link="'.THIS_URL.'" style="color: '.$info["color"].'"><i class="fa '.$info["icon"].'"></i></a>
						';
						Assets::footer("/assets/general/js/print-element.js");
					break;

					case "copy":
						$share.='<a title="Sao chép liên kết" data-modal="share-link" data-pos="top" class="tooltip link modal-click"><i class="fa '.$info["icon"].'"></i></a>';
						$share.=modal("Sao chép liên kết", '
							<div class="bg pd-10">
								<input class="width-100" type="text" value="'.THIS_URL.'" />
								<input class="width-100" style="margin-top: 5px" type="text" value="'.htmlEncode('<a href="'.THIS_URL.'">'.$post["title"].'</a>').'" />
							</div>
						', "share-link", '450px', false, true);
					break;

					default:
						$share.='<a title="Chia sẻ lên '.$name.'" data-pos="top" class="tooltip" rel="nofollow" style="color: '.$info["color"].'" target="_blank" href="'.$info["link"].''.THIS_URL.'"><i class="fa '.$info["icon"].'"></i></a>';
				}
				
			}
		}
		$share.='</div>';

		//Đánh giá bài viết
		$vote="";
		if($postVoteType=="star"){
			$postVoteAllow=true;
			$postVoteIP=$post["storage"]["postVoteIP"]??[];
			$voteUp=$post["storage"]["voteUp"]??0;
			$voteTotal=$post["storage"]["voteTotal"]??0;
			$clientIP=$_SERVER["REMOTE_ADDR"];
			if( isset($postVoteIP[$clientIP]) ){
				$postVoteAllow=false;
			}
			if( isset($_POST["postVoteStar"]) && $postVoteAllow ){
				if($_POST["postVoteStar"]>2){
					//Đánh giá tốt
					Posts::updateStorage($post["id"], ["voteUp"=>($voteUp+2), "voteTotal"=>($voteTotal+1)]);//Ưu tiên like :)))
				}else{
					//Đánh giá xấu
					Posts::updateStorage($post["id"], ["voteTotal"=>($voteTotal+1)]);
				}
				$postVoteIP=array_slice($postVoteIP, -50);
				$postVoteIP[$clientIP]=(INT)$_POST["postVoteStar"]-1;
				Posts::updateStorage($post["id"], ["postVoteIP"=>$postVoteIP]);
				$postVoteAllow=false;
			}
			$starTitle=[__("Tệ"), __("Không hay"), __("Tạm"), __("Khá"), __("Hay")];
			if($voteUp>0){
				$percent=ceil(($voteUp/$voteTotal)*10);
				if($percent>1){
					$voteScript='
					<script type="application/ld+json">
						{
							"@context": "https://schema.org/",
							"@type": "CreativeWork",
							"name": "'.$post["title"].'",
							"description": "'.cutWords($post["content"], 30).'",
							"aggregateRating": {
								"@type": "AggregateRating",
								"ratingValue": "'.($percent/2).'",
								"bestRating": "5",
								"ratingCount": "'.$voteTotal.'"
							}
						}
					</script>
					';
				}
			}else{
				$percent=0;
			}
			for($i=0;$i<5;$i++){
				$star=($i+1)*2;
				if($percent>=$star){
					$star='';
				}else if($percent==($star-1)){
					$star='-half-o';
				}else{
					$star='-o';
				}
				$vote.='<span title="'.$starTitle[$i].'" class="'.($postVoteAllow ? 'post-single-vote-item ' : ($postVoteIP[$clientIP]==$i ? 'post-single-voted ' : '') ).'tooltip" data-pos="top"><i class="fa fa-star'.$star.'"></i></span>';
			}
			Assets::footer("/assets/general/js/post-vote.js");
		}else if($postVoteType=="fb"){
			$vote='<div class="fb-like" data-href="'.THIS_LINK.'" data-layout="button_count" data-action="like" data-size="large" data-show-faces="true" data-share="false"></div>';
		}
		$vote=($voteScript??'').'<div class="post-single-vote tooltip-outer">'.$vote.'</div>';


		//Hiện nội dung
		if($post["parent"]>0){
			//Bài viết
			$out.='
			<article class="post-single">
				<div class="post-single-body">'.$post["content"].'</div>
				'.$near.'
				'.($postVoteType!="n" || !empty($shareSocial) ? '
				<div class="flex flex-middle post-single-share-vote">
					'.($share??'').'
					'.($vote??'').'
				</div>
				' : '').'
				'.($breadcrumb=="bottom" ? $bcrumb : '').'
				'.(permission("post") ? '
					'.($post["status"]=="public" ? '' : '<script>alert("Bài viết này chưa được đăng công khai!");</script>').'
					<a class="tooltip" title="Chỉnh sửa bài viết" href="/admin/PostEditor?id='.$post["id"].'" style="position: absolute; top: 5px; right: 5px; background: skyblue; width: 30px;height: 30px; border-radius: 50%">
						<i style="position: absolute;top: 50%; left: 50%; transform: translate(-50%, -50%);" class="fa fa-edit"></i>
					</a>' : '').'
			</article>
			';
		}else{
			//Trang
			$out.='
			<article class="post-single">
				<h1><span itemprop="name">'.$post["title"].'</span></h1>
				<div class="post-single-wall"></div>
				<div class="post-single-body">'.$post["content"].'</div>
				'.(permission("post") ? '
					'.($post["status"]=="public" ? '' : '<script>alert("trang này chưa được đăng công khai!");</script>').'
					<a class="tooltip" title="Chỉnh sửa bài viết" href="/admin/PostEditor?id='.$post["id"].'" style="position: absolute; top: 5px; right: 5px; background: skyblue; width: 30px;height: 30px; border-radius: 50%">
						<i style="position: absolute;top: 50%; left: 50%; transform: translate(-50%, -50%);" class="fa fa-edit"></i>
					</a>' : '').'
			</article>
			';
		}

		//Bình luận
		if( permission("member") ){
			PostsComments::where("posts_id", $post["id"])->where( "reply_user", user("id") )->update(["reply_read"=>1]);
		}
		if($commentsType=="my" && Storage::option("register",1)==1 && ($post["storage"]["commentsEnable"]??0)==1 && $post["parent"]>0){//Bình luận mặc định
			//Đăng bình luận
			if(isset($_POST["comment"]) && permission("member") ){
				$cmtData=$_POST["comment"];
				foreach($cmtData as $key=>$value){
					$cmtData[$key]=strip_tags($value);
				}

				//Chặn từ khóa
				$cmtFilter=storage::option("commentFilter");
				if(!empty($cmtFilter)){
					$cmtFilter=explode(PHP_EOL, $cmtFilter);
					foreach($cmtFilter as $str){
						if(strpos($cmtData["content"], $str)>-1){
							$postCommentMsg="Không thể gửi";
						}
					}
				}

				//Nội dung không hợp lệ
				if( strlen($cmtData["content"])<5 || strlen($cmtData["content"])>500 ){
					$postCommentMsg="Nội dung không được ngắn hơn 5 & dài hơn 500 ký tự";
				}

				//Chặn gửi quá nhanh
				$lastSent=PostsComments::where("users_id", user("id") )->last();
				$lastSent=timestamp($lastSent->updated_at??0);
				$waitTime=time()-30;
				if( $lastSent>$waitTime && !permission("post") ){
					$postCommentMsg="Bạn không thể đăng quá nhanh, hãy chờ sau: ".($lastSent-$waitTime)."s";
				}

				//Chặn spam
				$cmtPendingCount=PostsComments::where("users_id", user("id") )->where("status", "pending")->total();
				if($cmtPendingCount>15 && !permission("post") ){
					$postCommentMsg="Bạn đã gửi quá 15 bài mà chưa được duyệt, xin vui lòng chờ duyệt!";
				}

				//Check trả lời có hợp lệ hay không
				if( $cmtData["parent"] >0 && !PostsComments::where("id", $cmtData["parent"] )->exists() ){
					$postCommentMsg="Trả lời không hợp lệ, hãy thử lại";
				}

				//Lưu bình luận
				if(empty($postCommentMsg)){
					$cmtData["posts_id"]=$post["id"];
					$cmtData["status"]="pending";
					if( permission("post") ){
						$cmtData["status"]="accept";
					}
					$cmtData["users_id"]=user("id");
					if($cmtData["reply_user"]==user("id")){
						unset($cmtData["reply_user"]);
					}
					PostsComments::create($cmtData);
					$postCommentMsg=1;
				}
				
			}

			//Xóa bình luận
			if(isset($_POST["deleteComment"])){
				$comment=PostsComments::find($_POST["deleteComment"]);
				if( permission("post_manager") || $comment->users_id==user("id") ){
					if($comment->parent==0){
						PostsComments::where("parent", $_POST["deleteComment"])->delete();
					}
					PostsComments::where("id", $_POST["deleteComment"])->delete();
				}
			}
			$out.='
			<section class="comments-outer" data-url="'.URI.'" data-role="'.user("role", 0).'">
			<h2 class="heading-simple">Bình luận bài viết</h2>
			<form action="" method="POST" class="pd-10 comments-form">
				<input type="hidden" value="0" name="comment[parent]" />
				<input type="hidden" value="'.$post["users_id"].'" name="comment[reply_user]" />
				<div class="comments-content-outer">
					<textarea class="rm-radius width-100" rows="5" maxlength="500" name="comment[content]" placeholder="Bạn đang có suy nghĩ gì? '.user("name").'"></textarea>
					<span class="comments-submit btn-primary hidden" style="border-radius: 30px">Gửi</span>
				</div>
				<div class="comments-msg alert-danger hidden">'.($postCommentMsg??'').'</div>
			</form>
			<div class="post-single-wall"></div>
			';
			//Danh sách bình luận
			$replyOffset=GET("replyPage", 0)*(INT)$commentsReplyLimit;
			$comments=PostsComments::where("posts_id", $post["id"])
			->where("parent", 0)
			->where("status", "accept")
			->orWhere("posts_id", $post["id"])
			->where("parent", 0)
			->where("users_id", user("id") )
			->orderBy("created_at", "DESC")
			->paginate($commentsLimit);
			
			$out.='<div class="paginate-ajax tooltip-outer" id="comments-list">';
			user($comments);//Lưu thông tin người dùng để nhanh hơn
			foreach($comments as $cmt){
				$reply=PostsComments::where("parent", $cmt->id)
				->where("status", "accept")
				->orWhere("parent", $cmt->id)
				->where("users_id", user("id") )
				->orderBy("created_at", "DESC")
				->offset($replyOffset)
				->limit($commentsReplyLimit)
				->get();
				$out.='
				<div class="comments-item comments-item-outer flex" id="comments-item-outer-'.$cmt->id.'" data-id="'.$cmt->id.'" data-user="'.$cmt->users_id.'">
					<div class="comments-left">
						<div class="user-avatar">
							<img alt="'.user("name", $cmt->users_id).'" src="'.user("avatar", $cmt->users_id).'" />
						</div>
					</div>
					<div class="comments-right">
						<div>
							'.user("name_color", $cmt->users_id).'
							<span class="comments-time"><i class="fa fa-clock-o"></i> '.dateText( timestamp($cmt->created_at) ).'</span>
							'.($cmt->status=="pending" ? '<span class="label-danger">Đang chờ duyệt</span>' : '').'
						</div>
						<div class="comments-content">'.nl2br($cmt->content).'</div>
						<div class="comments-action">
							<a data-action="reply" class="'.($reply->total()==0 ? 'comments-reply-alert' : '').'" href="javascript:void(0)">Bình luận ('.$reply->total().')</a>
							'.(permission("post_manager") || $cmt->users_id==user("id") ? '<a data-id="'.$cmt->id.'" data-action="delete" href="javascript:void(0)">Xóa</a>' : '').'
						</div>
						<div class="comments-reply-outer hidden">
							<div class="comments-reply-form"></div>
							<div class="comments-reply-list-'.$cmt->id.'">
						';
						//Trả lời
						foreach($reply as $rep){
							$out.='
							<div class="comments-reply-item comments-item-outer" data-id="'.$cmt->id.'" data-user="'.$rep->users_id.'">
								<div>
									<span class="comments-reply-name">'.user("name_color", $rep->users_id).'</span>
									<span class="comments-time"><i class="fa fa-clock-o"></i> '.dateText( timestamp($rep->created_at) ).'</span>
									'.($rep->status=="pending" ? '<span class="label-danger">Đang chờ duyệt</span>' : '').'
								</div>
								<div class="comments-content">'.nl2br($rep->content).'</div>
								<div class="comments-action comments-reply-alert">
									'.($rep->users_id==user("id") ? '' : '<a data-action="reply" href="javascript:void(0)">Trả lời</a>').'
									'.(permission("post_manager") || $rep->users_id==user("id") ? '<a data-id="'.$rep->id.'" data-action="delete" href="javascript:void(0)">Xóa</a>' : '').'
								</div>
								<div class="comments-reply-form"></div>
							</div>
							';
						}
						$out.='
						</div>
						'.($reply->total()>$commentsReplyLimit ? '<span data-offset="0" data-id="'.$cmt->id.'" data-page="'.GET("page", 1).'" class="comments-reply-more see-more link">Xem thêm</span>' : '').'
						</div>
					</div>
				</div>';
			}
			$out.=$comments->links();
			$out.='</div>';
			$out.='</section>';
			Assets::footer("/assets/general/js/post-comment.js");
		}else if( $commentsType=="fb" && ($post["storage"]["commentsEnable"]??0)==1 && $post["parent"]>0){
			//Bình luận Facebook
			$out.='<div class="fb-comments" data-width="100%" data-href="'.THIS_LINK.'" data-numposts="5"></div>';
		}
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["html"=>'<div class="pd-10 bg"></div>'],
			["type"=>"number", "name"=>"mainPaddingX", "title"=>"Khoảng cách lề (phải-trái)", "note"=>"", "min"=>0, "max"=>9999, "value"=>$mainPaddingX,"attr"=>''],
			["type"=>"number", "name"=>"mainPaddingTop", "title"=>"Khoảng cách lề (trên)", "note"=>"", "min"=>0, "max"=>9999, "value"=>$mainPaddingTop,"attr"=>''],
			["type"=>"number", "name"=>"mainPaddingBottom", "title"=>"Khoảng cách lề (dưới)", "note"=>"", "min"=>0, "max"=>9999, "value"=>$mainPaddingBottom,"attr"=>''],
			["type"=>"number", "name"=>"contentMargin", "title"=>"Khoảng cách tiêu đề với nội dung", "note"=>"", "min"=>0, "max"=>9999, "value"=>$contentMargin,"attr"=>''],
			["type"=>"color", "name"=>"mainBG", "title"=>"Màu nền", "default"=>"", "value"=>$mainBG, "required"=>false],
			["type"=>"color", "name"=>"lineBorder", "title"=>"Màu thanh ngăng cách", "default"=>"", "value"=>$lineBorder, "required"=>false],
			["type"=>"color", "name"=>"mainShadow", "title"=>"Màu đổ bóng", "default"=>"", "value"=>$mainShadow, "required"=>false],
			["type"=>"select", "name"=>"breadcrumb", "title"=>"Liên kết đến chuyên mục", "option"=>
				["top"=>"Trên đầu", "bottom"=>"Dưới cùng", "n"=>"Không hiện"],
			"value"=>$breadcrumb, "horizontal"=>35],
			["type"=>"switch", "name"=>"postNear", "title"=>"Hiện bài viết gần", "value"=>$postNear],

			["html"=>'<div class="panel panel-default"><div class="heading link">Tiêu đề bài viết</div><div class="panel-body hidden">'],
				["type"=>"color", "name"=>"titleColor", "title"=>"Màu chữ tiêu đề", "default"=>"", "value"=>$titleColor, "required"=>false],
				["type"=>"number", "name"=>"titleSize", "title"=>"Cỡ chữ tiêu đề", "note"=>"", "min"=>0, "max"=>9999, "value"=>$titleSize,"attr"=>''],
			["html"=>'</div></div>'],

			["html"=>'<div class="panel panel-default"><div class="heading link">Nội dung bài viết</div><div class="panel-body hidden">'],
				["type"=>"color", "name"=>"contentColor", "title"=>"Màu chữ nội dung", "default"=>"", "value"=>$contentColor, "required"=>false],
				["type"=>"number", "name"=>"contentSize", "title"=>"Cỡ chữ nội dung", "note"=>"", "min"=>0, "max"=>9999, "value"=>$contentSize,"attr"=>''],
				["type"=>"number", "name"=>"contentLineHeight", "title"=>"Khoảng cách dòng", "note"=>"", "min"=>0, "max"=>5, "value"=>$contentLineHeight,"attr"=>'step="0.1"'],
			["html"=>'</div></div>'],

			["html"=>'<div class="panel panel-default"><div class="heading link">Thông tin bài viết</div><div class="panel-body hidden">'],
				["type"=>"color", "name"=>"infoColor", "title"=>"Màu chữ", "default"=>"", "value"=>$infoColor, "required"=>false],
				["type"=>"number", "name"=>"infoSize", "title"=>"Cỡ chữ", "note"=>"", "min"=>0, "max"=>9999, "value"=>$infoSize,"attr"=>''],
				["type"=>"switch", "name"=>"infoShowTime", "title"=>"Hiện ngày đăng với khách", "value"=>$infoShowTime],
				["type"=>"switch", "name"=>"infoShowAuthor", "title"=>"Hiện tác giả với khách", "value"=>$infoShowAuthor],
				["type"=>"switch", "name"=>"infoShowCount", "title"=>"Hiện lượt xem với khách", "value"=>$infoShowCount],
			["html"=>'</div></div>'],

			["html"=>'<div class="panel panel-default"><div class="heading link">Chia sẻ & đánh giá</div><div class="panel-body hidden">'],
				["type"=>"checkbox", "name"=>"shareSocial", "title"=>"Chọn mạng xã hội",
					"checkbox"=>["Facebook"=>"Facebook", "Twitter"=>"Twitter", "LinkedIn"=>"LinkedIn", "copy"=>"Sao chép link", "Print"=>"In văn bản"],
					"value"=>$shareSocial
				],
				["type"=>"select", "name"=>"postVoteType", "title"=>"Đánh giá", "option"=>
					["star"=>"Xếp hạng sao", "fb"=>"Facebook like", "n"=>"Tắt"],
					"value"=>$commentsType, "horizontal"=>35],
				["type"=>"number", "name"=>"voteShareSize", "title"=>"Kích cỡ", "note"=>"", "min"=>0, "max"=>9999, "value"=>$voteShareSize,"attr"=>''],
			["html"=>'</div></div>'],

			["html"=>'<div class="panel panel-default"><div class="heading link">Bình luận</div><div class="panel-body hidden">'],
				["html"=>(Storage::option("register", 1)==1 ? '' : '<div class="alert-danger">Không thể dùng chức năng bình luận vì đã tắt đăng ký thành viên!</div>')],
				["type"=>"select", "name"=>"commentsType", "title"=>"Hệ thống bình luận", "option"=>
					["my"=>"Trên web", "fb"=>"Facebook", "n"=>"Tắt"],
					"value"=>$commentsType, "horizontal"=>35],
				["type"=>"number", "name"=>"commentsLimit", "title"=>"Số bài bình luận", "note"=>"", "min"=>0, "max"=>9999, "value"=>$commentsLimit,"attr"=>''],
				["type"=>"number", "name"=>"commentsReplyLimit", "title"=>"Số bài bình luận(trả lời)", "note"=>"", "min"=>0, "max"=>9999, "value"=>$commentsReplyLimit,"attr"=>''],
				["type"=>"color", "name"=>"commentsItemBorder", "title"=>"Màu viền ngăn cách bình luận", "value"=>$commentsItemBorder, "required"=>true],
				["type"=>"color", "name"=>"commentsReplyBG", "title"=>"Màu nền phần trả lời", "value"=>$commentsReplyBG, "required"=>true],
				["type"=>"number", "name"=>"commentsReplyRadius", "title"=>"Độ bo góc phần trả lời", "note"=>"", "min"=>0, "max"=>9999, "value"=>$commentsReplyRadius,"attr"=>''],
			["html"=>'</div></div>']
		];
		$out.=Form::create([
			"form"=>$form,
			"function"=>"",
			"prefix"=>"",
			"name"=>"{$prefixName}[data]",
			"class"=>"menu",
			"hover"=>false
		]);
		return $out;
	}




}
//</Class>