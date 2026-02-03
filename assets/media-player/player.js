// Script Media player by SienLo

mediaPlayer=function(mediaPlayerAds){
	// <Video full page>
	$(".video-full-page").filter(function() {
		$("body").after($(this)[0].outerHTML);
		$(this).remove();
		$(".video-full-page").show();
	});

	// <Add controls>
	$(".media-player").filter(function() {
		if($(this).attr("data-noinstallscript")=="1"){
			return false;
		}
		$(this).show();
		if ($(this).find("video[controls],audio[controls]").length > 0) {
			$(this).find("video,audio").removeAttr("controls");
			var config = $(this).attr("data-config");

			if ($(this).hasClass('audio')) {
				var media_type = "audio";
			} else {
				var media_type = "video";
				if (typeof $(this).find("video").attr("poster") == 'undefined') {
					$(this).find("video").css({
						"object-fit": "contain"
					});
				}
			}



			// Subtitle
			var get_track = $(this).find("track");
			var sublist = "";
			if (get_track.length > 0) {

				get_track.each(function(i) {

					sublist += '<span data-label="' + $(this).attr("label") + '" data-src="' + $(this).attr("src") + '" data-kind="' + $(this).attr("kind") + '" data-srclang="' + $(this).attr("srclang") + '" data-action="subtitle"' + (i == 0 ? ' class="item-active"' : '') + '>' + $(this).attr("label") + '</span>';

				});

			} else {
				var sublist = "";
			}
			var subtitle = '<div style="display:' + (sublist.length == 0 ? 'none' : 'block') + '"><span data-action="settings" data-show="media-subtitle"><i class="fa fa-commenting-o"></i> Phụ đề</span> <div class="media-setting-body media-subtitle"><span data-src="0" data-action="subtitle">Tắt</span>   <div>' + sublist + '</div> <sublist></sublist></div> </div>';



			// Save quality
			var get_media_source = $(this).find("source");
			var media_source = "";

			get_media_source.each(function(i) {
				media_source += '<span data-action="quality" media-url="' + $(this).attr("src") + '" type="' + $(this).attr("type") + '"' + (i == 0 ? ' class="item-active"' : '') + '>' + $(this).attr("data-quality") + '</span>';
			});

			// Change quality
			if (get_media_source.length > 1) {
				var list_media_source = '<span data-action="settings" data-show="media-quality"><i class="fa fa-indent"></i> Chất lượng</span> <div class="media-setting-body media-quality"><div>' + media_source + '</div> <quality></quality></div>';
			} else {
				var list_media_source = "";
			}

			// Download
			if (config.indexOf('download') != -1) {
				var media_download = '<i data-action="download" class="fa fa-arrow-circle-o-down"></i>';
			} else {
				var media_download = "";
			}




			// Play list
			if ($(this).find("ul").length > 0) {
				get_play_list = "";
				$(this).find("li").each(function(i) {

					get_play_list += '<span data-action="change-playlist" id="' + i + '" data-singer="' + $(this).attr("data-singer") + '" type="' + $(this).find("data").attr('data-type') + '" media="' + $(this).find("data").attr('value') + '">' + $(this).attr('data-label') + '</span>';
				});


				var media_play_list = '<div class="media-item"><i data-action="show-sub" data-show="media-play-list" class="fa fa-bars"><sup class="media-playlist-count">' + ($(this).find("ul li").length + 1) + '</sup></i> <div class="media-item-body media-play-list"><div> <span data-action="change-playlist" id="origin" class="item-active" data-singer="' + $(this).find(".audio-singer span").text() + '" type="' + $(this).find("source").attr("type") + '" media="' + $(this).find("source").attr("src") + '">' + $(this).find(media_type).attr("data-label") + '</span>  ' + get_play_list + '</div></div> </div>';
				var media_play_prev = ' <i style="display:none" data-action="play_prev_next" play="prev" class="play-prev fa fa-backward"></i>';
				var media_play_next = ' <i data-action="play_prev_next" play="next" class="play-next fa fa-forward"></i>';

			} else {
				var media_play_list = "";
				var media_play_next = "";
				var media_play_prev = "";
			}




			// Settings
			var settings = '<div class="media-item"><i data-action="show-sub" data-show="media-list-setting" class="fa fa-cog"></i> <div class="media-item-body media-list-setting"><div> ' + list_media_source + ' ' + subtitle + '  <span data-action="settings" data-show="media-speed"><i class="fa fa-sliders"></i> Tốc độ</span> <div class="media-setting-body media-speed"> <span data-action="speed" data-speed="0.5">- 0.5</span><span data-action="speed" data-speed="1.0">Mặc định</span><span data-action="speed" data-speed="1.5">+ 1.5</span><span data-action="speed" data-speed="2.0">+ 2.0</span> </div>      <span data-action="settings" data-show="media-loop"><i class="fa fa-retweet"></i> Phát khi hết</span> <div class="media-setting-body media-loop"> <span data-action="loop" loop-type="play_next" class="item-active">Phát bài tiếp theo</span><span data-action="loop" loop-type="loop">Lặp lại bài này</span> </div>         ' + (media_type == 'video' ? '<span data-action="settings" data-show="media-info"><i class="fa fa-info-circle"></i> Thông tin</span> <div class="media-setting-body media-info"><span class="media-size"></span></div>' : '') + '             </div> </div></div>';

			if (media_type == "audio") {
				var media_center_btn = "";
			} else {
				var media_center_btn = '<span class="media-center"><i data-action="play_pause" class="media-play-btn fa fa-play-circle-o"></i> <i data-action="play_pause" class="media-play-loading fa fa-spinner" style="display:none"></i></span> <span class="media-play-warning">Nếu tải quá lâu, bạn hãy thử tải lại trang này</span>';
			}


			$(this).find(".media-meta").append(' ' + media_center_btn + ' <div class="media-controls">    <div class="media-progress"> <div class="media-time-hover">00:00</div> <progress class="media-progress-bar" value="0" max="100"></progress>   <div class="media-buffered"></div> </div>   <div class="controls-pd">     ' + media_play_prev + '        <i data-action="play_pause" class="media-play-pause fa fa-play"></i> ' + media_play_next + ' <span class="media-time"><span class="media-current-time">00:00</span>  /  <span class="media-duration-time">00:00</span></span>   <span style="float: right"> ' + media_play_list + ' ' + media_download + ' ' + settings + '  <span id="media-volume"><i data-action="mute" class="fa fa-volume-up"> <span class="media-volume-bar"><span></span></span></i></span>    ' + (media_type == 'video' ? '<i data-action="fullscreen" class="fa fa-expand"></i></span>' : '') + ' </div></div>');


		}

		$(this).find(".media-title").append("<i></i><span></span>");
		$(this).find("video").css({
			"max-height": "" + $(this).height() + "px"
		});


	});

	// <Hover show/hidden controls>
	$(".media-player .media-body").on("mousemove touchmove click", function() {
		var this_div = $(this);
		var this_media = $(this).find("video")[0];


		if (typeof bar_hide !== 'undefined') {
			clearTimeout(bar_hide);
		}
		$(".media-player video").css({
			"cursor": ""
		});
		$(".media-meta").show();
		bar_hide = setTimeout(function() {
			if (!this_media.paused && this_div.find(".media-ads").length == 0) {
				$(".media-player video").css({
					"cursor": "none"
				});
				this_div.find(".media-meta").fadeOut(1000);
			}
		}, 5000); // Hidden bar after 5s


	});

	// <Click media div>
	$(".media-player").on("click", function(e) {
		var this_div = $(this);

		if ($(this).hasClass('audio')) {
			media_type = "audio";
		} else {
			media_type = "video";
			this_div.find("video").css({
				"object-fit": "contain"
			});
		}


		var this_media = $(this).find("" + media_type + "")[0];
		var progressbar = $(this).find(".media-progress");
		checkFile($(this_media).children("source").attr("src"));



		// <Hover seek bar>
		$(".media-controls").on("mousemove touchmove", function(e) {
			$(this).find(".media-progress-bar").css({
				"height": "15px"
			});

		}).mouseleave(function() {
			$(this).find(".media-progress-bar").css({
				"height": "6px"
			});
		});
		// </Hover seek bar>



		// <Time in hover>
		$(progressbar).on("mousemove touchmove", function(e) {

			var pos = e.pageX - $(this).offset().left;
			var progress_pos = Math.round(pos / $(this).width() * 100);
			var thistime = this_media.duration * (progress_pos / 100);
			var margin_right = $(this).width() - pos;

			if (pos < 81 || margin_right < 81) {
				this_div.find(".media-time-hover").show().removeClass("hover-left hover-right").addClass("hover-" + (pos < 81 ? "left" : "right") + "").css({
					"left": ""
				}).text(media_time("", thistime));
			} else {
				this_div.find(".media-time-hover").show().removeClass("hover-left hover-right").css({
					"left": progress_pos + "%"
				}).text(media_time("", thistime));
			}


		}).mouseleave(function() {
			this_div.find(".media-time-hover").hide();
		});
		// </Time in hover> 




		// <Click seek bar>
		$(progressbar).on("click", function(e) {

			var pos = e.pageX - $(this).offset().left;
			progress_pos = Math.round(pos / $(this).width() * 100);

			if (progress_pos > 100) {
				var progress_pos = 100;
			}
			if (progress_pos < 0) {
				var progress_pos = 0;
			}

			this_div.find(".media-progress-bar").val(progress_pos);



			var ctime = this_media.duration * (this_div.find(".media-progress-bar").val() / 100);
			this_media.currentTime = ctime;
			if (this_media.paused) {
				this_media.play();
				this_div.find(".media-play-btn").removeClass("fa-play-circle-o");
				this_div.find(".media-play-pause").removeClass("fa-play").addClass("fa-pause");
			}

		});
		// </Click seek bar>




		// <Volume>
		$(".media-volume-bar").on("click", function(e) {

			var volume_pos = e.pageX - $(this).offset().left;
			var get_volume_level = Math.round(volume_pos / $(this).width() * 10);

			if (get_volume_level >= 9) {
				var get_volume_level = 10;
			}
			$(this).children("span").css({
				"width": "" + get_volume_level * 10 + "%"
			});

			if (get_volume_level >= 9) {
				var volume_level = 1.0;
			} else {
				var volume_level = "0." + get_volume_level + "";
			}
			this_media.volume = volume_level;



		});
		// </Volume>


		function media_time(type, time) {
			if (time > 0) {
				var media_current = {
					"h": Math.floor(time / 3600),
					"m": (Math.floor(time / 60) % 60 >= 10 ? Math.floor(time / 60) % 60 : "0" + Math.floor(time / 60) % 60 + ""),
					"i": (Math.floor(time % 60) >= 10 ? Math.floor(time % 60) : "0" + Math.floor(time % 60) + "")
				};

				if (type.length == 0) {
					var media_time = " " + (media_current["h"] > 0 ? "" + media_current["h"] + ":" : "") + "" + media_current["m"] + ":" + media_current["i"] + " ";
				} else {
					var media_time = media_current[type];
				}
			} else {
				var media_time = "00";
			}

			return media_time;
		}


		// <Update Progress bar>
		this_media.addEventListener('timeupdate', function() {

			// Update seek bar
			if (this_media.duration > 0) {
				updateval = (100 / this_media.duration) * this_media.currentTime;
				this_div.find(".media-progress-bar").val(updateval);


				this_div.find(".media-current-time").text("" + (Math.floor(this_media.duration / 3600) > 0 ? "" + media_time("h", this_media.currentTime) + ":" : "") + "" + media_time("m", this_media.currentTime) + ":" + media_time("i", this_media.currentTime) + "");

			}


			// Update loaded				
			var buffered = this_media.buffered;
			var last_buffered_count = 0;
			var last_buffered = 0;
			for (var i = 0; i < buffered.length; i++) {
				if (buffered.end(i) > this_media.currentTime) {
					if (last_buffered_count == 0) {
						last_buffered = buffered.end(i);
						last_buffered_count++;
					}
				}
			}

			var last_buffered_bar = Math.round((100 / this_media.duration) * last_buffered);
			this_div.find(".media-buffered").css({
				"width": "" + last_buffered_bar + "%"
			});




		});
		// </Update Progress bar>


		$(this).off("mousemove touchmove");


	});

	// <Click div player - event>
	var click_count = 0;
	var clicked_id = null;
	$(".media-player").on("click", function(e) {
		click_count++;
		var this_div = $(this);
		var this_media = $(this).find("" + media_type + "")[0];
		var div_media = $(this).children(".media-body")[0];
		var this_action = $(e.target).attr("data-action");




		// <Update current Time>
		function update_currentTime() {
				var media_duration = {
					"h": Math.floor(this_media.duration / 3600),
					"m": (Math.floor(this_media.duration / 60) % 60 >= 10 ? Math.floor(this_media.duration / 60) % 60 : "0" + Math.floor(this_media.duration / 60) % 60 + ""),
					"i": (Math.floor(this_media.duration % 60) >= 10 ? Math.floor(this_media.duration % 60) : "0" + Math.floor(this_media.duration % 60) + "")
				};
				if (this_media.duration > 1) {
					this_div.find(".media-duration-time").text("" + (media_duration["h"] > 0 ? "" + media_duration["h"] + ":" : "") + "" + media_duration["m"] + ":" + media_duration["i"] + "");
				} else {
					setTimeout(function() {
						update_currentTime();
					}, 2000);
				}
			}
			// </Update current Time>
		update_currentTime();


		// <Play or pause>
		if ($(e.target).is("" + media_type + "") || this_action == "play_pause") {


			if (media_type == "video") {
				// Loading
				var play_warning = null;
				this_media.onwaiting = function() {
					clearTimeout(play_warning);
					this_div.find(".media-play-btn").hide();
					this_div.find(".media-play-loading").show();
					play_warning = setTimeout(function() {
						this_div.find(".media-play-warning").show().fadeOut(10000);
					}, 15000);
				}

				//Playing
				this_media.onplaying = function() {
					clearTimeout(play_warning);
					this_div.find(".media-play-btn").show();
					this_div.find(".media-play-loading").hide();
					this_div.find(".media-play-warning").hide();

				}




			}


			// Play and pause
			if (this_div.find(".media-ads").length == 0) {
				if (this_media.paused) {
					this_media.play();
					this_div.find(".media-play-pause").removeClass("fa-play").addClass("fa-pause");
					apply_subtitle();
					if (media_type == "audio") {
						this_div.find(".media-meta img").addClass("img-spin");
					} else {
						this_div.find(".media-play-btn").removeClass("fa-play-circle-o");
					}
				} else if (this_media.duration > 0) {
					this_media.pause();
					this_div.find(".media-play-pause").removeClass("fa-pause").addClass("fa-play");
					if (media_type == "audio") {
						this_div.find("img").removeClass("img-spin");
					} else {
						this_div.find(".media-play-btn").addClass("fa-play-circle-o");
					}

				}
			}
		}
		// </Play or pause>


		//<MimeType>
		function media_mime(file) {
				var fext = file.substr((file.lastIndexOf('.') + 1)).toLowerCase();
				return "" + media_type + "/" + fext + "";
			}
			//</MimeType>


		// <Change source>

		function change_media(url, toTime, type) {
			if (url !== "undefined") {
				this_div.find("source").remove();
				this_div.find("" + media_type + "").prepend('<source src="' + url + '" type="' + (type.length > 0 ? type : media_mime(url)) + '"/>');
				this_media.load();
				this_media.play();

				this_div.find(".media-play-btn").removeClass("fa-play-circle-o");
				this_div.find(".media-play-pause").removeClass("fa-play").addClass("fa-pause");
				update_currentTime();

				this_media.addEventListener('loadedmetadata', function() {
					this_media.currentTime = toTime;
				}, false);
				checkFile(url);
			}
		}

		// </Change source>




		// <Download>
		if (this_action == "download") {
			if (confirm("Tải về máy " + media_type + " đang phát " + this_div.find(".media-quality .item-active").text() + " ") == true) {
				var godl = window.open('' + this_div.find("source").attr("src") + '', '_blank');
				if (godl) {
					godl.focus();
				} else {
					alert('Vui lòng cho phép mở trong tab mới');
				}
			}
		}

		// </Download>



		// <Full screen>
		if (this_action == "fullscreen") {
			var isFullscreen = document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement || document.msFullscreenElement;

			if (isFullscreen) {
				if (document.exitFullscreen) {
					document.exitFullscreen();
				} else if (document.webkitExitFullscreen) {
					document.webkitExitFullscreen();
				} else if (document.mozCancelFullScreen) {
					document.mozCancelFullScreen();
				} else if (document.msExitFullscreen) {
					document.msExitFullscreen();
				}


				$(e.target).removeClass("fa-compress").addClass("fa-expand");
			} else {

				if (div_media.requestFullscreen) {
					div_media.requestFullscreen();
				} else if (div_media.webkitRequestFullscreen) {
					div_media.webkitRequestFullscreen();
				} else if (div_media.mozRequestFullScreen) {
					div_media.mozRequestFullScreen();
				} else if (div_media.msRequestFullscreen) {
					div_media.msRequestFullscreen();
				}


				$(e.target).removeClass("fa-expand").addClass("fa-compress");

			}


		}
		// </Full screen> 


		// <Mute>
		if (this_action == "mute") {
			if (this_media.muted == true) {
				this_media.muted = false;
				$(this).css({
					"color": ""
				});
				$(e.target).css({
					"color": ""
				});
			} else {
				this_media.muted = true;
				$(e.target).css({
					"color": "gray"
				});
			}
		}
		// </Mute>


		// <Speed>
		if (this_action == "speed") {
			this_media.playbackRate = $(e.target).attr("data-speed");
			this_div.find('[data-action="speed"]').removeClass();
			$(e.target).addClass("item-active");
		}
		// </Speed>


		// <Speed>
		if (this_action == "loop") {
			this_div.find('[data-action="loop"]').removeClass();
			$(e.target).addClass("item-active");
		}
		// </Speed>


		// <Subtitle>
		if (this_action == "subtitle") {
			this_div.find('[data-action="subtitle"]').removeClass();
			$(e.target).addClass("item-active");
			apply_subtitle();
		}


		function apply_subtitle() {
				if (this_div.find("track").length > 0) {
					this_media.textTracks[0].mode = 'hidden';
					this_div.find("track").remove();
				}
				this_div.find(".media-subtitle .item-active").each(function() {
					if ($(this).attr("data-src") != "0") {
						this_div.find("" + media_type + "").append('<track label="' + $(this).attr("data-label") + '" kind="' + $(this).attr("data-kind") + '" srclang="' + $(this).attr("data-srclang") + '" src="' + $(this).attr("data-src") + '">');
						this_media.textTracks[0].mode = 'showing';
					}
				});

			}
			// </Subtitle>


		// <Change quality>
		if (this_action == "quality") {
			change_media($(e.target).attr('media-url'), this_media.currentTime, $(e.target).attr('type'));
			this_div.find('[data-action="quality"]').removeClass();
			$(e.target).addClass("item-active");
			this_div.find(".media-title span").text('' + $(e.target).text() + '');

		}
		// </Change quality>




		// <Count>
		function mediaAdsCount(id,type,name) {
			$.post("/api", {"MediaPlayer":1, "action": "count", "id": id, "type": type, "name":name});
		}
		// </Count>



		// </ADS>

		if (this_div.attr("data-config").indexOf('no-ads') == -1) {

			// <Video ADS>

			// ADS start
			var ads_runing = 0;
			var ads_stt = false;

			function ads_start(title, video, link_title, link_url, skip_time, audio) {
				if (audio == 1 || audio == 0 && media_type == "video") {

					var media_link = this_div.find("source").attr("src");
					var media_mime = this_div.find("source").attr("type");
					if (!ads_stt) {
						ads_stt = true;
						ads_runing = 1;
						ads_stt = false;
					}

					currentTime = this_media.currentTime;
					change_media(video, 0, "");
					if (this_div.find("track").length > 0) {
						this_media.textTracks[0].mode = 'hidden';
					}
					this_div.find(".media-controls").hide().after('<div class="media-ads"> ' + (link_title.length > 0 ? '<span class="media-ads-link"><a target="_blank" href="' + link_url + '">' + link_title + '</a></span>' : '') + ' <span class="media-ads-time"></span> <span style="display:none" class="media-ads-skip">Bỏ Qua &raquo;</span> </div>');
					this_div.find(".media-title").hide().after('<div class="media-title-ads">' + title + ' <i></i></div>');
					this_div.find(".media-meta").show();
					this_div.find(".media-center").hide();

					skip_time_count = skip_time;
					var skip_down = setInterval(function() {
						skip_time_count -= 1;
						this_div.find(".media-ads-time").text('Tắt quảng cáo (' + skip_time_count + ')');
						if (skip_time_count == 0) {
							clearInterval(skip_down);
						}
					}, 1000);

					setTimeout(function() {
						this_div.find(".media-ads-time").remove();
						$(".media-ads-skip").show();
					}, skip_time * 1000);

					// Skip AD


					this_div.find("" + media_type + "").on('timeupdate', function() {

						var ads_time = Math.round(this_media.duration - this_media.currentTime);
						this_div.find(".media-title-ads i").text(' (' + ads_time + ')');

						if (ads_time < 1) {
							disable_ads();
							this_div.find("" + media_type + "").off("timeupdate");
						}

					});

					$(".media-ads-skip").on("click", function() {
						disable_ads();
						this_div.find("" + media_type + "").off("timeupdate");
					});




					function disable_ads() {
						this_div.find(".media-controls").show();
						this_div.find(".media-ads").remove();
						this_div.find(".media-title").show();
						this_div.find(".media-title-ads").remove();
						this_div.find(".media-center").show();
						video_ads_end(media_link, media_mime);
						ads_runing = 0;
					}

				}
			}




			// ADS end
			function video_ads_end(origin_url, mime) {
				change_media(origin_url, currentTime, mime);
				apply_subtitle();
			}

			if (click_count == 1) {
				$.each(mediaPlayerAds["video"], function(aid, ads) {
					setTimeout(function() {
						var src=ads["src"].split("|");
						var random=Math.floor(Math.random()*src.length);
						if (ads_runing == 0) {
							ads_start(ads["title"], src[random], ads["link_title"], ads["link_url"], ads["skip_time"], ads["audio"]);
						}
						mediaAdsCount(aid, "video", src[random]);
					}, ads["sec"] * 1000);
				});


			}
			// </Video ADS>




			// <Popup ADS>
			// ADS start
			function pop_ads_start(title, img, url, hidden_after, audio) {
				if (audio == 1 || audio == 0 && media_type == "video") {
					$(".media-ads-popup").remove();
					this_div.find(".media-meta").before('<div class="media-ads-popup"><a class="ads-click" title="' + title + '" target="_blank" href="' + url + '"><img src="' + img + '"/></a> <span class="media-ads-close"><i class="fa fa-times-circle-o"></i></span></div>');

					// Close AD
					setTimeout(function() {
						$(".media-ads-popup").remove();
					}, hidden_after * 1000);
					$(".media-ads-close").on("click", function() {
						$(".media-ads-popup").remove();
					});

					$(".ads-click").on("click", function() {
						$(".media-ads-popup").remove();
					});


				}
			}



			if (click_count == 1) {

				$.each(mediaPlayerAds["popup"], function(aid, ads) {
					setTimeout(function() {
						var src=ads["src"].split("|");
						var random=Math.floor(Math.random()*src.length);
						pop_ads_start(ads["title"], src[random], ads["link"], ads["hidden_after"], ads["audio"]);
						mediaAdsCount(aid, "popup", src[random]);
					}, ads["sec"] * 1000);
				});


			}
			// </Popup ADS>


		}
		// </ADS>


		// <Auto play next>

		this_media.onended = function() {

			if (this_div.find(".media-loop .item-active").attr("loop-type") == "play_next") {
				if (this_div.find(".media-play-list .item-active").next().attr("media") !== "undefined") {
					this_div.find(".media-play-list .item-active").removeClass().next().addClass("item-active");
					change_media(this_div.find(".media-play-list .item-active").attr("media"), 0, this_div.find(".media-play-list .item-active").attr("type"));
					this_div.find(".play-prev").show();
					if (this_div.find(".media-play-list .item-active").next().attr("media") === "undefined") {
						this_div.find(".play-next").hide();
					}
					update_quality();
				} else {
					if (this_div.find("ul").length > 0) {
						this_div.find(".media-play-list .item-active").removeClass();
						change_media(this_div.find(".media-play-list #origin").addClass("item-active").attr("media"), 0, this_div.find(".media-play-list #origin").attr("type"));
						this_div.find(".play-prev").hide();
						this_div.find(".play-next").show();
						update_quality();
					} else {
						this_media.currentTime = 0;
						this_media.play();
					}
				}

			} else {
				this_media.currentTime = 0;
				this_media.play();
			}


		}

		// </Auto play next>




		// <Play next>
		if (this_action == "play_prev_next") {
			this_div.find(".media-play-list .item-active").removeClass()[$(e.target).attr("play")]().addClass("item-active");
			if (this_div.find(".media-play-list .item-active")[$(e.target).attr("play")]().attr("media") === "undefined") {
				this_div.find(".play-" + $(e.target).attr("play") + "").hide();
			}
			if ($(e.target).attr("play") == "next") {
				this_div.find(".play-prev").show();
			} else {
				this_div.find(".play-next").show();
			}
			change_media(this_div.find(".media-play-list .item-active").attr("media"), 0, this_div.find(".media-play-list .item-active").attr("type"));
			update_quality();
		}
		// </Play next>




		// <Playlist>
		function update_quality() {
			var id = this_div.find(".media-play-list .item-active").attr("id");
			if (id == "origin") {
				this_div.find(".media-quality div").show();
				this_div.find(".media-quality quality").hide();
			} else {
				this_div.find(".media-quality div").hide();
				this_div.find(".media-quality quality").show();
				var media_source = "";
				this_div.find("li").eq(id).each(function() {
					$(this).find("data").each(function(i) {
						media_source += '<span data-action="quality" type="' + $(this).attr("data-type") + '" media-url="' + $(this).attr("value") + '"' + (i == 0 ? ' class="item-active"' : '') + '>' + $(this).text() + '</span>';
					});
				});
				this_div.find(".media-quality quality").html(media_source);
			}

			this_div.find(".media-title i,.audio-title span").html('' + (media_type == 'video' ? '<br/>' : '') + '' + this_div.find('.media-play-list [class="item-active"]').text() + '');
			this_div.find(".audio-singer span").html('' + this_div.find('.media-play-list [class="item-active"]').attr("data-singer") + '');

			update_subtitle();
		}




		//Update Subtitle
		function update_subtitle() {
			var id = this_div.find(".media-play-list .item-active").attr("id");
			this_div.find(".media-subtitle .item-active").removeClass();
			if (id == "origin") {
				this_div.find(".media-subtitle div").show();
				this_div.find(".media-subtitle sublist").hide();
				this_div.find(".media-subtitle div span").eq(0).addClass("item-active");
			} else {
				this_div.find(".media-subtitle div").hide();
				this_div.find(".media-subtitle sublist").show();
				var media_sub = "";
				this_div.find("li").eq(id).each(function() {
					$(this).find("sub").each(function(i) {
						media_sub += '<span data-label="' + $(this).text() + '" data-src="' + $(this).attr("data-src") + '" data-kind="' + $(this).attr("data-kind") + '" data-srclang="' + $(this).attr("data-srclang") + '" data-action="subtitle"' + (i == 0 ? ' class="item-active"' : '') + '>' + $(this).text() + '</span>';
					});
				});
				this_div.find(".media-subtitle sublist").html(media_sub);

			}
			if (this_div.find(".media-subtitle .item-active").length == 0) {
				this_div.find(".media-subtitle").parent().hide();
			} else {
				this_div.find(".media-subtitle").parent().show();
			}
			apply_subtitle();

		}




		if (this_action == "change-playlist") {
			change_media($(e.target).attr("media"), 0, $(e.target).attr("type"));
			if ($(e.target).index() == (this_div.find(".media-play-list span").length - 1)) {
				this_div.find(".play-next").hide();
			} else {
				this_div.find(".play-next").show();
			}

			if ($(e.target).index() == 0) {
				this_div.find(".play-prev").hide();
			} else {
				this_div.find(".play-prev").show();
			}

			this_div.find(".media-play-list span").removeClass();
			$(e.target).addClass("item-active");
			update_quality();

		}
		// </Playlist>




		// <Show sub menu>
		if (this_action == "show-sub") {
			$(".media-item-body").not(this_div.find("." + $(e.target).attr("data-show") + "")).hide();
			this_div.find("." + $(e.target).attr("data-show") + "").toggle();
			$(".media-item-click").not($(e.target)).removeClass("media-item-click");
			$(e.target).toggleClass("media-item-click");

		}
		// </Show sub menu>


		// <Show setting>
		if (this_action == "settings") {
			$(".media-setting-body").not(this_div.find("." + $(e.target).attr("data-show") + "")).hide();
			this_div.find("." + $(e.target).attr("data-show") + "").toggle();



			// <Update video quality>
			if ($(e.target).attr("data-show") == "media-info") {
				var media_quality = "";
				if (this_media.videoHeight > 100) {
					var media_quality = " (144P)";
				}
				if (this_media.videoHeight > 200) {
					var media_quality = " (240P)";
				}
				if (this_media.videoHeight > 300) {
					var media_quality = " (360P)";
				}
				if (this_media.videoHeight > 400) {
					var media_quality = " (480P)";
				}

				if (this_media.videoHeight > 700) {
					var media_quality = " (HD)";
				}
				if (this_media.videoHeight > 1000) {
					var media_quality = " (FHD)";
				}
				if (this_media.videoHeight > 1400) {
					var media_quality = " (QHD)";
				}
				if (this_media.videoHeight > 2000) {
					var media_quality = " (4K)";
				}
				if (this_media.videoHeight > 4000) {
					var media_quality = " (8K)";
				}
				if (this_media.videoHeight > 5000) {
					var media_quality = " (++)";
				}

				this_div.find(".media-size").text("" + this_media.videoWidth + "x" + this_media.videoHeight + "" + media_quality + "");
			}
			// </Update video quality>



		}
		// </Show setting>
		clicked_id = this_div.index(".media-player");

	});
	
	//Kiểm tra file lỗi
	function checkFile(url){
		//Thống kê lượt xem mỗi ngày
		$.post("/api", {"MediaPlayer":1, "action": "countToday", "link": url});

		//Gửi link lỗi
		$.ajax({
			url: url,
			timeout: 2000,
			type: "HEAD",
			error: function(e){
				$.post("/api", {"MediaPlayer":1, "action": "brokenLink", "link": url});
			}
		});
	}

	
}


