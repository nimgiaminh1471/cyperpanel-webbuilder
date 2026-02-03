<?php
namespace pages\api\controllers;
use DB,Storage, PushNotifications;
use models\AppStore;
use models\AppStoreCategories;
use models\AppStoreOwned;

class AppStoreAPI{

	/*
	 * Cập nhật chuyên mục
	 */
	public function updateCategory(){
		$data = $_POST['category'] ?? [];
		if( empty($data['name']) ){
			$error = 'Vui lòng nhập đầy đủ thông tin';
		}
		if( !permission('website_manager') ){
			$error = 'Không có quyền truy cập';
		}
		if( empty($error) ){
			if( empty($data['id']) ){
				$saveData = new AppStoreCategories;
			}else{
				$saveData = AppStoreCategories::find($data['id']);
			}
			foreach($data as $key => $value){
				$saveData->$key = $value;
			}
			$saveData->save();
		}
		returnData([
			'error' => $error ?? ''
		]);
	}

	/*
	 * Xóa chuyên mục
	 */
	public function deleteCategory(){
		$id = $_POST['id'] ?? 0;
		if( !permission('website_manager') ){
			$error = 'Không có quyền truy cập';
		}
		if( empty($error) ){
			AppStoreCategories::destroy($id);
		}
		returnData([
			'error' => $error ?? ''
		]);
	}
	
	/*
	 * Cập nhật ứng dụng
	 */
	public function updateApp(){
		$data = $_POST['app'] ?? [];
		foreach($data as $key => $value){
			if($key == 'id' || $key == 'paid_content' || $key == 'plugin_file' && $data['type'] != 'plugin') {
				continue;
			}
			if( strlen($value) == 0 ){
				$error = 'Vui lòng nhập đầy đủ thông tin';
			}
		}
		$data['required_domain'] = $data['required_domain'] ?? 0;
		if( !permission('website_manager') ){
			$error = 'Không có quyền truy cập';
		}
		if( empty($error) ){
			if( empty($data['id']) ){
				$saveData = new AppStore;
			}else{
				$saveData = AppStore::find($data['id']);
			}
			$data['price'] = toNumber($data['price']);
			foreach($data as $key => $value){
				$saveData->$key = $value;
			}
			$saveData->save();
		}
		returnData([
			'error' => $error ?? ''
		]);
	}

	/*
	 * Xóa ứng dụng
	 */
	public function deleteApp(){
		$id = $_POST['id'] ?? 0;
		if( !permission('website_manager') ){
			$error = 'Không có quyền truy cập';
		}
		if( empty($error) ){
			$item = AppStore::find($id);
			\Gallery::deleteByFilePath( $item->image );
			\Gallery::deleteByFilePath( $item->plugin_file );
			AppStore::destroy($id);
		}
		returnData([
			'error' => $error ?? ''
		]);
	}

	/*
	 * Cài ứng dụng
	 */
	public function installApp(){
		extract($_POST['app'] ?? []);
		if( !permission('member') ){
			$error = 'Không có quyền truy cập';
		}
		$app = AppStore::find($id);
		if( empty($app->id) ){
			$error = 'Ứng dụng không hợp lệ';
		}else{
			if( $app->required_domain == 1 && empty($domain) ){
				$error = 'Vui lòng chọn tên miền bạn muốn cài';
			}
			$error = $error ?? checkMoneyCorrect($app->price);
			if( $app->type == 'plugin' && !file_exists(PUBLIC_ROOT.'/'.$app->plugin_file) ){
				$error = 'File plugin không hợp lệ, vui lòng liên hệ hỗ trợ';
			}
			if( $app->type == 'plugin' && empty($domain) ){
				$error = 'Cần chọn tên miền để cài plugin';
			}
		}
		if( empty($error) ){
			// Lưu app đã mua
			switch($app->renew_type){
				case 'monthly':
					$expired = strtotime('+30 days'); // Hết hạn sau 30 ngày
				break;
				case 'yearly':
					$expired = strtotime('+1 year'); // Hết hạn sau 1 năm
				break;
				default:
					$expired = null;
			}
			// Cài các gói
			switch($app->type){
				case 'plugin':
					$installPluginPath = \classes\WebBuilder::userPublic($domain).'/builder/install-plugins/';
					if( !is_dir($installPluginPath) ){
						mkdir($installPluginPath, 0755, true);
					}
					copy(
						PUBLIC_ROOT.'/'.$app->plugin_file,
						$installPluginPath.basename($app->plugin_file)
					);
				break;
			}
			$getOwned = AppStoreOwned::where('user_id', user('id'))->where('app_id', $app->id)->first();
			$dataOwned = [
				'user_id'   => user('id'),
				'app_id'    => $app->id,
				'app_name'  => $app->name,
				'app_price' => $app->price,
				'domain'    => $domain ?? null,
				'expired'   => $expired
			];
			// Cài lần đầu
			AppStoreOwned::create($dataOwned);
			$payment = true;
			if( $payment ){
				// Lưu lịch sử giao dịch
				if( !permission("website_manager") && $app->price > 0 ){
					userPayment($app->price);
					userPaymentHistory([
						"name"     => "Mua gói dịch vụ",
						"amount"   => -$app->price,
						"note"     => '
						'.(empty($domain) ? '' : 'Website: <b>'.$domain.'</b>').'
						<br>
						Gói: '.$app->name.'
						',
						"users_id" => null
					]);
					$data = [
						'browser' => [
							'msg' => 'Khách mới cài plugin: '.$app->name,
							'url' => HOME.'/admin/AppStoreManager'
						],
						'email' => [
							"To"          => [],
							"Subject"     => "[".DOMAIN."] Khách mới cài plugin: {$app->name} #".user('phone')."",
							"Body"        => "
							Email: <b>".user('email')."</b>
							<br>
							Gói plugin: <b>{$app->name}</b>
							<br>
							Giá plugin: <b>".number_format($app->price)."</b>
							<br>
							Tên miền: <b>".(empty($domain) ? '' : 'Website: <b>'.$domain.'</b>')."</b>
							<br>
							<br>
							Chi tiết tại: ".HOME."/admin/AppStoreManager
							",
							"Attachments" => []
						]
					];
					PushNotifications::sendToManager($data['browser'], $data['email']);
				}
			}
		}
		returnData([
			'error'          => $error ?? '',
			'success_notify' => $app->paid_content ?? ''
		]);
	}
}