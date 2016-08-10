
<?php 
	$artist = $this -> artist;
	$id = $artist -> getIdentity();
?>
<?php $genres = $artist->getGenres();?>
<?php if (!empty($genres)) :?>
<div class="artist-genres music-genres">
	<span class="label"><i class="fa fa-folder-open"></i><?php echo $this->translate('Genres')?>:</span>
	<span class="value"><?php echo implode(', ', $genres)?></span>
</div>
<?php endif;?>

<div class="artist-description music-description">
<?php echo $artist -> description;?>
</div>