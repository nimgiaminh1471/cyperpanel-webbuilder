/*
# Thống kê hình vòm
*/
(function($) {
	progressPieInstall=function(){
		$(".progress-pie").each(function(){
			var size=parseInt($(this).attr("data-size"));
			var canvasColor=$(this).attr("data-color");
			var value=$(this).attr("data-value");
			var percentage = value/100;
			if(canvasColor.length==0){
				//Tự tạo màu
				if(value<75){
					var canvasColor="#00B4FF";
				}else if(value<90){
					var canvasColor="#FFA500";
				}else{
					var canvasColor="#FF0000";
				}
			}
			if( !$(this).hasClass("progress-pie-installed") ){
				$(this).children("p").css({color: canvasColor});
				$(this).css({height: size+'px', width: size+'px'});
				$(this).children("p").before('<canvas width="'+size+'px" height="'+size+'px"></canvas><canvas style="top: -'+(size+4)+'px;position: relative" width="'+size+'px" height="'+size+'px"></canvas>');
				$(this).wrap('<div style="text-align: center"></div>');
				$(this).addClass("progress-pie-installed");
			}
			var iProgress = $(this).children("canvas").eq(0)[0];
			var aProgress = $(this).children("canvas").eq(1)[0];
			var iProgressCTX = iProgress.getContext('2d');
			var canvasPos=size/2;
			var canvasInactiveOut=canvasPos-16;
			var canvasActive=canvasPos-26;
			var canvasInactiveIn=canvasPos-36;
			var canvasLineWidth=20;
			drawInactive(iProgressCTX);
			drawProgress(aProgress, (value/100));

			function drawInactive(iProgressCTX){
				iProgressCTX.lineCap = 'square';

				//Tạo vòng tròn
				iProgressCTX.beginPath();
				iProgressCTX.lineWidth = 0;
				iProgressCTX.fillStyle = '#EAEAEA';
				iProgressCTX.arc(canvasPos,canvasPos,canvasInactiveOut,0,2*Math.PI);
				iProgressCTX.fill();

				//Nền trong vòng tròn
				iProgressCTX.beginPath();
				iProgressCTX.lineWidth = 0;
				iProgressCTX.fillStyle = '#fff';
				iProgressCTX.arc(canvasPos,canvasPos,canvasInactiveIn,0,2*Math.PI);
				iProgressCTX.fill();

			}
			function drawProgress(bar, percentage){
				var barCTX = bar.getContext("2d");
				var quarterTurn = Math.PI / 2;
				var endingAngle = ((2*percentage) * Math.PI) - quarterTurn;
				var startingAngle = 0 - quarterTurn;

				bar.width = bar.width;
				barCTX.lineCap = 'square';

				barCTX.beginPath();
				barCTX.lineWidth = canvasLineWidth;
				barCTX.strokeStyle = canvasColor;
				barCTX.arc(canvasPos,canvasPos,canvasActive,startingAngle, endingAngle);
				barCTX.stroke();
			}
		});
	}
	$(document).ready(function(){
		progressPieInstall();
	});
})( jQuery );
