<?php
$remoteImage = "background/".rand(1,20).".jpg";
$imginfo = getimagesize($remoteImage);
header("Content-type: {$imginfo['mime']}");
readfile($remoteImage);