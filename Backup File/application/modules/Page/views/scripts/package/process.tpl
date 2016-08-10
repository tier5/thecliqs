<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: process.tpl 8221 2011-07-25 00:24:02Z taalay $
 * @author     Taalay (TJ)
 */
?>

<script type="text/javascript">
  window.addEvent('load', function(){
    var url = '<?php echo $this->transactionUrl ?>';
    var data = <?php echo Zend_Json::encode($this->transactionData) ?>;
    var request = new Request.Post({
      url : url,
      data : data
    });
    request.send();
  });
</script>

<b></b><?php echo $this->translate('Please wait...')?></b>