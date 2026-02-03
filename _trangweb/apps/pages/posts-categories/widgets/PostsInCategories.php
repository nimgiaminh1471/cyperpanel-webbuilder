<?php
/*
# Bài viết trong chuyên mục
*/
namespace pages\posts__categories\widgets;
use classes\PostsListTemplate;
use Form;
use models\PostsCategories;
use models\Posts;
use DB;
class PostsInCategories{

	//Thông số mặc định
	private static $option=[
		"type"=>"flex",
		"itemBackground"=>"#FFFFFF",
		"itemPadding"=>"8px",
		"itemBorder"=>"#EAEAEA",
		"itemFontSize"=>17,
		"itemColor"=>"",
		"itemHover"=>"",
		"postslimit"=>10,
		"orderBy"=>"id",
		"orderType"=>"DESC",
		"seeMoreTitle"=>"",
		"seeMoreLink"=>"",
		"paginate"=>1,
		"categories"=>[],
		"flexImgHeight"=>100,
		"flexImgWidth"=>100,
		"itemImgRadius"=>0,
		"itemDescLeng"=>15,
		"itemDescSize"=>14,
		"itemDescColor"=>"#808080",
		"itemColumn"=>2,
		"itemTime"=>0,
		"itemTitleInline"=>1,
		"itemDescInline"=>1,
		"showDescription"=>1,
		"ajaxLoad"=>0,
		"breadcrumbEnable"=>1,
		"breadcrumbBG"=>"#FBFBFB",
		"albumType"=>"default",
		"albumSliderPlay"=>5,
		"textBG"=>"#000000",
		"textBGOpacity"=>0.5,
		"albumHoverOpacity"=>0.5,
		"albumHoverZoom"=>1,
		"columnAmountLg"=>2,
		"columnAmountMd"=>2,
		"columnAmountSm"=>1,
		"albumItemHeightSm"=>280,
		"albumItemHeightMd"=>250,
		"albumItemHeightLg"=>250,
		"gridColumnAmountLg"=>2,
		"gridColumnAmountMd"=>2,
		"gridColumnAmountSm"=>1,
		"gridItemHeightSm"=>280,
		"gridItemHeightMd"=>250,
		"gridItemHeightLg"=>250,
		"gridHoverZoom"=>1,
		"gridDescLeng"=>20,
		"gridDescHeight"=>80,
		"gridDescColor"=>"#808080",
		"gridDescSize"=>14,
	];
	private static $count=0;


	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => 'Bài viết trong chuyên mục',
			"icon"  => "fa-newspaper-o",
			"color" =>"tomato"
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		self::$count++;
		$option=array_replace(self::$option, $option);
		extract(WIDGET_DATA);
		$storage=unserialize($categories["storage"]);
		if(empty($option["titleIcon"])){
			$option["titleIcon"]=$storage["icon"];
		}
		$option=array_replace(self::$option, $option);
		if($option["showDescription"]==1 && !empty($storage["description"])){
			$option["description"]='<div class="menu-bg bd-bottom">'.$storage["description"].'</div>';
		}
		$query=Posts::whereIn("parent", PostsCategories::allChildren($categories["id"], true))->where([ ["status", "=", "public"], ["parent", ">", 0] ]);
		if($option["orderBy"]=="rand"){
			$query=$query->inRandomOrder();
		}else{
			$query=$query->orderBy("pin", "DESC")->orderBy($option["orderBy"], $option["orderType"]);
		}
		$posts=$query->paginate($option["postslimit"]);
		$cateID=[];
		foreach($posts as $p){
			$cateID[]=$p->parent;
		}
		if(empty($cateID)){
			$cateID=[0];
		}
		$cate=PostsCategories::select("title", "id")->whereIn("id", $cateID)->get();
		$option["cate"]=[];
		foreach($cate as $c){
			$option["cate"][$c->id]=$c->title;
		}
		$parents=PostsCategories::grandparents($categories["id"]);
		if(empty($parents)){
			$parents=[0];
		}
		$getCate=PostsCategories::select("link", "title")->whereIn("id", $parents)->get();
		foreach($getCate as $cat){
			$link="/posts-categories/".$cat->link;
			$breadcrumbData[$link]=$cat->title;
		}
		if(!empty($breadcrumbData) && $option["breadcrumbEnable"]==1){
			$option["breadcrumb"]=breadcrumb($breadcrumbData);
		}
		if(self::$count==1){
			$option["title"]=$categories["title"];
		}else{
			$option["paginate"]=0;
			$option["description"]="";
		}
		return PostsListTemplate::{$option["type"]}($posts, $option);
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"select", "name"=>"type", "title"=>"Kiểu danh sách", "option"=>
				["classic"=>"Cổ điển", "flex"=>"Chia cột", "album"=>"Album", "grid"=>"Danh sách lưới"],
			"value"=>$type, "horizontal"=>35, "attr"=>' data-id="postsListType" '],
			["type"=>"text", "name"=>"itemPadding", "title"=>"Khoảng cách lề", "note"=>"", "value"=>$itemPadding, "attr"=>'', "horizontal"=>35],
			["type"=>"color", "name"=>"itemBackground", "title"=>"Màu nền", "default"=>"", "value"=>$itemBackground, "required"=>false],
			["type"=>"color", "name"=>"itemBorder", "title"=>"Màu viền", "default"=>"", "value"=>$itemBorder, "required"=>false],
			["type"=>"number", "name"=>"itemFontSize", "title"=>"Cỡ link", "note"=>"", "min"=>0, "max"=>9999, "value"=>$itemFontSize,"attr"=>''],
			["type"=>"color", "name"=>"itemColor", "title"=>"Màu link", "default"=>"", "value"=>$itemColor, "required"=>false],
			["type"=>"color", "name"=>"itemHover", "title"=>"Màu link (Hover)", "default"=>"", "value"=>$itemHover, "required"=>false],

			["html"=>'
			<section class="panel panel-default">
				<div class="heading link">Tùy chọn bài viết</div>
				<div class="panel-body hidden">
			'],
			["type"=>"number", "name"=>"postslimit", "title"=>"Số lượng bài viết", "note"=>"", "min"=>0, "max"=>9999, "value"=>$postslimit,"attr"=>''],
			["type"=>"select", "name"=>"orderBy", "title"=>"Sắp xếp theo", "option"=>
				["id"=>"Ngày đăng", "updated_at"=>"Ngày chỉnh sửa", "count"=>"Lượt xem", "rand"=>"Ngẫu nhiên"],
			"value"=>$orderBy, "horizontal"=>35],
			["type"=>"select", "name"=>"orderType", "title"=>"Thứ tự", "option"=>
				["DESC"=>"Giảm dần", "ASC"=>"Tăng dần"],
			"value"=>$orderType, "horizontal"=>35],
			["html"=>'
				</div>
			</section>
			'],

			["html"=>'<div class="posts-list-type '.($type=="classic" ? "" : "hidden").'" data-id="classic">'],
				
			["html"=>'</div>'],

			["html"=>'<div class="posts-list-type '.($type=="flex" ? "" : "hidden").'" data-id="flex">'],
				["type"=>"number", "name"=>"flexImgWidth", "title"=>"Chiều rộng ảnh", "note"=>"", "min"=>0, "max"=>9999, "value"=>$flexImgWidth, "attr"=>''],
				["type"=>"number", "name"=>"flexImgHeight", "title"=>"Chiều cao ảnh", "note"=>"", "min"=>0, "max"=>9999, "value"=>$flexImgHeight, "attr"=>''],
				["type"=>"number", "name"=>"itemImgRadius", "title"=>"Bo tròn ảnh đại diện", "note"=>"", "min"=>0, "max"=>50, "value"=>$itemImgRadius, "attr"=>''],
				["type"=>"number", "name"=>"itemDescLeng", "title"=>"Số từ mô tả (0=tắt)", "note"=>"", "min"=>0, "max"=>9999, "value"=>$itemDescLeng,"attr"=>''],
				["type"=>"switch", "name"=>"itemDescInline", "title"=>"Không xuống dòng mô tả", "value"=>$itemDescInline],
				["type"=>"number", "name"=>"itemDescSize", "title"=>"Cỡ chữ mô tả", "note"=>"", "min"=>0, "max"=>9999, "value"=>$itemDescSize,"attr"=>''],
				["type"=>"color", "name"=>"itemDescColor", "title"=>"Màu chữ mô tả", "default"=>"", "value"=>$itemDescColor, "default"=>$itemDescColor, "required"=>true],
				["type"=>"select", "name"=>"itemColumn", "title"=>"Chia cột", "option"=>
					["2"=>"2 cột", "1"=>"1 cột"],
				"value"=>$itemColumn, "horizontal"=>35],
				["type"=>"switch", "name"=>"itemTime", "title"=>"Hiện thời gian & chuyên mục", "value"=>$itemTime],
				["type"=>"switch", "name"=>"itemTitleInline", "title"=>"Không xuống dòng tiêu đề", "value"=>$itemTitleInline],
			["html"=>'</div>'],

			["html"=>'<div class="posts-list-type '.($type=="album" ? "" : "hidden").'" data-id="album">'],
				["type"=>"select", "name"=>"albumType", "title"=>"Dạng hiển thị", "option"=>
					["default"=>"Danh sách", "grid"=>"Slider"],
					"value"=>$albumType, "horizontal"=>35, "attr"=>''
				],
				["type"=>"number", "name"=>"albumSliderPlay", "title"=>"Tự động chuyển slider (s)", "note"=>"", "min"=>1, "max"=>100, "value"=>$albumSliderPlay,"attr"=>''],
				["type"=>"number", "name"=>"columnAmountLg", "title"=>"Số bài/hàng (máy tính)", "note"=>"", "min"=>1, "max"=>10, "value"=>$columnAmountLg,"attr"=>''],
				["type"=>"number", "name"=>"columnAmountMd", "title"=>"Số bài/hàng (máy tính bảng)", "note"=>"", "min"=>1, "max"=>10, "value"=>$columnAmountMd,"attr"=>''],
				["type"=>"number", "name"=>"columnAmountSm", "title"=>"Số bài/hàng (điện thoại)", "note"=>"", "min"=>1, "max"=>10, "value"=>$columnAmountSm,"attr"=>''],
				["type"=>"number", "name"=>"albumItemHeightLg", "title"=>"Chiều cao(máy tính)", "note"=>"", "min"=>1, "max"=>9999, "value"=>$albumItemHeightLg,"attr"=>''],
				["type"=>"number", "name"=>"albumItemHeightMd", "title"=>"Chiều cao(máy tính bảng)", "note"=>"", "min"=>1, "max"=>9999, "value"=>$albumItemHeightMd,"attr"=>''],
				["type"=>"number", "name"=>"albumItemHeightSm", "title"=>"Chiều cao(điện thoại)", "note"=>"", "min"=>1, "max"=>9999, "value"=>$albumItemHeightSm,"attr"=>''],
				["type"=>"color", "name"=>"textBG", "title"=>"Màu nền link", "default"=>"", "value"=>$textBG, "default"=>$textBG, "required"=>true],
				["type"=>"number", "name"=>"textBGOpacity", "title"=>"Độ trong suốt", "note"=>"", "min"=>0, "max"=>1, "value"=>$textBGOpacity,"attr"=>'step="0.1"'],
				["type"=>"number", "name"=>"albumHoverOpacity", "title"=>"Độ trong suốt trỏ chuột", "note"=>"", "min"=>0, "max"=>1, "value"=>$albumHoverOpacity,"attr"=>'step="0.1"'],
				["type"=>"switch", "name"=>"albumHoverZoom", "title"=>"Hiệu ứng zoom ảnh trỏ chuột", "value"=>$albumHoverZoom],
			["html"=>'</div>'],

			["html"=>'<div class="posts-list-type '.($type=="grid" ? "" : "hidden").'" data-id="grid">'],
				["type"=>"number", "name"=>"gridColumnAmountLg", "title"=>"Số bài/hàng (máy tính)", "note"=>"", "min"=>1, "max"=>10, "value"=>$gridColumnAmountLg,"attr"=>''],
				["type"=>"number", "name"=>"gridColumnAmountMd", "title"=>"Số bài/hàng (máy tính bảng)", "note"=>"", "min"=>1, "max"=>10, "value"=>$gridColumnAmountMd,"attr"=>''],
				["type"=>"number", "name"=>"gridColumnAmountSm", "title"=>"Số bài/hàng (điện thoại)", "note"=>"", "min"=>1, "max"=>10, "value"=>$gridColumnAmountSm,"attr"=>''],
				["type"=>"number", "name"=>"gridItemHeightLg", "title"=>"Chiều cao(máy tính)", "note"=>"", "min"=>1, "max"=>9999, "value"=>$gridItemHeightLg,"attr"=>''],
				["type"=>"number", "name"=>"gridItemHeightMd", "title"=>"Chiều cao(máy tính bảng)", "note"=>"", "min"=>1, "max"=>9999, "value"=>$gridItemHeightMd,"attr"=>''],
				["type"=>"number", "name"=>"gridItemHeightSm", "title"=>"Chiều cao(điện thoại)", "note"=>"", "min"=>1, "max"=>9999, "value"=>$gridItemHeightSm,"attr"=>''],
				["type"=>"switch", "name"=>"gridHoverZoom", "title"=>"Hiệu ứng zoom ảnh trỏ chuột", "value"=>$gridHoverZoom],
				["type"=>"number", "name"=>"gridDescLeng", "title"=>"Số từ mô tả (0=tắt)", "note"=>"", "min"=>0, "max"=>9999, "value"=>$gridDescLeng,"attr"=>''],
				["type"=>"number", "name"=>"gridDescHeight", "title"=>"Chiều cao phần mô tả", "note"=>"", "min"=>0, "max"=>9999, "value"=>$gridDescHeight,"attr"=>''],
				["type"=>"number", "name"=>"gridDescSize", "title"=>"Cỡ chữ mô tả", "note"=>"", "min"=>0, "max"=>9999, "value"=>$gridDescSize,"attr"=>''],
				["type"=>"color", "name"=>"gridDescColor", "title"=>"Màu chữ mô tả", "default"=>"", "value"=>$gridDescColor, "default"=>$gridDescColor, "required"=>true],
			["html"=>'</div>'],

			["type"=>"switch", "name"=>"showDescription", "title"=>"Hiện mô tả chuyên mục", "value"=>$showDescription],
			["type"=>"switch", "name"=>"ajaxLoad", "title"=>"Chuyển trang nhanh", "value"=>$ajaxLoad],
			["type"=>"switch", "name"=>"breadcrumbEnable", "title"=>"Liên kết tới mục cha", "value"=>$breadcrumbEnable],
			["type"=>"color", "name"=>"breadcrumbBG", "title"=>"Màu nền", "default"=>"", "value"=>$breadcrumbBG, "required"=>false],
		];
		$out.=Form::create([
			"form"=>$form,
			"function"=>"",
			"prefix"=>"",
			"name"=>"{$prefixName}[data]",
			"class"=>"menu",
			"hover"=>false
		]);
		return $out;
	}




}
//</Class>