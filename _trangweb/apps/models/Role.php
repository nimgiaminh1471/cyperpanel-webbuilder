<?php
namespace models;
use Model;
use DB;

class Role extends Model{
	protected $table      = "roles";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps = false;
	private static $rolesPermissions = [];

	/*
	 * Danh sách quyền thuộc chức vụ
	 */
	public function permissions(){
		return $this->beLongsToMany("models\Permission", "roles_permissions", "role_id", "permission_name");
	}

	/*
	 * Lấy danh sách chức vụ
	 */
	public static function getPermissionsByRole($roleId){
		if( isset(self::$rolesPermissions[$roleId]) ){
			return self::$rolesPermissions[$roleId];
		}
		self::$rolesPermissions[$roleId] = [];
		foreach(RolePermission::where("role_id", $roleId)->get() as $pr){
			self::$rolesPermissions[$roleId][$pr->permission_name] = true;
		}
		return self::$rolesPermissions[$roleId];
	}

}

