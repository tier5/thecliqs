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
<h3>
	<?php echo $this -> translate("Manage content types of '%s' module", $this -> module_name);?>
</h3>
<p>
   <?php echo $this->translate('You can manage content types of individual plugins on this page. Each plugin has its own content types. Content types added on this page are available to choose from when users create a custom filter list.'); ?>
</p>
<br />
<div>
  <a href="<?php echo $this->url(array('action' =>'index', 'module' => 'ynfeed', 'controller' => 'filters'), 'admin_default', true) ?>" class="buttonlink ynfeed_icon_back" title="<?php echo $this->translate('Back to Manage Filters');?>"><?php echo $this->translate('Back to Manage Filters');?></a>
  <?php if($this -> m_name != 'user'):?>
  	<a href="<?php echo $this->url(array('action' =>'add-custom')) ?>" class="buttonlink ynfeed_icon_add smoothbox" title="<?php echo $this->translate('Add a Content Type');?>"><?php echo $this->translate('Add a Content Type');?></a>
  <?php endif;?>
</div>
<br />
<?php if(count($this->customLists)):?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
</form>
<div class="admin_table_form">
	<form id='multiselect_form' method="post" action="<?php echo $this->url();?>">
	<table class='admin_table'>
	  <thead>
	    <tr>
	    	<th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
	    	<th style="width: 40%"><?php echo $this->translate("Title") ?></th>
		    <th><?php echo $this->translate("Resource Type") ?></th>
		    <th style="width: 10%"><input <?php if (count($this -> customLists) == count($this -> customListsEnabled)) echo 'checked'?> id="show_check_all" onclick='enableAll(this);' type='checkbox' class='checkbox' /><?php echo $this->translate("Enabled") ?></th>
		    <th style="width: 10%"><?php echo $this->translate("Options") ?></th>
	     </tr>
	  </thead>
	  <tbody id="demo-list">
	  	<?php foreach ($this->customLists as $item) : ?>
	  	<tr id='filter_item_<?php echo $item->getIdentity() ?>'>
	  		<td>
	  			<?php if($item -> default == 0):?>
	  				<input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/>
	  			<?php endif;?>
	  		</td>
	  		<td><?php echo $this->translate($item->resource_title); ?></td>
	  		<td><?php echo $item->resource_type; ?></td>
	  		<td>
	  			<input type="checkbox" class="enable_checkbox" value="1" onclick="enableCustom(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->enabled) echo 'checked'?>/></td>
	        <td>
	        	<a class = 'smoothbox' href='<?php echo $this->url(array('action' => 'edit-custom','module_name'=>$item->module_name, 'resource_type' => $item->resource_type)) ?>'>
		              <?php echo $this->translate("edit") ?>
		        </a>
		         <?php if(empty($item->default)):?>
	              | <a class = 'smoothbox' href='<?php echo $this->url(array('action' => 'delete-custom','resource_type' => $item->resource_type)) ?>' class="smoothbox">
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

<?php else:?>
	<div class="tip">
        <span>
            <?php echo $this->translate("There are no content types added yet.") ?>
        </span>
    </div>
<?php endif;?>
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
          url: '<?php echo $this->url(array('controller'=>'filters','action'=>'sort-custom'), 'admin_default') ?>',
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
function enableAll(obj) 
{
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.elements;
    for (i = 1; i < inputs.length; i++) 
    {
        if (!inputs[i].disabled && inputs[i].hasClass('enable_checkbox')) 
        {
            inputs[i].checked = obj.checked;
        }
    }
    var value = (obj.checked) ? 1 : 0;
	new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'ynfeed' ,'controller'=>'filters','action'=>'enable-multi'), 'admin_default', true) ?>',
          'data' : {
            'value' : value,
            'module_name': '<?php echo $this -> m_name?>'
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

function enableCustom(obj, id) 
{
    var value = (obj.checked) ? 1 : 0;
    new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'ynfeed', 'controller'=>'filters','action'=>'enable'), 'admin_default', true) ?>',
          'data' : {
            'value' : value,
            'id': id
          }
        }).send();
}
</script>