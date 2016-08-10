<?php foreach ($this->listings as $listing) : ?>
<div class="item-widget-listing">
    <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png";?>
    <div class="image-background" style="background-image: url(<?php echo $photo_url; ?>);"></div>
	<div class="item-content">
		<?php echo $this->htmlLink(
            $listing->getHref(),
            $listing->title,
            array('class' => 'title')
        )?>
		<div class="comments">
			<span class="fa fa-eye"></span>
            <?php echo $this->translate(array('%s review', '%s reviews', $listing->review_count), $listing->review_count)?>
		</div>
	</div>
</div>
<?php endforeach; ?>