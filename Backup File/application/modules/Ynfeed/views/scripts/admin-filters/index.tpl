<h2>
  <?php echo $this->translate('Advanced Feed Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    /*---- Render the menu ----*/
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<p>
   <?php echo $this->translate("This page allows you to manage filter options that appears on Activity Feed filter list. You can drag and drop to reorder filters. Some of the core filters can't be deleted but they can be hidden by unticking the show check box."); ?> 
</p>
<br />
<div>
  <a href="<?php echo $this->url(array('action' =>'add-filter')) ?>" class="buttonlink ynfeed_icon_add smoothbox" title="<?php echo $this->translate('Add More Filter');?>"><?php echo $this->translate('Add More Filter');?></a>
</div>
<br />

<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
</form>
<div class="admin_table_form">
	<form id='multiselect_form' method="post" action="<?php echo $this->url();?>">
	<table class='admin_table'>
	  <thead>
	    <tr>
	    	<th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
	    	<th style="width: 40%"><?php echo $this->translate("Filter Name") ?></th>
		    <th><?php echo $this->translate("Module") ?></th>
		    <th style="width: 10%"><?php echo $this->translate("Icon") ?></th>
		    <th style="width: 10%"><input <?php if (count($this -> contents) == count($this -> contentsShow)) echo 'checked'?> id="show_check_all" onclick='showAll(this);' type='checkbox' class='checkbox' /><?php echo $this->translate("Show") ?></th>
		    <th style="width: 10%"><?php echo $this->translate("Options") ?></th>
	     </tr>
	  </thead>
	  <tbody id='demo-list'>
	  	<?php foreach ($this->contents as $item) : ?>
	  	<tr id='filter_item_<?php echo $item->getIdentity() ?>'>
	  		<td>
	  			<?php if($item -> default == 0):?>
	  				<input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/>
	  			<?php endif;?>
	  		</td>
	  		<td><?php echo $this->translate($item->resource_title); ?></td>
	  		<td><?php echo $item->module_title; ?></td>
	  		<td>
	  			<?php $image_url = $item -> getPhotoUrl("thumb.main", 1);
	  			if($image_url):?>
                <img width="16px" src="<?php echo $image_url;?>" />
                <?php endif;?>
	  		</td>
	  		<td>
	  			<?php if($item -> content_tab):?><input type="checkbox" class="show_checkbox" value="1" onclick="showFilter(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->show) echo 'checked'?>/><?php endif;?></td>
	        <td>
	        	<?php if((empty($item->default) || $item -> filter_type == 'membership') && Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfeed.customlist.filtering', 1)):?>
	        	<a href='<?php echo $this->url(array('action' => 'custom-lists','module_name'=>$item->module_name)) ?>'>
		              <?php echo $this->translate("custom lists") ?>
		        </a>
		        |
		        <?php endif; ?>
	        	<a class = 'smoothbox' href='<?php echo $this->url(array('action' => 'edit-filter','module_name'=>$item->module_name, 'filter_type' => $item->filter_type)) ?>'>
		              <?php echo $this->translate("edit") ?>
		        </a>
		         <?php if(empty($item->default)):?>
	              | <a class = 'smoothbox' href='<?php echo $this->url(array('action' => 'delete-filter','filter_type' => $item->filter_type)) ?>' class="smoothbox">
		              <?php echo $this->translate("delete") ?>
		            </a>
	              <?php endif; ?>
		    </td>
		</tr>
		<?php endforeach; ?>
	   </tbody>
	</table>
	</form>
</div>
<div class='buttons'>
    <button type='button' onclick="deleteSelected()"><?php echo $this->translate('Delete Selected') ?></button>
</div>
<script type="text/javascript">
  en4.core.runonce.add(function(){
    new Sortables('demo-list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('controller'=>'filters','action'=>'sort'), 'admin_default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'order': this.serialize().toString(),
          }
        }).send();
      }
    });
});

function selectAll() {
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled && inputs[i].hasClass('multiselect_checkbox')) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}
function showAll(obj) 
{
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.elements;
    for (i = 1; i < inputs.length; i++) 
    {
        if (!inputs[i].disabled && inputs[i].hasClass('show_checkbox')) 
        {
            inputs[i].checked = obj.checked;
        }
    }
    var value = (obj.checked) ? 1 : 0;
	new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'ynfeed', 'controller'=>'filters','action'=>'show-multi'), 'admin_default') ?>',
          'data' : {
            'value' : value,
          }
        }).send();
}

function deleteSelected()
{
    var checkboxes = $$('td input.multiselect_checkbox[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on')
      {
        selecteditems.push(value);
      }
    });
    $('ids').value = selecteditems;
    $('multiselect').submit();
}

function showFilter(obj, id) 
{
    var value = (obj.checked) ? 1 : 0;
    new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'ynfeed', 'controller'=>'filters','action'=>'show'), 'admin_default') ?>',
          'data' : {
            'value' : value,
            'id': id
          }
        }).send();
}
</script>