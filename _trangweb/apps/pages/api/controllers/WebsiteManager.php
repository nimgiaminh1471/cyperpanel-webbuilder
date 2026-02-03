<?php
namespace pages\api\controllers;
use models\Users;
use DB,Storage, CreateBackup;
use models\BuilderDomain;
use classes\WebBuilder;

class WebsiteManager{
	/*
	 * Tạo backup
	 */
	public function createBackup(){
		$res = [];
		if( permission("website_manager") ){
			$web = BuilderDomain::where( "id", POST("id") )
				->first();
		}else{
			$web = BuilderDomain::where( "id", POST("id") )
				->where("users_id", user("id") )
				->first();
		}
		if( empty( $web->id ) ){
			$res['error'] = 'Web không hợp lệ';
		}
		if( empty($res['error']) ){
			$res['data'] = CreateBackup::run( $web->domain, $_POST["onlyDatabase"] ?? false, $_POST["reinstall"] ?? false, $_POST["download"] ?? false );
		}
		return returnData($res);
	}

	/*
	 * Cập nhật web mẫu
	 */
	public function updateWebTemplate(){
		$res = [];
		if( !permission('website_manager') ){
			return;
		}
		$web = BuilderDomain::where( "id", POST("id") )->first();
		WebBuilder::updateSSL(
			$web->domain,
			Storage::setting("builder_ssl_private_key"),
			Storage::setting("builder_ssl_certificate"),
			Storage::setting("builder_ssl_cacert")
		);
		return self::createBackup();
	}

	/*
	 * Cập nhật trạng thái liên hệ website sắp hết hạn
	 */
	public function websiteUpdateContactStatus(){
		$res = [];
		if( !permission('website_manager') ){
			return;
		}
		$data = $_POST['contact_status'] ?? [];
		$web = BuilderDomain::where( "id", $data['id'])->first();
		$web->update([
			'contact_status' => $data['status'],
			'contact_detail' => serialize([
				'note'    => $data['note'],
				'time'    => time(),
				'user_id' => user('id')
			])
		]);
		return returnData($res);
	}
}