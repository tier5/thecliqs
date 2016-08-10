<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
 ?>

<!-- Base MasterSlider style sheet -->
<link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/style/masterslider.css" />
 
<!-- Master Slider Skin -->
<link href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/skins/default/style.css" rel='stylesheet' type='text/css'>

<!-- MasterSlider Template Style -->
<link href='<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/style/ms-tabs-style.css' rel='stylesheet' type='text/css'>
 
<!-- jQuery -->
<script src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/jquery.easing.min.js"></script>
 
<!-- Master Slider -->
<script src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/masterslider.js"></script>


<div id="ynultimatevideo_featured" class="ms-tabs-template <?php echo ($this->vertcialThumbnails == 1) ? 'ms-tabs-vertical-template' : ''; ?> ynultimatevideo">
<!-- masterslider -->
    <div class="master-slider ms-skin-default" id="ynultimatevideo_featured_masterslider">
        <?php foreach ($this->videos as $index => $video) : ?>
            <?php
                echo $this->partial('_video_featured.tpl', 'ynultimatevideo', array('video' => $video));
            ?>
        <?php endforeach; ?>
    </div>
</div>


<script type="text/javascript">      
    (function( $ ) {
        $(function() {
            var slider = new MasterSlider();

            if ( <?php echo $this->vertcialThumbnails; ?> == 1) {

                slider.control('arrows');  
                slider.control('circletimer' , {color:"#FFFFFF" , stroke:9});
                slider.control('thumblist' , {autohide:false ,dir:'v',type:'tabs', align:'right', margin:0, space:0, width:170, height:100, hideUnder:550});
             
                slider.setup('ynultimatevideo_featured_masterslider' , {
                    // width:720,
                    width: 1000,
                    // height:450,
                    height: 600,
                    space:0,
                    autoplay:false,
                    loop: true,
                    view:'basic'
                });
            }

            else{
                slider.control('arrows');  
                slider.control('circletimer' , {color:"#FFFFFF" , stroke:9});
                slider.control('thumblist' , {autohide:false ,dir:'h', type:'tabs',width:165,height:115, align:'bottom', space:0 , margin:-12, hideUnder:400});
             
                slider.setup('ynultimatevideo_featured_masterslider' , {
                    width:1140,
                    height:580,
                    space:0,
                    autoplay:false,
                    loop: true,
                    preload:'all', 
                    view:'basic'
                });               
            }
             
        });
    })(jQuery);     
</script>


