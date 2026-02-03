@php
	
	//Thống kê từng file  quảng cáo
	function adsCount($name){
		$out='
		<table class="table table-border width-100">
		<tr><th class="width-80">Tên file</th> <th class="width-20">Lượt xem</th></tr>
		';
		foreach(Storage::option($name, []) as $data){
			foreach( explode("|", $data["src"]) as $fileName){
				if( !isset($file[md5($fileName)]) ){
					$out.='<tr><td><a target="_blank" href="'.$fileName.'">'.basename($fileName).'</a></td> <td>'.($data[md5($fileName)]??0).'</td></tr>';
				}
				$file[md5($fileName)]=1;
			}
			$out.='';
		}
		$out.='</table>';
		if(isset($file)){
			return $out;
		}
	}
@endphp
{!!
ST("player_", "Giao diện", [
	["html"=>'
<script src="/assets/media-player/player.js"></script>
<script src="/assets/media-player/ads__complete.js"></script>
<section class="media-player-preview" style="position: fixed;right: 35px;width: 100%;max-width:400px;z-index: 199999999">
	<div class="media-player" style="width:100%;height:230px; margin: 0 auto;" data-config="download">
	<div class="media-body">
	<div class="media-meta"><div class="media-title">Còn lại gì sau cơn mưa - Hồ Quang Hiếu</div></div>
	<video poster="/shared/media-player/assets/Con-lai-gi-sau-con-mua.jpg" preload="metadata" controls="controls">
	<source src="/shared/media-player/assets/Con-Lai-Gi-Sau-Con-Mua-360P.mp4" type="video/mp4" data-quality="360P">
	<source src="/shared/media-player/assets/Con-Lai-Gi-Sau-Con-Mua-144P.mp4" type="video/mp4" data-quality="144P">
	</video>
	</div>
	</div>

	<div class="media-player audio" data-config="download" style="width:100%;margin-top: 50px;">
	<div class="media-meta">
	<img src="/shared/media-player/assets/BG-video.jpg"/>
	<span class="audio-info">
	<span class="audio-title">      <i class="fa fa-music"></i> <span>Người Đã Từng Thương</span> </span>
	<span class="audio-singer"><i class="fa fa-microphone"></i> <span>Thái Lan Viên</span> </span>
	<i class="fa fa-clock-o"></i> 201x
	</span>
	</div>
	<audio data-label="Người đã từng thương" controls>
	<source src="/shared/media-player/assets/Nguoi-Da-Tung-Thuong-Remix_128Kbps.mp3" type="audio/mpeg" data-quality="128Kbps"/>
	<source src="/shared/media-player/assets/Nguoi-Da-Tung-Thuong-Remix_320Kbps.mp3" type="audio/mpeg" data-quality="320Kbps"/>
	</audio>



	</div>
</section>
	'],
	["note"=>"", "name"=>"navbar_bg", "title" => "Màu nền", "default" => "#07141e", "type" => "color", "required"=>true],
	["note"=>"", "name"=>"navbar_color", "title" => "Màu chữ", "default" => "#FAFAFA", "type" => "color", "required"=>true],
	["note"=>"", "name"=>"bg_item_in", "title" => "Màu nền menu trong", "default" => "#7F849B", "type" => "color", "required"=>true],
	["note"=>"", "name"=>"color_item_in", "title" => "Màu chữ menu trong", "default" => "#EAEAEA", "type" => "color", "required"=>true],
	["note"=>"", "name"=>"navbar_transparent", "title" => "Độ trong suốt NavBar (0 -> 1.0)", "value" => "0.7", "type" => "number", "min" => "0", "max" => "1", "attr"=>'step="0.1"'],
	["note"=>"", "name"=>"max_height_bot_control", "title" => "Max height NavBar bên dưới", "value" => "50", "type" => "number", "min" => "0", "max" => "1000", "attr"=>'step="1"'],
	["note"=>"", "name"=>"icon_size", "title" => "Kích cỡ nút icon", "value" => "16", "type" => "number", "min" => "5", "max" => "1000", "attr"=>'step="1"'],
	["note"=>"", "name"=>"volume_height", "title" => "Height âm lượng", "value" => "12", "type" => "number", "min" => "5", "max" => "1000", "attr"=>'step="1"'],
	["note"=>"", "name"=>"progress_bar_bottom", "title" => "Bottom thanh trạng thái", "value" => "10", "type" => "number", "min" => "0", "max" => "1000", "attr"=>'step="1"'],
	["note"=>"", "name"=>"bottom_hover_time", "title" => "Bottom Thời gian trên thanh tua", "value" => "30", "type" => "number", "min" => "10", "max" => "1000", "attr"=>'step="1"'],


	["html" => '<div class="heading-block">Audio player</div>'],

	["note"=>"", "name"=>"audio_height", "title" => "Height trình phát nhạc (PC)", "value" => "230", "type" => "number", "min" => "0", "max" => "1000", "attr"=>'step="1"'],
	["note"=>"", "name"=>"audio_img_size", "title" => "Kích cỡ ảnh đại diện (PC)", "value" => "180", "type" => "number", "min" => "0", "max" => "500", "attr"=>'step="1"'],
	["note"=>"", "name"=>"audio_height_mb", "title" => "Height trình phát nhạc (Mobile)", "value" => "150", "type" => "number", "min" => "0", "max" => "1000", "attr"=>'step="1"'],
	["note"=>"", "name"=>"audio_img_size_mb", "title" => "Kích cỡ ảnh đại diện (Mobile)", "value" => "100", "type" => "number", "min" => "0", "max" => "500", "attr"=>'step="1"'],
	["note"=>"", "name"=>"audio_img_padding", "title" => "Padding ảnh đại diện", "value" => "15", "type" => "number", "min" => "0", "max" => "50", "attr"=>'step="1"'],
	["note"=>"", "name"=>"audio_font_size", "title" => "Cỡ chữ tên bài hát, ca sĩ", "value" => "16", "type" => "number", "min" => "0", "max" => "50", "attr"=>'step="1"'],


	["note"=>"", "name"=>"audio_img_radius", "title" => "Bo tròn ảnh (0-50)", "value" => "50", "type" => "number", "min" => "0", "max" => "50", "attr"=>'step="1"'],
	["note"=>"", "name"=>"audio_img_spin_speed", "title" => "Tốc độ xoay ảnh (0=Tắt)", "value" => "30", "type" => "number", "min" => "0", "max" => "1000", "attr"=>'step="1"'],

	["note"=>"", "name"=>"audio_bg_1", "title" => "Màu nền audio [1]", "default" => "#15617c", "type" => "color", "required"=>true],
	["note"=>"", "name"=>"audio_bg_2", "title" => "Màu nền audio [2]", "default" => "#7c1851", "type" => "color", "required"=>true],
	["note"=>"", "name"=>"audio_bg_3", "title" => "Màu nền audio [3]", "default" => "#1f4259", "type" => "color", "required"=>true],
	["note"=>"", "name"=>"audio_bg_4", "title" => "Màu nền audio [4]", "default" => "#147a62", "type" => "color", "required"=>true]

], 800)
!!}


{!!
OP("media_player_ads_box_", "Quảng cáo trong trình phát", [
	["html"=>'
	<section class="section">
		<div class="heading">Video quảng cáo</div>
	'],
	
	["html"=>
		Form::itemManager([
			"data"=>Storage::option("ads_video_video"),
			"name"=>"storage[option][ads_video_video]",
			"sortable"=>true,
			"max"=>20,
			"time"=>true,
			"form"=>[
				["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"Có thể bỏ trống", "value"=>"[Quảng cáo] ", "attr"=>''],
				["type"=>"file", "name"=>"src", "title"=>"Chọn video", "note"=>"Có thể chọn nhiều tệp, video sẽ phát ngẫu nhiên", "value"=>"", "ext"=>"mp4", "max"=>50, "post"=>0],
				["type"=>"number", "name"=>"sec", "title"=>"Hiện sau (s) giây", "note"=>"", "min"=>0, "max"=>9999, "value"=>"5","attr"=>''],
				["type"=>"number", "name"=>"skip_time", "title"=>"Cho phép đóng sau (s) giây", "note"=>"", "min"=>0, "max"=>9999, "value"=>"5","attr"=>''],
				["type"=>"text", "name"=>"link_url", "title"=>"Liên kết", "note"=>"http://", "value"=>"", "attr"=>''],
				["type"=>"text", "name"=>"link_title", "title"=>"Tiêu đề link", "note"=>"", "value"=>"", "attr"=>''],
				["type"=>"switch", "name"=>"audio", "title"=>"Quảng cáo trong audio", "value"=>0],
			]
		])
	],
	["html"=>adsCount("ads_video_video")],
	["html"=>'
	</section>
	<section class="section">
		<div class="heading">Ảnh popup quảng cáo</div>
	'],
	
	["html"=>
		Form::itemManager([
			"data"=>Storage::option("ads_video_popup"),
			"name"=>"storage[option][ads_video_popup]",
			"sortable"=>true,
			"max"=>5,
			"time"=>true,
			"form"=>[
				["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"Có thể bỏ trống", "value"=>"[Quảng cáo] ", "attr"=>''],
				["type"=>"file", "name"=>"src", "title"=>"Chọn ảnh (~750x100 px)", "note"=>"Có thể chọn nhiều tệp, ảnh sẽ hiện ngẫu nhiên", "value"=>"", "ext"=>"jpg,jpeg,png,gif", "max"=>50, "post"=>0],
				["type"=>"number", "name"=>"sec", "title"=>"Hiện sau (s) giây", "note"=>"", "min"=>0, "max"=>9999, "value"=>"5","attr"=>''],
				["type"=>"number", "name"=>"hidden_after", "title"=>"Tự ẩn sau (s) giây", "note"=>"", "min"=>0, "max"=>9999, "value"=>"5","attr"=>''],
				["type"=>"text", "name"=>"link", "title"=>"Liên kết", "note"=>"http://", "value"=>"", "attr"=>''],
				["type"=>"switch", "name"=>"audio", "title"=>"Quảng cáo trong audio", "value"=>0],
			]
		])
	],
	["html"=>adsCount("ads_video_popup")],
	["html"=>'</section>'],
], 800)
!!}

{!!
ST("media_player_broken_", "Link media bị lỗi", [
	["html"=>'
	'.call_user_func(function(){
		$out='
		<script>
			function mediaPlayerBrokenLink(){
				$("#mediaPlayerBrokenLink").html(\'<input type="hidden" name="deleteStorage[]" value="media_player_broken_link" />\');
			}
		</script>
		<div class="alert-warning">
			<button type="button" class="btn-danger" onclick="mediaPlayerBrokenLink()">Xóa danh sách lỗi</button>
			<p>Danh sách link có thể bị lỗi không thể phát</p>
		</div>
		<ol id="mediaPlayerBrokenLink">
		';
		foreach(Storage::media_player_broken_link() as $key=>$link){
			$out.='<li class="menu"><input type="hidden" name="storage[media_player_broken_link]['.$key.']" value="'.$link.'" /><a target="_blank" href="'.$link.'">'.$link.'</a></li>';
		}
		$out.='</ol>';
		return $out;
	}).'
	'],
], 800)
!!}


<script>
$(document).ready(function(){

});
</script>
<style type="text/css">
	@media(max-width: 767px){
		.media-player-preview{
			position: static !important;
		}
	}
</style>