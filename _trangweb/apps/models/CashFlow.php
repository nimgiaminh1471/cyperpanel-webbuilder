<?php
/*
# Thao tác dữ liệu từ bảng nạp tiền
*/
namespace models;
use DB, Model;

class CashFlow extends Model{
	protected $table      = "cash_flow";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps=true;


}

