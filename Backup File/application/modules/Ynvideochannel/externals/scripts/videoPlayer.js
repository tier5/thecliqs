function ynvideochannelPlayer(slider) {
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
        jQuery(".ynvideochannel_status_button").each(function(){
            status[jQuery(this).data('status')] = jQuery(this)[0].hasClass('active') ? 1 : 0;
        });
        return status;
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
            jQuery('.ynvideochannel_btn_playlist_play').show();
        }
    };

    this.removeAllPlayers = function() {
        // remove previous player
        jQuery('.ynvideochannel-player').each(function() {
            if (jQuery(this)[0] && jQuery(this)[0].player) {
                jQuery(this)[0].player.remove();
            }
        });
    };

    this.play = function() {
        var player = jQuery('#player_' + currentVideoId);
        jQuery('.ynvideochannel_btn_playlist_play').hide();
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
                        ynvideochannelAutoNext();
                    }
                });
                mediaElement.addEventListener('pause', function() {
                    switchPlayState(false);
                });
            }
        });
    };
}
