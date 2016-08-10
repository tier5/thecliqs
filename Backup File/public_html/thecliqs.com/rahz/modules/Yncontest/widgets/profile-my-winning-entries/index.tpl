<?php 

if( count($this->paginator) > 0 ): ?>
	
	<ul class="ynContest_listCompare thumbs ynContest_listCompareShort">
		<?php 	
	foreach ($this->paginator as $item):		
	 ?>
		<li>
			<div>
				<?php
					/*$entry =  Engine_Api::_()->yncontest()->getEntryThumnail($item->entry_type,$item->item_id);
										
					$src = "";
					if(is_object($entry))
						if($item->entry_type == 'ynblog'){
							//$src = $this->itemPhoto($entry->getOwner(), 'thumb.profile');
							$src = $item->getOwner()->getPhotoUrl('thumb.profile');	
						}else {
							$src =  $entry->getPhotoUrl('thumb.normal');
						}
					else {
					 	$src ='';
					}*/					
				?>
				<a class="thumbs_photo" href="<?php echo $item->getHref(); ?>">
					<?php $src = $item->getPhotoUrl("thumb.normal")?>
				<?php if(!$src):?>
					<span style="">
						<?php echo $this->itemPhoto($item, "thumb.normal", "",array('style'=>'max-width:none;'))?>
					</span>	
				<?php else:?>
				
					<span style="background-image: url(<?php echo $item->getPhotoUrl("thumb.normal"); ?>);"></span>
				<?php endif;?>	
				<p class="ynContest_listCompareInfo thumbs_info">
					<span class="thumbs_title">						
						<?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->yncontest()->subPhrase($item->entry_name, 50), array('title' => $this->string()->stripTags($item->entry_name)));?>						
					</span>
					<?php 
						$contest = Engine_Api::_()->getItemTable('contest')->find($item->contest_id)->current();       
	        			echo $this->translate("On").": ".$this->htmlLink($contest->getHref(), $contest->getTitle());  
					?>
					<br/>
					<?php echo $this->translate("Votes").": ".$item->vote_count;?>
					<br/>
					<?php echo $this->translate("View").": ".$item->view_count; ?>
					<br/>
					<?php echo $this->translate("Like").": ".$item->like_count; ?>								
									
				</p>
			</div>			
		</li>
		
	<?php endforeach; ?>
	</ul>
<?php endif; ?>  
