<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<?php for ($x = 1; $x <= $this->rating; $x++): ?>
    <i class="fa fa-star"></i>
<?php endfor; ?>
<?php if ((round($this->rating) - $this->rating) > 0): ?>
    <i class="fa fa-star-half-o"></i>
<?php endif; ?>
<?php for ($x = (round($this->rating)); $x < 5; $x++): ?>
    <i class="fa fa-star-o"></i>
<?php endfor; ?>