<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  01.08.12 16:13 TeaJay $
 * @author     Taalay
 */
?>

<?php
	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl.'application/modules/Credit/externals/scripts/core.js')
  ;
?>

<script type="text/javascript">
  function moreDetails($id)
  {
    var url = '<?php echo $this->url(array('module' => 'credit', 'controller' => 'buy-level', 'action' => 'details'), 'default', true); ?>/package_id/'+$id;
    var $element = new Element('a', {'href': url, 'class': 'smoothbox'});
    Smoothbox.open($element, {mode: 'Request'});
  }

  en4.core.runonce.add(function()
  {
    credit_manager.buy_level_url = '<?php echo $this->url(array('module'=>'credit', 'controller'=>'buy-level', 'action' => 'confirm'), 'default', true) ?>';
    if (!credit_manager.started) {
      credit_manager.init();
    }
  });

  function redirect()
  {
    window.location = '<?php echo $this->url(array('module'=>'payment', 'controller'=>'settings', 'action' => 'index'), 'default', true) ?>';
  }
</script>

<?php echo $this->form->render($this); ?>