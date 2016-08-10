<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
?>

<span class="ynvideochannel_rating_point" style="display: none;">
  <?php echo round($this->rating,2); ?>
</span>
<?php for ($x = 1; $x <= $this->rating; $x++): ?>
    <i class="fa fa-star"></i>
<?php endfor; ?>
<?php if ((round($this->rating) - $this->rating) > 0): ?>
    <i class="fa fa-star-half"></i>
<?php endif; ?>
<?php for ($x = (round($this->rating)); $x < 5; $x++): ?>
    <i class="fa fa-star disable"></i>
<?php endfor; ?>