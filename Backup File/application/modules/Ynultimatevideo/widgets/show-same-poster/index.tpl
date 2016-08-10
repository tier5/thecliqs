<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<ul class="ynultimatevideo_most_sameposter_videos ynultimatevideo_most_videos">
    <?php foreach ($this->paginator as $item): ?>
        <li class="ynultimatevideo_most_sameposter_videos_item ynultimatevideo_most_videos_item clearfix">
            <?php echo $this->partial('_video_listing.tpl', 'ynultimatevideo', array('video' => $item, 'height' => $this -> height, 'width' => $this -> width, 'margin_left' => $this -> margin_left)) ?>
        </li>
    <?php endforeach; ?>
</ul>