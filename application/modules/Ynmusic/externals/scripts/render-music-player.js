window.addEvent('domready', function() {
    addEventForPlayBtn();
    renderPlayerInCookie();
});

function addEventForPlayBtn() {
	$$('.music-play-btn a i.fa').removeEvents('click');
	$$('.music-play-btn a i.fa').addEvent('click', function(){
		if (this.hasClass('fa-play')) {
			var subject = this.get('rel');
			var player = $('ynmusic-player');
			if (player && player.hasClass(subject) && this.hasClass('playing')) {
				player.play();
			}
			else {
				var parent = this.get('parent');
				if (parent){
					var li = $('player-song-'+subject);
					var parentObj = this.getParent('.music-item#'+parent);
					if (!parentObj) parentObj = $('music-detail-'+parent);
					if (parentObj) {
						var parentPlayBtn = parentObj.getElements('.music-play-btn.parent a i.fa');
					}
					if (player && player.hasClass(parent) && li && parentPlayBtn.length > 0 && parentPlayBtn[0].hasClass('playing')) {
						 playSong(subject); 
					}
					else {
						if (parentObj && parentPlayBtn.length > 0) {
							reloadPlayer(parentPlayBtn, parent, subject);
						}
						else {
							reloadPlayer(this, subject);
						}
					}
				}
				else reloadPlayer(this, subject);
			}
			
				
		}
		else if (this.hasClass('fa-pause')) {
			var subject = this.get('rel');
			var player = $('ynmusic-player');
			if (player && player.hasClass(subject)) {
				player.pause();
			}
		}	
	});
}

function reloadPlayer(obj, subject, object) {
    var request = new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/name/ynmusic.music-player',
        data : {
			format: 'html',
			subject: subject,
			song: object,
			play: true
		},
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        	var body = document.getElementsByTagName('body')[0];
        	if (responseElements.length != 0) {
	            $$('.layout_ynmusic_music_player').destroy();
	            Elements.from(responseHTML).inject(body);
	            eval(responseJavaScript);
	            $$('.music-play-btn a i.fa').removeClass('playing');
	            obj.addClass('playing');
	            var parent = $('ynmusic-player-render').getParent();
	        	if (parent && !parent.hasClass('mini-player')) {
	        		body.addClass('body-padding-bottom');
	        	}
	        }
	        else {
	        	var notice = new Element('div', {
	            	'class' : 'can-not-play-notice',
	            	text : en4.core.language.translate('ynmusic_can_not_play_item')
	            });
	            body.grab(notice, 'top');
	            notice.fade('in');
	            (function() {
	            	notice.fade('out').get('tween').chain(function() {
	            		notice.destroy();
	            	});
	        	}).delay(2000, notice);
	        }
        }
    });
    request.send();
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}
	
function renderPlayerInCookie() {
	var currentPlay = getCookie('player_current');
	var playlist = getCookie('player_playlist');
	if (currentPlay == '' || playlist == '') {
		return;
	}
	var request = new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/name/ynmusic.music-player',
        data : {
			format: 'html',
			subject: currentPlay,
			playlist: playlist
		},
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        	if (responseElements.length != 0) {
	            $$('.layout_ynmusic_music_player').destroy();
	            var body = document.getElementsByTagName('body')[0];
	            Elements.from(responseHTML).inject(body);
	            eval(responseJavaScript);
	            var parent = $('ynmusic-player-render').getParent();
	        	if (parent && !parent.hasClass('mini-player')) {
	        		body.addClass('body-padding-bottom');
	        	}
	        }
        }
    });
    request.send();
}
