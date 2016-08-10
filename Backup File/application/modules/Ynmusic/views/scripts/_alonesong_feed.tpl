<?php $item = $this -> item;?>
<div class="ynmusic-alonesong-feed music-item">
<!-- get songs -->
<?php $songs = $item -> getSongs();?>
<?php if (count($songs)) :?>
<div class="album-songs music-songs">
	<?php echo $this->partial('_song-list.tpl', 'ynmusic', array('songs' => $songs, 'parent' => $item, 'feed' => true));?>
</div>
<?php endif;?>
</div>