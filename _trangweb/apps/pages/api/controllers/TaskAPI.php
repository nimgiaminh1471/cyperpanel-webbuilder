<?php
namespace pages\api\controllers;
use models\Users;
use models\Task;
use models\TaskCategory;
use DB, Form;

class TaskAPI{

	/*
	 * Lưu công việc
	 */
	public function updateTask(){
		if( !permission('work_manager') ){
			return;
		}
		$data = $_POST['invoice'] ?? [];
		if( empty($data['assign_user_id']) ){
			$error = 'Vui lòng chọn người muốn giao việc';
		}
		if( empty( trim($data['note']) ) ){
			$error = 'Vui lòng nhập nội dung công việc';
		}
		if( empty($data['duration']) ){
			$error = 'Vui lòng chọn thời hạn hoàn thành';
		}
		if( empty($data['category_id']) ){
			$error = 'Vui lòng chọn phân loại';
		}
		if( empty($error) ){
			$data['duration'] = explode(' ', $data['duration']);
			$data['duration']['hour'] = $data['duration'][0];
			$data['duration']['date'] = explode('/', $data['duration'][1]);
			$data['duration'] = "{$data['duration']['date'][2]}-{$data['duration']['date'][1]}-{$data['duration']['date'][0]} {$data['duration']['hour']}:00";
			$dataToSave = [
				'assign_user_id' => $data['assign_user_id'],
				'creator_id'     => user('id'),
				'note'           => trim($data['note']),
				'duration'       => $data['duration'],
				'category_id'    => $data['category_id']
			];
			if( empty($data['id']) ){
				// Tạo mới
				$dataToSave['status'] = 0;
				Task::create($dataToSave);
			}else{
				// Cập nhật phiếu
				$getInvoice = Task::find($data['id']);
				$getInvoice->update($dataToSave);
			}
			// Gửi thông báo tới người được giao
			$notificationsPlayers = unserialize( user('notifications_player_ids', $data['assign_user_id']) );
			if( !empty($notificationsPlayers) ){
				\PushNotifications::send([
					"message"   => cutwords( strip_tags($data['note']), 15).'...',
					"playerIDS" => $notificationsPlayers,
					'url'       => HOME.'/admin/Task'
				]);
			}
		}
		return returnData(["error" => $error ?? null]);
	}

	/*
	 * Cập nhật trạng thái công việc
	 */
	public function updateTaskStatus(){
		if( !permission('work') ){
			return;
		}
		$data = $_POST['invoice'] ?? [];
		if( empty($error) ){
			$dataToSave = [
				'status'    => $data['status']
			];
			if( empty($data['id']) ){
			}else{
				// Cập nhật phiếu
				Task::find($data['id'])->update($dataToSave);
			}
		}
		return returnData(["error" => $error ?? null]);
	}

	/*
	 * Lưu phân loại
	 */
	public function updateCategory(){
		if( !permission('work_manager') ){
			return;
		}
		$data = $_POST['task_category'] ?? [];
        foreach($data as $id => $item){
            $item['name'] = strip_tags($item['name']);
            if( empty($item['name']) ){
                continue;
            }
            if( $item['_deleted'] == 1 ){
                // Xóa
                TaskCategory::destroy($id);
                Task::find($id)->update(['category_id' => null]);
            }else if( is_numeric($id) ){
                unset($item['_deleted']);
                // Cập nhật
                TaskCategory::find($id)->update($item);
            }else{
                unset($item['_deleted']);
                // Thêm mới
                TaskCategory::create($item);
            }
        }
		return returnData(["error" => $error ?? null]);
	}

}

