/*
# Script trình viên soạn HTML - WYSIWYG
# Author: LoKiem
*/

$(document).ready(function(){
		//Thông số cấu hình
		var fontSize=[8,11,12,14,18,24,36,48,72];
		var textJustify=[
			{type: "justifyLeft",icon: "left"},
			{type: "justifyCenter",icon: "center"},
			{type: "justifyRight",icon: "right"},
			{type: "justifyFull",icon: "justify"}
		];
		var fontFamily=[
			{name: "Arial",value: "Arial,Helvetica,sans-serif"},
			{name: "Georgia",value: "Georgia,serif"},
			{name: "Impact",value: "Impact,Charcoal,sans-serif"},
			{name: "Times New Roman",value: "Times New Roman,Times,serif,-webkit-standard"}
		];
		var olList=[
			{title: "1-2-9", style: "decimal"},
			{title: "01-02-09", style: "decimal-leading-zero"},
			{title: "I-II-IX", style: "upper-roman"},
			{title: "A-AA-BB", style: "upper-alpha"}
		];
		var ulList=[
			{title: '<i class="fa fa-circle"></i> Tròn đặc', style: "disc"},
			{title: '<i class="fa fa-circle-o"></i> Tròn viền', style: "circle"},
			{title: '<i class="fa fa-square"></i> Vuông', style: "square"}
		];
		var smile = [
			"https://appstickers-cdn.appadvice.com/1243232544/822375734/f002898dfaf184e0e68b8c794037ee4b-3.gif",
			"https://emojis.slackmojis.com/emojis/images/1531847457/4230/blob-cry.gif",
			"https://emojis.slackmojis.com/emojis/images/1542340458/4962/anger.gif"
		];
		var character=["&bull;","&ndash;","&mdash;","&ldquo;","&rdquo;","❝","❞","❛","❜","&frac12;","&frac14;","&frac34;","&otimes;","&osol;","&odash;","&divide;","&infin;","&telrec;","&star;","&starf;","&dagger;","&Dagger;","&hellip;","&lsaquo;","&rsaquo;","&laquo;","&raquo;","&larr;","&uarr;","&rarr;","&darr;","↕","&harr;","↖","↗","↘","↙","⇄","⇇","↶","↷","⇦","⇧","⇨","⇩","&copy;","&yen;","&pound;","&trade;","&reg;","&deg;","&Delta;","&Theta;","&spades;","&clubs;","&hearts;","❤","&diams;","♩","♪","♫","♬","☀","☁","☂","☯","✔","✖","❄"];
		var add=[
			{type: "slider", icon: "fa-file-image-o", title: "Slider ảnh", class: "form-file-select-custom"},
			{type: "panel", icon: "fa-list-alt", title: "Menu ẩn hiện"},
			{type: "timeline", icon: "fa-ellipsis-v", title: "Timeline"},
			{type: "quote", icon: "fa-quote-left", title: "Trích dẫn"},
			{type: "code", icon: "fa-code", title: "Mã nguồn", class: "execCommand"},
			{type: "newPage", icon: "fa-file-text-o", title: "Thêm trang mới", class: "execCommand"},
			{type: "script", icon: "fa-file-code-o", title: "Mã JavaScript", class: "execCommand"}
		];

	$("body").append('<div id="editorPopup"></div><div id="editorAsHTML"></div>');
	var editorID=0;
	var _ScrollToLastInsert=false;
	//Khởi tạo trình biên soạn HTML
	$("textarea.editor-textarea").filter(function(){
		editorID++;
		var id="editor-"+$(this).attr("name").replace(/\[/g, "_").replace(/\]/g, "_")+""+editorID;
		$(this).removeClass("editor-textarea");
		$(this).wrap('<div class="editor" id="'+id+'-wrap"></div>');
		var wrap=$("#"+id+"-wrap");
		wrap.append('<section class="editor-textarea-clone" contenteditable="true" id="'+id+'"></section>');
		var textarea="#"+id;
		$(textarea).html( $(this).val() );
		if($(textarea).html().length<2){
			$(textarea).html("<p></p>");
		}
		$(textarea+">._empty--new-page").each(function(i,item){
			$(item).text("Trang "+(i+2));
		});
		document.execCommand("defaultParagraphSeparator", false, "p");

		//<Tạo thanh công cụ>
		var out='<section class="editor-toolbar">';
		var button = 
		[
		[
		{
			type:  "editorType",
			icon:  "fa-code",
			title: "Sửa mã HTML",
			class: "editor-button-action"
		},
		{
			type:  "removeFormat",
			icon:  "fa-eraser",
			title: "Xóa thẻ (bôi đoạn cần xóa)",
			class: "execCommand"
		},
		{
			type:  "undo",
			icon:  "fa-undo",
			title: "Hoàn tác (CTRL+Z)",
			class: "execCommand editor-button-disabled"
		},
		{
			type:  "redo",
			icon:  "fa-repeat",
			title: "Tiếp (CTRL+Y)",
			class: "execCommand editor-button-disabled"
		},
		{
			type:  "fullScreen",
			icon:  "fa-expand",
			title: "Toàn màn hình",
			class: "editor-button-action"
		},
		{
			type:  "showSub",
			icon:  "fa-file",
			title: "Đính kèm",
			sub:   "embed"
		},
		{
			type:  "showSub",
			icon:  "fa-link",
			title: "Thêm liên kết",
			sub:   "link"
		},
		{
			type:  "showSub",
			icon:  "fa-table",
			title: "Chèn bảng",
			sub:   "table"
		},
		{
			type:  "showSub",
			icon:  "fa-smile-o",
			title: "Biểu tượng",
			sub:   "smile"
		},
		{
			type:  "showSub",
			icon:  "fa-hashtag",
			title: "Ký tự đặc biệt",
			sub:   "character"
		},
		{
			type:  "showSub",
			icon:  "fa-list-ol",
			title: "Danh sách đếm số",
			sub:   "ol"
		},
		{
			type:  "showSub",
			icon:  "fa-list-ul",
			title: "Danh sách mục",
			sub:   "ul"
		},
		{
			type:  "showSub",
			icon:  "fa-question-circle",
			title: "Trợ giúp",
			sub: "help"
		}
		],

		[
		{
			type:  "bold",
			icon:  "fa-bold",
			title: "Chữ in đậm (CTRL+B)",
			class: "execCommand"
		},
		{
			type:  "italic",
			icon:  "fa-italic",
			title: "Chữ in nghiêng (CTRL+I)",
			class: "execCommand"
		},
		{
			type:  "underline",
			icon:  "fa-underline",
			title: "Chữ gạch dưới (CTRL+U)",
			class: "execCommand"
		},
		{
			type:  "strikeThrough",
			icon:  "fa-strikethrough",
			title: "Chữ gạch ngang",
			class: "execCommand"
		},
		{
			type:  "subscript",
			icon:  "fa-subscript",
			title: "Chữ nhỏ dưới",
			class: "execCommand"
		},
		{
			type:  "superscript",
			icon:  "fa-superscript",
			title: "Chữ nhỏ trên",
			class: "execCommand"
		},
		{
			type:  "showSub",
			icon:  "fa-align-justify",
			title: "Căn lề",
			sub:   "align"
		},
		{
			type:  "showSub",
			icon:  "fa-font",
			title: "Kiểu chữ",
			sub:   "fontFamily"
		},
		{
			type:  "showSub",
			icon:  "fa-text-width",
			title: "Cỡ chữ",
			sub:   "fontSize"
		},
		{
			type:  "showSub",
			icon:  "fa-paint-brush",
			title: "Màu chữ",
			id:    "foreColor",
			sub:   "color"
		},
		{
			type:  "showSub",
			icon:  "fa-delicious",
			title: "Màu nền",
			id:    "backColor",
			sub:   "color"
		},
		{
			type:  "showSub",
			icon:  "fa-plus",
			title: "Chèn",
			sub: "add"
		}
		]
		];


		for(var i=0;i<button.length;i++){
			out+='<div>';
			var btn=button[i];
			for(var iB=0;iB<btn.length;iB++){
				out+='<span class="tooltip tooltip-top editor-buttonBox editor-buttonBox-'+btn[iB].type+'">';
				out+='<span class="tooltip-body">'+btn[iB].title+'</span>';
				out+='<button class="editor-button-'+btn[iB].type+' '+btn[iB].class+'" data-value="'+btn[iB].value+'" type="button" data-type="'+btn[iB].type+'"><i class="fa '+btn[iB].icon+'"></i> </button>';	
				//Sub menu
				if(typeof btn[iB].sub!="undefined"){
					out+='<section'+(btn[iB].sub=="help" ? ' style="width: 350px; text-align: left"' : '')+'>';
					switch(btn[iB].sub){

						//Chọn màu
						case "color":
							out+='<div class="input-color"><span>Chọn màu</span> <input type="color" value="#EAEAEA"></div><button data-type="color" data-color="'+btn[iB].id+'" class="editor-btn-block"  type="button" data-type="color"><i class="pd-5 fa fa-check"></i> </button>';
						break;

						//Ol list
						case "ol":
							for(var ic=0;ic<olList.length;ic++){
								out+='<button class="editor-btn-block" data-value="'+olList[ic].style+'" type="button" data-type="ol"> <span class="pd-5">'+olList[ic].title+'</span> </button>';
							}
						break;

						//Ul list
						case "ul":
							for(var ic=0;ic<ulList.length;ic++){
								out+='<button class="editor-btn-block" data-value="'+ulList[ic].style+'" type="button" data-type="ul"> <span class="pd-5">'+ulList[ic].title+'</span> </button>';
							}
						break;

						//Căn lề
						case "align":
							for(var ic=0;ic<textJustify.length;ic++){
								out+='<button class="editor-button-'+textJustify[ic].type+' execCommand" data-value="" type="button" data-type="'+textJustify[ic].type+'"><i class="fa fa-align-'+textJustify[ic].icon+'"></i> </button>';
							}
						break;

						//Font chữ
						case "fontFamily":
							for(var ic=0;ic<fontFamily.length;ic++){
								out+='<button class="tooltip tooltip-top editor-btn-block editor-button-fontName execCommand" data-value="'+fontFamily[ic].value+'" type="button" data-type="fontName"> <span class="pd-5" style="font-family:'+fontFamily[ic].value+'">'+fontFamily[ic].name+'</span> </button>';
							}
						break;

						//Cỡ chữ
						case "fontSize":
							for(var ic=0;ic<fontSize.length;ic++){
								out+='<button style="display:block" class="tooltip tooltip-top editor-btn-block editor-button-fontSize" data-value="'+fontSize[ic]+'px" type="button" data-type="fontSize"> <span class="pd-5" style="font-size:'+fontSize[ic]+'px;">'+fontSize[ic]+'px</span> </button>';
							}
						break;

						//Ký tự đặc biệt
						case "character":
							for(var ic=0;ic<character.length;ic++){
								out+='<button data-value="'+character[ic]+'" type="button" data-type="text"> <span class="pd-5">'+character[ic]+'</span> </button>';
							}
						break;

						//Biểu tượng
						case "smile":
							for(var ic=0;ic<smile.length;ic++){
								out+='<button data-value="'+smile[ic]+'" type="button" data-type="smile"> <span class="pd-5"><img style="max-width: 50px; max-height: 50px" src="'+smile[ic]+'"/></span> </button>';
							}
						break;

						//Liên kết
						case "link":
							out+='<div class="left">';
							out+='<div class="pd-5"><input class="editor-link editor-link-url" type="text" placeholder="http://"></div>';
							out+='<div class="pd-5"><input class="editor-link editor-link-title" type="text" placeholder="Tiêu đề"></div>';
							out+='<div class="pd-5"><label class="check"><input class="editor-link editor-link-target" type="checkbox" value="1"><s></s> Mở tab mới</label></div>';
							out+='<div class="pd-5"><label class="check"><input class="editor-link editor-link-nofollow" type="checkbox" value="1" checked><s></s> Nofollow</label></div>';
							out+='<div class="pd-5 right"><button type="button" data-type="link"><i class="fa fa-check"></i></button></div>';
							out+='</div>';
						break;

						//Bảng
						case "table":
							out+='<div class="editor-table-number">Cột: <i>0</i> <br/>Hàng: <i>0</i></div><div class="editor-drag-table">';
							for(var ic=0;ic<10;ic++){
								out+='<div>';
								for(var i2=0;i2<10;i2++){
									out+='<span></span>';
								}
								out+='</div>';
							}
							out+='</div>';
						break;

						//File
						case "embed":
							out+='<div class="menu-bg bd-bottom pd-15 editor-embed-switch link" data-show="image"><i class="fa fa-image"></i></div>';
							out+='<div class="menu bd-bottom pd-15 editor-embed-switch link" data-show="video"><i class="fa fa-video-camera"></i></div>';
							out+='<div class="menu bd-bottom pd-15 editor-embed-switch link" data-show="audio"><i class="fa fa-volume-up"></i></div><div class="clearfix"></div>';
							out+='<div class="menu bd-bottom editor-upload-btn link form-file-select-custom"><i class="fa fa-upload"></i> Tải lên</div>';

							//Chèn ảnh
							out+='<div class="left editor-embed editor-embed-image">';
							out+='<div class="pd-5"><input class="editor-image editor-image-url" type="text" placeholder="Link ảnh"></div>';
							out+='<div class="pd-5"><input class="editor-image editor-image-title" type="text" placeholder="Tiêu đề ảnh"></div>';
							out+='<div class="pd-5"><textarea class="editor-image editor-image-description" placeholder="Mô tả ảnh"></textarea></div>';
							out+='<div class="pd-5 right"><button type="button" data-type="image"><i class="fa fa-check"></i></button></div>';
							out+='</div>';

							//Chèn video
							out+='<div class="left editor-embed editor-embed-video hidden">';
							out+='<div class="pd-5"><input class="editor-video editor-video-url" type="text" placeholder="Link youtube hoặc link video"></div>';
							out+='<div class="alert-warning">Link youtube có dạng:<br/> https://www.youtube.com/watch?v=aVoR8hi4aL0</div>';
							out+='<div class="pd-5 right"><button type="button" data-type="video"><i class="fa fa-check"></i></button></div>';
							out+='</div>';

							//Chèn audio
							out+='<div class="left editor-embed editor-embed-audio hidden">';
							out+='<div class="pd-5"><input class="editor-audio editor-audio-url" type="text" placeholder="Link audio"></div>';
							out+='<div class="pd-5 right"><button type="button" data-type="audio"><i class="fa fa-check"></i></button></div>';
							out+='</div>';

						break;

						//Chèn nội dung khác
						case "add":
							for(var ic=0;ic<add.length;ic++){
								out+='<button title="'+add[ic].title+'" data-tag="'+add[ic].tag+'" data-class="'+add[ic].class+'" data-text="'+add[ic].text+'" class="editor-button-'+add[ic].type+' '+add[ic].class+'" data-value="" type="button" data-type="'+add[ic].type+'"><i class="fa '+add[ic].icon+'"></i> </button>';
							}
						break;

						//Trợ giúp
						case "help":
							out+='<div class="menu">Ấn chuột phải để chỉnh từng mục</div>';
							out+='<div class="menu">Ấn giữ enter+shift để xuống dòng văn bản</div>';
							out+='<div class="menu">CTRL+shift+V để dán dạng văn bản</div>';

						break;

						}

						out+='</section>';
					}

					out+='</span>';
				}
				out+='</div>';
			}



			out+='</section>';
			wrap.prepend('<div class="editor-toolbar-wrap">'+out+'</div>');
			wrap.append('<div class="editor-info"><span></span><i>0</i></div>');
		//<Tạo thanh công cụ>

		//Click tab đính kèm
		wrap.on("click", ".editor-embed-switch", function(){
			$(".editor-embed").hide();
			$(".editor-embed-switch").addClass("menu").removeClass("menu-bg");
			$(this).addClass("menu-bg");
			wrap.find(".editor-embed-"+$(this).attr("data-show")+"").show();
		});

		//Lấy giá trị insert
		function insertValue(id){
			var value=wrap.find(".editor-"+id+"").val();
			if(typeof value=="undefined"){
				var value="";
			}
			return value;
		}

		//Chọn màu
		wrap.on("change",".editor-toolbar .input-color input", function(){
			$(this).parent().next().click();
		});
		
		//Click nút công cụ
		wrap.on("click",".editor-toolbar button", function(e){
			$(textarea).focus();
			var type  = $(this).attr("data-type");
			var subEl = $(this).parent().children("section");
			wrap.find(".editor-toolbar section").not(subEl).hide();
			wrap.find(".editor-toolbar button").not($(this)).not($(".editor-button-action")).removeClass("editor-btn-actived");
			_ScrollToLastInsert=true;
			switch(type){

				//Hiện sub menu
				case "showSub":
					offsetLeft=$(this).parent()[0].offsetLeft
					if(offsetLeft<150){
						subEl.css({"left": "0px"});
					}else if(offsetLeft<220){
						subEl.css({"left": "-40px"});
					}else{
						subEl.css({"right": "0px"});
					}
					subEl.slideToggle();
					saveSelection();
				break;

				//Kiểu chỉnh sửa tổng quản - HTML
				case "editorType":
					editorAsHTMLElement=$(textarea);
					editorAsHTML($(textarea).html(), false);
					$(this).addClass("editor-btn-actived");
				break;

				//Xóa định dạng thẻ
				case "removeFormat":
					if(document.execCommand('removeFormat')==true){
						document.execCommand('removeFormat');
					}else{
						var selection = document.getSelection();
						var range = document.createRange();
						range.selectNodeContents(selection.anchorNode.parentElement);
						selection.removeAllRanges();
						selection.addRange(range);
						document.execCommand('removeFormat');
					}
				break;

				//Toàn màn hình
				case "fullScreen":
					wrap.toggleClass("editor-fullScreen");
				break;

				//Hoàn tác & tiếp
				case "undo":
				case "redo":
					document.execCommand(type);
					if(document.execCommand(type)==false){
						wrap.find(".editor-button-"+type).addClass("editor-button-disabled");
					}else{
						wrap.find(".editor-button-undo,.editor-button-redo").removeClass("editor-button-disabled");
					}
				break;


				//Văn bản
				case "text":
					insertHTML($(this).attr("data-value"),'',false);
				break;

				//Biểu tượng
				case "smile":
					insertHTML('<img src="'+$(this).attr("data-value")+'" class="smile-icon" alt="'+baseName($(this).attr("data-value"))+'"','>',false);
				break;

				//Danh sách UL,OL
				case "ol":
				case "ul":
					insertHTML('<'+type+' style="list-style-type: '+$(this).attr("data-value")+';"><li>','&nbsp;</i></'+type+'>',false);
				break;

				//Cỡ chữ
				case "fontSize":
					insertHTML('<span style="font-size:'+$(this).attr("data-value")+'">','&nbsp;</span>',true);
				break;

				//Link
				case "link":
					var url=insertValue("link-url");
					var title=insertValue("link-title");
					if(title.length==0){
						var title=url;
					}
					if(wrap.find(".editor-link-nofollow").is(':checked')){
						var nofollow=' rel="nofollow"';
					}else{
						var nofollow='';
					}
					if(wrap.find(".editor-link-target").is(':checked')){
						var target=' target="_blank"';
					}else{
						var target='';
					}

					if(url.length>0){
						insertHTML('<a href="'+url+'"'+target+''+nofollow+'>'+title+'','</a>',false);
						wrap.find(".editor-link").val("");
					}
				break;

				//Image
				case "image":
					insertImage(insertValue("image-url"), insertValue("image-title"), insertValue("image-description"));
				break;

				//Video
				case "video":
					if(insertValue("video-url").length>1){
						if(insertValue("video-url").indexOf(".youtube.com")==-1){
							insertVideo('<video data-label="Tiêu đề" poster="" preload="auto" controls=""><source src="'+insertValue("video-url")+'" type="video/'+fileExt(insertValue("video-url"))+'" data-quality="Full HD" /></video>');						
						}else{
							//Youtube
							var youtubeID = /youtu.+(\/|=)(.+)$/.exec(insertValue("video-url"))[2];
							insertHTML('<div class="youtube-video"><iframe width="100%" height="315" src="https://www.youtube.com/embed/'+youtubeID+'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', '</div>', false);
						}
					}
				break;

				//Audio
				case "audio":
					if(insertValue("audio-url").length>1){
						insertAudio('<audio data-label="'+title+'" controls=""><source src="'+insertValue("audio-url")+'" type="audio/mpeg" data-quality="128Kbps"></audio>');
					}
				break;
				
				//Slider ảnh
				case "slider":
					$(".editor-buttonBox-showSub section").hide();
					$("#galleryWrap").show();
					$(this).attr("data-parent", $('input[name="post[id]"]').val());
					$(this).attr("data-column", $('input[name="post[gallery_type]"]').val());
					$(".gallery-manager").off("click", ".gallery-insert-file,.gallery-multiple-insert");
					$(".gallery-manager").on("click", ".gallery-insert-file,.gallery-multiple-insert", function(){
						var image="";
						//Chèn file đã chọn
						var selected=$(".gallery .gallery-selected");
						$.each(selected, function(i,el){
							var path=$(el).find(".gallery-filePath").text();
							switch($(el).attr("data-type")){
								case "image":
									image+='<li><img src="'+path+'" /></li>';
								break;
							}
						});

						if(image.length>0){
							insertHTML('<div data-noinstallscript="1" class="slider" style="width:100%;max-height: 320px"><ul class="slider-basic" data-autoplay="2">'+image, '</ul><div></div><i class="slider-btn-prev"></i><i class="slider-btn-next"></i></div>');
						}
						$(".gallery-tab span")[0].click();
						$("#galleryWrap").hide();
					});
				break;

				//Nội dung ẩn/hiện
				case "panel":
					insertHTML('<div class="panel panel-default"><div class="heading link">Tiêu đề</div><div class="panel-body hidden">Nội dung bị ẩn','</div></div>',true);
				break;

				//Timeline
				case "timeline":
					insertHTML('<ul data-noinstallscript="1" class="timeline"><li><div class="timeline-icon">Test</div><div class="timeline-content"><span class="timeline-title">Ấn chuột phải để thiết lập</li></div>','</ul>',true);
				break;

				//Trích dẫn
				case "quote":
					insertHTML('<blockquote class="quote"><span>NickName</span><i>Text</i>','</blockquote>',true);
				break;

				//Thẻ HTML
				case "tag":
					insertHTML('<'+$(this).attr("data-tag")+' class="'+$(this).attr("data-class")+'">'+$(this).attr("data-text")+'','</'+$(this).attr("data-tag")+'>',true);
				break;

				//Trang mới
				case "newPage":
					$(textarea).append('<div class="_empty--new-page">Trang mới</div><p></p>');
					$(textarea+">._empty--new-page").each(function(i,item){
						$(item).text("Trang "+(i+2));
					});
					$(textarea).animate({ scrollTop: $(textarea).prop("scrollHeight") },0);
				break;

				//Mã nguồn
				case "code":
					insertHTML('<pre class="language-markup line-numbers"><code><xmp><b>Dùng phím Enter+shift để xuống dòng</b>', '</xmp></code></pre>', true);
				break;

				//Mã Javascript
				case "script":
					insertHTML('<div class="script"><!--[#]\n\n<script>\nalert("Mã Javascript tùy chỉnh");\n</script>\n\n[#]-->', '</div>', true);
				break;

				//Chọn màu
				case "color":
					var value=$(this).parent().find("input").val();
					$(this).parents("section").prev().css({"color": value});
					document.execCommand($(this).attr("data-color"), false, value);
				break;

				//Các thẻ khác
				default:
					//Fix căn lề
					if(type.indexOf("justify")>-1){
						var selection = document.getSelection();
						var range = selection.getRangeAt(0);
						var anchor=$(selection.anchorNode);
						console.log( anchor.html() )
						if( anchor.html()==""){
							$(selection.anchorNode).html("&nbsp;");
						}
					}

					//Dùng dạng style
					switch(type){
						case "bold":
						case "italic":
						case "underline":
						case "strikeThrough":
						break;

						default:
							document.execCommand("styleWithCSS", false, "true"); 
					}
					var value = null;
					var cmd = type;
					var value = $(this).attr("data-value");
					document.execCommand(cmd,false,value);
				break;
			}
			$(this).toggleClass("editor-btn-actived");
		});

		//Thao tác trên phần chính
		wrap.on("click keyup change", function(){
			var thisEl=$(this);
			$.each(thisEl.find(".editor-toolbar .execCommand"), function(i,el) {
				var id=$(el).attr("data-type");
				if (document.queryCommandState(id)==true) {
					thisEl.find(".editor-button-"+id+"").addClass("editor-btn-actived");
				}else{
					$(".editor-button-"+id+"").removeClass("editor-btn-actived");
				}
			});
			$("#editorPopup").hide();
		});

		//Nhập
		var _pressShiftKey=false;
		wrap.on("keydown keyup", ".editor-textarea-clone", function(e){

			//Đếm ký tự
			var content=htmlOutputFilter($(textarea).html(), false);
			var leng=content.split(" ").length;
			wrap.find(".editor-info>i").text(leng);
			if(leng>2){
				$(".editor-toolbar button").removeClass("editor-button-disabled");
			}

			//Không cho xóa thẻ P mặc định
			if(e.keyCode==8 || e.keyCode == 46){
				if(content.length==0){
					e.preventDefault();
				}
			}

			//Phím tab
			if(e.keyCode===9 && e.type=="keydown"){
				document.execCommand('insertHTML', false, '&#009');
				e.preventDefault();
			}

			//Phím enter
			if(e.keyCode==13 && !_pressShiftKey && e.type=="keyup"){
				var selection = document.getSelection();
				var anchor=$(selection.anchorNode);
				if(!anchor.is("p,li")){
					var anchor=anchor.parents("p,li");
				}
				if(anchor.text().trim().length==0){
					anchor.removeAttr("style");
					anchor.children().remove();
				}
			}
			if(e.keyCode==13 && e.type=="keydown"){
				if(e.shiftKey){
					_pressShiftKey=true;
				}else{
					_pressShiftKey=false;
				}
			}
		});

		//Click textarea
		wrap.on("click", ".editor-textarea-clone",function(e){
			wrap.find(".editor-toolbar section").hide();
			wrap.find(".editor-toolbar button").not($(".editor-button-action")).removeClass("editor-btn-actived");
			addPtag(textarea);
		});


		//Dán dạng văn bản
		wrap.on("paste", ".editor-textarea-clone pre xmp",function(e){
			e.preventDefault();
			var text = (e.originalEvent || e).clipboardData.getData('text/plain');
			var text=htmlEntities(text);
			document.execCommand("insertHTML", false, text);
		});

		//Tạo bảng
		wrap.on("mouseover", ".editor-drag-table span", function(e){
			$(".editor-table-span-actived,.editor-table-div-actived").removeClass();
			var thisOffsetTop=$(this)[0].offsetTop;
			var thisOffsetLeft=$(this)[0].offsetLeft;
			var thisEl=$(this);
			$(this).addClass("editor-table-span-actived");
			wrap.find(".editor-drag-table span").each(function(i,item){
				if(item.offsetTop<=thisOffsetTop && item.offsetLeft<=thisOffsetLeft){
					$(item).addClass("editor-table-span-actived");
					$(item).parent().addClass("editor-table-div-actived");
				}
			});
			var rows=wrap.find(".editor-table-div-actived:first-child>.editor-table-span-actived").length;
			var column=wrap.find(".editor-drag-table .editor-table-div-actived").length;
			wrap.find(".editor-table-number>i").eq(0).text(column);
			wrap.find(".editor-table-number>i").eq(1).text(rows);
		});
		wrap.on("click", ".editor-drag-table", function(e){
			var rows=wrap.find(".editor-table-div-actived:first-child>.editor-table-span-actived").length;
			var column=wrap.find(".editor-drag-table .editor-table-div-actived").length;
			var table="";
			if(column<1){
				var column=1;
			}
			if(rows<1){
				var rows=1;
			}
			for(var r=0;r<column;r++){
				table+='<tr>';
				for(var c=0;c<rows;c++){
					if(r==0){
						table+='<th></th>';
					}else{
						table+='<td></td>';
					}
				}
				table+='</tr>';
			}
			insertHTML('<table class="table table-border" style="width:100%"><caption class="heading-block">Tiêu đề</caption>'+table,'</table>', false);
			$(".editor-buttonBox-showSub section").hide();
		});

		//Xuất dữ liệu sang textarea
		var _exportContent;
		$(".editor, .gallery, #editorPopup, #editorAsHTML").on("click change keyup", function(){
			function exportContent(){
				if(wrap.length>0){
					wrap.children("textarea").val( htmlOutputFilter($(wrap).children(".editor-textarea-clone").html(), true) );
				}
			}
			clearTimeout(_exportContent);
			_exportContent=setTimeout(function(){
				exportContent(wrap);
				setTimeout(function(){
					exportContent(wrap);
				}, 3e3);
			}, 500);
		});
		$(textarea).focus();
	});//</Filter>


	// Lưu vị trí của chuột
	var selRange;
	function saveSelection() {
		if (window.getSelection) {
			sel = window.getSelection();
			if (sel.getRangeAt && sel.rangeCount) {
				selRange=sel.getRangeAt(0);
			}
		} else if (document.selection && document.selection.createRange) {
			selRange=document.selection.createRange();
		}
	}
	// Khôi phục vị trí của chuột
	function restoreSelection() {
		if(selRange) {
			if (window.getSelection) {
				sel = window.getSelection();
				sel.removeAllRanges();
				sel.addRange(selRange);
			}else if(document.selection && selRange.select){
				selRange.select();
			}
		}
	}

	//Thêm thẻ P
	function addPtag(textarea){
		if(typeof $(textarea+">p:last-child").html()=="undefined"){
			$(textarea).append('<p></p>');
		}else if($(textarea+">p:last-child").html().length>0){
			$(textarea).append('<p></p>');
		}
		$(textarea).children("br:first-child").remove();
	}

	// Lấy tên file
	function baseName(str){
		var base = new String(str).substring(str.lastIndexOf('/') + 1); 
		if(base.lastIndexOf(".") != -1)       
			base = base.substring(0, base.lastIndexOf("."));
		return base;
	}

	// Lấy đuôi file
	function fileExt(filename){
		var ext = /^.+\.([^.]+)$/.exec(filename);
		return ext == null ? "" : ext[1];
	}


	//Chèn ảnh
	function insertImage(url, title, caption){
		if(url.length>0){
			if(title.length==0){
				var title=baseName(url);
			}
			if(caption.length>0){
				var caption='<i class="image-caption">'+caption+'</i>';
			}else{
				var caption='<br/>';
			}
			insertHTML('<span class="image" style="display: block"><img src="'+url+'" title="'+title+'" alt="'+title+'" />'+caption, '</span>', false);
			$(".editor .editor-image").val("");
		}
	}

	//Chèn video
	function insertVideo(video){
		if(video.length>0){
			insertHTML('<div class="media video"><div data-noinstallscript="1" class="media-player" style="max-width:100%;height:350px;" data-config="download no-ads"><div class="media-body"><div class="media-meta"><div class="media-title">Tiêu đề</div></div>'+video+'</div></div>', '</div>', false);
			$(".editor .editor-video").val("");
		}
	}

	//Chèn Audio
	function insertAudio(audio){
		if(audio.length>0){
			insertHTML('<div class="media audio"><div data-noinstallscript="1" class="media-player audio" style="max-width:100%;height:200px;" data-config="download no-ads"><div class="media-meta"><img alt="Audio" src="link-poster"><span class="audio-info"><span class="audio-title"> <i class="fa fa-music"></i> <span>Tên bài hát</span> </span><span class="audio-singer"><i class="fa fa-microphone"></i> <span>Tên ca sĩ</span> </span><span class="audio-year"><i class="fa fa-clock-o"></i> <span>201x</span></span></span></div>'+audio+'</div></div>', '</div>', false);
			$(".editor .editor-audio").val("");
		}
	}

	// Click chọn file
	$(".editor").on("click",".editor-upload-btn", function(){
		$(".editor-buttonBox-showSub section").hide();
		$("#galleryWrap").show();
		$(this).attr("data-parent", $('input[name="post[id]"]').val());
		$(this).attr("data-column", $('input[name="post[gallery_type]"]').val());
		$(".gallery-manager").off("click", ".gallery-insert-file,.gallery-multiple-insert");
		$(".gallery-manager").on("click", ".gallery-insert-file,.gallery-multiple-insert", function(){
			var video="", audio="", iv=0, ia=0;
			//Chèn file đã chọn
			var selected=$(".gallery .gallery-selected");
			$.each(selected, function(i,el){
				var path=$(el).find(".gallery-filePath").text();
				var title=$(el).attr("title");
				var caption=$(el).find(".gallery-filecaption").text();

				switch($(el).attr("data-type")){

					//ảnh
					case "image":
						insertImage(path, title, caption);
					break;

					//Video
					case "video":
						if(iv==0){
							video+='<video data-label="'+title+'" poster="" preload="auto" controls=""><source src="'+path+'" type="video/'+fileExt(path)+'" data-quality="Full HD" /></video>';
							video+='\n<!--PlayList-->\n<ul>';
						}else{
							video+='<li data-label="'+title+'"><data data-type="video/'+fileExt(path)+'" value="'+path+'">Full HD</data></li>';
						}
						if((iv+1)==selected.length){
							video+='</ul>\n<!--/PlayList-->\n';
						}
						iv++;
					break;

					//Audio
					case "audio":
						if(ia==0){
							audio+='<audio data-label="'+title+'" controls=""><source src="'+path+'" type="audio/mpeg" data-quality="128Kbps"></audio>';
							audio+='\n<!--PlayList-->\n<ul>';
						}else{
							audio+='<li data-label="'+title+'" data-singer="Tên ca sĩ"><data data-type="audio/mpeg" value="'+path+'">128Kbps</data></li>';
						}
						if((ia+1)==selected.length){
							audio+='</ul>\n<!--/PlayList-->\n';
						}
						ia++;
					break;

					//File khác
					default:
						insertHTML('<a href="'+path+'">'+title, '</a>', false);
					break;
				}
				
			});

			if(video.length>0){
				insertVideo(video);
			}
			if(audio.length>0){
				insertAudio(audio);
			}

			$(".gallery-tab span")[0].click();
			$("#galleryWrap").hide();
		});
	});


	//Insert thẻ
	function insertHTML(open,close,select){
		restoreSelection();
		var selection = document.getSelection();
		var range = selection.getRangeAt(0);
		var textareaEl=$(selection.anchorNode).closest(".editor-textarea-clone");
		var currentEl=$(selection.anchorNode).closest("p,div,blockquote,ul,ol,table")[0];
		if(close.length<1){
			//Văn bản
			var html=document.createTextNode(open);
			range.insertNode(html);
			range.setStartAfter(html);
			range.setEndAfter(html);
		}else if(select && document.getSelection().toString() != ""){
			//HTML Có vùng chọn
			var html=$(""+open+""+range.extractContents().textContent+""+close+"")[0];
			range.insertNode(html);
		}else{
			//HTML Không có vùng chọn
			var html = $(""+open+""+close+"");
			var childNode = html[0].childElementCount>0 ? html.find('*:not(:has(*))')[0] : html[0];
			var currentNode=$(selection.anchorNode).closest("p");
			if(html.is("table,ul,ol,div,blockquote,pre,form,code,iframe,script,style") && currentNode.length>0){
				currentNode.after(html);
				if(currentNode.text().trim().length==0){
					var removeCurrentNode=currentNode;
				}
			}else{
				range.insertNode(html[0]);
			}
		}
		addPtag("#"+textareaEl.attr("id"));
		if(typeof childNode!="undefined"){
			if( $(childNode).is("img, :hidden") ){
				var childNode=$("#"+textareaEl.attr("id")).children("p").last()[0];
			}
			selection.removeAllRanges();
			range = document.createRange();
			range.selectNodeContents(childNode);
			selection.addRange(range);
		}
		if(_ScrollToLastInsert){
			textareaEl.animate({ scrollTop: currentEl.offsetTop-150 },0);
			_ScrollToLastInsert=false;
		}
		if(typeof removeCurrentNode!="undefined"){
			removeCurrentNode.remove();
		}
	}

	function cssValue(el,key){
		if(el.length==0){
			return "";
		}else{
			return $(el)[0].style.getPropertyValue(key);
		}
	}
	//Click ẩn & hiện nội dung panel
	$("#editorPopup").on("click", ".panel .link", function(e){
		var thisEl=$(this).parent();
		$(".panel-actived").not($(this)).removeClass("panel-actived");
		$(".panel .hidden").not(thisEl.children(".hidden")).slideUp();
		thisEl.children(".hidden").slideToggle();
		$(this).toggleClass("panel-actived");

	});

	//Lưu mã HTML
	$("#editorAsHTML").on("click", "button", function(e){
		editorAsHTML($("#editorAsHTML").find("textarea").val(), true);
	});


	//Hiện popup chỉnh sửa
	function popup(menu, e, id, width){
		var out='';
		menu.unshift(
			{type: "block"},
			{type: "btn", title: "Loại bỏ", icon: "trash", action: "remove"},
			{type: "btn", title: "Sửa mã HTML", icon: "code", action: "html"},
			{type: "btn", title: "Chèn khối trên", icon: "angle-double-up", action: "before"},
			{type: "btn", title: "Chèn khối dưới", icon: "angle-double-down", action: "after"},
			{type: "btn", title: "Chuyển lên", icon: "arrow-up", action: "up"},
			{type: "btn", title: "Chuyển xuống", icon: "arrow-down", action: "down"},
			{type: "endblock"}
		);
		menu.forEach(function(cog,i){
			var value=(typeof cog.value==="undefined" ? "" : cog.value)
			var attr=(typeof cog.attr==="undefined" ? "" : cog.attr);
			switch(cog.type){

				case "btn":
					out+='<span data-action="'+cog.action+'" data-title="'+cog.title+'" class="tooltip tooltip-top"><i class="fa fa-'+cog.icon+'"></i><small class="tooltip-body">'+cog.title+'</small></span>';
				break;

				case "link":
					out+='<a><span data-action="'+cog.action+'" data-title="'+cog.title+'"><i class="fa fa-'+cog.icon+'"> '+cog.title+'</i></span></a>';
				break;

				case "block":
					out+='<div class="center">';
				break;

				case "endblock":
					out+='</div>';
				break;

				case "group":
					out+='<div class="panel panel-default"><div class="heading link">'+cog.title+'</div><div class="panel-body hidden">';
				break;

				case "endgroup":
					out+='</div></div>';
				break;

				case "input":
					out+='<div class="tooltip tooltip-top block"><input '+attr+' type="text" data-action="'+cog.action+'" value="'+value+'" placeholder="'+cog.title+'" required/><span class="tooltip-body">'+cog.title+'</span></div>';
				break;

				case "textarea":
					out+='<div class="tooltip tooltip-top"><textarea data-action="'+cog.action+'" placeholder="'+cog.title+'">'+value+'</textarea><span class="tooltip-body">'+cog.title+'</span></div>';
				break;

				case "checkbox":
					out+='<div class="checkbox"><label class="check"><input type="checkbox" data-action="'+cog.action+'" value="1" '+(cog.checked ? 'checked' : '')+'/><s></s> '+cog.title+'</label></div>';
				break;

				case "select":
					out+='<div><select title="'+cog.title+'" data-action="'+cog.action+'">';
					cog.option.forEach(function(op,i){
						out+='<option value="'+op.value+'" '+(value==op.value ? 'selected' : '')+'>'+op.title+'</option>';
					});
					out+='</select></div>';
				break;

				case "color":
					out+='<div class="input-color"><input class="editor-color-picker" data-changed="0" data-action="'+cog.action+'" type="color" value="'+(value.length>2 ? colorToHex(value) : '#EAEAEA')+'"> <i class="editor-remove-color fa fa-times"></i> <span>'+cog.title+'</span></div>';
				break;

				case "class":
					out+='<div><select title="'+cog.title+'" data-action="'+cog.action+'">';
					var option=[
						{value : "", title: "Mặc định"},
						{value : "menu bd", title: "Menu"},
						{value : "heading-basic", title: "Tiêu đề basic"},
						{value : "heading-block", title: "Tiêu đề block"},
						{value : "alert-success", title: "Alert(success)"},
						{value : "alert-info", title: "Alert(info)"},
						{value : "alert-warning", title: "Alert(warning)"},
						{value : "alert-danger", title: "Alert(danger)"},
					];
					option.forEach(function(op,i){
						out+='<option value="'+op.value+'" '+(value==op.value ? 'selected' : '')+'>'+op.title+'</option>';
					});
					out+='</select></div>';
				break;
				default:
				out+=cog.html;
			}
		});
		var popup=$("#editorPopup");
		popup.css({"max-width": width}).html(out);
		if( e.pageY>($(window).height()-(popup.height()+20)) ){
			var top=(e.pageY-30-popup.height());
		}else{
			var top=(e.pageY+30);
		}
		popup.css({left:e.pageX-40+"px", top:top}).show();
		editorPopupId=id;
	}

	//Chỉnh sửa link
	$(".editor").on("contextmenu", ".editor-textarea-clone a", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this);
		var menu=[
		{type: "input", title: "Tiêu đề", value: $(this).text(), action: "text"},
		{type: "input", title: "Link", value: $(this).attr("href"), action: "url"},
		{type: "color", title: "Màu chữ", value: cssValue($(this), "color"), action: "color"},
		{type: "select", title: "Button", value: $(this).attr("class"), action: "class",
			option: [
				{value : "", title: "Không"},
				{value : "btn-primary", title: "Primary"},
				{value : "btn-info", title: "Info"},
				{value : "btn-danger", title: "Danger"},
			]
		},
		{type: "checkbox", title: "Mở trong tab mới", checked: (typeof $(this).attr("target")=="undefined" ? false : true), action: "target"},
		{type: "checkbox", title: "Nofollow", checked: (typeof $(this).attr("rel")=="undefined" ? false : true), action: "rel"}
		];
		popup(menu, e, "link", "220px");
	});

	//Chỉnh sửa ảnh
	$(".editor").on("contextmenu", ".editor-textarea-clone .image", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this);
		var menu=[
		{type: "block"},
		{type: "btn", title: "left", icon: "align-left", action: "align"},
		{type: "btn", title: "center", icon: "align-center", action: "align"},
		{type: "btn", title: "right", icon: "align-right", action: "align"},
		{type: "endblock"},

		{type: "group", title: "Hiển thị"},
		{type: "select", title: "Dạng hiển thị", value: $(this).css("display"), action: "display",
			option: [{value : "block", title: "Khối riêng"}, {value : "inline-block", title: "Một hàng"}]
		},
		{type: "input", title: "Rộng tối đa (px,%)", value: $(this).find("img").css("max-width"), action: "width"},
		{type: "input", title: "Cao tối đa", value: $(this).find("img").css("max-height"), action: "height"},
		{type: "input", title: "Vo góc (px,%)", value: $(this).find("img").css("border-radius"), action: "radius"},
		{type: "endgroup"},

		{type: "group", title: "Chỉnh sửa"},
		{type: "input", title: "Tiêu đề", value: $(this).find("img").attr("title"), action: "title"},
		{type: "input", title: "Link", value: $(this).find("img").attr("src"), action: "src"},
		{type: "textarea", title: "Mô tả", value: $(this).children(".image-caption").html(), action: "caption"},
		{type: "endgroup"},

		{type: "group", title: "Liên kết"},
		{type: "input", title: "Liên kết", value: $(this).children("a").attr("href"), action: "link"},
		{type: "checkbox", title: "Mở trong tab mới", checked: (typeof $(this).children("a").attr("target")=="undefined" ? false : true), action: "target"},
		{type: "endgroup"}
		];
		popup(menu, e, "image", "250px");
	});

	//Slider ảnh
	$(".editor").on("contextmenu", ".editor-textarea-clone .slider", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this);
		var menu=[
		{type: "group", title: "Thiết lập chung"},
		{type: "input", title: "Tự động chuyển ảnh (giây)", value: $(this).children("ul").attr("data-autoplay"), action: "autoplay"},
		{type: "input", title: "Cao tối đa", value: $(this).css("max-height"), action: "height"},
		{type: "input", title: "Rộng", value: cssValue($(this), "width"), action: "width"},
		{type: "select", title: "Hiệu ứng", value: $(this).children("ul").attr("class"), action: "class",
			option: [
				{value: "slider-basic", title: "Trái-phải"},
				{value: "slider-fadein", title: "Hiện mờ dần"}
			]
		},
		{type: "checkbox", title: "Icon mũi tên chuyển", checked: ($(this).children("i").is(":hidden") ? false : true), action: "arrow"},
		{type: "checkbox", title: "Icon ô tròn", checked: ($(this).children("div").is(":hidden") ? false : true), action: "circle"},
		{type: "endgroup"},
		{type: "group", title: "Chỉnh từng ảnh"},
		{type: "btn", title: "Thêm ảnh mới", icon: "plus", action: "addSlider"},
		{type: "btn", title: "Xóa ảnh này", icon: "times", action: "rmSlider"}
		];

		var option=[], sub=[];
		$(this).find("li").each(function(i){
			option[i]={value:  i, "title": "Ảnh "+(i+1)};
			sub.push({html: '<div data-id="'+i+'" class="editor-option-body '+(i==0 ? '' : 'hidden')+'">'});
			sub.push({html: '<img src="'+$(this).find("img").attr("src")+'" />'});
			sub.push({type: "input", attr: 'data-ext="jpg,jpeg,png,gif,webm,sgv"', title: "File (chuột phải chọn file)", value: $(this).find("img").attr("src"), action: "src-"+i});
			sub.push({type: "input", title: "Liên kết", value: $(this).find("a").attr("href"), action: "link-"+i});
			sub.push({type: "checkbox", title: "Mở trong tab mới", checked: (typeof $(this).children("a").attr("target")=="undefined" ? false : true), action: "newTab-"+i});
			sub.push({html: "</div>"});
		});
		menu.push({type: "select", title: "Danh sách", value: option, action: "body", option: option});
		var menu=menu.concat(sub);
		menu.push({type: "endgroup"});
		popup(menu, e, "slider", "250px");
	});

	//Chỉnh video
	$(".editor").on("contextmenu", ".editor-textarea-clone .media.video", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this);
		var menu=[
		{type: "group", title: "Thông tin video"},
		{type: "input", title: "Tiêu đề", value: $(this).find(".media-title").text(), action: "title"},
		{type: "input", title: "Link ảnh poster", value: $(this).find("video").attr("poster"), action: "poster"},
		{type: "endgroup"},
		{type: "input", title: "Rộng", value: $(this).children(".media-player").css("max-width"), action: "width"},
		{type: "input", title: "Cao", value: $(this).children(".media-player").css("height"), action: "height"},
		{type: "checkbox", title: "Cho phép tải", checked: ($(this).children("div").attr("data-config").indexOf("download")>=0 ? true : false), action: "download"},
		{type: "checkbox", title: "Quảng cáo", checked: ($(this).children("div").attr("data-config").indexOf("no-ads")>=0 ? false : true), action: "ads"}
		];
		popup(menu, e, "video", "220px");
	});

	//Video youtube
	$(".editor").on("contextmenu", ".youtube-video", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this);
		var menu=[
		{type: "input", title: "Chiều rộng", value: $(this).children("iframe").attr("width"), action: "width"},
		{type: "input", title: "Chiều cao", value: $(this).children("iframe").attr("height"), action: "height"},
		{type: "input", title: "Link embed", value: $(this).children("iframe").attr("src"), action: "src"},
		];
		popup(menu, e, "youtube", "220px");
	});

	//Chỉnh audio
	$(".editor").on("contextmenu", ".editor-textarea-clone .media.audio", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this);
		var menu=[
			{type: "group", title: "Thông tin audio"},
			{type: "input", title: "Link ảnh poster", value: $(this).find("img").attr("src"), action: "poster"},
			{type: "input", title: "Tiêu đề", value: $(this).find(".audio-title>span").text(), action: "title"},
			{type: "input", title: "Nghệ sĩ", value: $(this).find(".audio-singer>span").text(), action: "singer"},
			{type: "input", title: "Năm", value: $(this).find(".audio-year>span").text(), action: "year"},
			{type: "endgroup"},
			{type: "input", title: "Rộng", value: $(this).children(".media-player").css("max-width"), action: "width"},
			{type: "input", title: "Cao", value: $(this).children(".media-player").css("height"), action: "height"},
			{type: "checkbox", title: "Cho phép tải", checked: ($(this).children("div").attr("data-config").indexOf("download")>=0 ? true : false), action: "download"},
			{type: "checkbox", title: "Quảng cáo", checked: ($(this).children("div").attr("data-config").indexOf("no-ads")>=0 ? false : true), action: "ads"}
		];
		popup(menu, e, "audio", "220px");
	});

	//Chỉnh bảng
	var tableEvent;
	$(".editor").on("contextmenu", ".editor-textarea-clone td,.editor-textarea-clone th", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this).parents("table");
		tableEvent=$(this);
		var cellSelected=editorPopupElement.find(".editor-td-selected");
		if(cellSelected.length>1){
			var menu=[
				{type: "block"},
				{type: "link", title: "Gộp mục đã chọn", icon: "plus", action: "tbMerge"},
				{type: "endblock"}
			];
		}else{
			var columnWidth=$(this).css("width").replace("px", "");
			tableWidth=parseInt(editorPopupElement.css("width").replace("px", "")).toFixed(1);
			var percent=(columnWidth/tableWidth*100).toFixed(5);
			var menu=[
			{type: "block"},
			{type: "btn", title: "left", icon: "align-left", action: "columnAlign"},
			{type: "btn", title: "center", icon: "align-center", action: "columnAlign"},
			{type: "btn", title: "right", icon: "align-right", action: "columnAlign"},
			{type: "endblock"},

			{type: "block"},
			{type: "link", title: "Xóa hàng", icon: "ellipsis-h", action: "removeRow"},
			{type: "link", title: "Xóa cột", icon: "ellipsis-v", action: "removeColumn"},
			{type: "endblock"},

			{type: "group", title: "Chèn thêm"},
				{type: "input", title: "Số lượng", value: 1, action: "number"},
				{type: "block"},
				{type: "btn", title: "Cột trái", icon: "angle-double-left", action: "addBefore"},
				{type: "btn", title: "Cột phải", icon: "angle-double-right", action: "addAfter"},
				{type: "btn", title: "Hàng trên", icon: "angle-double-up", action: "addAbove"},
				{type: "btn", title: "Hàng dưới", icon: "angle-double-down", action: "addBelow"},
				{type: "endblock"},
			{type: "endgroup"},

			{type: "group", title: "Đã chọn"},
				{type: "color", title: "Màu chữ", value: cssValue($(this),"color"), action: "thisColor"},
				{type: "color", title: "Màu nền", value: cssValue($(this),"background-color"), action: "thisBG"},
			{type: "endgroup"},

			{type: "group", title: "Chỉnh hàng"},
				{type: "color", title: "Màu chữ hàng", value: cssValue($(this),"color"), action: "rowColor"},
				{type: "color", title: "Màu nền hàng", value: cssValue($(this),"background-color"), action: "rowBG"},
			{type: "endgroup"},

			{type: "group", title: "Chỉnh cột"},
				{type: "color", title: "Màu chữ cột", value: cssValue($(this),"color"), action: "columnColor"},
				{type: "color", title: "Màu nền cột", value: cssValue($(this),"background-color"), action: "columnBG"},
				{type: "input", title: "Chiều rộng cột", value: percent+"%", action: "columnWidth"},
			{type: "endgroup"},

			{type: "group", title: "Chỉnh bảng"},
			{type: "input", title: "Style", value: editorPopupElement.attr("style"), action: "style"},
			{type: "select", title: "Kiểu viền", value: editorPopupElement.attr("class"), action: "class",
				option: [
					{value : "table table-border", title: "Mặc định"},
					{value : "table table-border-top", title: "Viền trên"},
					{value : "table", title: "Không viền"}
				]
			},
			{type: "checkbox", title: "Chia đều chiều rộng cột", checked: false, action: "equallyDivided"},

			{type: "endgroup"}
			];
		}
		popup(menu, e, "table", "250px");
	});

	$(".editor").on("contextmenu", ".editor-textarea-clone table>caption", function(e){
		var thisEl=$(this);
		editorShowMenu=true;
		editorPopupElement=thisEl;
		var menu=[
			{type: "block"},
			{type: "btn", title: "left", icon: "align-left", action: "align"},
			{type: "btn", title: "center", icon: "align-center", action: "align"},
			{type: "btn", title: "right", icon: "align-right", action: "align"},
			{type: "endblock"},
			{type: "class", title: "Kiểu khối", value: thisEl.attr("class"), action: "class"},
			{type: "color", title: "Màu nền", value: cssValue(thisEl,"background-color"), action: "background"}
		];
		popup(menu, e, "tbCaption", "220px");
	});

	//Nối cột, hàng
	$(".editor").on("mousedown touchstart", "td,th", function(){
		var thisTr=$(this).parent();
		var tdStart=[$(this).index()];

		$(".editor").on("mouseover touchmove", "td,th", function(e){
			var tdEnd=[$(e.target).index()];
			var tdIndex=tdStart.concat(tdEnd).sort();
			var trStart=[thisTr.index()];
			var trEnd=[$(e.target).parent().index()];
			var trIndex=trStart.concat(trEnd).sort();
			$(".editor .editor-td-selected").removeClass("editor-td-selected");
			if(trIndex[0]==trIndex[1]){
				//Chọn hàng
				thisTr.find("td,th").each(function(i,item){
					if(i>=tdIndex[0] && i<=tdIndex[1]){
						$(item).addClass("editor-td-selected");
					}
				});
			}else{
				//Chọn cột
				var thisPr=thisTr.parent();
				for(var i=trIndex[0]; i<=trIndex[1]; i++){
					thisPr.find("tr").eq(i).children("td,th").eq(tdIndex[0]).addClass("editor-td-selected");
				}
				
			}
		});
	}).on("mouseup touchend", function(e){
		if($(".editor .editor-td-selected").length<2){
			$(".editor .editor-td-selected").removeClass("editor-td-selected");
		}
		$(".editor").on("click", ".editor-td-selected", function(){
			$(".editor .editor-td-selected").removeClass("editor-td-selected");
		});
		$(".editor").off("mouseover", "td,th");
	});

	//Olist
	$(".editor").on("contextmenu", ".editor-textarea-clone ol, .editor-textarea-clone ul", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this);
		if($(this).is("ol")){
			var option=[
				{value : "none", title: "Không"},
				{value : "decimal", title: "1-2-9"},
				{value : "decimal-leading-zero", title: "01-02-09"},
				{value : "upper-roman", title: "I-II-IX"},
				{value : "upper-alpha", title: "A-AA-BB"}
			];
		}else{
			var option=[
				{value : "none", title: "Không"},
				{value : "disc", title: "Tròn đặc"},
				{value : "circle", title: "Tròn viền"},
				{value : "square", title: "Vuông"}
			];
		}
		var menu=[
		{type: "select", title: "Kiểu danh sách", value: $(this).css("list-style-type"), action: "type",
			option: option
		},
		{type: "class", title: "Kiểu khối", value: $(this).attr("class"), action: "class"},
		{type: "input", title: "Khoảng cách lề", value: $(this).css("padding-inline-start"), action: "margin"}

		];
		popup(menu, e, "list", "220px");
	});

	//Smile
	$(".editor").on("contextmenu", ".editor-textarea-clone .smile-icon", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this);
		var menu=[
		{type: "input", title: "Rộng tối đa (px,%)", value: $(this).css("max-width"), action: "width"},
		{type: "input", title: "Cao tối đa", value: $(this).css("max-height"), action: "height"}

		];
		popup(menu, e, "smile", "220px");
	});

	//Nội dung ẩn/hiện
	$(".editor").on("contextmenu", ".editor-textarea-clone .panel>.link", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this).parent();
		var menu=[
		{type: "select", title: "Kiểu khối", value: $(this).parent().attr("class"), action: "class",
			option: [
				{value : "panel panel-default", title: "Mặc định"},
				{value : "panel panel-primary", title: "Primay"},
				{value : "panel panel-info", title: "info"},
				{value : "panel panel-success", title: "Success"},
				{value : "panel panel-warning", title: "Warning"},
				{value : "panel panel-danger", title: "Danger"}
			]
		}

		];
		popup(menu, e, "panel", "220px");
	});

	//Timeline
	$(".editor").on("contextmenu", ".editor-textarea-clone .timeline", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this);
		var menu=[
		{type: "input", title: "Số lượng line", value: $(this).children("li").length, action: "number"},
		{type: "input", title: "Tiêu đề icon", value: "", action: "iconTitle"},
		{type: "input", title: "Tiêu đề chính", value: "", action: "title"},
		{type: "checkbox", title: "Luôn hiện toàn bộ", checked: (cssValue($(this).children("li:last-child").find(".timeline-body"), "display")=="block" ? true : false), action: "show"},
		{html: '<div class="center"><button type="button" class="btn-primary">Cập nhật</button></div>'}
		];
		popup(menu, e, "timeline", "220px");
	});

	//Mã nguồn
	$(".editor").on("contextmenu", ".editor-textarea-clone pre>code", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this).parent();
		var menu=[
		{type: "textarea", title: "Nội dung", value: htmlEntities($(this).children("xmp").html()), action: "content"},
		{type: "select", title: "Loại ngôn ngữ", value: editorPopupElement.attr("class").replace(" line-numbers", ""), action: "class",
			option: [
				{value : "language-markup", title: "HTML,XML..."},
				{value : "language-javascript", title: "JavaScript"},
				{value : "language-css", title: "CSS"},
				{value : "language-php", title: "PHP"},
				{value : "language-sql", title: "SQL"},
			]
		},
		{type: "checkbox", title: "Đếm dòng", checked: (editorPopupElement.hasClass("line-numbers") ? true : false), action: "number"},
		{type: "checkbox", title: "Cho phép chạy thử", checked: (typeof editorPopupElement.attr("data-code")=="undefined" ? false : true), action: "preview"}
		];
		popup(menu, e, "code", "220px");
	});

	//Mã Javascript
	$(".editor").on("contextmenu", ".editor-textarea-clone .script", function(e){
		editorShowMenu=true;
		editorPopupElement=$(this);
		var menu=[
			
		];
		popup(menu, e, "code", "220px");
	});

	//Không hiện menu
	$(".editor").on("contextmenu", ".editor-textarea-clone>div>*,.editor-textarea-clone>._empty--new-page", function(e){
		editorShowMenu=true;
	});

	//Chỉnh sửa thẻ
	$(".editor").on("contextmenu", ".editor-textarea-clone>p,.editor-textarea-clone>div", function(e){
		var thisEl=$(this);
		setTimeout(function(){
			if(!editorShowMenu){
				editorShowMenu=true;
				editorPopupElement=thisEl;
				var menu=[
				{type: "class", title: "Kiểu khối", value: thisEl.attr("class"), action: "class"},
				{type: "color", title: "Màu nền", value: cssValue(thisEl,"background-color"), action: "background"},
				{type: "input", title: "Khoảng cách 2 bên", value: thisEl.css("margin-left"), action: "margin"},
				];
				popup(menu, e, "element", "220px");
			}
		},200);
	});

	//Ẩn menu chỉnh sửa
	var editorShowMenu=false;
	$(".editor").on("contextmenu", ".editor-textarea-clone", function(e){
		e.preventDefault();
		setTimeout(function(){
			if(!editorShowMenu){
				$("#editorPopup").hide();
			}
			editorShowMenu=false;
		},200);
	});

	//Click thao tác
	$("#editorPopup").on("click", "span", function(e){
		var thisEl=$(this);
		//Tạo hàng - cột mới
		var addtd="", addth="", newRows="";
		for(var i=0;i<$("#editorPopup").find("[data-action='number']").val();i++){
			addtd+='<td></td>';
			addth+='<th></th>';
			newRows+="<tr>";
			for(var ir=0; ir<$(tableEvent).parent().find("td,th").length; ir++){
				newRows+='<td></td>';
			}
			newRows+="</tr>";
		}
		var cellSelected=editorPopupElement.find(".editor-td-selected");
		switch($(this).attr("data-action")){
			//Xóa bỏ
			case "remove":
				editorPopupElement.remove();
			break;

			//Sửa mã HTML
			case "html":
				editorAsHTMLElement=editorPopupElement;
				editorAsHTML(editorPopupElement[0].outerHTML, false);
			break;

			//Căn ảnh
			case "align":
				editorPopupElement.css({"text-align": $(this).attr("data-title")});
			break;

			//Chèn khối bên trên
			case "before":
				editorPopupElement.before("<p></p>");
			break;

			//Chèn khối bên dưới
			case "after":
				editorPopupElement.after("<p></p>");
			break;

			//Chuyển lên
			case "up":
				if(editorPopupElement.prev().length>0){
					editorPopupElement.prev().before(editorPopupElement[0].outerHTML);
					editorPopupElement.remove();
				}
			break;

			//Chuyển xuống
			case "down":
				if(editorPopupElement.next().length>0){
					editorPopupElement.next().after(editorPopupElement[0].outerHTML);
					editorPopupElement.remove();
				}
			break;

			//Xóa hàng
			case "removeRow":
				$(tableEvent).parent().remove();
				if(editorPopupElement.find("td,th").length==0){
					editorPopupElement.remove();
				}
			break;

			//Xóa cột
			case "removeColumn":
				var index=$(tableEvent).index();
				editorPopupElement.find("tr").each(function(){
					$(this).children("td,th").eq(index).remove();
					
				});
				if(editorPopupElement.find("td,th").length==0){
					editorPopupElement.remove();
				}
			break;

			//Căn cột
			case "columnAlign":
				var index=$(tableEvent).index();
				editorPopupElement.find("tr").each(function(){
					$(this).children("td,th").eq(index).css({"text-align": thisEl.attr("data-title")});
				});
			break;

			//Cột trước
			case "addBefore":
				var index=$(tableEvent).index();
				editorPopupElement.find("tr").each(function(){
					$(this).children("td").eq(index).before(addtd);
					$(this).children("th").eq(index).before(addth);
				});
			break;

			//Cột sau
			case "addAfter":
				var index=$(tableEvent).index();
				editorPopupElement.find("tr").each(function(){
					$(this).children("td").eq(index).after(addtd);
					$(this).children("th").eq(index).after(addth);
				});
			break;

			//Hàng trên
			case "addAbove":
				$(tableEvent).parent().before(newRows);
			break;

			//Hàng dưới
			case "addBelow":
				$(tableEvent).parent().after(newRows);
			break;

			//Gộp cột
			case "tbMerge":
				var pan=0, newText="", tagName="td";
				var panType=(cellSelected.eq(0).next().hasClass("editor-td-selected") ? 'col' : 'row');
				cellSelected.each(function(i){
					newText+=$(this).text()+" ";
					if(typeof $(this).attr("colspan")!="undefined"){
						pan+=$(this).attr("colspan");
					}else{
						pan++;
					}
					if(i>0){
						$(this).remove();
					}else{
						tagName=this.tagName.toLowerCase();
					}
				});
				var newCell='<'+tagName+' '+panType+'span="'+pan+'">'+newText+'</'+tagName+'>';
				cellSelected.replaceWith(newCell);
			break;

			//Thêm ảnh vào slider
			case "addSlider":
				editorPopupElement.children("ul").append('<li><img src="" /></li>');
			break;

			//Xóa ảnh slider
			case "rmSlider":
				editorPopupElement.children("ul").children("li").eq($("#editorPopup .editor-option-body:visible").attr("data-id")).remove();
			break;

		}
		$("#editorPopup").hide();
	});

	//Lưu giá trị
	$("#editorPopup").on("change keyup click", "input, select, textarea, button, .editor-remove-color", function(e){
		var el=editorPopupElement;
		var thisEl=$(this);
		var removeColor="";
		if($(this).hasClass("editor-remove-color")){
			var colorEl=thisEl.parent().children("input");
			colorEl.attr("data-changed",0);
			var removeColor=colorEl.attr("data-action");
		}else if($(this).hasClass("editor-color-picker")){
			thisEl.attr("data-changed",1);
		}

		function getValue(id){
			return $("#editorPopup").find("[data-action='"+id+"']").val();
		}
		function isChecked(id){
			return ($("#editorPopup").find("[data-action='"+id+"']").is(":checked") ? true : false);
		}
		function colorChanged(id){
			return $("#editorPopup").find("[data-action='"+id+"']").attr("data-changed")==1 ? true : false;
		}
		function setColor(el, type, key){
			$.each(el, function(i,item){
				if(removeColor==key){
					item.style.removeProperty(type);
				}else if(colorChanged(key)){
					item.style.setProperty(type, getValue(key));
				}
			});
		}
		switch(editorPopupId){
		//Link
		case "link":
			el.text(getValue("text"));
			el.attr("href", getValue("url"));
			el.attr("class", getValue("class"));
			setColor(el, "color", "color");
			( isChecked("target") ? el.attr("target","blank") : el.removeAttr("target") );
			( isChecked("rel") ? el.attr("rel","nofollow") : el.removeAttr("rel") );
		break;


		//Image
		case "image":
			el.css({"display": getValue("display")});
			el.children("img").attr("src", getValue("src"));
			el.children("img").attr("title", getValue("title")).attr("alt", getValue("title"));
			el.children(".image-caption").remove();
			el.children("img").css({"max-width": getValue("width"), "max-height": getValue("height"), "border-radius": getValue("radius")});
			if(getValue("caption").length>0){
				el.append('<i class="image-caption">'+getValue("caption")+'</i>');
			}
			if(getValue("link").length>0){
				if(el.children("a").length==0){
					el.children("img").wrap('<a'+(isChecked("target") ? ' target="_blank"' : '')+' href="'+getValue("link")+'"></a>');
				}else{
					el.children("a").attr("href", getValue("link"));
					(isChecked("target") ? el.children("a").attr("target", "_blank") : el.children("a").removeAttr("target") );
				}
			}
		break;

		//Slider ảnh
		case "slider":
			el.css({"max-height": getValue("height"), "width": getValue("width")});
			el.children("ul").attr("data-autoplay", getValue("autoplay"));
			el.children("ul").attr("class", getValue("class"));
			(isChecked("arrow") ? el.children("i").removeAttr("style") : el.children("i").hide() );
			(isChecked("circle") ? el.children("div").removeAttr("style") : el.children("div").hide() );
			el.find("li").each(function(i){
				var thisImg=$(this).find("img")
				thisImg.attr("src", getValue("src-"+i));
				if(getValue("link-"+i).length>0){
					var attr='href="'+getValue("link-"+i)+'"'+(isChecked("newTab-"+i) ? ' target="_blank"' : '')+'';
					if($(this).children("a").length>0){
						$(this).children("a").attr("href", getValue("link-"+i));
						(isChecked("newTab-"+i) ? $(this).children("a").attr("target", "_blank") : $(this).children("a").removeAttr("target") );
					}else{
						thisImg.wrap('<a '+attr+'></a>');
					}
				}else{
					if($(this).children("a").length>0){
						$(this).children("a").replaceWith(thisImg);
					}
				}
			});
		break;

		//Smile
		case "smile":
			el.css({"max-width": getValue("width"), "max-height": getValue("height")});
		break;

		//Video
		case "video":
			el.find(".media-title").text(getValue("title"));
			el.find("video").attr("poster", getValue("poster"));
			el.children(".media-player").css({"max-width": getValue("width")});
			el.children(".media-player").css({"height": getValue("height")});
			var config="";
			if(isChecked("download")){
				config+='download';
			}
			if(!isChecked("ads")){
				config+=' no-ads';
			}
			el.children("div").attr("data-config", config);
		break;

		//Video youtube
		case "youtube":
			el.children("iframe").attr("width", getValue("width"));
			el.children("iframe").attr("height", getValue("height"));
			el.children("iframe").attr("src", getValue("src"));
		break;

		//Audio
		case "audio":
			el.find("img").attr("src", getValue("poster"));
			el.find(".audio-title>span").text(getValue("title"));
			el.find(".audio-singer>span").text(getValue("singer"));
			el.find(".audio-year>span").text(getValue("year"));
			el.children(".media-player").css({"max-width": getValue("width")});
			el.children(".media-player").css({"height": getValue("height")});
			var config="";
			if(isChecked("download")){
				config+='download';
			}
			if(!isChecked("ads")){
				config+=' no-ads';
			}
			el.children("div").attr("data-config", config);
		break;

		//List
		case "list":
			el.css({"list-style-type": getValue("type"),"padding-inline-start": getValue("margin")});
			el.attr("class", getValue("class"));
		break;

		//Bảng
		case "table":
			var index=$(tableEvent).index();
			el.attr("style", getValue("style"));
			el.attr("class", getValue("class"));
			if(!colorChanged("columnColor") || !colorChanged("rowColor")){
				setColor(tableEvent, "color", "thisColor");
			}
			if(!colorChanged("columnBG") || !colorChanged("rowBG")){
				setColor(tableEvent, "background-color", "thisBG");
			}
			//Màu chữ
			setColor($(tableEvent).parents("tr").find("td,th"), "color", "rowColor");
			el.find("tr").each(function(){
				setColor($(this).children("td,th").eq(index), "color", "columnColor");
			});
			//Màu nền
			setColor($(tableEvent).parents("tr").find("td,th"), "background-color", "rowBG");
			el.find("tr").each(function(){
				setColor($(this).children("td,th").eq(index), "background-color", "columnBG");
			});
			//Chiều rộng cột
			el.find("tr:first-child").each(function(){
				$(this).children("td,th").eq(index).css({"width": getValue("columnWidth")});
			});
			//Chia đều các cột
			if(isChecked("equallyDivided")){
				var td=el.find("tr:last-child>td").length;
				var equallyDivided=(100/td).toFixed(5);
				el.find("tr:first-child>td,tr:first-child>th").each(function(){
					$(this).css({"width": equallyDivided+"%"})
				});
			}
		break;

		//Tiêu đề bảng
		case "tbCaption":
			setColor(el, "background-color", "background");
			el.attr("class", getValue("class"));
		break;

		//Nội dung ẩn/hiện
		case "panel":
			el.attr("class", getValue("class"));
		break;

		//Line
		case "timeline":
			if($(e.target).is("button")){
				var item=el.children("li");
				var list="";
				for(var i=1; i<=getValue("number"); i++){
					var oldItem=item.eq(i-1);
					if(oldItem.length>0){
						var thisBody=oldItem.find(".timeline-body").text();
						var title=oldItem.find(".timeline-title").text();
						var iconTitle=oldItem.find(".timeline-icon").text();
					}else{
						var thisBody='Body'+i;
						var title=''+getValue("title")+' '+i;
						var iconTitle=''+getValue("iconTitle")+' '+i;
					}
					
					list+='<li><div class="timeline-icon">'+iconTitle+'</div><div class="timeline-content"><span class="timeline-title'+(i==1 ? ' timeline-title-active' : '')+'">'+title+'</span><div class="timeline-body" '+(isChecked("show") || i==1 ? 'style="display: block"' : '')+'>'+thisBody+'</div></div></li>';
				}
				if(list.length>1){
					el.children("li").remove();
					el.html(list);
				}
				$("#editorPopup").hide();
			}
		break;

		//Code
		case "code":
			el.attr("class", getValue("class"));
			el.find("code>xmp").html(getValue("content"));
			( isChecked("number") ? el.addClass("line-numbers") : el.removeClass("line-numbers") );
			( isChecked("preview") ? el.attr("data-code", "preview") : el.removeAttr("data-code") );
		break;

		//Thẻ mặc định
		case "element":
			if(e.type=="change" || removeColor.length>0){
				var style="";
				if(getValue("margin")!="0px" || getValue("margin").length!=0){
					style+='margin-left: '+getValue("margin")+'; margin-right: '+getValue("margin")+'';
				}
				if(colorChanged("background")){
					style+=';background-color: '+getValue("background")+'';
				}
				if(style.length>0){
					var style=' style="'+style+'"';
				}

				if(getValue("class").length>0){
					el.replaceWith('<div class="'+getValue("class")+'"'+style+'>'+el.html()+'</div>');
				}else{
					el.replaceWith('<p'+style+'>'+el.html()+'</p>');
				}
				$("#editorPopup").hide();
			}
		break;

		}
	});

	//Hiện nội dung trong option
	$("#editorPopup").on("change", "select[data-action='body']", function(e){
		$("#editorPopup .editor-option-body").hide();
		$("#editorPopup .editor-option-body[data-id='"+$(this).val()+"']").show();
	});

	// Đổi file
	$("#editorPopup").on("contextmenu", ".editor-option-body input[data-action*='src-']", function(e){
		e.preventDefault();
		$(".file--selecting").removeClass("file--selecting");
		$(this).addClass("file--selecting");
		$(this).attr("data-parent", $('input[name="post[id]"]').val());
		$(this).attr("data-column", $('input[name="post[gallery_type]"]').val());
		$("#galleryWrap").show();
		$(".gallery-manager").off("click", ".gallery-insert-file,.gallery-multiple-insert");
		$(".gallery-manager").on("click", ".gallery-insert-file,.gallery-multiple-insert", function(){
			var file_selected = $(".gallery .gallery-selected");
			if($(".gallery-selected").length>1){ var wrong="Chỉ được chọn 1 tệp tin"; }
			var ext_allow = $(".file--selecting").attr("data-ext");
			if(ext_allow.indexOf(file_selected.attr("data-ext"))<0 && ext_allow.length>0){ var wrong="Chỉ cho phép file đuôi: "+ext_allow+" "; }

			if(wrong){
				alert(wrong);
			}else{
				$(".file--selecting").val(file_selected.find(".gallery-filePath").text());
				$(".file--selecting").removeClass("file--selecting");
				$("#galleryWrap").hide();
			}
			$("#editorPopup input")[0].click();
			$("#editorPopup").hide();
		});
	});
	

	//Chỉnh sửa dạng HTML
	function editorAsHTML(content, save){
		var content=htmlOutputFilter(content, false);
		if(content.length<2){
			var content='<p></p>';
		}
		if(save){
			//Lưu
			if(editorAsHTMLElement.hasClass("editor-textarea-clone")){
				editorAsHTMLElement.html(content);
			}else{
				editorAsHTMLElement.replaceWith(content);
			}
			$("#editorAsHTML").html("");
			$("body").css({"overflow": ""});
		}else{
			//Sửa
			$("body").css({"overflow": "hidden"});
			$("#editorAsHTML").html('<div class="modal"><div class="modal-body width-90" style="height: 90%"><textarea></textarea> <button style="display: block" type="button" class="width-100 rm-radius btn-primary">Lưu lại</button> </div> </div>').show();
			$("#editorAsHTML textarea").val(formatFactory(content));
		}
	}

	//Định dạng lại code
	function formatFactory(str) {
		var doc=$('<div>'+str+'</div>');
		var excludedTag=["CODE", "PRE"];
		var calledCount=0;
		function setNewline(doc){
			calledCount++;
			doc.children().each(function(i){
				if(this.nextSibling==null || this.nextSibling.nodeName!="#text"){
					if(i==0 && calledCount>1){
						$(this).before("\n");
					}
					$(this).after("\n");
				}
				if( $(this).children().length>0 && excludedTag.indexOf($(this)[0].nodeName)==-1 ){
					setNewline($(this));
				}
			});
		}
		setNewline(doc);
		return doc.html();
	}

	//Chuyển màu sang HEX
	function colorToHex(_rgb){
		_rgb = _rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
		return (_rgb && _rgb.length === 4) ? "#" +
		("0" + parseInt(_rgb[1],10).toString(16)).slice(-2) +
		("0" + parseInt(_rgb[2],10).toString(16)).slice(-2) +
		("0" + parseInt(_rgb[3],10).toString(16)).slice(-2) : '';
	}

	//Mã hóa HTML
	function htmlEntities(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}

	//Vị trí event
	function positionType(e){
		if(e.type == 'touchstart' || e.type == 'touchmove' || e.type == 'touchend' || e.type == 'touchcancel'){
			var position = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
		}else{
			var position = e;
		}
		return position;
	}

	//Lọc nội dung HTML
	function htmlOutputFilter(content, output){
		var content=$('<div>'+content+'</div>');
		//Thay thẻ b=strong
		content.find("b").each(function(){
			$(this).replaceWith('<strong>'+$(this).html()+'</strong>');
		});
		if(output){
			content.children("._empty--new-page").text("");
		}
		var content=content.html();
		var replace=[
		['outline: tomato dashed 1px !important;', ''],
		[' style=""', ''],
		[' class=""', ''],
		['<p></p>', ''],
		['<p><br></p>', '']
		];

		//Xuất dữ liệu để lưu
		if(output){
			replace.push(
				['&nbsp;', '']
			);
		}
		//var content=content.replace(/(\t|\n)/g,"");
		for(var i=0; i<replace.length; i++) {
			content=content.replace(new RegExp(replace[i][0], 'g'),replace[i][1]);
		}
		return content;
	}
});