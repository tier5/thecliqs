<script type="text/javascript">
function print_listing() {
    window.print();
}
</script>
<div class="ynlisting_detail_layout ynlisting_detail_layout_<?php echo $this->listing->theme; ?> clearfix">
    
<?php
      $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
?>   
<?php 
    $session = new Zend_Session_Namespace('mobile'); 
    if($session -> mobile):
?>
    <!-- Base MasterSlider style sheet -->
    <link rel="stylesheet" href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/masterslider.css" />
     
    <!-- Master Slider Skin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/masterslider-style.css" rel='stylesheet' type='text/css'>

    <!-- MasterSlider Template Style -->
    <link href='<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/ms-lightbox.css' rel='stylesheet' type='text/css'>

    <!-- Prettyphoto Lightbox jQuery Plugin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/prettyPhoto.css"  rel='stylesheet' type='text/css'/>    
     
    <!-- jQuery -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery-1.10.2.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery.easing.min.js"></script>
     
    <!-- Master Slider -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/masterslider.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery.prettyPhoto.js"></script>

    <div class="ynlisting_style_1">
        <div class="ynlisting-slider-master">
            <div class="ynlisting-tab-content">
            <?php if(count($this->photos) > 0):?>       
                <!-- template -->
                <div class="ynlisting-photo-details ms-lightbox-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                     <?php foreach($this->photos as $photo):?>
                        <?php if($this->listing->photo_id == $photo->file_id):?>
                             <div class="ms-slide">
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="dasdasdas<?php echo $photo->image_title; ?>"/> 
                                <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a href="<?php echo $photo->getPhotoUrl(); ?>" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $photo->image_title; ?>"></a>
                            </div>
                        <?php break; endif;?>
                    <?php endforeach;?> 
                    <?php foreach($this->photos as $photo):?>
                        <?php if($this->listing->photo_id != $photo->file_id):?>
                            <div class="ms-slide">
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="dasdasdas<?php echo $photo->image_title; ?>"/> 
                                <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a href="<?php echo $photo->getPhotoUrl(); ?>" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $photo->image_title; ?>"></a>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>
                <!-- end of template -->

                <script type="text/javascript">      
                    jQuery.noConflict();

                    var slider = new MasterSlider();
                    slider.setup('masterslider' , {
                        width: 800,
                        height: 360,
                        space: 5,
                        loop: true,
                        view: 'fade'
                    });
                    slider.control('arrows');  
                    slider.control('lightbox');
                    slider.control('thumblist' , {autohide:false ,dir:'h'});
                     
                    jQuery(document).ready(function(){
                        jQuery("a[rel^='prettyPhoto']").prettyPhoto();
                    });  
                </script>
            <?php else:?>
                <!-- no photo -->
                <div class="ynlisting-photo-details ms-lightbox-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                             <div class="ms-slide">
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_main.png" alt="<?php echo $this->translate('No Photo')?>"/> 
                                <img class="ms-thumb" src="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" alt="thumb" />
                                <a href="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $this->translate('No Photo')?>"></a>
                            </div>
                    </div>
                </div>
                <script type="text/javascript">      
                    jQuery.noConflict();

                    var slider = new MasterSlider();
                    slider.setup('masterslider' , {
                        width: 350,
                        height: 360,
                        space: 5,
                        loop: true,
                        view: 'fade'
                    });
                    slider.control('arrows');  
                    slider.control('lightbox');
                    slider.control('thumblist' , {autohide:false ,dir:'h'});
                     
                    jQuery(document).ready(function(){
                        jQuery("a[rel^='prettyPhoto']").prettyPhoto();
                    });  
                </script>
            <?php endif;?>

            <?php if(count($this->videos) > 0):?>
                <div class="ynlisting-video-details ms-lightbox-template" style="display: none;">
                    <div class="master-slider ms-skin-default" id="masterslider2">
                    <?php foreach($this->videos as $video):?>
                        <?php if($this->listing->video_id == $video->getIdentity()):?> 
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => 'video',
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <img class="ms-thumb" src="<?php echo $video->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                        <?php break; endif; ?>
                    <?php endforeach;?> 
                    <?php foreach($this->videos as $video):?>
                        <?php if($this->listing->video_id != $video->getIdentity()):?> 
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => 'video',
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <img class="ms-thumb" src="<?php echo $video->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>

            <?php else:?>
                <!-- no video -->
            <?php endif;?>
            </div>

            <?php if(count($this->videos) > 0):?>
            <div class="ynlisting-tab">
                <div class="ynlisting-tab-item active" data-id="ynlisting-photo-details"><span class="fa fa-picture-o"></span> Photo</div>
                <div class="ynlisting-tab-item" data-id="ynlisting-video-details"><span class="fa fa-video-camera"></span> Video</div>
            </div>
            <?php endif;?>
        </div>
        <script type="text/javascript">
        var active_slider2 = 0;
            $$('.ynlisting-tab .ynlisting-tab-item').addEvent('click', function(){
                $$('.ynlisting-tab-content > div').hide();
                $$('.ynlisting-tab-content').getElement( "."+this.get('data-id') ).show();

                $$('.ynlisting-tab .ynlisting-tab-item').removeClass('active');
                this.addClass('active');

                if (active_slider2 == 0) {
                    jQuery.noConflict();
                    (function( $ ) {
                        $(function() {
                            // More code using $ as alias to jQuery
                            var slider2 = new MasterSlider();
                            slider2.setup('masterslider2' , {
                                width: 800,
                                height: 360,
                                space: 5,
                                loop: true,
                                view: 'fade'
                            });
                            slider2.control('arrows');  
                            slider2.control('lightbox');
                            slider2.control('thumblist' , {autohide:false ,dir:'h'});
                        });
                    })(jQuery);

                    active_slider2 = 1;
                }
            });
        </script>

        <div class="ynlisting-detail-content">
            <div class="listing_title"><?php echo $this->listing -> title; ?></div>

            <div class="listing_review clearfix">
                <div class="listing_rating">
                    <?php echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $this->listing)); ?>

                    <span class="review">
                        <?php echo $this->listing->ratingCount().' '.$this->translate('review(s)')?>
                    </span>
                    
                    <?php if ($this->can_review){
                        echo $this->htmlLink(
                            array(
                                'route' => 'ynlistings_review',
                                'action' => 'create',
                                'listing_id' => $this->listing->getIdentity(),
                                'tab' => $this->identity,
                                'page' => $this->page
                            ),
                            '<span class="fa fa-pencil-square-o"></span>'.$this->translate('Add your Review'),
                            array(
                                'class' => 'listing-add-review smoothbox'
                            )
                        );
                    } else if ($this->has_review) {
                        echo '<span>'.$this->translate('You have revieweded.').'</span>';
                    }?>

                </div>
            </div>

            <div class="listing_category">
                <span class="fa fa-folder-open-o"></span>
               <?php $i = 0;  $category = $this->listing->getCategory();  ?>
               <?php if($category) :?>
                    <?php foreach($category->getBreadCrumNode() as $node): ?>
                        <?php if($node -> category_id != 1) :?>
                        <?php if($i != 0) :?>
                            &raquo; 
                        <?php endif;?>
                            <?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
                        <?php endif; ?>
                 <?php endforeach; ?>
                 <?php if($category -> parent_id != 0 && $category -> parent_id  != 1) :?>
                            &raquo; 
                 <?php endif;?>
                 <?php echo $this->htmlLink($category->getHref(), $category->title); ?>
                 <?php endif;?>
            </div>

            <div class="listing_currency">
                <?php echo $this -> locale()->toCurrency($this->listing->price, $this->listing->currency); ?>
            </div>

            <div class="listing_description rich_content_body"><?php echo $this->listing->short_description?></div>

            <div class="listing_contact">
                <a href="javascript:void(0)" onclick="print_listing()"><span class="fa fa-print"></span><?php echo $this->translate('Print Listing')?></a>
                <a href="<?php echo $this->listing->getHref()?>" ><span class="fa fa-chevron-left"></span><?php echo $this->translate('Back')?></a>
            </div>
        </div>
    </div> 
<?php else : ?>
<?php if ($this->listing->theme == 'theme1') : ?>
    <!-- Base MasterSlider style sheet -->
    <link rel="stylesheet" href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/masterslider.css" />
     
    <!-- Master Slider Skin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/masterslider-style.css" rel='stylesheet' type='text/css'>

    <!-- MasterSlider Template Style -->
    <link href='<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/ms-lightbox.css' rel='stylesheet' type='text/css'>

    <!-- Prettyphoto Lightbox jQuery Plugin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/prettyPhoto.css"  rel='stylesheet' type='text/css'/>    
     
    <!-- jQuery -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery-1.10.2.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery.easing.min.js"></script>
     
    <!-- Master Slider -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/masterslider.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery.prettyPhoto.js"></script>

    <div class="ynlisting_style_1">
        <div class="ynlisting-slider-master">
            <div class="ynlisting-tab-content">
            <?php if(count($this->photos) > 0):?>       
                <!-- template -->
                <div class="ynlisting-photo-details ms-lightbox-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                     <?php foreach($this->photos as $photo):?>
                        <?php if($this->listing->photo_id == $photo->file_id):?>
                             <div class="ms-slide">
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="dasdasdas<?php echo $photo->image_title; ?>"/> 
                                <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a href="<?php echo $photo->getPhotoUrl(); ?>" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="vbxcvcxvcx<?php echo $photo->image_title; ?>"></a>
                            </div>
                        <?php break; endif;?>
                    <?php endforeach;?> 
                    <?php foreach($this->photos as $photo):?>
                        <?php if($this->listing->photo_id != $photo->file_id):?>
                            <div class="ms-slide">
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="dasdasdas<?php echo $photo->image_title; ?>"/> 
                                <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a href="<?php echo $photo->getPhotoUrl(); ?>" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="vbxcvcxvcx<?php echo $photo->image_title; ?>"></a>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>
                <!-- end of template -->

                <script type="text/javascript">      
                    jQuery.noConflict();

                    var slider = new MasterSlider();
                    slider.setup('masterslider' , {
                        width: 350,
                        height: 360,
                        space: 5,
                        loop: true,
                        view: 'fade'
                    });
                    slider.control('arrows');  
                    slider.control('lightbox');
                    slider.control('thumblist' , {autohide:false ,dir:'h'});
                     
                    jQuery(document).ready(function(){
                        jQuery("a[rel^='prettyPhoto']").prettyPhoto();
                    });  
                </script>
            <?php else:?>
                <!-- no photo -->
                <div class="ynlisting-photo-details ms-lightbox-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                         <div class="ms-slide">
                            <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" alt="<?php echo $this->translate('No Photo')?>"/> 
                            <img class="ms-thumb" src="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" alt="thumb" />
                            <a href="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $this->translate('No Photo')?>"></a>
                        </div>
                    </div>
                </div>
                
                <script type="text/javascript">      
                    jQuery.noConflict();

                    var slider = new MasterSlider();
                    slider.setup('masterslider' , {
                        width: 350,
                        height: 360,
                        space: 5,
                        loop: true,
                        view: 'fade'
                    });
                    slider.control('arrows');  
                    slider.control('lightbox');
                    slider.control('thumblist' , {autohide:false ,dir:'h'});
                     
                    jQuery(document).ready(function(){
                        jQuery("a[rel^='prettyPhoto']").prettyPhoto();
                    });  
                </script>
            <?php endif;?>

            <?php if(count($this->videos) > 0):?>
                <div class="ynlisting-video-details ms-lightbox-template" style="display: none;">
                    <div class="master-slider ms-skin-default" id="masterslider2">
                    <?php foreach($this->videos as $video):?>
                        <?php if($this->listing->video_id == $video->getIdentity()):?> 
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => 'video',
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <img class="ms-thumb" src="<?php echo $video->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                        <?php break; endif; ?>
                    <?php endforeach;?> 
                    <?php foreach($this->videos as $video):?>
                        <?php if($this->listing->video_id != $video->getIdentity()):?> 
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => 'video',
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <img class="ms-thumb" src="<?php echo $video->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>

            <?php else:?>
                <!-- no video -->
            <?php endif;?>
            </div>

            <?php if(count($this->videos) > 0):?>
            <div class="ynlisting-tab">
                <div class="ynlisting-tab-item active" data-id="ynlisting-photo-details"><span class="fa fa-picture-o"></span> Photo</div>
                <div class="ynlisting-tab-item" data-id="ynlisting-video-details"><span class="fa fa-video-camera"></span> Video</div>
            </div>
            <?php endif;?>
        </div>
        <script type="text/javascript">
        var active_slider2 = 0;
            $$('.ynlisting-tab .ynlisting-tab-item').addEvent('click', function(){
                $$('.ynlisting-tab-content > div').hide();
                $$('.ynlisting-tab-content').getElement( "."+this.get('data-id') ).show();

                $$('.ynlisting-tab .ynlisting-tab-item').removeClass('active');
                this.addClass('active');

                if (active_slider2 == 0) {
                    jQuery.noConflict();
                    (function( $ ) {
                        $(function() {
                            // More code using $ as alias to jQuery
                            var slider2 = new MasterSlider();
                            slider2.setup('masterslider2' , {
                                width: 350,
                                height: 360,
                                space: 5,
                                loop: true,
                                view: 'fade'
                            });
                            slider2.control('arrows');  
                            slider2.control('lightbox');
                            slider2.control('thumblist' , {autohide:false ,dir:'h'});
                        });
                    })(jQuery);

                    active_slider2 = 1;
                }
            });
        </script>

        <div class="ynlisting-detail-content">
            <div class="listing_title"><?php echo $this->listing -> title; ?></div>

            <div class="listing_review clearfix">
                <div class="listing_rating">
                    <?php echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $this->listing)); ?>

                    <span class="review">
                        <?php echo $this->listing->ratingCount().' '.$this->translate('review(s)')?>
                    </span>
                </div>
            </div>

            <div class="listing_category">
                <span class="fa fa-folder-open-o"></span>
               <?php $i = 0;  $category = $this->listing->getCategory();  ?>
               <?php if($category) :?>
                    <?php foreach($category->getBreadCrumNode() as $node): ?>
                        <?php if($node -> category_id != 1) :?>
                        <?php if($i != 0) :?>
                            &raquo; 
                        <?php endif;?>
                            <?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
                        <?php endif; ?>
                 <?php endforeach; ?>
                 <?php if($category -> parent_id != 0 && $category -> parent_id  != 1) :?>
                            &raquo; 
                 <?php endif;?>
                 <?php echo $this->htmlLink($category->getHref(), $category->title); ?>
                 <?php endif;?>
            </div>

            <div class="listing_currency">
                <?php echo $this -> locale()->toCurrency($this->listing->price, $this->listing->currency); ?>
            </div>

            <div class="listing_description rich_content_body"><?php echo $this->listing->short_description?></div>
            <div class="listing_contact">
                <a href="javascript:void(0)" onclick="print_listing()"><span class="fa fa-print"></span><?php echo $this->translate('Print Listing')?></a>
                <a href="<?php echo $this->listing->getHref()?>" ><span class="fa fa-chevron-left"></span><?php echo $this->translate('Back')?></a>
            </div>
            
        </div>
    </div>    
<?php elseif ($this->listing->theme == 'theme2') : ?>
    <!-- Base MasterSlider style sheet -->
    <link rel="stylesheet" href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/masterslider.css" />
     
    <!-- Master Slider Skin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/masterslider-style.css" rel='stylesheet' type='text/css'>

    <!-- MasterSlider Template Style -->
    <link href='<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/ms-lightbox.css' rel='stylesheet' type='text/css'>

    <!-- Prettyphoto Lightbox jQuery Plugin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/prettyPhoto.css"  rel='stylesheet' type='text/css'/>    
     
    <!-- jQuery -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery-1.10.2.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery.easing.min.js"></script>
     
    <!-- Master Slider -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/masterslider.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery.prettyPhoto.js"></script>

    <div class="ynlisting_style_2">

        <div class="listing_category">
                <span class="fa fa-folder-open-o"></span>
               <?php $i = 0;  $category = $this->listing->getCategory();  ?>
               <?php if($category) :?>
                    <?php foreach($category->getBreadCrumNode() as $node): ?>
                        <?php if($node -> category_id != 1) :?>
                        <?php if($i != 0) :?>
                            &raquo; 
                        <?php endif;?>
                            <?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
                        <?php endif; ?>
                 <?php endforeach; ?>
                 <?php if($category -> parent_id != 0 && $category -> parent_id  != 1) :?>
                            &raquo; 
                 <?php endif;?>
                 <?php echo $this->htmlLink($category->getHref(), $category->title); ?>
                 <?php endif;?>
            </div>

        <div class="listing_theme2_info_top clearfix">
            <div class="listing_currency">
                <?php echo $this -> locale()->toCurrency($this->listing->price, $this->listing->currency); ?>
            </div>

            <div class="listing_title"><?php echo $this->listing -> title; ?></div>
        </div>

        <div class="ynlisting-detail-content">

            <div class="listing_review clearfix">
                <div class="listing_rating">
                    <?php echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $this->listing)); ?>

                    <span class="review">
                        <?php echo $this->listing->ratingCount().' '.$this->translate('review(s)')?>
                    </span>
                    
                </div>                
            </div>

            <div class="listing_description rich_content_body"><?php echo $this->listing->short_description?></div>
            <div class="listing_contact">
                <a href="javascript:void(0)" onclick="print_listing()"><span class="fa fa-print"></span><?php echo $this->translate('Print Listing')?></a>
                <a href="<?php echo $this->listing->getHref()?>" ><span class="fa fa-chevron-left"></span><?php echo $this->translate('Back')?></a>
            </div>
        </div>

        <div class="ynlisting-slider-master">
            <div class="ynlisting-tab-content">
            <?php if(count($this->photos) > 0):?>       
                <!-- template -->
                <div class="ynlisting-photo-details ms-lightbox-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                    <?php foreach($this->photos as $photo):?>
                    <?php if($this->listing->photo_id == $photo->file_id):?>
                        <div class="ms-slide">
                            <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
                            <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                            <a href="<?php echo $photo->getPhotoUrl(); ?>" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $photo->image_title; ?>"></a>
                        </div>
                    <?php break; endif; ?>
                    <?php endforeach;?>
                    <?php foreach($this->photos as $photo):?>
                        <?php if($this->listing->photo_id != $photo->file_id):?>
                            <div class="ms-slide">
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
                                <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a href="<?php echo $photo->getPhotoUrl(); ?>" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $photo->image_title; ?>"></a>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>

                <script type="text/javascript">      
                    jQuery.noConflict();

                    var slider = new MasterSlider();
                    slider.setup('masterslider' , {
                        width: 800,
                        height: 300,
                        space: 5,
                        loop: true,
                        view:'fade'
                    });
                    slider.control('arrows');  
                    slider.control('lightbox');
                    slider.control('thumblist' , {autohide:false ,dir:'h'});
                     
                    jQuery(document).ready(function(){
                        jQuery("a[rel^='prettyPhoto']").prettyPhoto();
                    });  
                </script>
            <?php else:?>
                <!-- no photo -->
                <div class="ynlisting-photo-details ms-lightbox-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                         <div class="ms-slide">
                            <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" alt="<?php echo $this->translate('No Photo')?>"/> 
                            <img class="ms-thumb" src="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" alt="thumb" />
                            <a href="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $this->translate('No Photo')?>"></a>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <?php if(count($this->videos) > 0):?>
                <div class="ynlisting-video-details ms-lightbox-template" style="display: none;">
                    <div class="master-slider ms-skin-default" id="masterslider2">
                     <?php foreach($this->videos as $video):?>
                        <?php if($this->listing->video_id == $video->getIdentity()):?> 
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => 'video',
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <img class="ms-thumb" src="<?php echo $video->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                        <?php break; endif; ?>
                    <?php endforeach;?> 
                    <?php foreach($this->videos as $video):?>
                        <?php if($this->listing->video_id != $video->getIdentity()):?> 
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => 'video',
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <img class="ms-thumb" src="<?php echo $video->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>
            <?php else:?>
                <!-- no video -->
            <?php endif;?>
            </div>

            <?php if(count($this->videos) > 0):?>
            <div class="ynlisting-tab">
                <div class="ynlisting-tab-item active" data-id="ynlisting-photo-details"><span class="fa fa-picture-o"></span> Photo</div>
                <div class="ynlisting-tab-item" data-id="ynlisting-video-details"><span class="fa fa-video-camera"></span> Video</div>
            </div>
            <?php endif; ?>
        </div>
        <script type="text/javascript">
            var active_slider2 = 0;            

            $$('.ynlisting-tab .ynlisting-tab-item').addEvent('click', function(){
                $$('.ynlisting-tab-content > div').hide();
                $$('.ynlisting-tab-content').getElement( "."+this.get('data-id') ).show();

                $$('.ynlisting-tab .ynlisting-tab-item').removeClass('active');
                this.addClass('active');

                if (active_slider2 == 0) {
                    (function( $ ) {
                        $(function() {
                            // More code using $ as alias to jQuery
                            var slider2 = new MasterSlider();

                            slider2.setup('masterslider2' , {
                                width: 800,
                                height: 300,
                                space:5,
                                loop:true,
                                view:'fade'
                            });
                            slider2.control('arrows');  
                            slider2.control('lightbox');
                            slider2.control('thumblist' , {autohide:false ,dir:'h'});
                        });
                    })(jQuery);                    

                    active_slider2 = 1;
                }
            });
        </script>

    </div>

<?php elseif ($this->listing->theme == 'theme3') : ?>
    <!-- Base MasterSlider style sheet -->
    <link rel="stylesheet" href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/masterslider.css" />
     
    <!-- Master Slider Skin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/masterslider-style.css" rel='stylesheet' type='text/css'>
      
    <!-- MasterSlider Template Style -->
    <link href='<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/styles/masterslider/ms-caro3d.css' rel='stylesheet' type='text/css'>
     
    <!-- jQuery -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery-1.10.2.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/jquery.easing.min.js"></script>
     
    <!-- Master Slider -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/masterslider.min.js"></script>
    
    <div class="ynlisting_style_3">
        <div class="ynlisting-slider-master">
            <div class="ynlisting-tab-content">
            <?php if(count($this->photos) > 0):?>       
                <!-- template -->
                <div class="ynlisting-photo-details ms-caro3d-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                    <?php foreach($this->photos as $photo):?>
                        <?php if($this->listing->photo_id == $photo->file_id):?>
                            <div class="ms-slide">
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
                            </div>
                        <?php break; endif; ?>
                    <?php endforeach;?>
                    <?php foreach($this->photos as $photo):?>
                        <?php if($this->listing->photo_id != $photo->file_id):?>
                            <div class="ms-slide">
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>
                <!-- end of template -->

                <script type="text/javascript">      
                    jQuery.noConflict();

                    var slider = new MasterSlider();
                    slider.setup('masterslider' , {
                        width:460,
                        height:270,
                        space:0,
                        loop:true,
                        view:'flow'
                    });
                     
                    slider.control('arrows');  
                </script>
            <?php else:?>
                <!-- no photo -->
                <div class="ynlisting-photo-details ms-lightbox-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                         <div class="ms-slide">
                            <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" alt="<?php echo $this->translate('No Photo')?>"/> 
                            <img class="ms-thumb" src="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" alt="thumb" />
                            <a href="application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $this->translate('No Photo')?>"></a>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <?php if(count($this->videos) > 0):?>
                <div class="ynlisting-video-details ms-caro3d-template" style="display: none;">
                    <div class="master-slider ms-skin-default" id="masterslider2">
                     <?php foreach($this->videos as $video):?>
                        <?php if($this->listing->video_id == $video->getIdentity()):?> 
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => 'video',
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                            
                        <?php break; endif; ?>
                    <?php endforeach;?>
                    <?php foreach($this->videos as $video):?>
                        <?php if($this->listing->video_id != $video->getIdentity()):?> 
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => 'video',
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynlistings/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>

            <?php else:?>
                <!-- no video -->
            <?php endif;?>
            </div>

            <?php if(count($this->videos) > 0):?>
            <div class="ynlisting-tab">
                <div class="ynlisting-tab-item active" data-id="ynlisting-photo-details"><span class="fa fa-picture-o"></span> Photo</div>
                <div class="ynlisting-tab-item" data-id="ynlisting-video-details"><span class="fa fa-video-camera"></span> Video</div>
            </div>
            <?php endif; ?>
        </div>

        <script type="text/javascript">
            var active_slider2 = 0;

            $$('.ynlisting-tab .ynlisting-tab-item').addEvent('click', function(){
                $$('.ynlisting-tab-content > div').hide();
                $$('.ynlisting-tab-content').getElement( "."+this.get('data-id') ).show();

                $$('.ynlisting-tab .ynlisting-tab-item').removeClass('active');
                this.addClass('active');

                if (active_slider2 == 0) {
                    (function( $ ) {
                        $(function() {
                            // More code using $ as alias to jQuery
                            var slider2 = new MasterSlider();

                            slider2.setup('masterslider2' , {
                                width:460,
                                height:270,
                                space:0,
                                loop:true,
                                view:'flow'
                            });                     
                            slider2.control('arrows'); 
                        });
                    })(jQuery);                    

                    active_slider2 = 1;
                }
            });
        </script>

        <div class="listing_title"><?php echo $this->listing -> title; ?></div>
        
        <div class="listing_currency">
            <?php echo $this -> locale()->toCurrency($this->listing->price, $this->listing->currency); ?>
        </div>

        <div class="listing_description rich_content_body"><?php echo $this->listing->short_description?></div>

        <div class="listing_review clearfix">
            <div class="listing_rating">
                <?php echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $this->listing)); ?>

                <span class="review">
                    <?php echo $this->listing->ratingCount().' '.$this->translate('review(s)')?>
                </span>
                
            </div>

            <div class="listing_category">
                <span class="fa fa-folder-open-o"></span>
               <?php $i = 0;  $category = $this->listing->getCategory();  ?>
               <?php if($category) :?>
                    <?php foreach($category->getBreadCrumNode() as $node): ?>
                        <?php if($node -> category_id != 1) :?>
                        <?php if($i != 0) :?>
                            &raquo; 
                        <?php endif;?>
                            <?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
                        <?php endif; ?>
                 <?php endforeach; ?>
                 <?php if($category -> parent_id != 0 && $category -> parent_id  != 1) :?>
                            &raquo; 
                 <?php endif;?>
                 <?php echo $this->htmlLink($category->getHref(), $category->title); ?>
                 <?php endif;?>
            </div>
        </div>

        <div class="listing_contact">
            <a href="javascript:void(0)" onclick="print_listing()"><span class="fa fa-print"></span><?php echo $this->translate('Print Listing')?></a>
            <a href="<?php echo $this->listing->getHref()?>" ><span class="fa fa-chevron-left"></span><?php echo $this->translate('Back')?></a>
        </div>
    </div>
<?php endif; ?>

</div>
<script type="text/javascript">
    //hot fix case window.print style (overwrite screen to all)
    document.getElement('head').getElements('link[rel=stylesheet]').set('media', 'all');   
    // hot fix layout style theme
    $$('.layout_page_ynlistings_index_print .layout_main').addClass('ynlisting_layout_<?php echo $this->listing->theme; ?>')
</script>
<?php endif; ?>