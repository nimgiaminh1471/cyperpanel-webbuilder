/*
# Chọn ngày giờ
# Author: LoKiem
*/
$(document).ready(function(){
	//Click chọn ngày tháng
	$("form").on("click",".form-date-wrap>.input-icon", function(){
		var monthsText=["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];
		//var weekName={sun: "CN", mon: "Hai", tue: "Ba", wed: "Tư", thu: "Năm", fri: "Sáu", sat: "Bảy"};//Chủ nhật là ngày đầu tuần
		var weekName={mon: "Hai", tue: "Ba", wed: "Tư", thu: "Năm", fri: "Sáu", sat: "Bảy", sun: "CN"};//Thứ hai là ngày đầu tuần
		var msg={
			disabledSelect: "Không thể chọn ngày này",
			canNotSelectMonth: "Vui lòng chọn tháng khác",
			hour: "Giờ",
			minute: "Phút",
			selectHour: "Chọn giờ",
			selectDate: "Chọn ngày",
			requiredHour: "Vui lòng chọn giờ"

		};

		var wrap=$(this).parent();
		var format=wrap.attr("data-format");
		var originFormat=format;
		var cog=JSON.parse(wrap.children("code").text());
		var oldData=wrap.find(".input").val();
		var oldDate={hour:"", minute: "00"};
		if(format.indexOf(" ")>-1){
			var format=format.split(" ")[1]
		}
		if(format.indexOf("-")>-1){
			var separator="-";
		}else{
			var separator="/";
		}

		for(var i=0; i<3; i++){
			var getDate=oldData.split(separator)[i];
			var type=format.split(separator)[i];
			if(typeof getDate=="undefined" || getDate==""){
				oldDate[type]=cog.value[type];
			}else{
				oldDate[type]=getDate;
				if(i==0 && oldDate[type].indexOf(" ")>-1){
					var oldTime=oldDate[type].split(" ")[0];
					oldDate[type]=oldDate[type].split(" ")[1];
					oldDate["hour"]=oldTime.split(":")[0];
					oldDate["minute"]=oldTime.split(":")[1];
				}
			}

		}
		var tHTML="", dHTML="", nHTML="";
		if(typeof cog["allow"]["hours"]!="undefined" && cog["allow"]["hours"].length>0){
			var timeClass="hidden";
		}else{
			var timeClass="";
		}
		//Tạo dữ liệu
		Object.keys(cog["allow"]).forEach(function(type){
			var arg=cog["allow"][type];
			switch(type){

				//Giờ
				case "hours":
					if(timeClass.length>0){
						var hoursPart=cog["allow"][type];
						nHTML+='<div class="flex"><div data-show="time" class="form-date-nav primary-actived width-50"><i class="fa fa-clock-o"></i> '+msg["selectHour"]+'</div><div data-show="date" class="form-date-nav width-50"><i class="fa fa-calendar"></i> '+msg["selectDate"]+'</div></div>';
						tHTML+='<div class="form-date-warning"></div>';
						tHTML+='<select data-type="'+type+'">';
						tHTML+='<option value="">'+msg["hour"]+'</option>';
						for(var i=0; i<hoursPart.length; i++){
							tHTML+='<optgroup label="---">';
							var hours=hoursPart[i];
							for(var is=parseInt(hours[0]); is<=parseInt(hours[1]); is++){
								if(is>9){
									var val=is;
								}else{
									var val='0'+is;
								}
								tHTML+='<option value="'+val+'">'+val+'</option>';
							}
							tHTML+='</optgroup>';
						}
						tHTML+='</select>';
						if(cog["allow"]["minutes"].length>0){
							tHTML+='<select data-type="minutes">';
							tHTML+='<option value="00">'+msg["minute"]+'</option>';
							var minutes=cog["allow"]["minutes"].split("-");
							for(var is=minutes[0]; is<=minutes[1]; is++){
								if(is>9){
									var val=is;
								}else{
									var val='0'+is;
								}
								tHTML+='<option value="'+val+'">'+val+'</option>';
							}
							tHTML+='</select>';
						}
					}
				break;

				//Tháng
				case "months":
					dHTML+='<select data-type="'+type+'">';
					for(var i=1; i<=12; i++){
						if(i>9){
							var val=i;
						}else{
							var val='0'+i;
						}
						var disabled=false;
						if(Array.isArray(arg)){
							if(arg.indexOf(i)==-1){
								var disabled=true;
							}
						}else{
							if(i<arg.split("-")[0] || i>arg.split("-")[1]){
								var disabled=true;
							}
						}
						dHTML+='<option data-val="'+val+'" '+(disabled ? 'disabled="disabled" class="form-date-month-disabled"' : '')+' value="'+val+'">'+monthsText[i-1]+'</option>';
					}
					dHTML+='</select>';
				break;

				//Năm
				case "min":
					var min=cog["allow"]["min"];
					var max=cog["allow"]["max"];
					dHTML+='<select data-type="years">';
					var i=max["y"];
					for(i; i>=min["y"]; i--){
						dHTML+='<option value="'+i+'">'+i+'</option>';
					}
					dHTML+='</select>';
				break;
			}
		});

		//Ghi dữ liệu
		$(".form-date-wrap").children(".form-date-picker").not(wrap.children(".form-date-picker")).hide();
		wrap.children(".form-date-picker").html('<div> '+nHTML+' <div class="form-date-body form-date-body-time">'+tHTML+'</div> <div class="form-date-body form-date-body-date '+timeClass+'">'+dHTML+'<table class="form-date-day width-100"></table></div> </div>').toggle();
		if(typeof oldDate["month"]!="undefined"){
			wrap.find('[data-type="minutes"]').val(oldDate["minute"]);
			wrap.find('[data-type="hours"]').val(oldDate["hour"]);
			wrap.find('[data-type="months"]').val(oldDate["month"]);
			wrap.find('[data-type="years"]').val(oldDate["year"]);
			updateCalendar();
			wrap.find('.primary-hover[data-day="'+oldDate["day"]+'"]').addClass("primary-bg");
		}

		//Số ngày trên tháng
		function numberDayInMonth(month,year) {
			return new Date(year, month, 0).getDate();
		}
		
		//Thứ trong tuần
		function weekID(date) {
			var weekDay=["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
			var d = new Date(date);
			return weekDay[d.getDay()];
		}

		//Tạo ngày
		function createDay(month, year){
			var day='';
			var arg=cog["allow"]["days"];
			var min=cog["allow"]["min"];
			var max=cog["allow"]["max"];
			var thisY=parseInt(year), thisM=parseInt(month);
			var minY=parseInt(min["y"]), minM=parseInt(min["m"]), minD=parseInt(min["d"]);
			var maxY=parseInt(max["y"]), maxM=parseInt(max["m"]), maxD=parseInt(max["d"]);
			var monthsEl=wrap.find('select[data-type="months"]>option');
			var totalDay=numberDayInMonth(month, year);
			day+='<tr>';
			var weekMap=[];
			Object.keys(weekName).forEach(function(key, i){
				day+='<th data-key="'+key+'" class="'+(key=="sun" ? 'form-date-sunday' : '')+'" style="width:14%;">'+weekName[key]+'</th>';
				weekMap[i]=key;
			});
			day+='</tr>';
			var i=0, d=1;
			while(d<=totalDay){
				var weekDay=weekID(year+"/"+month+"/"+d);
				if(i%7==0){
					if(i!=0){
						day+='</tr>';
					}
					day+='<tr>';
				}
				if(d>9){
					var val=d;
				}else{
					var val='0'+d;
				}
				if(d>1 || weekDay==weekMap[i]){
					var disabled=false;
					if(Array.isArray(arg)){
						if(arg.indexOf(d)==-1){
							var disabled=true;
						}
					}else{
						if(d<arg.split("-")[0] || d>arg.split("-")[1]){
							var disabled=true;
						}
					}
					if(cog["allow"]["weekDay"].length>0 && cog["allow"]["weekDay"].indexOf(weekDay)==-1){
						var disabled=true;
					}
					var thisD=parseInt(d);
					if(thisY<=minY && thisM<=minM){
						if(thisD<minD || thisM<minM){
							var disabled=true;
						}
					}
					if(thisY>=maxY && thisM>=maxM){
						if(thisD>maxD || thisM>maxM){
							var disabled=true;
						}
					}
					day+='<td title="'+(disabled ? msg["disabledSelect"] : ''+thisD+' - '+monthsText[thisM-1]+' - '+thisY+'')+'" data-day="'+val+'" class="'+(disabled ? 'form-date-day-disabled' : 'primary-hover')+' '+(weekDay=="sun" ? 'form-date-sunday' : '')+'">'+d+'</td>';
					d++;
				}else{
					day+='<td>&nbsp;</td>';
				}
				if(i%7!=0 && d>totalDay){
					day+='</tr>';
				}
				i++;
			}
			monthsEl.each(function(){
				if(!$(this).hasClass("form-date-month-disabled")){
					$(this).removeAttr("disabled");
				}
				var thisVal=$(this).val();
				if(thisVal<minM && thisY<=minY || thisVal>maxM && thisY>=maxY){
					$(this).attr("disabled", "disabled");
				}
			});
			if(day.indexOf("primary-hover")==-1){
				var day='<cation class="form-date-sunday">'+msg["canNotSelectMonth"]+'</cation>';
			}
			return day;
		}

		//Cập nhật lại lịch
		function updateCalendar(){
			var month=wrap.find('[data-type="months"]').val();
			var year=wrap.find('[data-type="years"]').val();
			if(month==null){
				var month=wrap.find('[data-type="months"]>option:selected').attr("data-val");
			}
			var data=createDay(month, year);
			wrap.find(".form-date-day").html(data);
		}

		//Hiện tab lịch
		function showCalendar(show){
			var hour=wrap.find('select[data-type="hours"]').val();
			if(show=="date" && cog["allow"]["requiredHour"] && hour.length==0){
				wrap.find(".form-date-warning").html(msg["requiredHour"]).show();
			}else{
				wrap.find(".form-date-body, .form-date-warning").hide();
				wrap.find(".form-date-body-"+show).show();
				wrap.find(".primary-actived").removeClass("primary-actived");
				wrap.find('.form-date-nav[data-show="'+show+'"]').addClass("primary-actived");
			}
		}

		//Thay đổi tháng - năm
		wrap.off("change", ".form-date-body-date select");
		wrap.on("change", ".form-date-body-date select", function(){
			updateCalendar();
		});

		//Click chuyển tab giờ-ngày
		wrap.off("click", ".form-date-nav");
		wrap.on("click", ".form-date-nav", function(){
			showCalendar($(this).attr("data-show"));
		});

		//Chọn xong giờ-phút
		wrap.off("change", ".form-date-body-time select");
		wrap.on("change", ".form-date-body-time select", function(){
			if($(this).attr("data-type")=="minutes" || wrap.find('select[data-type="minutes"]').length==0){
				showCalendar("date");
			}
		});

		//Lưu thay đổi
		wrap.off("click", ".primary-hover");
		wrap.on("click", ".primary-hover", function(){
			var date={hours:"", minutes: "00"};
			date["day"]=$(this).attr("data-day");
			wrap.find("select").each(function(){
				var type=$(this).attr("data-type");
				date[type]=$(this).val();
			});
			date["time"]=date["hours"].length>0 ? date["hours"]+':'+date["minutes"] : "";
			var output=originFormat.replace(/hour/, date["time"]).replace(/day/, date["day"]).replace(/month/, date["months"]).replace(/year/, date["years"]);
			if(date["hours"].length==0){
				var output=output.replace(/\s/,"");
			}
			wrap.find(".input").val(output);
			$(".form-date-wrap").children(".form-date-picker").hide();
		});
	});
	// Click ra bên ngoài
	$(document).on('click', function(e){
		if( $(e.target).closest('.form-date-wrap').length == 0 ){
			$('.form-date-picker').hide();
		}
	});
});