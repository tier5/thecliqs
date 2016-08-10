<?php if ($this->paginator->getTotalItemCount() > 0) :?>
<div id="ynmusic-artist-listing">
	<?php if (!$this->paging):?>
	<div class="artist-count music-count">
		<span class="label"><?php echo $this->translate(array('ynmusic_artist_count', 'Artists', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?></span>
		<span class="value count"><?php echo $this->translate('(%s)', $this->paginator->getTotalItemCount())?></span>
	</div>
	<?php endif;?>
	<ul class="artist-items music-items clearfix">
	<?php foreach ($this->paginator as $item) :?>
	<li id="artist-<?php echo $item->getIdentity()?>" class="artist-item music-item">

		<div class="artist-photo music-photo">
			<?php $photo_url = ($item->getPhotoUrl()) ? $item->getPhotoUrl() : "application/modules/Ynmusic/externals/images/nophoto_artist_thumb_profile.png";?>
			<a href="<?php echo $item->getHref(); ?>" style="background-image: url(<?php echo $photo_url; ?>) "></a>
				<div class="artist-photo-grid-hover" onclick="window.location.href='<?php echo $item->getHref();?>'">
					<span><?php echo $this->translate(array('Song: %s', 'Songs: %s', $item->getCountItems('ynmusic_song')), $item->getCountItems('ynmusic_song'))?></span>
					<span><?php echo $this->translate(array('Album: %s', 'Albums: %s', $item->getCountItems('ynmusic_album')), $item->getCountItems('ynmusic_album'))?></span>
					<?php if (!empty($item->country)) :?>
					<span><?php echo $this->translate('Country:')?>
						<span><?php echo $this->translate($item->country)?></span>
					</span>
					<?php endif;?>
					<?php $genres = $item->getGenres();?>
					<?php if (!empty($genres)) :?>
					<span>
						<?php echo $this->translate('Genre:')?>
						<?php echo implode(', ', $genres)?>
					</span>
					<?php endif;?>
				</div>
			
		</div>

		<div class="artist-title music-title">
			<?php echo $item;?>
		</div>

		<div class="artist-info music-info">
			<div class="artist-genre-country">
				<?php $genres = $item->getGenres();?>
				<?php if (!empty($genres)) :?>
				<div class="artist-genre music-genre">
					<span class="label"><?php echo $this->translate('Genre:')?></span>
					<span class="value"><?php echo implode(', ', $genres)?></span>
				</div>
				<?php endif;?>
				<div class="artist-country-statistic">
					<?php if (!empty($item->country)) :?>
					<span class="label"><?php echo $this->translate('Country:')?></span>
					<span class="value artist-country"><?php echo $this->translate($item->country)?> - </span>
					<?php endif;?>
					<span class="value artist-song-count"><?php echo $this->translate(array('ynmusic_song_count_num_ucf', '%s Songs', $item->getCountItems('ynmusic_song')), $item->getCountItems('ynmusic_song'))?> - </span>
					<span class="value artist-song-count"><?php echo $this->translate(array('ynmusic_album_count_num_ucf', '%s Albums', $item->getCountItems('ynmusic_album')), $item->getCountItems('ynmusic_album'))?></span>
				</div>
			</div>
				
			<div class="artist-short_description music-short_description">
				<span class="label"><?php echo $this->translate('Info:')?></span>
				<span class="value"><?php echo $this->viewMore($item->short_description, 256)?></span>
			</div>
		</div>
	</li>
	<?php endforeach;?>
	</ul>
	<?php if (!$this->paging):?>
	<div class="view-all">
		<a href="<?php echo $this->url(array(),'ynmusic_artist', true)?>?<?php echo http_build_query($this->formValues)?>"><?php echo $this->translate(array('View %s Artist', 'View %s Artists', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount())?></a>
	</div>
	<?php else:?>
	<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
	</div>
	<?php endif;?>
</div>
<?php else:?>
	<?php if ($this->paging):?>
	<div class="tip">
		<span><?php echo $this->translate('There are no artists.')?></span>
	</div>
	<?php endif;?>
<?php endif;?>