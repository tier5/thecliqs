<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl:  07.07.11 14:27 taalay $
 * @author     Taalay
 */
?>
<script type="text/javascript">
  en4.core.runonce.add(function(){
    store_manager.init();
  });
</script>
<?php echo $this->filterForm->render($this); ?>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
'topLevelId' => (int) @$this->topLevelId,
'topLevelValue' => (int) @$this->topLevelValue
))
?>