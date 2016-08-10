<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  18.01.12 19:04 TeaJay $
 * @author     Taalay
 */
?>

<?php
	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl.'application/modules/Credit/externals/scripts/core.js')
  ;
?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    credit_manager.buy_credits_url = '<?php echo $this->url(array('action' => 'buy'), 'credit_general', true) ?>';
    if (!credit_manager.started) {
      credit_manager.init();
    }
  });
</script>

<?php echo $this->form->render($this); ?>