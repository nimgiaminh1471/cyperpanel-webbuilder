<?php
    /*
    # Thao tác dữ liệu từ bảng nạp tiền
    */
    namespace models;
    use classes\InetAPI;
    use DB, Model;

    class InetDomain extends Model{
        protected $table      = "inet_domains";//Bảng
        protected $primaryKey = "id";//Khóa chính
        //protected $fillable   = ["title","content","price"];//Column cho phép thao tác
        protected $guarded    = ['no'];//Column Không cho thao tác
        public $timestamps = true;
        public static function sync_domain($item){
            if( $item->suffix == 'com.vn' ){
                //dd($item);
            }
            if( !Users::where('inet_id', $item->customerId)->exists() ) {
                $inet_customer = InetAPI::connect('customer/get', [
                    'id' => $item->customerId
                ]);
                if(isset($inet_customer->id)){
                    $get_user = Users::where('email', $inet_customer->email)->first();

                    if( empty($get_user->id) ){
                        // Tạo khách hàng nếu chưa có trên hệ thống
                        $user_id = Users::create([
                            'inet_id' => $inet_customer->id,
                            'name' => $inet_customer->fullname,
                            'phone' => $inet_customer->phone,
                            'email' => $inet_customer->email,
                            'password' => $inet_customer->password,
                            'role' => 2
                        ]);
                        Users::updateStorage($user_id,
                            [
                                'address' => $inet_customer->address ?? '',
                            ]);
                    }else{
                        // Cập nhật
                        $user_id = $get_user->id;

                        Users::find($get_user->id)->update([
                            'inet_id' => $inet_customer->id,
                            'name' => $inet_customer->fullname
                        ]);
                        Users::updateStorage($get_user->id,
                            [
                                'address' => $inet_customer->address ?? '',
                            ]);
                    }
                }
            }
            $register_date = date('Y-m-d H:i:s', strtotime($item->createdDate) );
            $expired_date = date('Y-m-d H:i:s', strtotime($item->expireDate) );
            $data = [
                'inet_id' => $item->id,
                'name' => $item->name,
                'inet_customer_id' => $item->customerId,
                'user_id' => Users::where('inet_id', $item->customerId)->value('id'),
                'registrant' => $item->registrant ?? null,
                'contact_token' => $item->contractToken,
                'suffix' => $item->suffix,
                'status' => $item->status,
                'contract' => $item->contract ?? null,
                'verify_status' => $item->verifyStatus ?? null,
                'register_date' => $register_date,
                'expired_date' => $expired_date
            ];
            if( InetDomain::where('inet_id', $item->id)->exists() ){
                InetDomain::where('inet_id', $item->id)->update($data);
            }else{
                InetDomain::create($data);
            }
        }
    }

