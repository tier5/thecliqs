<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<?php foreach ($this->paginator as $item): ?>
<div class='widget_game'>
	<div class='widget_game_image'>
		<a href='<?php echo $this->url(array('game_id' => $item->game_id,'slug' => $item->title), "games_view"); ?>'><img src='<?php echo $item->image; ?>' alt='<?php echo $item->title; ?>' title="<?php echo $item->title; ?>"/></a>
	</div>
	<div class='widget_game_title'><a href='<?php echo $this->url(array('game_id' => $item->game_id,'slug' => $item->title), "games_view"); ?>'><?php echo $item->title; ?></a></div>
	<div class='widget_category'><?php echo $item->category; ?></div>
	<div class='fl'>
		<i class="fa fa-user"></i> <?php echo $item->total_members; ?> <?php echo $this->translate("players") ?>
	</div>
	<div class='fr'>
		<i class="fa fa-eye"></i> <?php echo $item->total_views; ?> <?php echo $this->translate("views") ?>
	</div>
	<div class='clr'></div>
</div>
 <?php endforeach; ?>