<?php
?>
<div class="ynbusinesspages_block">
    <?php for ($x = 1; $x <= $this->review->rate_number; $x++): ?>
        <span class="ynbusinesspages_rating_star_generic rating_star_big"></span>
    <?php endfor; ?>
    <?php if ((round($this->review->rate_number) - $this->review->rate_number) > 0): $x ++; ?>
        <span class="ynbusinesspages_rating_star_generic rating_star_big_half"></span>
    <?php endif; ?>
    <?php if ($x <= 5) :?>
        <?php for (; $x <= 5; $x++ ) : ?>
            <span class="ynbusinesspages_rating_star_generic rating_star_big_disabled"></span>
        <?php endfor; ?>
    <?php endif; ?>
</div>