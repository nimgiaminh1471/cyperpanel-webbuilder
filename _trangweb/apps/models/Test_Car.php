<?php
/*
# Thao tác dữ liệu từ bảng
*/
namespace apps\models;
use system\core\Model;
use DB;

class Car extends Model{
	protected $table      = "car";//Bảng
	protected $primaryKey = "id";//Khóa chính
	protected $fillable   = ["name","description","price","password"];//Column cho phép thao tác
  //protected $guarded    = ['text','desc1'];//Column Không cho thao tác
	protected $timestamps=true;

	public function color(){
		return $this->beLongsToMany("apps\models\Color","car_color")
		->withPivot('id', 'note')
		->wherePivot("id",">","1")
		->withTimestamps()
		;
	}

}

