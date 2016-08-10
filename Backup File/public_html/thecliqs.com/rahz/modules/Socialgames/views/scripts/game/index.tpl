<script type='text/javascript' src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Socialgames/externals/scripts/script.js'; ?>"></script>
<?php $this->headTranslate(array("Remove favourite","Add favourite")) ?>
<h2 class='fl'><?php echo $this->game->title; ?></h2>
<?php if ($this->viewer()->getIdentity()) { ?>
<a href="javascript://void(0)" onclick="favourite(<?php echo $this->game->game_id; ?>)" class="favourite">
	<span id="fav_text">
		<?php if ($this->is_favourite) { ?>
			<i class="fa fa-star"></i> <?php echo $this->translate("Remove favourite");?></span>
		<?php } else { ?>
			<i class="fa fa-star-o"></i> <?php echo $this->translate("Add favourite");?></span>
		<?php } ?>
</a>
<?php } ?>
<div class='clr'></div>
<div class='game_button' <?php if ($this->is_played or !$this->viewer()->getIdentity()){ ?>style='display:none;'<?php } ?>>
	<button onclick='play(<?php echo $this->game->game_id; ?>)'><?php echo $this->translate("Ready to Play?");?></button>
</div>
<div class='game_object' <?php if ($this->is_played or !$this->viewer()->getIdentity()){ ?>style='display:block;'<?php } ?>>
	<?php if ($this->game->category!="GoodGames") { ?>
	<embed src="<?php echo $this->game->flash; ?>" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
	<?php } else { ?>
		<iframe src="<?php echo $this->game->flash; ?>" width="820" height="650" style="margin-left:-10px;"></iframe>
	<?php } ?>
</div>
<div class='mt-10 mb-10'>
	<?php echo $this->content()->renderWidget('core.comments'); ?>
</div>
<div class='clr'></div>
<?php if ($this->game->instruction) { ?>
	<h3 class='mt-10'><?php echo $this->translate("Instructions");?></h3>
	<div class='game_instruction'><?php echo $this->game->instruction; ?></div>
<?php } ?>
<h3 class='mt-10'><?php echo $this->translate("Game Info");?></h3>
<div class='game_image fl'>
	<img src='<?php echo $this->game->image; ?>' alt='<?php echo $this->game->title; ?>' title="<?php echo $this->game->title; ?>"/>
</div>
<div class='game_info fl'>
	<div class='widget_game_title'><?php echo $this->game->title; ?></div>
	<div class="mt-5">
		<i class="fa fa-user"></i> <?php echo $this->game->total_members; ?>  <?php echo $this->translate("players");?>  &nbsp;&nbsp;&nbsp;
		<i class="fa fa-tag"></i> <?php echo $this->game->category; ?> &nbsp;&nbsp;&nbsp;
		<i class="fa fa-eye"></i> <?php echo $this->game->total_views; ?>  <?php echo $this->translate("views");?> 
	</div>
	<div class='game_desc'><?php echo $this->game->description; ?></div>
</div>
<div class='clr'></div>
<?php if (count($this->users_played)) { ?>
<h3 class='mt-10'><?php echo $this->translate("Already played");?></h3>
<ul class='game_users'>
  <?php foreach( $this->users_played as $user ): ?>
    <li>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle())) ?>
    </li>
  <?php endforeach; ?>
   <div class='clr'></div>
</ul>
<?php } ?>