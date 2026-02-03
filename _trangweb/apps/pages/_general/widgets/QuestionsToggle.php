<?php
/*
# Chèn nội dung tùy ý
*/
namespace pages\_general\widgets;
use Form;
use Assets, Widget, Storage;
class QuestionsToggle{

	//Thông số mặc định
	private static $option=[
		"title" => "Tiêu đề",
		"data"  => []
	];



	//Thông tin widget
	public static function info($key){
		$info=[
			"name"  => "Danh sách câu hỏi thường gặp",
			"icon"  => "fa-question",
			"color" =>""
		];
		return $info[$key];
	}



	//Hiện widget
	public static function show($option){
		extract( array_replace(self::$option, $option) );
		if( !is_array($data) ){
			return;
		}
		$out = "";
		$out .= '
		<section class="questions-toggle main-layout pd-20">
			<h2 class="center pd-20">'.$title.'</h2>
			<div class="flex flex-middle flex-medium">
		';
		foreach($data as $item){
			$out .= '
				<div class="width-50 pd-10 questions-toggle-item">
					<div class="questions-toggle-item-title">
						'.$item["title"].'
						<i class="fa fa-angle-down"></i>
					</div>
					<div class="hidden">
						'.nl2br($item["description"]).'
					</div>
				</div>
			';
		}
		$out .= '
			</div>
		</section>
		';
		$out .= '
		<script>
			$(".questions-toggle-item-title").on("click", function(){
				var parentEl = $(this).parent();
				$(".questions-toggle-item>.hidden").not( parentEl.find(".hidden") ).slideUp();
				$(".questions-toggle-item-title").not( $(this) ).removeClass("primary-color");
				$(".questions-toggle-item-title>i").not( $(this).children("i") ).removeClass("fa-angle-up").addClass("fa-angle-down");
				if( parentEl.find(".hidden").is(":hidden") ){
					$(this).find("i").removeClass("fa-angle-down").addClass("fa-angle-up");
				}else{
					$(this).find("i").removeClass("fa-angle-up").addClass("fa-angle-down");
				}
				parentEl.find(".hidden").slideToggle();
				$(this).toggleClass("primary-color");
			});
		</script>
		';
		if(PAGE_EDITOR){
$style = <<<HTML
		.questions-toggle-item{
			border-bottom: 1px solid #ebebeb;
		}
		.questions-toggle .flex{
			justify-content: center;
			align-items: stretch;
		}
		.questions-toggle-item-title{
			cursor: pointer;
			font-weight: bold;
			position: relative;
			padding-right: 10px;
			line-height: 1.4
		}
		.questions-toggle-item-title:hover{
			opacity: .6
		}
		.questions-toggle-item-title>i{
			position: absolute;
			right: 0;
			top: 50%;
			transform: translate(0, -50%)
		}
		.questions-toggle-item>.hidden{
			padding: 10px 0;
			line-height: 1.6;
			text-align: justify;
		}
HTML;
		Widget::css($style);
		}
		return $out;
	}



	//Chỉnh sửa widget
	public static function editor($option, $prefixName){
		$out="";
		extract( array_replace(self::$option, $option) );
		$form=[
			["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=> $title, "attr"=>''],
			["html"=>
				Form::itemManager([
					"data"=>$data,
					"name"=>"{$prefixName}[data][data]",
					"sortable"=>true,
					"max"=>10,
					"form"=>[
						["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"", "value"=> "", "attr"=>''],
						["type"=>"textarea", "name"=>"description", "title"=>"Giới thiệu", "note"=>"", "value"=>"", "attr"=>'', "full"=>true],
					]
				])
			],
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