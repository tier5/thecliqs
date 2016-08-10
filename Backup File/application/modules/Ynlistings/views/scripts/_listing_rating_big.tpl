<?php
?>
<div class="ynlistings_block">
    <?php for ($x = 1; $x <= $this->listing->getRating(); $x++): ?>
        <span class="ynlistings_rating_star_generic rating_star_big"></span>
    <?php endfor; ?>
    <?php if ((round($this->listing->getRating()) - $this->listing->getRating()) > 0): $x ++; ?>
        <span class="ynlistings_rating_star_generic rating_star_big_half"></span>
    <?php endif; ?>
    <?php if ($x <= 5) :?>
        <?php for (; $x <= 5; $x++ ) : ?>
            <span class="ynlistings_rating_star_generic rating_star_big_disabled"></span>
        <?php endfor; ?>
    <?php endif; ?>
    
</div>