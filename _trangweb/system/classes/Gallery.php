<?php
/*
##### Class upload file
*/
use models\Files;

class Gallery{
	
	private static $config, $option=[];
	private static $called=false;

	public static function setup($option){
		self::$config=[
			"folder"=>"/files/uploads/".date("Y/m")."/",//Thư mục chứa file
			"fileExclude"=>["php", "exe", "htaccess"],//Đuôi không cho phép
			"maxUpload"=>"1000M",//Cỡ tối đa
			"logo"=>Storage::option("theme_header_logo"),//Logo đóng dấu
			"permission"=>"member",//Quyền truy cập
			"imageSize"=>[//Các ảnh thu nhỏ
				"small"     => ["width" =>350, "height" =>0],
				"medium"    => ["width" =>800, "height" =>0],
			]
		];
		self::$option=$option;
		if( !permission(self::$config["permission"]) || self::$called){
			self::$called=true;
			return;
		}
		if(isset($_POST["type"])){
			Storage::update("gallery_config", $_POST);
		}
		switch(POST("_galleryAction")){
			//Tải lên
	    	case "upload":
				$out=self::galleryUpload();
	    	break;

			//Thông tin file
	    	case "detail":
				$out=self::detail(POST('id','INT'));
	    	break;

			//Xóa 1 file qua id
	    	case "delete":
				$out=self::delete(POST('id','INT'));
	    	break;

			//Thay đổi tên, mô tả file qua id
	    	case "update":
				$out=self::update(POST("id","INT"), $_POST["file_name"], $_POST["file_desc"]);
	    	break;

	    	//Xoay ảnh
	    	case "rotate":
				$out=self::rotate(POST("id", "INT"));
	    	break;

	    	//Box nội dung
		    default:
	    		$out=self::main();
		}
		return '<div class="gallery">'.$out.'</div>';
	}
	
	//Upload
	/*
		$uploadStt=Gallery::upload([
			"form"=>"file",//Tên form chứa file
			"folder"=>ROOT."/files",//Thư mục lưu
			"name"=>"",//Tên file (để trống sẽ tự đặt)
			"ext"=>["jpg", "jpeg", "png"],//Cho phép đuôi
			"overwrite"=>true,//Ghi đè file đã tồn tại
			"maxSize"=>9000, //Cỡ tối đa (Kb)
		]);
		if(is_null($uploadStt)){
			echo 'Thành công';
		}else{
			echo $uploadStt;
		}
	*/
	public static function upload($params){
		extract($params);
		if(!file_exists($folder)){
			mkdir($folder,0755,true);
		}
		
		$file=$_FILES[$form];
		if( (folderSize(PUBLIC_ROOT)+$file["size"])>=mb2Bytes(CONFIG["MAXDISK"]) ){
			$wrong="Website đã hết dung lượng lưu trữ!";
		}
		$fileName=pathinfo($file["name"], PATHINFO_FILENAME);
		$fileExt=vnStrFilter( pathinfo($file["name"], PATHINFO_EXTENSION) );
		if(empty($name)){
			$name=vnStrFilter($fileName, "-", false).".".$fileExt;
		}
		if(strpos($name, ".")===false){
			$name=$name.".".$fileExt;
		}
		$save=$folder."/".$name;
		if(file_exists($save) && !$overwrite){
			$wrong="File đã tồn tại";
		}
		if(empty($file["name"])){
			$wrong="File không hợp lệ";
		}else if( !in_array($fileExt, $ext) ){
			$wrong="Chỉ cho phép định dạng: ".implode(",", $ext)." ";
		}else if($file["size"]>$maxSize*1000){
			$wrong="Tệp tin quá lớn, hãy chọn tệp khác";
		}

		if(empty($wrong)){
			if(! move_uploaded_file($file["tmp_name"], $save) ){
				$wrong="Lỗi upload, hãy thử lại";
			}
		}
		return $wrong ?? null;
	}

	// Main hiển thị chính
	public static function main($out=""){
		$out.='
		'.Assets::show("/assets/gallery/style.css", "/assets/gallery/script.js").'
			<!--Tab upload & file manager-->
			<div class="gallery-modal" id="galleryWrap"'.(self::$option["hidden"] ? ' style="display:none"' : '').'>
				<div class="gallery-modal-body gallery-tab">
					<div class="heading-block">
						Quản lý tệp tin
						'.(self::$option["close"] ? '<span data-show="hidden" style="float:right"><i class="fa fa-window-close-o" aria-hidden="true"></i></span>' : '').'
					</div>
					<!--Upload-->
					<form class="gallery-form gallery-tab-body gallery-upload-body" action="">
						<div class="flex flex-middle pd-15">
							<div class="width-50">
								<div class="flex flex-middle flex-medium">
									<div class="gallery-upload-btn btn-primary small-full-width" style="width: calc(100% - 120px);margin-right: 2px">
										<i class="fa fa-folder-o" aria-hidden="true"></i> Tải lên <input class="gallery-upload-file" name="file[]" type="file" multiple="multiple" />
									</div>
									<div style="margin-left: 5px" class="small-full-width">
										'.(empty( Storage::option("theme_header_logo") ) ? '' : '<label class="check"><input type="checkbox" value="1" id="galleryUploadPhotoStamp" '.(Storage::gallery_config("galleryUploadPhotoStamp")=='1' ? 'checked' :'').'/> <s></s> Gắn logo</label>').'
									</div>
								</div>
							</div>
							<div class="right width-50">
								<select data-option="find_by" class="gallery-option small-full-width">';
									foreach(array("name"=>"Tìm tên", "id"=>"Tìm ID", "description"=>"Tìm mô tả") as $key=>$value){
									$out.='<option value="'.$key.'" '.(POST('type')==$key ? 'selected' : '').'>'.$value.'</option>';
								}
							$out.='
								</select>
								<input type="text" data-option="find" class="gallery-option small-full-width" placeholder="Tìm file" />
							</div>
						</div>
						<div class="gallery-upload-list"></div>
						<div class="gallery-max-upload" data-size="'.ini_get('post_max_size').'" data-bytes="'.self::max_upload_byte(ini_get('post_max_size')).'"></div>
					</form>
					<!--/Upload-->

					<!--File manager-->
					<div class="gallery-tab-body gallery-manager" style="position: relative;">
						
						<input type="hidden" data-option="parent_id" class="gallery-option" value="0" />
						<input type="hidden" data-option="parent_column" class="gallery-option" value="posts_id" />
						<div id="galleryFilesList">'.self::manager().'</div>
					</div>
					<!--/File manager-->

					
					
				</div>
				
			</div>
			<!--/Tab upload & file manager-->
		';
		return $out;
	}
	
	
	
	
	
	// Tải lên
	public static function galleryUpload(){
		$dir=PUBLIC_ROOT."/".self::$config["folder"];
		if(!file_exists($dir)){
			mkdir($dir,0755,true);
			mkdir($dir."/images",0755,true);
		}
		if(!empty($_FILES["gallery"]["name"])){
			$file_info = pathinfo($_FILES["gallery"]["name"]);
			$file_name = vnStrFilter($file_info['filename'],'-',false);
			$file_ext  = vnStrFilter($file_info['extension']);
			$path_save = "$dir$file_name.$file_ext";
			$max_size_byte = self::max_upload_byte(self::$config["maxUpload"]);

			if( in_array($file_ext, self::$config["fileExclude"]) ){
				$error = "Không cho phép đuôi .$file_ext";
			}
			if(file_exists($path_save)){ $error = "File đã tồn tại"; }
			if($_FILES["gallery"]["size"]>$max_size_byte){ $error = "File không được lớn hơn ".self::$config["maxUpload"]." "; }
			if( (folderSize(PUBLIC_ROOT)+$_FILES["gallery"]["size"])>=mb2Bytes(CONFIG["MAXDISK"]) ){
				$error="Website đã hết dung lượng lưu trữ!";
			}
			if(empty($error)){
				if(move_uploaded_file($_FILES["gallery"]["tmp_name"],$path_save)){
				//Tạo ảnh thu nhỏ
					$img_size="";
					if(self::type($path_save)=="image" && $file_ext!="gif"){
						foreach(self::$config["imageSize"] as $key=>$size){
							if(Image::resize($path_save, "".$dir."/images/".$key."_$file_name.$file_ext", $size["width"], $size["height"])){
								$img_size.="/".$key."_$file_name.$file_ext";
							}
						}
						if(POST("galleryUploadPhotoStamp")==1){ Image::copyright($path_save, $path_save, PUBLIC_ROOT."".self::$config["logo"], 1, 1); }
						Storage::update("gallery_config", ["galleryUploadPhotoStamp"=>POST("galleryUploadPhotoStamp")]);
					}

					//Lưu vào database
					$data = [
						"name"   => "$file_name.$file_ext",
						"size"   => ltrim($img_size,"/"),
						"folder" => self::$config["folder"],
						"description"   => $file_info["filename"],
						"type"   => self::type($path_save),
						"users_id" => user("id")
					];
					if( (int)POST("parent_id")>0 && strlen( POST("parent_column") )>0 ){
						$data[POST("parent_column")]=POST("parent_id");
					}
					Files::create($data);
					$info=["error" => 0, "link" => "".$file_name.""];
				}
			}

			if(isset($error)){
				$info=["error" => $error];
			}
			return '<textarea id="galleryUploadMsg">'.json_encode($info).'</textarea>';
		}
	}


	
	
	
	
	// Quản lý file
    public static function manager($out=""){
		$parent_id=POST("parent_id", 0);
		//Đang kiểm tra file bị xóa
		if(isset($_POST['checking'])){
			return '<div id="galleryCheckingCount">'.Files::where("deleted",2)->total().'</div>';
		}

		//Kiểu file cần hiện
		$show_type=POST("type");
		if($show_type=="null"){
			if((int)$parent_id>0){
				$show_type="parent";
			}else{
				$show_type="";
			}
		}

		if(empty($show_type)){
			DB::query("UPDATE `files` SET `deleted` = '2'");
		}else{
			//Kiểm tra file bị xóa
			if($show_type=='deleted'){
				$files=Files::where("deleted",2)->limit(50)->get();
				foreach($files as $file){
					$path=PUBLIC_ROOT."/".$file->folder."".$file->name;
					if(file_exists($path)){
						Files::where("id", $file->id)->update(["deleted"=>0]);
					}else{
						Files::where("id", $file->id)->update(["deleted"=>1]);
					}
				}
				$checking=Files::where("deleted",2)->total();
				if($checking>0){
					$out.='<div class="alert-success">Đang kiểm tra: <b>'.$checking.'</b> file</div>';
				}else{
					$out.='<div class="alert-danger">Đã kiểm tra hoàn tất, <b>'.Files::where("deleted",1)->total().'</b> file bị lỗi </div>';
				}

				$where[]=["deleted","=",1];
			}else if($show_type=='parent'){
				if($parent_id>0){
					$where[]=[POST("parent_column"),"=",$parent_id];
				}else{
					$whereNull[]=["posts_id"];
					$whereNull[]=["products_id"];
				}
			}else{
				$where[]=["type","=",$show_type];
			}
		}

		//Tìm file
		if(!empty($_POST["find"])){
			$where[]=[POST("find_by"),"LIKE", "".(POST("find_by")=="name" || $_POST["find_by"]=="description" ? "%".POST("find")."%" : POST("find")).""];
		}

		//Phân loại file
		$out.='<div class="pd-10"><select data-option="type" class="gallery-option">';
		$type=[
			"Kiểu file" => [""=>"Tất cả", "image"=>"Hình ảnh", "video"=>"Video clip", "audio"=>"Âm thanh", "document"=>"Tài liệu", "file"=>"File Khác"],
			"Phân loại" => ["deleted"=>"File bị xóa", "parent"=>"".($parent_id==0 ? "Chưa" : "")." Đính kèm"]
		];
		foreach($type as $label=>$ftype){
			$out.='<optgroup label="'.$label.'">';
			foreach($ftype as $key=>$value){
				if($key=="deleted"){
					$count="*";
				}elseif($key=="parent"){
					if($parent_id>0){
						$count=Files::where(POST("parent_column"), $parent_id)->total();
					}else{
						$count=Files::whereNull("posts_id")->whereNull("products_id")->total();
					}
				}else if(!empty($key)){
					$count=Files::where("type","=",$key)->total();
				}else{
					$count=Files::total();
				}
				$out.='<option value="'.$key.'" '.($show_type==$key ? 'selected' : '').'>'.$value.' ('.$count.')</option>'; 
			}
			$out.='</optgroup>';
		}

		//Các nút thao tác file
		$out.='
		</select>
		<label class="check gallery-check-all" style="display:none"><input type="checkbox"/> <s></s> Chọn hết</label>
		<button class="btn-primary gallery-multiple-select">Chọn nhiều</button>
		<button class="btn-danger gallery-multiple-delete hidden">Xóa nhiều</button>
		<button class="btn-primary gallery-multiple-copy hidden">Copy link</button>
		<button class="btn-primary gallery-multiple-insert hidden">Chèn vào</button>
		</div>
		';

		//Danh sách files
		$files=Files::where(($where ?? ""))->whereNull($whereNull ?? "")->orderBy("updated_at", "DESC")->paginate( Storage::gallery_config("number", 50) );
		if(!empty($_POST['find'])){
			$out.='<div class="alert-success"><i class="fa fa-search" aria-hidden="true"></i> '.$_POST['find'].' : '.Files::where($where)->total().' </div>';
		}
		$out.='<section class="flex">';
			
			foreach($files as $file){
				if($file->type=='image'){
					if( POST('showImage')=="false" ){
						$ficon='<i class="fa fa-image"></i><br/><label>'.$file->name.'</label>';
					}else{
						$ficon='<i class="fa fa-image" style="display:none"></i><img src="'.$file->folder.''.(empty($file->size) ? '' : '/images/small_').''.$file->name.'"/>';
					}
				}

				if($file->type=='audio'){ $ficon='<i class="fa fa-volume-up"></i><br/><label>'.$file->name.'</label>'; }
				if($file->type=='video'){ $ficon='<i class="fa fa-video-camera"></i><br/><label>'.$file->name.'</label>'; }
				if($file->type=='document') { $ficon='<i class="fa fa-file-text-o"></i><br/><label>'.$file->name.'</label>'; }
				if($file->type=='file') { $ficon='<i class="fa fa-file"></i><br/><label>'.$file->name.'</label>'; }
				$out.='<div title="'.strip_tags($file->description).'" data-ext="'.strtolower(pathinfo($file->name,PATHINFO_EXTENSION)).'" data-id="'.$file->id.'" data-type="'.$file->type.'" class="gallery-item">'.$ficon.'<span class="hidden"><i class="gallery-filePath">'.$file->folder.''.$file->name.'</i> <i class="gallery-filecaption">'.$file->description.'</i></span></div>';
			}

		$out.='</section>';
		//Phần tùy chọn bên dưới
		$out.='<div class="clear"></div><div class="center" style="width:100%"><select data-option="number" class="gallery-option">';
		foreach(array(20,50,100,200,500,1000,5000) as $i){
			$out.='<option value="'.$i.'" '.(Storage::gallery_config("number", 50)==$i ? 'selected' : '').'>'.$i.' file</option>';
		}
		$out.='
		</select>
		<select data-option="showImage" class="gallery-option">
			<option value="">Hiện ảnh</option>
			<option value="false" '.(POST('showImage')=="false" ? 'selected' : '').'>Không hiện ảnh</option>
		</select>';
		$out.='</div>';
		$out.='
		<div class="center">
			'.$files->links([
				"ajaxLoad"=>"",
				"next" => '<i class="fa fa-arrow-right"></i>',
				"prev" => '<i class="fa fa-arrow-left"></i>'
			]).'
		</div>
		';
		return $out;
	}

	

	
	
	// Chi tiết file
    public static function detail($id){
   		// <Left content>
    	$out='<div class="width-60 flex-margin center">';
    	$file=self::info($id);
    	if($file->type=='image'){
    		$out.='<img class="gallery-image-src" style="max-height:350px" src="'.$file->folder.''.$file->name.'"/>';
    		$imgSize='<b>('.self::get_image_size($file->id, 'width').'x'.self::get_image_size($file->id, 'height').')</b>';
    	}
    	if($file->type=='audio'){
    		$out.='<audio controls><source src="'.$file->folder.''.$file->name.'"/></audio>';
    	}
    	if($file->type=='video'){
    		$out.='<video style="max-width:100%;max-height:100%" controls><source src="'.$file->folder.''.$file->name.'"/></video>';
    	}

    	$out.='
    	<div class="section">
    		<div class="heading">'.($imgSize??'').' '.$file->name.'</div>
    		<div class="section-body"><input placeholder="Link" style="width:100%" type="text" value="'.HOME.''.$file->folder.''.$file->name.'"/></div>
    	</div>';
    	if(!empty($file->size)){
    		$out.='<div class="section" style="margin: 0">
    					<div class="heading">Kích cỡ khác</div>
    					<div class="section-body">
    		';
    		$img_other_size=explode("/",$file->size);
    		foreach($img_other_size as $other_size){
    			$out.='<div class="form"><input style="width:100%" type="text" value="'.HOME.''.$file->folder.'images/'.$other_size.'"/></div>';
    		}
    		$out.='</div></div>';
    	}

    	$out.='</div>';
    	// </Left content>


    	// <Right content>
    	$out.='<div class="width-40 flex-margin">';
    	$out.='
    	<div class="menu bd-bottom form">
    		<div><input class="gallery-file-info-name" placeholder="Tên file" style="width:100%" type="text" value="'.$file->name.'"/></div>
    		<div><textarea class="gallery-file-info-description" placeholder="Mô tả" style="width:100%">'.$file->description.'</textarea></div>
    		<div class="center pd-5">
    			'.($file->users_id==user("id") || permission("post_manager") ? '
    				'.($file->type=='image' ? '
    					<button data-id="'.$file->id.'" class="gallery-rotate-image btn-primary">Xoay ảnh</button>
    				' : '').'
    				<button data-id="'.$file->id.'" class="gallery-update-file btn-primary">Thay đổi</button>
    				' : '').'
    			<button data-id="'.$file->id.'" class="gallery-insert-file btn-primary hidden">Chèn file</button>
    		</div>
    	</div>
    	<div class="pd-5"></div>
    	<div class="menu bd-bottom"><i class="fa fa-user" aria-hidden="true"></i> <a target="_blank" href="/admin/UsersList?id='.$file->users_id.'">'.user("name", $file->users_id).'</a></div>
    	<div class="menu bd-bottom"><i class="fa fa-external-link" aria-hidden="true"></i> <a target="_blank" href="'.$file->folder.''.$file->name.'">'.$file->folder.''.$file->name.'</a></div>
    	<div class="menu bd-bottom"><i class="fa fa-clock-o" aria-hidden="true"></i> '.date("H:i - d/m/Y",timestamp($file->created_at) ).'</div>
    	<div class="menu bd-bottom"><i class="fa fa-floppy-o" aria-hidden="true"></i> '.self::size($file->id).'</div>
    	'.($file->users_id==user("id") || permission("post_manager") ? '<div class="gallery-delete-file btn-danger width-100" data-id="'.$file->id.'" style="border-radius: 0"><i class="fa fa-trash-o" aria-hidden="true"></i> Xóa tệp tin</div>' : '').'
    	';
    	$out.='</div>';
    	// <Right content>

    	return '<div id="galleryDetail"><div class="flex flex-large">'.$out.'</div></div>';
	}
	
	
	
	// Thay đổi thông tin file
	public static function update($id, $newName='', $desc=''){
		if($file  = self::info($id)){
			if( $file->users_id==user("id") || permission("post_manager")){
				$new_file = pathinfo($newName);
				$new_name = vnStrFilter($new_file['filename'],'-',false);
				$new_ext  = vnStrFilter($new_file['extension']);
				$path_old = PUBLIC_ROOT."".$file->folder."".$file->name;
				$path_new = PUBLIC_ROOT."".$file->folder."".$new_name.".".$new_ext;

				if( !file_exists($path_new) && !in_array($new_ext, self::$config["fileExclude"]) ){
					if(file_exists($path_old)){
						rename($path_old, $path_new);
						self::delete_other_size($file->id);

						//Resize image
						$img_size="";
						if(self::type($newName)=="image" && $new_ext!="gif"){
							foreach(self::$config["imageSize"] as $key=>$size){
								if(Image::resize($path_new, "".PUBLIC_ROOT."".$file->folder."images/".$key."_$new_name.$new_ext", $size["width"], $size["height"])){
									$img_size.="/".$key."_$new_name.$new_ext";
								}
							}
						}
					}
					Files::find($id)->update(["name"=>"$new_name.$new_ext", "size"=>ltrim($img_size,"/")]);
				}
				Files::find($id)->update(["description"=>strip_tags($desc)]);
			}
		}
	}


	//Xoay ảnh
	public static function rotate($id){
		if($file  = self::info($id)){
			if( $file->users_id==user("id") || permission("post_manager")){
				$filePath = PUBLIC_ROOT."".$file->folder."".$file->name;
				if(file_exists($filePath)){
					self::delete_other_size($file->id);
					Image::rotate($filePath);
					//Resize image
					if(self::type($file->name)=="image" && pathinfo($file->name, PATHINFO_EXTENSION)!="gif"){
						foreach(self::$config["imageSize"] as $key=>$size){
							Image::resize($filePath, PUBLIC_ROOT."".$file->folder."images/".$key."_{$file->name}", $size["width"], $size["height"]);
						}
					}
				}
			}
		}
	}
	
	
	// Lấy thông tin 1 file
	public static function info($id,$out=""){
		$file=Files::find($id);
		if(empty($out)){
			$out=$file;
		}else{
			$out = $file->$out;
		}
		return $out;
	}
	
	
	// Xóa 1 file
	public static function delete($id){
		$file=Files::find($id);
		if(!empty($file->name)){
			if( $file->users_id==user("id") || permission("post_manager")){
				$path=PUBLIC_ROOT."".$file->folder."".$file->name;
				if(file_exists($path)){ unlink($path); }
				self::delete_other_size($id);
				$file->delete();
			}
		}
	}
	
	
	//Xóa các file theo id cha mẹ
	public static function deleteByPostsId($id){
			$files=Files::where("posts_id", $id)->get();
			foreach($files as $file){
				self::delete($file->id);
			}
	}

	//Xóa các file theo id sản phẩm
	public static function deleteByProductsId($id){
			$files=Files::where("products_id", $id)->get();
			foreach($files as $file){
				self::delete($file->id);
			}
	}
	
	// Xóa các file theo id tài khoản
	public static function deleteByUserId($users_id){
			$files=Files::where("users_id", $users_id)->get();
			foreach($files as $file){
				self::delete($file->id);
			}
	}
	
	//Xóa các file theo tên file
	public static function deleteByFilePath($path){
		if( empty($path) ){
			return;
		}
		$fileFolder = dirname($path).'/';
		$fileName = basename($path);
		$fileId = Files::where("name", $fileName)
			->where("folder", $fileFolder)
			->first()->id ?? null;
		if( !empty($fileId) ){
			self::delete($fileId);
		}
	}

	// Xóa các ảnh thu nhỏ
	public static function delete_other_size($id){
		if($file=self::info($id)){
			if(!empty($file->size)){
				foreach(explode("/", $file->size) as $img){
					$other_size="".PUBLIC_ROOT."".$file->folder."images/{$img}";
					if(file_exists($other_size)){ unlink($other_size); }
				}
			}
		}
	}
	
	
	
	// Kiểu file
	public static function type($file,$type="file"){
		$ext=strtolower(pathinfo($file,PATHINFO_EXTENSION));
		
		if(in_array($ext,array("jpeg","jpg","png","gif"))){ $type="image"; }
		if(in_array($ext,array("mp3","flac","aac","wma"))){ $type="audio"; }
		if(in_array($ext,array("mp4","avi","3gp","mov"))){ $type="video"; }
		if(in_array($ext,array("txt","doc","pdf","html"))){ $type="document"; }
		
		return $type;
	}
	
	
	
	
	// Cỡ ảnh
	public static function get_image_size($id,$type){
		$file=self::info($id);
		$path=PUBLIC_ROOT."".$file->folder."".$file->name;
		if(file_exists($path)){
			$info = @getimagesize($path);
			if($type=="width") {$out = $info[0];}else{ $out = $info[1]; }
		}else{
			$out=0;
		}
		return $out;
	}
	
	
	
	
	// Cỡ file
	public static function size($id,$bytes=0){
		if($bytes==0){
			$file=self::info($id);
			$path=PUBLIC_ROOT."".$file->folder."".$file->name;
			if(file_exists($path)){
				$bytes=@filesize($path);
			}
		}
        if($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }elseif($bytes >= 1048576){
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }elseif($bytes >= 1024){
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }elseif($bytes > 1){
            $bytes = $bytes . ' bytes';
        }elseif($bytes == 1){
            $bytes = $bytes . ' byte';
        }else{
            $bytes = '0 bytes';
        }

        return $bytes;
	}
	
	
	
	// Tải lên tối đa
	public static function max_upload_byte($value){
    	if ( is_numeric( $value ) ) {
        	return $value;
    	} else {
        	$value_length = strlen($value);
        	$qty = substr( $value, 0, $value_length - 1 );
        	$unit = strtolower( substr( $value, $value_length - 1 ) );
        	switch ( $unit ) {
            	case 'k':
                	$qty *= 1024;
                break;
            	case 'm':
                	$qty *= 1048576;
                break;
            	case 'g':
                	$qty *= 1073741824;
                break;
        	}
        	return $qty;
    	}
	}
	
	

}
