<?php
    $playlist = $this->playlist;
    $staticBaseUrl = $this->layout()->staticBaseUrl . 'application/modules/Ynvideochannel/externals/scripts/';
    $this->headLink()->appendStylesheet($staticBaseUrl . 'mediaelementplayer/mediaelementplayer.css')
        ->appendStylesheet($staticBaseUrl . 'masterslider/style/masterslider.css')
        ->appendStylesheet($staticBaseUrl . 'masterslider/style/ms-videogallery.css')
        ->appendStylesheet($staticBaseUrl . 'masterslider/skins/default/style.css');
    $this->headScript()->appendFile($staticBaseUrl . 'jquery-1.10.2.min.js')
        ->appendFile($staticBaseUrl . 'jquery.easing.min.js')
        ->appendFile($staticBaseUrl . 'mediaelementplayer/mediaelement-and-player.min.js')
        ->appendFile($staticBaseUrl . 'videoPlayer.js');
?>
<script type="text/javascript" src="<?php echo $staticBaseUrl?>masterslider.js"></script>
<div class="ynvideochannel_playlist_top_info">
    <span><?php echo $this->translate("Playing"); ?> :</span>
    <span id="current_video_title"></span>
</div>

<div id="ynvideochannel_playlist_slideshow" class="ms-videogallery-template  ms-videogallery-vertical-template">
    <div class="master-slider ms-skin-default" id="ynvideochannel_playlist_slideshow_masterslider">
        <a class="ynvideochannel_playbutton ynvideochannel_btn_playlist_pre" href="javascript:void(0)" onclick="ynvideochannelPrev();"><i class="fa fa-chevron-left"></i></a>
        <a class="ynvideochannel_playbutton ynvideochannel_btn_playlist_play" href="javascript:void(0)" onclick="ynvideochannelPlay();"><i class="fa fa-play"></i></a>
        <a class="ynvideochannel_playbutton ynvideochannel_btn_playlist_next" href="javascript:void(0)" onclick="ynvideochannelNext();"><i class="fa fa-chevron-right"></i></a>
        <div class="ynvideochannel_playlist_detail_actions">
            <a id="ynvideochannel_continue_button" data-status="auto_play" href="javascript:void(0);" title="<?php echo $this->translate('Autoplay')?>" onclick="ynvideochannelSwitch(this);" class="ynvideochannel_status_button">
                <i class="fa fa-play-circle"></i>
            </a>
            <a id="ynvideochannel_repeat_button" data-status="repeat" href="javascript:void(0);" title="<?php echo $this->translate('Repeat')?>" onclick="ynvideochannelSwitch(this);" class="ynvideochannel_status_button">
                <i class="fa fa-repeat"></i>
            </a>
            <a id="ynvideochannel_shuffle_button" data-status="shuffle" href="javascript:void(0);" title="<?php echo $this->translate('Shuffle')?>" onclick="ynvideochannelSwitch(this);" class="ynvideochannel_status_button">
                <i class="fa fa-random"></i>
            </a>
            <span>
                <?php echo $this->translate(array('%s video', '%s videos', $playlist->video_count), $this->locale()->toNumber($playlist->video_count)) ?>
            </span>
        </div>
        <?php foreach ($this->paginator as $video) : ?>
            <?php echo $this->partial('_playlist_slideshow_video_item.tpl', 'ynvideochannel', array('video' => $video))?>
        <?php endforeach; ?>
    </div>
</div>

<script type="text/javascript">
    function ynvideochannelSwitch(ele) {
        if ($(ele).hasClass('active')) {
            $(ele).removeClass('active');
        } else {
            $(ele).addClass('active');
        }
        var status = getPlayingStatus();
    }

    function getPlayingStatus() {
        var status = {
            auto_play: 0,
            repeat: 0,
            shuffle: 0
        };
        jQuery(".ynvideochannel_status_button").each(function(){
            status[jQuery(this).data('status')] = jQuery(this)[0].hasClass('active') ? 1 : 0;
        });
        return status;
    }

    function generateRandom(current, min, max) {
        // playlist contain only 1 video
        if (max == 1) {
            return 0;
        }
        var gen = Math.floor(Math.random() * (max - min + 1)) + min;
        while (gen == current) {
            gen = Math.floor(Math.random() * (max - min + 1)) + min;
        }
        return gen;
    }

    (function( $ ) {
        $(function() {
            var slider = new MasterSlider();
            var previousIndex = [0];

            slider.setup('ynvideochannel_playlist_slideshow_masterslider', {
                width : 854,
                height : 550,
                space : 0,
                loop : false,
                view : 'basic',
                swipe : false,
                mouse : false,
                speed : 100,
                autoplay : false
            });

            slider.control('arrows');
            slider.control('thumblist', {autohide : false,  dir : 'v',width:300,height:80,});

            // init Player
            var player = new ynvideochannelPlayer(slider);
            var $previous_playlist = $("");
            slider.api.addEventListener(MSSliderEvent.CHANGE_START , function(){
                var current = slider.api.index();
                previousIndex.push(current);
                player.removeAllPlayers();

                $previous_playlist.removeClass('active'); 
                $current = slider.api.view.currentSlide.$element;
                $current.addClass('active');
                $previous_playlist = $current;
            });

            slider.api.addEventListener(MSSliderEvent.CHANGE_END , function(){
                player.updateCurrentSlideInfo();
                player.init(false);
            });

            window.ynvideochannelPlay = function() {
                player.init(true);
            };

            window.ynvideochannelNext = function() {
                var min = 0;
                var total = slider.api.count();
                var current = slider.api.index();
                var status = getPlayingStatus();
                if (status.shuffle) {
                    slider.api.gotoSlide(generateRandom(current, min, total));
                } else if (status.repeat && current == total - 1) {
                    slider.api.gotoSlide(0);
                } else if (current < total - 1) {
                    slider.api.next();
                }
            };

            window.ynvideochannelAutoNext = function() {
                var status = getPlayingStatus();
                if (status.auto_play) {
                    ynvideochannelNext();
                }
            };

            window.ynvideochannelPrev = function() {
                var pos = previousIndex.pop();
                pos = previousIndex.pop();
                if (pos != null){
                    slider.api.gotoSlide(pos);
                } else {
                    slider.api.gotoSlide(0);
                }
            };
        });
    })(jQuery);
</script>
