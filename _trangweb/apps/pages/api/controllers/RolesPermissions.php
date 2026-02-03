<?php
namespace pages\api\controllers;
use models\Role;
use models\Users;
use models\Permission;
use models\RolePermission;

class RolesPermissions{

	/*
	 * Thêm vai trò mới
	 */
	public function addRole(){
		$data = $_POST["role"];
		if( empty($data["label"]) ){
			$error = "Vui lòng nhập tên";
		}
		if( empty($data["color"]) ){
			$error = "Vui lòng chọn màu";
		}
		if( empty($data["id"]) && Role::where("label", $data["label"])->exists() || isset($data["id"]) && Role::where("id", "!=", $data["id"])->where("label", $data["label"])->exists() ){
			$error = "Tên chức vụ đã tồn tại";
		}
		if( empty($error) ){
			if( empty($data["id"]) ){
				// Tạo mới
				Role::create($data);
			}else{
				// Cập nhật
				Role::find($data["id"])->update($data);
			}
		}
		returnData(["error" => $error ?? null]);
	}

	/*
	 * Xóa vai trò
	 */
	public function deleteRole(){
		$data = $_POST["role"];
		$role = Role::find($data["id"] ?? null);
		if( !$role ){
			$error = "Chức vụ không hợp lệ";
		}
		if( !Role::find($data["move_to"] ?? null)->id ){
			$error = "Vui lòng chọn chức vụ mới cho thành viên đang dùng hiện tại";
		}
		if($role->default == 1){
			$error = "Không thể xóa vai trò mặc định";
		}
		if( empty($error) ){
			Users::where("role", $data["id"])->update([
				"role" => $data["move_to"]
			]);
			Role::find($data["id"])->delete($data);
		}
		returnData(["error" => $error ?? null]);
	}

	/*
	 * Thiết lập quyền cho chức vụ
	 */
	public function setPermissions(){
		$data = $_POST["set_permission"];
		if( !Role::find($data["role_id"] ?? null)->id ){
			$error = "Chức vụ không hợp lệ";
		}else if( $data["role_id"] == 1 && !in_array("admin", $data["permissions"]) ){
			$error = "Bắt buộc phải chọn quyền quản lý hệ thống cho Admin";
		}else if( $data["role_id"] > 1 && in_array("admin", $data["permissions"]) ){
			$error = "Không thể set quyền quản trị hệ thống cho thành viên";
		}
		if( empty($data["permissions"]) ){
			$error = "Vui lòng chọn ít nhất 1 quyền";
		}
		if( empty($error) ){
			RolePermission::where("role_id", $data["role_id"])->delete();
			foreach($data["permissions"] as $p){
				RolePermission::create([
					"role_id"         => $data["role_id"],
					"permission_name" => $p
				]);
			}
			Role::find($data["role_id"])->permissions()->sync($data["permissions"]);
		}
		returnData(["error" => $error ?? null]);
	}
}

