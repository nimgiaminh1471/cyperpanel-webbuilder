<?php
namespace models;
use DB,Model;

class AppStore extends Model{
	protected $table      = "app_store";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps=true;

	public static function getItem($params = []){
		extract($params);
		$getItem = AppStore::where('id', '>', 0)->orderBy('updated_at', 'DESC');
		if( !empty($category) ){
			$getItem = $getItem->where('category', '=', $category);
		}
		if( !empty($keyword) ){
			$getItem = $getItem->where('name', 'LIKE', '%'.$keyword.'%');
		}
		$getItem = $getItem->get();
		$getCategories = AppStoreCategories::orderBy('updated_at', 'DESC')->get()->keyBy('id');
		$data = [];
		foreach($getItem as $key => $value){
			$data[$key] = $value;
			$data[$key]->categories = $getCategories[$value->category] ?? null;
			$data[$key]->owned = AppStoreOwned::where('user_id', user('id'))
			->where( 'app_id', $value->id )
			->get()->toArray();
		}
		return $data;
	}
}

