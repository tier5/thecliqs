<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  02.02.12 11:48 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  var redirect = function()
  {
    var href = '<?php echo $this->url(array('action' => 'faq'), 'credit_general', true)?>';
    window.location.href = href;
  }
</script>

<div class="credit_faq_widget_container">
  <div class="credit_faq_icon">
    <img src="application/modules/Credit/externals/images/faq<?php echo $this->index?>.png" onclick="redirect()" style="cursor: help"/>
  </div>
  <div class="credit_faq_desc">
    <?php echo $this->string()->truncate($this->faq, 150, '... '.$this->htmlLink($this->url(array('action' => 'faq'), 'credit_general', true), $this->translate('more'), array('target' => '_blank'))); ?>
  </div>
</div>