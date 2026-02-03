/*
# Phân trang
*/
function PaginateActionInstall(){
	$(".paginate-ajax").off("click", ".paginate>a");
	$(".paginate-ajax").on("click", ".paginate>a", function(){
		var thisEl=$(this).parents(".paginate-ajax");
		if(typeof thisEl.attr("id")=="undefined"){
			alert("Thẻ paginate-ajax phải có ID");
		}
		var thisID="#"+thisEl.attr("id");
		var page=$(this).attr("data-page");
		function refresh(){
			$("#loading").show();
			setTimeout(function(){
				$.ajax({
					url: "",
					data: {page: page},
					type: "GET",
					complete: function(){
						$("#loading").hide();
					},
					success: function(data){
						$(thisID).html( $(data).find(thisID).html() );
						$("html").animate({ scrollTop: $(thisID).offset().top-100 }, 0);
					},
					error: function(){
						setTimeout(function(){
							refresh(page);
						}, 2e3);
					}
				});
			}, 200);
		}
		refresh();
		return false;
	});
}
PaginateActionInstall();