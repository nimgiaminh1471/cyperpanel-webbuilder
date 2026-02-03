/*
 * Click hiện tất cả video
 */
function videoList__showAll(thisEL){
	var thisParents = $(thisEL).parents(".videos-list");
	thisParents.find(".videos-list-item .hidden").show();
	$(thisEL).parent().hide();
}