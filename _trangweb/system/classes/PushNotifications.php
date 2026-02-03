<?php
/*
# Gửi thông báo tới trình duyệt người dùng - Onesignal
*/
class PushNotifications{
	private static $apiURL="https://onesignal.com/api/v1/notifications";
	private static $appID="faed255c-b294-4f80-94fd-7ef4f6d0a211";
	private static $restfulKey="OWYxMjBjMTAtMzQ5MS00Yjc5LWI5NzEtNGMwM2Q0YWQwNmRj";

	public static function send($params){
		extract($params);
		$content = [
			"vi" => $message,
			"en" => $message,
		];

		$fields = array(
			'app_id'            => self::$appID,
			'included_segments' => array('All'),
			'data'              => array("foo" => "bar"),
			'large_icon'        =>Storage::option("theme_header_favicon"),
			'contents'          => $content,
			'url'               => $url ?? HOME
		);
		if( !empty($playerIDS) ){
			unset($fields["included_segments"]);
			$fields["include_player_ids"]=$playerIDS;
		}
		$fields = json_encode($fields);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$apiURL);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic '.self::$restfulKey));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	/*
	 * Gửi thông báo cho các tài khoản quản lý trang
	 */
	public static function sendToManager($params, $emailParams = []){
		extract($params);
		$getChatManagerList = \models\Users::select('users.*')
			->leftJoin('roles_permissions', 'users.role', '=', 'roles_permissions.role_id')
			->where('permission_name', 'website_manager')
			->get();
		$notificationsPlayers = $emailParams['To'] = [];
		$emailParams['Body'] .= '<br>Gửi đến: ';
		//Lấy danh sách trình duyệt của quản lý
		$id = 0;
		foreach( $getChatManagerList as $u){
			$playerList = unserialize($u->notifications_player_ids);
			if( is_array($playerList) ){
				$notificationsPlayers = array_merge($notificationsPlayers, $playerList);
			}
			if( $u->email_notify == 1 ){
				$emailParams['To'][] = $u->email;
				$emailParams['Body'] .= ($id > 0 ? ', ' : '').'<b>'.$u->name.'</b>';
				$id++;
			}
		}
		// Tiến hành gửi thông báo
		if( !empty($notificationsPlayers) ){
			self::send([
				"message"   => ($msg ?? "Bạn có tin nhắn mới"),
				"playerIDS" => array_unique( $notificationsPlayers ),
				'url'       => $url ?? HOME
			]);
		}
		if( !empty($emailParams) && count($emailParams['To']) > 0 ){
			// Tiến hành gửi email
			mailer\WebMail::send($emailParams);
		}
	}
}