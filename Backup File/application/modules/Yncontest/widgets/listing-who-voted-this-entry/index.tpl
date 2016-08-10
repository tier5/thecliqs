<div>
<?php foreach ($this->users as $user) : ?>
	<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'thumb')) ?>
<?php endforeach;?>
</div>