
<div class="layout_left">
<?php echo $this->partial('_options.tpl', array(
		'contest'=>$this->item,
		'announcement' => $this->announcement,		
		'checkMaxEntries'=>$this->checkMaxEntries,	
		'plugin' => $this->plugin,
		));?>
</div>


<script type="text/javascript">
  var tagAction =function(tag){
        window.location = en4.core.baseUrl + 'contest/listing?tags=' + tag;  
  }
</script>
