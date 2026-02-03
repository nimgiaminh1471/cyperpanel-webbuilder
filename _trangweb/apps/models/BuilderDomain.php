<?php
/*
# Thao tác dữ liệu từ bảng tên miền
*/
namespace models;
use DB,Model;

class BuilderDomain extends Model{
	protected $table      = "builder_domain";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps=true;


}

