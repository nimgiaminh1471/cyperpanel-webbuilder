<?php
namespace pages\api\controllers;

class Captcha{

	//Tạo ảnh mã xác minh
	public function image(){
		$text=rand(1000,9999);
		if( isset($_SESSION["captcha_code"]) ){
			$text = $_SESSION["captcha_code"];
		}
		if( !isset($_REQUEST["no_captcha"]) ) {
			$_SESSION["captcha_code"] = $text;
		}
		$width = 60;
		$height = 24;
		$fontsize = 20;

		$img = imagecreate($width, $height);

	    //Nền trong suốt
		$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
		imagefill($img, 0, 0, $bg);
		imagecolortransparent ($img, $bg);
		$font = PUBLIC_ROOT."/assets/general/fonts/corsiva.ttf";
	    //Chữ
		$color = imagecolorallocate($img, 00, 00, 00);
		//Tạo chấm
		$pixel_color = imagecolorallocate($img, 0,0,0);
		for($i=0;$i<600;$i++) {
			imagesetpixel($img,rand()%200,rand()%50,$pixel_color);
		}
		header('Content-type: image/png');
		imagettftext($img, $fontsize, 0, 5, 20, $color, $font, $text);
		imagepng($img);
		imagedestroy($img);
	}

}

