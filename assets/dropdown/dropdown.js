function dropdown_toggle(self){
    var parent_el = $(self).parent();
    $('.dropdown-content').not( parent_el.children('.dropdown-content') ).hide();
    parent_el.children('.dropdown-content').toggle();
    $('.table-responsive').css({overflowX: 'auto'});
    if( parent_el.children('.dropdown-content').is(':visible') ){
        $('.table-responsive').css({overflowX: 'visible'});
    }
}
$(document).click(function (e) {
    e.stopPropagation();
    if (!$(event.target).closest('.dropdown-btn').length) {
        $('.dropdown-content').hide();
    }
});