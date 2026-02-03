<?php
/*
# Bài viết
*/
namespace models;
use DB,Model;
use models\PostsCategories;
use models\Posts;

class PostsComments extends Model{
	protected $table      = "posts_comments";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
  	protected $guarded    = ['no'];//Column Không cho thao tác
  	public $timestamps=true;
  	

}

