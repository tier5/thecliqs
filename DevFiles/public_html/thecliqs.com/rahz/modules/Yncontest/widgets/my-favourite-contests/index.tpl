<?php if( count($this->paginator) > 0 ): ?>
	
	<ul class="ynContest_listCompare thumbs ynContest_listCompareLong">
		<?php foreach ($this->paginator as $item): ?>
		<li>
			<div>
				<a class="thumbs_photo" href="<?php echo $item->getHref(); ?>">
					<span style="background-image: url(<?php echo $item->getPhotoUrl('thumb.normal');?>);"></span>	
				</a>
				<p class="ynContest_listCompareInfo thumbs_info">
					<span class="thumbs_title">						
						<?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->yncontest()->subPhrase($item->contest_name, 50), array('title' => $this->string()->stripTags($entry->entry_name)));?>						
					</span>
					<?php echo $this->translate("Contest type").": ".Engine_Api::_()->yncontest()->arrPlugins[$item->contest_type]; ?>
					<br/>
					<?php echo $this->translate("Status").": ".$item->contest_status;?>
					<br/>
					<?php echo $this->translate("Start date").": ".str_replace("/", "-",$this->locale()->toDate( $item->start_date, array('size' => 'long')))." "; ?>
					<br/>
					<?php echo $this->translate("End date").": ".str_replace("/", "-",$this->locale()->toDate( $item->end_date, array('size' => 'long'))); ?>
					<br/>
					<?php echo $this->translate("Entries").": ".$item->entries;?>
					<br/>
					<?php echo $this->translate("Views").": ".$item->view_count;?>	
					<?php echo $this->translate("Likes").": ".$item->like_count;?>	
				</p>
			</div>
			
			<div class="ynContest_compareEntries">
				<?php 					
					echo $this->htmlLink(
						  array('route' => 'yncontest_mycontest', 'action' => 'un-favourite', 'contestId' => $item->contest_id),
						  $this->translate('Unfavorite'),
						  array('class' => 'smoothbox buttonlink menu_yncontest_unfavourite'));					
				 ?>
			</div>
			
		</li>
		<?php endforeach; ?>
	</ul>	
	
<?php else:?>
		<div class="tip">
			<span>
				<?php echo $this->translate('You have no favorite contests.');?>
			</span>
		</div>
<?php endif; ?>
  
<?php echo $this->paginationControl($this->paginator, null, null, array(
			'pageAsQuery' => true,
			'query' => '',
			'params' => $this->formValues,
			  )); 
?>