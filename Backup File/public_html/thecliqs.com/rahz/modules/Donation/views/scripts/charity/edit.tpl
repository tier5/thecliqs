
<script type="text/javascript">
  var switchSelectAmount = function () {
    if ($('can_choose_amount-1').checked) {
      $('predefine_list-wrapper').style.display = 'block';
      $('predefine_list').value = '5,10,20,50,100';
    } else {
      $('predefine_list-wrapper').style.display = 'none';
      $('predefine_list').value = '0';
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

<div class='layout_middle' >
  <?php echo $this->form->render($this); ?>
</div>
