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

<div id="payment_loading">
  <?php echo $this->translate('STORE_Please Wait')?>
</div>