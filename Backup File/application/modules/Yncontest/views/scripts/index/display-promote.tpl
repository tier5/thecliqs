<div class="ynContest_PromoteWrapper">
	<div class="ynContest_contestPromote ynContest_subProperty">	
		<?php echo $this->htmlLink($this->contest->getHref(), $this->string()->truncate($this->contest->getTitle(), 28), array('title' => $this->string()->stripTags($this->contest->getTitle()), 'class' => 'ynContest_promoteTitle','target'=> '_blank')) ?>
		<p class="ynContest_ownerStat">
			<?php echo $this->translate("Created by");?>
			<a target="_blank" href="<?php echo $this->contest->getOwner()->getHref()?>"><?php echo $this->contest->getOwner()->getTitle();?> </a>
		</p>
		<div class ='ynContest_promote_photoColRight'>
			<a target="_blank" href="<?php echo $this->contest->getHref()?>"><?php echo $this->itemPhoto($this->contest, 'thumb.profile') ?></a>
		</div>		
		<p class="ynContest_promoteDesc">
			<?php echo $this->string()->truncate($this->string()->stripTags($this->contest->description), 115);?>
		</p>				
	</div>
</div>
