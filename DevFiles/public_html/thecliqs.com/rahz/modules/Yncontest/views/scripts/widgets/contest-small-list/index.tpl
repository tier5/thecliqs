<?php if($this->totalItems > 0): ?>
<ul class="ynContest_LRH3ULLi">
	<?php foreach($this->items as $item): ?>
		<?php if(Engine_Api::_()->user()->getUser($item->user_id)->getIdentity() != 0): ?>
		<li>
			<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon', $item->getTitle()), array('class' => 'ynContest_LRH3ULLi_thumb')) ?>					
			<div class='ynContest_LRH3ULLi_info'>
				<div class='ynContest_LRH3ULLi_name'>
					<?php echo $this->htmlLink($item->getHref(),wordwrap(Engine_Api::_()->yncontest()->subPhrase(strip_tags($item->contest_name),30), 48, "\n", true), array('title'=>$item->getTitle())); ?>					
				</div>	
				<p class="ynContest_LRH3ULLi_listInfo">
					<span>
						<?php 
						$pa = ($item->participants!=0)? $item->participants :'0';
						echo $this->translate('Participants').": ".$pa;?>
					</span> <br/>
					<span>
						<?php 
						$en = ($item->entries!=0)? $item->entries :'0';
						echo $this->translate('Entries').": ".$en;?>
					</span>
				</p>
			</div>			
		</li>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php if($this->totalItems > 5): ?>
	<div class="ynContest_rightColViewAll ynContest_viewAll">
		<?php 
			if($this->typed ==2):
			echo $this->htmlLink(array(
			'route' => 'yncontest_general', 
			'action' => 'listing', 
			'typed' => $this->typed,		  		
			),
			"<span>&rsaquo;</span>".$this->translate('View more'), 
			array('class' => 'contest_viewmore'));
			endif;
		?>		
	</div>	
	<?php endif; ?>
</ul>
<?php endif; ?>