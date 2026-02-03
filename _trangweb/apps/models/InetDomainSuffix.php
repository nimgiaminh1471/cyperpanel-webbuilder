<?php
    /*
    # Thao tác dữ liệu từ bảng nạp tiền
    */
    namespace models;
    use classes\InetAPI;
    use DB, Model;

    class InetDomainSuffix extends Model{
        protected $table      = "inet_domain_suffix";//Bảng
        protected $primaryKey = "id";//Khóa chính
        //protected $fillable   = ["title","content","price"];//Column cho phép thao tác
        protected $guarded    = ['no'];//Column Không cho thao tác
        public $timestamps = true;

        public static function get_suffix(){
            global $Schedule;
            $Schedule->H( function(){
                // Cập nhật lại mỗi giờ
                InetAPI::update_domain_suffix();
            });
            // InetAPI::update_domain_suffix(); // Update
            if( !InetDomainSuffix::where('id', '>', 1)->exists() ){
                InetAPI::update_domain_suffix();
            }
            $data = [];
            foreach( InetDomainSuffix::orderBy('priority', 'ASC')->get() as $item){
                $data[ $item->suffix ] = $item;
                $data[ $item->suffix ]->reg_price = toNumber( \Storage::setting("builder_inet_reg_price_{$item->suffix}", $item->reg) ); // Giá đăng ký (Chưa VAT)
                $data[ $item->suffix ]->reg_price_origin = $item->reg; // Giá đăng ký gốc
                $data[ $item->suffix ]->reg_price_vat = toNumber( \Storage::setting("builder_inet_reg_price_{$item->suffix}_vat", $item->reg_vat) ); // Giá đăng ký (+VAT)

                $data[ $item->suffix ]->renew_price = toNumber( \Storage::setting("builder_inet_renew_price_{$item->suffix}", $item->renew) ); // Giá gia hạn
                $data[ $item->suffix ]->renew_price_origin = $data[ $item->suffix ]->renew_price;
                $data[ $item->suffix ]->renew_price_vat = $data[ $item->suffix ]->renew_price;

                // Giảm giá cho đại lý & cộng tác viên
                if( permission('website_manager') ){
                    continue;
                }
                $discount_agency = \Storage::setting('builder_inet_discount_'.user('role'));
                //dd($discount_agency);
                if( $discount_agency > 0 ){
                    $data[ $item->suffix ]->reg_price -= ($data[ $item->suffix ]->reg_price / 100) * $discount_agency;
                    $data[ $item->suffix ]->reg_price_vat -= ($data[ $item->suffix ]->reg_price_vat / 100) * $discount_agency;
                }
            }

            return $data;
        }

    }

