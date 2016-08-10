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
   <?php echo $this->translate("Welcome can be configured based on various conditions. You can add multiple content and only one content will be shown to users if they satisfy its condition. That way each subset of members can see different welcome tab. If members satisfy condition of multiple content, the one that has higher order will be shown."); ?> 
</p>
<br />
<div>
  <a href="<?php echo $this->url(array('action' =>'add-content')) ?>" class="buttonlink ynfeed_icon_add" title="<?php echo $this->translate('Add More Content');?>"><?php echo $this->translate('Add More Content');?></a>
</div>
<br />
<?php if(count($this->contents)):?>
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
		    <th style="width: 10%"><input <?php if (count($this -> contents) == count($this -> contentsShow)) echo 'checked'?> id="show_check_all" onclick='showAll(this);' type='checkbox' class='checkbox' /><?php echo $this->translate("Show") ?></th>
		    <th style="width: 10%"><?php echo $this->translate("Options") ?></th>
	     </tr>
	  </thead>
	  <tbody id='demo-list'>
	  	<?php foreach ($this->contents as $item) : ?>
	  	<tr id='filter_item_<?php echo $item->getIdentity() ?>'>
	  		<td>
	  			<input type='checkbox' class='multiselect_checkbox' value="<?php echo $item->getIdentity(); ?>"/>
	  		</td>
	  		<td><?php echo $this->translate($item->title); ?></td>
	  		<td>
	  			<input type="checkbox" class="show_checkbox" value="1" onclick="showContent(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->show) echo 'checked'?>/>
	  		</td>
	        <td>
	        	<a href='<?php echo $this->url(array('action' => 'edit-content','content_id'=> $item->getIdentity())) ?>'>
		              <?php echo $this->translate("edit") ?>
		        </a>
              | <a class = 'smoothbox' href='<?php echo $this->url(array('action' => 'delete-content','content_id'=> $item->getIdentity())) ?>' class="smoothbox">
	              <?php echo $this->translate("delete") ?>
	            </a>
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
            <?php echo $this->translate("There are no contents added yet.") ?>
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
          url: '<?php echo $this->url(array('controller'=>'welcome','action'=>'sort'), 'admin_default') ?>',
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
          'url' : '<?php echo $this->url(array('module' => 'ynfeed', 'controller'=>'welcome','action'=>'show-multi'), 'admin_default') ?>',
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

function showContent(obj, id) 
{
    var value = (obj.checked) ? 1 : 0;
    new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'ynfeed', 'controller'=>'welcome','action'=>'show'), 'admin_default') ?>',
          'data' : {
            'value' : value,
            'id': id
          }
        }).send();
}
</script>