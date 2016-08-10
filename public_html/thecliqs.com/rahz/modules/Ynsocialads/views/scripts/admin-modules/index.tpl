<script type="text/javascript">

function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('order').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }

function actionSelected(actionType){
    var checkboxes = $$('td.ynsocialads_check input[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('action_selected').action = en4.core.baseUrl +'admin/ynsocialads/modules/' + actionType + '-selected';
    $('ids').value = selecteditems;
    $('action_selected').submit();
  }



function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<h2>
  <?php echo $this->translate('SocialAds Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
	<?php echo $this->translate("YNSOCIALADS_MANAGE_MODULE_DESCRIPTION") ?>
</p>
<br />

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<table class='admin_table ynsocial_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('module_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('module_name', 'DESC');"><?php echo $this->translate("Module Name") ?></a></th>
      <th><?php echo $this->translate("Module Title") ?></th>
	  <th><?php echo $this->translate("Item Table") ?></th>
	  <th><?php echo $this->translate("Title Field") ?></th>
	  <th><?php echo $this->translate("Body Field") ?></th>
      <th><?php echo $this->translate("Owner Field") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td class="ynsocialads_check"><input type='checkbox' class='checkbox' value='<?php echo $item->getIdentity() ?>' /></td>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $item->module_name ?></td>
        <td><?php echo $item->module_title ?></td>
		<td><?php echo $item->table_item ?></td>
		<td><?php echo $item->title_field ?></td>
		<td><?php echo $item->body_field ?></td>
		<td><?php echo $item->owner_field ?></td>
        <td>
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'modules', 'action' => 'edit', 'id' => $item->getIdentity()),
                $this->translate("edit"),
                array('class' => 'smoothbox')) ?>
          |      
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'modules', 'action' => 'delete', 'id' => $item->getIdentity()),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
  <button type='button' onclick="javascript:actionSelected('delete');"><?php echo $this->translate("Delete Selected") ?></button>
  <a href='<?php echo $this->url(
					array(
						'module'=>'ynsocialads',
						'controller' => 'modules', 
						'action' => 'create',
					), 
					'admin_default', 
					true
				); ?>' class='smoothbox'>
<button type="button"><?php echo $this->translate("Add New Module") ?></button> 
  </a>
</div>
</form>
<br/>
  <form id='action_selected' method="post" action="">
   		<input type="hidden" id="ids" name="ids" value=""/>
  </form>
<div>
    <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
 </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no module entries yet.") ?>
    </span>
  </div>
  <div class='buttons'>
    <a href='<?php echo $this->url(
					array(
						'module'=>'ynsocialads',
						'controller' => 'modules', 
						'action' => 'create',
					), 
					'admin_default', 
					true
				); ?>' class='smoothbox'>
		<button type="button"><?php echo $this->translate("Add New Module") ?></button> 
  </a>
</div>
<?php endif; ?>
