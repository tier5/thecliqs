<ul class="ynvideochannel_manage_menu">
	<li class="ynvideochannel_manage_menu_item ynvideochannel_myvideos <?php if($this -> action == 'manage-videos') echo 'active'?>">
		<a href="<?php echo $this -> url(array('action' => 'manage-videos'), 'ynvideochannel_general', true)?>">
			<i class="fa fa-video-camera"></i>
			<?php echo $this -> translate("My Videos")?>
		</a>
	</li>
	<li class="ynvideochannel_manage_menu_item ynvideochannel_myfavorite <?php if($this -> action == 'favorites') echo 'active'?>">
		<a href="<?php echo $this -> url(array('action' => 'favorites'), 'ynvideochannel_general', true)?>">
			<i class="fa fa-heart"></i>
			<?php echo $this -> translate("My Favorite Videos")?>
		</a>
	</li>
	<li class="ynvideochannel_manage_menu_item ynvideochannel_myplaylists <?php if($this -> action == 'manage-channels') echo 'active'?>">
		<a href="<?php echo $this -> url(array('action' => 'manage-channels'), 'ynvideochannel_general', true)?>">
			<i class="fa fa-desktop"></i>
			<?php echo $this -> translate("My Channels")?>
		</a>
	</li>
	<li class="ynvideochannel_manage_menu_item ynvideochannel_watchlater <?php if($this -> action == 'manage-playlists') echo 'active'?>">
		<a href="<?php echo $this -> url(array('action' => 'manage-playlists'), 'ynvideochannel_general', true)?>">
			<i class="fa fa-bookmark"></i>
			<?php echo $this -> translate("My Playlists")?>
		</a>
	</li>
</ul>