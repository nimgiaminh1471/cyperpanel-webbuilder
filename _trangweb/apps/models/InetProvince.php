<?php
    /*
    # Thao tác dữ liệu từ bảng nạp tiền
    */
    namespace models;
    use classes\InetAPI;
    use DB, Model;

    class InetProvince extends Model{
        protected $table      = "inet_provinces";//Bảng
        protected $primaryKey = "id";//Khóa chính
        //protected $fillable   = ["title","content","price"];//Column cho phép thao tác
        protected $guarded    = ['no'];//Column Không cho thao tác
        public $timestamps = true;

        public static function get_provinces(){
            global $Schedule;
            InetAPI::update_provinces();
            $Schedule->m( function(){
                // Cập nhật lại mỗi tháng
                InetAPI::update_provinces();
            });
            $data = InetProvince::orderBy('inet_id', 'ASC')->get()->keyBy('name');
            return $data;
        }

    }

