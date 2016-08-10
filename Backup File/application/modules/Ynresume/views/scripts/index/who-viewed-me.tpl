<?php if($this -> error) :?>
	<div class="tip">
		<span>
			<?php echo $this->translate('Please create resume first before using this service.') ?>
		</span>
    </div>
<?php else:?>
<?php if($this -> resume -> serviced) :?>
	<?php
			$serviceDateObj = new Zend_Date(strtotime($this -> resume -> service_expiration_date));	
			if( isset($this->viewer) && $this->viewer->getIdentity() ) {
				$tz = $this->viewer->timezone;
				if (!is_null($serviceDateObj))
				{
					$serviceDateObj->setTimezone($tz);
				}
		    }
	?>
	<div class="">
	<?php echo $this -> translate("You are using <span class='ynresume-who-view-me-title'>\"Who Viewed Me\"</span> service and it is valid until %s", (!is_null($serviceDateObj)) ? "<span style='color:red'>".date('M d Y', $serviceDateObj -> getTimestamp())."</span>"  : '');?>
	</div>
	<div class="">
	<?php echo $this -> translate("<a class='smoothbox button bold' href='%s'>Click here</a> to expand the duration of this service", $this -> url(array('action' => 'service', 'resume_id' => $this -> resume -> getIdentity()), 'ynresume_specific', true));?>
	</div>

<?php else:?>
	<div class="ynresume-who-viewed-resume-description"><?php echo $this -> translate('See the full list of <span>%1s people</span> who viewed your resume by using %2s service', $this -> total, $this -> htmlLink($this -> url(array('action' => 'service', 'resume_id' => $this -> resume -> getIdentity()), 'ynresume_specific', true),$this -> translate("\"Who Viewed Me\""), array('class' => 'smoothbox'))) ?></div>
<?php endif;?>

<?php if( count($this->paginator) > 0 ): ?>
	<div class="ynresume-user-item-list">
	<?php foreach($this -> paginator as $viewer) :?>
			<?php $user = Engine_Api::_() -> getItem('user', $viewer -> user_id); ?>
			<?php if($user -> getIdentity()) :?>
				<li>
				<?php $resume = Engine_Api::_() -> ynresume() -> getUserResume($user -> getIdentity());?>	
				<div class="ynresume-user-item">
					<div class="ynresume-user-item-thumb">
						<?php echo Engine_Api::_()->ynresume()->getPhotoSpan($user, 'thumb.main'); ?>
					</div>					
					<div class="ynresume-user-item-main">
						<div class="ynresume-user-item-title">
							<?php if(!empty($resume)) :?>
								<?php echo $this -> htmlLink($resume -> getHref(), $user -> getTitle());?>
						<div id="ynresume-view-save-status-<?php echo $resume->getIdentity();?>">
							<i><?php echo ($resume -> hasSaved())? $this -> translate('(Saved)') : '';?></i>
	               		</div>
					<?php else:?>
						<?php echo $this -> htmlLink($user -> getHref(), $user -> getTitle());?>
					<?php endif;?>
				</div>
					<div class="ynresume-user-item-subline">
						<span><i class="fa fa-briefcase"></i> <?php if(!empty($resume))  echo $resume->getJobTitle();?></span>
						<span><i class="fa fa-building"></i> <?php if(!empty($resume)) echo $resume->getCompany();?></span>
					</div>
					<div class="ynresume-user-item-subline">
						<span class="ynresume-user-item-location"><?php if(!empty($resume)) echo $resume->location?></span>
						<span class="ynresume-user-item-position"><?php if(!empty($resume)) $industry = $resume->getIndustry();?>
	                        <?php echo ($industry) ? $industry->getTitle() : $this->translate('Unknown Industry');?></span>
					</div>
			</div>
			<div class="ynresume-user-item-footer">
				<div><a class='smoothbox' href="<?php echo $this->url(array('action' => 'compose-message', 'to' => $user -> getIdentity()), 'ynresume_general', true);?>"><span><i class="fa fa-envelope"></i> <span><?php echo $this -> translate('Message');?></span></span></a></div>	
				<div class="ynresume-user-item-action">
					<span><i class="fa fa-ellipsis-h"></i> <span> <?php echo $this -> translate('More');?></span></span>
					<ul class="ynresume-show-options">
						<?php if(!empty($resume)) :?>	
							<li><a href="<?php echo $this -> url(array('controller' => 'resume', 'action' => 'export-pdf', 'resume_id' => $resume->getIdentity()), 'ynresume_extended');?>"><i class="fa fa-file-pdf-o"></i><?php echo $this -> translate('Save to PDF');?></a></li>
							<?php if($resume -> hasSkill()):?>
								<li><a href="<?php echo $resume -> getHref();?>/endorse/1"><i class="fa fa-check-square-o"></i><?php echo $this -> translate('Endorse');?></a></li>
							<?php endif;?>
							<li><a href="<?php echo $this -> url(array('action' => 'give', 'receiver_id' => $user -> getIdentity()), 'ynresume_recommend', true);?>"><i class="fa fa-comments-o"></i><?php echo $this -> translate('Recommend');?></a></li>
							<li><a id="ynresume_save_<?php echo $resume -> getIdentity();?>" onclick="saveResume('<?php echo $resume -> getIdentity() ;?>');" href="javascript:;"><i class="fa fa-floppy-o"></i><?php if ($resume -> hasSaved()){ echo $this -> translate('Unsave Resume'); echo $this -> translate('Save Resume');}?></a></li>
							<li><a href="javascript:void(0)" class="ynresume_favourite_<?php echo $resume -> getIdentity();?>" onclick="favouriteResume('<?php echo $resume -> getIdentity() ;?>');"><i class="fa fa-star-o"></i><?php echo ($resume -> hasFavourited()) ? $this -> translate('Unfavourite') : $this -> translate('Favourite');?></a></li>
						<?php endif;?>	
							<li><a href="<?php echo $user->getHref();?>"><i class="fa fa-eye"></i><?php echo $this -> translate('View Profile');?></a></li>
					</ul>		
				</div>					
			</div>
		</div>
		</li>
		<?php endif;?>
	<?php endforeach;?> 
		<!-- if resume no service and has more person or people to show -->
		<?php if(!$this -> resume -> serviced) :?>
			<?php if($this->total > 2) :?>
				<li>
					<div class="ynresume-user-item-more">
						<span><?php echo $this->total - 2; ?></span>
						<span><?php echo $this -> translate("more person viewed you") ?></span>
						<div class="ynresume-who-viewed-resume-description"><?php echo $this -> translate('See the full list of <span>%1s people</span> who viewed your resume by using \"Who Viewed Me\" service', $this -> total) ?></div> 
						<div class="register-who-viewed-me"></div>
						<?php echo $this -> htmlLink($this -> url(array('action' => 'service', 'resume_id' => $this -> resume -> getIdentity()), 'ynresume_specific', true),$this -> translate("Register Service"), array('class' => 'smoothbox button')); ?>	
					</div>
				</li>
			<?php endif;?>
		<?php endif;?>
		<!-- endif -->
	</div>
	
<?php else: ?>
	<br />
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no viewers yet.') ?>
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

<script text="text/javascript">
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

    $$('.ynresume-user-item-list .ynresume-user-item-action').addEvent('outerClick', function(){
    	if ( this.hasClass('open-submenu') ) {
    		this.removeClass('open-submenu');	
    	}
    });

	$$('.ynresume-user-item-list .ynresume-user-item-action').addEvent('click', function(){
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
	if($$('.ynresume-user-item').length > 0)
		$$('.ynresume-user-item-more').setStyle('height', $$('.ynresume-user-item')[0].getSize().y );
	var timer;
	window.addEvent('resize', function(){
		$clear(timer);
		timer = (function()
		{
			if($$('.ynresume-user-item').length > 0)
		    	$$('.ynresume-user-item-more').setStyle('height', $$('.ynresume-user-item')[0].getSize().y );
		}).delay(50);
	});
</script>
<?php endif;?>

<script type="text/javascript">
	
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
	            	if($("ynresume_save_"+id))
	            		$("ynresume_save_"+id).set("html", '<i class="fa fa-floppy-o"></i><?php echo $this -> translate("Unsave Resume");?>');
	            	if($("ynresume-view-save-status-"+id))
	            		$("ynresume-view-save-status-"+id).set("html", '<i><?php echo $this -> translate("(Saved)");?></i>');
	            }
	            else
	            {
	            	if($("ynresume_save_"+id))
	            		$("ynresume_save_"+id).set("html", '<i class="fa fa-floppy-o"></i><?php echo $this -> translate("Save Resume");?>');
	            	if($("ynresume-view-save-status-"+id))
	            		$("ynresume-view-save-status-"+id).set("html", '');
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
</script>