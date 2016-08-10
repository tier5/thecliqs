<h2>
    <?php echo $this->translate('Ultimate Video Plugin') ?>
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

<h3><?php echo $this->translate('Migration Report') ?></h3>

<?php echo $this->htmlLink(
    array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'migration-video'),
    '<i class="fa fa-arrow-circle-left"></i>'.$this->translate('Back to Migration Page'),
   	array('class' => 'back-migration')
)?>

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>

<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
</form>
<form id='multiselect_form' class="ynadmin-table" method="post" action="<?php echo $this->url();?>">
    <table class='admin_table'>
        <thead>
            <tr>
            	<th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                <th><?php echo $this->translate("Item") ?></th>
                <th><?php echo $this->translate("Orginal Item") ?></th>
                <th><?php echo $this->translate("Type") ?></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user.displayname', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('report.modified_date', 'ASC');"><?php echo $this->translate("Update Time") ?></a></th>
                <th><?php echo $this->translate("Status") ?></th>
                <th><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody id="import-items">
        <?php foreach ($this->paginator as $item): ?>
            <tr id="import-item_<?php echo $item->import_id?>">
            	<td><input type='checkbox' <?php if (in_array($item->status, array('processing','updating'))) echo 'disabled';?> class='checkbox' name='remove_<?php echo $item->import_id; ?>' value="<?php echo $item->import_id; ?>" /></td>
            	<?php $newItem = ($item->status == 'processing') ? null : Engine_Api::_()->getItem($item->item_type, $item->item_id);?>
            	<?php $oldItem = Engine_Api::_()->getItem($item->from_type, $item->from_id);?>
                <td><?php echo ($item->status == 'processing') ? '-' : (($newItem) ? $newItem : $this->translate('Deleted Item')) ?></td>
                <td><?php echo ($oldItem) ? $oldItem : $this->translate('Deleted Item') ?></td>
                <td><?php echo $this->translate(substr($item->item_type, 16))?></td>
                <td><?php echo ($newItem) ? $newItem->getOwner() : (($oldItem) ? $oldItem->getOwner() : $this->translate('Unknown')) ?></td>
                <td><?php echo $this->locale()->toDateTime($item->modified_date) ?></td>
                <td><?php echo $this->translate($item->status) ?></td>
                <td class="options-list">
                <?php if ($newItem && $oldItem && !in_array($item->status, array('processing','updating'))) :?>
				<?php $canUpdate = Engine_Api::_()->ynultimatevideo()->canUpdateImport($newItem, $oldItem);?>
				<?php if ($canUpdate) :?>
				<?php echo $this->htmlLink(
				    array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'migration-video', 'action' => 'update-videos', 'id' => $item->import_id),
				    $this->translate('update videos'),
				   	array('class' => 'smoothbox')
				)?>	               
               	<?php endif;?>
               	<?php endif;?>
               	
               	<?php if (!in_array($item->status, array('processing','updating'))) :?>
               		<?php echo $this->htmlLink(
					    array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'migration-video', 'action' => 'remove', 'id' => $item->import_id),
					    $this->translate('remove'),
					   	array('class' => 'smoothbox')
					)?>
           		<?php endif;?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</form>
<div><?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?></div>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>

<div class='buttons'>
    <button type='button' onclick="removeSelected()"><?php echo $this->translate('Remove Selected') ?></button>
</div>
<?php else: ?>
<div style="margin-top: 10px">
	<div class="tip">
	    <span><?php echo $this->translate("There are no imported items.") ?></span>
	</div>
</div>
<?php endif; ?>

<script type="text/javascript">

function selectAll() {
    var i;
    var multiselect_form = $('multiselect_form');
    var inputs = multiselect_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}

function removeSelected(){
    var checkboxes = $$('td input.checkbox[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
        var checked = item.checked;
        var value = item.value;
        if (checked == true && value != 'on'){
            selecteditems.push(value);
        }
    });
    $('multiselect').action = '<?php echo $this->url(array('module'=>'ynultimatevideo','controller'=>'migration-video','action'=>'multiremove'), 'admin_default', true) ?>';
    $('ids').value = selecteditems;
    $('multiselect').submit();
}

function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
    // Just change direction
    if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } 
    else {
        $('order').value = listby;
        $('direction').value = default_direction;
    }
    $('filter_form').submit();
}
</script>