<?php
namespace models;
use DB,Model;

class RolePermission extends Model{
	protected $table      = "roles_permissions";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps = false;
}