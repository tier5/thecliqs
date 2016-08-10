<link rel="stylesheet" href="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/styles/masterslider.css" />
<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/scripts/jquery-1.10.2.min.js"></script>
<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/scripts/jquery.easing.min.js"></script>
<script src="<?php echo $this->layout() -> statisBaseUrl?>application/modules/Ynvideochannel/externals/scripts/masterslider.js"></script>

<div class="ms-partialview-template ynvideochannel_featured_videos" id="partial-view-1">
    <div class="master-slider ms-skin-default" id="ynvideochannel_featured_videos">
        <?php foreach($this -> paginator as $item):?>
            <?php $poster = $item->getOwner(); ?>
            <?php $photo_url = ($item->getPhotoUrl('thumb.main')) ? $item->getPhotoUrl('thumb.main') : 'application/modules/Ynvideochannel/externals/images/nophoto_video_thumb_normal.png'; ?>
            <div class="ms-slide">
                <a href="http://www.youtube.com/embed/<?php echo $item -> code;?>" data-type="video"></a>
                <div class="ynvideochannel_featured_videos-block">
                    <div class="ynvideochannel_featured_videos-background" style="background-image: url(<?php echo $photo_url?>)">
                        <div class="ynvideochannel_featured_videos-background-info">
                            <a class="ynvideochannel_featured_videos-title" href="<?php echo $item -> getHref()?>"><?php echo $item -> getTitle()?></a>

                            <div class="ynvideochannel_featured_videos-options">
                                <?php echo $this->partial('_video_options.tpl', 'ynvideochannel', array('video' => $item)); ?>
                                <?php echo $this->partial('_add_to_menu.tpl','ynvideochannel', array('video' => $item)); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ynvideochannel_featured_videos-info">
                        <div class="ynvideochannel_featured_videos-info-owner">
                            <?php echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon'), array()) ?>
                        </div>
                        
                        <div class="ynvideochannel_featured_videos-info-text">
                            <div class="ynvideochannel_featured_videos-ownername">
                                <span><?php echo $this->translate('by') ?></span>
                                <?php echo $this -> translate("%s", $item -> getOwner())?>
                            </div>
                            
                            <div class="ynvideochannel_featured_videos-count">
                                <span><?php echo $this -> translate(array("%s like", "%s likes", $item -> view_count), $item -> like_count)?></span>
                                &nbsp;.&nbsp;
                                <span><?php echo $this -> translate(array("%s comment", "%s comments", $item -> view_count), $item -> comment_count)?></span>
                                &nbsp;.&nbsp;
                                <span><?php echo $this -> translate(array("%s favorite", "%s favorites", $item -> view_count), $item -> favorite_count)?></span>
                                &nbsp;.&nbsp;
                                <span><?php echo $this -> translate(array("%s view", "%s views", $item -> view_count), $item -> view_count)?></span>
                            </div>
                        </div>
                        
                        <div class="ynvideochannel_featured_videos-rating-duration">
                            <div class="ynvideochannel_featured_videos-duration">
                                <?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $item)); ?>
                            </div>

                            <div class="ynvideochannel_featured_videos-rating ynvideochannel_videos_rating">
                                <?php echo $this->partial('_video_rating.tpl', 'ynvideochannel', array('rating' => $item->rating)); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </div>
</div>


<script type="text/javascript">
    ynvideochannelVideoOptions();
    //function check IE
    function GetIEVersion() {
      var sAgent = window.navigator.userAgent;
      var Idx = sAgent.indexOf("MSIE");

      // If IE, return version number.
      if (Idx > 0){
        return parseInt(sAgent.substring(Idx+ 5, sAgent.indexOf(".", Idx)));
      }

      // If IE 11 then look for Updated user agent string.
      else if (!!navigator.userAgent.match(/Trident\/7\./)){
        return 11;
      }

      else{
        return 0; //It is not IE
      }
    };


    var space;

    if(GetIEVersion() > 0){
        space = 20;
    }else{
        space = 450;
    }
    
    //INIT SLIDER
    var slider = new MasterSlider();
    slider.control('arrows');  
    slider.control('circletimer' , {color:"#FFFFFF" , stroke:9});
 
    slider.setup('ynvideochannel_featured_videos' , {
        width:540,
        height:380,
        // space:450,
        space: space,
        autoplay: true,
        loop:true,
        speed: 100,
        fillMode: "fill",
        view:'partialWave',
        layout:'partialview'

    });

    //CHANGE START
    $previous = jQuery('');
    slider.api.addEventListener(MSSliderEvent.CHANGE_START , function(){
        $previous.find('.ynvideochannel_featured_videos-block').removeClass('active'); 
        $current = slider.api.view.currentSlide.$element;
        $current.find('.ynvideochannel_featured_videos-block').addClass('active');
        $previous = $current;
    });


    jQuery(document).on('click', '#playlist_title', function(e){this.focus()});
</script>