<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<div class="ynvideo_block">
    <?php for ($x = 1; $x <= $this->video->rating; $x++): ?>
        <span class="rating_star_big_generic rating_star_big"></span>
    <?php endfor; ?>
    <?php if ((round($this->video->rating) - $this->video->rating) > 0): ?>
        <span class="rating_star_big_generic rating_star_big_half"></span>
    <?php endif; ?>
    <?php if (round($this->video->rating) == 0) :?>
        <?php for ($i = 0; $i < 5; $i++ ) : ?>
            <span class="rating_star_big_generic rating_star_big_disabled"></span>
        <?php endfor; ?>
    <?php endif; ?>
</div>