<?php
	$item = $this -> item;
	$playlist = Engine_Api::_() -> getItemTable('ynultimatevideo_playlist') -> getUserPlaylist($this -> viewer());
	$favoriteTable = Engine_Api::_() -> getDbTable('favorites', 'ynultimatevideo');
	$addedFavorite = $favoriteTable->isAdded($item->getIdentity(), $this->viewer()->getIdentity());
?>
<div class="checkbox-row">
	<input onclick="ynultimatevideoAddToFavorite(this, '<?php echo $item -> getIdentity();?>');" <?php echo ($addedFavorite)? "checked" : "" ;?> type="checkbox"><?php echo $this -> translate("Favorites");?>
</div>
<?php foreach($playlist as $playlist) :?>
	<?php if ($playlist->isEditable() && $playlist->canAddVideos()) :?>
		<div class="checkbox-row">
			<?php
				$check = false;
				if($item -> getType() == "ynultimatevideo_video"){
					$tablePlaylistAssoc = Engine_Api::_() -> getDbTable('playlistassoc', 'ynultimatevideo');
					$row = $tablePlaylistAssoc -> getMapRow($playlist -> getIdentity(), $item -> getIdentity());
					if(isset($row) && !empty($row)) {
						$check = true;
					}
				}
			?>
			<input onclick="ynultimatevideoAddToPlaylist(this, '<?php echo $playlist -> getIdentity();?>', '<?php echo $item -> getGuid();?>');" <?php echo ($check)? "checked" : "" ;?> type="checkbox"><?php echo $playlist -> getTitle();?>
		</div>
	<?php endif;?>
<?php endforeach;?>
