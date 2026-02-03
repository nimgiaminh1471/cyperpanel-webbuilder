<?php
/*
# Thao tác dữ liệu từ bảng chuyên mục
*/
namespace models;
use DB,Model;

class PostsCategories extends Model{
	protected $table      = "posts_categories";//Bảng
	protected $primaryKey = "id";//Khóa chính
	//protected $fillable   = ["title","content","price"];//Column cho phép thao tác
	protected $guarded    = ['no'];//Column Không cho thao tác
	public $timestamps=true;

	public static $attribute;


	public function posts(){
		return $this->beLongsToMany("models\Posts","posts_categories_ref","categories_id","posts_id");
	}

	//Lấy categories
	public static function get($id){
		if(is_numeric($id)){
			$data=self::where("id", $id)->first();
		}else{
			$data=self::where("link", $id)->first();
		}
		if(isset($data->storage)){
			$data->storage=unserialize($data->storage);
			return $data;
		}
	}

	//Cập nhật & thêm
	public static function change($data){
		extract($data);
		$id=$categories["id"] ?? "";
		$oldLink=self::find($id)->link ?? "";
		if(PostsCategories::where([ ["title","=",$categories["title"]],["parent","=",$categories["parent"]] ])->exists() && $oldLink!=vnStrFilter($categories["title"]) ){
			$error="Chuyên mục đã tồn tại";
		}

		if( $categories["parent"]>0 && !PostsCategories::where("id", $categories["parent"])->exists() ){
			$error="Chuyên mục cha không tồn tại";
		}

		$categories["title"]=trim(strip_tags($categories["title"]));
		if(empty($categories["title"])){
			$error="Tên chuyên mục không được để trống";
		}

		//Lưu dữ liệu
		if(empty($error)){
			$categories["link"]=vnStrFilter($categories["title"]);
			$categories["storage"]=serialize($categories["storage"]);
			if($id>0){
				PostsCategories::find($id)->update($categories);
			}else{
				unset($categories["id"]);
				PostsCategories::create($categories);
			}
		}
		returnData(["error"=>$error ?? ""]);
	}

	//Danh sách chuyên mục con
	public static function children($id,$includeThis=false){
		$getChild=self::select("id")->where("parent",$id)->get();
		$children=[];
		if($includeThis){ $children[]=$id; }
		foreach($getChild as $child){
			$children[]=$child->id;
		}
		return $children ?? [];
	}



	//Danh sách toàn bộ chuyên mục con
	public static function allChildren($id,$includeThis=false){
		$getChild=self::where("id",$id)->value("children");
		$out=unserialize($getChild);
		if($includeThis){ $out=array_merge([$id],$out); }
		return $out;
	}

	//Danh sách toàn bộ chuyên mục cha
	public static function grandparents($id,$includeThis=false){
		$grandparents=self::where("id",$id)->value("grandparents");
		$out=unserialize($grandparents);
		if($includeThis){ $out=array_merge([$id],$out); }
		return $out;
	}



	//Danh sách toàn bộ chuyên mục con (chia cấp)
	public static function multilevelChildren($id,$includeThis=false,$first=true){
		$getChild=self::select("id")->where("parent",$id)->get();
		$children=[];
		foreach($getChild as $child){
			$children[$child->id]=self::multilevelChildren($child->id,$includeThis,false);
		}
		if($includeThis && $first){ $out[$id]=$children; }
		return $out ?? $children;
	}




	//Hiện toàn bộ danh sách chuyên mục con
	public static function showChildren($gid,$first=true){
		if($first){
			self::$attribute["show"]="";
			$children=self::multilevelChildren($gid,true);
		}else{
			$children=$gid;
		}

		foreach($children as $id => $child){
			if(count(self::grandparents($id))>0){
				$class='menu';
			}else{
				$class='cMenu';
			}
			self::$attribute["show"].='<div data-id="'.$id.'" class="'.$class.' bdBot" style="margin-left:'.(count(self::grandparents($id))*3).'0px">
			<a href="/posts-categories/'.self::where("id",$id)->value("link").'">'.self::where("id",$id)->value("title").'</a>
			</div>';
			if(!empty($child)){ self::showChildren($child,false); }
		}

		return self::$attribute["show"];
	}

	//Hiện toàn bộ danh sách chuyên mục
	public static function showAllCategories(){
		$out="";
		foreach(self::select("id")->where("parent",0)->get() as $parent){
			$out.=PostsCategories::showChildren($parent->id);
		}
		return $out;
	}


	//Hiện select toàn bộ danh sách chuyên mục con
	public static function selectChildren($gid,$selected=1,$count=false,$hidden="", $link=false, $first=true){
		if($first){
			self::$attribute["select"]="";
			$children=self::multilevelChildren($gid,true);
		}else{
			$children=$gid;
		}

		foreach($children as $id => $child){
			$outId=$link ? self::where("id",$id)->value("link") : $id;
			if($id!=$hidden){
				self::$attribute["select"].='<option value="'.$outId.'" '.($outId==$selected ? 'selected' : '').'>'.call_user_func(function($number){
					$out="";
					$i=0;
					for($i;$i<$number;$i++){
						$out.="-";
					}
					return $out;
				},count(self::grandparents($id)) ).''.self::where("id",$id)->value("title").''.($count ? ' ('.DB::table("posts_categories_ref")->where("categories_id",$id)->total().')' : '').'</option>';
			}
			if(!empty($child)){ self::selectChildren($child,$selected,$count,$hidden,$link,false); }
		}
		return self::$attribute["select"];
	}

	//Hiện checkbox toàn bộ danh sách chuyên mục con
	public static function checkboxChildren($gid, $name="name", $checked=[], $radioName="", $radioChecked="", $first=true){
		if($first){
			self::$attribute["checkbox"]="";
			$children=self::multilevelChildren($gid,true);
		}else{
			$children=$gid;
		}

		foreach($children as $id => $child){
			self::$attribute["checkbox"].='
			<div data-id="'.$id.'" class="menu bdBot" style="margin-left:'.(count(self::grandparents($id))*3).'0px">
				<label class="check checkbox">
					<input name="'.$name.'" type="checkbox" value="'.$id.'" '.(in_array($id, $checked) ? 'checked' : '').'/>
					'.self::where("id",$id)->value("title").'
					<s></s>
				</label>
				'.(empty($radioName) ? '' : '
				<label class="check radio '.(in_array($id, $checked) ? '' : 'hidden').'">
					<input name="'.$radioName.'" type="radio" value="'.$id.'" '.($id==$radioChecked ? 'checked' : '').' /> <i>Chính</i>
					<s></s>
				</label>
				').'
			</div>';
			if(!empty($child)){ self::checkboxChildren($child, $name, $checked, $radioName, $radioChecked, false); }
		}

		return self::$attribute["checkbox"];
	}

}

