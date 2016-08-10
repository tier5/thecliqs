<script type="text/javascript">

function selectAll() {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}

function deleteSelected(){
    var checkboxes = $$('td input.checkbox[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('multidelete').action = en4.core.baseUrl +'admin/ynlistings/faqs/multidelete';
    $('ids').value = selecteditems;
    $('multidelete').submit();
}
</script>
<h2>
    <?php echo $this->translate('YouNet Listings Plugin') ?>
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

<h3><?php echo $this->translate('FAQs') ?></h3>

<p>
	<?php echo $this->translate("YNLISTINGS_MANAGE_FAQ_DESCRIPTION") ?>
</p>

<div class="add_link">
<?php echo $this->htmlLink(
array('route' => 'admin_default', 'module' => 'ynlistings', 'controller' => 'faqs', 'action' => 'create'),
$this->translate('add FAQ'), 
array(
    'class' => 'buttonlink add_faq',
)) ?>
</div>
<?php if( count($this->paginator) ): ?>
<form id='multidelete' method="post" action="">
        <input type="hidden" id="ids" name="ids" value=""/>
  </form>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>">
<table class='admin_table ynsocial_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th><?php echo $this->translate("Question") ?></th>
      <th><?php echo $this->translate("Status") ?></th>
      <th><?php echo $this->translate("Order") ?></th>
      <th><?php echo $this->translate("Created") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
        <td><?php echo $this->translate($item->title) ?></td>
        <td><?php echo ucfirst($this->translate($item->status)) ?></td>
        <td><?php echo $item->order ?></td>
        <td><?php echo $this->locale()->toDateTime($item->created_date) ?></td>
        <td>
              <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynlistings', 'controller' => 'faqs', 'action' => 'edit', 'id' => $item->faq_id),
                    $this->translate('edit')
              )?>
              |
              <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynlistings', 'controller' => 'faqs', 'action' => 'delete', 'id' => $item->faq_id),
                    $this->translate("delete"),
                    array('class' => 'smoothbox')
              )?> 
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo ($this->translate('Total').' '.$total.' '.$this->translate('result(s)'));
    echo '</p>';
}?>
<div class='buttons'>
  <button type='button' onclick="deleteSelected()"><?php echo $this->translate('Delete Selected') ?></button>
</div>
</form>

<br/>
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no FAQs.") ?>
    </span>
  </div>
<?php endif; ?>