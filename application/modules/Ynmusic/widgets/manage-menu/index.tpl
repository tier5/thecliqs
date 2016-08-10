<ul class="ynmusic_manage_menu">
	<li class="ynmusic_myalbum <?php if($this -> controller == "albums" && $this -> action == 'manage') echo "active"?>">
		<a href="<?php echo $this -> url(array('action' => 'manage'), 'ynmusic_album', true)?>">
			<i class="fa fa-th-large"></i>
			<?php echo $this -> translate("My Albums")?>
		</a>
	</li>
	<li class="ynmusic_myplaylist <?php if($this -> controller == "playlists" && $this -> action == 'manage') echo "active"?>">
		<a href="<?php echo $this -> url(array('action' => 'manage'), 'ynmusic_playlist', true)?>">
			<i class="fa fa-th"></i>
			<?php echo $this -> translate("My Playlists")?>
		</a>
	</li>
	<li class="ynmusic_mysong <?php if($this -> controller == "songs" && $this -> action == 'manage') echo "active"?>">
		<a href="<?php echo $this -> url(array('action' => 'manage'), 'ynmusic_song', true)?>">
			<i class="fa fa-music"></i>
			<?php echo $this -> translate("My Songs")?>
		</a>
	</li>
	<li class="ynmusic_myhistory <?php if($this -> controller == "history" && $this -> action == 'index') echo "active"?>">
		<a href="<?php echo $this -> url(array('action' => 'index'), 'ynmusic_history', true)?>">
			<i class="fa fa-history"></i>
			<?php echo $this -> translate("History")?>
		</a>
	</li>
</ul>