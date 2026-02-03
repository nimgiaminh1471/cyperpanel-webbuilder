<?php
/*
# Tạo file Controller mẫu
*/

file_put_contents($path,
'<?php
namespace pages\\'.self::nameSpace().'\\controllers;
use models\Users;
use DB;

//permission("admin", null, "/user/login");

class '.$controller[0].'{

	public function '.$controller[1].'('.$param.'){
		$controllerName = "Controller: '.$controller[0].'";
		$functionName="Function: '.$controller[1].'";
		view("'.$controller[0].'", compact("controllerName", "functionName"));
	}

}

');