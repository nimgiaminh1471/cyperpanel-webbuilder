<?php
namespace models;
use DB,Model;

class AppStoreOwned extends Model{
	protected $table      = "app_store_owned";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps=true;

	public static function getItem($params = []){
		extract($params);
		$getItem = self::with(['user', 'app'])->where('id', '>', 0);
			if( !empty($user_id) ){
				$getItem = $getItem->where('user_id', $user_id);
			}
		$getItem = $getItem->orderBy('updated_at', 'DESC');
		return $getItem->paginate(10);
	}
	public function user(){
		return $this->beLongsTo("models\Users", "id", "user_id");
	}
	public function app(){
		return $this->hasOne("models\AppStore", "id", "app_id");
	}
}

