<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: details.tpl  02.08.12 12:09 TeaJay $
 * @author     Taalay
 */
?>

<?php if ($this->error) : ?>
  <?php echo $this->translate('Invalid Data')?>
<?php return; endif;?>

<h3>
  <?php echo $this->translate('Details') ?>
</h3>
<p>
  <?php echo $this->translate('You are about to subscribe to the plan: ' .
      '%1$s', '<strong>' .
      $this->translate($this->package->title) . '</strong>') ?>
  <br />
  <?php echo $this->translate('You will be charged: %1$s',
      '<strong>' . $this->packageDescription
      . '</strong>') ?>
  <p class="package-description">
    <?php echo $this->translate($this->package->description) ?>
  </p>
</p>