<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: cancel.tpl 2011-08-11 17:53 taalay $
 * @author     Taalay
 */
?>

<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php else: ?>

  <div style="padding: 10px;">

    <?php if( $this->status ): ?>
      <?php echo $this->translate('The subscription has been cancelled.') ?>
    <?php else: ?>
      <?php echo $this->translate('There was a problem cancelling the ' .
          'subscription. The message was:') ?>
      <?php echo $this->error ?>
    <?php endif; ?>

    <br />
    <br />

    <?php /* echo $this->htmlLink(array(
      'reset' => false,
      'action' => 'detail',
      'subscription_id' => $this->subscription_id,
    ), $this->translate('return')) */ ?>

    <a href="javascript:void(0);" onclick="parent.Smoothbox.close(); return false">
      <?php echo $this->translate('close') ?>
    </a>

  </div>

<?php endif; ?>