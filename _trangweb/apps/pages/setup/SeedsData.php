<?php
/*
# Tạo dữ liệu mẫu
*/
use models\Users;
use models\Role;
use models\Permission;
use models\RolePermission;

DB::table("storage")
	->where("key", "actived")
	->create(["key"=>"actived", "value"=>1]);

// Tạo chức vụ mặc định
DB::table("roles")
	->where("id", 1)
	->create([
		"id"      => 1,
		"label"   => "Admin",
		"color"   => "#FF0000",
		"default" => 1
	]);
DB::table("roles")
	->where("id", 2)
	->create([
		"id"      => 2,
		"label"   => "Thành viên",
		"color"   => "#000000",
		"default" => 1
	]);
// Tạo quyền mặc định
Permission::truncate();
$permissions = [
	"member"                   => "Thành viên đăng nhập",
	"admin"                    => "Quản trị hệ thống",
	"users_manager"            => "Quản lý thành viên",
	"add_user"                 => "Thêm thành viên",
	"website_template_master"  => "Cài plugin cho web mẫu",
	"website_manager"          => "Quản lý các website",
	"website_template_manager" => "Quản lý web mẫu",
	"recharge"                 => "Nạp tiền vào tài khoản",
	"accountant"               => "Kế toán (Tạo, sửa phiếu thu - chi...)",
	"post"                     => "Đăng bài viết",
	"post_manager"             => "Quản lý bài viết",
	"online_chat_manager"      => "Quản lý tin nhắn chat online",
	"seller"                   => "Cộng tác viên bán website",
	"agency"                   => "Đại lý bán website",
	"work"                     => "Công việc",
	"work_manager"             => "Phân chia công việc",
	"app_store"                => "Kho ứng dụng",
	"create_website"           => "Tạo website",
	"send_notification"        => "Gửi thông báo đến thành viên",
	"change_user_password"     => "Đổi mật khẩu thành viên",
	"telesales"                => "Telesale khách hàng"
];

foreach($permissions as $name => $label){
	Permission::create([
		"name"  => $name,
		"label" => $label
	]);
}

// Set quyền thành viên mặc định
foreach(Role::all() as $role){
	if( !RolePermission::where("role_id", $role->id)->where("permission_name", "member")->exists() ){
		RolePermission::create([
			"role_id"         => $role->id,
			"permission_name" => "member" 
		]);
	}
}


// Set quyền Admin mặc định
if( !RolePermission::where("role_id", 1)->where("permission_name", "admin")->exists() ){
	RolePermission::create([
		"role_id"         => 1,
		"permission_name" => "admin"
	]);
}