<ul>
  <?php foreach( $this->users_played as $user ): if ($user->total_played) :?>
    <li class='fl mr-10'>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle())) ?>
    </li>
	<li class='fl'>
		<div ><b><?php echo $this->htmlLink($user->getHref(),  $user->getTitle()) ?></b></div>
		<?php echo $user->total_played; ?> <?php echo $this->translate("games played"); ?> 
	</li>
	 <div class='clr'></div>
  <?php endif; endforeach; ?>
   <div class='clr'></div>
</ul>