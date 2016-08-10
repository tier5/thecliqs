<?php $item = $this -> item; ?>
<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$controller = $request -> getControllerName();
?>
<!-- thumbnail -->
<div class="ynultimatevideo_list_most_item_content">
	<?php
		$photoUrl = $item ->getPhotoUrl('thumb.normal');
    	if (!$photoUrl) {
    		$photoUrl = $item->getLastVideoPhoto();
    	}
    	if (!$photoUrl) {
    		$photoUrl = $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynultimatevideo/externals/images/nophoto_playlist_thumb_icon.png';
    	}

		$videos = $item -> getVideos();
		$count = count($videos);
	?>

	<div class="ynultimatevideo_wrapper" style="background-image: url(<?php echo $photoUrl ?>)">
		<div class="ynultimatevideo_grid_playlist_count_video">
			<i class="fa fa-bars"></i>
			<span class="ynultimatevideo-number"><?php echo $count ?></span>
			<span><?php echo ucfirst($this->translate(array('ynultimatevideo_video', 'videos', $count), $count)); ?></span>
		</div>
		<div class="ynultimatevideo_background_opacity"></div>
		<div class="ynultimatevideo_playlist_play video-play-btn">
            <a href="<?php echo $item->getHref() ?>">
                <i class="fa fa-play"></i>
            </a>
        </div>
	</div>
	
	<!-- stat block, may check to show with count > 0 -->
	<div class="ynultimatevideo-playlist-count-video">
		<span class="ynultimatevideo-number"><?php echo $this->locale()->toNumber($count) ?></span>
		<span><?php echo $this->translate(array('ynultimatevideo_video','videos', $count)); ?></span>
	</div>

	<div class="ynultimatevideo_content_padding">
		<div class="ynultimatevideo-playlist-title"><?php echo $item ?></div>

		<!-- actions -->
		<?php if ($this->viewer()->getIdentity() && $controller != 'history' && ($item->isEditable() || $item->isDeletable())) :?>
		<div class="ynultimatevideo_options_block">
			<span class="ynultimatevideo_options_btn"><i class="fa fa-pencil"></i></span>
			<div class="ynultimatevideo_options" style="display:none">
					<?php if ($item->isEditable()) :?>
					<?php $url = $this->url(array('action' => 'edit', 'playlist_id' => $item -> getIdentity()), 'ynultimatevideo_playlist', true);?>
					<a class="icon_ynultimatevideo_edit" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-pencil-square-o"></i><?php echo $this->translate('Edit')?></a>
					<?php endif;?>

					<?php if ($item->isDeletable()) :?>
					<?php $url = $this->url(array('action' => 'delete', 'playlist_id' => $item -> getIdentity()), 'ynultimatevideo_playlist', true);?>
					<a class="smoothbox icon_ynultimatevideo_delete" href="<?php echo $url?>" rel="<?php echo $item->getIdentity()?>"><i class="fa fa-trash"></i><?php echo $this->translate('Delete')?></a>
					<?php endif;?>
			</div>
		</div>
		<?php endif; ?>
		<?php if ($this->viewer()->getIdentity() && $controller == 'history'):?>
			<div class="ynultimatevideo_options_block">
				<?php
					echo $this->htmlLink(
					Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
					'action' => 'removeplaylist',
					'playlist_id' => $item->getIdentity()), null),
					null, array('class' => 'ynultimatevideo_options_btn fa fa-remove smoothbox')
					);
				?>
			</div>
		<?php endif; ?>


		<!-- get mini videos list -->
		<?php $i = 0 ?>
		<?php if ($count): ?>
			<div class="ynultimatevideo-playlist-videos">
				<ul>
					<?php foreach($videos as $video): ?>
					<li>
						<i class="fa fa-angle-right"></i>&nbsp;<?php echo $this->htmlLink($video->getHref(), $video->getTitle(), array('class' => 'ynultimatevideo_title_video', 'title' => $video->getTitle())) ?>
						<?php if ($video->duration): ?>
							<?php echo $this->partial('_video_duration.tpl', 'ynultimatevideo', array('video' => $video)) ?>
						<?php endif ?>
					</li>
					<!-- may have a setting for mini list-->
					<?php if(++$i == 3) break ?>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif;?>

	</div>
</div>