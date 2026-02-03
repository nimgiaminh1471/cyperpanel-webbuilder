<?php
/*
# Thao tác dữ liệu từ bảng chat online
*/
namespace models;
use DB, Model;
use Folder;

class OnlineChatTable extends Model{
	protected $table      = "online_chat";//Bảng
	protected $primaryKey = "users_id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps=true;

	//Xóa cuộc trò chuyện
	public static function deleteConversation($id){
		$attachmentsFolder="/assets/online-chat/attachments/".$id;
		Folder::clear(PUBLIC_ROOT.$attachmentsFolder, false);
		self::destroy($id);
	}

}

