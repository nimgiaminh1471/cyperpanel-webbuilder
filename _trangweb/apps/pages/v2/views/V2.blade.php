@extends("Default")
@php
	use classes\WebBuilder;
	define("PAGE", [
		"name"        =>"Tên trang",
		"title"       =>"",
		"description" =>"",
		"loading"     =>0,
		"background"  =>"",
		"image"       =>"",
		"canonical"   =>"",
		"robots"      =>"index,follow"
	]);

	// dd( WebBuilder::createUser() ); // Tạo tài khoản
	// dd( WebBuilder::deleteUser() ); // Xóa tài khoản
	/*dd( WebBuilder::ftpUpload([
		'sourceFile' => PUBLIC_ROOT.'/code.zip',
		'savePath'   => '/domains/demo.azit.vn/code.zip'
	]) ); // Upload file*/

/*	dd( WebBuilder::extractFile([
		'action'    => 'extract',
		'path'      => '/domains/demo.azit.vn/code.zip',
		'directory' => '/domains/demo.azit.vn/public_html',
		'page'      => 2
	]) ); // Giải nén file*/

	// dd( WebBuilder::ftpReadFile('/domains/demo.azit.vn/public_html/wp-login.php') ); // Đọc nội dung file

	// dd( WebBuilder::ftpDelete('/domains/demo.azit.vn/public_html/wp-trackback.php') ); // Xóa file
	dd( WebBuilder::copySourceCode([
		'sourcePath' => SERVER_ROOT.'/domains/hocvothuat.hethongwebsite.com/public_html',
		'savePath'   => '/domains/demo.azit.vn/code.zip'
	]) ); // Upload file
@endphp


@section("main")
	@parent
@endsection


@section("script")

@endsection


@section("footer")
	@parent
@endsection
