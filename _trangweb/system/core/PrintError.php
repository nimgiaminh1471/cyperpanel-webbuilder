<?php
/*
# File báo lỗi tùy chỉnh
*/

function errorHandler($level, $msg, $file, $line, $context = null){
	if( explode(":", $msg)[0]=="mysqli_connect()" ){
		return;
	}
	
	$error = '
	<div class="menu bd-bot"><span style="color:red">File:</span> <b>***'.(explode(SYSTEM_ROOT,($file??''))[1]??'').'</b><br/>
	<span style="color:red">Line:</span> <b>'.$line.'</b></div>
	<div class="menu error-message"><span style="color:red">Message:</span> <b>'.preg_replace('/#([0-9])/', "<br/> <br/>",$msg).'</b></div>
	';
	if( file_exists($file) && isset($_COOKIE["login_key"]) ){
		$contents=file($file, FILE_USE_INCLUDE_PATH);
		$error.='
		'.htmlEncode($contents[$line-2]??"").'
		<div class="red">'.htmlEncode($contents[$line-1]??"").'</div>
		'.htmlEncode($contents[$line]??"").'
		';
	}
	printError($error);
}

function shutdownHandler(){
	$error = error_get_last();
	if(!empty($error)){
		$error = '
		<div class="menu bd-bot"><span style="color:red">File:</span> <b>***'.(explode(SYSTEM_ROOT,($error['file']??''))[1]??'').'</b><br/>
		<span style="color:red">Line:</span> <b>'.($error['line']??'').'</b></div>
		<div class="menu error-message"><span style="color:red">Message:</span> <b>'.($error['message']??'').'</b></div>
		';
		printError($error);
	}
}



function printError($msg,$title="Warning"){
echo '
<text>
<head>
<meta charset="UTF-8">
<title>'.$title.'</title>
<link rel="stylesheet" href="/assets/general/css/style__complete.css" media="all" />
</head>
<body>

<main data-id="error">
	<div class="modal">
		<div class="modal-body" style="max-width: 650px">
			<div class="heading heading-block"><span>'.$title.'</span></div>
			<div class="menu bd">'.$msg.'</div>
		</div>
	</div>
</main>


</body>
</text>
<script>
	var data=document.getElementsByTagName("text")[0].innerHTML;
	document.documentElement.innerHTML=data;
	setTimeout(function(){
		location.reload();
	},20e3);
</script>
';
die;
}




set_error_handler("errorHandler");
register_shutdown_function("shutdownHandler");