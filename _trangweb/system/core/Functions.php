<?php
/*
##### Các Functions hệ thống
*/

//Load file view
function view($file, $var='', $folder=''){
	if(empty($folder)){
		$path=Route::path("views/$file");
	}else{
		$path=APPS_ROOT."/pages/".$folder."/views/$file";
	}
	if(is_array($var)){
		extract($var);
	}
	if(file_exists($path.".blade.php")){

		//Xóa file views cache mỗi ngày
		global $Schedule;
		$Schedule->d( function(){
			$cacheFolder=glob(APPS_ROOT."/pages/*/views/_cache");
			foreach($cacheFolder as $folder){
				Folder::clear($folder, true);
			}
		});
		
		//Tạo hoặc nhúng file cache
		$cachePath=Route::path("views/_cache/{$file}.cache.php");
		if( file_exists($cachePath) && time() > filemtime($path.".blade.php")+3600 && empty( user("id") ) ){
			require($cachePath);
		}else{
			BladeTemplate::create($path.".blade.php", $var, $cachePath);
		}
	}else{
		if(!file_exists($path.".php")){
			//Tạo file view mẫu
			require_once("".SYSTEM_ROOT."/system/template/View.php");
			redirect(THIS_URL);
		}
		require_once($path.".php");
	}
}

//View trang không tồn tại
function pageNotfound(){
	Route::folder("not-found");
	view("NotFound", "", "not-found");
}



//Nhúng các file layout
function layout($file, $var=''){
	if(is_array($var)){ extract($var); }
	$path=SYSTEM_ROOT."/layouts/$file.php";
	if(file_exists($path)){
		require_once($path);
	}
}


//Lấy giá trị $_GET["value"]
function GET($key,$default=""){
	return $_GET[$key] ?? $default;
}


//Lấy giá trị $_POST["value"]
function POST($key,$default=""){
	return $_POST[$key] ?? $default;
}

//Lấy giá trị $_REQUEST["value"]
function REQUEST($key,$default=""){
	return $_REQUEST[$key] ?? $default;
}

//Lấy giá trị $_COOKIE["value"]
function COOKIE($key,$default=""){
	return $_COOKIE[$key] ?? $default;
}

//Lấy giá trị $_SESSION["value"]
function SESSION($key,$default=""){
	return $_SESSION[$key] ?? $default;
}

// chuyển hướng tới link
function redirect($link="", $js=false, $alert="", $ref=true){
	if($ref && !$js){
		setcookie("ref_link", THIS_URL, time()+3600, "/");
	}
	if(empty($link)){
		$link=THIS_URL;
	}
	if($js){
		echo '
		<style>
			body>*{display: none}
			#loading{display: block !important}
		</style>
		<script>
			'.(empty($alert) ? '' : 'alert("'.$alert.'");').'
			location.href="'.$link.'";
		</script>'
		;
	}else{
		Header("Location: $link");
	}
	die;
}



// Mã hóa mật khẩu
function passwordEncode($password){
	foreach(array("sha256", "sha384", "sha512", "md5") as $hash){
		for($i=0; $i <= 1000; $i++){ $password = hash($hash,$password); }
	}
return $password;
}

function passwordCreate($password){
	return password_hash(passwordEncode($password), PASSWORD_DEFAULT);
}
function passwordCheck($password, $hash){
	return password_verify(passwordEncode($password), $hash);
}





// Loại bỏ tất cả dấu, ký tự đặc biệt
function vnStrFilter($text, $space="-", $lower=true){
	$text = html_entity_decode(trim($text),ENT_QUOTES,'UTF-8');
	$replace = array(
		'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
		'd'=>'đ',
		'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
		'i'=>'í|ì|ỉ|ĩ|ị',
		'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
		'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
		'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
		'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
		'D'=>'Đ',
		'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
		'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
		'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
		'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
		'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
		' ' => '[^a-z0-9]'
	);
	foreach($replace as $to=>$from){
		$text = preg_replace("/($from)/i", $to, $text);
	}
	$text=trim($text);
	$text=str_replace(" ", $space, $text);
	while( strpos($text, "--")!==false ){
		$text = str_replace("--", "-", $text);
	}
	if($lower){ $text=strtolower($text); }
	return $text;
}

//Chuyển sang dạng số nguyên
function toNumber($str){
	return (int)vnStrFilter($str,"");
}



// Phân loại thiết bị người dùng
function device($os=false ,$full=false){
	$output="";
	$ua=$_SERVER['HTTP_USER_AGENT']??"";
	if($full){
		preg_match('/\((.+?)\)/i', $ua, $getUA);
		$output="".($getUA[1] ?? "Unknown")." - IP: ".($_SERVER['REMOTE_ADDR']??"0")."";
	}else{
		if($os){
			if(preg_match('/Windows NT|^$/i', $ua)){
				$output='Windows';
			}elseif(preg_match('/Windows Mobile|Windows Phone|^$/i', $ua)){
				$output='Windowsphone';
			}elseif(preg_match('/Android|^$/i', $ua)){
				$output='Android';
			}elseif(preg_match('/MIDP|Symbian|^$/i', $ua)){
				$output='Java';
			}elseif(preg_match('/iPhone OS|ipad|^$/i', $ua)){
				$output='IOS';
			}elseif(preg_match('/Mac OS|^$/i', $ua)){
				$output='Mac';
			}else{
				$output="";
			}
		}else{
			if(isset($_COOKIE["device"])){
				$output=$_COOKIE["device"];
			}else if(preg_match('/Android|iPhone|ipad|MIDP|Phone|Mobile|Wap|^$/i', $ua)){
				$output='mobile';
			}else{
				$output='desktop';
			}
		}
	}
	return $output;
}









// Html char
function htmlEncode($str){
	return htmlentities($str, ENT_QUOTES, 'UTF-8');
}
function htmlDecode($str){
	return html_entity_decode($str, ENT_QUOTES, 'UTF-8');
}





// Cắt văn bản
function cutWords($str, $leng, $more="", $filter=true){
	$str=preg_replace('#<[^>]+>#', ' ', htmlDecode($str));
	if($filter){
		$replace=['"', "'", "/", "<", ">", "\\"];
		$str=preg_replace('/\s+/', ' ',$str);
		$str=str_replace($replace, "", $str);
		while( stristr($str, '  ') ){
			$str=str_replace('  ', ' ', $str);
		}
	}
	$str=trim($str);
	if(substr_count($str, " ")>$leng){
		$str=implode(" ", array_slice(explode(" ", $str), 0, $leng));
		$str=$str."".$more;
	}
	return $str; 
}



// Tạo ký tự ngẫu nhiên
function randomString($length=10, $number=true) {
	$characters = ''.($number ? '0123456789' : '').'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$str= '';
	for($i=0; $i < $length; $i++){
		$str.= $characters[rand(0, $charactersLength - 1)];
	}
	return $str;
}







// Modal box
/*
echo modal('id1', 'tiêu đề', '<div class="menu">nội dung 1</div>','350px', false, true);
echo '<a data-modal="id1" class="link modal-click">Click để hiện hộp thông báo</a>';
*/
function modal($id='', $title, $content, $width='90%', $show=false, $close=true, $scroll=true){
	$output="";
	Assets::footer("/assets/general/js/modal.js");
	$output.= '
	<div class="modal'.(empty($id) ? '' : ' modal-'.$id.'').''.($show ? '' : ' hidden').' '.($close ? 'modal-allow-close' : '').''.($scroll ? ' modal-allow-scroll' : '').'">
		<div class="modal-body" style="max-width:'.$width.'">
			<div class="modal-content">
				'.(empty($title) ? '' : '<div class="'.($title["class"]??"heading modal-heading").'">
					<span>'.($title["title"]??$title).'</span>
					'.($close ? '<i class="modal-close link fa"></i>' : '').'</div>').'
				<div>'.$content.'</div>
			</div>
		</div>
	</div>
	';
	return $output;
}

function modalForm($id='', $title, $content, $width='90%', $show=false, $close=true, $scroll=true){
	$output="";
	Assets::footer("/assets/general/js/modal.js");
	$output.= '
	<div class="modal'.(empty($id) ? '' : ' modal-'.$id.'').''.($show ? '' : ' hidden').' '.($close ? 'modal-allow-close' : '').''.($scroll ? ' modal-allow-scroll' : '').'">
		<div class="modal-body" style="max-width:'.$width.'">
			<div class="modal-content" style="background: transparent;">
				<div class="modal-form">
					'.(empty($title) ? '' : '<div class="'.($title["class"]??"heading").'">
						<span>'.($title["title"]??$title).'</span>
						'.($close ? '<i class="modal-close link fa"></i>' : '').'</div>').'
					<div>
						'.$content.'
					</div>
				</div>
			</div>
		</div>
	</div>
	';
	return $output;
}



// CURL get content url
function file_get_contents_curl($url) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);     
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}


//Tạo & chuyển định dạng Timestamp
function timestamp($date=""){
	return (empty($date) ? date("Y-m-d H:i:s") : strtotime($date));
}

//Ngày tháng dạng chữ
function dateText($time){
	if(!is_numeric($time)){
		return;
	}
	$timeElapsed = time()-$time;
	$minutes = round($timeElapsed/60);
	$hours = round($timeElapsed/3600);
	$days = round($timeElapsed/86400);


	if($minutes <=60){
		if($minutes==0){
			$minutes=1;
		}
		$date="$minutes phút trước";
	}else if($hours<=24){
		$date="$hours giờ trước";
	}else if($days<=7){
		if($days==1){
			$date="Hôm qua";
		}else{
			$date="$days ngày trước";
		}
	}else{
		$date=date("d/m/Y", $time);
	}
	return $date;
}

//Trả về dữ liệu
function returnData($data){
	ob_end_clean();
	if(is_array($data)){
		header("Content-Type: application/json");
		echo json_encode($data);
	}else{
		echo $data;
	}
	die;
}

//Lấy User
$__usersData=[];
function user($column="", $id="", $link=true){
	global $__usersData;
	//Lưu dữ liệu để truy xuất nhanh hơn
	if(is_object($column)){
		$usersID=array_unique( array_column($column->toArray(), "users_id") );
		if( empty($usersID) ) return;
		$data=DB::table("users")->select("*")->whereIn("id", $usersID)->get(true);
		foreach($data as $u){
			$__usersData[$u->id]=$u;
		}
		return;
	}
	if(empty($id)){
		$id=THIS_USER["id"]??0;
	}
	if(empty($__usersData[$id])){
		$__usersData[$id]=DB::table("users")->select("*")->where("id", $id)->first(true);
	}
	$user=$__usersData[$id];

	if( !empty($user->id) ){
		//Màu nick thành viên
		if($column=="name_color"){
			$color = models\Role::find($user->role)->color;
			if($link){
				$user->name_color='
				<a class="tooltip" title="'.models\Users::role($user->role).'" style="color: '.$color.'" href="'.( permission("admin") ? '/admin/UsersList?id='.$user->id.'" target="_blank"' : 'javascript:void(0)"').'>
					<b>'.$user->name.'</b>
				</a>
				';
			}else{
				$user->name_color='
				<span class="tooltip" title="'.models\Users::role($user->role).'" style="color: '.$color.'">
					<b>'.$user->name.'</b>
				</span>
				';
			}
			
		}
		if(!is_array($user->storage)){
			$user->storage=unserialize($user->storage??"");
		}
		$user->avatar=file_exists(PUBLIC_ROOT."/files/users/avatars/{$user->id}.png") ? "/files/users/avatars/{$user->id}.png" : "/files/users/avatars/0.png";
	}
	return (empty($column) ? $user : $user->$column ?? $user->storage[$column] ?? NULL);
}



//Quyền truy cập từng phần
function permission($key, $userId = null, $redirect = null){
	$roleId = is_null($userId) ? user("role") : user("role", $userId);
	$permissionForUser = models\Role::getPermissionsByRole($roleId);
	foreach( explode("|", $key) as $splitKey){
		if( isset($permissionForUser[$splitKey]) ){
			return true;
		}
	}
	if( !empty($redirect) ){
		return redirect($redirect);
	}
	return false;
}

//Phân trang (PHP 8: required params trước optional)
function paginationLinks($pageCurrent, $limit, $total, $op = [], $amount = 3, $out = ""){
	if($limit<1){$limit=1;}
	if($total<=$limit){ return; }
	$totalPage   = ceil($total/$limit);
	//Thông số mặc định
	$last=true;
	$next='<i class="fa fa-arrow-right"></i>';
	$prev='<i class="fa fa-arrow-left"></i>';
	$class="center";
	if(!empty($op)){
		extract($op);
	}

	parse_str(parse_url( URI, PHP_URL_QUERY), $urlQuery);
	unset($urlQuery["page"]);
	$toUrl=(count($urlQuery)>0 ? '?' : '').''.http_build_query($urlQuery).''.(count($urlQuery)>0 ? '&' : '?').'page=';
	if(empty($url)){ $url=$toUrl; }
	$pageCurrent=($pageCurrent > 0 ? $pageCurrent : 1);
	$out.='<nav class="paginate '.$class.'">';
	if($pageCurrent > 1 && $totalPage > 1){
		// Trang trước
		$out.='<a class="paginate-prev" data-page="'.($pageCurrent-1).'" href="'.$url.''.($pageCurrent-1).'">'.$prev.'</a>';
	}
	if($pageCurrent > 1){
		// Trang 1
		$out.='<a class="paginate-first" data-page="1" href="'.$url.'1">1</a>';
	}
	// Trang hiện tại
	$out.='<span class="paginate-current">'.$pageCurrent.'</span>';
	for($i = 1; $i < $amount; $i++){
		$page = ($pageCurrent + $i);
		if( $page < $totalPage ){
			$out.='<a class="paginate-number" data-page="'.$page.'" href="'.$url.''.$page.'">'.$page.'</a>';
		}
	}
	if($pageCurrent < $totalPage && $totalPage > 1){
		$out .= ' &#183;&#183;&#183; ';
		if($last){
			// Trang cuối
			$out.='<a class="paginate-last" data-page="'.$totalPage.'" href="'.$url.''.$totalPage.'">'.$totalPage.'</a>';
		}
		// Trang tiếp
		$out.='<a class="paginate-next" data-page="'.($pageCurrent+1).'" href="'.$url.''.($pageCurrent+1).'">'.$next.'</a>';
	}
	$out.='</nav>';
	Assets::footer('/assets/general/js/paginate.js');
	return $out;
}

//Xuất nội dung trình soạn thảo HTML
function htmlEditorOutput($content){
	//Thay thế văn bản
	$replace=[
		"<!--[#]"=>"",
		"[#]-->"=>"",
		'data-noinstallscript="1"'=>''
	];
	foreach($replace as $from=>$to){
		$content=str_replace($from, $to, $content);
	}

	//Phân trang
	if(strpos($content,'<div class="_empty--new-page"></div>')!==false){
		$part=explode('<div class="_empty--new-page"></div>', $content);
		//$part=array_reverse($part);
		$page=$_GET["page"]??1;
		$newContent=$part[($page-1)] ?? printError("Trang không tồn tại", "Lỗi");
		$config=(strpos($content, '<script')===false && strpos($content, 'class="panel')===false ? ["ajaxLoadId"=>"postsBody"] : '');
		$content=$newContent.'<div class="center">'.paginationLinks($page, 1, count($part), $config, 3, "").'</div>';
	}

	//Media player
	if(strpos($content,'"media-player')!==false){
		Assets::footer("/assets/media-player/player__complete.css", "/assets/media-player/player.js", "/assets/media-player/ads__complete.js");
	}

	//Mã nguồn
	if(strpos($content, '<pre class="language-')!==false){
		Assets::footer("/assets/syntax-highlighter/prism.css", "/assets/syntax-highlighter/prism.js");
		if(strpos($content, 'data-code="preview"')!==false){
			Assets::footer("/assets/syntax-highlighter/preview.css", "/assets/syntax-highlighter/preview.js");
		}
	}

	//Slider ảnh
	if(strpos($content,'class="slider"')!==false){
		Assets::footer("/assets/slider/style__complete.css", "/assets/slider/script.js");
	}

	//Timeline
	if(strpos($content,'class="timeline"')!==false){
		Assets::footer("/assets/timeline/style__complete.css", "/assets/timeline/script.js");
	}

	return $content;
}


//Văn bản hiển thị
function __($text, $key=null){
	$key=is_null($key) ? crc32($text) : crc32($key);
	$lang=Storage::language($key, null);
	if(is_null($lang)){
		Storage::update("language", [$key=>$text]);
		$lang=$text;
	}
	if( permission("admin") ){
		PageOption::language([$key=>$lang]);
	}
	return $lang;
	
}

//Tối ưu lại css
function cssMinifier($content, $comment="/* Designed by LoKiem */\n"){
	$content = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $content);
	while( stristr($content, '  '))   $content = str_replace('  ', ' ', $content);
	$content=preg_replace('/\/\*(.+?)\*\//', "", $content);
	return $comment."".$content;
}

//Lấy ảnh thu nhỏ từ link gốc
function imageOtherSize($path, $size="small"){
	$name=basename($path);
	$sizePath=dirname($path)."/images/".$size."_".$name;
	if(file_exists(PUBLIC_ROOT."".$sizePath)){
		$path=$sizePath;
	}
	return $path;
}

//Breadcrumb
function breadcrumb(array $data){
	$out='<ol class="breadcrumb text-inline" vocab="https://schema.org/" typeof="BreadcrumbList">';
	if(count($data)<2){
		$data=array_merge(["/"=>__("Trang chủ")], $data);
	}
	$i=0;
	foreach($data as $link=>$title){
		$i++;
		$out.='
		<li property="itemListElement" typeof="ListItem">
			<a property="item" typeof="WebPage" href="'.$link.'"><span property="name">'.$title.'</span></a>
			<meta property="position" content="'.$i.'" />
		</li>
		';
	}
	$out.='</ol>';
	return $out;
}

//Kiểm tra mã xác minh
function captchaCorrect($code=""){
	if( $code == SESSION("captcha_code", "empty") ){
		$result = true;
	}
	unset($_SESSION["captcha_code"]);
	return $result ?? false;
}

//Chuyển mã màu hex sang RGB
function hex2rgb($colour){
	if ( $colour[0] == '#' ) {
		$colour = substr( $colour, 1 );
	}
	if ( strlen( $colour ) == 6 ) {
		list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
	} elseif ( strlen( $colour ) == 3 ) {
		list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
	} else {
		return false;
	}
	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );
	return ['r' => $r, 'g' => $g, 'b' => $b];
}

//Check dung lượng thư mục
function folderSize($dir){
	$size = 0;
	foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
		$size += is_file($each) ? filesize($each) : folderSize($each);
	}

	return $size;
}


//Chuyển Mb sang Byte
function mb2Bytes($size){
	return $size*1024*1024;
}

//Chuyển byte sang Kb,Mb,Gb
function bytesConvert($size, $type="auto"){
	if($size<1024){
		$out["auto"]=$size." Bytes";
		$out["Bytes"]=$out["auto"];
	}else if(($size<1048576)&&($size>1023)){
		$out["auto"]=round($size/1024, 0)." KB";
		$out["KB"]=$out["auto"];
	}elseif(($size<1073741824)&&($size>1048575)){
		$out["auto"]=round($size/1048576, 0)." MB";
		$out["MB"]=$out["auto"];
	}else{
		$out["auto"]=round($size/1073741824, 1)." GB";
		$out["GB"]=$out["auto"];
	}
	return $out[$type]??$size;
}

//Replace duy nhất 1 lần
function str_replace_first($from, $to, $content){
	return preg_replace('/'.preg_quote($from, '/').'/', $to, $content, 1);
}

/*
 * Hỗ trợ in dữ liệu fix bug
 */
function dd($data){
	echo '
		<div class="menu-bg bd">
			<pre>'.print_r($data, true).'</pre>
		</div>
	';
	die;
}

/*
 * Kiểm tra chuỗi có phải serialize không
 */
function is_serialized( $data, $strict = true ) {
	if ( ! is_string( $data ) ) {
		return false;
	}
	$data = trim( $data );
	if ( 'N;' == $data ) {
		return true;
	}
	if ( strlen( $data ) < 4 ) {
		return false;
	}
	if ( ':' !== $data[1] ) {
		return false;
	}
	if ( $strict ) {
		$lastc = substr( $data, -1 );
		if ( ';' !== $lastc && '}' !== $lastc ) {
			return false;
		}
	} else {
		$semicolon = strpos( $data, ';' );
		$brace     = strpos( $data, '}' );
		if ( false === $semicolon && false === $brace ) {
			return false;
		}
		if ( false !== $semicolon && $semicolon < 3 ) {
			return false;
		}
		if ( false !== $brace && $brace < 4 ) {
			return false;
		}
	}
	$token = $data[0];
	switch ( $token ) {
		case 's':
		if ( $strict ) {
			if ( '"' !== substr( $data, -2, 1 ) ) {
				return false;
			}
		} elseif ( false === strpos( $data, '"' ) ) {
			return false;
		}
		case 'a':
		case 'O':
		return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		case 'b':
		case 'i':
		case 'd':
		$end = $strict ? '$' : '';
		return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
	}
	return false;
}

/*
 * Thay thế toàn bộ văn bản trong array hoặc object
 */
function replace_string_in_array($data = [], $replace = []){
	if( is_array($data) || is_object($data) ){
		if( is_array($data) ){
			$out = [];
		}else{
			$out = new stdClass;
		}
		foreach($data as $key => $value){
			if( is_array($value) || is_object($value) ){
				$value = replace_string_in_array($value, $replace);
			}else{
				if( !empty($replace) ){
					foreach($replace as $from => $to){
						$value = preg_replace($from, $to, $value);
					}
				}
			}
			if( is_array($data) ){
				$out[$key] = $value;
			}else{
				$out->$key = $value;
			}
		}
	}
	return $out ?? $data;
}

/*
 * Chuyển màu từ HEX sang RGB
 */
function hexToRGB($hex){
  list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
  return "$r,$g,$b";
}

/*
 * Hiện mã HTML tùy chỉnh
 */
function htmlCustom($name){
	$data = Storage::option('html_tag_'.$name, []);
	if( empty($data) ){
		return;
	}
	$out = '';
	foreach($data as $item){
		$show = true;
		foreach(explode(PHP_EOL, $item['filter_path'] ?? '') as $ex){
			$ex = str_replace('*', '(.*?)', $ex);
			if( empty($ex) ){
				continue;
			}
			switch($item['filter_path_type'] ?? ''){
				case 'excluded': // Không hiện trên các trang
					if( preg_match('#'.trim($ex).'$#i', URI) ){
						$show = false;
					}
				break;
				case 'included': // Chỉ hiện tại các trang
					if( !preg_match('#'.trim($ex).'$#i', URI) ){
						$show = false;
					}
				break;
			}			
		}
		if( $show ){
			$out .= $item['content'];
		}
	}
	return $out;
}