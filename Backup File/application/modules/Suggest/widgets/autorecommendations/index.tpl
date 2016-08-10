<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>
  
<?php
$wid = rand(1, 10000);
?>

<div class="suggest-widget-container">
  
<?php
$ids = array();
?>

<?php if (count($this->items) > 0): ?>
  <?php foreach($this->items as $type => $item): ?>
    <?php $ids[$type][] = $item->getIdentity(); ?>
    <?php echo $this->partial('widget/item.tpl', 'suggest', array('item' => $item, 'wid' => $wid)); ?>
  <?php endforeach; ?>
<?php endif; ?>

<div class="clr"></div>
  
</div>

<script type="text/javascript">
en4.core.runonce.add(function(){
  <?php foreach ($ids as $type => $id): ?>
  var options = {
    widgetId: <?php echo $wid; ?>,
    except: <?php echo Zend_Json_Encoder::encode($id); ?>,
    object_type: '<?php echo $type; ?>',
    url: '<?php echo $this->url(array('controller' => 'index', 'action' => 'reject'), 'suggest_general'); ?>'
  };
  if (!window.auto) {
    window.auto = {};
  }
  window.auto.<?php echo $type; ?> = new RecItems(options);
  <?php endforeach; ?>
});
</script>