<?php
	$item = $this -> item;
	$playlist = Engine_Api::_() -> getItemTable('ynmusic_playlist') -> getUserPlaylist($this -> viewer());
?>
<?php foreach($playlist as $playlist) :?>
	<?php if ($playlist->isEditable() && $playlist->canAddSongs()) :?>
	<div class="checkbox-row">
		<?php
			$check = false;
			if($item -> getType() == "ynmusic_song"){
				$tablePlaylistSong = Engine_Api::_() -> getDbTable('playlistSongs', 'ynmusic');
				$row = $tablePlaylistSong -> getMapRow($playlist -> getIdentity(), $item -> getIdentity());
				if(isset($row) && !empty($row)) {
					$check = true;
				}
			}
		?>
		<input onclick="addToPlaylist(this, '<?php echo $playlist -> getIdentity();?>', '<?php echo $item -> getGuid();?>');" <?php echo ($check)? "checked" : "" ;?> type="checkbox"><?php echo $playlist -> getTitle();?>
	</div>
	<?php endif;?>
<?php endforeach;?>
