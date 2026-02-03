<?php
namespace classes;
use models\Posts;
use models\Users;
use models\PostsComments;
use models\CashFlow;
use Storage, Assets, PageOption;
/*
# Nút treo bên dưới trang
*/
class FixedButton{
	public static function show($autoRefresh = true){
		$out="<nav>";
		//Trang cá nhân
		if( permission("member") ){
			$nav[]=["link"=>"/admin", "title"=>"Trang quản trị", "icon"=>"fa-cog", "count"=>0];
			$nav[]=["link"=>"/admin/WebsiteList", "title"=>"Quản lý website", "icon"=>"fa-globe", "count"=>0];
			$nav[]=["link"=>"/user/profile", "title"=>"Trang cá nhân", "icon"=>"fa-user", "count"=>0];
		}

		//Đăng bài viết
		if( permission("post") ){
			$nav[]=["link"=>"/admin/PostEditor", "title"=>"Đăng bài viết", "icon"=>"fa-edit", "count"=>0];
			$postsDaft=Posts::where("status", "draft")->total();
			if($postsDaft>0){
				$nav[]=["link"=>"/admin/PostsList?filter[status]=draft", "title"=>"Bài viết nháp", "icon"=>"fa-book", "count"=>$postsDaft];
			}
		}

		//Duyệt bình luận
		if(permission("post_manager")){
			$commentsPending=PostsComments::where("status", "pending")->total();
			if($commentsPending>0){
				$nav[]=["link"=>"/admin/PostsComments", "title"=>"Duyệt bình luận", "icon"=>"fa-comments-o", "count"=>$commentsPending];
			}
		}

		// Công việc chưa xử lý
		if(permission("work")){
			$taskPending = \models\Task::whereIn('status', [0, 1])->where('assign_user_id', user('id') )->total();
			if( $taskPending > 0 ){
				$nav[]=["link"=>"/admin/Task", "title"=>"Việc cần xử lý", "icon"=>"fa-check-square-o", "count"=>$taskPending];
			}
		}

		//Dành cho Admin
		if( permission("admin") && $autoRefresh ){
			$nav[]=["link"=>"javascript:void(0)", "title"=>"Thiết lập nhanh", "icon"=>"fa-wrench", "count"=>0, "modal"=>PageOption::main()];

			//Đơn nạp tiền chờ duyệt
			$rechargePending=CashFlow::where("status", 0)->whereNull('deleted_at')->total();
			if($rechargePending>0){
				$nav[]=["link"=>"/admin/Receipts", "title"=>"Đơn nạp tiền", "icon"=>"fa-money", "count"=>$rechargePending];
			}
		}
		if( permission('member') ){
			$notifyMsg='';
			$notifyCount=0;

			//Thông báo từ Admin
			if( !empty(user("message")) ){
				if( isset($_GET["_deleteMessage"]) ){
					Users::updateStorage(user("id"), ["message"=>""]);
					redirect(null, true);
				}
				$notifyCount++;
				$notifyMsg.='
				<div class="alert-warning">'.nl2br( user("message") ).' <a onclick="FixedButtonReaded()" style="padding-left: 5px; color: red"><i title="Xóa thông báo" class="fa fa-times-circle-o"></i></a></div>
				';
			}
			$notifyReaded=(array)user("notify_readed");
			foreach(Storage::option("notify", []) as $time=>$item){
				if( in_array(user("role"), $item["role"]) && empty($_GET["_readedNotify"]) ){
					$notifyMsg.='
					<div class="panel panel-danger">
						<div class="heading link'.(in_array($time, $notifyReaded) ? '' :' panel-actived').'">
							<i class="fa fa-info-circle"></i> '.$item["title"].'
						</div>
						<div class="panel-body hidden"  style="'.(in_array($time, $notifyReaded) ? '' :' display: block').'">
							'.str_replace(["[@name]"], [user("name")], $item["content"]).'
							<div style="color: gray"><i class="fa fa-clock-o"></i> '.dateText($time).'</div>
						</div>
					</div>
					';
					if( !in_array($time, $notifyReaded) ){
						$notifyCount++;
						$notifyReaded[]=$time;
					}
				} 
			}

			//Đã đọc
			if( isset($_POST["_readedNotify"]) ){
				$notifyReaded=array_slice($notifyReaded, -6);
				Users::updateStorage(user("id"), ["notify_readed"=>$notifyReaded]);
			}

			//Thông báo bình luận
			$notifyCount+=PostsComments::where([
				["status", "=", "accept"],
				["reply_user", "=", user("id")],
				["reply_read", "=", 0]
			])->orderBy("created_at", "DESC")->limit(20)->total();
			
			foreach([0,1] as $replyRead){
				$notifyData=PostsComments::where([
					["status", "=", "accept"],
					["reply_user", "=", user("id")],
					["reply_read", "=", $replyRead]
				])->orderBy("created_at", "DESC")->limit(10)->get();
				if($notifyData->count()>0){
					$notifyMsg.='
					<div class="panel panel-info">
						<div class="heading link'.($replyRead==0 ? ' panel-actived' : '').'">
							Bình luận '.($replyRead==0 ? 'chưa đọc' : 'đã đọc').'
						</div>
						<div class="panel-body hidden"  style="'.($replyRead==0 ? 'display: block' : '').'; padding: 0">
					';
					
					user($notifyData);
					foreach($notifyData as $rep){
						$notifyMsg.='
						<a style="margin-bottom: -1px" class="menu bd block" href="/'.Posts::where("id", $rep->posts_id)->value("link").'#comments-item-outer-'.($rep->parent==0 ? $rep->id : $rep->parent).'" style="'.($rep->reply_read==0 ? '' : 'color: gray').'">
							'.user("name_color", $rep->users_id, false).':
							'.cutWords( str_replace("@".user("name").":", "", $rep->content) , 10, "...").'
							<br/>
							<span style="color: gray"><i class="fa fa-clock-o"></i> '.dateText( timestamp($rep->created_at) ).'</span>
						</a>
						';
					}
					$notifyMsg.='
						</div>
					</div>
					';
				}
			}
			$nav[]=['id' => 'notify', "link"=>"javascript:void(0)", "title"=>"Thông báo", "icon"=>"fa-bell", "count"=>$notifyCount, "modal"=>'<div class="panel-list">'.$notifyMsg.'</div>', "hidden"=>(empty($notifyMsg) ? true : false)];
		}
		//Tạo menu
		$count=$countNav=0;
		foreach($nav??[] as $item){
			$modalID='modal-button-nav-'.$countNav;
			if( empty($item["hidden"]) ){
				$out.='
				<a data-id="'.($item['id'] ?? null).'" data-modal="'.$modalID.'" class="'.(isset($item["modal"]) ? 'modal-click' : '').'" href="'.$item["link"].'">
					<i style="min-width: 17px" class="fa '.$item['icon'].'"></i> '.$item['title'].' '.($item['count']>0 ? "<sup>({$item['count']})</sup>":"").'
				</a>';
			}
			if(isset($item["modal"])){
				Assets::footer(modal($modalID, '<i style="min-width: 17px" class="fa '.$item['icon'].'"></i> '.$item["title"], $item["modal"], '550px', false, true));
			}
			$count=$count+$item['count'];
			$countNav++;
		}
		$out.='</nav>';
		$out.=($count>0 ? "<span>$count</span>" : "");

		//Nút bật-tắt sửa trang
		if( permission("admin") ){
			if( isset($_POST["_enablePageEditor"]) ){
				$pageEditorUpdate=(Storage::option("enablePageEditor", 0)==1 ? 0 : 1);
				Storage::update("option", ["enablePageEditor"=>$pageEditorUpdate]);
				redirect("", true);
			}
			$pageEditor='
			<form title="Bật-tắt chế độ sửa trang" class="fixed-button '.($autoRefresh ? '' : 'hidden').'" style="top:50%;bottom: auto" method="POST">
				<input type="hidden" name="_enablePageEditor" value="1" />
				<a href="javascript:void(0)" style="width:30px;height:30px">
					<button type="submit" style="border:none;padding:0;cursor: pointer;">
						<i style="font-size:20px; color: '.(Storage::option("enablePageEditor", 0)==1 ? 'skyblue' : 'white').'" class="fa fa-wrench"></i>
					</button>
				</a>
			</form>
			';
		}

		//Hiện nội dung
		if($countNav>0){
			return '
				'.($pageEditor??'').'
				<div class="fixed-button '.($autoRefresh ? '' : 'hidden').'" id="fixed-button-bottom" data-refresh="'.$autoRefresh.'">
					'.$out.'
					<a href="javascript:void(0)"'.($count>0 ? 'style="animation: spin 2s;"' : '').'><img src="'.user("avatar").'" /></a>
				</div>
				'.Assets::show("/assets/general/js/fixed-button.js").'
			';
		}
	}
}