<div class="ynresume-layout-list-modeview">
<?php $viewer = Engine_Api::_() -> user() -> getViewer();?>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class="ynresume-layout-header">
	<div id="ynresume-mode-view-<?php echo $this->idName;?>" class="ynresume-modeview-button">
		<?php if(in_array('list', $this -> mode_enabled)):?>
			<span class="" rel="ynresume-layout-content-list-view"><i class="fa fa-th-list"></i></span>
		<?php endif;?>
		<?php if(in_array('grid', $this -> mode_enabled)):?>
			<span class="" rel="ynresume-layout-content-grid-view"><i class="fa fa-th"></i></span>
		<?php endif;?>	
		<?php if(in_array('map', $this -> mode_enabled)):?>
			<span class="" rel="ynresume-layout-content-map-view"><i class="fa fa-map-marker"></i></span>
		<?php endif;?>	
	</div>
</div>
<div id="ynresume-resume-listing-<?php echo $this -> idName; ?>" class="ynresume-layout-content">
	<ul class="ynresume-clearfix">
		<?php foreach($this->paginator as $resume) :?>
		<li>
			<div class="ynresume-layout-list-item">
			    <div class="ynresume-layout-list-item-thumb">
	                <div class="ynresume-layout-list-item-thumb-border"></div>
	                <?php echo Engine_Api::_()->ynresume()->getPhotoSpan($resume); ?>
	            </div>
	            <div class="ynresume-layout-list-item-main">
	                <div class="ynresume-layout-list-item-title">
	                    <?php echo $this->htmlLink($resume->getHref(), $resume->getTitle())?>
	                    <div id="ynresume-save-status-<?php echo $resume->getIdentity();?>">
							<i><?php echo ($resume -> hasSaved())? $this -> translate('(Saved)') : '';?></i>
	               		</div>
	                </div>
	                <div class="ynresume-layout-list-item-subline">
	                    <span><i class="fa fa-briefcase"></i><?php echo $resume->getJobTitle();?></span>
	                    <span><i class="fa fa-building"></i><?php echo $resume->getCompany();?></span>
	                </div>
	                <div class="ynresume-layout-list-item-subline">
	                    <span class="ynresume-layout-list-item-location"><?php echo $resume->location?></span>
	                    <span class="ynresume-layout-list-item-position">
	                        <?php $industry = $resume->getIndustry();?>
	                        <?php echo ($industry) ? $industry->getTitle() : $this->translate('Unknown Industry');?>
	                    </span>
	                </div>
	                <div class="ynresume-layout-list-item-description ynresume-description">
	                    <div class="ynresume-layout-list-item-description-title"><?php echo $this->translate('Summary') ?></div>
	                    <div class="ynresume-description"><?php echo strip_tags($resume->getSummary());?></div>
	                    <div class="ynresume-layout-list-item-description-action">
	                        <?php $owner = $resume->getOwner()?>
	                        <?php echo $this->htmlLink($owner->getHref(), $this->translate('View Profile'), array())?>
	                        <?php echo $this->htmlLink($resume->getHref(), $this->translate('View Resume'), array())?>
	                    </div>
	                </div>
	            </div>
	            <div class="ynresume-layout-list-item-footer">
	                <?php if($viewer -> isSelf($resume -> getOwner())) :?>
	                	<div><a href="<?php echo $this->url(array('controller' => 'resume', 'action' => 'export-pdf', 'resume_id' => $resume->getIdentity()), 'ynresume_extended');?>"><span><i class="fa fa-file-pdf-o"></i> <span><?php echo $this -> translate('Save to PDF');?></span></span></a></div>
	                	<div class="ynresume-layout-list-item-action">
		                    <span><i class="fa fa-ellipsis-h"></i> <span> <?php echo $this -> translate('More');?></span></span>
		                    <ul class="ynresume-show-options">
		                    	<li><?php echo $this->htmlLink(array('route'=>'ynresume_recommend','action'=>'ask'), '<i class="fa fa-question-circle"></i>'.$this->translate('Ask Recommendation'), array())?></li>
		                    	<li><a href="<?php echo $resume->getOwner()->getHref();?>"><i class="fa fa-eye"></i><?php echo $this -> translate('View Profile');?></a></li>
		                    </ul>       
	               		</div>  
	                <?php else :?>
						<div><a class='smoothbox' href="<?php echo $this->url(array('action' => 'compose-message', 'to' => $resume -> getOwner() -> getIdentity()), 'ynresume_general', true);?>"><span><i class="fa fa-envelope"></i> <span><?php echo $this -> translate('Message');?></span></span></a></div>

		                <div class="ynresume-layout-list-item-action">
		                    <span><i class="fa fa-ellipsis-h"></i> <span> <?php echo $this -> translate('More');?></span></span>
		                    <ul class="ynresume-show-options">
								<li><a href="<?php echo $this->url(array('controller' => 'resume', 'action' => 'export-pdf', 'resume_id' => $resume->getIdentity()), 'ynresume_extended');?>"><i class="fa fa-file-pdf-o"></i><?php echo $this -> translate('Save to PDF');?></a></li>
								<?php if($viewer -> getIdentity() && !$viewer -> isSelf($resume -> getOwner())):?>
									<?php if($resume -> hasSkill()) :?>
										<li><a href="<?php echo $resume -> getHref();?>/endorse/1"><i class="fa fa-check-square-o"></i><?php echo $this -> translate('Endorse');?></a></li>
									<?php endif;?>
										<li><a href="<?php echo $this -> url(array('action' => 'give', 'receiver_id' => $resume -> getOwner() -> getIdentity()), 'ynresume_recommend', true);?>"><i class="fa fa-comments-o"></i><?php echo $this -> translate('Recommend');?></a></li>
								<?php endif;?>
								<?php if($this -> viewer() -> getIdentity()) :?>
								<li><a id="ynresume_save_listing<?php echo $resume -> getIdentity();?>" onclick="saveResume('<?php echo $resume -> getIdentity() ;?>');" href="javascript:;"><i class="fa fa-floppy-o"></i><?php echo ($resume -> hasSaved())? $this -> translate('Unsave Resume') : $this -> translate('Save Resume');?></a></li>
								<li><a href="javascript:void(0)" class="ynresume_favourite_<?php echo $resume -> getIdentity();?>" onclick="favouriteResume('<?php echo $resume -> getIdentity() ;?>');"><i class="fa fa-star-o"></i><?php echo ($resume -> hasFavourited()) ? $this -> translate('Unfavourite') : $this -> translate('Favourite');?></a></li>
								<?php endif;?>
								<li><a href="<?php echo $resume->getOwner()->getHref();?>"><i class="fa fa-eye"></i><?php echo $this -> translate('View Profile');?></a></li>
		                    </ul>       
		                </div>  
	                <?php endif;?>                    
	            </div>
	        </div>
		</li>
		<?php endforeach;?>
	</ul>
	<div id="ynresume-resume-listing-maps-<?php echo $this->idName;?>" class="ynresume-layout-maps">
		<iframe id='map-view-iframe-<?php echo $this->idName;?>' style="max-height: 500px;"></iframe>
	</div>
</div>

	<?php if($this -> isWidget):?>
		<div class="ynresume-clearfix">
			  <div id="ynresume_listing_previous-<?php echo $this->idName;?>" class="paginator_previous">
			    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
			      'onclick' => '',
			      'class' => 'buttonlink icon_previous'
			    )); ?>
			  </div>
			  <div id="ynresume_listing_next-<?php echo $this->idName;?>" class="paginator_next">
			    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
			      'onclick' => '',
			      'class' => 'buttonlink_right icon_next'
			    )); ?>
			  </div>
		</div>  
		<?php else:?>
		
		<div id='paginator'>
			<?php if( $this->paginator->count() > 1 ): ?>
			     <?php echo $this->paginationControl($this->paginator, null, null, array(
			            'pageAsQuery' => true,
			            'query' => $this->formValues,
			          )); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no resumes found yet.') ?>
		</span>
    </div>
<?php endif; ?>
</div>

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
<script type="text/javascript">

<?php if($this -> viewer() -> getIdentity()) :?>
function saveResume(id)
{
	new Request.JSON({
        url: '<?php echo $this->url(array('action' => 'save'), 'ynresume_general', true); ?>',
        method: 'post',
        data : {
        	format: 'json',
            'id' : id
        },
        onComplete: function(responseJSON, responseText) {
            if (responseJSON.save == '1')
            {
            	$("ynresume_save_listing"+id).set("html", '<i class="fa fa-floppy-o"></i><?php echo $this -> translate("Unsave Resume");?>');
            	$("ynresume-save-status-"+id).set("html", '<?php echo $this -> translate("(Saved)");?>');
            }
            else
            {
            	$("ynresume_save_listing"+id).set("html", '<i class="fa fa-floppy-o"></i><?php echo $this -> translate("Save Resume");?>');
            	$("ynresume-save-status-"+id).set("html", '');
            }
            
        }
    }).send();
}
function favouriteResume(id)
{
	new Request.JSON({
        url: '<?php echo $this->url(array('action' => 'favourite'), 'ynresume_general', true); ?>',
        method: 'post',
        data : {
        	format: 'json',
            'id' : id
        },
        onComplete: function(responseJSON, responseText) {
            if (responseJSON.save == '1')
            {
            	$$(".ynresume_favourite_"+id).each(function(el) {
            		var icon = (el.hasClass('button')) ? '' : '<i class="fa fa-star-o"></i>';
            		el.set("html", icon+'<?php echo $this -> translate("Unfavourite");?>');
            	});
            }
            else
            {
            	$$(".ynresume_favourite_"+id).each(function(el) {
            		var icon = (el.hasClass('button')) ? '' : '<i class="fa fa-star-o"></i>';
            		el.set("html", icon+'<?php echo $this -> translate("Favourite");?>');
            	});
            }
            
        }
    }).send();
}
<?php endif;?>
<?php if ($this->isWidget) :?>
en4.core.runonce.add(function()
<?php else:?>
window.addEvent('domready', function()
<?php endif;?>
{
	
	<?php if($this -> isWidget):?>
		var anchor = $('ynresume-resume-listing-<?php echo $this -> idName; ?>').getParent();
	    
	    $('ynresume_listing_previous-<?php echo $this->idName;?>').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
	    $('ynresume_listing_next-<?php echo $this->idName;?>').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
	
	    $('ynresume_listing_previous-<?php echo $this->idName;?>').removeEvents('click').addEvent('click', function(){
	      en4.core.request.send(new Request.HTML({
	        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->idName) ?>,
	        data : {
	          format : 'html',
	          subject : en4.core.subject.guid,
	          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
	        },
	        onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript) 
	    	{
	        	// Get cookie
			    var myCookieViewMode = getCookie('ynresume-layout-list-modeview-<?php echo $this -> idName; ?>');
			    if ( myCookieViewMode == '') {
			        myCookieViewMode = 'ynresume-layout-content-list-view';
			    }
			    
			    $$('#ynresume-mode-view-<?php echo $this -> idName;?> > span[rel='+myCookieViewMode+']')[0].addClass('active');
			    $$('#ynresume-resume-listing-<?php echo $this -> idName; ?>')[0].addClass(myCookieViewMode);
			
			    // render MapView
			    if ( myCookieViewMode == 'ynresume-layout-content-map-view') {
			        setMapMode<?php echo $this->idPrefix;?>();
			    }
	        	$('ynresume-mode-view-<?php echo $this -> idName; ?>').addClass( myCookieViewMode );
	        	$('ynresume-resume-listing-<?php echo $this -> idName; ?>').addClass( myCookieViewMode );
	    	}
	      }), {
	        'element' : anchor
	      })
	    });
		
	    $('ynresume_listing_next-<?php echo $this->idName;?>').removeEvents('click').addEvent('click', function(){
	      en4.core.request.send(new Request.HTML({
	        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->idName) ?>,
	        data : {
	          format : 'html',
	          subject : en4.core.subject.guid,
	          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
	        },
	        onSuccess: function(responseJSON, responseText, responseHTML, responseJavaScript) 
	    	{
	        	// Get cookie
			    var myCookieViewMode = getCookie('ynresume-layout-list-modeview-<?php echo $this -> idName; ?>');
			    if ( myCookieViewMode == '') {
			        myCookieViewMode = 'ynresume-layout-content-list-view';
			    }
			    
			    $$('#ynresume-mode-view-<?php echo $this -> idName;?> > span[rel='+myCookieViewMode+']')[0].addClass('active');
			    $$('#ynresume-resume-listing-<?php echo $this -> idName; ?>')[0].addClass(myCookieViewMode);
			
			    // render MapView
			    if ( myCookieViewMode == 'ynresume-layout-content-map-view') {
			        setMapMode<?php echo $this->idPrefix;?>();
			    }
	        	$('ynresume-mode-view-<?php echo $this -> idName; ?>').addClass( myCookieViewMode );
	        	$('ynresume-resume-listing-<?php echo $this -> idName; ?>').addClass( myCookieViewMode );
	    	}
	      }), {
	        'element' : anchor
	      })
	    }); 
	<?php endif;?>
	
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
    
    function setMapMode<?php echo $this->idPrefix;?>(){
        var html =  "<?php echo $this->url(array('action'=>'display-map-view', 'ids' => $this->resumeIds), 'ynresume_general') ?>";
        document.getElementById('map-view-iframe-<?php echo $this->idName;?>').dispose();
        var iframe = new IFrame({
            id : 'map-view-iframe-<?php echo $this->idName;?>',
            src: html,
            styles: {                  
                'height': '500px',
                'width' : '100%'
            },
        });
        
        iframe.inject( $('ynresume-resume-listing-maps-<?php echo $this->idName;?>') );
        document.getElementById('map-view-iframe-<?php echo $this->idName;?>').style.display = 'block';
    }

    // Get cookie
    var myCookieViewMode = getCookie('ynresume-layout-list-modeview-<?php echo $this -> idName; ?>');
    if ( myCookieViewMode == '') {
        myCookieViewMode = '<?php echo $this->class_mode?>';
    }
    
    $$('#ynresume-mode-view-<?php echo $this -> idName;?> > span[rel='+myCookieViewMode+']')[0].addClass('active');
    $$('#ynresume-resume-listing-<?php echo $this -> idName; ?>')[0].addClass(myCookieViewMode);

    // render MapView
    if ( myCookieViewMode == 'ynresume-layout-content-map-view') {
        setMapMode<?php echo $this->idPrefix;?>();
    }

    // Set click viewMode
    $$('#ynresume-mode-view-<?php echo $this -> idName;?> > span').addEvent('click', function(){
        var viewmode = this.get('rel');
        var browse_content = $('ynresume-resume-listing-<?php echo $this -> idName; ?>');

        setCookie('ynresume-layout-list-modeview-<?php echo $this -> idName; ?>', viewmode, 1);

        // set class active
        $$('#ynresume-mode-view-<?php echo $this->idName;?> > span').removeClass('active');
        this.addClass('active');

        browse_content
            .removeClass('ynresume-layout-content-list-view')
            .removeClass('ynresume-layout-content-grid-view')
            .removeClass('ynresume-layout-content-map-view');

        browse_content.addClass( viewmode );

        // render MapView
        if ( viewmode == 'ynresume-layout-content-map-view') {
            setMapMode<?php echo $this->idPrefix;?>();
        } 
    });

    (function($,$$){
      var events;
      var check = function(e){
        var target = $(e.target);
        var parents = target.getParents();
        events.each(function(item){
          var element = item.element;
          if (element != target && !parents.contains(element))
            item.fn.call(element, e);
        });
      };
      Element.Events.outerClick = {
        onAdd: function(fn){
          if(!events) {
            document.addEvent('click', check);
            events = [];
          }
          events.push({element: this, fn: fn});
        },
        onRemove: function(fn){
          events = events.filter(function(item){
            return item.element != this || item.fn != fn;
          }, this);
          if (!events.length) {
            document.removeEvent('click', check);
            events = null;
          }
        }
      };
    })(document.id,$$);

    $$('#ynresume-resume-listing-<?php echo $this -> idName; ?> .ynresume-layout-list-item-action').addEvent('click', function(){
    	if ( this.hasClass('open-submenu') ) {
    		this.removeClass('open-submenu');	
    	} else {
    		$$('.open-submenu').removeClass('open-submenu');
    		this.addClass('open-submenu');
    	}

        var global_content = this.getParent('#global_content');
        var y_position = this.getPosition(global_content).y;
        var p_height = global_content.getHeight();
        var c_height = this.getChildren('.ynresume-show-options').getHeight();
        if(p_height - y_position < c_height)
        {
            this.addClass('ynresume-show-option-reverse');
        }

    });

    $$('.ynresume-layout-list-item-action').addEvent('outerClick', function(){
    	if ( this.hasClass('open-submenu') ) {
    		this.removeClass('open-submenu');	
    	}
    });
});
</script>
