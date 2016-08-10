<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       13.08.12
 * @time       17:19
 */?>

<script type="text/javascript">

  en4.core.runonce.add(function()
  {
    <?php $date = strtotime($this->donation->expiry_date) > time() ? strtotime($this->donation->expiry_date) : time() - 86400;?>
    var endDate = new Date('<?php echo date('Y, m, d', $date);?>');
    cal_expiry_date.calendars[0].start = new Date();
      cal_expiry_date.calendars[0].end = endDate;
    cal_expiry_date.navigate(cal_expiry_date.calendars[0], 'm', 1);
    cal_expiry_date.navigate(cal_expiry_date.calendars[0], 'm', -1);

  });

  var resetValue = function() {
    $('expiry_date-date').value = '';
    $('expiry_date-hour').value = '';
    $('expiry_date-minute').value = '';
    $('expiry_date-ampm').value = '';
    $('calendar_output_span_expiry_date-date').innerHTML = '<?php echo $this->translate('DONATION_Select a date')?>';
  }

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
<?php echo $this->form->render($this); ?>