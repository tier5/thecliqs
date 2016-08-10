//Build Shuffle button
(function($) {
  var SCROLL_ITEM = 5;
	var CONTAINER_HEIGHT = 205;
	var mp3ContainerList;
	//Choose next song
	function mejsPlayNext(currentPlayer, audio) {
		var current_item = '';
		var audio_src = '';
		var mp3Container = $(audio).closest('.mp3music_container');
		var currentIndex =  mp3Container.find('li').index(mp3Container.find('li.current'));
		
		if (mp3Container.find('li.current').length > 0) {
			current_item = mp3Container.find('li.current span.link');
			audio_src = nextSong(current_item, mp3Container).find(".link").text();
		}
		else {
			current_item = mp3Container.find('.song-list li:first .link');
			audio_src = newPlay(current_item);
		}

		if ($(current_item.parent()).is(':last-child')) {// if it is last - stop playing
			if (mp3Container.find('.mejs-loop-on').size() > 0) {
				var ul = $('.song-list');
				var li = ul.find('li').size();
				var song_title = '';
				if (li == 1) {
					audio_src = current_item.text();
					if (mp3Container.find('#song-title-head').size() > 0) {
						song_title = current_item.parent().find(".song-title").text();
					}
				}
				else {
					current_item = mp3Container.find('.song-list li:first span.link');
					mp3Container.find('.song-list .current').removeClass("current");
					mp3Container.find('.song-list li:first').addClass("current");
					
					audio_src = current_item.text();
					if (mp3Container.find('#song-title-head').size() > 0) {
						song_title = current_item.parent().find(".song-title").text()
					}
				}
				if ($('#song-title-head').size() > 0) {
					mp3Container.find('#song-title-head').text(song_title);
				}
				currentPlayer.pause();
				currentPlayer.setSrc(audio_src);
				currentPlayer.load();
				
				//Delay play if is mobile
				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
					setTimeout(function(){currentPlayer.play()},2000);
				} else {
					currentPlayer.play();
				}
			}
			else
			if (mp3Container.find('.mejs-shuffle-on').size() > 0) {
				audio_src = nextSong(current_item, mp3Container).find(".link").text();
				currentPlayer.pause();
				currentPlayer.setSrc(audio_src);
				currentPlayer.load();
				
				//Delay play if is mobile
				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
					setTimeout(function(){currentPlayer.play()},2000);
				} else {
					currentPlayer.play();
				}
			}
			else {
				//Do nothing to stop player
			}
		}
		else {
			currentPlayer.pause();
			currentPlayer.setSrc(audio_src);
			currentPlayer.load();
			
			//Delay play if is mobile
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
				setTimeout(function(){currentPlayer.play()},2000);
			} else {
				currentPlayer.play();
			}
		}
		
		/*Slim scroll*/
		var newIndex =  mp3Container.find('li').index(nextSong(current_item, mp3Container));
		var countSong = mp3Container.find('li').size();
		var itemHeight = mp3Container.find('li.current').outerHeight();
		var barPosition = parseInt(mp3Container.find('.slimScrollBar').css('top'));
		// If album/playlist end
		if(newIndex == -1) 
		{
			newIndex = 0;
			offsets = 0;
		}
		// Get user distance
		var userScroll = Math.round(mp3Container.find('.song-list').scrollTop());
		var currentHeight = currentIndex * itemHeight;
		var offset = newIndex - currentIndex;
		var offsets = currentHeight - userScroll;
		
		if((newIndex > (countSong - SCROLL_ITEM)) && (currentIndex < (countSong - SCROLL_ITEM))) {
			offset = countSong - SCROLL_ITEM - currentIndex;
		}
		
		if((newIndex > (countSong - SCROLL_ITEM)) && (currentIndex >= (countSong - SCROLL_ITEM))) { 
			offset = 0;
			offsets = 0;
		}
		
		if((newIndex < (countSong - SCROLL_ITEM)) && (currentIndex > (countSong - SCROLL_ITEM))) {
			offset = newIndex - (countSong - SCROLL_ITEM);
		}
		
		if((newIndex > (countSong - SCROLL_ITEM)) && (userScroll < ((countSong - SCROLL_ITEM) * itemHeight)))
		{
			offsets = (countSong - SCROLL_ITEM)*itemHeight - userScroll;
		}
		mp3Containers = mp3ContainerList.eq((mp3ContainerList.index(mp3Container)));
		mp3music_scroll(mp3Containers, offset * (itemHeight) + offsets);
	}

	// Shuffle button
	MediaElementPlayer.prototype.buildshuffle = function(player, controls, layers, media) {
		var
		shuffle = $('<div class="mejs-button mejs-shuffle-button ' + ((player.options.shuffle) ? 'mejs-shuffle-on' : 'mejs-shuffle-off') + '">' + '<button type="button"></button>' + '</div>')
		.appendTo(controls)
		.click(function() {
			player.options.shuffle = !player.options.shuffle;
			if (player.options.shuffle) {
				shuffle.removeClass('mejs-shuffle-off').addClass('mejs-shuffle-on');
				player.options.loop = false;
				$('.mejs-loop-button').removeClass('mejs-loop-on').addClass('mejs-loop-off');
			}
			else {
				shuffle.removeClass('mejs-shuffle-on').addClass('mejs-shuffle-off');
			}
		});
	}
	
  // Loop button
	MediaElementPlayer.prototype.buildloop = function(player, controls, layers, media) {
		var
		loop = $('<div class="mejs-button mejs-loop-button ' + ((player.options.loop) ? 'mejs-loop-on' : 'mejs-loop-off') + '">' + '<button type="button"></button>' + '</div>')
		.appendTo(controls)
		.click(function() {
			player.options.loop = !player.options.loop;
			if (player.options.loop) {
				loop.removeClass('mejs-loop-off').addClass('mejs-loop-on');
				player.options.shuffle = false;
				$('.mejs-shuffle-button').removeClass('mejs-shuffle-on').addClass('mejs-shuffle-off');
			}
			else {
				loop.removeClass('mejs-loop-on').addClass('mejs-loop-off');
			}
		});
	}
	
  // Preview button
	MediaElementPlayer.prototype.buildprev = function(player, controls, layers, media) {
		var mp3Container = $(controls).closest('.mp3music_container');
		var
		prev = $('<div class="mejs-button mejs-prev-button ' + ((player.options.prev) ? 'mejs-prev-on' : 'mejs-prev-off') + '">' + '<button type="button"></button>' + '</div>')
		.appendTo(controls)
		.click(function(evt) {
			var prevsong = '';
			var audio_src = '';
			var song_title = '';
			var current_item = $('.song-list').find('li.current');
			if (mp3Container.find('.mejs-shuffle-on').size() > 0) {
				var ul = mp3Container.find('.song-list');
				var songCount = ul.find('li').size();
				if (songCount <= 1) {
					nextItemGet = 0;
				}
				else {
					while (( nextItemGet = getRandomInt(0, songCount - 1)) == mp3Container.find('li.current').index()) {
					}
				}
				prevsong = mp3Container.find(".song-list li").eq(nextItemGet);

				mp3Container.find('li.current').removeClass('current');
				audio_src = prevsong.find(".link").text();
				if (mp3Container.find('#song-title-head').size() > 0) {
					song_title = prevsong.find(".song-title").text();
				}

				prevsong.addClass('current');
			}
			else {
				prevsong = mp3Container.find('li.current').prev();
				if (!prevsong.size()) {
					return true;
				}

				mp3Container.find('li.current').removeClass('current');
				audio_src = prevsong.find(".link").text();
				if (mp3Container.find('#song-title-head').size() > 0) {
					song_title = prevsong.find(".song-title").text()
				}
				;
				prevsong.addClass('current');
			}


			player.pause();
			player.setSrc(audio_src);
			player.load();
			
			//Delay play if is mobile
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
				setTimeout(function(){player.play()},2000);
			} else {
				player.play();
			}
			
			if (mp3Container.find('#song-title-head').size() > 0) {
				mp3Container.find('song-title-head').text(song_title);
			}
		});
	}
	
  // Next button
	MediaElementPlayer.prototype.buildnext = function(player, controls, layers, media) {
		var mp3Container = $(controls).closest('.mp3music_container');
		var
		next = $('<div class="mejs-button mejs-next-button ' + ((player.options.next) ? 'mejs-next-on' : 'mejs-next-off') + '">' + '<button type="button"></button>' + '</div>')
		.appendTo(controls)
		.click(function(evt) {
			var nextsong = '';
			var audio_src = '';
			var song_title = '';
			var current_item = mp3Container.find('.song-list').find('li.current');
			if (mp3Container.find('.mejs-shuffle-on').size() > 0) {//check shuffle mode
				var ul = mp3Container.find('.song-list');
				var songCount = ul.find('li').size();
				if (songCount <= 1) {
					nextItemGet = 0;
				}
				else {
					while (( nextItemGet = getRandomInt(0, songCount - 1)) == mp3Container.find('li.current').index()) {
					}
				}
				nextsong = mp3Container.find(".song-list li").eq(nextItemGet);

				mp3Container.find('li.current').removeClass('current');
				audio_src = nextsong.find(".link").text();
				if (mp3Container.find('#song-title-head').size() > 0) {
					song_title = nextsong.find(".song-title").text();
				}

				nextsong.addClass('current');
			}
			else if ($(current_item).is(':last-child')) {
				nextsong = mp3Container.find('.song-list li:first span.link');
				audio_src = nextsong.text();
				if (mp3Container.find('#song-title-head').size() > 0) {
					song_title = nextsong.parent().find(".song-title").text();
				}

				mp3Container.find('li.current').removeClass('current');
				nextsong.parent().addClass('current');
			}
			else {
				nextsong = mp3Container.find('li.current').next();
				if (!nextsong.size()) {
					return true;
				}

				mp3Container.find('li.current').removeClass('current');
				audio_src = nextsong.find(".link").text();
				if (mp3Container.find('#song-title-head').size() > 0) {
					song_title = nextsong.find(".song-title").text();
				}

				nextsong.addClass('current');
			}
			
			player.pause();
			player.setSrc(audio_src);
			player.load()
			
			//Delay play if is mobile
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
				setTimeout(function(){player.play()},2000);
			} else {
				player.play();
			}
			
			if (mp3Container.find('#song-title-head').size() > 0) {
				mp3Container.find('#song-title-head').text(song_title);
			}
		});
	}
	
  // Col button
	MediaElementPlayer.prototype.buildcol = function(player, controls, layers, media) {
		var
		col = $('<div class="col-vol">' + '<span></span>' + '</div>')
		.appendTo(controls)
	}
	
  // Get an random number
	function getRandomInt(min, max) {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}

	function getNextSong(current_item) {//get next li to get song
		var nextItem = '';
		var ul = $(current_item).closest('ul');
		var li = ul.find('li').size();
		var mp3Container = $(current_item).closest('.mp3music_container');
		if (mp3Container.find('.mejs-shuffle-on').length > 0) {
			var nextItemGet = getRandomInt(0, li - 1);
			nextItem = $(".song-list li").eq(nextItemGet);
		}
		else
		if (mp3Container.find('.mejs-loop-on').length > 0) {
			if (li == 1) {
				nextItem = $(current_item);
			}

			if ($(current_item).parent().next()) {
				nextItem = $(current_item).parent().next();
			}
			else {
				nextItem = $(current_item).closest('li');
			}
		}
		else {
			nextItem = $(current_item).parent().next();
		}
		return nextItem;
	}

	// Get next song's link
	function nextSong(current_item, mp3Container) {
		var SCROLL_ITEM = 5;
		var nextItem = '';
		var ul = $(mp3Container).find('ul');
		var songCount = ul.find('li').size();
		
		if (mp3Container.find('.mejs-shuffle-on').length > 0) {
			var nextItemGet = getRandomInt(0, songCount - 1);
			nextItem = ul.find('li').eq(nextItemGet);
		}
		else
		if (mp3Container.find('.mejs-loop-on').length > 0) {
			if (songCount == 1) {
				nextItem = $(current_item);
			}
			
			if ($(current_item).parent().next()) {
				nextItem = $(current_item).parent().next();
			}
			else {
				nextItem = $(current_item).closest('li');
			}
		}
		else {
			nextItem = $(current_item).parent().next();
		}
		//get next song
		
		var audio_src = nextItem.find(".link").text();
		var songTitle = nextItem.find(".song-title").text();

		if (mp3Container.find('#song-title-head').size() > 0 && songTitle != '') {
			mp3Container.find('#song-title-head').text(songTitle);
		}

		nextItem.addClass('current').siblings().removeClass('current');

		//return audio_src;
		return nextItem;
	}

	// Play new playlist
	function newPlay(current_item) {
		var nextItem = '';
		nextItem = getNextSong(current_item);
		//get next song
		var audio_src = nextItem.next().text();
		$(current_item).next().addClass('current').siblings().removeClass('current');
		return audio_src;
	}
	
	// Remove nex, prev feature
	function removeFeature(features) {
		for(i = 0; i < features.length; i++) {
			if(i == features.indexOf('prev') || i == features.indexOf('next')) {
				features.splice(i, 1);
			}
		}
		return features;
	}
	
	$(document).ready(function() {
		mp3ContainerList = $('.mp3music_container');
		var fullFeature = ['prev', 'playpause', 'next', 'progress', 'duration', 'shuffle', 'loop', 'col', 'volume'];
		var feedFeature = ['prev', 'playpause', 'next', 'progress', 'duration', 'volume'];
		var profileFeature = ['prev', 'playpause', 'next', 'progress'];

		if($('.yn-music').next().find("li").size() == 1) {
			fullFeature = removeFeature(fullFeature);
		}

		//Create a new flash audio - Album player
		//Check if have only one song, remove next, preview button in feature
		$('.yn-music audio').mediaelementplayer({
			  success : function(mediaElement, domObject) {
				mediaElement.addEventListener('ended', function(e) { 
					mejsPlayNext(e.target, domObject);
				}, false);
				
        mediaElement.addEventListener('loadeddata', function(e) {
					//Fix on safari
          var currentItem = $(domObject).closest('.mp3music_container').find('li.current:last');
					var mp3musicVote = currentItem.find(".song_vote").text();
					var isVote = currentItem.find('.isvote').text();
					
          changevote(mp3musicVote, isVote);
          //_onItemChanged(currentItem.find('.song_id').text());
        }, false);
			},
			flashName : "flashmediaelement.swf",
			// List features.
			features : fullFeature,
			keyActions : [],
			startVolume : 0.8,
			pauseOtherPlayers : true,
		}).load(); //disable for safary end of song fail scroll
		//});

		// Feed player
		$('.mp3music_feed_player').each(function(index){
				if($(this).parent().find("li").size() == 1) 
				{
					$(this).find('audio').mediaelementplayer({
						success : function(mediaElement, domObject) 
						{
							mediaElement.addEventListener('ended', function(e) 
							{
								mejsPlayNext(e.target, domObject);
							}, false);
							
							mediaElement.addEventListener('loadeddata', function(e) 
							{
								var currentItem = $(domObject).closest('.mp3music_container').find('li.current');
								_changePlayCount(currentItem.find('.song_id').text());
							}, false);
							
							//mediaElement.addEvenListener('', function(e)
							//{
								
							//}, false);
							
						},
						flashName : "flashmediaelement.swf",
						features : ['playpause', 'progress', 'duration', 'volume'],
						keyActions : [],
						startVolume : 0.8,
						pauseOtherPlayers : true
					});
				}
				else 
				{
					$(this).find('audio').mediaelementplayer({
						success : function(mediaElement, domObject) 
						{
							mediaElement.addEventListener('ended', function(e) 
							{
								mejsPlayNext(e.target, domObject);
							}, false);
							mediaElement.addEventListener('loadeddata', function(e) 
							{
								var currentItem = $(domObject).closest('.mp3music_container').find('li.current');
								_changePlayCount(currentItem.find('.song_id').text());
							}, false);
						},
						flashName : "flashmediaelement.swf",
						features : feedFeature,
						keyActions : [],
						startVolume : 0.8,
						pauseOtherPlayers : true
					});
				}
		});
		
		if($('.mp3music_share_player').next().find("li").size() == 1) 
		{
			feedFeature = removeFeature(feedFeature);
		}
		
		// Create a new flash audio - Share player
		$('.mp3music_share_player audio').mediaelementplayer({
			success : function(mediaElement, domObject) 
			{
				mediaElement.addEventListener('ended', function(e) {
					mejsPlayNext(e.target, domObject);
				}, false);
        mediaElement.addEventListener('loadeddata', function(e) {
					var current_item = $(domObject).closest('.mp3music_container').find('li.current');
          _changePlayCount(current_item.find('.song_id').text());
        }, false);
			},
			flashName : "flashmediaelement.swf",
			features : feedFeature,
			keyActions : [],
			startVolume : 0.8,
			pauseOtherPlayers : true
		});
		
		if($('.profile-player').next().find("li").size() == 1) {
			profileFeature = removeFeature(profileFeature);
		}
		
		// Playlist on profile player
		$('.profile-player audio').mediaelementplayer({
			success : function(mediaElement, domObject)
			{
				mediaElement.addEventListener('ended', function(e) {
					mejsPlayNext(e.target, domObject);
				}, false);
				
        mediaElement.addEventListener('loadeddata', function(e) {
          var current_item = $('.song-list li.current:first span.link');
          _changePlayCount(current_item.parent().find('.song_id').text());
          
        }, false);
			},
			flashName : "flashmediaelement.swf",
			// List features.
			features : profileFeature,
			// List key actions.
			keyActions : [],
			startVolume : 0.8,
			pauseOtherPlayers : true
		});
    
		// Select a song
		$(document).on('click', '.mp3music-song-title', function(){
			var mp3Container = $(this).closest('.mp3music_container');
			var strSongTitle = $(this).children('.song-title').text();
			var audio_src = $(this).parent().children(".link").text();
			
			$(this).parent().addClass('current').siblings().removeClass('current');
      mp3Container.find('#song-title-head').html(strSongTitle);
			
			var player = mp3Container.find('audio');
			
			//Iphone, Ipad error here
			//Delay play if is mobile
			//Break here
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
				player[0].pause();
				player[0].setSrc(audio_src);
				player[0].load();
				setTimeout(function(){player[0].play()},2000);
			} else {
				player[0].player.pause();
				player[0].player.setSrc(audio_src);
				player[0].player.load();
				player[0].player.play();
			}
		});
		
	$(document).on('click', '.mp3music_expand_thumb', function()
	{
		$(this).parent().css("display", "none");
		$(this).closest('.mp3music_wrapper').find('.younet_mp3music_feed').css({'position':'static', 'visibility':'visible'});
		var audio = $(this).closest('.mp3music_wrapper').find('audio');
		if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
			audio[0].load();
			setTimeout(function(){audio[0].play()},1000);
		} else {
			if(typeof audio[0].player == 'undefined') {
				//Re init mediaelment
				var mp3musicWrapper = $(this).closest('.mp3music_wrapper');
				
				if(mp3musicWrapper.find("li").size() == 1) 
				{
					mp3musicWrapper.find('audio').mediaelementplayer({
						success : function(mediaElement, domObject) 
						{
							console.log("Success");
							mediaElement.addEventListener('ended', function(e) 
							{
								mejsPlayNext(e.target, domObject);
							}, false);
							
							mediaElement.addEventListener('loadeddata', function(e) 
							{
								console.log("Loaded data");
								var currentItem = $(domObject).closest('.mp3music_container').find('li.current');
								_changePlayCount(currentItem.find('.song_id').text());
							}, false);
						},
						flashName : "flashmediaelement.swf",
						features : ['playpause', 'progress', 'duration', 'volume'],
						keyActions : [],
						autoplay: true,
						startVolume : 0.8,
						pauseOtherPlayers : true
					});
				}
				else 
				{
					mp3musicWrapper.find('audio').mediaelementplayer({
						success : function(mediaElement, domObject) 
						{
							mediaElement.addEventListener('ended', function(e) 
							{
								mejsPlayNext(e.target, domObject);
							}, false);
							mediaElement.addEventListener('loadeddata', function(e) 
							{
								var currentItem = $(domObject).closest('.mp3music_container').find('li.current');
								_changePlayCount(currentItem.find('.song_id').text());
							}, false);
						},
						flashName : "flashmediaelement.swf",
						features : feedFeature,
						keyActions : [],
						startVolume : 0.8,
						pauseOtherPlayers : true
					});
				}
				
				audio = $(this).closest('.mp3music_wrapper').find('audio');
				var audio_src = $(this).closest('.mp3music_wrapper').find(".current .link").text();
				setTimeout(function(){
					audio[0].player.pause();
					audio[0].player.setSrc(audio_src);
					audio[0].player.load();
					audio[0].player.play();
				},1000);
				
			} else {
				audio[0].player.load();
				audio[0].player.play();
			}
		}
	});

		$('#mp3music_rating a').hover(function(){
			if($(this).not('.mp3music_rate_disable').length >= 1) {
				// Get index
				cindex = $('#mp3music_rating a').index($(this));
				// Add class rated to this
				++cindex;
				for(var i = 1; i <= cindex; i++) {
					$('#rate_' + i).addClass('mp3music_rate_hover');
				}
			}
		}, function() {
			cindex = $('#mp3music_rating a').index($(this));
			++cindex;
			for(var i = 1; i <= cindex; i++) {
				$('#rate_' + i).removeClass('mp3music_rate_hover');
			}
		});
	$('.layout_mp3music_profile_music').css({'position':'absolute', 'visibility': 'hidden', 'display': 'block'});
	mp3music_init_scroll($(mp3ContainerList));
	$('.layout_mp3music_profile_music').css({'position':'static', 'visibility': 'visible', 'display': 'none'});
	});
	
	function mp3music_scroll(mp3Container, position) {
		var height = SCROLL_ITEM *(mp3Container.find('li.current').outerHeight());
		height = String(height) + 'px';
		if(mp3Container.find('.song-list li').size() > SCROLL_ITEM) {
			mp3Container.find('.song-list').slimScroll({
				color: '#222',
				size: '10px',
				height: height,
				alwaysVisible: true,
				scrollPos: position,
			});
		}
	}
	
	function mp3music_init_scroll(mp3ContainerList) {
		mp3ContainerList.each(function(index){
			var height = SCROLL_ITEM *($(this).find('li.current').outerHeight());
			height = String(height) + 'px';
			if($(this).find('.song-list li').size() > SCROLL_ITEM) {
				$(this).find('.song-list').slimScroll({
					color: '#222',
					size: '10px',
					height: height,
					alwaysVisible: true,
					scrollPos: 0,
					start: 'top',
				});
			}
		});
	}
	function changevote(value, isVote) {
		for (var x = 1; x <= parseInt(value); x++) {
			$('#rate_' + x).attr('class', 'mp3music_rated');
		}
		
		for (var x = parseInt(value) + 1; x <= 5; x++) {
			$('#rate_' + x).attr('class', 'mp3music_unrate');
		}
		
		var remainder = Math.round(value) - value;
		if (remainder <= 0.5 && remainder != 0) {
			var last = parseInt(value) + 1;
			$('#rate_' + last).attr('class', 'rating_star_big_generic rating_star_big_half');
		}
		
		if(isVote != 1) {
			for(x = 1; x <= 5; x++) {
				$('#rate_' + x).addClass('mp3music_rate_disable');
			}
		}
	}
	
})(jQuery);

