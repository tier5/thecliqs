<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    ${NAME}
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       25.07.12
 * @time       18:29
 */
?>
<?php if ($this->donation->status == 'expired'):?>

<div class="global_form_box">
  <div>
    <div class="form-elements">
            <img align="left" style="margin-right: 5px;"
                 src="<?php echo $this->baseUrl() . '/application/modules/Donation/externals/images/tick32.png'; ?>">

      <h3><?php echo $this->translate("Completed"); ?></h3>
      <?php echo $this->translate("Thank you to everyone who donated and supported.");?>
    </div>
  </div>
</div>

<?php elseif ($this->donation->status == 'initial'):?>

<div class="global_form_box ">
  <div>
    <div class="form-elements">
            <img align="left" style="margin-right: 5px;"
                 src="<?php echo $this->baseUrl() . '/application/modules/Donation/externals/images/warning.png'; ?>">
          <?php if ($this->isOwner) : ?>
          <h3><?php echo $this->translate("Initialization"); ?></h3>
          <?php echo $this->translate('That others have donated, first you need to enter %1$sfinancial information%2$s!',
                  '<a href="'.$this->url(array(
                    'controller' => $this->donation->type,
                    'action' => 'fininfo',
                    'donation_id' => $this->donation->getIdentity()), 'donation_extended', true).'">', '</a>'); ?>
        <?php else :?>
          <h3><?php echo $this->translate("None Active"); ?></h3>
        <?php endif;?>
    </div>
  </div>
</div>

<?php elseif($this->donation->approved) : ?>
  <button name="submit" type="submit"
          onclick="window.open('<?php echo $this->url(array('object' => $this->donation->getType(),'object_id' => $this->donation->getIdentity()),'donation_donate',true); ?>')">
    <?php echo $this->translate('DONATION_Donate'); ?>
  </button>
  <br /><br />

<?php endif; ?>

<?php if (!$this->donation->approved && $this->isOwner): ?>
<br/>
<div class="global_form_box ">
    <div>
        <div class="form-elements">
            <img align="left" style="margin-right: 5px;"
                 src="<?php echo $this->baseUrl() . '/application/modules/Donation/externals/images/not_approved.png'; ?>">

            <h3><?php echo $this->translate("DONATION_Not Approved"); ?></h3>
            <?php
              echo $this->translate("DONATION_Your donation is not approved. Please wait while administrator approve this donation.");
            ?>
        </div>
    </div>
</div>
<?php endif; ?>
<br/>
  <div id='profile_options'>
      <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
      echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->setPartial(array('_navIcons.tpl', 'core'))
          ->render();
      ?>
  </div>
