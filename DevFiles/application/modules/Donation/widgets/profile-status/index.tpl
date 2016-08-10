<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       02.08.12
 * @time       12:45
 */
?>

<label> <?php echo  $this->translate('DONATION_Raised:');?> </label>
<span><?php echo $this->locale()->toCurrency((double)$this->donation->getRaised(), $this->currency) ?></span>

<?php if (in_array($this->donation->type, array('project', 'fundraise'))) : ?>
  <?php if ($this->donation->target_sum > 0) :?>
    <div class="target">
      <label><?php echo $this->translate('DONATION_Target:'); ?></label>
      <span><?php echo $this->locale()->toCurrency((double)$this->donation->getTargetSum(), $this->currency) ?></span>
    </div>
  <?php endif ?>
  <?php if (strtotime($this->donation->expiry_date) != '1546300800'):?>
    <div class="time_limited">
        <?php $left = Engine_Api::_()->getApi('core', 'donation')->datediff(new DateTime($this->donation->expiry_date), new DateTime(date("Y-m-d H:i:s")));?>
        <?php
          $month = (int)$left->format('%m');
          $day = (int)$left->format('%d');
        ?>
        <?php if($month > 0): ?>
         <?php echo $this->translate(array("%s month", "%s months", $month), $month);?>
        <?php endif;?>
        <?php echo $this->translate(array("%s day left", "%s days left", $day), $day);?>
    </div>
  <?php endif;?>

  <?php if ($this->donation->target_sum > 0) :?>
  <div class="progress_cont">
    <div class="progress">
      <?php $status = (int) (100 * $this->donation->getRaised() / $this->donation->getTargetSum());?>
      <div class="bar" style="width: <?php echo $status > 100 ? 100 : $status;?>%"></div>
    </div>
    <span style="font-size: 12px"> <?php echo $status > 100 ? 100 : $status;?>%</span>
    <br/>
  </div>
<?php endif; ?>
<?php endif; ?>
