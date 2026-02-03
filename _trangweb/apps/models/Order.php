<?php
    /*
    # Thao tác dữ liệu từ bảng nạp tiền
    */
    namespace models;
    use classes\InetAPI;
    use DB, Model;

    class Order extends Model{
        protected $table      = "orders";//Bảng
        protected $primaryKey = "id";//Khóa chính
        //protected $fillable   = ["title","content","price"];//Column cho phép thao tác
        protected $guarded    = ['no'];//Column Không cho thao tác
        public $timestamps = true;

    }

