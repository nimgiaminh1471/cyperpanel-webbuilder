<?php
/*
##### Class xử lý hình ảnh
*/

class Image{

    //Thu nhỏ cỡ ảnh
    public static function resize($image, $save='', $width=0, $height=0, $compression=100){
    	if( !is_dir( dirname($save) ) ){
    		mkdir( dirname($save), 0755, true);
    	}
     $image_info   = @getimagesize($image);
     $image_width  = $image_info[0];
     $image_height = $image_info[1];
     if($image_width<$width){ $wrong=1; }
     if($image_height<$height){ $wrong=1; } 
     if(empty($wrong)){
        switch ($image_info['mime']) {
            case "image/jpeg":
            $img = @imagecreatefromjpeg($image);
            break;
            case "image/gif":
            $img = @imagecreatefromgif($image);
            break;
            case "image/png":
            $img = @imagecreatefrompng($image);
            break;
        }
        if( !isset($img) ){
            return;
        }
        if($width==0){
    	// Resize by height
         $ratio = $height / $image_height;
         $width = $image_width * $ratio;
     }elseif($height==0){
    	// Resize by width
         $ratio = $width / $image_width;
         $height = $image_height * $ratio;
     }


    	// Tạo
     $new_image = imagecreatetruecolor($width, $height);
     imagealphablending( $new_image, false );
     imagesavealpha( $new_image, true );

     imagecopyresampled($new_image, $img, 0, 0, 0, 0, $width, $height, $image_width, $image_height);
     $img = $new_image;


    	// Lưu hình ảnh mới
     switch ($image_info['mime']) {
        case "image/jpeg":
        imagejpeg($img, $save, $compression);
        break;
        case "image/gif":
        imagegif($img, $save);
        break;
        case "image/png":
        imagepng($img, $save);
        break;
    }

    return true;
}else{
   return false;
}


    }





    //##### Đóng dấu hình ảnh
    public static function copyright($image, $save, $copyright, $marge_right=1, $marge_bottom=1){
        if(pathinfo($copyright,PATHINFO_EXTENSION)!='png'){ $wrong=1; }
        if(!file_exists($image)){ $wrong=1; }

        if(empty($wrong)){
            $temp="$image.temp";
            if(copy($image,$temp)){

                $folder=pathinfo($save,PATHINFO_DIRNAME);
                if(!file_exists($folder)){ mkdir($folder, 0755, true); }
                $image_info = getimagesize($temp);
                $image_width  = $image_info[0];
                switch ($image_info['mime']) {
                    case "image/jpeg":
                    $img = @imagecreatefromjpeg($temp);
                    break;
                    case "image/png":
                    $img = @imagecreatefrompng($temp);
                    imagealphablending( $img, false );
                    imagesavealpha( $img, true );
                    break;
                }

                $copyright_size   = @getimagesize($copyright);
                $copyright_width  = $copyright_size[0];
                if(($image_width-100) > $copyright_width){
    	           //Create
                    $stamp = imagecreatefrompng($copyright);
                    $sx = imagesx($stamp);
                    $sy = imagesy($stamp);


                    imagecopy($img, $stamp, imagesx($img) - $sx - $marge_right, imagesy($img) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
                    //Save new image
                    switch ($image_info['mime']) {
                        case "image/jpeg":
                        imagejpeg($img,$save);
                        break;
                        case "image/png":
                        imagepng($img,$save);
                        break;
                    }
                }
                unlink($temp);
            }
        }

    }

    //Xoay ảnh
    public static function rotate($filename, $rotate=90){
        $imageInfo=@getimagesize($filename);
        switch($imageInfo['mime']){
            case "image/jpeg":
                $image = @imagecreatefromjpeg($filename);
            break;
            case "image/gif":
                $image = @imagecreatefromgif($filename);
            break;
            case "image/png":
                $image = @imagecreatefrompng($filename);
            break;
        }
        $transparency = imagecolorallocatealpha( $image,0,0,0,127 );
        $image = imagerotate($image, $rotate, $transparency, 1);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Lưu hình ảnh mới
        switch($imageInfo['mime']){
            case "image/jpeg":
                imagejpeg($image,$filename,100);
            break;
            case "image/gif":
                imagegif($image,$filename);
            break;
            case "image/png":
                imagepng($image,$filename);
            break;
        }
        imagedestroy($image);
    }

}//</class>