<ul>
	<?php if ($this->settings->comment) : ?>
		<li><?php echo $this->translate('Allow members to comment on my contest.')?></li>
	<?php endif;?>
	
	<?php if ($this->settings->comment_entries) : ?>
		<li><?php echo $this->translate('Allow members to comment on entries.')?></li>
	<?php endif;?>
	
	
	
	<?php if ($this->settings->entries_approve) : ?>
		<li><?php echo $this->translate('New Entries can approve immediately.')?></li>
	<?php endif;?>
	
	
	<?php if ($this->settings->post_send_email) : ?>
		<li><?php echo $this->translate('Send me an email when there is a posted entry.')?></li>
	<?php endif;?>
	
	<li>
	<?php if($this->settings->max_entries == null):?>
		<?php echo $this->translate('Maximum entries which a member can submit. %s', 'unlimit')?>
	<?php else:?>
		<?php echo $this->translate('Maximum entries which a member can submit. %s', $this->settings->max_entries)?>
	<?php endif;?>
	</li>
</ul>