<?php


if(POST("formPart")=="MediaPlayer"){
  foreach($_POST["storage"]["setting"] as $key => $value){
    $css[str_replace("player_", "", $key)]=$value;
  }
  extract($css);
$primaryBG=Storage::setting("theme__primary_background");
$primaryHover=Storage::setting("theme__primary_hover");
$primaryColor=Storage::setting("theme__primary_color");

//Style
$style='

.media-player{
  overflow: hidden;
}
.media-player.audio,.media-body {
  position: relative;
  background-color:#000;
  user-select: none;
  height:100%; 
}

.media-body:-webkit-full-screen{width: 100% !important;height: 100% !important; }
.media-body:-moz-full-screen{width: 100% !important;height: 100% !important;}
.media-body:-o-full-screen{width: 100% !important;height: 100% !important;}
.media-body:-ms-fullscreen{width: 100% !important;height: 100% !important;}
.media-body:fullscreen{height: 100% !important;width:100% !important}


.media-player .fa{
color:'.$navbar_color.';
cursor:pointer;
margin:0px 3px 0px 3px;
min-width:15px;
}

@keyframes rotate { 100% {transform:rotateY(360deg); } }
.media-player ul{display:none}
.media-time{color: '.$navbar_color.';}


.media-time-hover{
display:none;
position: absolute;
bottom:'.$bottom_hover_time.'px;
background-color: '.$navbar_bg.';
padding:3px 5px 3px 5px;
border-radius:15px;
font-size:14px;
width:80px;
margin-left:-40px;
text-align: center;

}

.media-time-hover::after{
    content: " ";
    position: absolute;
    top: 97%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: '.$navbar_bg.' transparent transparent transparent;
}
.hover-left{left:2px;margin-left:0px}
.hover-right{right:2px;margin-left:0px}
.hover-left::after,.hover-right::after{display:none;}

.media-item-click,.media-player .fa:hover{color:'.$primaryHover.' !important}

.media-item {
    position: relative;
    display: inline-block;
}

.media-item-body {
  display:none;
    position: absolute;
    z-index: 9;
    right:-70px;
  bottom:25px;
  border-radius: 2px;
    width:200px;
  overflow: hidden;
}
.media-item-body>div{
  overflow: auto;
  max-height:'.$audio_img_size.'px;
  width: 220px;
  padding-right:10px;
}


.media-item-body span{
cursor:pointer;
display:block;
background-color: rgba('.hexToRGB(''.$navbar_bg.'').','.$navbar_transparent.');
padding:6px;
font-size:14px;
overflow: hidden;
white-space: nowrap;

}


.media-item-body span:hover{
  background-color:'.$primaryBG.' !important;
}
.item-active{background-color:'.$primaryHover.' !important;
}


.media-item-body a {
display: block
}

.media-item-body .fa{
  color: '.$navbar_color.';
}

.media-setting-body{
  display:none;
  z-index:9
}
.media-setting-body span{
  color: '.$color_item_in.';
  background-color: rgba('.hexToRGB(''.$bg_item_in.'').','.$navbar_transparent.');
}

.media-controls,.media-ads {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  color: '.$navbar_color.';
  font-size: '.$icon_size.'px;
  width: 100%;
  padding: 10px 0px 10px 0px;
  background-color: rgba('.hexToRGB(''.$navbar_bg.'').','.$navbar_transparent.');
  z-index: 9;
  max-height:'.$max_height_bot_control.'px

}
.controls-pd{padding:0px 10px 0px 10px}
.media-ads-link{font-size:15px;background-color: #E7E7E7;padding:7px;border-radius:30px;display:inline-block;margin:0px 5px 10px 5px}
.media-ads-skip,.media-ads-time{font-size:15px;float:right;background-color: #E7E7E7;padding:7px;border-radius:30px;display:inline-block; cursor:pointer; background-color: #07141e;  background-color: rgba(7,20,30,.7);margin:0px 5px 10px 5px}


.media-ads-popup{
    position: absolute;
    margin: 0 auto;
    left: 0;
    right: 0;
  bottom:60px;
  max-width:750px;
    width: 90%;
  margin-bottom:5px;
  text-align: center;
  z-index:9;
  font-size:18px
}

.media-ads-popup img {  max-height:100px; display:inline-block;}
.media-ads-close{position: absolute;cursor:pointer; right:5px;top:5px}
.media-ads-close:hover{font-size:19px}
.audio .media-ads-popup{left: auto !important;width:80%;}
.audio-title,.audio-singer{display:block}

.media-title,.media-title-ads{
  position: absolute;
  top: 0;
  color: '.$navbar_color.';
  width: 100%;
  padding: 10px;
  background-color: rgba('.hexToRGB(''.$navbar_bg.'').','.$navbar_transparent.');
  z-index: 9;
  text-align:center
}
.media-play-warning{
    position: absolute;
    margin: 0 auto;
    left: 0;
    right: 0;
  top:50%;
  max-width:750px;
    width: 80%;
  text-align: center;
  z-index:9;
  background-color: '.$navbar_bg.';
  padding:10px;
  color:yellow;
  opacity: 0.7;
  display:none
}



.media-title span{opacity:0.8;padding:2px;color:'.$primaryBG.';float:right}

.media-center {
    position:absolute;
    top:50%;
    left:50%;
  text-align: center;
  font-size:60px;
  transform: translate(-50%, -50%);
}



.media-play-loading{
animation:ispin 5s linear infinite;

}









video::cue {
  background: transparent;
  position:0%
}

video::cue(v[voice=\'Red\']) { color:#FF0000;}
video::cue(v[voice=\'Blue\']) { color:#0000ff;}
video::cue(v[voice=\'Green\']) { color:#00ff00;}
video::cue(v[voice=\'Yellow\']) { color:#ffff00;}
video::cue(v[voice=\'Black\']) { color:#000000;}
video::cue(v[voice=\'White\']) { color:#FFFFFF;}



.media-progress {/*Parents progress*/
    position: relative;
    width: 100%;
    display: block;
  opacity:0.9;
  cursor:pointer
  
}

.media-buffered {/*Has loaded*/
    position: absolute;
  left:0px;
    width: 0px;
    display: inline-block;
  border-bottom:1px solid '.$primaryBG.';
  bottom:'.$progress_bar_bottom.'px;
  z-index:9;

}

.media-progress-bar {/*BG seek bar*/
    position: absolute;
    width: 100%;
    cursor:pointer;
    height: 6px;
    background: #808080;
  border:none;
  transition: all 0.3s ease;
  bottom: '.$progress_bar_bottom.'px;
  z-index:9
}



.media-progress-bar::-webkit-progress-bar {/*Chrome-Safari BG seek bar*/
    background: #808080;
    border:none
}
.media-progress-bar::-webkit-progress-value { /*Chrome-Safari value*/
    background: '.$primaryBG.';
    border:none
}
.media-progress-bar::-moz-progress-bar { /*Firefox value*/
    background: '.$primaryBG.';
    border:none
}

.media-progress-bar::-ms-fill { /*IE-MS value*/
    background: '.$primaryBG.';
    border:none
}








.media-volume-bar{
width:50px;
background:#808080;
display:inline-block;
height:'.$volume_height.'px;
 position:relative;
}
.media-volume-bar > span{
background:'.$primaryBG.';
width:100%;
height:100%;
position:absolute;
}



.media-playlist-count{left:40%;top:-5px;position: absolute;font-size:8px;padding:2px;background-color:'.$primaryBG.';color:black;border-radius:50%;min-width:12px;max-height:12px;display:inline-block;text-align: center;}
.media-player audio{ margin-top:10px}
.media-player.audio .media-meta>img{max-width:100%;float:left;width:'.$audio_img_size.'px;height:'.$audio_img_size.'px;padding:'.$audio_img_padding.'px;border:1px solid transparent;border-radius:'.$audio_img_radius.'%;}
.media-player.audio .audio-info{font-size:'.$audio_font_size.'px;padding:1px; position: absolute; bottom:40%;color: '.$navbar_color.';}
.img-spin{'.($audio_img_spin_speed>0 ? 'animation:ispin '.$audio_img_spin_speed.'s linear infinite' : '').'}

@keyframes ispin { 100% {transform:rotate(360deg); } }
.media-player.audio {
  color: #fff;
  background: linear-gradient(-45deg, '.$audio_bg_1.', '.$audio_bg_2.', '.$audio_bg_3.', '.$audio_bg_4.');
  background-size: 400% 400%;
  animation: Gradient 10s ease infinite;
  overflow:hidden;
  height:'.$audio_height.'px
}

@keyframes Gradient {
  0% {background-position: 0% 50%}
  50% {background-position: 100% 50%}
  100% {background-position: 0% 50%}
}



@media(max-width: 480px) {/*is Mobile*/

#media-volume{display:none}
.media-item-body {right:-20px;}


}


@media(max-width: 600px) {/*is Table & Mobile*/
.media-player.audio .media-item-body>div{ max-height:'.$audio_img_size_mb.'px }
.media-player.audio .audio-info {font-size:'.($audio_font_size-2).'px;}
.media-player.audio {height:'.$audio_height_mb.'px;}
.media-player.audio .media-meta>img{height:'.$audio_img_size_mb.'px;width:'.$audio_img_size_mb.'px;}

}


.media-player video{
  cursor:pointer;
  width: 100%;
  min-height: 100%;
  height:auto;
  object-fit: cover;
  }
  
.video-full-page{
  display:none;
    position: fixed;
    top: 50%;
    left: 50%;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    z-index: -100;
    transform: translateX(-50%) translateY(-50%);
   transition: 1s opacity;
   cursor:default;
}


';


//Tạo file css
$style=cssMinifier($style);
$path=PUBLIC_ROOT."/assets/media-player";
if (!is_dir($path)) {
  mkdir($path, 0755, true);
}
file_put_contents($path."/player__complete.css", $style);
echo $style;

//Tạo quảng cáo
$ads=[];
$ads["video"]=Storage::option("ads_video_video");
$ads["popup"]=Storage::option("ads_video_popup");
$adsContent='
$(document).ready(function() {
  var mediaPlayerAds='.json_encode($ads).';
  mediaPlayer(mediaPlayerAds);
});
';
file_put_contents($path."/ads__complete.js", $adsContent );
}

