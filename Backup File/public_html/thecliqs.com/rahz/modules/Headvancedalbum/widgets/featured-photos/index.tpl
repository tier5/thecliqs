

<div class="hapLoader" id="hapLoader"></div>
<div class="hapLoader" id="hapBuildLoader"></div>


<?php
  $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Headvancedalbum/externals/scripts/hap.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function () {
    (new HapInstance({
      id:'hapFeaturedPhotos',
      request_url:'<?php echo $this->url();?>?format=json',
      max_width:150,
      loading_on_scroll:false
    }));
  });
</script>

<ul class="hapPhotos" id="hapFeaturedPhotos">
  <?php
    $this->id_prefix = 'hapFeaturedPhotos';
  ?>
  <?php echo $this->render('application/modules/Headvancedalbum/views/scripts/_photoItems.tpl');?>
</ul>