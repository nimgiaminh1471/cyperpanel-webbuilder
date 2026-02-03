<?php
    namespace classes;
    use models\InetDomain;
    use models\InetDomainSuffix;
    use Storage;
    /*
    # Lấy các thông số dịch vụ trong giỏ hàng
    */
    class ServicesCart{
        public static function get_services(){
            $carts = $_SESSION['carts'] ?? [];
            $out = [];
            $domain_suffix = InetDomainSuffix::get_suffix();
            foreach($carts as $type => $items){
                foreach($items as $value) {
                    switch ($type) {
                        // Tên miền
                        case 'domain':
                            $suffix = explode('.', $value);
                            unset($suffix[0]);
                            $suffix = implode('.', $suffix);
                            $suffix_price = $domain_suffix[$suffix];
                            $prices = [];
                            for($y = 1; $y <= 10; $y++){
                                // Giá đăng ký
                                $prices[$y] = (object)[
                                    'expire' => strtotime('+'.$y.' years'),
                                    'label' => $y.' Năm',
                                    'price' => $suffix_price->reg_price,
                                    'price_origin' => $suffix_price->reg_price_origin,
                                    'price_vat' => $suffix_price->reg_price_vat,
                                    'year' => $y
                                ];
                                if( $y > 1 ){
                                    // Giá đăng ký cho các năm tiếp theo
                                    $prices[$y]->price = $suffix_price->reg_price + ($suffix_price->reg_price_origin * ($y - 1) );
                                    $prices[$y]->price_origin = $suffix_price->reg_price_origin + ($suffix_price->reg_price_origin * ($y - 1) );
                                    $prices[$y]->price_vat = $suffix_price->reg_price_vat + ($suffix_price->reg_price_vat * ($y - 1) );
                                }
                            }

                            $out[] = (object)[
                                'type' => $type,
                                'value' => $value,
                                'label' => 'Đăng ký tên miền',
                                'note' => '',
                                'suffix' => $suffix,
                                'price' => $prices
                            ];
                            break;

                        // Gia hạn tên miền
                        case 'domain_renew':
                            $suffix = explode('.', $value);
                            unset($suffix[0]);
                            $suffix = implode('.', $suffix);
                            $suffix_price = $domain_suffix[$suffix];
                            $prices = [];
                            for($y = 1; $y <= 10; $y++){
                                // Giá gia hạn
                                $prices[$y] = (object)[
                                    'expire' => strtotime('+'.$y.' years'),
                                    'label' => $y.' Năm',
                                    'price' => $suffix_price->renew_price,
                                    'price_origin' => $suffix_price->renew_price_origin,
                                    'price_vat' => $suffix_price->renew_price_vat,
                                    'year' => $y
                                ];
                                if( $y > 1 ){
                                    // Giá gia hạn cho các năm tiếp theo
                                    $prices[$y]->price = $suffix_price->renew_price + ($suffix_price->renew_price * ($y - 1) );
                                    $prices[$y]->price_origin = $suffix_price->renew_price + ($suffix_price->renew_price * ($y - 1) );
                                    $prices[$y]->price_vat = $suffix_price->renew_price_vat + ($suffix_price->renew_price_vat * ($y - 1) );
                                }

                            }

                            $out[] = (object)[
                                'type' => $type,
                                'value' => $value,
                                'label' => 'Gia hạn tên miền',
                                'note' => '',
                                'suffix' => $suffix,
                                'price' => $prices,
                                'user_id' => InetDomain::where('name', $value)->value('user_id')
                            ];
                            break;
                    }
                }
            }
            return $out;
        }
    }