<ul class="ynultimatevideo_manage_menu">
	<li class="ynultimatevideo_manage_menu_item ynultimatevideo_myvideos <?php if($this -> controller == 'index' && $this -> action == 'manage') echo 'active'?>">
		<a href="<?php echo $this -> url(array('action' => 'manage'), 'ynultimatevideo_general', true)?>">
			<i class="fa fa-video-camera"></i>
			<?php echo $this -> translate("My Videos")?>
		</a>
	</li>
	<li class="ynultimatevideo_manage_menu_item ynultimatevideo_myfavorite <?php if($this -> controller == 'favorite') echo 'active'?>">
		<a href="<?php echo $this -> url(array('action' => 'favorite'), 'ynultimatevideo_general', true)?>">
			<i class="fa fa-heart"></i>
			<?php echo $this -> translate("My Favorite Videos")?>
		</a>
	</li>
	<li class="ynultimatevideo_manage_menu_item ynultimatevideo_myplaylists <?php if($this -> controller == 'playlist' && $this -> action == 'manage') echo 'active'?>">
		<a href="<?php echo $this -> url(array('action' => 'manage'), 'ynultimatevideo_playlist', true)?>">
			<i class="fa fa-file-text"></i>
			<?php echo $this -> translate("My Playlists")?>
		</a>
	</li>
	<li class="ynultimatevideo_manage_menu_item ynultimatevideo_watchlater <?php if($this -> controller == 'watch-later' && $this -> action == 'index') echo 'active'?>">
		<a href="<?php echo $this -> url(array('action' => 'index'), 'ynultimatevideo_watch_later', true)?>">
			<i class="fa fa-bookmark"></i>
			<?php echo $this -> translate("Watch Later")?>
		</a>
	</li>
	<li class="ynultimatevideo_manage_menu_item ynultimatevideo_myhistory <?php if($this -> controller == 'history') echo 'active'?>">
		<a href="<?php echo $this -> url(array('action' => 'index'), 'ynultimatevideo_history', true)?>">
			<i class="fa fa-clock-o"></i>
			<?php echo $this -> translate("History")?>
		</a>
	</li>
</ul>