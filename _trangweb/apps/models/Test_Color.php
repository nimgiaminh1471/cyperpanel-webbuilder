<?php
/*
# Thao tác dữ liệu từ bảng
*/
namespace apps\models;
use Model;
use DB;

class Color extends Model{
	protected $table      = "color";//Bảng
	protected $primaryKey = "id";//Khóa chính
	protected $fillable   = ["color_name","note","price","password"];//Column cho phép thao tác
  //protected $guarded    = ['text','desc1'];//Column Không cho thao tác
	protected $timestamps=true;

	public function car(){
		return $this->beLongsToMany("apps\models\Car","car_color","car_id","color_id");
	}

}

