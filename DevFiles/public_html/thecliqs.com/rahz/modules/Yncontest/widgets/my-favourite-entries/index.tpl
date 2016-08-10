 <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
 <h3> <?php echo $this->translate('Favorite entries'); ?> </h3>
 <ul id ="ynul" class="main_entries_list">

<?php foreach($this->paginator as $item):?>
	
	
	 <li>		
		<div class="recent_entries_img">
			<?php
			//$photo =  Engine_Api::_()->yncontest()->getEntryThumnail($item->entry_type,$item->item_id);
			//print_r($photo);die;
			
			echo $this->itemPhoto($item, 'thumb.normal') ?>
		</div>
				
		<div class="entries_info">
			<div class="entries_title"> <?php echo $item ?></div>
			<div class="entries_browse_info_date">
			<?php echo $this->translate('Posted by %s', $this->htmlLink($item->getOwner(), $item->getOwner()->getTitle())) ?>			
			</div>
			<div class="entries_description"> <?php echo Engine_Api::_()->yncontest()->subPhrase($item->summary,200); ?> </div>
			
			<div class="entries_favourite">
				<?php echo $this->favouriteEntries($item) ?>
			</div>
		
	 	</div>
		
		<div class = "yncontest_clear"></div>
	</li>
<?php endforeach; ?>
</ul>

  <?php else:?>
<div class="tip">
  <span>
    <?php echo $this->translate('You have no favorite entries.');?>
  </span>
</div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
'pageAsQuery' => true,
'query' => '',
'params' => $this->formValues,
  )); ?>
