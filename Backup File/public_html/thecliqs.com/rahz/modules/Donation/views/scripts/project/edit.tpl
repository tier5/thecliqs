<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       10.08.12
 * @time       18:35
 */?>


<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
  ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    cal_discount_expiry_date.calendars[0].start = new Date();
    cal_discount_expiry_date.navigate(cal_discount_expiry_date.calendars[0], 'm', 1);
    cal_discount_expiry_date.navigate(cal_discount_expiry_date.calendars[0], 'm', -1);
  });

  var resetValue = function() {
    $('expiry_date-date').value = '';
    $('expiry_date-hour').value = '';
    $('expiry_date-minute').value = '';
    $('expiry_date-ampm').value = '';
    $('calendar_output_span_expiry_date-date').innerHTML = '<?php echo $this->translate('STORE_Select a date')?>';
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
  <?php echo $this->form->render($this); ?>
</div>