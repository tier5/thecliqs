<div class="ynContest_newEntryWrapper">
	<div class="global_form_box">	
		<div>
		  <ul class="thumbs ynContest_thumbsAlbumSmall">
			 <?php 
				foreach($this->entries as $entry ):								
			 ?>
			  <li id="thumbs-photo-album-<?php echo $entry->entry_id; ?>">
				<?php
					/*$item =  Engine_Api::_()->yncontest()->getEntryThumnail($entry->entry_type,$entry->item_id);
										
					$src = "";
					if(is_object($item))
						if($entry->entry_type == 'ynblog'){
							//$src = $this->itemPhoto($entry->getOwner(), 'thumb.profile');
							$src = $entry->getOwner()->getPhotoUrl('thumb.profile');	
						}else {
							$src =  $item->getPhotoUrl('thumb.normal');
						}
					else {
					 	$src ='';
					}	*/				
				?>	
				<?php
					if ($entry->entry_type == 'ynblog') {
						$src = $entry->getPhotoUrl("thumb.profile");
					} else {
						$src = $entry->getPhotoUrl("thumb.profile");
					}
				?>
				<a class="thumbs_photo" href="<?php echo $entry->getHref(); ?>">
					
				<?php if(!$src):?>
					<span style="">
						<?php echo $this->itemPhoto($entry, "thumb.profile", "",array('style'=>'max-width:none;'))?>
					</span>	
				<?php else:?>									
					<span style="background-image: url(<?php echo $src; ?>);"></span>
				<?php endif;?>	
						
				</a>
				
					
				<p class="thumbs_info">
					<span class="thumbs_title">
						<?php echo $this->htmlLink($entry->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($entry->entry_name),20), 30, "\n", true), array('title'=>$entry->entry_name)); ?>						
					</span>
				   <?php echo $this->translate('By ').$entry->getOwner(); ?>
				</p>
			  </li>
			<?php endforeach;?>
		  </ul>
		</div>		
		
		<?php //if ($this->totalItems > 0) { ?> 
			<div class="yncontest_view_more ynContest_viewAll">
				<?php /*if ($this->totalItems > 6) {
						echo $this->htmlLink(array(
							'route' => 'yncontest_general', 
							'action' => 'entries', 
							'status' => 'published',		  		
							),
							"<span>&rsaquo;</span>".$this->translate('View more'), 
							array('class' => 'contest_viewmore'));
						
					?>					
					<?php } else echo "&nbsp;"; */?>
			</div>
		<?php //} ?>		
	</div>
</div>