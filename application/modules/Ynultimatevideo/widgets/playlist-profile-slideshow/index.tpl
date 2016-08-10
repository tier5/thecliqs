<!-- Base MasterSlider style sheet -->
<link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/style/masterslider.css" />

<!-- Master Slider Skin -->
<link href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/skins/default/style.css" rel='stylesheet' type='text/css'>

<!-- MasterSlider Template Style -->
<link href='<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/style/ms-videogallery.css' rel='stylesheet' type='text/css'>

<!-- jQuery -->
<script src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/jquery.easing.min.js"></script>

<!-- Master Slider -->
<script src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/masterslider.js"></script>

<?php
    $staticBaseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/mediaelementplayer/mediaelementplayer.css');
$this->headScript()->appendFile($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/mediaelementplayer/mediaelement-and-player.min.js')
->appendFile($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/videoPlayer.js')
->appendFile('https://api.dmcdn.net/all.js')
->appendFile($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/froogaloop2.min.js');
?>

<div class="ynultimatevideo_playlist_top_info">
    <span><?php echo $this->translate("Playing"); ?> :</span>
    <span id="current_video_title"></span>
    <a id="current_video_href" href="javascript:void(0);"><i class="fa fa-external-link"></i>   <?php echo $this->translate("view video"); ?></a>
</div>


<div id="ynultimatevideo_playlist_slideshow" class="ms-videogallery-template  ms-videogallery-vertical-template">
    <div class="master-slider ms-skin-default" id="ynultimatevideo_playlist_slideshow_masterslider">
        <a class="ynultimatevideo_playbutton ynultimatevideo_btn_playlist_pre" href="javascript:void(0)" onclick="ynultimatevideoPrev();"><i class="fa fa-angle-left"></i></a>
        <a class="ynultimatevideo_playbutton ynultimatevideo_btn_playlist_play" href="javascript:void(0)" onclick="ynultimatevideoPlay();"><i class="fa fa-play"></i></a>
        <a class="ynultimatevideo_playbutton ynultimatevideo_btn_playlist_next" href="javascript:void(0)" onclick="ynultimatevideoNext();"><i class="fa fa-angle-right"></i></a>
        <div class="ynultimatevideo_playlist_detail_actions">
            <a id="ynultimatevideo_continue_button" data-status="auto_play" href="javascript:void(0);" title="<?php echo $this->translate('Autoplay')?>" onclick="ynultimatevideoSwitch(this);" class="ynultimatevideo_status_button">
                <i class="fa fa-play-circle"></i>
            </a>
            <a id="ynultimatevideo_repeat_button" data-status="repeat" href="javascript:void(0);" title="<?php echo $this->translate('Repeat')?>" onclick="ynultimatevideoSwitch(this);" class="ynultimatevideo_status_button">
                <i class="fa fa-repeat"></i>
            </a>
            <a id="ynultimatevideo_shuffle_button" data-status="shuffle" href="javascript:void(0);" title="<?php echo $this->translate('Shuffle')?>" onclick="ynultimatevideoSwitch(this);" class="ynultimatevideo_status_button">
                <i class="fa fa-random"></i>
            </a>
            <span>
                <?php echo $this->translate(array('%1$s video', '%1$s videos', $this->playlist->getVideoCount()), $this->locale()->toNumber($this->playlist->getVideoCount())) ?>
            </span>
        </div>
        <?php foreach ($this->paginator as $video) : ?>
        <?php echo $this->partial('_video_playlist_slideshow.tpl', 'ynultimatevideo', array('video' => $video))?>
        <?php endforeach; ?>
    </div>
</div>

<script type="text/javascript">
    window.addEvent('domready', function() {
        var playerCookie = getCookie('ynultimatevideo_player_status');
        if (playerCookie) {
            var playerStatus = JSON.parse(playerCookie);
            jQuery(".ynultimatevideo_status_button").each(function(){
                if (playerStatus[jQuery(this).data('status')]) {
                    jQuery(this)[0].addClass('active');
                }
            });
        }

        DM.init({
            apiKey: 'ef845c2ccc263f065860',
            status: true, // check login status
            cookie: true // enable cookies to allow the server to access the session
        });
    });

    function ynultimatevideoSwitch(ele) {
        if ($(ele).hasClass('active')) {
            $(ele).removeClass('active');
        } else {
            $(ele).addClass('active');
        }
        var status = getPlayingStatus();
        setCookie('ynultimatevideo_player_status', JSON.stringify(status));
    }

    function getPlayingStatus() {
        var status = {
            auto_play: 0,
            repeat: 0,
            shuffle: 0
        };
        jQuery(".ynultimatevideo_status_button").each(function(){
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

            slider.setup('ynultimatevideo_playlist_slideshow_masterslider', {
                width : 854,
                height : 476,
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
            var player = new ynultimatevideoPlayer(slider);

            slider.api.addEventListener(MSSliderEvent.CHANGE_START , function(){
                var current = slider.api.index();
                previousIndex.push(current);
                player.removeAllPlayers();
            });

            slider.api.addEventListener(MSSliderEvent.CHANGE_END , function(){
                player.updateCurrentSlideInfo();
                player.init(false);
            });

            window.ynultimatevideoPlay = function() {
                player.init(true);
            };

            window.ynultimatevideoNext = function() {
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

            window.ynultimatevideoAutoNext = function() {
                var status = getPlayingStatus();
                if (status.auto_play) {
                    ynultimatevideoNext();
                }
            };

            window.ynultimatevideoPrev = function() {
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
