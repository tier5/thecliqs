<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/wookmark/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/wookmark/jquery.wookmark.min.js"></script>

<script type="text/javascript">
    //check open popup
    function checkOpenPopup(url) {
        if(window.innerWidth <= 480) {
            Smoothbox.open(url, {autoResize : true, width: 300});
        }
        else {
            Smoothbox.open(url);
        }
    }
</script>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

<div class="ynbusinesspages-business-listing-header ynbusinesspages-clearfix">
    <?php if (count($this->paginator)) 
    {
        $total = $this->paginator->getTotalItemCount();
        echo '<span class="ynbusinesspages_result_count">'.$total.'</span>';
        echo $this->translate(array('ynbusiness_found', 'Businesseses Found', $total),$total);
    }?>
	<div id="ynbusinesspages-business-mode-view-<?php echo $this -> idName; ?>" class="ynbusinesspages-business-mode-view">
		<?php if(in_array('list', $this -> mode_enabled)):?>
			<span class="ynbusinesspages-viewmode-list" rel="ynbusinesspages-browse-business-viewmode-list"></span>
		<?php endif;?>
		<?php if(in_array('grid', $this -> mode_enabled)):?>
			<span class="ynbusinesspages-viewmode-grid" rel="ynbusinesspages-browse-business-viewmode-grid"></span>
		<?php endif;?>	
        <?php if(in_array('pin', $this -> mode_enabled)):?>
            <span class="ynbusinesspages-viewmode-pins" rel="ynbusinesspages-browse-business-viewmode-pins"></span>
        <?php endif;?>  
		<?php if(in_array('map', $this -> mode_enabled)):?>
			<span class="ynbusinesspages-viewmode-maps" rel="ynbusinesspages-browse-business-viewmode-maps"></span>
		<?php endif;?>	
	</div>
</div>

<div id="ynbusinesspages-browse-listings-<?php echo $this -> idName; ?>" class="ynbusinesspages-business-listing">
<ul class="ynbusinesspages-business-listing-item-listings ynbusinesspages-clearfix">
	<?php foreach($this->paginator as $business) :?>
    <li>
	<div class="ynbusinesspages-business-listing-item">
	    <div class="ynbusinesspages-business-listing-item-header">
            <div class="ynbusinesspages-business-listing-item-title">
                <a href="<?php echo $business -> getHref();?>"><?php echo $business->getTitle();?></a>
            </div>
            <div class="ynbusinesspages-business-listing-item-main_location">
                <i class="fa fa-map-marker"></i><?php echo $business -> getMainLocation();?>
            </div>
            <!-- category -->
            <div class="ynbusinesspages-business-listing-item-category">
                <i class="fa fa-folder-open-o"></i>
                <?php $category = $business -> getMainCategory();?> 
                <?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
            </div>
            
            <div class="ynbusinesspages-business-listing-item-claim-status <?php if($business -> is_claimed) echo "claim-unclaimed"; ?>" title="<?php if($business -> is_claimed) echo $this->translate('Unclaimed'); else echo $this->translate('Verified'); ?>">
                <i class="fa fa-check"></i>
            </div>
        </div>
		<div class="ynbusinesspages-business-listing-item-image">
			<div class="ynbusinesspages-business-listing-item-photo">
				<?php echo $this->itemPhoto($business, 'thumb.profile') ?>
                <?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($business); ?>
			</div>
			<?php if($business -> featured == 1) :?>
				<span class="ynbusinesspages-item-featured"><?php echo $this -> translate('featured');?></span>
			<?php endif;?>

            <div class="ynbusinesspages-business-listing-item-claim-status <?php if($business -> is_claimed) echo "claim-unclaimed"; ?>" title="<?php if($business -> is_claimed) echo $this->translate('Unclaimed'); else echo $this->translate('Verified'); ?>">
                <i class="fa fa-check"></i>
            </div>
		</div>
		<div class="ynbusinesspages-business-listing-item-content">

		    <div class="ynbusinesspages-business-listing-item-short_review">
            <?php $review_count = $business->getReviewCount(); ?>
            <?php $can_review = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynbusinesspages_business', null, 'rate') -> checkRequire(); ?>
            <span class="business-rating"><?php echo Engine_Api::_()->ynbusinesspages()->renderBusinessRating($business->getIdentity(), false);?></span>
                <span class="business-review_count">(<?php echo $review_count; ?>)</span>

            <?php if ($review_count < 1):?>
                <?php if ($can_review && !($business -> is_claimed)) : ?>
                <span class="review-link">
                    <?php echo $this->htmlLink(array(
                        'route' => 'ynbusinesspages_review',
                        'action' => 'create',
                        'refresh' => true,
                        'business_id' => $business->getIdentity(),
                    ), '<i class="fa fa-pencil-square-o"></i><span>'.$this->translate('No reviews. Be the first!').'</span>'
                    , array (
                        'class' => 'smoothbox'
                    ))?>
                </span>
                <?php endif; ?>
            <?php else : ?>
                <?php if (!$business->hasReviewed() && $can_review && !($business -> is_claimed)) : ?>
                <span class="review-link">
                    <?php echo $this->htmlLink(array(
                        'route' => 'ynbusinesspages_review',
                        'action' => 'create',
                        'refresh' => true,
                        'business_id' => $business->getIdentity(),
                    ), '<i class="fa fa-pencil-square-o"></i><span>'.$this->translate('Write a review!').'</span>'
                    , array (
                        'class' => 'smoothbox'
                    ))?>
                </span>
                <?php endif; ?>
            <?php endif; ?>
            </div>
			<div class="ynbusinesspages-business-listing-item-short_description">
                <?php echo $business->getDescription();?>
            </div>
            
            <?php if(!Engine_Api::_()->ynbusinesspages()->isLogAsBusiness() && !($business -> is_claimed) && !Engine_Api::_()->ynbusinesspages()->isMobile2()) :?>
                <div class="ynbusinesspages-business-listing-item-add_compare">
                    <input rel="<?php echo $this->url(array('action' => 'add-to-compare', 'business_id' => $business -> getIdentity()), 'ynbusinesspages_specific', true)?>" type="checkbox" class="business-add-to-compare_<?php echo $business -> getIdentity();?>" <?php if ($business->inCompare()) echo 'checked'?> onchange="addToCompare(this, <?php echo $business -> getIdentity();?>)"/>
                    <label><?php echo $this->translate('Add to compare')?></label>
                </div>   
            <?php endif;?>      

            <?php if(!$business -> isClaimedByUser() && $business -> is_claimed) :?>
            <div class="ynbusinesspages-business-listing-item-claim">
                <i class="fa fa-paper-plane"></i>
                <?php echo $this->htmlLink($this -> url(array('action' => 'claim-business', 'id' => $business -> getIdentity()), 'ynbusinesspages_general', true), $this->translate('Claim a business'), array('class' => 'smoothbox'));?> 
            </div>
            <?php endif;?>

			<div class="ynbusinesspages-business-listing-item-footer">
                <div class="ynbusinesspages-business-listing-item-call_us">
                <?php if (Engine_Api::_()->ynbusinesspages()->isMobile()) : ?>
                    <?php $phones = $business->phone;?>
                    <?php if (count($phones) < 1 || $phones[0] == '') : ?>
                        <a href="javascript:void(0)" onclick="noInfoPopup('<?php echo $this->translate('Call us')?>')">
                            <i class="fa fa-phone"></i>
                            <span><?php echo $this->translate('Call us')?></span>
                        </a>
                    <?php else: ?>
                        <a href="tel:<?php echo $phones[0]?>">
                            <i class="fa fa-phone"></i>
                            <span><?php echo $this->translate('Call us')?></span>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php $url = $this -> url(
                        array(
                            'action' => 'tooltip', 
                            'type' => 'phone', 
                            'business_id' => $business -> getIdentity(),
                        )
                        ,'ynbusinesspages_specific', true);
                    ?>
                    <a href="<?php echo $url?>" onclick="event.preventDefault();" class="ynbusinesspages-business-listing-item-tooltip">
                        <i class="fa fa-phone"></i>
                        <span><?php echo $this->translate('Call us')?></span>
                    </a>
                <?php endif; ?>
                </div>	  
                <div class="ynbusinesspages-business-listing-item-website">
                <?php if (Engine_Api::_()->ynbusinesspages()->isMobile()) : ?>
                    <?php $websites = $business->web_address;?>
                    <?php if (count($websites) < 1 || $websites[0] == '') : ?>
                        <a href="javascript:void(0)" onclick="noInfoPopup('<?php echo $this->translate('Website')?>')">
                            <i class="fa fa-globe"></i>
                            <span><?php echo $this->translate('Website')?></span>
                        </a>
                    <?php else: ?>
                        <?php $websiteURl = $websites[0];?>
                        <?php if((strpos($websiteURl,'http://') === false) && (strpos($websiteURl,'https://') === false)) $websiteURl = 'http://'.$websiteURl; ?>
                        <a target="_blank" href="<?php echo $websiteURl?>">
                            <i class="fa fa-globe"></i>
                            <span><?php echo $this->translate('Website')?></span>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php $url = $this -> url(
                        array( 
                            'action' => 'tooltip', 
                            'type' => 'website', 
                            'business_id' => $business -> getIdentity(),
                           )
                        ,'ynbusinesspages_specific', true);
                    ?>
                    <a href="<?php echo $url?>" onclick="event.preventDefault();" class="ynbusinesspages-business-listing-item-tooltip">
                        <i class="fa fa-globe"></i>
                        <span><?php echo $this->translate('Website')?></span>
                    </a>
                <?php endif; ?>
                </div>
                <div class="ynbusinesspages-business-listing-item-email">
                    <a href="mailto:<?php echo $business->email;?>">
                        <i class="fa fa-envelope"></i>
                        <span><?php echo $this->translate('Email')?></span>
                    </a>
                </div>
                <div class="ynbusinesspages-business-listing-item-location">
                <?php if (Engine_Api::_()->ynbusinesspages()->isMobile()) : ?>
                    <?php $mainLocations = $business->getMainLocation(true);?>
                    <?php if ($mainLocations) : ?>
                        <?php $url = $this->url(
                            array(
                                'action' => 'direction', 
                                'id' => $mainLocations -> getIdentity()
                            ), 
                            'ynbusinesspages_general',
                            true);
                        ?>
                        <a href="<?php echo $url?>" class="get_direction smoothbox">
                            <i class="fa fa-location-arrow"></i>
                            <span><?php echo $this->translate('Get Direction')?></span>
                        </a>
                    <?php else: ?>
                        <a href="javascript:void(0)" class="get_direction" onclick="noInfoPopup('<?php echo $this->translate('Get Direction')?>')">
                            <i class="fa fa-location-arrow"></i>
                            <span><?php echo $this->translate('Get Direction')?></span>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php $url = $this -> url(
                        array( 
                            'action' => 'tooltip', 
                            'type' => 'location', 
                            'business_id' => $business -> getIdentity(),
                           )
                        ,'ynbusinesspages_specific', true);
                    ?>
                    <a href="<?php echo $url?>" onclick="event.preventDefault();" class="ynbusinesspages-business-listing-item-tooltip">
                        <i class="fa fa-location-arrow"></i>
                        <span><?php echo $this->translate('Get Directions')?></span>
                    </a>
                <?php endif; ?>
                </div>               						
			</div>
		</div>
    </div>
	</li>
	<?php endforeach;?>
</ul>
<div id="ynbusinesspages-business-listing-maps-<?php echo $this->idName;?>" class="ynbusinesspages-business-listing-maps">
	<iframe id='map-view-iframe-<?php echo $this->idName;?>' style="max-height: 500px;"></iframe>
</div>
</div>
<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no businesses found yet.') ?>
		</span>
    </div>
<?php endif; ?>

<div id='paginator'>
	<?php if( $this->paginator->count() > 1 ): ?>
	     <?php echo $this->paginationControl($this->paginator, null, null, array(
	            'pageAsQuery' => true,
	            'query' => $this->formValues,
	          )); ?>
	<?php endif; ?>
</div>

<script type="text/javascript">
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
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

    function setPinterestMode() {
        jQuery.noConflict();
        (function (jQuery){
            var handler = jQuery('#ynbusinesspages-browse-listings-<?php echo $this -> idName; ?> .ynbusinesspages-business-listing-item-listings li');

            handler.wookmark({
              // Prepare layout options.
              autoResize: true, // This will auto-update the layout when the browser window is resized.
              container: jQuery('#ynbusinesspages-browse-listings-<?php echo $this -> idName; ?> .ynbusinesspages-business-listing-item-listings'), // Optional, used for some extra CSS styling
              offset: 20, // Optional, the distance between grid items
              outerOffset: 0, // Optional, the distance to the containers border
              itemWidth: 220, // Optional, the width of a grid item
              flexibleWidth: '50%',
            });
        })(jQuery);
    }

    function removePinterestMode() {
        $$('#ynbusinesspages-browse-listings-<?php echo $this -> idName; ?> .ynbusinesspages-business-listing-item-listings')[0].erase('style'); 
        $$('#ynbusinesspages-browse-listings-<?php echo $this -> idName; ?> .ynbusinesspages-business-listing-item-listings li').each(function(el){
            el.erase('style'); 
        });

        (function (jQuery){
            jQuery(window).unbind('resize.wookmark');
        })(jQuery);
    }

    
    function setMapMode<?php echo $this->idPrefix;?>(){
        var html =  "<?php echo $this->url(array('action'=>'display-map-view', 'ids' => $this->businessIds), 'ynbusinesspages_general') ?>";
        document.getElementById('map-view-iframe-<?php echo $this->idName;?>').dispose();
        var iframe = new IFrame({
            id : 'map-view-iframe-<?php echo $this->idName;?>',
            src: html,
            styles: {                  
                'height': '500px',
                'width' : '100%'
            },
        });
        
        iframe.inject( $('ynbusinesspages-business-listing-maps-<?php echo $this->idName;?>') );
        document.getElementById('map-view-iframe-<?php echo $this->idName;?>').style.display = 'block';
        $$('ul.ynbusinesspages-clearfix').hide();
        $$('#ynbusinesspages-business-listing-maps-<?php echo $this->idName;?>').show();
    }

    // Get cookie
    removePinterestMode();
    var myCookieViewMode = getCookie('ynbusinesspages-business-viewmode-cookie-<?php echo $this -> idName; ?>');
    if ( myCookieViewMode == '') 
    {
        myCookieViewMode = '<?php echo trim($this -> class_mode); ?>';
    }
    if ( myCookieViewMode == '') 
    {
        myCookieViewMode = 'ynbusinesspages-browse-business-viewmode-list';
    }

    $$('#ynbusinesspages-business-mode-view-<?php echo $this -> idName; ?>').addClass( myCookieViewMode );
    $$('#ynbusinesspages-browse-listings-<?php echo $this -> idName; ?>').addClass( myCookieViewMode );

    // render MapView
    if ( myCookieViewMode == 'ynbusinesspages-browse-business-viewmode-maps') {
        setMapMode<?php echo $this->idPrefix;?>();
    }

    // render pinterestView
    if ( myCookieViewMode == 'ynbusinesspages-browse-business-viewmode-pins') {
        setPinterestMode();
    }

    // Set click viewMode
    $$('#ynbusinesspages-business-mode-view-<?php echo $this -> idName;?> > span').addEvent('click', function(){
        var viewmode = this.get('rel'),
            browse_content = $('ynbusinesspages-browse-listings-<?php echo $this -> idName; ?>'),
            //header_mode = $$('.ynbusinesspages-business-mode-view')[0];
            header_mode = $('ynbusinesspages-business-mode-view-<?php echo $this -> idName; ?>');

        setCookie('ynbusinesspages-business-viewmode-cookie-<?php echo $this -> idName; ?>', viewmode, 1);

        header_mode
            .removeClass('ynbusinesspages-browse-business-viewmode-list')
            .removeClass('ynbusinesspages-browse-business-viewmode-grid')
            .removeClass('ynbusinesspages-browse-business-viewmode-pins')
            .removeClass('ynbusinesspages-browse-business-viewmode-maps');

        browse_content
            .removeClass('ynbusinesspages-browse-business-viewmode-list')
            .removeClass('ynbusinesspages-browse-business-viewmode-grid')
            .removeClass('ynbusinesspages-browse-business-viewmode-pins')
            .removeClass('ynbusinesspages-browse-business-viewmode-maps');

        header_mode.addClass( viewmode );
        browse_content.addClass( viewmode );


        // remove pinterest mode
        if (viewmode != 'ynbusinesspages-browse-business-viewmode-pins' ) {
            removePinterestMode();
        }
        
        // render MapView
        if ( viewmode == 'ynbusinesspages-browse-business-viewmode-maps') {
            setMapMode<?php echo $this->idPrefix;?>();
            
        } else {
            //document.getElementById('paginator').style.display = 'block';
            $$('ul.ynbusinesspages-clearfix').show();
            $('ynbusinesspages-business-listing-maps-<?php echo $this->idName;?>').hide();
            if (viewmode == 'ynbusinesspages-browse-business-viewmode-pins' ) {
                setPinterestMode();
            } 
        }
    });
</script>
 
 <script type="text/javascript">
 //script for add to compare
    function addToCompare(obj, id) {
        var value = (obj.checked) ? 1 : 0;
        var url = obj.get('rel');
        var jsonRequest = new Request.JSON({
            url : url,
            onSuccess : function(json, text) {
                if (!json.error) {
                    $$('.business-add-to-compare_'+id).set('checked', obj.checked);
                    var params = {};
                    params['format'] = 'html';
                    var request = new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/name/ynbusinesspages.compare-bar',
                        data : params,
                        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                            $$('.layout_ynbusinesspages_compare_bar').destroy();
                            var body = document.getElementsByTagName('body')[0];
                            Elements.from(responseHTML).inject(body);
                            eval(responseJavaScript);
                        }
                    });
                    request.send();
                }
                else {
                    obj.set('checked', !obj.checked);
                    alert(json.message);
                }
            }
        }).get({value:value});
    }

</script>

<?php if (!Engine_Api::_()->ynbusinesspages()->isMobile()) : ?> 
<script type="text/javascript">
     //script for tooltip
    jQuery.noConflict();
    var businessTooltip = {
        ele : 0,
        href : 0,
        timeoutId : 0,
        isShowing: 0,
        cached: {},
        dir: {cx:0,cy:0},
        isShowing: 0,
        isMouseOver: 1,
        mouseOverTimeoutId: 0,
        box:0,
        timeoutOpen: 300,
        timeoutClose: 300,
        boxContent: 0,
        setTimeoutOpen: function(time){
            businessTooltip.timeoutOpen = time;
            return businessTooltip;
        },
        clearCached: function(){
            businessTooltip.cached = {};
        },
        openSmoothBox: function(href){
            // create an element then bind to object
            var a = new Element('a', {
                href : href,
                'style' : 'display:none'
            });
            var body = document.getElementsByTagName('body')[0];
            a.inject(body);
            Smoothbox.open(a);
        },
        setTimeoutClose: function(time){
            businessTooltip.timeoutClose = time;
            return businessTooltip;
        },
        boot : function() {
            $$('.ynbusinesspages-business-listing-item-tooltip').each(function(el) {
                el.addEvent('mouseover', businessTooltip.check);
            });
        },
        check : function(e) {
            if(e.target == null && e.target == undefined){
                return;
            }
    
            var a = e.target;
            var ele = e.target;
    
            if(a.getAttribute == null || a.getAttribute == undefined){
                return;
            }
    
            var href = a.getAttribute('href');
            if(href == null && href == undefined){
                return;
            }
    
            businessTooltip.ele = $(ele);
            businessTooltip.href = href;
            if(businessTooltip.timeoutId) {
                try {
                    window.clearTimeout(businessTooltip.timeoutId);
                } catch(e) {
    
                }
            }

            $(a).addEvent('mouseleave',function(){businessTooltip.resetTimeout(0);});
            businessTooltip.timeoutId = 0;
            businessTooltip.isRunning = 0;
            businessTooltip.dir.cx = e.event.clientX;
            businessTooltip.dir.cy = e.event.clientY;
            businessTooltip.timeoutId = window.setTimeout('businessTooltip.requestPopup()', businessTooltip.timeoutOpen);
            return ;
        },
        updateBoxContent: function(html){
          businessTooltip.boxContent.innerHTML = html;
          return businessTooltip;
        },
        startSending: function(html){
          businessTooltip.boxContent.innerHTML = '<div class="uiContextualDialogContent"> \
                                    <div class="uibusinessTooltipHovercardStage"> \
                                        <div class="uibusinessTooltipHovercardContent"> \
                                        ' +html+ ' \
                                        </div> \
                                    </div> \
                                </div> \
                                ';
            return businessTooltip;
    
        },
        requestPopup : function() {
            businessTooltip.timeoutId = 0;
            var box = businessTooltip.getBox();
            box.style.display = 'none';
    
            var key = businessTooltip.href;
            if(businessTooltip.cached[key] != undefined){
                businessTooltip.showPopup(businessTooltip.cached[key]);
                return;
            }
            var jsonRequest = new Request.JSON({
                url : businessTooltip.href,
                onSuccess : function(json, text) {
                    businessTooltip.cached[key] = json;
                    businessTooltip.showPopup(json);
                }
            }).get({type_show:'ajax'});
            businessTooltip.startSending(en4.core.language.translate('Loading...'));
            businessTooltip.resetPosition(1);
            return businessTooltip;
    
        },
        resetTimeout: function($flag){
            businessTooltip.isMouseOver = $flag;
            if(businessTooltip.mouseOverTimeoutId){
                try{
                    window.clearTimeout(businessTooltip.mouseOverTimeoutId);
                    businessTooltip.mouseOverTimeoutId = 0;
                    if(businessTooltip.timeoutId){
                        try{
                            window.clearTimeout(businessTooltip.timeoutId);
                            businessTooltip.timeoutId = 0;
                        }catch(e){
                        }
                    }
                }catch(e){
                }
            }
            if($flag ==0){
                businessTooltip.mouseOverTimeoutId = window.setTimeout('businessTooltip.closePopup()',businessTooltip.timeoutClose);
            }
            return businessTooltip;
    
        },
        closePopup: function(){
            box = businessTooltip.getBox();
            box.style.display = 'none';
            businessTooltip.isShowing = 0;
            return businessTooltip;
        },
        resetPosition: function(flag){
            businessTooltip.isShowing = 1;
            var box = businessTooltip.getBox();
            var ele =  businessTooltip.ele;
    
            if(!ele){
                return ;
            }
            var pos = ele.getPosition();
            var size = ele.getSize();
    
            if(pos == null || pos == undefined){
                return ;
            }
            
            if(businessTooltip.dir.cy >180){
                box.style.top =  pos.y  +'px';
                box.removeClass('uibusinessTooltipDialogDirDown').addClass('uibusinessTooltipDialogDirUp');
            }else{
                box.style.top =  pos.y + size.y +'px';
                box.removeClass('uibusinessTooltipDialogDirUp').addClass('uibusinessTooltipDialogDirDown');
            }
    
    
            if(en4.orientation=='ltr'){
                // check the position of the content
    
                if(window.getSize().x - businessTooltip.dir.cx > 350){
                    box.removeClass('uibusinessTooltipDialogDirLeft').addClass('uibusinessTooltipDialogDirRight');
                    var px = size.x > 200? businessTooltip.dir.cx:pos.x;
                    box.style.left =  px + 'px';
                }else{
                    box.removeClass('uibusinessTooltipDialogDirRight').addClass('uibusinessTooltipDialogDirLeft');
                    var px = size.x > 200? businessTooltip.dir.cx:(pos.x+size.x);
                    box.style.left =  px + 'px';
                }
            }else{
                // right to left
                if(businessTooltip.dir.cx< 310){
                    box.removeClass('uibusinessTooltipDialogDirLeft').addClass('uibusinessTooltipDialogDirRight');
                    var px = size.x > 200? businessTooltip.dir.cx:pos.x;
                    box.style.left =  px + 'px';
                }else{
                    var px = size.x > 200? businessTooltip.dir.cx:(pos.x+size.x);
                    box.style.left =  px + 'px';
                    box.removeClass('uibusinessTooltipDialogDirRight').addClass('uibusinessTooltipDialogDirLeft');
                }
    
            }
            if(flag){
                box.style.display = 'block';
            }
    
    
        },
        showPopup : function(json) {
            if(json == null || json == undefined){
                return ;
            }
            businessTooltip.resetPosition(1);
            var box = businessTooltip.getBox();
            businessTooltip.updateBoxContent(json.html);
            box.style.display='block';
            return businessTooltip;
        },
        getBox: function(){
            if(businessTooltip.box){
                return businessTooltip.box;
            }
            
            var ct = document.createElement('DIV');
            ct.setAttribute('id','uibusinessTooltipDialog');
            var html = '<div class="uibusinessTooltipDialogOverlay" id="businessTooltipUiOverlay" onmouseover="businessTooltip.resetTimeout(1)" onmouseout="businessTooltip.resetTimeout(0)">'
                        + '<div class="uibusinessTooltipOverlayContent" id="businessTooltipUiOverlayContent">'
                        + '</div>'
                        + '<i class="uibusinessTooltipContextualDialogArrow"></i>'
                        + '</div>';
            ct.innerHTML = html;
            var body = document.getElementsByTagName('body')[0];
            body.appendChild(ct);
            $(ct).addClass('uibusinessTooltipDialog');
            businessTooltip.box = $('uibusinessTooltipDialog');
            businessTooltip.boxContent = $('businessTooltipUiOverlayContent');
            return businessTooltip.box;
        }
    };
    
    window.addEvent('domready', businessTooltip.boot);
</script>
<?php else: ?>
<script type="text/javascript">
    function noInfoPopup(label) {
        var div = new Element('div', {
            'class': 'mobile-noinfo-popup'
        });
        var h3 = new Element('h3', {
            text: label
        });
        var p = new Element('p', {
            text: '<?php echo $this->translate('No information.')?>'
        });
        var btn = new Element('button', {
            type: 'button',
            text: '<?php echo $this->translate('Close')?>',
            onclick: 'parent.Smoothbox.close()'
        })
        div.grab(h3);
        div.grab(p);
        div.grab(btn);
        if(window.innerWidth <= 480) {
            Smoothbox.open(div, {autoResize : true, width: 300});
        }
        else {
            Smoothbox.open(div);
        }
    }
</script>    
<?php endif; ?>    