<?php $wid = rand(1, 10000); ?>

<div class="suggest-widget-container">
  
<?php $ids = array(); ?>
<?php if (count($this->items) > 0): ?>
  <?php foreach($this->items as $item): ?>
    <?php $ids[] = $item->getIdentity(); ?>
    <?php echo $this->partial('widget/profile/item.tpl', 'suggest', array('item' => $item, 'wid' => $wid)); ?>
  <?php endforeach; ?>
<?php endif; ?>
  
<div class="clr"></div>
</div>

<script type="text/javascript">
en4.core.runonce.add(function(){
  var options = {
    widgetId: <?php echo $wid; ?>,
    except: <?php echo Zend_Json_Encoder::encode($ids); ?>,
    object_type: '<?php echo $this->type; ?>',
    url: '<?php echo $this->url(array('controller' => 'index', 'action' => 'reject'), 'suggest_general'); ?>'
  };
  if (!window.recs) {
    window.recs = {};
  }
  window.recs.<?php echo $this->type; ?> = new RecItems(options);
});
</script>