

<?php if($this->tab == 'tab21'):?>
<div id="ynContest_win_entry_<?php echo $this->entries->getIdentity()?>" class="checksub">
	<input id="ynContest_win_entry_checkbox_<?php echo $this->entries->getIdentity()?>" type="checkbox" 
	
	<?php if($this->entries->waiting_win == 1):?> checked
	
	<?php endif;?>
	 class="checkbox" onclick="entryChoose(<?php echo $this->entries->getIdentity(); ?>,this)"  >
</div>

<?php endif;?>
<div>
<div class="ynContestSubmit_thumb_wrapper ynContest_thumb_wrapper">
  <?php echo $this->htmlLink($this->entries->getHref(), $this->itemPhoto($this->entries, 'thumb.profile')) ?> 
</div>
<?php 
	echo $this->htmlLink($this->entries->getHref(), 
		$this->string()->truncate($this->entries->getTitle(), 30), 
		array('class' => 'ynContest_thumbEntries_title', 'title' => $this->entries->getTitle())) 
?>
<div class="ynContest_author">
	<span>
		<?php $user = $this->entries->getOwner() ?>
		<?php if ($user) : ?>
			<?php echo $this->translate('By') ?>
			<?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
		<?php endif; ?>
	</span>
    | 
    <span class="ynContest_views">
        <?php if (!isset($this->infoCol) || ($this->infoCol == 'view')) : ?>
            <?php echo $this->translate('%1$s vote(s)', $this->locale()->toNumber($this->entries->vote_count)) ?>
        <?php endif; ?>
    </span>    
</div> 


<!--div>
	<div class="ynContestSubmit_thumb_wrapper ynContest_thumb_wrapper">
	  <?php echo $this->htmlLink($this->entries->getHref(), $this->itemPhoto($this->entries, 'thumb.icon')) ?> 
	</div>
	<?php 
		echo $this->htmlLink($this->entries->getHref(), 
			$this->string()->truncate($this->entries->getTitle(), 30), 
			array('class' => 'ynContest_thumbEntries_title', 'title' => $this->entries->getTitle())) 
	?>
	<div class="ynContest_author">
		<span>
			<?php $user = $this->entries->getOwner() ?>
			<?php if ($user) : ?>
				<?php echo $this->translate('By') ?>
				<?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
			<?php endif; ?>
		</span>
		|
		<span class="ynContest_views">
			<?php if (!isset($this->infoCol) || ($this->infoCol == 'view')) : ?>
				<?php echo $this->translate('%1$s vote(s)', $this->locale()->toNumber($this->entries->vote_count)) ?>
			<?php endif; ?>
		</span>    
	</div> 
</div-->

</div>
