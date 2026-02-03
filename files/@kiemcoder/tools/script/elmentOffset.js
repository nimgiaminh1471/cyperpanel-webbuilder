var childPos = el.offset();
var parentPos = el.parent().offset();
var thisOffset = {
	top: childPos.top - parentPos.top,
	left: childPos.left - parentPos.left
}