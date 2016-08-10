<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: photo.tpl  28.02.12 19:44 TeaJay $
 * @author     Taalay
 */
?>

<?php if (!isset($this->message)) : ?>
  <div class="gift_on_smoothbox">
    <?php echo $this->itemPhoto($this->gift) ?>
  </div>
<?php else : ?>
  <?php echo $this->translate($this->message);?>
<?php endif; ?>
