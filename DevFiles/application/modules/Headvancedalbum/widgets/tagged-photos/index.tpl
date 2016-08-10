
<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Headvancedalbum/externals/scripts/hap.js');
?>
<script type="text/javascript">

  en4.core.runonce.add(function () {
    (new HapInstance({
      id:'hapTaggedPhotos',
      request_url:'<?php echo $this->url(array('module' => 'headvancedalbum', 'controller' => 'index', 'action' => 'index', 'owner' => $this->subject()->getGuid(), 'order' => 'recent', 'type' => 'tagged'), 'default', true);?>?format=json',
      max_width:150,
      loading_on_scroll:false
    }));
  });
  
</script>

<div class="hapLoader" id="hapLoader"></div>
<div class="hapLoader" id="hapBuildLoader"></div>

<ul class="hapPhotos" id="hapTaggedPhotos">
  <?php
    $this->id_prefix = 'hapTaggedPhotos';
  ?>
  <?php echo $this->render('application/modules/Headvancedalbum/views/scripts/_photoItems.tpl');?>
</ul>
