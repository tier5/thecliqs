<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: process.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if (count($this->navigation)): ?>
  <div class='store_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<script type="text/javascript">
  window.addEvent('load', function() {
    var url = '<?php echo $this->transactionUrl ?>';
    var data = <?php echo Zend_Json::encode($this->transactionData) ?>;
    var request = new Request.Post({
      url : url,
      data : data
    });
    request.send();

    loading(1);
  });

  function loading($count)
  {
    var $point = ' .';
    if ($count == 2) {
      $point = ' ..';
    } else if($count == 3) {
      $point = ' ...';
    }
    $('payment_loading').set('text', '<?php echo $this->translate('STORE_Please Wait')?>' + $point);
    setTimeout(function(){
      $count ++;
      if ($count == 4) {
        $count = 1;
      }
      loading($count);
    }, 300);
  }
</script>

<div class="admin_home_middle">
  <div id="payment_loading">
    <?php echo $this->translate('STORE_Please Wait')?>
  </div>
</div>
