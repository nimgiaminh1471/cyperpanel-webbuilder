<?php
    namespace classes;
    use models\InetProvince;
    use Storage, Assets, PageOption, Route;
    use models\InetDomainSuffix;
    /*
    # Kết nối API iNet
    https://github.com/thesunbg/iNET.vn/blob/master/API.md
    */
    class InetAPI{
        private static $host = 'https://dms.inet.vn:443/';
        private static $msg = [
            'api.token.not.found' => 'Token không hợp lệ',
            'can_not_connect' => 'Lỗi kết nối server iNet'
        ];

        /*
         * Kết nối
         * */
        public static function connect($path, $params = [], $host_prefix = 'api/rms/v1/'){
            //dd( json_encode($params) );
            $ssl = false;
            $method = 'POST';
            $ch = curl_init();
            $header = [
                'Token:  '.Storage::setting('builder_inet_api_token', ''),
                'Content-Type: application/json'
            ];
            curl_setopt_array($ch, array(
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_SSL_VERIFYPEER => $ssl,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_URL => self::$host.$host_prefix.$path,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($params)
            ));
            if ($ssl && file_exists(__DIR__."/cacert.pem")) {
                curl_setopt($ch, CURLOPT_CAINFO, realpath(__DIR__."/cacert.pem"));
            }
            $response = curl_exec($ch);
            curl_close ($ch);
            $response = json_decode($response);
            if( empty($response) ){
                $response = new \stdClass();
                $response->status = 'error';
                $response->message = 'can_not_connect';
            }
            if( isset($response->status) && $response->status == 'error' ){
                $response->message = self::$msg[ $response->message ?? 'null'] ?? $response->message ?? 'Lỗi không xác định';
            }
            return $response;
        }

        /*
         * Chuyển dạng JSON sang array
         * */
        public static function json2array($json_body, $print = true){
            if( !$print ){
                return;
            }
            $json_body = json_decode($json_body);
            return dd( var_export($json_body, true) );
        }

        /*
         * Cập nhật đuôi miền
         * */
        public static function update_domain_suffix(){
            $get_data = InetAPI::connect('suffix/list', []);
            foreach($get_data as $item){
                $data = [
                    'inet_id' => $item->id,
                    'suffix' => $item->suffix,
                    'type' => $item->type ?? null,
                    'priority' => $item->priority,
                    'popular' => $item->popular ?? null,
                    'is_vn' => 0
                ];
                if( empty($item->regOrigin) ){
                    continue;
                }
                $data['reg'] = $item->regOrigin ?? 0;
                $data['renew'] = $item->renOrigin ?? 0;

                // Cộng giá có thuế VAT 10%
                $data['reg_vat'] = $data['reg'] + ($data['reg'] / 100) * 10;
                $data['renew'] = $data['renew'] + ($data['renew'] / 100) * 10;
                if( ($item->type ?? null) === 'vn' || strpos($item->suffix, '.vn') !== false ){
                    // Đối với các tên miền VN
                    //dd($item);
                    $data['reg'] = ($item->lePhiDangKy + $item->phiDuyTri + ($item->qtdvNamDau + ($item->qtdvNamDau / 100 * 10) ) );
                    $data['reg_vat'] = $data['reg'];
                    $data['renew'] = $item->renOrigin ?? 0;
                    $data['is_vn'] = 1;
                }
                if( InetDomainSuffix::where('inet_id', $item->id)->exists() ){
                    $data['updated_at'] = timestamp();
                    InetDomainSuffix::where('inet_id', $item->id)->update($data);
                }else{
                    InetDomainSuffix::create($data);
                }
            }
        }

        /*
         * Cập nhật danh sách tỉnh thành
         * */
        public static function update_provinces(){
            foreach(InetAPI::connect('category/provincelist', []) as $item){
                $data = [
                    'inet_id' => $item->id,
                    'name' => $item->name,
                    'label' => $item->value
                ];
                if( InetProvince::where('inet_id', $item->id)->exists() ){
                    $data['updated_at'] = timestamp();
                    InetProvince::where('inet_id', $item->id)->update($data);
                }else{
                    InetProvince::create($data);
                }
            }
        }

    }