<?php
    namespace pages\api\controllers;
    use classes\InetAPI;
    use classes\ServicesCart;
    use DB,Storage;
    use models\CashFlow;
    use models\InetDomain;
    use models\InetDomainSuffix;
    use models\Users;
    use models\Order;

    class InetAPIController{
        /*
         * Kiểm tra tên miền đã đăng ký chưa
         */
        public function check_domain(){
            $res = [];
            if( empty($_GET['domain']) ){
                return;
            }
            $domain_name = explode('.', $_GET['domain'])[0];
            if( strlen($domain_name) < 3 ){
                $error = 'Tên miền không được phép đăng ký';
            }
            if( empty($error) ){
                $data = InetAPI::connect('domain/checkavailable', [
                    'name' => $_GET['domain']
                ]);
                $res = [
                    'is_available' => $data->status == 'available' ? true : false,
                    'message' => ''
                ];
                if( empty( $res['is_available'] ) ){
                    $res['message'] = 'Tên miền đã có người đăng ký!';
                }
            }else{
                $res = [
                    'is_available' => false,
                    'message' => $error
                ];
            }

            return returnData($res);
        }

        /*
         * Whois domain
         */
        public function whois(){
            $res = [];
            if( empty($_GET['domain']) ){
                return;
            }
            $res = InetAPI::connect('api/public/whois/v1/whois/directly', ['domainName' => $_GET['domain']], '');
            return json_encode($res);
        }

        /*
         * Thêm vào giỏ hàng
         */
        public function add_to_cart(){
            $res = [];
            if( empty($_GET['value']) ){
                return;
            }
            $value = $_GET['value'];
            $type = $_GET['type'] ?? 'unknown';
            $_SESSION['carts'][$type] = $_SESSION['carts'][$type] ?? [];
            if( isset( $_SESSION['carts'][$type][ $value ] ) ){
                // Xóa khỏi giỏ hàng
                unset( $_SESSION['carts'][$type][ $value ] );
                $res['is_add'] = false;
            }else{
                // Thêm vào giỏ hàng
                $_SESSION['carts'][$type][ $value ] = $value;
                $res['is_add'] = true;
            }
            return returnData($res);
        }

        /*
         * Tạo đơn hàng
         */
        public function checkout(){
            $res = [
                'error' => ''
            ];
            $user = $_POST['user'] ?? [];
            $services = $_POST['services'] ?? [];
            if( empty($user) || empty($services) ){
                $res['error'] = 'Dữ liệu không hợp lệ';
            }
            $user_id = user('id');
            if( permission('accountant') ){
                $user_id = $user['id'] ?? Users::where('email', $user['email'])->value('id');
            }else{
                if( Users::where("phone", $user['phone'])->where("id", "!=", $user_id)->exists() ){
                    $res['error'] = "Số điện thoại đã tồn tại trên hệ thống";
                }
            }
            $field_validate = [
                'gender' => [
                    'min' => 1,
                    'max' => 1,
                    'label' => 'giới tính'
                ],
                'name' => [
                    'min' => 5,
                    'max' => 30,
                    'label' => 'họ và tên'
                ],
                'birthday' => [
                    'min' => 8,
                    'max' => 12,
                    'label' => 'ngày sinh'
                ],
                'email' => [
                    'min' => 5,
                    'max' => 50,
                    'label' => 'email'
                ],
                'phone' => [
                    'min' => 8,
                    'max' => 15,
                    'label' => 'số điện thoại'
                ],
                'card_id' => [
                    'min' => 9,
                    'max' => 20,
                    'label' => 'số CMND/Căn cước'
                ],
                'province' => [
                    'min' => 2,
                    'max' => 10,
                    'label' => 'tỉnh thành'
                ],
                'address' => [
                    'min' => 10,
                    'max' => 150,
                    'label' => 'địa chỉ'
                ],
            ];
            foreach($field_validate as $name => $vald){
                if( empty($user[ $name ]) || strlen($user[ $name ]) > $vald['max'] || strlen($user[ $name ]) < $vald['min'] ){
                    $res['error'] = 'Vui lòng nhập <b>'.$vald['label'].'</b> hợp lệ';
                }
            }
            if( Users::where("email", $user['email'])->where("id", "!=", $user_id)->exists() ){
                $res['error'] = "Email đã tồn tại trên hệ thống";
            }

            if(!filter_var($user['email'], FILTER_VALIDATE_EMAIL)){
                $res['error'] = "Email không hợp lệ";
            }
            if( empty($user_id) ){
                $res['error'] = "Tài khoản không tồn tại trên hệ thống, hãy ấn tạo tài khoản";
            }
            if( strlen($user['company']) > 0 && strlen($user['company_tax']) < 6 ){
                $res['error'] = 'Mã số thuế không hợp lệ';
            }
            $amount = 0;
            $services_data = [];
            $sv_cart = ServicesCart::get_services();
            foreach ($services as $id => $item){
                $sv = ServicesCart::get_services()[$id] ?? [];
                if( empty($sv) ){
                    $res['error'] = 'Dịch vụ trong giỏ hàng không hợp lệ';
                    continue;
                }
                $sv->price = $sv->price[ $item['year'] ];
                $services_data[ $id ] = $sv;
                $amount += $sv->price->price_vat;
            }
            $user['email'] = strtolower($user['email']);
            if( empty($res['error']) ){
                // Cập nhật thông tin tài khoản
                Users::find( $user_id )->update([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'gender' => $user['gender'],
                ]);
                Users::updateStorage( $user_id, [
                    'birthday' => $user['birthday'],
                    'province' => $user['province'],
                    'address' => $user['address'],
                    'card_id' => $user['card_id'],
                    'company' => $user['company'],
                    'company_tax' => $user['company_tax'],
                ] );

                // Tạo tài khoản bên iNet
                $user_bithday = explode('/', $user['birthday']);
                $user_bithday = "{$user_bithday[2]}-{$user_bithday[1]}-{$user_bithday[0]}";
                $get_inet_user = InetAPI::connect('customer/getbyemail', [
                    'email' => mb_strtolower($user['email']),
                ]);
                if( empty( user('inet_id', $user_id) ) && empty($get_inet_user->id) ){
                    $inet_res = InetAPI::connect('customer/create', [
                        'email' => $user['email'],
                        'password' => randomString(20),
                        'fullname' => $user['name'],
                        'organizationName' => '',
                        'gender' => $user['gender'] == 1 ? 'male' : 'female',
                        'birthday' => $user_bithday.' 00:00',
                        'country' => 'VN',
                        'province' => $user['province'],
                        'address' => $user['address'],
                        'phone' => $user['phone'],
                    ]);
                    if( empty($inet_res->id) ){
                        $res['error'] = $inet_res->message ?? 'Không thể tạo tài khoản inet';
                    }else{
                        Users::find( $user_id )->update([
                            'inet_id' => $inet_res->id,
                        ]);
                    }
                }else{
                    InetAPI::connect('customer/updateinfo', [
                        'id' => user('inet_id', $user_id),
                        'email' => $user['email'],
                        'password' => randomString(20),
                        'fullname' => $user['name'],
                        'organizationName' => '',
                        'gender' => $user['gender'] == 1 ? 'male' : 'female',
                        'birthday' => $user_bithday.' 00:00',
                        'country' => 'VN',
                        'province' => $user['province'],
                        'address' => $user['address'],
                        'phone' => $user['phone'],
                    ]);
                    Users::find( $user_id )->update([
                        'inet_id' => $get_inet_user->id,
                    ]);
                }
                // Tạo đơn hàng
                $order_data = [
                    'user_id' => $user_id,
                    'services' => json_encode($services_data),
                    'amount' => $amount,
                    'agency_amount' => $_POST['agency_price']
                ];
                $order = Order::create($order_data);
                $res['order_id'] = $order;
                // Xóa toàn bộ giỏ hàng
                unset($_SESSION['carts']);
            }

            return returnData($res);
        }

        /*
         * Gia hạn
         */
        public function checkout_renew(){
            $res = [
                'error' => ''
            ];
            $services = $_POST['services'] ?? [];
            if( empty($services) ){
                $res['error'] = 'Dữ liệu không hợp lệ';
            }
            $amount = [];
            $services_data = [];
            $sv_cart = ServicesCart::get_services();
            foreach ($services as $id => $item){
                $sv = ServicesCart::get_services()[$id] ?? [];
                if( empty($sv) ){
                    $res['error'] = 'Dịch vụ trong giỏ hàng không hợp lệ';
                    continue;
                }
                $sv->price = $sv->price[ $item['year'] ];
                $services_data[ $sv->user_id ][ $id ] = $sv;
                if( empty($amount[ $sv->user_id ]) ){
                    $amount[ $sv->user_id ] = 0;
                }
                $amount[ $sv->user_id ] += $sv->price->price_vat;
            }

            foreach($services_data as $user_id => $item){
                // Tạo đơn hàng
                $order_data = [
                    'user_id' => $user_id,
                    'services' => json_encode($item),
                    'amount' => $amount[ $user_id ],
                    'agency_amount' => $_POST['agency_price']
                ];
                $order = Order::create($order_data);
                $res['order_id'] = $order;
            }

            // Xóa toàn bộ giỏ hàng
            unset($_SESSION['carts']);
            return returnData($res);
        }

        /*
         * Hủy đơn hàng
         */
        public function cancel_order(){
            $res = [];
            if( !permission('accountant') ){
                return;
            }
            Order::find($_POST['id'])->update([
                'status' => 9,
                'user_mod' => user('id')
            ]);
            return returnData($res);
        }

        /*
         * Kích hoạt đơn hàng
         */
        public function active_order(){
            $res = [
                'error' => ''
            ];
            $order = Order::find($_POST['id']);
            if( !permission('accountant') ){
                if( $order->amount > user('money') ){
                    $res['Số dư tài khoản không đủ: '.number_format($order->amount)];
                }
            }
            $user = user('', $order->user_id);
            $domain_suffix = InetDomainSuffix::get_suffix();
            $cash_flow_note = '';

            foreach(json_decode($order->services) as $item){
                switch ($item->type){
                    // Đăng ký tên miền
                    case 'domain':
                        if( empty($user->storage['province']) ){
                            $res['error'] = 'Tài khoản '.$user->name.' chưa điền tỉnh thành';
                            returnData($res);
                        }
                        if( empty($user->storage['province']) ){
                            $res['error'] = 'Tài khoản '.$user->name.' chưa điền địa chỉ';
                            returnData($res);
                        }
                        if( empty($user->phone) ){
                            $res['error'] = 'Tài khoản '.$user->name.' chưa điền SĐT';
                            returnData($res);
                        }
                        if( empty($user->storage['card_id']) ){
                            $res['error'] = 'Tài khoản '.$user->name.' chưa điền số CMND/CC';
                            returnData($res);
                        }
                        if( empty($user->storage['birthday']) ){
                            $res['error'] = 'Tài khoản '.$user->name.' chưa điền ngày sinh';
                            returnData($res);
                        }
                        if( strlen($user->storage['company'] ?? '') > 0 && strlen($user->storage['company_tax'] ?? '') < 6 ){
                            $res['error'] = 'Mã số thuế không hợp lệ';
                            returnData($res);
                        }
                        $user_contact = [
                            'fullname' => $user->name,
                            'organization' => false,
                            'email' => $user->email,
                            'country' => 'VN',
                            'province' => $user->storage['province'],
                            'address' => $user->storage['address'],
                            'phone' => $user->phone,
                            'fax' => '',
                            'type' => 'admin',
                            'gender' => $user->gender == 1 ? 'male' : 'female',
                            'idNumber' => $user->storage['card_id'],
                            'birthday' => $user->storage['birthday'],
                        ];
                        $user_contact_registrant = $user_contact;
                        if( empty($user->storage['company']) ){
                            // Đăng ký dưới tên cá nhân
                        }else{
                            // Đăng ký dưới tên công ty
                            $user_contact_registrant['organization'] = true;
                            $user_contact_registrant['fullname'] = $user->storage['company'];
                            $user_contact_registrant['taxCode'] = $user->storage['company_tax'];
                            if( $domain_suffix[$item->suffix]->is_vn ){
                                // Nếu là tên miền VN
                            }else{
                                // Tên miền quốc tế
                                $user_contact = $user_contact_registrant;
                            }
                        }
                        $inet_res = InetAPI::connect('domain/create', [
                            'name' => $item->value,
                            'period' => $item->price->year,
                            // Số năm
                            'customerId' => $user->inet_id,
                            // ID khách hàng iNet
                            'nsList' => [
                                ['hostname' => Storage::setting('builder_inet_ns_1')],
                                ['hostname' => Storage::setting('builder_inet_ns_2')],
                            ],
                            'contacts' => [
                                array_replace($user_contact_registrant, ['type' => 'registrant']),
                                array_replace($user_contact, ['type' => 'admin']),
                                array_replace($user_contact, ['type' => 'technique']),
                                array_replace($user_contact, ['type' => 'billing']),
                            ],
                        ]);
                        if( ($inet_res->status ?? null) == 'error'){
                            $res['error'] = ''.$item->value.' >>> iNet: '.$inet_res->message;
                        }else{
                            // Đăng ký thành công
                            $inet_data = InetAPI::connect('domain/search', [
                                'pageSize' => 1,
                                'name' => $item->value,
                            ]);
                            foreach($inet_data->content as $ditem){
                                InetDomain::sync_domain($ditem);
                            }
                            // Thêm bản ghi
                            sleep(1);
                            $recordList = [];
                            $recordList[] = [
                                'type' => 'CNAME',
                                'name' => '@',
                                'data' => DOMAIN,
                                'action' => 'add',
                            ];
                            $recordList[] = [
                                'type' => 'CNAME',
                                'name' => '*',
                                'data' => DOMAIN,
                                'action' => 'add',
                            ];
                            InetAPI::connect('domain/updaterecord', [
                                'id' => $inet_res->id,
                                'recordList' => $recordList
                            ]);
                        }
                        $res['move_to'] = '/admin/DomainManager';
                        break;
                    // Gia hạn tên miền
                    case 'domain_renew':
                        $inet_res = InetAPI::connect('domain/renew', [
                            'id' => InetDomain::where('name', $item->value)->value('inet_id'),
                            'period' => $item->price->year, // Số năm
                        ]);
                        if( ($inet_res->status ?? null) == 'error'){
                            $res['error'] = ''.$item->value.' >>> iNet: '.$inet_res->message;
                        }else{
                            // Gia hạn thành công
                            $inet_data = InetAPI::connect('domain/search', [
                                'pageSize' => 1,
                                'name' => $item->value,
                            ]);
                            foreach($inet_data->content as $ditem){
                                InetDomain::sync_domain($ditem);
                            }
                        }
                        $res['move_to'] = '/admin/DomainManager';
                        break;
                }
                $cash_flow_note .= ' '.$item->label.' ('.$item->price->label.'): '.$item->value.' | ';
            }
            if( empty($res['error']) ) {
                if( permission('accountant') ){
                    // Nếu là kế toán
                    // Tạo phiếu thu
                    $cash_flow = [
                        'amount' => $order->agency_amount,
                        'amount_total' => $order->agency_amount,
                        'method' => '',
                        'customer_id' => user('id',
                            $order->user_id),
                        'customer_name' => user('name',
                            $order->user_id),
                        'status' => 1,
                        'type' => 0,
                        'category_id' => 1,
                        'creator_id' => user('id'),
                        'note' => rtrim($cash_flow_note,
                            '| ')
                    ];
                    CashFlow::create($cash_flow);
                }else{
                    // Thành viên tự gia hạn
                    userPayment($order->amount);
                    userPaymentHistory([
                        "name"     => "Kích hoạt dịch vụ",
                        "amount"   => -$order->amount,
                        "note"     => $cash_flow_note,
                        "users_id" => null
                    ]);
                }

                $order->update([
                    'status' => 1,
                    'user_mod' => user('id')
                ]);
            }
            return returnData($res);
        }

        /*
         * Đồng bộ tên miền
         */
        public function sync_domain(){
            $res = [
                'error' => ''
            ];
            if( !permission('accountant') ){
                return;
            }

            if( $_POST['page'] == 1 ){
                InetDomain::truncate();
            }

            $inet_data = InetAPI::connect('domain/search', [
                'pageSize' => 100,
                'page' => ($_POST['page']) - 1
            ]);
            //dd($inet_data);
            foreach($inet_data->content as $item){
                InetDomain::sync_domain($item);
            }

            $res['total_page'] = $inet_data->totalPages;
            return returnData($res);
        }

        public function sync_dns(){
            $domain_id = $_POST['id'] ?? null;
            $domain_detail = InetAPI::connect('domain/detail', [
                'id' => $domain_id
            ]);
            $dns = [];
            foreach($domain_detail->nsList as $dn){
                $dns[] = $dn->hostname;
            }
            asort($dns);
            $dns = array_values($dns);
            $data = [
                'contacts' => json_encode($domain_detail->contacts),
                'dns' => json_encode($dns, true),
            ];
            InetDomain::where('inet_id', $domain_id)->update($data);
            returnData($dns);
        }
        public function sync_record(){
            $domain_id = $_POST['id'] ?? null;
            $record = InetAPI::connect('domain/getrecord', [
                'id' => $domain_id
            ]);
            $data = [
                'record' => json_encode($record->recordList ?? null),
            ];
            InetDomain::where('inet_id', $domain_id)->update($data);
            returnData($record->recordList);
        }

        /*
         * Cập nhật DNS
         */
        public function update_dns(){
            $res = [
                'error' => ''
            ];
            if( !InetDomain::where('inet_id', $_POST['inet_id'])->where('user_id', user('id'))->exists() ){
                if( !permission('accountant') ){
                    $res['error'] = 'Bạn không có quyền truy cập';
                }
            }
            if( empty($_POST['ns']) ){
                $res['error'] = 'Vui lòng nhập NS';
            }
            if( empty($res['error']) ){
                $dns = [];
                foreach( $_POST['ns'] as $val){
                    $dns[] = [
                        'hostname' => $val
                    ];
                }
                //dd($dns);
                $update_dns = InetAPI::connect('domain/updatedns', [
                    'id' => $_POST['inet_id'],
                    'nsList' => $dns,
                ]);
                $res['error'] = $update_dns->message ?? '';
                if( strpos($res['error'], 'Required parameter missing') !== FALSE ){
                    $res['error'] = 'Không có gì thay đổi';
                }
                $res['error'] = str_replace(['Object association prohibits operation', ' ns not found'], ['Không thể kết nối đến ', ''], $res['error']);
                if( empty($res['error']) ){
                    $inet_data = InetAPI::connect('domain/search', [
                        'pageSize' => 1,
                        'page' => 0,
                        'name' => InetDomain::where('inet_id', $_POST['inet_id'])->value('name')
                    ]);
                    foreach($inet_data->content as $item){
                        InetDomain::sync_domain($item);
                    }
                }
            }
            return returnData($res);
        }

        /*
         * Bản ghi tên miền
         */
        public function record(){
            $res = [
                'error' => ''
            ];
            if( !InetDomain::where('inet_id', $_POST['inet_id'])->where('user_id', user('id'))->exists() ){
                if( !permission('accountant') ){
                    $res['error'] = 'Bạn không có quyền truy cập';
                }
            }
            if( empty($res['error']) ){
                switch ($_POST['action']){
                    case 'update':
                        if( empty($_POST['record_id']) ){
                            // Thêm bản ghi
                            $recordList = [];
                            $recordList[] = [
                                'type' => $_POST['type'],
                                'name' => $_POST['name'],
                                'data' => $_POST['data'],
                                'priority' => $_POST['priority'] ?? '',
                                'action' => 'add',
                            ];
                            $res['data'] = InetAPI::connect('domain/updaterecord', [
                                'id' => $_POST['inet_id'],
                                'recordList' => $recordList
                            ]);
                        }else{
                            // Cập nhật bản ghi
                            $recordList = [];
                            $recordList[] = [
                                'type' => $_POST['type'],
                                'name' => $_POST['name'],
                                'data' => $_POST['data'],
                                'priority' => $_POST['priority'] ?? '',
                                'action' => 'add',
                            ];
                            $recordList[] = [
                                'id' => $_POST['record_id'],
                                'type' => $_POST['type'],
                                'name' => $_POST['name'],
                                'data' => $_POST['data'],
                                'priority' => $_POST['priority'] ?? '',
                                'action' => 'del',
                            ];
                            $res['data'] = InetAPI::connect('domain/updaterecord', [
                                'id' => $_POST['inet_id'],
                                'recordList' => $recordList
                            ]);
                        }
                        break;
                    case 'del':
                        $res['data'] = InetAPI::connect('domain/updaterecord', [
                            'id' => $_POST['inet_id'],
                            'recordList' => [
                                [
                                    'id' => $_POST['record_id'],
                                    'type' => 'A',
                                    'name' => 'noname',
                                    'data' => '127.0.0.1',
                                    'priority' => '',
                                    'action' => 'del',
                                ],
                            ]
                        ]);
                        break;
                }
            }
            if( isset($res['data']->recordList) ){
                InetDomain::where('inet_id', $_POST['inet_id'])->update([
                    'record' => json_encode($res['data']->recordList)
                ]);
            }

            return returnData($res);
        }
        /*
         * Bản ghi tên miền
         */
        public function get_user(){
            $keyword = $_POST['keyword'] ?? '';
            $get_data = Users::where('id', '>', 0);
            if( !empty($keyword) ){
                $get_data = $get_data->where('name', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('phone', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('email', 'LIKE', '%'.$keyword.'%');
            }
            $get_data = $get_data->limit(3)
                ->orderBy('id', 'DESC')
                ->get()
                ->toArray();
            $data = [];
            if( empty($keyword) ){
                $get_data = array_merge($get_data, Users::where('id', user('id') )->get()->toArray() );
            }
            foreach($get_data as $item){
                $item = (object)$item;
                $id = $item->id;
                $data[$id] = $item;
                $data[$id]->storage = unserialize($item->storage ?? null);
                $avatar_path = '/files/users/avatars/'.$item->id.'.png';
                if( file_exists(PUBLIC_ROOT.$avatar_path) ){
                    $data[$id]->avatar = '/files/users/avatars/'.$item->id.'.png';
                }else{
                    $data[$id]->avatar = '/files/users/avatars/0.png';
                }
            }
            returnData($data);
        }

        /*
         * Xác minh CMND/CC
         */
        public function upload_id_card(){
            $res = [
                'error' => ''
            ];
            if( !InetDomain::where('id', $_POST['domain_id'])->where('user_id', user('id'))->exists() ){
                if( !permission('accountant') ){
                    $res['error'] = 'Bạn không có quyền truy cập';
                }
            }
            if( empty($_FILES['front_end']['name']) || empty($_FILES['back_end']['name']) ){
                $res['error'] = 'Vui lòng chọn file chứng minh thư/căn cước';
            }
            $user = Users::find( InetDomain::where('id', $_POST['domain_id'])->value('user_id') );
            $domain = InetDomain::find($_POST['domain_id']);
            $id_card_folder = '/files/id-card/';
            $id_card_path = PUBLIC_ROOT.$id_card_folder;
            if( !is_dir($id_card_path) ){
                mkdir($id_card_folder, 0755, true);
            }
            //dd($_FILES['front_end']);
            $id_card_front_name = md5( $user->email ).'_front.jpeg';
            $id_card_back_name = md5( $user->email ).'_back.jpeg';
            move_uploaded_file($_FILES['front_end']['tmp_name'], $id_card_path.'/'.$id_card_front_name);
            move_uploaded_file($_FILES['back_end']['tmp_name'], $id_card_path.'/'.$id_card_back_name);
            $ct_id = json_decode($domain->contacts)[0]->id;
            /*dd( [
                "id" => $ct_id,
                "documentType" => "frontEnd", // (hoặc backEnd là mặt sau)
                "url" => HOME.$id_card_folder.$id_card_front_name
            ] );*/
            InetAPI::connect('contact/uploadidnumber', [
                "id" => $ct_id,
                "documentType" => "frontEnd", // (hoặc backEnd là mặt sau)
                "url" => str_replace('https://', 'http://', HOME).$id_card_folder.$id_card_front_name
            ]);
            InetAPI::connect('contact/uploadidnumber', [
                "id" => $ct_id,
                "documentType" => "backEnd", // (hoặc backEnd là mặt sau)
                "url" => str_replace('https://', 'http://', HOME).$id_card_folder.$id_card_back_name
            ]);
            returnData($res);
        }

        /*
         * Xác minh CMND/CC
         */
        public function get_fund_amount()
        {
            $res = [
                'error' => ''
            ];
            $inet_org = \classes\InetAPI::connect('organization/detailself', []);
            $res['inet_fund'] = $inet_org->fund ?? 0;
            returnData($res);
        }

        /*
         * Lấy giá đại lý
         */
        public function get_agency_price()
        {
            $res = [
                'error' => ''
            ];
            $res['amount'] = 0;
            foreach($_POST['data'] as $item){
                $inet_res = InetAPI::connect('fee/getprice', [
                    'serviceType' => 'domain',
                    'action' => $item['type'],
                    'name' => $item['value'],
                    'period' => $item['year']
                ]);
                $res['amount'] += $inet_res->value ?? 0;
            }
            returnData($res);
        }
    }