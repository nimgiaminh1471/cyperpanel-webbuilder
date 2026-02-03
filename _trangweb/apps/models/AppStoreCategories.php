<?php
namespace models;
use DB,Model;

class AppStoreCategories extends Model{
	protected $table      = "app_store_categories";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps=true;


}