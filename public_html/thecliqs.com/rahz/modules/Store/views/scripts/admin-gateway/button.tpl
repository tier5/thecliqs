<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: button.tpl  3/19/12 6:52 PM mt.uulu $
 * @author     Mirlan
 */
?>
<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if( count($this->navigation) ): ?>
  <div class='store_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php echo $this->render('admin/_gatewayMenu.tpl'); ?>

<div class="settings admin_home_middle" style="clear: none;">
  <h2><?php echo $this->translate("STORE_%s Settings", $this->gateway->getTitle()); ?></h2>

  <p>
    <?php echo $this->translate(
    "STORE_To replace the gateway's button paste Image's URL into the following text field. Leave empty to show the default button.",
    $this->htmlLink(array('route'=>'admin_default', 'controller'=>'files'), 'Upload', array('target'=>'_blank'))
  ); ?>
  </p>
  <br/>
  <div>
    <div class="button-preview">
      <?php echo $this->htmlImage(
            $this->gateway->getButtonUrl(),
            $this->translate('STORE_Checkout with %s', $this->gateway->getTitle()),
            array('alt'=>$this->gateway->getTitle())); ?>
    </div>

    <div class="button-url">
      <form action="<?php $this->url(); ?>" method="post">
        <?php if($this->status): ?>
        <div class="success"><?php echo $this->translate('STORE_Replaced successfully'); ?></div>
        <?php endif; ?>

        <?php echo $this->translate('STORE_Button URL');?>:
        <input type="text" name="gateway-button" title="<?php echo $this->translate('STORE_Button URL'); ?>" value="<?php echo $this->gateway->getButtonUrl(); ?>"/>
        <button type="submit"><?php echo $this->translate("Replace"); ?></button>
      </form>
    </div>
  </div>
</div>