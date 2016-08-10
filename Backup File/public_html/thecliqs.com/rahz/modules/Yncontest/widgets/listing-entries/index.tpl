<?php if( count($this->paginator) > 0 ): ?>
	<?php foreach ($this->paginator as $entry): ?>
	<ul class="ynContest_listCompare thumbs">
		<?php foreach ($this->paginator as $entry): ?>
		<?php if($entry -> checkVote()):?>
		<li>
			<div>
				<?php
				/*	$item =  Engine_Api::_()->yncontest()->getEntryThumnail($entry->entry_type,$entry->item_id);
										
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
				<a class="thumbs_photo" href="<?php echo $entry->getHref(); ?>">
					<?php $src = $entry->getPhotoUrl("thumb.profile")?>
				<?php if(!$src):?>
					<span style="">
						<?php echo $this->itemPhoto($entry, "thumb.profile", "",array('style'=>'max-width:none;'))?>
					</span>	
				<?php else:?>
				
					<span style="background-image: url(<?php echo $entry->getPhotoUrl("thumb.profile"); ?>);"></span>
				<?php endif;?>	
						
				</a>		
				
				<p class="ynContest_listCompareInfo thumbs_info">
					<span class="thumbs_title">						
						<?php echo $this->htmlLink($entry->getHref(), Engine_Api::_()->yncontest()->subPhrase($entry->entry_name, 47), array('title' => $this->string()->stripTags($entry->entry_name)));?>						
					</span>
					<?php
						$contest = Engine_Api::_()->getDbTable('contests','yncontest')->find($entry->contest_id)->current(); 
						echo $this->translate('On').' '.$this->htmlLink($contest->getHref(),Engine_Api::_()->yncontest()->subPhrase($entry->contest_name,25)); 
					?><br/>					
					<?php echo $this->translate('Created by %s', $this->htmlLink($entry->getOwner(),  Engine_Api::_()->yncontest()->subPhrase($entry->getOwner()->getTitle(),12), array('title'=>$entry->getOwner()->getTitle())))?><br/>					
					<?php echo $this->translate("Vote: ").$entry->vote_count." - ". $this->translate("Like: "). $entry->like_count; ?><br/>	
				</p>			
			</div>
			
		</li>
		<?php endif; ?>
		<?php endforeach;?>			
	</ul>
	<?php endforeach; ?>	
<?php if( count($this->paginator) > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
          )); ?>
      <?php endif; ?>
 
    <?php else: ?>
  <div class="tip">
    <span>
    <?php echo $this->translate('There are no entries.') ?>        
    </span>
  </div>
<?php endif; ?>