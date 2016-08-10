<?php $item = $this -> item; ?>
<?php
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$controller = $request -> getControllerName();
?>
<li class="ynvideochannel_playlist_list-item">
	<div class="ynvideochannel_playlist_list-content ynvideochannel_playlists_grid-content">
		<?php $photoUrl = ($item->getPhotoUrl('thumb.normal')) ? $item->getPhotoUrl('thumb.normal') : 'application/modules/Ynvideochannel/externals/images/nophoto_playlist_thumb_icon.png'; ?>

		<div class="ynvideochannel_playlist_list-bg" style="background-image: url(<?php echo $photoUrl ?>)"></div>

		<div class="ynvideochannel_playlist_list-count">
			<span class="ynvideochannel_playlist_list-number"><?php echo $item->video_count ?></span>
			<span><?php echo $this->translate(array("ynvideochannel_videos", "videos", $item -> video_count),$item -> video_count); ?></span>
		</div>


		<div class="ynvideochannel_playlist_list-info">
			<div class="ynvideochannel_playlist_list-actions ynvideochannel_video-channel-playlist_options">
				<?php echo $this->partial('_playlist_options.tpl', 'ynvideochannel', array('playlist' => $item)); ?>
			</div>
			<div class="ynvideochannel_playlist_list-title"><?php echo $item ?></div>

			<div class="ynvideochannel_playlist_list-owner">
				<span><?php echo $this->translate('by') ?></span>
				<?php echo $item->getOwner(); ?>
				<span>&nbsp;.&nbsp;</span>
				<span><?php echo $this->locale()->todateTime(strtotime($item->creation_date), array('type' => 'date')) ?></span>
			</div>

			<?php $videos = $item -> getVideos(3) ?>
			<?php if (count($videos)): ?>
				<div class="ynvideochannel_playlist_list-videos">
					<ul class="ynvideochannel_playlist_list-videos-item">
						<?php foreach($videos as $video): ?>
							<li>
								<i class="fa fa-angle-right"></i>&nbsp;<?php echo $this->htmlLink($video->getHref(), $video->getTitle(), array('class' => 'ynvideochannel_playlist_list-videos-title', 'title' => $video->getTitle())) ?>
								<?php if ($video->duration): ?>
									<?php echo $this->partial('_video_duration.tpl', 'ynvideochannel', array('video' => $video)) ?>
								<?php endif ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif;?>
		</div>
	</div>
</li>

