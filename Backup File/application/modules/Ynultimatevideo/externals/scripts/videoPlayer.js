function ynultimatevideoPlayer(slider) {
    // @TODO consider using adapter for each type
    var currentVideoTitle = '';
    var currentVideoHref = '';
    var previousVideoId = 0;
    var currentVideoId = 0;
    var isPlaying = false;

    function switchPlayState(state) {
        isPlaying = state;
    }

    function getPlayingStatus() {
        var status = {
            continue: 0,
            repeat: 0,
            shuffle: 0
        };
        jQuery(".ynultimatevideo_status_button").each(function(){
            status[jQuery(this).data('status')] = jQuery(this)[0].hasClass('active') ? 1 : 0;
        });
        return status;
    }

    function fixMEJSCss() {
        jQuery('#player_' + currentVideoId).css('margin', '0');
        jQuery('.mejs-container').each(function(){
            jQuery(this).css('width','100%').css('height','100%');
        });
        jQuery('.mejs-layer').each(function(){
            jQuery(this).css('width','100%').css('height','100%');
        });
        jQuery('.mejs-controls').each(function(){
            jQuery(this).css('width','100%').css('bottom','0');
        });
    }

    this.updateCurrentSlideInfo = function() {
        var $slideItemEle = slider.api.view.currentSlide.$element;
        previousVideoId = currentVideoId;
        currentVideoId = $slideItemEle.find('.video_id').val();
        // update title
        currentVideoTitle = $slideItemEle.find('.title').val();
        jQuery("#current_video_title").text(currentVideoTitle);
        // update href
        currentVideoHref = $slideItemEle.find('.href').val();
        jQuery("#current_video_href").attr('href',currentVideoHref);
    };

    this.init = function(startPlaying) {
         //init playing or continue playing
        previousVideoId = currentVideoId;
        if (startPlaying || isPlaying) {
            this.play();
        } else {
            //var player = jQuery('#player_' + currentVideoId);
            //var videoType = player.data('type');
            //if (videoType == 3 || videoType == 5) {
            //    if (player[0].player) {
            //        player[0].player.pause();
            //        jQuery('.ynultimatevideo_btn_playlist_play').hide();
            //    }
            //} else {
            jQuery('.ynultimatevideo_btn_playlist_play').show();
            //}
        }
    };

    this.removeAllPlayers = function() {
        // remove previous player
        jQuery('.ynultimatevideo-player').each(function() {
            switch(jQuery(this).data('type')) {
                case 1:
                    if (jQuery(this)[0] && jQuery(this)[0].player) {
                        jQuery(this)[0].player.remove();
                    }
                    break;
                case 2:
                    var data = {method:'unload'};
                    var message = JSON.stringify(data);
                    jQuery(this)[0].contentWindow.postMessage(message,'*');
                    jQuery(this)[0].hide();
                    break;
                case 4:
                    jQuery(this)[0].hide();
                    jQuery(this).html('');
                    break;
                case 3:
                case 5:
                    break;
            }
        });
    };

    /**
     * 1 youtube
     * 2 vimeo
     * 3 uploaded
     * 4 dailymotion
     * 5 url
     **/
    this.play = function() {
        var player = jQuery('#player_' + currentVideoId);
        var videoType = player.data('type');
        jQuery('.ynultimatevideo_btn_playlist_play').hide();
        switch (videoType) {
            case 1:
                player.mediaelementplayer({
                    success: function(mediaElement, domObject) {
                        // set play state for slide change
                        mediaElement.addEventListener('canplay', function() {
                            mediaElement.play();
                        });
                        mediaElement.addEventListener('play', function() {
                            switchPlayState(true);
                        });
                        mediaElement.addEventListener('ended', function() {
                            // set this to overide last second pause action of player
                            if (mediaElement.played) {
                                switchPlayState(true);
                                ynultimatevideoAutoNext();
                            }
                        });
                        mediaElement.addEventListener('pause', function() {
                            switchPlayState(false);
                        });
                    }
                });
                break;
            case 4:
                player.show();
                var video_code = player.data('code');
                var dailymotion_iframe = '<div id="player_' + currentVideoId + '_iframe"></div>';
                player.html(dailymotion_iframe);
                var DMplayer = DM.player(document.getElementById('player_' + currentVideoId + '_iframe'), {
                    video: video_code,
                    width: '100%',
                    height: '100%',
                    params: {
                        autoplay: true
                    }
                });
                DMplayer.addEventListener('play', function(event){
                    switchPlayState(true);
                });
                DMplayer.addEventListener('pause', function(event){
                    switchPlayState(false);
                });
                DMplayer.addEventListener('ended', function(event){
                    switchPlayState(true);
                    ynultimatevideoAutoNext();
                });
                break;
            case 3:
            case 5:
                player.show();
                player.mediaelementplayer({
                    success: function(mediaElement, domObject) {
                        // set play state for slide change
                        switchPlayState(true);
                        fixMEJSCss();
                        mediaElement.play();
                        mediaElement.addEventListener('play', function() {
                            fixMEJSCss();
                            // when go back to mp4 playerplay evnet is trigger, check this to skip this event
                            if (mediaElement.currentTime > 0.5) {
                                switchPlayState(true);
                            }
                        });
                        mediaElement.addEventListener('ended', function() {
                            switchPlayState(true);
                            ynultimatevideoAutoNext();
                        });
                        mediaElement.addEventListener('pause', function() {
                            fixMEJSCss();
                            // when changing slide, pause event for url is triggered, use this check to skip at ending
                            if (mediaElement.currentTime > 0.5) {
                                switchPlayState(false);
                            }
                        });
                        mediaElement.addEventListener('timeupdate', function() {
                            fixMEJSCss();
                        });
                    }
                });
                break;
            case 2:
                var iframe = player[0];
                jQuery(iframe).show();
                var VimeoPlayer = $f(iframe);

                // When the player is ready, add listeners for pause, finish, and playProgress
                VimeoPlayer.addEvent('ready', function() {

                    var data = {method:'play'};
                    var message = JSON.stringify(data);
                    iframe.contentWindow.postMessage(message,'*');
                    switchPlayState(true);

                    VimeoPlayer.addEvent('pause', function(){
                        //switchPlayState(false);
                    });
                    VimeoPlayer.addEvent('finish', function(){
                        switchPlayState(true);
                        ynultimatevideoAutoNext();
                    });
                });
                break;
        }
    };
}
