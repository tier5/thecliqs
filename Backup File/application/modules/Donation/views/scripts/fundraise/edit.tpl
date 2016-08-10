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
 * @time       19:34
 */?>

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

<div class='layout_right' style="width: 200px !important;">
  <?php echo $this->content()->renderWidget('donation.donation-edit-options') ?>
</div>

<div class='layout_middle' >
  <?php echo $this->form->render($this); ?>
</div>