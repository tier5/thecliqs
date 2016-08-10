<div id="compare-bar-widget">
    <div id="show-hide-bar">
        <div class="ynbusinesspages-compare-width">
        <a href="javascript:void(0)" onclick="showCompareBar(this)">
           <span class="fa fa-angle-double-up"></span>
           <span><?php echo $this->translate('Compare')?></span>
        </a>
        </div>
    </div>
    <div id="comparebusinesses_list_item" class="<?php echo $this -> class_mode;?>">  
    	<div class="compare-tabs-header">
            <div id="compare_tabs" class="tabs_alt tabs_parent">
            <ul id="compare_tab_list" class = "main_tabs ynbusinesspages-compare-width">
                <?php foreach ($this->categories as $category) : ?>
                <li id="compare_tab_title_<?php echo $category->getIdentity()?>" rel="<?php echo $category->getIdentity()?>">
                    <a href="javascript:void(0);">
                        <span><?php echo $category->getTitle()?></span>
                        <span class="business-count">(<?php echo Engine_Api::_()->ynbusinesspages()->countComparebusinessesOfCategory($category->getIdentity())?>)</span>
                    </a>
                    <span class="remove-all" onclick="removeAll(<?php echo $category->getIdentity()?>)"><?php echo $this->translate('<i class="fa fa-times"></i>')?></span>
                </li>
                <?php endforeach; ?>  
            </ul>
            </div>
    	</div>
    	<div id="compare_tab_contents" class="compare-tabs-content">
            <div class="ynbusinesspages-compare-width">
    		<?php foreach ($this->categories as $category) : ?>
    		<div id="compare_tab_<?php echo $category->getIdentity()?>" class="compare_tab_content">
                <div class="options">
                    <div class="compare">
                        <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_compare', 'category_id' => $category->getIdentity()), $this->translate('Compare'))?>
                    </div>                   

                    <a class="hideCompare" href="javascript:void(0)" onclick="hideCompareBar(this)">
                        <span class="fa fa-angle-double-down"></span>
                    </a>
                </div>
                <div class="compare-businesses">
                <?php $compare_businesses = Engine_Api::_()->ynbusinesspages()->getComparebusinessesOfCategory($category->getIdentity());?>
    		    <?php foreach ($compare_businesses as $business) : ?>
                    <div class="compare-business-item" id="<?php echo $business->getIdentity();?>">
                        <div class="photo">
                            <?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($business, 'thumb.normal'); ?>
                        </div>
                        <div class="title">
                            <?php echo $this->htmlLink($business->getHref(), $business->getTitle());?>
                        </div>
                        <div class="delete">
                            <a href="javascript:void(0)" onclick="deleteComparebusiness(this, <?php echo $business->getIdentity();?>, <?php echo $category->getIdentity();?>)"><i class="fa fa-times"></i></a>
                        </div>
                    </div>
    		    <?php endforeach;?>  
    		    </div>
    		</div>
    	   <?php endforeach; ?>
           </div>
    	</div>
    </div>
</div>

<script type="text/javascript">
    window.addEvent('domready', function(){
        $$('#compare_tab_list li').removeClass('active');
        if ($$('#compare_tab_list li').length) {
            $$('#compare_tab_list li')[0].addClass('active');
        }
        $$('#compare_tab_contents .compare_tab_content').removeClass('active');
        if ($$('#compare_tab_contents .compare_tab_content').length) {
            $$('#compare_tab_contents .compare_tab_content')[0].addClass('active');
        }
        
        $$('#compare_tab_list li').each(function(el) {
            el.addEvent('click', function(){
                $$('#compare_tab_list li').removeClass('active');
                this.addClass('active');
                var id = this.get('rel');
                $$('#compare_tab_contents .compare_tab_content').removeClass('active');
                $('compare_tab_'+id).addClass('active');
            });
        });
        
        var hideBar = getCookie('hide_compare_bar');
        if ( hideBar == 1 && $('compare-bar-widget') ) {
            $('compare-bar-widget').addClass('ynbusinesspages-hideCompareBar');
        }
    });
    
    function deleteComparebusiness(obj, id, category_id) {
        var url = '<?php echo $this->url(array('action' => 'remove-business'),'ynbusinesspages_compare', true)?>';
        new Request.JSON({
            url: url,
            method: 'post',
            data: {
                'id': id,
                'category_id' : category_id,
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.status) {
                    if (responseJSON.count == 0) {
                        obj.getParent('.compare_tab_content').destroy();
                        $('compare_tab_title_'+category_id).destroy();
                        $$('#compare_tab_list li').removeClass('active');
                        if ($$('#compare_tab_list li').length) {
                            $$('#compare_tab_list li')[0].addClass('active');
                        }
                        else {
                            $('compare-bar-widget').hide();
                        }
                        $$('#compare_tab_contents .compare_tab_content').removeClass('active');
                        if ($$('#compare_tab_contents .compare_tab_content').length) {
                            $$('#compare_tab_contents .compare_tab_content')[0].addClass('active');
                        }
                    }
                    else {
                        obj.getParent('.compare-business-item').destroy();
                        $$('#compare_tab_title_'+category_id+' span.business-count')[0].set('text', '('+responseJSON.count+')');
                    }
                    $$('.business-add-to-compare_'+id).set('checked', false);
                    $$('.business-add-to-compare_'+id).set('checked', false);
                    if ($('compare-business_'+id)) {
                        $('compare-business_'+id).destroy();
                    }
                }
            }
        }).send();
    };
    
    function removeAll(category_id) {
        var url = '<?php echo $this->url(array('action' => 'remove-category'),'ynbusinesspages_compare', true)?>';
        new Request.JSON({
            url: url,
            method: 'post',
            data: {
                'id' : category_id,
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.status) {
                    $$('#compare_tab_'+category_id+' .compare-business-item').each(function(el) {
                       var id = el.get('id');
                       $$('.business-add-to-compare_'+id).set('checked', false); 
                    });
                    
                    $('compare_tab_title_'+category_id).destroy();
                    $('compare_tab_'+category_id).destroy();
                    if (responseJSON.count == 0) {
                        $('compare-bar-widget').hide();
                    }
                    else {
                        $$('#compare_tab_list li').removeClass('active');
                        $$('#compare_tab_list li')[0].addClass('active');
                        $$('#compare_tab_contents .compare_tab_content').removeClass('active');
                        $$('#compare_tab_contents .compare_tab_content')[0].addClass('active');
                    }
                }
            }
        }).send();
    };
    
    function hideCompareBar(obj) {
        setCookie('hide_compare_bar', 1, 1);
        $('compare-bar-widget').addClass('ynbusinesspages-hideCompareBar');
    }
    
    function showCompareBar(obj) {
        setCookie('hide_compare_bar', 0, 1);
        $('compare-bar-widget').removeClass('ynbusinesspages-hideCompareBar');
    }
    
    function setCookie(cname,cvalue,exdays) {
        var d = new Date();
        d.setTime(d.getTime()+(exdays*24*60*60*1000));
        var expires = "expires="+d.toGMTString();
        document.cookie = cname + "=" + cvalue + "; " + expires + ";path=/";
    }
    
    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) 
        {
            var c = ca[i].trim();
            if (c.indexOf(name)==0) return c.substring(name.length,c.length);
        }
        return "";
    }
</script>