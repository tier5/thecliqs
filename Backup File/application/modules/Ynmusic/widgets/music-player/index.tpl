<?php 
//for applying settings on backend
$settings = Engine_Api::_()->getApi('settings', 'core');
$zIndex = 3000;
if ($settings->getSetting('ynmusic.player.display', 0) == 1) $zIndex = 50;

$class = "mini-player";
if ($settings->getSetting('ynmusic.player.display', 0)) $class = "bottom-player";
$x = $settings->getSetting('ynmusic.x.value', 0)*100;
$y = $settings->getSetting('ynmusic.y.value', 0)*100;
?>

<style type="text/css">
.layout_ynmusic_music_player
{
	z-index: <?php echo $zIndex?>;
}
.layout_ynmusic_music_player.mini-player {
	z-index: <?php echo $zIndex?>;
	<?php if ($class == "mini-player") :?>
	<?php if ($x <= 50) :?>
	left: <?php echo $x?>%;
	<?php else:?>
	right: <?php echo (100 - $x)?>%;
	<?php endif;?>
	<?php if ($y <= 50) :?>
	top: <?php echo $y?>%;
	bottom: auto;
	<?php else:?>
	bottom: <?php echo (100 - $y)?>%;
	<?php endif;?>
	<?php endif;?>
}	
</style>

<div id="change-type-btn"></div>

<div id="ynmusic-player-render">
	<div class="song-info clearfix">
		<div class="song-photo">  
			<?php echo $this->itemPhoto($this->song, 'thumb.icon') ?>
		</div>
		<div id="player-control-button">
			<span id="player-previous" title="<?php echo $this->translate('Previous')?>"><i class="fa fa-backward"></i></span>
			<span id="player-play-pause" class="pause" title="<?php echo $this->translate('Play')?>"><i class="fa fa-play"></i></span>
			<span id="player-next" title="<?php echo $this->translate('Next')?>"><i class="fa fa-forward"></i></span>
		</div>

		<div class="song-title-option">
			<span id="song-title-head">
				<marquee behavior="scroll" direction="<?php echo ($this->layout()->orientation == 'right-to-left' ? 'right' : 'left' )?>" onmouseover="this.stop();" onmouseout="this.start();">
					<?php echo $this->song->getTitle();?>
				</marquee>
			</span>
			<div id="song-option">
				<ul id="option-list">
					<li class="option-item" id="list-song" title="<?php echo $this->translate('Song List')?>">
						<i class="fa fa-list"></i>
					</li>
					<li class="option-item" id="repeat-song" title="<?php echo $this->translate('Repeat')?>">
						<i class="fa fa-repeat"></i>
					</li>
					<li class="option-item<?php if (count($this->songs) <= 1) echo '-disabled disabled-btn'?>" id="suffer-song" title="<?php echo $this->translate('Suffer')?>">
						<i class="fa fa-random"></i>
					</li>
					<li class="option-item<?php if (!$this->song->isDownloadable()) echo '-disabled disabled-btn'?>" id="download-song" title="<?php echo $this->translate('Download')?>">
						<a download="<?php echo $this->song->getTitle().'.mp3'?>" href="<?php echo ($this->song->isDownloadable()) ? $this->song->getFilePath() : 'javascript:void(0)'?>"><i class="fa fa-download"></i></a>
					</li>
				</ul>
			</div>
		</div>
		
		<div class="player-mini-title">
			<?php echo $this->translate('Social Music')?>
		</div>
			
		<div>
			<audio id="ynmusic-player" rel="<?php echo $this->song->getGuid()?>" class="<?php echo $this->song->getGuid()?> <?php if ($this->subject) echo $this->subject->getGuid()?>" src="<?php echo $this->song->getFilePath();?>" type="audio/mp3" controls="controls"></audio>
		</div>


	</div>
	<div id="player-song-list" class="player-songs music-songs" style="clear:both;">
	<?php echo $this->partial('_player-song-list.tpl', 'ynmusic', array('songs' => $this->songs, 'playImg' => true));?>
	</div>
</div>

<script>
	var media_events = {
    	loadstart: 2, progress: 2, suspend: 2, abort: 2,
       	error: 2, emptied: 2, stalled: 2, play: 2, pause: 2,
       	loadedmetadata: 2, loadeddata: 2, waiting: 2, playing: 2,
       	canplay: 2, canplaythrough: 2, seeking: 2, seeked: 2,
       	timeupdate: 2, ended: 2, ratechange: 2, durationchange: 2, volumechange: 2
   	}
   	Element.NativeEvents = $merge(Element.NativeEvents, media_events);
   
	var media_properties = [
    	'videoWidth', 'videoHeight', 'readyState', 'autobuffer','error', 'networkState', 'currentTime', 'duration', 'paused', 'seeking','ended', 'autoplay', 'loop',  'controls', 'volume', 'muted',
    	'startTime', 'buffered', 'defaultPlaybackRate', 'playbackRate', 'played', 'seekable' // these 6 properties currently don't work in firefox     
   	];
   	
   	media_properties.each(function(prop){
      	Element.Properties.set(prop, {
         	set: function(value){
            	this[prop] = value;
         	},
         	get: function(){
            	return this[prop];
         	}
      	})
   });
	
	var reload = false;
	var newSong = false;
	if ($('ynmusic-player')) {
		jQuery('#ynmusic-player').mediaelementplayer({
			success: function (mediaElement, domObject) {
       			// add event listener
       			addEventForPlayer(); 
        		// call the play method
        		$$('.left-time').each(function(el) {el.destroy()});
        		reloadNextPrevious();
    		},
		});
	}
	
	window.addEvent('domready', function() {
		$$('.player-songs .action-link.remove').addEvent('click', function() {
			var li = this.getParent('li.song-item');
			var subject = li.getElement('.player-play-btn a i.fa').get('rel');
			if (subject == $('ynmusic-player').get('rel')) {
				playNext(false);
			}
			li.destroy();
			updatePlaylistInCookie();
			if ($('player-song-list').getElements('li.song-item').length <= 0) {
				$('ynmusic-player-render').destroy();
				updateCurrentPlayInCookie();
			} 			
		});
		
		$$('.player-play-btn a i.fa').addEvent('click', function() {
			if (this.hasClass('fa-pause')) {
				var subject = this.get('rel');
				var player = $('ynmusic-player');
				if (player && player.hasClass(subject)) {
					player.pause();
				}
			}
			else if (this.hasClass('fa-play')) {
				var subject = this.get('rel');
				var player = $('ynmusic-player');
				if (player && player.hasClass(subject)) {
					player.play();
				}
				else {
					playSong(subject);
				}
			}
		});
		
		$$('#option-list .option-item').addEvent('click', function () {
			if (this.hasClass('active')) {
				this.removeClass('active');
			}
			else {
				this.addClass('active');
			}
			
			var id = this.get('id');
			switch (id) {
				case 'list-song':
					$('player-song-list').toggle(300);
					break;
				case 'repeat-song':
					setCookieNoPath('player_mode', id);
					$('suffer-song').removeClass('active');
					break;
				case 'suffer-song':
					setCookieNoPath('player_mode', id);
					$('repeat-song').removeClass('active');
					break;
			}
			reloadNextPrevious();
		});
		
		$$('#player-play-pause').addEvent('click', function() {
			var player = $('ynmusic-player');
			if (this.hasClass('pause')) {
				player.play();
			}
			else {
				player.pause();
			}
		});
		
		$$('#player-next').addEvent('click', function() {
			playNext(true);
		});
		
		$$('#player-previous').addEvent('click', function() {
			playPrevious();
		});
		
		$$('#change-type-btn').addEvent('click', function() {
			if (!$('ynmusic-player-render')) return;
			var parent_div = $('ynmusic-player-render').getParent();
			var body = parent_div.getParent('body');
			if (parent_div.hasClass('mini-player')) {
				parent_div.removeClass('mini-player');
				body.addClass('body-padding-bottom');
				setCookieNoPath('player_type', 'bottom-player');
			}
			else {
				parent_div.addClass('mini-player');
				body.removeClass('body-padding-bottom');
				setCookieNoPath('player_type', 'mini-player');
			}
		});
		
		var playerType = '';
		playerType = getCookie('player_type');
		if (playerType == '') {
			playerType = '<?php echo $class;?>';
		}
		
		if ($('ynmusic-player-render')) {
			var parent_div = $('ynmusic-player-render').getParent();
			if (playerType == 'mini-player') {
				parent_div.addClass('mini-player');
			}
		}
			
		updatePlaylistInCookie();
		updateCurrentPlayInCookie();
		reloadCookieSettings();
	});
	
	function reloadCookieSettings() {
		var currentVolume = getCookie('player_volume');
		if (currentVolume != '') {
			var mediaElement = $('ynmusic-player');
			if (mediaElement) mediaElement.volume = currentVolume;
		}
		var currentMode = getCookie('player_mode');
		if (currentMode != '') {
			if ($(currentMode)) $(currentMode).click();
		}
	}
	
	function addEventForPlayer() {
		var mediaElement = $('ynmusic-player');
		var subject = mediaElement.get('rel');
		
		mediaElement.removeEvents('pause');
		mediaElement.addEvent('pause', function(e) {
    		updateOnView('pause');
			updateOnPlayer('pause');
			setCookieNoPath('player_status', 'pause');
		});
		
		mediaElement.removeEvents('play');
		mediaElement.addEvent('play', function(e) {
			updateOnView('play');
			updateOnPlayer('play');
			if (newSong == true) {
				updatePlayCount();
				newSong = false;
			}
			setCookieNoPath('player_status', 'play');
		});
		
		mediaElement.removeEvents('ended');
		mediaElement.addEvent('ended', function(e) {
			setCookieNoPath('player_time', 0);
			playNext(false);
		});
		
		mediaElement.removeEvents('timeupdate');
		mediaElement.addEvent('timeupdate', function(e) {
			var currentTime = this.currentTime;
			var duration = this.duration;
			var percent = currentTime*100/duration;
			updateWave(currentTime, percent);
			setCookieNoPath('player_time', currentTime);
		});
		
		mediaElement.removeEvents('volumechange');
		mediaElement.addEvent('volumechange', function(e) {
			var currentVolume = this.volume;
			setCookieNoPath('player_volume', currentVolume);
		});
		
		mediaElement.removeEvents('canplay');
		mediaElement.addEvent('canplay', function(e) {
			<?php if ($this->play) :?>
    		this.play();
    		<?php else:?>
			var currentTime = getCookie('player_time');
			if (currentTime == 0) {
				newSong = true;
			}
			else {
				this.currentTime = currentTime;
				if (getCookie('player_status') != 'pause') {
					setTimeout(function() {
						var newTime = getCookie('player_time');
						if (newTime == currentTime) {
							mediaElement.play();
						}
			      	}, 1000);
				}
			}
    		<?php endif;?>
    		mediaElement.removeEvents('canplay');
		});
		
	}
	
	function updateWave(currentTime, percent) {
		$$('.music-item.playing').each(function(el) {
			var parent = el.getElement('.image-song');
			if (parent && !parent.hasClass('on-drag')) {
				var play_div = el.getElement('.play-div');
				if (play_div) {
					play_div.setStyle('width', percent+'%');
				}
				var drag_btn = el.getElement('.drag-btn');
				if (drag_btn) {
					drag_btn.setStyle('left', percent+'%');
				}
			}
			var s = parseInt(currentTime % 60);
    		var m = parseInt((currentTime / 60) % 60);
    		if (s < 10) s = '0'+s;
    		if (m < 10) m = '0'+m;
			var time_div = el.getElement('.duration-time');
			if (time_div) {
				var left_div = el.getElement('.left-time'); 
				if (!left_div) {
					left_div = new Element('div', {
						'class':'left-time',
					});
				}
				left_div.innerHTML = m+':'+s;
				left_div.inject(time_div, 'before');
			}
		});
	}
	
	function reloadNextPrevious() {
		if (canNext() == false) {
			$$('#player-next').addClass('disabled-btn');
		}
		else {
			$$('#player-next').removeClass('disabled-btn');
		}
		if (canPrevious() == false) {
			$$('#player-previous').addClass('disabled-btn');
		}
		else {
			$$('#player-previous').removeClass('disabled-btn');
		}	
	}
	
	function canNext() {
		if (reload) return false;
		if ($('repeat-song').hasClass('active')) {
			return true;
		}
		var ul = $('player-song-list').getElement('.song-items');
		var player = $('ynmusic-player');
		var subject = player.get('rel');
		var li = $('player-song-'+subject);
		var index = ul.getChildren('li.song-item').indexOf(li);
		index = index+2;
		if (index > ul.getChildren('li.song-item').length) {
			return false;
		}
		return true;
	}
	
	function canPrevious() {
		if ($('repeat-song').hasClass('active')) {
			return true;
		}
		var ul = $('player-song-list').getElement('.song-items');
		var player = $('ynmusic-player');
		var subject = player.get('rel');
		var li = $('player-song-'+subject);
		var index = ul.getChildren('li.song-item').indexOf(li);
		if (index <= 0) {
			return false;
		}
		return true;
	}
	
	function playNext(click) {
		var ul = $('player-song-list').getElement('.song-items');
		if ($('suffer-song').hasClass('active')) {
			var length = ul.getChildren('li.song-item').length;
			var index = Math.floor((Math.random() * length) + 1);
		}
		else {
			var player = $('ynmusic-player');
			var subject = player.get('rel');
			var li = $('player-song-'+subject);
			var index = ul.getChildren('li.song-item').indexOf(li);
			index = index+2;
			if (index > ul.getChildren('li.song-item').length) {
				if ($('repeat-song').hasClass('active')) {
					index = 1;
				}
				else {
					if(click == true) {
						return false;
					}
					updateOnPlayer('pause');
					updateOnView('pause');
					index = 1;
					reload = true;
				}
			}
		}
		if (index == 1) var newLi = ul.getElement('li.song-item:first-child');
		else var newLi = ul.getElement('li.song-item:nth-child('+index+')');
		var newSubject = newLi.getElement('.player-play-btn a i.fa').get('rel');
		playSong(newSubject);
		reloadNextPrevious();
		return true;
	}
	
	function playPrevious() {
		var ul = $('player-song-list').getElement('.song-items');
		if ($('suffer-song').hasClass('active')) {
			var length = ul.getChildren('li.song-item').length;
			var index = Math.floor((Math.random() * length) + 1);
		}
		else {
			var player = $('ynmusic-player');
			var subject = player.get('rel');
			var li = $('player-song-'+subject);
			var index = ul.getChildren('li.song-item').indexOf(li);
			if (index <= 0) {
				if (index  == 0 && $('repeat-song').hasClass('active')) {
					index = ul.getChildren('li.song-item').length;
				}
				else return false;
			}
		}
		if (index == 1) var newLi = ul.getElement('li.song-item:first-child');
		else var newLi = ul.getElement('li.song-item:nth-child('+index+')');
		var newSubject = newLi.getElement('.player-play-btn a i.fa').get('rel');
		playSong(newSubject);
		reloadNextPrevious();
		return true;
	}
	
	function updateOnPlayer(action) {
		var player = $('ynmusic-player');
		var subject = player.get('rel');
		var nextAction = (action == 'play') ? 'pause' : 'play';
		if (action == 'play') {
			$$('.player-play-btn a i.fa').removeClass('fa-'+nextAction);
			$$('.player-play-btn a i.fa').addClass('fa-'+action);
			$('player-play-pause').removeClass('pause');
			$('player-play-pause').addClass('play');
			$('player-play-pause').set('title', '<?php echo $this->translate('Pause')?>');
			$('player-play-pause').innerHTML = '<i class="fa fa-pause"></i>';
			
		}
		else {
			$('player-play-pause').removeClass('play');
			$('player-play-pause').addClass('pause');
			$('player-play-pause').set('title', '<?php echo $this->translate('Play')?>');
			$('player-play-pause').innerHTML = '<i class="fa fa-play"></i>';
		}
		
		$$('#player-song-list li.song-item').removeClass('playing');
		$$('#player-song-'+subject).addClass('playing');
		$$('.player-play-btn.play-btn-'+subject+' a i.fa').removeClass('fa-'+action);
		$$('.player-play-btn.play-btn-'+subject+' a i.fa').addClass('fa-'+nextAction);
	}
	
	function updateOnView(action) {
		var player = $('ynmusic-player');
		var old_subject = $$('.music-item.playing');
		if (old_subject.length > 0) {
			old_subject = old_subject[0];
			if (old_subject.hasClass('music-detail')) {
				old_subject = old_subject.get('rel');
			}
			else old_subject = old_subject.get('id');
			
		}
		else {
			old_subject = '';
		}
		var nextAction = (action == 'play') ? 'pause' : 'play';
		var classList = player.classList;
		if (action == 'play') {
			if (!player.hasClass(old_subject)) {
				$$('.music-item.playing .play-div').setStyle('width', 0);
				
			}
			$$('.music-item.playing').removeClass('playing');
			$$('.music-play-btn a i.fa').removeClass('fa-'+nextAction);
			$$('.music-play-btn a i.fa').addClass('fa-'+action);
		}
		for (var i = 0; i < classList.length; i++) {
			var subject = classList[i];
			var btn = $$('.music-play-btn.play-btn-'+subject+' a i.fa.playing');
			if (btn.length > 0) {
				btn.removeClass('fa-'+action);
				btn.addClass('fa-'+nextAction);
				var parent = btn[0].getParent('.music-item');
				if (parent) {
					 var subject = player.get('rel');
					 parent.addClass('playing');
					 scrollInPlaylist(parent);
					 if (action == 'play') {
					 	parent.removeClass('pause');
					 }
					 else {
					 	parent.addClass('pause');
					 }
					 if (action == 'play') {
					 	 var li = $('player-song-'+subject);
						 var playImgSrc = li.getElement('.play-img').get('value');
						 var noPlayImgSrc = li.getElement('.noplay-img').get('value');
						 var playImg = parent.getElements('.play-div');
						 var noPlayImg = parent.getElements('.no-play-div');
						 if (playImg.length > 0 && noPlayImg.length > 0) {
				 			playImg[0].setStyle('background-image', 'url('+playImgSrc+')');
						 	noPlayImg[0].setStyle('background-image', 'url('+noPlayImgSrc+')');
						 }
					}
				}
			}
			else {
			<?php if ($this->subject) :?>
				var parentSubject = '<?php echo $this->subject->getGuid()?>';
				var parentBtn = $$('.music-play-btn.play-btn-'+parentSubject+' a i.fa.playing')[0];
				var li = parentBtn.getParent('.music-item');
				if (!li || li.hasClass('music-detail')) li = $('music-songs-detail');
				if (li) {
					li.getElements('.song-items .music-play-btn i.fa').removeClass('playing');
					var child = li.getElement('.song-items .music-play-btn.play-btn-'+subject+' i.fa');
					if (child) {
						child.removeClass('fa-'+action);
						child.addClass('fa-'+nextAction);
						child.addClass('playing');
						var parent = child.getParent('.music-item');
						if (parent) {
							parent.addClass('playing');
							scrollInPlaylist(parent);
						}
					}
				}
			<?php endif;?>
			}
		}
		updateSongDuration();
	}
	
	function playSong(subject) {
		var li = $('player-song-'+subject);
		if (li) {
			var imgSrc = li.getElement('img').get('src');
			var title = li.getElement('.title a').get('text');
			var path = li.get('rel');
			var canDownload = li.getElement('.action .download');
			var div = $('ynmusic-player-render');
			
			if (div) {
				var songPhoto = div.getElement('.song-photo img');
				songPhoto.set('src', imgSrc);
				var songTitle = div.getElement('#song-title-head marquee');
				songTitle.set('text', title);
				
				var songDownload = div.getElement('#download-song');
				if (canDownload) {
					songDownload.removeClass('option-item-disabled');
					songDownload.removeClass('disabled-btn');
					songDownload.addClass('option-item');
					songDownload.getElement('a').set('href', path);	
					songDownload.getElement('a').set('download', title+'.mp3');					
				}
				else {
					songDownload.addClass('option-item-disabled');
					songDownload.addClass('disabled-btn');
					songDownload.removeClass('option-item');
					songDownload.getElement('a').set('href', 'javascript:void(0)');
					songDownload.getElement('a').set('download', '');
				}
				songDownload.getElement('a').set('download', title);
				
				var player = $('ynmusic-player');
				player.set('src', path);
				var oldSubject = player.get('rel');
				player.set('rel', subject);
				player.removeClass(oldSubject);
				player.addClass(subject);
				addEventForPlayer();
				updateCurrentPlayInCookie();
				if (reload == false) {
					$$('.left-time').each(function(el) {el.destroy()});
					player.play();
				}
				else {
					reload = false;
				}
				newSong = true;
			}
		}
		else {
			
		}
	}
	
	function updatePlaylistInCookie() {
		var ids = [];
		$$('#player-song-list .song-items .song-item').each(function(el) {
			var id = el.get('song_id');
			if (ids.indexOf(id) == -1) ids.push(id);
		});
		setCookieNoPath('player_playlist', ids.join());
	}
	
	function updateCurrentPlayInCookie() {
		var subject = '';
		var player = $('ynmusic-player');
		if (player) subject = player.get('rel');
		setCookieNoPath('player_current', subject);
	}
	
	function setCookieNoPath(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + "; " + expires+"; path=/";;
	}
	
	function scrollInPlaylist(obj) {
		var ul = obj.getParent('.music-items');
		if (ul) {
			var myFx = new Fx.Scroll(ul).toElement(obj);
		}
	}
	
	function updatePlayCount() {
		var player = $('ynmusic-player');
		var subject = player.get('rel');
		var url = '<?php echo $this->url(array('action' => 'update-play-count'), 'ynmusic_general', true) ?>';
		var request = new Request.HTML({
	      url : url,
	      data : {
	        'subject': subject,
	      }
	    });
		request.send();
	}
	
	function updatePlayerTime(percent) {
		var player = $('ynmusic-player');
		var duration = player.duration;
		var time = percent*duration/100;
		var time = time.toFixed(20);
		player.currentTime = time;
	}
	
	function updateSongDuration() {
		var player = $('ynmusic-player');
		if (player) {
			var subject = player.get('rel');
			var li = $('player-song-'+subject);
			var duration = li.getElement('.duration').get('value');
			duration = parseInt(duration);
			$$('.music-item.playing').each(function(el) {
				var div = el.getElement('.duration-time');
				if (div) {
					var s = parseInt(duration % 60);
		    		var m = parseInt((duration / 60) % 60);
		    		if (s < 10) s = '0'+s;
		    		if (m < 10) m = '0'+m;
					div.innerHTML = m+':'+s;
				}
				
				var title_div = el.getElement('.playing-song-title');
				if (title_div) {
					var div_playing = el.getElement('.music-item.playing');
					if (div_playing) {
						var count = div_playing.getElement('.song-count').get('text');
						var title = div_playing.getElement('.title-artist .title a').get('text');
						title_div.innerHTML = count+'. '+title;
					}
					else {
						div_playing = $('player-song-'+subject);
						var count = div_playing.getElement('.count').get('text');
						var title = div_playing.getElement('.title a').get('text');
						title_div.innerHTML = count+' '+title;
					}
				}
			});
		}
	}
</script>
