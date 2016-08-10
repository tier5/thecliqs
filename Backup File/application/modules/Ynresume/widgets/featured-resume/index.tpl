<?php
    $this->headScript()
        ->appendFile($this->baseUrl() . '/application/modules/Ynresume/externals/scripts/jquery.min.js')
        ->appendFile($this->baseUrl() . '/application/modules/Ynresume/externals/scripts/jquery.flexslider.js');
    $this->headLink()
        ->appendStylesheet($this->baseUrl() . '/application/modules/Ynresume/externals/styles/flexslider.css');
	
	$viewer = Engine_Api::_() -> user() -> getViewer();
?>



<div id="ynresume-features-list" class="ynresume-features-list flexslider">
	<ul class="slides">
		<?php foreach ($this -> paginator as $resume) :?>
			<li>
				<div class="ynresume-features-item">
					<div class="ynresume-features-item-thumb">
						<div class="ynresume-features-item-thumb-border"></div>
						<?php echo Engine_Api::_()->ynresume()->getPhotoSpan($resume); ?>
					</div>					
					<div class="ynresume-features-item-main">
						<div class="ynresume-features-item-title">
							<a href="<?php echo $resume->getHref();?>"><?php echo $resume->getTitle()?></a>
							<div id="ynresume-feature-save-status-<?php echo $resume->getIdentity();?>">
							<i><?php echo ($resume -> hasSaved())? $this -> translate('(Saved)') : '';?></i>
	               		</div>
						</div>
						<div class="ynresume-features-item-info">
							<span><i class="fa fa-briefcase"></i> <?php echo $resume->getJobTitle();?></span>
							<span><i class="fa fa-building"></i> <?php echo $resume->getCompany();?></span>
						</div>
						<div class="ynresume-features-item-subline">
							<?php if ($resume->location) : ?>
								<span class="ynresume-features-item-location"><?php echo $resume->location?></span>
							<?php endif; ?>
							<span class="ynresume-features-item-position"> <?php $industry = $resume->getIndustry();?>
                        <?php echo ($industry) ? $industry->getTitle() : $this->translate('Unknown Industry');?></span>
						</div>
						<div class="ynresume-features-item-description">
							<?php echo $resume->getSummary();?>
						</div>
					</div>
					<div class="ynresume-features-item-footer">
						<?php if($viewer -> isSelf($resume -> getOwner())) :?>
		                	<div><a href="<?php echo $this->url(array('controller' => 'resume', 'action' => 'export-pdf', 'resume_id' => $resume->getIdentity()), 'ynresume_extended');?>"><span><i class="fa fa-file-pdf-o"></i> <span><?php echo $this -> translate('Save to PDF');?></span></span></a></div>
		                	<div class="ynresume-features-item-action">
			                    <span><i class="fa fa-ellipsis-h"></i> <span> <?php echo $this -> translate('More');?></span></span>
			                    <ul>
			                    	<li><?php echo $this->htmlLink(array('route'=>'ynresume_recommend','action'=>'ask'), '<i class="fa fa-question-circle"></i>'.$this->translate('Ask Recommendation'), array())?></li>
			                    	<li><a href="<?php echo $resume->getOwner()->getHref();?>"><i class="fa fa-eye"></i><?php echo $this -> translate('View Profile');?></a></li>
			                    </ul>       
		               		</div>  
	                	<?php else :?>
							<div><a class='smoothbox' href="<?php echo $this->url(array('action' => 'compose-message', 'to' => $resume -> getOwner() -> getIdentity()), 'ynresume_general', true);?>"><span><i class="fa fa-envelope"></i> <span><?php echo $this -> translate('Message');?></span></span></a></div>
							<div class="ynresume-features-item-action">
								<span><i class="fa fa-ellipsis-h"></i> <span> <?php echo $this -> translate('More');?></span></span>
								<ul>
								<li><a href="<?php echo $this -> url(array('controller' => 'resume', 'action' => 'export-pdf', 'resume_id' => $resume->getIdentity()), 'ynresume_extended');?>"><i class="fa fa-file-pdf-o"></i><?php echo $this -> translate('Save to PDF');?></a></li>	
								<?php if(!$viewer -> isSelf($resume -> getOwner())):?>
								<?php if($resume -> hasSkill()) :?>
									<li><a href="<?php echo $resume -> getHref();?>/endorse/1"><i class="fa fa-check-square-o"></i><?php echo $this -> translate('Endorse');?></a></li>
								<?php endif;?>
									<li><a href="<?php echo $this -> url(array('action' => 'give', 'receiver_id' => $resume -> getOwner() -> getIdentity()), 'ynresume_recommend', true);?>"><i class="fa fa-comments-o"></i><?php echo $this -> translate('Recommend');?></a></li>
								<?php endif;?>
								<?php if($this -> viewer() -> getIdentity()) :?>
								<li><a id="ynresume_save_feature<?php echo $resume -> getIdentity();?>" onclick="saveResumeFeature('<?php echo $resume -> getIdentity() ;?>');" href="javascript:;"><i class="fa fa-floppy-o"></i><?php echo ($resume -> hasSaved())? $this -> translate('Unsave Resume') : $this -> translate('Save Resume');?></a></li>
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
</div>

<?php 
    $themes = Engine_Api::_()->getDbtable('themes', 'core')->fetchAll();
    $activeTheme = $themes->getRowMatching('active', 1);
    $arrname = explode("-", $activeTheme->name);
    $name_theme = $arrname[0];
 ?>

<script type="text/javascript">
    window.addEvent('domready', function(){
        $$('body')[0].addClass('<?php echo $name_theme; ?>');
    });
</script>

<script type="text/javascript">
	
	<?php if($this -> viewer() -> getIdentity()) :?>
	function saveResumeFeature(id)
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
	            	$("ynresume_save_feature"+id).set("html", '<i class="fa fa-floppy-o"></i><?php echo $this -> translate("Unsave Resume");?>');
	            	$("ynresume-feature-save-status-"+id).set("html", '<i><?php echo $this -> translate("(Saved)");?></i>');
	            }
	            else
	            {
	            	$("ynresume_save_feature"+id).set("html", '<i class="fa fa-floppy-o"></i><?php echo $this -> translate("Save Resume");?>');
	            	$("ynresume-feature-save-status-"+id).set("html", '');
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
	
    // Can also be used with $(document).ready()
    jQuery.noConflict();
    (function($) { 
    	
    	function getItemSize() {
    		if ( jQuery('#ynresume-features-list').innerWidth() > 600 ) {
    			return 2;
    		}

    		return 1;
		}

        $(window).load(function() {
            $('#ynresume-features-list').flexslider({
                animation: "slide",
                controlNav: false,
                pauseOnHover: true,
                prevText: "",
                nextText: "",
                itemWidth: 220,
                itemMargin: 20,
                minItems: 1,
                maxItems: getItemSize(),
            });
        });
    })(jQuery);

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

    $$('#ynresume-features-list .ynresume-features-item-action').addEvent('outerClick', function(){
    	if ( this.hasClass('open-submenu') ) {
    		this.removeClass('open-submenu');	
    	}
    });

	$$('#ynresume-features-list .ynresume-features-item-action').addEvent('click', function(){
		if ( this.hasClass('open-submenu') ) {
    		this.removeClass('open-submenu');	
    	} else {
    		$$('.open-submenu').removeClass('open-submenu');
    		this.addClass('open-submenu');
    	}  
	});
</script>

