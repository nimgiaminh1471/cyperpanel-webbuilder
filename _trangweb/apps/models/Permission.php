<?php
namespace models;
use Model;
use DB;

class Permission extends Model{
	protected $table      = "permissions";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps = false;


}

