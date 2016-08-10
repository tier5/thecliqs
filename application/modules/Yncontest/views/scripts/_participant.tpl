<div id="yncontest_participants_list">
	<ul id="list_member" class='ynContest_participantsList'>
	   
	      <li>
			<?php echo $this->htmlLink($this->member->getHref(), $this->itemPhoto($this->member, 'thumb.icon'), array('class' => 'ynContest_LRH3ULLi_thumb')) ?>
	        <div class = "ynContest_LRH3ULLi_info">		
				<div class='ynContest_LRH3ULLi_name'>
					<?php echo $this->htmlLink($this->member->getHref(),  $this->member->getTitle()) ?>	
				</div>
				<div class='ynContest_LRH3ULLi_listInfo'>
					<span><?php echo $this->translate('Joined date:')?> <?php echo $this->locale()->toDate($this->member->creation_date, array('size' => 'short')); ?></span> - 
					<span> <?php echo $this->translate('Entries:')?>
		        		<?php echo $this->entries; ?>
					</span>
				</div>	
			</div>	
	      </li>
	   
	</ul>				
	<div>