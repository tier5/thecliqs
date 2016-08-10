<ul class="ynultimatevideo_most_liked_videos ynultimatevideo_most_videos">
	<?php foreach ($this->paginator as $item): ?>
	<li class="ynultimatevideo_most_liked_videos_item ynultimatevideo_most_videos_item clearfix">
		<?php
                echo $this->partial('_video_listing.tpl', 'ynultimatevideo', array(
		'video' => $item,
		'recentCol' => 'creation_date',
		'infoCol' => 'like'
		));
		?>
	</li>
	<?php endforeach; ?>
</ul>