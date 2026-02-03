<?php
/*
# Tìm kiếm bài viết
*/
namespace pages\search\widgets;
use classes\PostsListTemplate;
use Form;
use models\PostsCategories;
use models\Posts;
use DB;
class SearchPosts{

	//Thông số mặc định
	private static $option=[
		"type"=>"flex",
		"title"=>"",
		"titleClass"=>"heading-simple",
		"titleAlign"=>"left",
		"titleIcon"=>"fa-search",
		"itemBackground"=>"#FFFFFF",
		"itemPadding"=>"8px",
		"itemBorder"=>"#EAEAEA",
		"itemFontSize"=>17,
		"itemColor"=>"",
		"itemHover"=>"",
		"postslimit"=>20,
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
		"itemColumn"=>1,
		"itemTitleInline"=>0,
		"itemDescInline"=>1,
		"itemTime"=>1,
		"ajaxLoad"=>0,
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



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => 'Danh sách bài viết tìm được',
			"icon"  => "fa-newspaper-o",
			"color" =>"tomato"
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		$option=array_replace(self::$option, $option);
		$query=Posts::select("*");
		$keywordStr=GET("keyword");
		$keywordStr=urldecode($keywordStr);
		$keywordStr=str_replace(["/"], "", $keywordStr);
		$keywords[]=$keywordStr;
		$keywords[]=vnStrFilter($keywordStr, " ");
		$query->where("id", 0);
		foreach($keywords as $id=>$keyword){
			$splitKeyword["title"]=[];
			$splitKeyword["content"]=[];
			foreach( explode(" ", $keyword) as $split){
				$splitKeyword["title"][]=["title", "LIKE", "%$split%"];
				$splitKeyword["content"][]=["content", "LIKE", "%$split%"];
				if($id==1){
					$splitKeyword["link"][]=["link", "LIKE", "%$split%"];
				}
			}
			$query->orWhere($splitKeyword["link"]??"")->where([ ["status", "=", "public"], ["parent", ">", 0] ])->orWhere($splitKeyword["title"])->where([ ["status", "=", "public"], ["parent", ">", 0] ])->orWhere($splitKeyword["content"]??"")->where([ ["status", "=", "public"], ["parent", ">", 0] ]);
		}
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
		$cate=PostsCategories::select("title", "id")->whereIn("id", $cateID)->get();
		$option["cate"]=[];
		foreach($cate as $c){
			$option["cate"][$c->id]=$c->title;
		}
		$option["title"]="".GET("keyword")." ({$posts->total()})";
		if(empty($posts[0])){
			$option["header"]='<div class="alert-danger">'.__("Không tìm thấy mục nào, Xin vui lòng tìm lại!").'</div>';
		}
		$option["info"]='
		<div class="center bg" style="'.(empty($option["itemBorder"]) ? '' : 'border-bottom: 1px solid '.$option["itemBorder"].';').'">
			<form medthod="GET" action="/search" class="input-search input-search-large" style="padding: 10px 5px">
				<input placeholder="'.__("Tìm kiếm").'" class="input" type="search" name="keyword" value="'.GET("keyword").'" required="" />
				<button type="submit"><i class="fa fa-search"></i></button>
			</form>
		</div>
		';
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
			["type"=>"select", "name"=>"titleClass", "title"=>"Kiểu tiêu đề", "option"=>
				["heading-simple"=>"Simple", "heading-basic"=>"Basic", "heading-block"=>"Block", "heading-line"=>"Line", "heading-sharp"=>"Sharp", "alert-info"=>"Info", "alert-danger"=>"Danger"],
				"value"=>$titleClass, "horizontal"=>35
			],
			["type"=>"select", "name"=>"titleAlign", "title"=>"Căn chữ tiêu đề", "option"=>
				["left"=>"Trái", "center"=>"Giữa", "right"=>"Phải"],
				"value"=>$titleAlign, "horizontal"=>35
			],
			["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"Tiêu đề", "value"=>$title, "attr"=>'', "horizontal"=>35],
			["type"=>"icon", "name"=>"titleIcon", "title"=>"Icon tiêu đề", "value"=>$titleIcon],
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
			["html"=>'</div>']
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