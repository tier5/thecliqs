<ul class="yncontest_slideshow_container" id ="yncontest_slideshow_container">
 <li class="yncontest_li_info">
 	<div id ="slide-runner-widget" class='slideshow'>
	<?php	
	
	 foreach($this->paginator as $contest):	
		 
	 ?>
		 <div class="slide featured_contest">
		 	<div style="clear: both">
			 	<div class="slideshow_left left">
			 		<!--<img width="" height="" src="http://1.bp.blogspot.com/-LATJ6RUf5BE/UGhVe4Z-cGI/AAAAAAAAAHw/4vGPUG_qJNE/s320/google+dance.jpeg" />-->
			 			<div class="contest_photo">
			              <?php echo $this->htmlLink($contest->getHref(), $this->itemPhoto($contest, 'thumb.profile')) ?>
			            </div>
          
          <div class="contest_description">
            <?php echo wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($contest->description),250), 48, "\n", true); ?>
          </div>
          
          <div class="view_more"> <?php echo $this->htmlLink($contest->getHref(), $this->translate("View detail"));?></div>
			 	</div>
			 	<div class="slideshow_right right">
			 		<div class="title">
			 			<?php $contest_title = Engine_Api::_()->yncontest()->subPhrase($contest->getTitle(),20);
					     echo $this->htmlLink($contest->getHref(), $contest_title);
						 ?>
			 		</div>
			 		<div class="rate"></div>
			 			<span></span> <span></span>		 		
			 		<div class="modification_date">
			 			 <span class="createday"><?php echo $this->translate('Start date')?></span>: <span class=""><?php echo $this->timestamp($contest->start_date);?></span>
			 		</div>
			 		<div class="author">
			 			 <span><?php echo $this->translate('Author')?></span>: <span class=""><?php echo $contest->getOwner();?></span>
			 		</div>
			 		<div class="author">
			 			 <span><?php echo $this->translate('Assignment ends')?></span>: <span class=""><?php echo $this->timestamp($contest->end_date);?></span>
			 		</div>
			 		<div class="author">
			 			 <span><?php echo $this->translate('Participants')?></span>: <span class=""><?php echo $contest->participants;?></span>
			 		</div>
			 		<div class="author">
			 			 <span><?php echo $this->translate('Entries')?></span>: <span class=""><?php echo $contest->entries;?></span>
			 		</div>
			 		<div class="author">
			 			 <span><?php echo $this->translate('Total awards')?></span>: <span class=""><?php echo number_format($contest->totalawards,0);?></span>
			 		</div>
                                                            
			 		<?php 
			 			$awards = Engine_Api::_()->yncontest()->getAwardByContest($contest->contest_id);						
			 			foreach($awards AS $award):
			 		?>
			 			<div>
              <div class="span_award_<?php echo $award->award_type?>"><span class = "bg_right"><?php echo $award->award_name ?></span></div>
              <?php if($award->award_type == 1):?>
              <span class="value_award_<?php echo $award->award_type?>"><?php echo $award->currency.$award->value ?></span>
              <?php else:?>
              <span class="value_award_<?php echo $award->award_type?>"><?php echo $award->description ?></span>
              <?php endif;?>
              <span class="number_award_<?php echo $award->award_type?>"><?php echo $award->quantities ?></span>
			 			</div>
			 		<?php endforeach;?>
				</div>
				
		 	</div>
		
		</div>
	<?php endforeach;
	?>
  </li>
  <?php   
  if($this->totalentries > 0):?>
   <li class="yncontest_li_avatar">
 		<?php  			
 			foreach($this->entries AS $entry):
 				echo $this->htmlLink($entry->getOwner()->getHref(),$this->itemPhoto($entry->getOwner(),'thumb.normal'));
 			endforeach;
 		?>
	</li>	
	<?php endif; ?>	
		
	
  
</ul>

 <script type="text/javascript">
    jQuery(document).ready(function(){
        var slideWidth = 508;
        /* call divSlideShow without parameters */
        jQuery('.slideshow').divSlideShow({
        width: slideWidth,
        loop:1000,
        arrow:'begin',
        controlClass:'slideshow_action',
        controlActiveClass:'slideshow_action_active'
        });
    });
</script>