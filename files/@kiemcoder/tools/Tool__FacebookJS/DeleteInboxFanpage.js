//Xóa tin nhắn fanpage
var count=0;
setInterval(function(){
	var del=document.querySelector("._3jcz._1pjy");
	if(typeof del!="undefined"){
		del.click();
	}
	setTimeout(function(){
		var confirm=document.querySelectorAll("._271k._271m._1qjd ._43rm");
		if(typeof confirm!="undefined"){
			confirm[1].click();
			count++;
		}
	},500);
	console.clear();
	console.log("Đã xóa: "+count);
},2e3);