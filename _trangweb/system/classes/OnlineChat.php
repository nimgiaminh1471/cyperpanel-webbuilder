<?php
/*
# Chat online với khách hàng
*/
use models\Users;
use models\OnlineChatTable;
class OnlineChat{
	public static function setup($welcome=""){
		$getChatManagerList = Users::select('users.id', 'users.notifications_player_ids')
			->leftJoin('roles_permissions', 'users.role', '=', 'roles_permissions.role_id')
			->where('permission_name', 'online_chat_manager')
			->get();
		Assets::footer("/assets/online-chat/style.css", "/assets/online-chat/script.js", "/assets/online-chat/jquery-play-sound.js");
		$out='';
		$unread=0;
		if( permission('online_chat_manager') ){
			$unread=OnlineChatTable::where("readed_manager", 0)->total();
		}else if( permission("member") ){
			$unread=OnlineChatTable::where("users_id", user("id") )->where("readed_member", 0)->total();
		}
		$out.='<section id="online-chat"'.( permission('online_chat_manager') ? ' style="max-width: 550px"' : ' ').'>';

		$out.='<div class="heading primary-bg"><i class="fa fa-comments-o"></i> '.__("Bạn cần hỗ trợ, chat ngay").' <span class="online-chat-unread right-icon">'.($unread>0 ? '<sup>'.$unread.'</sup>' : '').'</span> <i class="fa fa-angle-up"></i></div>';
		$out.='<div class="online-chat-body hidden">';
		if( !permission("member") ){
			$out.=$welcome;
		}
		//Nội dung cuộc trò chuyện
		$out.='<div class="online-chat-conversation flex flex-medium">';
		$out.='<div class="online-chat-conversation-left '.( permission('online_chat_manager') ? 'width-40' : ' hidden').'">';
		//Tab bên trái
		if( permission('online_chat_manager') ){
			//Quản lý
			if( isset($_POST["onlineChatManager"]) ){
				$cID=$_POST["onlineChatConversationID"];
				switch($_POST["onlineChatManager"]){
					//Xóa cuộc trò chuyện
					case "delete":
						OnlineChatTable::deleteConversation($cID);
					break;

					//Xóa 1 tin nhắn
					case "delete-msg":
						$data=OnlineChatTable::find($cID)->data;
						$data=(array)unserialize($data);
						foreach($data as $id=>$item){
							if($item["time"]==POST("id")){
								unset($data[$id]);
							}
						}
						if( empty($data) ){
							$data=[];
						}
						OnlineChatTable::find($cID)->update(["data"=>serialize($data)]);
					break;

					//Đang nhập nội dung
					case "typingOn":
						OnlineChatTable::where("users_id", $cID)->update(["typing"=>$_POST["onlineChatManagerUsersID"]]);
					break;

					//Thoát nhập nội dung
					case "typingOff":
						OnlineChatTable::where("users_id", $cID)->update(["typing"=>null]);
					break;

					//Đánh dấu là dã đọc
					case "readed":
						$readed=OnlineChatTable::where("users_id", $cID)->value("readed_manager")==1 ? 0 : 1;
						OnlineChatTable::where("users_id", $cID)->update(["readed_manager"=>$readed]);
					break;
				}
			}

			//Danh sách cuộc trò chuyện
			$conversations=OnlineChatTable::orderBy("updated_at", "DESC")->limit(20)->get();
			$conversationID=$_POST["onlineChatConversationID"] ?? $conversations[0]->users_id ?? 0;
			if( !OnlineChatTable::where("users_id", $conversationID)->exists() ){
				$conversationID=$conversations[0]->users_id ?? 0;
			}
			foreach($conversations as $c){
				$data=(array)unserialize($c->data);
				$out.='
				<div data-id="'.$c->users_id.'" class="menu bd-bottom primary-hover link'.($c->users_id==$conversationID ? ' primary-bg' : '').'">
					<div>
						<img src="'.user("avatar", $c->users_id).'" style="max-height: 40px;" class="user-avatar" /> <b>'.user("name", $c->users_id).'</b>
						'.($c->readed_manager==0 ? '<span style="background: tomato;padding: 2px; border-radius: 50%; width: 10px;height: 10px;display: inline-block;text-align: center"></span>' : '').'
					</div>
					<div class="text-inline"'.($c->readed_manager==0 ? ' style="font-weight: bold"' : '').'>'.strip_tags(end($data)["message"]).'</div>
					<div class="right gray"><small>'.date("H:i - d/m/Y", timestamp($c->updated_at) ).'</small></div>
				</div>';
			}
		}
		$out.='</div>';
		//Tab bên phải
		$out.='<div class="online-chat-conversation-right width-'.( permission('online_chat_manager') ? '60' : '100').'">';
		if( permission("member") ){
			$conversationID=permission('online_chat_manager') ? $conversationID : user("id");
			$conversation=OnlineChatTable::where("users_id", $conversationID)->first(true);
			$data=unserialize($conversation->data??"");
			if( empty($data) ){
				$data=[];
			}
			//Đánh dấu là đã đọc
			if( !permission('online_chat_manager') && POST("onlineChatMaskAsRead")==1  ){
				OnlineChatTable::where("users_id", $conversationID)->update(["readed_member"=>1]);
			}

			$out.='<input type="hidden" class="online-chat-last-updated" value="'.timestamp($conversation->updated_at??0).'" />';
			$welcomeMessage = [];
			if( permission('online_chat_manager') && $conversationID==0 ){
				$welcomeMessage[] = [
					"users_id" => user("id"),
					"message"  => 'Nhập nội dung để tạo cuộc trò chuyện giữa các thành viên quản lý trang!',
					"time"     => timestamp()
				];
			}
			foreach(array_merge($welcomeMessage, $data) as $id=>$item){
				$out.='
				<div class="online-chat-item-'.($item["users_id"]==user("id") || permission("admin", $item["users_id"]) ? 'my' : 'your').'">
				<div>
					<img src="'.user("avatar", $item["users_id"]).'" style="max-height: 40px;" class="user-avatar" /> '.user("name_color", $item["users_id"]).'
				</div>
				<div style="padding-top: 3px">
					'.nl2br( preg_replace('/(http[s]{0,1}\:\/\/\S{4,})\s{0,}/ims', ' <a class="primary-color" href="$1" target="_blank"><b>$1</b></a> ', $item["message"]) ).'
				</div>
					<div class="right gray">
						<small>'.date("H:i - d/m/Y", timestamp($item["time"]) ).'</small>
						'.(permission('online_chat_manager') ? '<a title="Xóa" data-id="'.$item["time"].'" class="red online-chat-manager pd-5" data-action="delete-msg"><i class="fa fa-trash"></i></a>' : '').'
					</div>
				</div>
				';
			}
		}
		if( permission('online_chat_manager') && $conversationID>0 ){
			if($conversation->readed_member==1){
				$out.='
				<div class="label-success block rm-radius pd-5">
					'.user("name", $conversation->users_id).' đã xem
				</div>
				';
			}
			$out.='
			<div class="pd-10">
				<a class="red online-chat-manager pd-5" data-action="delete"><i class="fa fa-trash"></i> Xóa</a>
				<a class="online-chat-manager pd-5" data-action="readed"><i class="fa fa-check"></i> Đã đọc</a>
			</div>
			';
		}
		$out.='<div class="online-chat-typing">';
			if( ($conversation->typing??0)>0 ){
				$out.='<div class="alert-warning">'.user("name_color", $conversation->typing).' Đang nhập nội dung...</div>';
			}
		$out.='</div>';
		$out.='</div>';
		$out.='</div>';

		$out.='<form>';
		if( permission("member") ){
			//Nếu đã đăng nhập
			$out.='<div class="online-chat-msg alert-danger hidden form-mrg">';
			if( isset($_POST["online_chat"]) ){
				$message=$_POST["online_chat"]["message"]??"";
				if( !permission('online_chat_manager') ){
					$message=strip_tags($message);
				}
				if( strlen($message)>1000 ){
					$error='Nội dung không được quá 1000 ký tự';
				}
				if( isset($_FILES[0]["name"]) ){
					//Có file đính kèm
					$message='';
					$attachmentsFolder="/assets/online-chat/attachments/".$conversationID;
					foreach($_FILES as $id=>$file){
						$fileName=time()."-".$file["name"];
						$uploadStt=Gallery::upload([
							"form"=>$id,//Tên form chứa file
							"folder"=>PUBLIC_ROOT.$attachmentsFolder,//Thư mục lưu
							"name"=>$fileName,//Tên file (để trống sẽ tự đặt)
							"ext"=>["jpg", "jpeg", "png", "gif", "doc", "docx", "pdf", "txt", "crt", "xlsx"],//Cho phép đuôi
							"overwrite"=>false,//Ghi đè file đã tồn tại
							"maxSize"=>9000, //Cỡ tối đa (Kb)
						]);
						if( is_null($uploadStt) ){
							switch( Gallery::type($fileName) ){
								case "image":
									$message.='<div class="pd-5 center"><a target="_blank" href="'.$attachmentsFolder.'/'.$fileName.'"><img src="'.$attachmentsFolder.'/'.$fileName.'" /></a></div>';
								break;
								default:
									$message.='<div class="pd-5"><a target="_blank" href="'.$attachmentsFolder.'/'.$fileName.'">'.$fileName.'</a></div>';
							}
						}else{
							$error=$uploadStt;
						}
					}
				}
				if( empty($message) ){
					$error='Vui lòng nhập nội dung';
				}
				if( empty($error) ){
					$data[] = [
						"users_id" => user("id"),
						"message"  => $message,
						"time"     => timestamp()
					];
					$data=array_slice($data, -50);//Giữ 50 tin nhắn
					$data=serialize($data);
					//Trả lời cuộc trò chuyện
					$notificationsPlayers=[];
					if( empty($conversation->users_id) ){
						OnlineChatTable::create(["users_id"=>user("id"), "readed_member"=>1, "readed_manager"=>0, "data"=>$data]);
						//Lấy danh sách trình duyệt của quản lý
						foreach( $getChatManagerList as $u){
							$playerList=unserialize($u->notifications_player_ids);
							if( is_array($playerList) ){
								$notificationsPlayers=array_merge($notificationsPlayers, $playerList);
							}
						}
						$notificationsMessage="Có tin nhắn mới từ: ".user("name");
					}else{
						OnlineChatTable::find($conversationID)->update(["readed_member"=>(permission('online_chat_manager') ? 0 : 1), "readed_manager"=>(permission('online_chat_manager') ? 1 : 0), "data"=>$data]);
						if( permission('online_chat_manager') ){
							//Lấy danh sách trình duyệt của thành viên
							$userPlayers=unserialize( user("notifications_player_ids", $conversationID) );
							if( is_array($userPlayers) ){
								$notificationsPlayers=$userPlayers;
							}
							$notificationsMessage=user("name", $conversationID)." đang có một tin nhắn chưa đọc!";
						}else{
							//Lấy danh sách trình duyệt của quản lý
							foreach( $getChatManagerList as $u){
								$playerList=unserialize($u->notifications_player_ids);
								if( is_array($playerList) ){
									$notificationsPlayers=array_merge($notificationsPlayers, $playerList);
								}
							}
							$notificationsMessage="Có tin nhắn mới từ: ".user("name");
						}
					}
				}else{
					$out.=$error;
				}
			}
			$out.='</div>';
		}else{
			//Chưa đăng nhập
			$out.='
			<div class="online-chat-register">
				<div class="form-mrg"> <input class="width-100" type="text" value="" name="online_chat[name]" placeholder="Họ & tên" /> </div>
				<div class="form-mrg"> <input class="width-100" type="text" value="" name="online_chat[phone]" placeholder="Số điện thoại" /> </div>
				<div class="form-mrg"> <textarea class="width-100" name="online_chat[register_message]" placeholder="Quý khách cần tư vấn hay hỗ trợ gì ạ?" rows="4"></textarea> </div>
				<button type="button" class="btn-primary online-chat-start center width-100 form-mrg">ĐỒNG Ý</button>
			</div>
			';
			//Ấn nút bắt đầu chat
			$out.='<div class="online-chat-msg alert-danger hidden form-mrg">';
			if( isset($_POST["online_chat"]["name"]) ){
				$data            = $_POST["online_chat"];
				$data["name"]    = ucwords($data["name"]);
				$data["phone"]   = $data["phone"] ?? "";
				$data["message"] = $data["register_message"] ?? "";
				unset($data["attachment"], $data["register_message"]);
				$dataInvalid = Form::invalid($data, false);
				$error = $dataInvalid["error"];
				$data  = $dataInvalid["data"];
				if( Users::where("phone", $data["phone"])->exists() ){
					$error = '
					Số điện thoại '.$data["phone"].' đã được tạo tài khoản
					<br><br>Vui lòng đăng nhập:
					<br>Tên đăng nhập: '.$data["phone"].'
					<br>Mật khẩu: '.$data["phone"].'
					<br>
					<br>
					<a href="/user/login" class="btn-primary">Đăng nhập ngay</a>
					';
				}
				if( empty($error) ){
					//Tạo tài khoản
					$out .= Users::createTempAccount($data);
				}else{
					$out .= $error;
				}
			}
			$out.='</div>';
		}
		$out.='
		<div class="online-chat-write'.( permission("member") ? '' : ' hidden' ).'">
			<input type="hidden" name="onlineChatConversationID" value="'.($conversationID??0).'" />
			<div class="pd-10 " style="position: relative">
				<span class="link online-chat-attachment">
					<i class="pd-5 fa fa-file"></i> Đính kèm ảnh hoặc file
					<input type="file" class="hidden" name="onlineChatAattachment" multiple />
				</span>
				<i title="Tắt âm báo" class="pd-5 fa fa-volume-up link online-chat-sound blue right-icon"></i>
			</div>
			<div class="online-chat-content">
				<textarea data-uid="'.user("id").'" class="width-100" name="online_chat[message]" rows="6" placeholder="Nhập nội dung..."></textarea>
				<button type="button" class="btn-primary online-chat-submit">Gửi</button>
			</div>
		</div>
		';
		$out.='</form>';
		$out.='';
		$out.='</div>';
		$out.='</section>';

		//Gửi thông báo tới trình duyệt
		if( !empty($notificationsPlayers) ){
			PushNotifications::send([
				"message"=>($notificationsMessage ?? "Bạn có tin nhắn mới"),
				"playerIDS"=>array_unique( $notificationsPlayers )
			]);
		}
		return $out;
	}
}