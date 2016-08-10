<?php
    $this->headLink() 
    ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/styles/magnific-popup.css');

    $this->headScript()    
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/jquery.min.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/jquery.magnific-popup.js');
?>
<script type="text/javascript">
	jQuery.noConflict();
    //check open popup
    function checkOpenPopup(url) {
        if(window.innerWidth <= 480) {
            Smoothbox.open(url, {autoResize : true, width: 300});
        }
        else {
            Smoothbox.open(url);
        }
    }
    
    function renderSection(type, params) {
    	if ($('sections-content-item_'+type)) {
            var content = $('sections-content-item_'+type).getElement('.ynresume-section-content');
            var loading = $('sections-content-item_'+type).getElement('.ynresume_loading');
            if (loading) {
                loading.show();
            }
            if (content) {
                content.hide();
            }
        }
        var resume_id = <?php echo $this->resume->getIdentity();?>;
        var url = '<?php echo $this->url(array('action' => 'render-section', 'resume_id' => $this->resume->getIdentity()), 'ynresume_specific', true)?>';
        var data = {};
        params.view = true;
        data.type = type;
        data.params = params;
        var request = new Request.HTML({
            url : url,
            data : data,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                elements = Elements.from(responseHTML);
                if (elements.length > 0) {
                    if ($('sections-content-item_'+type)) {
                        var content = $('sections-content-item_'+type).getElements('.ynresume-section')[0];
                        content.empty();
                        content.adopt(elements);
                    } else {
                        var li = new Element('li', {
                            'class': 'sections-content-item',
                            id: 'sections-content-item_'+type,
                        });
                        var label = new Element('label', {
                           'class': 'order',
                           text: 'Move' 
                        });
                        var div = new Element('div', {
                            'class': 'ynresume-section'
                        })
                        div.adopt(elements);
                        li.grab(label);
                        li.adopt(div);
                        $('sections-content-items').grab(li);
                    }
                    eval(responseJavaScript);
                    addEventToForm();
                }
            }
        });
        request.send();
    }
    
    function addEventToForm() {
        $$('.show-hide-recommendations-btn').removeEvents('click');
        $$('.show-hide-recommendations-btn').addEvent('click', function() {
            var list = this.getParent('.occupation-recommendations').getElement('.recomendation-list');
            if (list) list.toggle();
        });
        
        $$('.show-hide-photos-btn').removeEvents('click');
        $$('.show-hide-photos-btn').addEvent('click', function() {
            var list = this.getParent('.section-photos').getElement('.photo-lists');
            if (list) list.toggle();
        });
        
        //for show more/show less photos
        $$('.view-more-photos').removeEvents('click');
        $$('.view-more-photos').addEvent('click', function() {
            var div = this.getParent('.section-photos');
            if (div) {
            	div.getElements('.photo-lists li.view-more').setStyle('display','inline-block');
            	div.getElements('.view-more-photos').hide();
        		div.getElements('.view-less-photos').show();
            }
        });
        
        $$('.view-less-photos').removeEvents('click');
        $$('.view-less-photos').addEvent('click', function() {
            var div = this.getParent('.section-photos');
            if (div) {
            	div.getElements('.photo-lists li.view-more').hide();
            	div.getElements('.view-more-photos').show();
        		div.getElements('.view-less-photos').hide();
            }
        });       

        jQuery.noConflict();
        jQuery('a[data-lightbox-gallery]').magnificPopup({
            type: 'image',
            closeOnContentClick: true,
            closeBtnInside: false,
            fixedContentPos: true,
            mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
            image: {
                verticalFit: true
            },
            zoom: {
                enabled: false,
                duration: 300 // don't foget to change the duration also in CSS
            }
        });
    }
    
    window.addEvent('domready', function() {
        addEventToForm();
    });
</script>
<?php 
$allSections = Engine_Api::_()->ynresume()->getAllSectionsAndGroups();
if (isset($allSections['general_info'])) unset($allSections['general_info']);
$settings = Engine_Api::_()->getApi('settings', 'core');
$theme = $this->resume->theme;
?>

<?php if (!Engine_Api::_()->ynresume()->isMobile()) : ?>
<style type="text/css">
<?php foreach ($allSections as $key => $section) : ?>
    <?php if($key != 'photo') :?>
        /*** style 1 ***/
        #ynresume-view-sections-content.ynresume-detail-section-theme_1 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section h3.section-label,
        #ynresume-view-sections-content.ynresume-detail-section-theme_1 #sections-content-items #sections-content-item_<?php echo $key?> .ynresume-section-skill-mini-item .add-endorse-btn,
        #ynresume-view-sections-content.ynresume-detail-section-theme_1 #sections-content-items #sections-content-item_<?php echo $key?> .ynresume-section-skill-item .add-endorse-btn {
            color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;       
        }

        #ynresume-view-sections-content.ynresume-detail-section-theme_1 #sections-content-items #sections-content-item_<?php echo $key?> .ynresume-section-skill-mini-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-text,
        #ynresume-view-sections-content.ynresume-detail-section-theme_1 #sections-content-items #sections-content-item_<?php echo $key?> .ynresume-section-skill-mini-item .ynresume-section-skill-user > a:hover:after,
        #ynresume-view-sections-content.ynresume-detail-section-theme_1 #sections-content-items #sections-content-item_<?php echo $key?> .ynresume-section-skill-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-text,
        #ynresume-view-sections-content.ynresume-detail-section-theme_1 #sections-content-items #sections-content-item_<?php echo $key?> .ynresume-section-skill-item .ynresume-section-skill-user > a:hover:after {
            border-color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
        }

        #ynresume-view-sections-content.ynresume-detail-section-theme_1 #sections-content-items #sections-content-item_<?php echo $key?> .ynresume-section-skill-mini-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-count,
        #ynresume-view-sections-content.ynresume-detail-section-theme_1 #sections-content-items #sections-content-item_<?php echo $key?> .ynresume-section-skill-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-count {
            background-color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
        }

        /*** style 2 ***/     
        #ynresume-view-sections-content.ynresume-detail-section-theme_2 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-mini-item .ynresume-section-skill-user a:after,   
        #ynresume-view-sections-content.ynresume-detail-section-theme_2 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-item .ynresume-section-skill-user a:after,   
        #ynresume-view-sections-content.ynresume-detail-section-theme_2 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section h3.section-label .section-label-icon {
            background-color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;   
        }

        #ynresume-view-sections-content.ynresume-detail-section-theme_2 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-mini-item .add-endorse-btn,
        #ynresume-view-sections-content.ynresume-detail-section-theme_2 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-mini-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-text,
        #ynresume-view-sections-content.ynresume-detail-section-theme_2 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-mini-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-count,
        #ynresume-view-sections-content.ynresume-detail-section-theme_2 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-item .add-endorse-btn,
        #ynresume-view-sections-content.ynresume-detail-section-theme_2 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-text,
        #ynresume-view-sections-content.ynresume-detail-section-theme_2 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-count {
            color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;  
        }

        /*** style 3 ***/
        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-mini-item .add-endorse-btn,
        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-item .add-endorse-btn,
        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section h3.section-label .section-label-icon {
            background-color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
        }

        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-mini-item .ynresume-section-skill-user am
        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-item .ynresume-section-skill-user a {
            border-color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
        }

        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-mini-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-count,
        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-mini-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-text,
        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-count,
        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section-content .ynresume-section-skill-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-text,
        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section h3.section-label .section-label-icon + span {
            color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
        }

        #ynresume-view-sections-content.ynresume-detail-section-theme_3 #sections-content-items .sections-content-item .ynresume-section-content .ynresume-section-skill-item .ynresume-section-skill-user a {
            border-color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
        }

        /*** style 4 ***/
        #ynresume-view-sections-content.ynresume-detail-section-theme_4 #sections-content-item_<?php echo $key?> .ynresume-section-skill-item .ynresume-section-skill-user .add-endorse-btn,
        #ynresume-view-sections-content.ynresume-detail-section-theme_4 #sections-content-item_<?php echo $key?> .ynresume-section-skill-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-count,    
        #ynresume-view-sections-content.ynresume-detail-section-theme_4 #sections-content-item_<?php echo $key?> .ynresume-section-skill-mini-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-text,
        #ynresume-view-sections-content.ynresume-detail-section-theme_4 #sections-content-item_<?php echo $key?> .ynresume-section-skill-mini-item .add-endorse-btn,   
        #ynresume-view-sections-content.ynresume-detail-section-theme_4 #sections-content-items #sections-content-item_<?php echo $key?>.sections-content-item .ynresume-section h3.section-label {
            color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
        }


        #ynresume-view-sections-content.ynresume-detail-section-theme_4 #sections-content-item_<?php echo $key?> .ynresume-section-skill-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-count {
            border-color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;   
        }
        
        #ynresume-view-sections-content.ynresume-detail-section-theme_4 #sections-content-item_<?php echo $key?> .ynresume-section-skill-mini-item .ynresume-section-skill-endorses .ynresume-section-skill-endorses-count {
            background-color: <?php echo $settings->getSetting('ynresume_'.$theme.'_'.$key.'_color', Engine_Api::_()->ynresume()->getDefaultThemeColor($theme));?>;
        }
    <?php endif;?>   
<?php endforeach;?>    
</style>
<?php endif; ?>

<div id="ynresume-view-sections-content" class="ynresume-detail-section-<?php echo $this->resume->theme; ?>">
    <ul id="sections-content-items">
    <?php 
    $order = $this->resume->getOrder();
    if ($order) {
        $allSections = array_merge(array_flip($order->order), $allSections);
    }
    ?>
    <?php foreach ($allSections as $key => $section): ?>
        <?php if($key != 'photo') :?>
            <?php 
                $content = Engine_Api::_()->ynresume()->renderSection($key, $this->resume, array('view' => true));
                $can_view = $this->resume->authorization()->isAllowed(null, $key);
                if (strpos($key, 'field_') !== FALSE) {
                    $can_view = true;
                }
            ?>
            <?php if ($can_view && trim($content)) : ?>
                <li class="sections-content-item" id="sections-content-item_<?php echo $key?>">
                    <div class="ynresume-section">
                        <?php echo $content; ?>
                    </div>
                </li>
            <?php endif;?>
        <?php endif;?>   
    <?php endforeach;?>
    </ul>
</div>
