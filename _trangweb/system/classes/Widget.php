<?php
/*
##### Các Function xử lý tiện ích widget
*/

//<Class>
class Widget{
	private static $called=false, $css="";

	//Hiện widget
	public static function show($group, $allPage=0){
		$out='';
		$out.='<div class="widget" data-group="'.$group.'" id="widgetWrap-'.$group.'">';
		if(PAGE_EDITOR){
			$out.=self::manager();//Danh sách & quản lý widget
			$out.='
				<a href="#widgetWrap-'.$group.'" class="widget-add-btn center" data-all="0" data-group="'.$group.'"><i class="fa fa-plus"></i></a>
			';
		}
		$widgetAll=Storage::widget($group, []);
		$widgetData["all"]=$widgetAll["all"]??[];
		$widgetData[Route::folder()]=$widgetAll[Route::folder()]??[];
			foreach($widgetData as $location=>$item){
			$i=0;
			foreach($item as $id=>$wg){
				$i++;
				$data=$wg["data"];
				$device=isset($data["_device"]) ? (array)$data["_device"] : ["mb","tl","dk"];
				$widgetName=explode("@", $wg["type"]);
				$class="pages\\{$widgetName[0]}\\widgets\\{$widgetName[1]}";
				if(class_exists($class)){
					$out.='<div id="widgetItem-'.$group.'-'.$id.'" class="widget-item" data-id="'.$group.'-'.$id.'">';
					if(PAGE_EDITOR){
						$out.='<form>';
						$toolbar=[
							"delete"=>["title"=>"Xóa", "icon"=>"fa-times"],
							"editor"=>["title"=>"Chỉnh sửa", "icon"=>"fa-pencil"],
							"move-down"=>["title"=>"Chuyển xuống", "icon"=>"fa-chevron-circle-down"],
							"move-up"=>["title"=>"Chuyển lên", "icon"=>"fa-chevron-circle-up"],
						];
						if($i==1){
							unset($toolbar["move-up"]);
						}
						if(count($item)==$i){
							unset($toolbar["move-down"]);
						}
						$out.='<div class="widget-toolbar">';
						foreach($toolbar as $action=>$info){
							$out.='<a href="#widgetItem-'.$group.'-'.$id.'" data-location="'.$location.'" data-action="'.$action.'" title="'.$info["title"].'" class="tooltip widget-item-action primary-hover'.($location=="all" ? ' widget-item-action-all' : '').'" data-id="'.$id.'"><i class="fa '.$info["icon"].'"></i></a>';
						}
						$form=[
							["type"=>"text", "name"=>"_name", "title"=>"", "note"=>"Tên widget", "value"=>$data["_name"]??"", "attr"=>''],
							["type"=>"text", "name"=>"title", "title"=>"Tiêu đề", "note"=>"Tiêu đề", "value"=>$data["title"]??"", "attr"=>'', "horizontal"=>35],
							["type"=>"color", "name"=>"titleColor", "title"=>"Màu chữ tiêu đề", "default"=>"", "value"=>$data["titleColor"]??"", "required"=>false],
							["type"=>"icon", "name"=>"titleIcon", "title"=>"Icon tiêu đề", "value"=>$data["titleIcon"]??""],
							["type"=>"select", "name"=>"titleClass", "title"=>"Kiểu tiêu đề", "option"=>
								["heading-simple"=>"Simple", "heading-basic"=>"Basic", "heading-block"=>"Block", "heading-line"=>"Line", "heading-sharp"=>"Sharp", "alert-info"=>"Info", "alert-danger"=>"Danger"],
								"value"=>$data["titleClass"]??"", "horizontal"=>35
							],
							["type"=>"select", "name"=>"titleAlign", "title"=>"Căn chữ tiêu đề", "option"=>
								["left"=>"Trái", "center"=>"Giữa", "right"=>"Phải"],
								"value"=>$data["titleAlign"]??"", "horizontal"=>35
							],
							["type"=>"checkbox", "name"=>"_device", "title"=>"Hiển thị trên", "checkbox"=>["mb"=>'<i class="fa-icon fa fa-mobile"></i> Điện thoại', "tl"=>'<i class="fa-icon fa fa-tablet"></i> Máy tính bảng', "dk"=>'<i class="fa-icon fa fa-desktop"></i> Máy tính lớn'], "value"=>$device ],
							["type"=>"switch", "name"=>"_fixed", "title"=>"Treo theo màn hình", "value"=>$data["_fixed"]??0],
							["type"=>"color", "name"=>"_color", "title"=>"Màu chữ", "default"=>"", "value"=>$data["_color"]??"", "required"=>false],
							["type"=>"color", "name"=>"_bg", "title"=>"Màu nền", "default"=>"", "value"=>$data["_bg"]??"", "required"=>false],
							["type"=>"image", "name"=>"_img_bg", "title"=>"Ảnh nền", "value"=>$data["_img_bg"]??"", "post"=>0],
							["type"=>"number", "name"=>"_radius", "title"=>"Độ bo góc", "note"=>"", "value"=>$data["_radius"]??0, "min"=>0, "max"=>100],
							["type"=>"text", "name"=>"_max_width", "title"=>"Chiều rộng tối đa", "note"=>"", "value"=>$data["_max_width"]??"100%", "attr"=>'', "horizontal"=>35],

							["html"=>'
								<div class="panel panel-default">
									<div class="heading link">Bố cục (trên điện thoại)</div>
									<div class="panel-body hidden">
							'],
								["type"=>"number", "name"=>"_small_pdTop", "title"=>"Căn lề (trên)", "note"=>"", "value"=>$data["_small_pdTop"]??0, "min"=>0, "max"=>1997],
								["type"=>"number", "name"=>"_small_pdBot", "title"=>"Căn lề (dưới)", "note"=>"", "value"=>$data["_small_pdBot"]??0, "min"=>0, "max"=>1997],
								["type"=>"number", "name"=>"_small_pdLeftRight", "title"=>"Căn lề (phải-trái)", "note"=>"", "value"=>$data["_small_pdLeftRight"]??0, "min"=>0, "max"=>1997],
								["type"=>"number", "name"=>"_small_mrgTop", "title"=>"Khoảng cách với phần trên", "note"=>"", "value"=>$data["_small_mrgTop"]??0, "min"=> -1000, "max"=>1997],
								["type"=>"number", "name"=>"_small_mrgBot", "title"=>"Khoảng cách với phần dưới", "note"=>"", "value"=>$data["_small_mrgBot"]??0, "min"=> -1000, "max"=>1997],
								["type"=>"text", "name"=>"_small_max_height", "title"=>"", "note"=>"Chiều cao tối đa", "value"=>$data["_small_max_height"] ?? "auto", "attr"=>''],
							["html"=>'
								</div></div>
							'],

							["html"=>'
								<div class="panel panel-default">
									<div class="heading link">Bố cục (trên máy tính bảng)</div>
									<div class="panel-body hidden">
							'],
								["type"=>"number", "name"=>"_medium_pdTop", "title"=>"Căn lề (trên)", "note"=>"", "value"=>$data["_medium_pdTop"]??0, "min"=>0, "max"=>1997],
								["type"=>"number", "name"=>"_medium_pdBot", "title"=>"Căn lề (dưới)", "note"=>"", "value"=>$data["_medium_pdBot"]??0, "min"=>0, "max"=>1997],
								["type"=>"number", "name"=>"_medium_pdLeftRight", "title"=>"Căn lề (Phải-trái)", "note"=>"", "value"=>$data["_medium_pdLeftRight"]??0, "min"=>0, "max"=>1997],
								["type"=>"number", "name"=>"_medium_mrgTop", "title"=>"Khoảng cách với phần trên", "note"=>"", "value"=>$data["_medium_mrgTop"]??0, "min" => -1000, "max"=>1997],
								["type"=>"number", "name"=>"_medium_mrgBot", "title"=>"Khoảng cách với phần dưới", "note"=>"", "value"=>$data["_medium_mrgBot"]??0, "min"=> -1000, "max"=>1997],
								["type"=>"text", "name"=>"_medium_max_height", "title"=>"", "note"=>"Chiều cao tối đa", "value"=>$data["_medium_max_height"] ?? "auto", "attr"=>''],
							["html"=>'
								</div></div>
							'],

							["html"=>'
								<div class="panel panel-default">
									<div class="heading link">Bố cục (trên máy tính)</div>
									<div class="panel-body hidden">
							'],
								["type"=>"number", "name"=>"_large_pdTop", "title"=>"Căn lề (trên)", "note"=>"", "value"=>$data["_large_pdTop"]??0, "min"=>0, "max"=>1997],
								["type"=>"number", "name"=>"_large_pdBot", "title"=>"Căn lề (dưới)", "note"=>"", "value"=>$data["_large_pdBot"]??0, "min"=>0, "max"=>1997],
								["type"=>"number", "name"=>"_large_pdLeftRight", "title"=>"Căn lề (Phải-trái)", "note"=>"", "value"=>$data["_large_pdLeftRight"]??0, "min"=>0, "max"=>1997],
								["type"=>"number", "name"=>"_large_mrgTop", "title"=>"Khoảng cách với phần trên", "note"=>"", "value"=>$data["_large_mrgTop"]??0, "min"=>-1000, "max"=>1997],
								["type"=>"number", "name"=>"_large_mrgBot", "title"=>"Khoảng cách với phần dưới", "note"=>"", "value"=>$data["_large_mrgBot"]??0, "min"=>-1000, "max"=>1997],
								["type"=>"text", "name"=>"_large_max_height", "title"=>"", "note"=>"Chiều cao tối đa", "value"=>$data["_large_max_height"] ?? "auto", "attr"=>''],
							["html"=>'
								</div></div>
							'],
						];
						$formAnimate=[
							["type"=>"select", "name"=>"_animate_type", "title"=>"Kiểu hiệu ứng", "option"=>
								[
									""=>"Tắt",
									"fade-up"=>"fade-up", "fade-down"=>"fade-down", "fade-right"=>"fade-right", "fade-left"=>"fade-left",
									"flip-left"=>"flip-left", "flip-right"=>"flip-right", "flip-up"=>"flip-up",
									"zoom-in"=>"zoom-in", "zoom-in-up"=>"zoom-in-up", "zoom-in-down"=>"zoom-in-down", "zoom-in-left"=>"zoom-in-left", "zoom-in-right"=>"zoom-in-right", 
								],
								"value"=>$data["_animate_type"]??"fade-up", "horizontal"=>35
							],
							["type"=>"select", "name"=>"_animate_placement", "title"=>"Vị trí lướt đến", "option"=>
								[
									"top-bottom"=>"top-bottom",
									"center-bottom"=>"center-bottom",
									"bottom-bottom"=>"bottom-bottom",
									"top-center"=>"top-center",
									"center-center"=>"center-center",
									"bottom-center"=>"bottom-center"
								],
								"value"=>$data["_animate_placement"]??"top-bottom", "horizontal"=>35
							],
							["type"=>"number", "name"=>"_animate_duration", "title"=>"Độ trễ hiệu ứng", "note"=>"", "value"=>$data["_animate_duration"]??1000, "min"=>0, "max"=>3000, "attr"=>'step="500"'],
							
						];
						$pagesType=[Route::folder()=>PAGE["name"]];
						$pagesType["all"]="Tất cả các trang";
						$widgetFolder=explode("@", $wg["type"])[0];
						if($widgetFolder=="_general"){
							array_unshift($form,[
								"type"=>"radio",
								"name"=>"new_location",
								"title"=>"Hiển thị tại",
								"radio"=>$pagesType,
								"value"=>$location
							]);
							array_unshift($form,[
								"type"=>"hidden",
								"name"=>"old_location",
								"value"=>$location
							]);
						}
						$prefixName="widget[$group][$location][$id]";
						$out.='</div>
						<div class="widget-manager hidden">
							<div title="Giữ chuột để di chuyển" class="alert-info widget-manager-drag tooltip">
								#'.$i.' '.($data["_name"]??"").'
								<i style="display: inline-block" class="pd-10 link widget-manager-save fa fa-check right-icon"> LƯU LẠI</i>
							</div>
							<div>
								<input type="hidden" name="'.$prefixName.'[type]" value="'.$wg["type"].'" />
								<div class="panel panel-warning">
									<div class="heading link">Thiết lập chung</div>
									<div class="panel-body hidden">
										'.Form::create([
											"form"=>$form,
											"function"=>"",
											"prefix"=>"",
											"name"=>"{$prefixName}[data]",
											"class"=>"menu",
											"hover"=>false
										]).'
									</div>
								</div>
								<div class="panel panel-warning">
									<div class="heading link">Hiệu ứng lướt</div>
									<div class="panel-body hidden">
										'.Form::create([
											"form"=>$formAnimate,
											"function"=>"",
											"prefix"=>"",
											"name"=>"{$prefixName}[data]",
											"class"=>"menu",
											"hover"=>false
										]).'
									</div>
								</div>
								'.$class::editor($wg["data"], $prefixName).'
							</div>
						</div>
						';
						$out.='</form>';
					}
					$out.='
					<section class="widget-item-body'.(in_array("mb", $device) ? '' : ' hidden-small').''.(in_array("tl", $device) ? '' : ' hidden-medium').''.(in_array("dk", $device) ? '' : ' hidden-large').'" '.(empty($data["_animate_type"]??"fade-up") ? '' : 'data-aos="'.($data["_animate_type"]??"fade-up").'" data-aos-anchor-placement="'.($data["_animate_placement"]??"top-bottom").'" data-aos-duration="'.($data["_animate_duration"]??1000).'"').'>
					<div style="margin: auto; max-width: '.($data["_max_width"] ?? "100%").'">
					';
					if(PAGE_EDITOR){
						$style='
						#widgetItem-'.$group.'-'.$id.'>.widget-item-body{
							padding-top: '.($data["_large_pdTop"]??0).'px;
							padding-bottom: '.($data["_large_pdBot"]??0).'px;
							padding-left: '.($data["_large_pdLeftRight"]??0).'px;
							padding-right: '.($data["_large_pdLeftRight"]??0).'px;
							margin-top: '.($data["_large_mrgTop"]??0).'px;
							margin-bottom: '.($data["_large_mrgBot"]??0).'px;
							max-height: '.($data["_large_max_height"] ?? "auto").';
							'.( ($data["_large_max_height"] ?? "auto") == "auto" ? '' : 'overflow: auto;') .'
							'.( empty($data["_color"]??"") ? '' : 'color: '.$data["_color"].';').'
							'.( empty($data["_bg"]??"") ? '' : 'background-color: '.$data["_bg"].';').'
							'.( empty($data["_img_bg"]??"") ? '' : 'background-image: url('.$data["_img_bg"].'); background-position: center; background-repeat: no-repeat; background-size: cover;').'
							'.( empty($data["_radius"]??"") ? '' : 'border-radius: '.$data["_radius"].'px;').'
						}
						#widgetItem-'.$group.'-'.$id.'>.widget-item-body::-webkit-scrollbar-track {
							-webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
							background-color: #f5f5f5;
							border-radius: 3px;
						}

						#widgetItem-'.$group.'-'.$id.'>.widget-item-body::-webkit-scrollbar {
							width: 6px;
							background-color: #f5f5f5;
						}

						#widgetItem-'.$group.'-'.$id.'>.widget-item-body::-webkit-scrollbar-thumb {
							background-color: #cacaca;
							border-radius: 3px;
						}
						@media(max-width: 767px){
							#widgetItem-'.$group.'-'.$id.'>.widget-item-body{
								padding-top: '.($data["_small_pdTop"]??0).'px;
								padding-bottom: '.($data["_small_pdBot"]??0).'px;
								padding-left: '.($data["_small_pdLeftRight"]??0).'px;
								padding-right: '.($data["_small_pdLeftRight"]??0).'px;
								margin-top: '.($data["_small_mrgTop"]??0).'px;
								margin-bottom: '.($data["_small_mrgBot"]??0).'px;
								max-height: '.($data["_small_max_height"] ?? "auto").';
							}
						}

						@media(min-width: 768px) and (max-width: 1023px){
							#widgetItem-'.$group.'-'.$id.'>.widget-item-body{
								padding-top: '.($data["_medium_pdTop"]??0).'px;
								padding-bottom: '.($data["_medium_pdBot"]??0).'px;
								padding-left: '.($data["_medium_pdLeftRight"]??0).'px;
								padding-right: '.($data["_medium_pdLeftRight"]??0).'px;
								margin-top: '.($data["_medium_mrgTop"]??0).'px;
								margin-bottom: '.($data["_medium_mrgBot"]??0).'px;
								max-height: '.($data["_medium_max_height"] ?? "auto").';
							}
						}

						@media(min-width: 1024px){

						}
						';
						$out.=Widget::css($style);
					}
					$out.='
					'.$class::show($wg["data"], $id).'
					</div>
					</section>';
						if( ($data["_fixed"]??"n")==1){
						$out.='
						<script>
							$(document).on("scroll", function() {
								if($(document).width()>1024){
									var thisEL=$(\'.widget-item[data-id="'.$group.'-'.$id.'"]\');
									var width=thisEL.parent().width();
								    if($(this).scrollTop()>(thisEL.parent().position().top+thisEL.parent().height()) ){
								    	var top=0;
								    	if($("#header").css("position")=="fixed"){
								    		var top=$("#header").height();
								    	}
								        thisEL.css({"position": "fixed", "top": top+"px", "width": width});
								    }else{
								    	thisEL.css({"position": ""});
								    }
								}
							});
						</script>
						';
					}
					$out.='</div>';
				}
			}
		}
		$out.='</div>';
		return $out;
	}

	//Lấy thông tin widget
	function info($name, $key){
		$wg_name="widget_".$name."";
		if(class_exists($wg_name)){
		return $wg_name::info($key);
		}
	}

	//Quản lý widget
	public static function manager(){
		//Danh sách widget
		$out='
		<div class="modal widget-list hidden">
			<div class="modal-body bg" style="max-width:850px">
				<div class="heading heading-block">
					<i class="fa fa-plus"></i>
					Thêm mục
					<i class="modal-close link fa"></i>
				</div>
					<ul class="flex flex-middle flex-large ul-none-style">
		';
		$widgets[Route::folder()]=glob(Route::path()."/widgets/*.php");
		$widgets["_general"]=glob(APPS_ROOT."/pages/_general/widgets/*.php");
		foreach($widgets as $folder=>$files){
			foreach($files as $file){
				$class="pages\\".Route::nameSpace($folder)."\\widgets\\".pathinfo($file, PATHINFO_FILENAME)."";
				if( empty( $class::info("name") ) ){
					continue;
				}
				$out.='
					<div class="width-33x">
						<div class="panel panel-default link panel-last primary-hover" data-type="'.Route::nameSpace($folder).'@'.pathinfo($file, PATHINFO_FILENAME).'">
							<div class="heading" style="'.(empty( $class::info("color") ) ? '' : 'color: '.$class::info("color").' !important').'"><i class="fa fa-icon '.$class::info("icon").'"></i> '.$class::info("name").'</div>
						</div>
					</div>
				';
			}
		}
		$out.='</ul></div></div>';
		if(!self::$called){
			Assets::footer("/assets/admin/widget/style.css", "/assets/admin/widget/script.js");
			self::$called=true;
			//Quản lý
			switch( POST("_widgetManager") ){

				//Thêm mới
				case "add":
					$data=Storage::widget(POST("group"), []);
					if(!is_array($data)){
						$data=[];
					}
					if( POST("allPage")==1 ){
						$pageType="all";
					}else{
						$pageType=Route::folder();
					}
					$data[$pageType][]=[ "type"=>POST("type"), "data"=>[] ];
					Storage::update("widget", [
						POST("group")=>$data
					]);
				break;

				//Xóa
				case "delete":
					$data=Storage::widget(POST("group"));
					if(empty($data)){
						$data=[];
					}

					if( isset($data[POST("location")][POST("id")]) ){
						unset($data[POST("location")][POST("id")]);
						Storage::update("widget", [
							POST("group")=>$data
						], false);
					}
				break;

				//Cập nhật
				case "update":
					foreach(POST("widget", []) as $group=>$newData){
						$data=Storage::widget($group, []);
						foreach($newData as $location=>$items){
							foreach($items as $id=>$item){
								$oldLocation=$item["data"]["old_location"]??null;
								$newLocation=$item["data"]["new_location"]??null;
								if( !empty($oldLocation) && $oldLocation!=$newLocation ){
									unset($data[$oldLocation][$id], $newData[$oldLocation][$id]);
									$newData[$newLocation][$id]=$item;
								}
							}
						}
						Storage::update("widget", [
							$group=>array_replace($data, $newData)
						], false);
					}
				break;
			}
		}
		return $out;
	}

	//Lưu CSS vào file
	public static function css($style, $write=false){
		self::$css.=$style;
		if($write){
			$path=PUBLIC_ROOT."/assets/general/css/widgets";
			if( !file_exists($path) ){
				mkdir($path, 0755, true);
			}
			file_put_contents($path."/".Route::folder().".css", cssMinifier(self::$css));
		}
		return '<style>'.$style.'</style>';
	}

}//</Class>