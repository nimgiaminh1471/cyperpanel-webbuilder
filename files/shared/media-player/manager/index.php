<?php
define("TESTING", false);
define("LOGINCODE", "123456"); //Mã đăng nhập quản lý




switch(isset($_REQUEST['action']) ? $_REQUEST['action'] : '') {
    case "ads_count":
	
	$ads_count='ads/'.md5($_POST['title']).'.'.$_POST['type'].'.count';
	if(@file_exists($ads_count)){
	@file_put_contents($ads_count,(file_get_contents($ads_count,FILE_USE_INCLUDE_PATH)+1));
	}

        break;
		
		case "media_count":
	
	$media_count='data/total.count';
	@file_put_contents($media_count,(file_get_contents($media_count,FILE_USE_INCLUDE_PATH)+1));
	if(file_get_contents(''.$media_count.'.date',FILE_USE_INCLUDE_PATH)!=date("dmy")){
	@file_put_contents($media_count,"1");
	@file_put_contents(''.$media_count.'.date',date("dmy"));
	}

        break;
		
    case "save_link_media":
	$save_link_media='data/media_link.list';
	$check_link_exists=file_get_contents($save_link_media,FILE_USE_INCLUDE_PATH);
	foreach(explode(PHP_EOL,$_POST['link']) as $link){
	if(stripos($check_link_exists,$link)===false){
	@file_put_contents($save_link_media,"".$link."\n",FILE_APPEND);
	}
	}
	
        break;
		
		
    default:


	


//##### <Header>
echo '
<!DOCTYPE HTML>
<html lang="vi">
<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<title>Player Manager</title>
<link rel="stylesheet" href="include/style.css" media="all" />
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

</head>
<body>
';
//##### <./Header>


//<Login>
$media_login_code=isset($_POST['media_login_code']) ? md5($_POST['media_login_code']) : (isset($_COOKIE['media_login_code']) ? $_COOKIE['media_login_code'] : '');

if(isset($_POST['media_login_code'])){
if($media_login_code==md5(LOGINCODE)){ setcookie("media_login_code",md5($_POST['media_login_code']),time()+3600*24*365,"/"); }else{	echo '<script>alert("Mã không chính xác");</script>'; }
}

if($media_login_code!=md5(LOGINCODE)){
//Login Failed
echo '<div style="max-width:500px;margin: 0 auto">
<div class="big-title">Nhập Mã Quản Lý</div>
<form method="POST">
<div class="menu bd"><input placeholder="Mặc định: 123456" type="text" name="media_login_code" id="media_login_code" value="'.(isset($_POST['media_login_code']) ? $_POST['media_login_code'] : '').'"/></div>
</form>
</div>
';

die;
}


//</Login>




// <Function>
function fix_char($txt){
	return str_replace(array("'"),'"',$txt);
}

function css_minify($str){
   $str = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/', '', $str);
   $str = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $str);
   $str = preg_replace('/\s+/', ' ', $str);
   
    while ( stristr($str, '  '))   $str = str_replace('  ', ' ', $str);
 return trim($str);
}

function can_not_do(){
return '<script>alert("Chế độ xem thử không cho phép xóa hay chỉnh sửa");</script>';
}
// </Function>




//##### <Main>
echo '<div style="max-width:650px;margin: 0 auto">';





echo '<script>
$(document).ready(function(){
	
	
	
	
	
	
'.(isset($_GET['show']) ? '
$("#'.$_GET['show'].'").show();
$(document).find(".option-label[data-show=\''.$_GET['show'].'\']").parent().css({"border-bottom":"1px solid red"});
' : '$("#count").show();').'
	
	
	$(".option-label").click(function(){
	var show=$(this).attr("data-show");
	$(".option-body").hide();
	$("#"+show+"").slideToggle();
	$(".option-label").parent().css({"border-bottom":""});
	$(this).parent().css({"border-bottom":"1px solid red"});
	});
	
	
	
$("#video_form").keyup("input",function(e){
var this_empty=0;
$("#title, #video, #link, #sec, #skip_time").each(function(){
if($(this).val()=="") {this_empty=1;}
});
if(this_empty==1){$("#video_submit").hide(); }else{ $("#video_submit").show();  }
});

	
$("#popup_form").keyup("input",function(e){
var this_empty=0;
$(this).find("input").each(function(){ if($(this).val()=="") {this_empty=1;} });
if(this_empty==1){$("#popup_submit").hide(); }else{ $("#popup_submit").show();  }
});
	

	
	
//#####Check link
$("#check_link").click(function(){
$(this).parent().hide();

$(".media-list").find("div").eq(0).attr("id","pending");
$(".media-list").find("a").after(\'<i></i>\');
$(".media-list div").css({"margin-top":"10px","opacity":"0.5"});
var checking=null;
checking=setInterval(function(){
$(".media-list").find("#pending a").each(function(id){

var link = $(this).attr("href");
var this_link=$(this);



this_link.parent().attr("id","checking");

var container = $(".media-list");


container.animate({
    scrollTop: $("#checking").offset().top - container.offset().top + container.scrollTop()-150
},50);



setTimeout(function(){
$.ajax({
    type: "HEAD",
    url: link,
	timeout:2000,
	dataType: "jsonp",
	complete: function(e){
	this_link.parent().css({"opacity":""}).attr("id","checked").next("div").attr("id","pending");
},
    success: function() {
    this_link.css({"color":"blue"}).addClass("is_ok");
	    },  
    error: function(err,text) {
    this_link.css({"color":"red","text-decoration":"line-through"}).addClass("is_error");
	var err_info="Không xác định";
	if(text=="error"){var err_info="Trang bị lỗi";}
	if(text=="timeout"){var err_info="Hết thời gian kiểm tra";}
	
	this_link.parent().find("i").html("<br/>Mã lỗi: <a target=\'_blank\' href=\'https://www.google.com/search?&q=HTTP+Status\'>"+text+" ("+err.status+")</a> (<b>"+err_info+"</b>)");
    }
	


});

if(this_link.parent().next("div").text().length==0){this_link.parent().attr("id","checked"); $(".media-list").append(\'<div class="fmenu bd">Tổng lỗi: \'+$(\'.media-list\').find(\'.is_error\').length+\'</div>\'); $(".media-list").find(".is_ok").parent().hide(); alert("Đã kiểm tra xong"); clearInterval(checking);}

},1000);

});


},500);

});	





	
});

</script>';

echo '<table style="width:100%"><tr>
<td class="cmenu bd center"><a href="#" class="option-label" data-show="count">Thống kê</a></td>
<td class="cmenu bd center"><a href="#" class="option-label" data-show="video_ads">Video ADS</a></td>
<td class="cmenu bd center"><a href="#" class="option-label" data-show="popup_ads">Popup ADS</a></td>
<td class="cmenu bd center"><a href="#" class="option-label" data-show="player_style">Player Style</a></td>
</tr></table>';



// <Media list>
echo '<div class="option-body" id="count">';

echo '
<div class="menu bd">Tổng lượt phát Video/Audio ngày '.date('d/m/Y').' : <b style="color:blue">'.file_get_contents("data/total.count",FILE_USE_INCLUDE_PATH).'</b> lượt</div>

';


	
$list_media="";
$total_media=0;
foreach(explode(PHP_EOL,file_get_contents("data/media_link.list",FILE_USE_INCLUDE_PATH)) as $link){
	if(!empty($link)){
	$total_media++;
	
	$list_media.='<div class="menu bd-bot">'.$total_media.'. <a target="_blank" href="'.$link.'">'.$link.'</a></div>';

	}
}
if($total_media>0){
if(isset($_GET['reset_media_list'])){ 
if(TESTING){
	echo can_not_do();
	}else{
file_put_contents('data/media_link.list',''); echo '<script>location.href="index.php";</script>';
	}
}
echo '
<div class="big-title">Danh sách media ('.$total_media.') <a onclick="return confirm(\'Reset danh sách media? ,Tự cập nhật khi ấn phát\')" style="color:yellow;float:right" href="index.php?reset_media_list">(X)</a></div>
<div class="fmenu bd-bot center"><a id="check_link" href="#">Kiểm tra link bị lỗi</a></div>
<div class="media-list" style="max-height:300px;overflow:auto">'.$list_media.'</div>
'; 
}




echo '</div>'; // </Media list>



if(isset($_GET['delete'])){
	if(TESTING){
	echo can_not_do();
	}else{
	if(file_exists($_GET['delete'])){
	unlink($_GET['delete']);
	unlink(''.$_GET['delete'].'.count');
	echo '<script>alert("Đã xóa");</script>';
	}
	}
}




// <Video ADS>

echo '<div class="option-body" id="video_ads">
<div class="big-title">Video quảng cáo</div>
<form id="video_form" action="" method="POST">



'.call_user_func(function($out){
//<Save ads config>
if(isset($_POST["title"])){
	if(TESTING){
	echo can_not_do();
	}else{
foreach($_POST as $key => $value){
$ads_data[$key] = $value;
}
$ads_path = 'ads/'.md5($_POST['title']).'.video';
file_put_contents($ads_path,serialize($ads_data));
file_put_contents(''.$ads_path.'.count','0');

echo '<script>location.href="index.php?show=video_ads"</script>';
	}
}
//<./Save ads config>

//<Input Ads>
$ads_input=array();
$ads_input[] = array("name"=>"title", "title" => "Tên quảng cáo (*)", "default" => "[Quảng cáo] ", "type" => "text");
$ads_input[] = array("name"=>"video", "title" => "Link video (*)", "default" => "http://url.mp4", "type" => "text");
$ads_input[] = array("name"=>"sec", "title" => "Hiện sau (giây) (*)", "default" => "30", "type" => "number");
$ads_input[] = array("name"=>"skip_time", "title" => "Bỏ qua sau (giây) (*)", "default" => "10", "type" => "number");
$ads_input[] = array("name"=>"title_link", "title" => "Tiêu đề link", "default" => "", "type" => "text");
$ads_input[] = array("name"=>"url_link", "title" => "URL Link", "default" => "", "type" => "text");

$out.='<table class="bg" style="width:100%">';
foreach($ads_input as $input){
	$out.='<tr><td class="menu bd-bot">'.$input['title'].'</td> <td class="menu bd-bot"><input id="'.$input['name'].'" name="'.$input['name'].'" value="'.$input['default'].'" type="'.$input['type'].'"/></td> </tr>';
}
$out.='</table>';
//<./Input Ads>

return $out;
},"").'
<div class="menu bd-bot">Hiện trên trình phát Audio [<input type="radio" name="audio" value="1" checked/>Có] [<input type="radio" name="audio" value="0"/>Không] </div>
<div class="menu bd-bot"><input id="video_submit" type="submit" value="Thêm video quảng cáo"/></div>
</form>
';

$fads = glob("ads/*.video");
$fads = array_combine(array_map("filemtime", $fads), $fads);
ksort($fads);

// <List ADS>
$ads_video="";
foreach($fads as $ads){

$ads_info = unserialize(file_get_contents($ads,FILE_USE_INCLUDE_PATH));


echo '
<div class="list bd-top bd-bot" style="margin-top:10px"><b>'.$ads_info['title'].'</b> <span style="float:right"><a onclick="return confirm(\'Xóa vĩnh viễn Video QC: '.$ads_info['title'].'?\')" href="index.php?show=video_ads&delete='.$ads.'">(X)</a></span></div>
<table class="bg" style="width:100%;">
<tr><td class="menu bd" style="width:20%">Video</td> <td class="menu bd">'.$ads_info['video'].'</td></tr>
<tr><td class="menu bd" style="width:20%">Hiệu sau</td> <td class="menu bd"><b>'.$ads_info['sec'].'</b> giây</td></tr>
<tr><td class="menu bd" style="width:20%">Trên Audio</td> <td class="menu bd"><b>'.($ads_info['audio']==1 ? 'Có hiện' : 'Không hiện').'</b></td></tr>
<tr><td class="menu bd" style="width:20%">Lượt xem</td>  <td class="menu bd"><b><span style="color:blue">'.@file_get_contents("".$ads.".count",FILE_USE_INCLUDE_PATH).'</span></b></td></tr>
</table>
';


$ads_video.= "{
  'sec'       :  '".fix_char($ads_info['sec'])."',
  'skip_time' :  '".fix_char($ads_info['skip_time'])."',
  'title'     :  '".fix_char($ads_info['title'])."',
  'video'     :  '".fix_char($ads_info['video'])."',
  'title_link':  '".fix_char($ads_info['title_link'])."',
  'url_link'  :  '".fix_char($ads_info['url_link'])."',
  'audio'     :  '".fix_char($ads_info['audio'])."'
},";





}




//<./List ADS>

echo '</div>'; //#### <./Video ADS>






// <Popup ADS>


echo '
<div class="option-body" id="popup_ads">
<div class="big-title">Popup quảng cáo</div>
<form id="popup_form" action="" method="POST">

'.call_user_func(function($out){
//<Save ads config>
if(isset($_POST["popup_title"])){
	if(TESTING){
	echo can_not_do();
	}else{
foreach($_POST as $key => $value){
$ads_data[$key] = $value;
}
$ads_path = 'ads/'.md5($_POST['popup_title']).'.popup';
file_put_contents($ads_path,serialize($ads_data));
file_put_contents(''.$ads_path.'.count','0');
echo '<script>location.href="index.php?show=popup_ads"</script>';
	}
}
//<./Save ads config>

//<Input Ads>
$ads_input=array();
$ads_input[] = array("name"=>"popup_title", "title" => "Tên quảng cáo", "default" => "Popup IMG", "type"=>"text");
$ads_input[] = array("name"=>"popup_sec", "title" => "Hiện sau giây (*)", "default" => "15", "type"=>"number");
$ads_input[] = array("name"=>"popup_hiden_after", "title" => "Ẩn sau giây (*)", "default" => "5", "type"=>"number");
$ads_input[] = array("name"=>"popup_image", "title" => "Link ảnh (~750x100 PX) (*)", "default" => "http://", "type"=>"text");
$ads_input[] = array("name"=>"popup_link", "title" => "URL Link (*)", "default" => "http://", "type"=>"text");
$out.='<table class="bg" style="width:100%">';
foreach($ads_input as $input){
	$out.='<tr><td class="menu bd-bot">'.$input['title'].'</td> <td class="menu bd-bot"><input id="'.$input['name'].'" name="'.$input['name'].'" value="'.$input['default'].'" type="'.$input['type'].'"/></td> </tr>';
	}
$out.='</table>';
//<./Input Ads>

return $out;
},"").'
<div class="menu bd-bot">Hiện trên trình phát Audio [<input type="radio" name="popup_audio" value="1" checked/>Có] [<input type="radio" name="popup_audio" value="0"/>Không] </div>
<div class="menu bd-bot"><input id="popup_submit" type="submit" value="Thêm Poup quảng cáo"/></div>
</form>
';


// <List ADS>
$ads_popup="";
$fads = glob("ads/*.popup");
$fads = array_combine(array_map("filemtime", $fads), $fads);
ksort($fads);
foreach($fads as $ads){

$ads_info = unserialize(file_get_contents($ads,FILE_USE_INCLUDE_PATH));


echo '
<div class="list bd-top bd-bot" style="margin-top:10px"><b>'.$ads_info['popup_title'].'</b> <span style="float:right"><a onclick="return confirm(\'Xóa vĩnh viễn Popup QC: '.$ads_info['popup_title'].'?\')" href="index.php?show=popup_ads&delete='.$ads.'">(X)</a></span></div>
<table class="bg" style="width:100%;">
<tr><td class="menu bd" style="width:20%">Ảnh</td> <td class="menu bd"><img src="'.$ads_info['popup_image'].'"/></td></tr>
<tr><td class="menu bd" style="width:20%">Link</td> <td class="menu bd"><b>'.$ads_info['popup_link'].'</b></td></tr>
<tr><td class="menu bd" style="width:20%">Hiệu sau</td> <td class="menu bd"><b>'.$ads_info['popup_sec'].'</b> giây</td></tr>
<tr><td class="menu bd" style="width:20%">Ẩn sau</td> <td class="menu bd"><b>'.$ads_info['popup_hiden_after'].'</b> giây</td></tr>
<tr><td class="menu bd" style="width:20%">Trên Audio</td> <td class="menu bd"><b>'.($ads_info['popup_audio']==1 ? 'Có hiện' : 'Không hiện').'</b></td></tr>
<tr><td class="menu bd" style="width:20%">Lượt click</td>  <td class="menu bd"><b><span style="color:blue">'.@file_get_contents("".$ads.".count",FILE_USE_INCLUDE_PATH).'</span></b></td></tr>
</table>
';


$ads_popup.= "{
  'title'     :  '".fix_char($ads_info['popup_title'])."',
  'sec'       :  '".fix_char($ads_info['popup_sec'])."',
  'hidden_after' :  '".fix_char($ads_info['popup_hiden_after'])."',
  'link'      :  '".fix_char($ads_info['popup_link'])."',
  'image'     :  '".fix_char($ads_info['popup_image'])."',
  'audio'     :  '".fix_char($ads_info['popup_audio'])."',
},";





}




//<./List ADS>

echo '</div>'; //#### <./Popup ADS>






// <Update>
$player_js=''.file_get_contents('data/player_origin.js',FILE_USE_INCLUDE_PATH).'
player_path="'.dirname($_SERVER['SCRIPT_NAME']).'/";
ads_config = {"video": ['.$ads_video.'],"popup": ['.$ads_popup.']};
';


file_put_contents(''.dirname(dirname(__FILE__)).'/player.js', $player_js);
// </Update>








// <Style>

echo '<div class="option-body" style="width:1100px" id="player_style"><div style="float:left;width:60%;max-width:650px">';




	echo '
<script src="include/color-picker.js"></script>
 <script>$(document).ready(function(){
	 
	 $(document).on("click keyup",function(){
    if($("#player_style").is(":visible")){

                $.ajax({
                url: "index.php",
                type: "POST",
    data: $("#style").serialize(),
    success: function (data) {  


	$("#testing_style").html($(data).find("#testing_style").html());}


});
	}

	
	
	
$(".audio_height span").text(\'Chuẩn: \'+((parseInt($("#audio_img_size").val())+parseInt($(".audio .media-controls").height()))+1)+\' \');
$(".audio_height_mb span").text(\'Chuẩn: \'+((parseInt($("#audio_img_size_mb").val())+parseInt($(".audio .media-controls").height()))+1)+\' \');	
	
});
	 
$(".color_default").click(function(e) {
		   var gid = $(this).attr("data-id");
		   var dcolor = $("#"+gid+"").attr("data-default");
           $("#"+ gid +"").val(dcolor);
		   $("#"+ gid +"").css({"background-color":"#"+dcolor+""});
        }); 
		

		



	
    });</script>';
	



	

	
echo '<div class="big-title">Thiết lập style</div>


<form action="" method="POST" id="style">
<table style="width:100%" class="bg">';

function hex_to_rgba($hex){
list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
return "$r,$g,$b";
}








$style_info=array(
"navbar_top"                     => array("title" => "Màu nền NavBar [TOP]", "default" => "07141e", "type" => "color"),
"navbar_bottom"                  => array("title" => "Màu nền NavBar [BOT]", "default" => "07141e", "type" => "color"),
"navbar_color"                   => array("title" => "Màu chữ tiêu đề video", "default" => "FAFAFA", "type" => "color"),
"navbar_transparent"             => array("title" => "Độ trong suốt NavBar (0 -> 1.0)", "default" => "0.7", "type" => "number", "min" => "0", "max" => "1", "step" => "0.1"),
"bg_bot_control"                 => array("title" => "Nền NavBar bên dưới", "type" => "select", "option" => array("1"=>"Bật", "0"=>"Tắt")),
"max_height_bot_control"         => array("title" => "Max height NavBar bên dưới", "default" => "0", "type" => "number", "min" => "0", "max" => "1000", "step" => "1"),
"icon"                           => array("title" => "Màu các nút icon", "default" => "FAFAFA", "type" => "color"),
"icon_hover"                     => array("title" => "Màu các nút icon [Hover]", "default" => "FF00FF", "type" => "color"),
"icon_size"                      => array("title" => "Kích cỡ nút icon", "default" => "18", "type" => "number", "min" => "5", "max" => "1000", "step" => "1"),
"volume_height"                  => array("title" => "Height âm lượng", "default" => "14", "type" => "number", "min" => "5", "max" => "1000", "step" => "1"),
"progress_bar"                   => array("title" => "Màu thanh trạng thái,âm lượng [Ngoài]", "default" => "808080", "type" => "color"),
"progress_bar_active"            => array("title" => "Màu thanh trạng thái,âm lượng [Trong]", "default" => "17BAB3", "type" => "color"),
"progress_bar_bottom"            => array("title" => "Bottom thanh trạng thái", "default" => "0", "type" => "number", "min" => "0", "max" => "1000", "step" => "1"),
"bottom_hover_time"              => array("title" => "Bottom Thời gian trên thanh tua", "default" => "30", "type" => "number", "min" => "10", "max" => "1000", "step" => "1"),



"bg_item_in"                     => array("title" => "Màu nền menu [Trong]", "default" => "EAEAEA", "type" => "color"),
"bg_item_out"                    => array("title" => "Màu nền menu [Ngoài]", "default" => "66A8CC", "type" => "color"),
"bg_item_hover"                  => array("title" => "Màu nền menu [Hover]", "default" => "48d1cc", "type" => "color"),
"bg_item_selected"               => array("title" => "Màu nền menu [Đã chọn]", "default" => "48d1cc", "type" => "color"),

"wall"                            => array("content" => '<tr><td class="dmenu bd">Audio player</td><td class="dmenu bd"></td></tr>', "type" => "text"),

"audio_height"                    => array("title" => "Height trình phát nhạc (PC)", "default" => "230", "type" => "number", "min" => "0", "max" => "1000", "step" => "1"),
"audio_img_size"                  => array("title" => "Kích cỡ ảnh đại diện (PC)", "default" => "180", "type" => "number", "min" => "0", "max" => "500", "step" => "1"),
"audio_height_mb"                 => array("title" => "Height trình phát nhạc (Mobile)", "default" => "150", "type" => "number", "min" => "0", "max" => "1000", "step" => "1"),
"audio_img_size_mb"               => array("title" => "Kích cỡ ảnh đại diện (Mobile)", "default" => "100", "type" => "number", "min" => "0", "max" => "500", "step" => "1"),
"audio_img_padding"               => array("title" => "Padding ảnh đại diện", "default" => "15", "type" => "number", "min" => "0", "max" => "50", "step" => "1"),
"audio_font_size"                 => array("title" => "Cỡ chữ tên bài hát, ca sĩ", "default" => "16", "type" => "number", "min" => "0", "max" => "50", "step" => "1"),


"audio_img_radius"                => array("title" => "Vo tròn ảnh (0-50)", "default" => "50", "type" => "number", "min" => "0", "max" => "50", "step" => "1"),
"audio_img_spin_speed"            => array("title" => "Tốc độ xoay ảnh (0=Tắt)", "default" => "30", "type" => "number", "min" => "0", "max" => "1000", "step" => "1"),

"audio_bg_1"                      => array("title" => "Màu nền audio [1]", "default" => "15617c", "type" => "color"),
"audio_bg_2"                      => array("title" => "Màu nền audio [2]", "default" => "7c1851", "type" => "color"),
"audio_bg_3"                      => array("title" => "Màu nền audio [3]", "default" => "1f4259", "type" => "color"),
"audio_bg_4"                      => array("title" => "Màu nền audio [4]", "default" => "147a62", "type" => "color"),
);
if(isset($_POST['style'])){
$style=$_POST['style'];
}else{
$style=unserialize(file_get_contents('data/player_style.config',FILE_USE_INCLUDE_PATH));
}

foreach($style_info as $key=>$value){

if($value['type']=='color'){
	echo '<tr><td class="menu bd-bot">'.$value['title'].'</td> <td class="menu bd-bot"> <input class="jscolor" data-default="'.$value['default'].'" size="5" name="style['.$key.']" id="'.$key.'" value="'.$style[''.$key.''].'" autocomplete="off"/><button type="button" class="color_default btn-button" data-id="'.$key.'">Mặc định</button></td></tr>';
}elseif ($value['type']=='select'){
	echo '<tr><td class="menu bd-bot">'.$value['title'].'</td> <td class="menu bd-bot"> <select name="style['.$key.']">';
foreach($style_info[''.$key.'']['option'] as $k=>$v){echo '<option value="'.$k.'" '.($style[''.$key.'']==$k ? 'selected' : '').'>'.$v.'</option>';}
	echo '</select></td></tr>';
}elseif($value['type']=='text'){
echo $value['content'];
}else{
	echo '<tr><td class="menu bd-bot">'.$value['title'].'</td> <td class="menu bd-bot '.$key.'"> <input size="5" type="'.$value['type'].'" '.($value['type']=='number' ? ' min="'.$value['min'].'" max="'.$value['max'].'" step="'.$value['step'].'" ' : '').' name="style['.$key.']" id="'.$key.'" value="'.$style[''.$key.''].'"/> <span></span>';
}


}

echo '

</table>
<input name="style_submit" type="hidden" value="Lưu lại"/>
</form>
';



eval(file_get_contents('data/player_origin.css',FILE_USE_INCLUDE_PATH));

echo '<style id="testing_style">'.$style_content.'</style>';
if(!TESTING){
	if(isset($_POST['style_submit'])){ file_put_contents('data/player_style.config',serialize($_POST['style'])); }
	file_put_contents(''.dirname(dirname(__FILE__)).'/player.css',css_minify($style_content));
	
}


echo '
<div class="media-player audio" data-config="download" style="width:100%">

<div class="media-meta">
<img src="../assets/BG-video.jpg"/>
<span class="audio-info">
<span class="audio-title">      <i class="fa fa-music"></i> <span>Người Đã Từng Thương</span> </span>
<span class="audio-singer"><i class="fa fa-microphone"></i> <span>Thái Lan Viên</span> </span>
<i class="fa fa-clock-o"></i> 201x
</span>
</div>

<audio data-label"Người đã từng thương" controls>
<source src="../assets/Nguoi-Da-Tung-Thuong-Remix_128Kbps.mp3" type="audio/mpeg" data-quality="128Kbps"/>
<source src="../assets/Nguoi-Da-Tung-Thuong-Remix_320Kbps.mp3" type="audio/mpeg" data-quality="320Kbps"/>
</audio>



</div>';



echo '




</div>'; // </Style>

echo '

<script src="../player.js"></script>

<div style="padding:5px;float:left;width:40%;max-width:370px">
<div style="position:fixed">


<div class="media-player" style="width:350px;height:250px; margin: 0 auto;" data-config="download">
<div class="media-body">
<div class="media-meta"><div class="media-title">Tiêu đề video</div></div>
<video poster="../assets/Con-lai-gi-sau-con-mua.jpg" preload="metadata" controls="controls">
<source src="../assets/Con-Lai-Gi-Sau-Con-Mua-360P.mp4" type="video/mp4" data-quality="360P">
<source src="../assets/Con-Lai-Gi-Sau-Con-Mua-144P.mp4" type="video/mp4" data-quality="144P">
</video>
</div>
</div>


</div>
</div>






</div>
';




echo '</div>'; //##### <./Main>
	
	
	

	
	
	
//##### Footer
echo '</body></html>';
	
	
	
	
	
}






	





?>
