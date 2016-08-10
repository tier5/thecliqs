<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<h3><?php echo $this->translate("Already played games") ?></h3>
<?php foreach ($this->users_played as $item): ?>
		<div class='game_block' <?php if ($item->is_featured) { ?>style="border-color:#e3c1c1;"<?php } ?>>
			<div class='game_category'>
				<?php echo $item->category; ?>
			</div>
			<?php if ($item->is_featured) { ?>
				<i class="fa fa-thumbs-up featured" title="Featured"></i>
			<?php } ?>
			<a href='<?php echo $this->url(array('game_id' => $item->game_id,'slug' => $item->title), "games_view"); ?>'><img src='<?php echo $item->image; ?>' alt='<?php echo $item->title; ?>' title="<?php echo $item->title; ?>"/></a>
			<div class='widget_game_title'><a href='<?php echo $this->url(array('game_id' => $item->game_id,'slug' => $item->title), "games_view"); ?>'><?php echo $item->title; ?></a></div>
			<div class='desc'>
				<?php echo $this->string()->truncate($this->string()->stripTags($item->description), 100) ?>
			</div>
			<div class='game_profile'>
				<div class='fl'>
					<i class="fa fa-user"></i> <?php echo $item->total_members; ?> <?php echo $this->translate("players") ?>
				</div>
				<div class='fr'>
					<i class="fa fa-eye"></i> <?php echo $item->total_views; ?> <?php echo $this->translate("views") ?>
				</div>
				<div class='clr'></div>
			</div>
		</div>
 <?php endforeach; ?>