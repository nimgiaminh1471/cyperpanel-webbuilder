<?php
namespace pages\api\controllers;
use models\Users;
use DB,Storage;
use models\Posts;
use models\BuilderDomain;
use OnlineChat as OnlineChatInit;

class OnlineChat{

	public function index(){
		echo '<main>'.OnlineChatInit::setup(null).'</main>';
	}
}