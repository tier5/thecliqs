<?php if( $this->paginator->getTotalItemCount() > 0 ):?>
	<ul id="ynmusic-recent-played">
	<?php foreach( $this->paginator as $history): ?>
		<?php $item = Engine_Api::_()->getItem('ynmusic_'.$history['item_type'], $history['item_id']);?>
		<li>
			<?php if (!$item->isViewable()) :?>
		<div class="disabled"></div>
		<?php endif;?>
		<div class="music-title">
			<?php echo $item;?>
		</div>

		<div class="ynmusic-item-info">
			<?php if ($item->getType() != 'ynmusic_playlist') :?>
			<?php $artists = $item->getArtists();?>
			<?php if (!empty($artists)) :?>
			<div class="song-artist music-artist">
				<?php echo implode(', ', $artists)?>
			</div>
			<?php endif;?>
			<?php endif;?>
			<span class="play-count"><i class="fa fa-headphones"></i><?php echo $item -> play_count;?></span>
		</div>
			
		<div class="play-btn-<?php echo $item->getGuid()?> music-play-btn"><a href="javascript:void(0)"><i rel="<?php echo $item->getGuid()?>" class="fa fa-play"></i></a></div>

		</li>
	<?php endforeach;?>
	</ul>
<?php endif; ?>
