<div class="ynContest_PromoteWrapper">
	<div class="ynContest_campaign_PricePromote ynContest_subProperty">	
		<div class ='ynContest_campaign_photoColRight'>
			<a target="_blank" href="<?php echo $this->contest->getHref()?>"><?php echo $this->itemPhoto($this->contest, 'thumb.profile') ?></a>
		</div>	
		<?php echo $this->htmlLink($this->contest->getHref(), $this->string()->truncate($this->contest->getTitle(), 28), array('title' => $this->string()->stripTags($this->contest->getTitle()), 'class' => 'ynContest_campaignTitle')) ?>			
		<p class="ynContest_ownerStat">
			<?php echo $this->translate("Created by:");?>
			<a target="_blank" href="<?php echo $this->contest->getOwner()->getHref()?>"><?php echo $this->contest->getOwner()->getTitle();?> </a>
		</p>	
		<p class="ynfundraising_campaign_description">
			<?php echo $this->string()->truncate($this->string()->stripTags($this->contest->summary), 115);?>
		</p>		
	</div>
</div>
