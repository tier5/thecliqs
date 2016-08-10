<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>
<ul class="ynultimatevideo_most_liked_videos ynultimatevideo_most_videos">
    <?php foreach ($this->paginator as $item): ?>
    <li class="ynultimatevideo_most_liked_videos_item ynultimatevideo_most_videos_item clearfix">
        <?php
            echo $this->partial('_video_listing.tpl', 'ynultimatevideo', array(
                'video' => $item
            ));
        ?>
    </li>
    <?php endforeach; ?>
</ul>