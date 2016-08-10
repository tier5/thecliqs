
<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Headvancedalbum/externals/scripts/hap.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function (){
    (new HapInstance({
      request_url: '<?php echo $this->url(array('module' => 'headvancedalbum', 'controller' => 'index', 'action' => 'index', 'owner' => $this->subject()->getGuid()), 'default', true);?>?format=json',
      max_width:150
    }));

  });
</script>

<div class="hapLoader" id="hapLoader"></div>
<div class="hapLoader" id="hapBuildLoader"></div>

<ul class="hapPhotos" id="hapPhotos">
  <?php echo $this->render('application/modules/Headvancedalbum/views/scripts/_photoItems.tpl');?>
</ul>
