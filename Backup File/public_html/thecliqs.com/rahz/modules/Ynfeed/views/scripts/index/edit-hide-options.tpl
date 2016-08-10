<div class="ynfeed_edit_settings">
	<h3><?php echo $this->translate('Edit Activity Feed Settings'); ?></h3>
	<?php if(count($this->hideItems)>0):?>
	    <p><?php echo $this->translate('Hide feeds from:');?></p>
		<div class="ynfeed_setting_lists">  	
	    	<?php foreach ($this->hideItems as $resource_type=>$hideItem):?>    
	      	<?php foreach ($hideItem as $item_id):?>
			      <div id="hide_item_<?php echo $resource_type ?>_<?php echo $item_id ?>" class="ynfeed_edit_setting_right_list">
			      	<?php $content = Engine_Api::_()->getItem($resource_type, $item_id);?>
			        <?php echo $content->getTitle();?>
			        <span onclick="selectForUnhideItem('<?php echo $resource_type ?>','<?php echo $item_id ?>');" class="fa fa-close ynfeed_icon_remove" title="<?php echo $this->translate("Remove"); ?>"></span>
			      </div>
		      <?php endforeach; ?>
	    	<?php endforeach;?>
	    </div>
	  <?php else: ?>
	      <br/>
	      <div class="tip">
	        <span>
	          <?php echo $this->translate("You have not hidden activity feeds from any sources."); ?>
	        </span>
	      </div>
	<?php endif; ?>
	<form action="" method="post" style="text-align: right">
		<input type="hidden" name="unhide_items" id="unhide_items" value="" />
		<?php if(count($this->hideItems)>0):?>
			<button type="submit"> <?php echo $this->translate('Save') ?></button>  
			<?php echo $this -> translate("or")?>
		<?php endif;?>
		<a href="javascript:;" onclick='javascript:parent.Smoothbox.close()'> <?php echo $this->translate('cancel') ?></a>
	</form>
	
	<div class="clr dblock"></div>
</div>
<script type="text/javascript">
  var hideItem=new Array();
  function selectForUnhideItem(type,id)
  { 
	   var content= type+'_'+id;
	   var el= document.getElementById('hide_item_'+content);
	   if(el)
	     el.style.display='none';
	   hideItem.push(type+'-'+id);
	  document.getElementById('unhide_items').value =hideItem;
  }
</script>
