<?php if (count($this -> users)):?>
	<ul class="ynbusinesspages_list_reviewed_users">
	<?php foreach ($this -> users as $user):?>
		<li>
		<?php echo $this-> htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('target' => '_blank'));?>
		<?php echo $this-> htmlLink($user->getHref(), $user->getTitle(), array('target' => '_blank', 'class' => 'user-review'));?>
		</li>
	<?php endforeach;?>
	</ul>
<?php else:?>
	<div class="tip">
	    <span>
	      <?php echo $this->translate('There are no users.') ?>
	    </span>
	</div>
<?php endif;?>