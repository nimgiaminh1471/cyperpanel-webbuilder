<?php
/*
# Thao tác dữ liệu từ bảng
*/
namespace models;
use Model;
use DB;

class Users extends Model{
	protected $table      = "users";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["nick","email","name","password","role","login_key"];//Column cho phép thao tác
  	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps=true;

	//Chức vụ
	public static function role($level = 0){
		$roleMap = [];
		foreach(Role::all() as $role){
			$roleMap[$role->id] = $role->label;
		}
		return $level > 0 ? ($roleMap[$level] ?? "Chưa set quyền") : $roleMap;
	}

	//Cập nhật storage user
	public static function updateStorage($id, $newData){
		$data=user("storage", $id);
		foreach($newData as $key=>$value){
			$data[$key]=$value;
		}
		self::find($id)->update( ["storage"=>serialize($data)] );
	}

	//Bài viết
	public function posts(){
		return $this->hasMany("models\Posts");
	}

	// Tạo tài khoản khách hàng liên hệ
	public static function createTempAccount($data){
		$data["name"]             = ucwords($data["name"]);
		$data["email"]             = $data["email"] ?? '';
		$data["password"]         = passwordCreate($data["password"] ?? $data["phone"]);
		$data["role"]             = $data["role"] ?? 2;
		$data["login_key"]        = md5("".time()."".randomString(50)."");
		$data["register_message"] = $data["message"] ?? null;
		unset($data['captcha'], $data['message']);
		if(
			strpos($data["register_message"], 'https://') !== false ||
			strpos($data["register_message"], 'http://') !== false
		){
			return 'Quý khách vui lòng không gửi nội dung chứa liên kết chứa https://';
		}
		if( empty( user('id') ) ){
			$data['storage'] = serialize([
				'notify_to_manager' => [
					'browser' => [
						'msg' => 'Có tài khoản mới đăng ký: '.$data['name'],
						'url' => HOME.'/admin/Telesales'
					],
					'email' => [
						"To"          => [],
						"Subject"     => "[".DOMAIN."] Có khách hàng mới: {$data['name']} #{$data["phone"]}",
						"Body"        => "
						Email: <b>{$data["email"]}</b>
						<br>
						Số điện thoại: <b>{$data["phone"]}</b>
						<br>
						Gửi lúc: <b>".date("H:i - d/m/Y")."</b>
						<br>
						Nội dung: <b>".($data["register_message"] ?? "")."</b>
						<br>
						<br>
						Gửi từ: ".str_replace(HOME, '', $_SERVER["HTTP_REFERER"] ?? THIS_LINK)."
						<br>
						Chi tiết tại: ".HOME."/admin/Telesales
						",
						"Attachments" => []
					]
				]
			]);
			if( empty($data['email']) ){
				$data['email'] = 'noemail-'.$data['phone'].'@'.DOMAIN;
			}
			Users::create($data);
		}
		setcookie("login_key", $data["login_key"], time()+3600*24*365, "/");
	}
}

