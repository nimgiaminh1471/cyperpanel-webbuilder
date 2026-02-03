<?php
/*
# Thao tác dữ liệu từ bảng files
*/
namespace models;
use Model;
use DB;

class PaymentHistory extends Model{
	protected $table      = "payment_history";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps=true;


}

