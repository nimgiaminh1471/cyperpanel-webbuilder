//Script unfriend by LoKiem
setInterval(function(){
	var btn=document.querySelector(".FriendRequestFriends");
	btn.click();
	setTimeout(function(){
		var unfriendParent=document.querySelector(".FriendListUnfriend");
		var unfriend=unfriendParent.querySelector("a");
		unfriend.click();
		btn.remove();
	},200);
},1000);