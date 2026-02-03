function printElementInstall(){
	$(".print-element-btn").off("click");
	$(".print-element-btn").on("click", function(){
		var hiddenEl=$(this).attr("data-hidden");
		var showEl=$(this).attr("data-show");
		var link=$(this).attr("data-link");
		if(hiddenEl.length>0){
			$(hiddenEl).hide();
		}
		if(showEl.length>0){
			$(showEl).show();
		}
		var printContents=$($(this).attr("data-element"));
		if(link.length>0){
			printContents.append('<p style="margin-top: 50px;font-weight: bold"><i class="fa fa-link"></i> '+link+'</p>');
		}
		printContents.addClass("hidden-printing");
		$("body>*").hide();
		$("body").prepend(printContents);
		$("body").on("mouseenter click", function(){
			location.reload();
		});
		window.print();
	});
}
printElementInstall();