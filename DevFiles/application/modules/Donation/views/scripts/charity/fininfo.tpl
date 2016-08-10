<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 16.08.12
 * Time: 19:08
 * To change this template use File | Settings | File Templates.
 */?>
<script type="text/javascript">

  var PayPalChecked = function () {
    if ($('paypal').checked) {
      $('pemail-wrapper').style.display = 'block';
    } else {
      $('pemail-wrapper').style.display = 'none';
    }
  }
  var CheckOutChecked = function() {
    if ($('2checkout').checked) {
      $('2email-wrapper').style.display = 'block';
    }else {
      $('2email-wrapper').style.display = 'none';
    }
  }
</script>
<?php if($this->subject): ?>
  <?php echo $this->render('editMenu.tpl'); ?>
  <div class="headline donation">
    <h2><?php echo $this->translate('DONATION_Manage Donations'); ?></h2>
    <div class="tabs">
      <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
      <div class="donation_loader hidden" id="donation_loader">
        <?php echo $this->htmlImage($this->baseUrl().'/application/modules/Donation/externals/images/loader.gif'); ?>
      </div>
      <div class="clr"></div>
    </div>
  </div>
<?php endif; ?>

<div class='layout_right' style="width: 200px !important;">
  <?php echo $this->content()->renderWidget('donation.donation-edit-options') ?>
</div>
<div class="layout_middle">
  <?php echo  $this->form->render($this);?>
</div>