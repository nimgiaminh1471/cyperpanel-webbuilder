<?php
/*
# Tạo form nhanh
*/
/*

$form=[
	//Chọn màu
	["type"=>"color", "name"=>"id_1", "title"=>"Color", "value"=>"#FF00FF", "required"=>true],
	//Chọn file
	["type"=>"file", "name"=>"id_2", "title"=>"File", "note"=>"Ghi chú", "value"=>"", "ext"=>"jpg,png,gif,jpeg"],
	//Chọn ảnh
	["type"=>"image", "name"=>"img_1", "title"=>"Image", "value"=>""],
	//Chọn icon
	["type"=>"icon", "name"=>"id_3", "title"=>"Icon", "value"=>""],
	//Switch
	["type"=>"switch", "name"=>"id_4", "title"=>"Switch", "value"=>1],
];
echo Form::create([
		"form"=>$form,
		"function"=>"Storage",
		"prefix"=>$prefix,
		"name"=>"storage[".$name."]",
		"class"=>"form-padding",
		"hover"=>true
	]);

*/
class Form{
	static $called=false;
	
	//Kiểm tra nhập liệu
	public static function invalid($data, $required=null){
		$error=null;
		foreach($data as $key=>$value){
			//Kiểm tra dữ liệu trống
			if( !is_null($required) && empty($value) ){
				$requiredMsg="Vui lòng nhập đầy đủ thông tin";
				if( is_array($required) ){
					if(in_array($key, $required)){
						$error=$requiredMsg;
					}
				}else{
					$error=$requiredMsg;
				}
			}
			switch($key){
				//Email
				case "email":
					$data["email"]=strtolower($value);
					if( !filter_var($data["email"], FILTER_VALIDATE_EMAIL) ){
						$error="Email không hợp lệ";
					}
				break;

				//Số điện thoại
				case "phone":
					$data["phone"]=strip_tags($value);
					if( !preg_match('/^\+?\d+$/', $data["phone"]) || strlen($data["phone"])>20 || strlen($data["phone"])<5 ){
						$error="Số điện thoại không hợp lệ";
					}
				break;

				//Họ tên
				case "name":
					$data["name"]=trim(strip_tags($value));
					if(strlen($data["name"])>30 || strlen($data["name"])<1 || preg_match('/([^\pL\.\ ]+)/u', $data["name"]) || substr_count($data["name"], " ")>4 ){
						$error="Tên không hợp lệ";
					}
				break;

				//Mật khẩu
				case "password":
					$data["password"]=strip_tags($value);
					if( strlen($data["password"])<6 ){
						$error="Mật khẩu phải từ 6 ký tự trở lên";
					}
				break;

				//Địa chỉ
				case "address":
					$data["address"]=strip_tags($value);
					if( strlen($data["address"])>200 || strlen($data["address"])<2 ){
						$error="Địa chỉ không hợp lệ";
					}
				break;
			}
		}

		//Kiểm tra nhập lại mật khẩu
		if( isset($data["password"]) && isset($data["password2"]) ){
			if($data["password"]!=$data["password2"]){
				$error="Mật khẩu nhập lại không khớp nhau";
			}
		}
		//Mã xác minh
		if( isset($data["captcha"]) && !captchaCorrect($data["captcha"]) ){
			$error="Mã xác minh không chính xác";
		}
		unset($data["captcha"]);

		return ["error"=>$error, "data"=>$data];
	}


	//Tạo form
	public static function create($params){
		if(!self::$called){
			self::$called=true;
			Assets::footer("/assets/form/script.js", "/assets/form/style.css");
			Assets::footer('
			<div id="formScriptWrap">
				'.Gallery::setup([
					"insert"=>true,//Nút trèn file
					"close"=>true,//Cho phép đóng 
					"hidden"=>true,//Mặc định sẽ ẩn
				]).'
			</div>').'
			';
		}
		$op="";
		extract($params);
		foreach($form as $item){
			$f=(object)$item;
			$f->name="{$prefix}".($f->name??"");

			if(isset($f->html)){
				$op.=$f->html;
			}else{
				$val=empty($function) ? ($f->value??"") : call_user_func_array($function, [$f->name, ($f->value??"")] );
				$formType=$f->type??'';
				//<Section>
				if($formType!=="hidden"){
					$op.='<div class="form-item '.$class.'" data-id="'.$f->name.'">';
				}
				$newName=( strpos($f->name, "[")===false && !empty($name) ? ''.$name.'['.$f->name.']' : ($f->name??'') );
				$originName=$newName;
				if( in_array( ($f->type??""), ["multipleSelect", "checkbox"]) ){
					$newName=''.$newName.'[]';
				}
				if(isset($f->dataName)){
					$nameAttr='data-name="'.$newName.'"';
				}else{
					$nameAttr='name="'.$newName.'"';
				}
				$horizontal=$f->horizontal??false;
				switch($formType){

					//Nút bật - tắt
					case "switch":
					$op.='
					<input type="hidden" '.$nameAttr.' value="0" />
					<label class="switch">
						<input class="checkbox" type="checkbox" '.$nameAttr.' value="1" '.($val=='1' ? 'checked' : '').' '.($f->attr??'').' />
						<s></s>
						<span class="form-item-title">'.$f->title.'</span>
					</label>';
					break;

					//Select
					case "select":
						$op.='
						<div class="'.($horizontal ? 'input-horizontal-outer' : '').'">
						<div '.($horizontal ? 'class="width-'.$horizontal.'"' : 'style="margin-bottom: 4px"').'>'.$f->title.'</div>
						<select class="'.($horizontal ? 'width-'.(100-$horizontal).' ' : '').'input'.(isset($f->full) ? ' width-100' : '').'" '.$nameAttr.' '.($f->attr??'').'>';
							foreach($f->option as $key=>$value){
								$op.='<option value="'.$key.'" '.($val==''.$key.'' ? 'selected' : '').'>'.$value.'</option>';
							}
						$op.='</select>';
						$op.='</div><div class="clearfix"></div>';
					break;

					//Multiple select
					case "multipleSelect":
						$val=(array)$val;
						$op.='
						<div style="margin-bottom: 4px">'.$f->title.'</div>
						<input type="hidden" name="'.$originName.'" value="" />
						<select style="min-width: 150px;min-height:100px" '.$nameAttr.' multiple '.($f->attr??'').'>';
							foreach($f->option as $key=>$value){
								$op.='<option value="'.$key.'" '.(in_array($key, $val) ? 'selected' : '').'> '.$value.'</option>';
							}
						$op.='</select>';
					break;

					//Input text
					case "text":
						$op.='
						<div class="'.($horizontal ? 'input-horizontal-outer' : '').'">
							<div '.($horizontal ? ' class="width-'.$horizontal.'"' : 'style="margin-bottom: 4px"').'>'.$f->title.'</div>
							<input class="'.($horizontal ? 'width-'.(100-$horizontal).' ' : 'width-100').' input" placeholder="'.($f->note??'').'" type="text" '.$nameAttr.' value="'.htmlEncode($val).'" '.($f->attr??'').'/>
						</div>
						<div class="clearfix"></div>
						';
					break;

					//Input currency
					case "currency":
						$op.='
						<div class="'.($horizontal ? 'input-horizontal-outer' : '').'">
							<div '.($horizontal ? ' class="width-'.$horizontal.'"' : 'style="margin-bottom: 4px"').'>'.$f->title.'</div>
							<input onchange="inputCurrency(this)" onkeyup="inputCurrency(this)" class="'.($horizontal ? 'width-'.(100-$horizontal).' ' : 'width-100').' input" placeholder="'.($f->note??'').'" type="text" '.$nameAttr.' value="'.$val.'" '.($f->attr??'').'/>
						</div>
						<div class="clearfix"></div>
						';
					break;

					//Input password
					case "password":
						$op.='
						<div class="'.($horizontal ? 'input-horizontal-outer' : '').'">
							<div '.($horizontal ? 'class="width-'.$horizontal.'"' : 'style="margin-bottom: 4px"').'>'.$f->title.'</div>
							<input class="'.($horizontal ? 'width-'.(100-$horizontal).' ' : 'width-100').' input" placeholder="'.($f->note??'').'" type="password" '.$nameAttr.' value="'.$val.'" '.($f->attr??'').'/>
						</div>
						<div class="clearfix"></div>
						';
					break;

					//Hidden
					case "hidden":
						$op.='<input type="hidden" '.$nameAttr.' value="'.$val.'" />';
					break;

					//Textarea
					case "textarea":
						$op.='
						<div class="'.($horizontal ? 'input-horizontal-outer' : '').'">
							<div '.($horizontal ? 'class="width-'.$horizontal.'"' : 'style="margin-bottom: 4px"').'>'.$f->title.'</div>
							<div class="'.($horizontal ? 'width-'.(100-$horizontal).' ' : '').'">
								<textarea class="form-textarea" placeholder="'.htmlEncode($f->note??'').'" '.$nameAttr.' '.($f->attr??'').'>'.$val.'</textarea>
							</div>
						</div>
						';
					break;

					//Editor
					case "editor":
						Assets::footer("/assets/timeline/style__complete.css");
						Assets::footer("/assets/html-editor/style.css", "/assets/html-editor/script.js");
						$op.='
						<div style="margin-bottom: 4px">'.$f->title.'</div>
						<textarea class="editor-textarea hidden" '.$nameAttr.' '.($f->attr??'').'>'.htmlEncode($val).'</textarea>
						';
					break;

					//Number
					case "number":
						$op.='<span class="'.( empty($f->value) ? '' : 'tooltip').'" title="'.($f->value??"").'">
								<input class="input number-input" min="'.$f->min.'" max="'.$f->max.'" style="width:'.(isset($f->full) ? '100%' : '140px').';'.(isset($f->full) ? 'margin-top: 4px' : '').'" type="number" placeholder="'.( empty($f->note) ? '('.$f->min.'-'.$f->max.')' : $f->note ).'" '.$nameAttr.' value="'.$val.'" '.($f->attr??'').'/>
							</span>
						 <span class="form-item-title">'.$f->title.'</span>';
					break;

					//Color
					case "color":
						Assets::footer("/assets/form/color-picker.js");
						$default=($f->default??$val);
						if(empty($val)){
							$val=$default;
						}
						$op.='<div class="input-group">
								<input class="jscolor input" data-default="'.$default.'"  size="5" '.$nameAttr.' value="'.$val.'" data-jscolor="{hash:true, '.($f->required ? '' : 'required: 0').'}" '.($f->attr??'').' /><button type="button" class="btn form-color-default btn-primary" data-id="'.$f->name.'"><i class="fa fa-'.(empty($default) ? 'times': 'undo').'"></i></button>
									<span class="form-item-title">'.$f->title.'</span>
								</div>
							';
					break;

					//Date
					case "date":
						Assets::footer("/assets/form/date-picker.css", "/assets/form/date-picker.js");
						$op.='
						<div class="form-date-wrap" data-format="'.$f->format.'">
							<div style="margin-bottom: 4px">'.$f->title.'</div>
							<div class="form-date-picker form-date-picker-'.$f->position.' hidden"></div>
							<div class="input-icon">
								<i class="fa fa-calendar"></i>
								<input class="input width-100 input-disabled" placeholder="'.($f->note??'').'" type="text" '.$nameAttr.' value="'.$val.'" '.($f->attr??'').' readonly=""/>
							</div>
							<code>'.json_encode($f->config).'</code>
						</div>
						';
					break;

					//Select file
					case "file":
						$count=substr_count($val, "|")+1;
						$f->max=$f->max??1;
						$op.='
						<div style="margin-bottom: 4px">'.$f->title.'</div>
						';
						$note='<i class="fa fa-files-o"></i> '.($f->note??'');
						if(strlen($val)>1){
							$note='';
							$i=0;
							foreach(explode("|", $val) as $key=>$value){
								$i++;
								$note.='<li>'.basename($value).'</li>';
							}
						}
						$op.='<div class="tooltip tooltip-top block">';
						if($f->max>1){
							$op.='
							<ol data-parent="'.$f->post.'" data-column="'.($f->column??'posts_id').'" class="form-file-select-btn input-disabled form-files-list link" style="height: 100px; overflow: auto; padding: 15px 25px 15px 25px">'.$note.'</ol>
								<textarea class="hidden input" data-max="'.$f->max.'" '.$nameAttr.'  data-ext="'.($f->ext??'').'">'.$val.'</textarea>
								<i class="link form-clear-file-btn right-icon fa fa-times"'.(strlen($val)>1 ? '' : ' style="display: none"').'></i>
							';
						}else{
							$op.='
							<input data-parent="'.$f->post.'" data-column="'.($f->column??'posts_id').'" style="cursor: pointer !important" class="input input-disabled form-file-select-btn width-100" data-max="'.$f->max.'" placeholder="'.($f->note??'').'" type="text" '.$nameAttr.' value="'.$val.'" data-ext="'.($f->ext??'').'" readonly />
							<i class="link form-clear-file-btn right-icon fa fa-times"'.(strlen($val)>1 ? '' : ' style="display: none"').'></i>
							';
						}
						$op.='
						<span class="tooltip-body">Ấn để chọn file</span>
						</div>
						';
					break;

					//Select image
					case "image":
						$op.='
						<div style="margin-bottom: 4px">'.$f->title.'</div>
						<div class="form-item-image">
							<input class="input" type="hidden" '.$nameAttr.' value="'.$val.'" data-max="1" data-ext="'.($f->ext??'jpg,jpeg,png,gif,bmp').'"/>
							<img src="'.$val.'"/>
							<i data-parent="'.$f->post.'" data-column="'.($f->column??'posts_id').'" class="form-file-select-btn btn-primary fa fa-folder-open-o"></i>
							<i class="link form-clear-file-btn fa fa-times"'.(strlen($val)>1 ? '' : ' style="display: none"').'></i>
						</div>';
					break;

					//Checkbox
					case "checkbox":
						$op.='
						<div style="margin-bottom: 4px">'.$f->title.'</div>
						<input type="hidden" name="'.$originName.'" value="" />
						<ul style="list-style-type: none">
						';
						$val=(array)$val;
						foreach($f->checkbox as $key=>$value){
							$op.='
							<li>
								<label class="check">
									<input value="'.$key.'" class="checkbox" type="checkbox" '.$nameAttr.' '.(in_array($key, $val) ? 'checked' : '').' '.($f->attr??'').'/> '.$value.'
									<s></s>
								</label>
							</li>';
						}			
						$op.='</ul>';
					break;

					//Radio
					case "radio":
						$op.='
						<div style="margin-bottom: 4px">'.$f->title.'</div>
						<ul style="list-style-type: none">';
							foreach($f->radio as $key => $value){
								$op.='
								<li>
									<label class="check radio">
										<input type="radio" '.$nameAttr.' value="'.$key.'" '.($f->attr??'').' '.($val==$key ? 'checked' : '').'/> '.$value.'
										<s></s>
									</label>
								</li>';
							}				
						$op.='</ul>';
					break;

					//Select icon
					case "icon":
						$op.='
						<div class="input-icon">
							<i class="fa '.(empty($val) ? 'fa-exchange' : $val).'"></i>
							<input class="input input-disabled form-browser-icon" placeholder="'.$f->title.'" readonly="" />
							<input class="hidden" size="8" type="text" '.$nameAttr.' value="'.$val.'" '.($f->attr??'').'/>
						</div>
						';
					break;

					//Sắp xếp
					case "sort":
						Assets::footer("/assets/sortable/script.js", "/assets/sortable/style.css");
						$sort='<ul class="sortable">';
						$val=empty($val) ? [] : $val;
						foreach($val as $key => $value){ //Only tab
							$sort.='
							<li>
								<div class="sortable-header">
									<span class="sortableLabel_'.$f->name.'">'.$value.'</span>
									<input type="text" class="input sortableInput_'.$f->name.'" name="'.$name.'['.$f->name.']['.$key.']" value="'.$value.'" style="display: none">
									<span style="float:right"><i class="fa fa-arrows-v"></i></span>
								</div>
							</li>
							';
						}
						$sort.='</ul>';
						$op.='
						'.(isset($f->edit) ? '<span class="link float-right pd-5 sortable-edit" data-id="'.$f->name.'"><i title="Chỉnh sửa" class="fa fa-pencil-square-o"></i></span>' : '').'
						<div class="pd-5">'.$f->title.'</div>
						<div class="sortable-section">'.$sort.'
						<div class="right"><a data-id="'.$f->name.'" class="link sortable-reset"><i class="fa fa-refresh"></i> Cập nhật lại</a></div>
						</div>
						';
					break;

					//Sắp xếp nhiều
					case "multipleSort":
						Assets::footer("/assets/sortable/script.js", "/assets/sortable/style.css");
						$sort="";
						$val=empty($val) ? [] : $val;
						foreach($val as $key=>$item){
							$sort.='
							<div class="sortable-item">
							<div>
								<input type="text" class="input sortableInput_'.$f->name.'" name="'.$name.'['.$f->name.']['.$key.'][title]" value="'.$item["title"].'" style="display: none">
								<span class="sortableLabel_'.$f->name.'">'.$item["title"].'</span> <i class="link sortableUp fa fa-chevron-circle-up"></i> <i class="link sortableDown fa fa-chevron-circle-down"></i>
							</div>
							
							
							<ul class="sortable">';
								foreach($item["item"] as $k=>$value){
									$sort.='
									<li>
										<div class="sortable-header">
											<span class="sortableLabel_'.$f->name.'">'.$value.'</span>
											<input type="text" class="input sortableInput_'.$f->name.'" name="'.$name.'['.$f->name.']['.$key.'][item]['.$k.']" value="'.$value.'" style="display: none">
											<span class="float-right"><i class="fa fa-arrows-v"></i></span>
										</div>
									</li>
									';
								}
							$sort.='</ul></div>';
						}

						$op.='
						'.(isset($f->edit) ? '<span class="link float-right pd-5 sortable-edit" data-id="'.$f->name.'"><i title="Chỉnh sửa" class="fa fa-pencil-square-o"></i></span>' : '').'
						<div class="pd-5">'.$f->title.'</div>
						<div id="sortable_'.$f->name.'" class="sortable-section">'.$sort.'
						<div class="right"><a data-id="'.$f->name.'" class="link sortable-reset"><i class="fa fa-refresh"></i> Cập nhật lại</a></div>
						</div>
						';
					break;	
				}
				if($formType!=="hidden"){
					$op.='</div>';//</Section>
				}
			}
		}
		return '<div class="form-section '.($hover ? 'form-hover' : '').'">'.$op.'</div>';
	}

	//Quản lý item
	public static function itemManager($params){
		extract($params);
		Assets::footer("/assets/form/item-manager.js", "/assets/sortable/script.js", "/assets/sortable/style.css");
		$out='
		<section class="item-manager">
			<input type="hidden" value="" name="'.$name.'" />
			<ol class="'.($sortable ? 'sortable' : '').'">
		';
		if(!is_array($data)){
			$data=[];
		}else if(count($data)>$max){
			$data=array_slice($data,0,$max);
		}
		$data["_i_"]=0;
		foreach($data as $index=>$value){
				$index=$index;
				if( $index!="_i_" && !empty($value["name"]) && isset($setKeyFromName) ){
					$index=vnStrFilter($value["name"]);
				}
				$fullName="{$name}[$index]";
				unset($filterForm);
				foreach($form as $item){
					$iName=$item["name"];
					$item["value"]=$value[$iName] ?? $item["value"] ?? "";
					$item["name"]=$fullName."[$iName]";
					if(isset($item["html"])){
						$item["html"]=str_replace('[%s1]', $item["value"], $item["html"]);
					}
					if($index=="_i_"){
						$item["dataName"]=true;
					}
					$filterForm[]=$item;
				}
				$itemTemplate=self::create([
					"form"=>$filterForm,
					"function"=>"",
					"prefix"=>"",
					"name"=>$name,
					"class"=>"menu",
					"hover"=>false
				]);
				if($index!="_i_"){
					//Hiện dữ liệu
					$out.='
					<li data-title="'.($value[$titleBy ?? null] ?? $value["name"] ?? $value["title"] ?? "").'">
						<div class="hidden">
							'.$itemTemplate.'
						</div>
					</li>
					';
				}
			}
		$out.='
		</ol>
		<template data-max="'.$max.'" class="hidden">
			'.$itemTemplate.'
		</template>
		</section>
		';
		return $out;
	}
}