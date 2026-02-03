/*
Chạy thử code
*/
function codePreview(content){
	$("body").css({"overflow": "hidden"});
	codePreviewChanged=false;
	var create='<div class="middle code-preview"><div class="middle-body bg width-90" style="height: 80%"> <div class="heading-block">Chạy thử code <i class="link right-icon fa fa-times"></i></div>     <div class="code-editor"><div class="column width-50 column-large code-editor-textarea"><textarea></textarea></div><div class="column width-50 column-large"><div class="code-editor-result"></div></div><div class="clearfix"></div></div>          </div></div>';
	$("body").append(create);
	$(".code-preview textarea").val(content);
	$(".code-preview .code-editor-result").html(content);
	$(".code-preview .heading-block>i").click(function(){
		var close=false;
		if(codePreviewChanged){
			if(confirm("Bạn có chắc muốn đóng?")){
				var close=true;
			}
		}else{
			var close=true;
		}
		if(close){
			$("body").css({"overflow": ""});
			$(".code-preview").remove();
		}
	});
	$(".code-preview textarea").on("keyup change", function(){
		codePreviewChanged=true;
		$(".code-editor-result").html($(this).val());
	});
}

Prism.plugins.toolbar.registerButton('preview', {
	text: 'Chạy thử', // required
	onClick: function (env) { // optional
		codePreview(env.code);
	}
});