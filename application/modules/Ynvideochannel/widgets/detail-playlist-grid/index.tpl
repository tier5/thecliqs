<div class="ynvideochannel_playlist_detail-countvideos">
	<i class="fa fa-video-camera" aria-hidden="true"></i>
    <?php echo $this -> translate(array("%s video", "%s videos", $this -> paginator -> getTotalItemCount()), $this -> paginator -> getTotalItemCount())?>
</div>

<?php echo $this->partial('_videos_grid.tpl', 'ynvideochannel', array('videos' => $this->paginator)); ?>
<?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
)); ?>
