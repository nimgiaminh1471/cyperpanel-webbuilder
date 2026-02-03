<?php
namespace pages\api\controllers;
use models\Users;
use models\CashFlow;
use models\CashFlowCategory;
use DB, Form;

class CashFlowAPI{

	/*
	 * Duyệt đơn nạp tiền
	 */
	public function acceptInvoice(){
		$data = POST("contact");
		$order = CashFlow::where("id", POST("id") )->first(true);
		if( empty($order->id) ){
			$error = 'Phiếu không hợp lệ';
		}
		if( !permission('accountant') ){
			$error = 'Không có quyền truy cập';
		}
		if( empty($error) ){
			switch( POST("action") ){
				//Đồng ý duyệt
				case "accept":
					if( $order->status != 1 ){
						Users::where("id", $order->customer_id)->update([ "money"=> (user("money", $order->customer_id)+$order->amount) ]);
						CashFlow::where("id", $order->id)->update(['status' => 1, 'accept_user_id' => user('id')]);
						$msg = 'Đã cộng <b>'.number_format($order->amount).'</b> cho tài khoản '.user("name_color", $order->customer_id).'';
						userPaymentHistory([
							"name"     => "Nạp tiền",
							"amount"   => $order->amount,
							"note"     => "Nạp tiền vào tài khoản",
							"users_id" => $order->customer_id
						]);
					}
				break;

				//Xóa
				case "delete":
					if( $order->customer_id ){
						$msg = 'Đã xóa đơn và trừ tiền <b>'.number_format($order->amount).'</b> tài khoản '.user("name_color", $order->customer_id).'';
						Users::where("id", $order->customer_id)->update([
							"money"=> (user("money", $order->customer_id)-$order->amount)
						]);
					}else{
						$msg = 'Đã xóa phiếu: '.number_format($order->amount);
					}
					CashFlow::where("id", $order->id)->update(['deleted_at' => timestamp()]);
				break;
			}
		}
		return returnData(["error" => $error ?? "", "msg" => $msg ?? ""]);
	}

	/*
	 * Lấy danh sách khách hàng
	 */
	public function getCustomer(){
		$data = Users::select('name', 'phone', 'id')
			->where('name', 'LIKE',  '%'.POST('keyword').'%' )
			->orWhere('phone', 'LIKE',  '%'.POST('keyword').'%' )
			->orderBy('id', 'DESC')
			->limit(10)
			->get()
			->toArray();
		return returnData($data);
	}

	/*
	 * Lưu phiếu thu chi
	 */
	public function updateInvoice(){
		$data = $_POST['invoice'] ?? [];
		if( empty($data['amount']) ){
			$error = 'Vui lòng nhập số tiền';
		}
		if( empty($data['method']) ){
			$error = 'Vui lòng chọn phương thức thanh toán';
		}
		if( empty($data['category_id']) ){
			$error = 'Vui lòng chọn phân loại';
		}
		if( strpos($data['customer'], '|') !== false ){
			$numberPhone = explode('|', $data['customer']);
			$getCustomer = Users::where('phone', $numberPhone[1])->first();
			if( empty($getCustomer->id) ){
				$error = 'Khách hàng không hợp lệ';
			}
		}
		if( empty($error) ){
			$dataToSave = [
				'type'        => $data['type'],
				'customer_id' => $getCustomer->id ?? null,
				'creator_id'  => user('id'),
				'amount'      => toNumber($data['amount']),
				'method'      => $data['method'],
				'category_id' => $data['category_id'],
				'note'        => $data['note']
			];
			if( empty($data['id']) ){
				// Tạo mới
				if( !empty($getCustomer->id) ){
					// Cộng tiền cho tài khoản
					Users::find($getCustomer->id)->update([
						'money' => $getCustomer->money + $dataToSave['amount']
					]);
				}
				$dataToSave['status'] = 1;
				CashFlow::create($dataToSave);
			}else{
				// Cập nhật phiếu
				$getInvoice = CashFlow::find($data['id']);
				if( $getInvoice->status == 1 && !empty($getCustomer->id) ){
					// Thay đổi số tiền trong tài khoản
					Users::find($getCustomer->id)->update([
						'money' => $getCustomer->money - $getInvoice->amount + $dataToSave['amount']
					]);
				}
				if($data['category_id'] != 1){
					$dataToSave['customer_id'] = null;
				}
				$getInvoice->update($dataToSave);
			}
		}
		return returnData(["error" => $error ?? null]);
	}

	/*
	 * Lấy danh sách khách hàng
	 */
	public function updateCategory(){
		$data = $_POST['cash_flow_category'] ?? [];
        foreach($data as $id => $item){
            $item['name'] = strip_tags($item['name']);
            if( empty($item['name']) ){
                continue;
            }
            if( $item['_deleted'] == 1 ){
                // Xóa
                CashFlowCategory::destroy($id);
                CashFlow::find($id)->update(['category_id' => null]);
            }else if( is_numeric($id) ){
                unset($item['_deleted']);
                // Cập nhật
                CashFlowCategory::find($id)->update($item);
            }else{
                unset($item['_deleted']);
                // Thêm mới
                $item['default'] = 0;
                CashFlowCategory::create($item);
            }
        }
		return returnData(["error" => $error ?? null]);
	}

}

