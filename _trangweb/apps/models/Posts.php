<?php
/*
# Bài viết
*/
namespace models;
use DB, Model;
use models\PostsCategories;
use models\Users;
use Gallery;
use classes\Sitemap;
use models\PostsComments;

class Posts extends Model{
	protected $table      = "posts";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
  	protected $guarded    = ['no'];//Column Không cho thao tác
  	public $timestamps=true;
  	
  	//Liên kết bảng chuyên mục
	public function categories(){
		return $this->beLongsToMany("models\PostsCategories", "posts_categories_ref", "posts_id", "categories_id");
	}

	//Xóa bài viết theo Id
	public static function deletePosts($ids){
		if(!is_array($ids)){ $ids=[$ids]; }
		foreach($ids as $id){
			$uid=self::find($id)->users_id;
			Gallery::deleteByPostsId($id);//Xóa files đính kèm
			DB::table("posts_categories_ref")->where("posts_id",$id)->delete();//Xóa dữ liệu trong bảng trung gian
			PostsComments::where("posts_id", $id)->delete();//Xóa bình luận
			self::destroy($id);//Xóa bài viết
			Users::find($uid)->update(["posts_count"=>user("posts_count", $uid)-1 ]);//Trừ bài của người đăng
		}
	}

	//Lấy bài viết
	public static function get($id, $output=false){
		$select=is_numeric($id) ? 'id' : 'link';
		$data=self::where($select, $id)->first(true);
		if(isset($data->storage)){
			$data->storage=unserialize($data->storage);
			if($output){
				$data->content=htmlEditorOutput($data->content);
			}
		}
		
		return $data;
	}


	//Cập nhật bài viết
	public static function change($params){
		$p=(object)$params;
		if($p->id>0){
			$old=self::find($p->id);
		}
		$p->title=trim(strip_tags($p->title));
		if(is_numeric($p->title)){
			$error='Tiêu đề bài viết phải gồm ít nhất 1 chữ cái';
		}
		if(empty($p->link)){
			$p->link=$p->title;
		}
		$p->link=vnStrFilter($p->link);
		if(Posts::where("link","=",$p->link)->where("id","!=",$p->id??0)->exists()){
			$error='Link bài viết: <a target="_blank" href="/'.$p->link.'">'.$p->link.'</a> đã tồn tại';
		}
		// if( strlen($p->content)>20000 ){
		// 	$error="Nội dung bài viết không được vượt quá 20k ký tự, hãy chia làm nhiều bài viết";
		// }
		//Không được để trống
		$notEmpty=[
			"title"=>"Tiêu đề",
			"content"=>"Nội dung"
		];
		if($_POST["postSubmit"]=="autoSave"){
			unset($notEmpty["content"]);
		}
		foreach($notEmpty as $var=>$text){
			if(empty($p->$var)){
				$error="$text không được để trống!";
			}
		}
		
		//Check quyền chỉnh sửa
		if( $p->id>0 && Posts::find($p->id)->users_id!=user("id") && !permission("post_manager") ){
			$error="Bạn không có quyền chỉnh sửa bài viết này";
		}

		//Không cho phép gửi JavaScript
		if( preg_match('/<([\s]*)script/',$p->content) && !permission("post_manager") ){
			$error="Bạn không được phép dùng JavaScript";
		}

		//Lưu dữ liệu
		if(empty($error)){
			$data=[
				"title"=>$p->title,
				"link"=>$p->link,
				"content"=>$p->content,
				"pin"=>$p->pin??0,
				"parent"=>$p->parent??0,
				"updated_at"=>timestamp()
			];
			$storage=Posts::get($p->id)->storage??[];
			if(!is_array($storage)){
				$storage=[];
			}
			foreach(POST("postStorage", []) as $key=>$value){
				if(is_array($value)){
					$storageValue=[];
					foreach($value as $k=>$v){
						if( isset($storage[$key][$k]) && is_array($v) ){
							$storageValue[$k]=array_merge($storage[$key][$k], $v);
						}else{
							$storageValue[$k]=$v;
						}
					}
				}else{
					$storageValue=$value;
				}
				$storage[$key]=$storageValue;
			}

			$getPoster=DB::table("files")->where("posts_id", $p->id)->where("type", "image")->first();
			if( empty($storage["posterPath"]) && !empty($getPoster->id) ){
				$storage["posterPath"]=$getPoster->folder."".$getPoster->name;
			}
			$storage["poster"]=[];
			$storage["poster"]["original"]=$storage["posterPath"];
			foreach(["small", "medium"] as $size){
				$storage["poster"][$size]=imageOtherSize($storage["posterPath"], $size);
			}
			$storage["last_editor"]=user("id");
			$storage["description"]=cutWords($p->content,70);
			$data["storage"]=serialize($storage);
			$data["users_id"]=user("id");

			if($p->id>0){
				//Cập nhật
				$data["status"]=$old->status;
				if($_POST["postSubmit"]=="save"){
					$data["status"]="public";
				}
				if($_POST["postSubmit"]=="delete"){
					$data["status"]=$old->status=="trash" ? "public" : "trash";
				}
				$data["users_id"]=$old->users_id;
				Posts::find($p->id)->update($data);
			}else{
				//Tạo mới
				$data["status"]="draft";
				$data["created_at"]=timestamp();
				$p->id=Posts::insertGetId($data);
				Users::find( user("id") )->update(["posts_count"=>user("posts_count")+1]);
			}
			//Lưu chuyên mục
			if(isset($p->parents)){
				Posts::find($p->id)->categories()->sync($p->parents);
			}else{
				Posts::find($p->id)->categories()->detach();
			}
		}
		Sitemap::update();
		returnData(["error"=>$error ?? "", "link"=>"/".$p->link??""."", "id"=>$p->id]);
	}

	//Cập nhật storage bài viết
	public static function updateStorage($id, $newData){
		$data=Posts::get($id)->storage??[];
		foreach($newData as $key=>$value){
			$data[$key]=$value;
		}
		Posts::where("id", $id)->update( ["storage"=>serialize($data)] );
	}

}

