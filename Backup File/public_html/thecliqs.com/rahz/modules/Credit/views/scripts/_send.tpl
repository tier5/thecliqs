<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _send.tpl  16.04.12 18:18 TeaJay $
 * @author     Taalay
 */
?>

<?php if (!$this->limit) : ?>
  <li>
    <?php echo $this->translate('Please wait, the credits are still sending, do not close this page! There are still <span style="color: red">%s</span> users who have not received credits.', $this->count);?>
    <i class="icon_loading"></i>
  </li>
<?php else : ?>
  <li>
    <?php
      if (!empty($this->set_default) && $this->set_default == 1) {
        echo $this->translate('Credits have been successfully set to %s users', $this->locale()->toNumber($this->total));
      } else {
        echo $this->translate('Credits have been successfully sent to %s users', $this->locale()->toNumber($this->total));
      }
    ?>
  </li>
<?php endif; ?>