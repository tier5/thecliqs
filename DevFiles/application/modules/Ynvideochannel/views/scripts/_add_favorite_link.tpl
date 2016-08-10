<?php
	$favoriteTable = Engine_Api::_() -> getDbTable('favorites', 'ynvideochannel');
	$addedFavorite = $favoriteTable->isAdded($this->video->getIdentity(), $this->viewer()->getIdentity());
?>
<div class="video-action-favorite" onclick="ynvideochannelAddToFavorite(this, '<?php echo $this->video -> getIdentity() ?>', '<?php echo $this->url(array('action' => 'favorite', 'video_id' => ''), 'ynvideochannel_video', true);?>', '<?php echo $this->translate('Favorite') ?>', '<?php echo $this->translate('Un-favorite') ?>');">
	<?php if ($addedFavorite): ?>
	<i class="fa fa-star"></i>
	<?php echo $this->translate('Un-favorites') ?>
	<?php else: ?>
	<i class="fa fa-star-o"></i>
	<?php echo $this->translate('Favorites') ?>
	<?php endif; ?>
</div>