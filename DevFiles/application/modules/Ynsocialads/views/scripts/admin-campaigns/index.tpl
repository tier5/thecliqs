<script type="text/javascript">
function multiDelete() {
    var url = (document.URL);
    if ($$('#multidelete_form input[type="checkbox"]:not(#check_all):checked').length > 0) {
        if (confirm("<?php echo $this->translate('Are you sure you want to delete the selected Compaigns?');?>")) {
            $('multidelete_form').set('action', url);
            return true;
        }
    }
    else
        return false;
}

function selectAll() {
    var hasElement = false;
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
    $('multidelete').action = en4.core.baseUrl +'admin/ynsocialads/campaigns/multidelete';
    $('ids').value = selecteditems;
    $('multidelete').submit();
}
</script>
<h2>
    <?php echo $this->translate('YouNet Social Ads Plugin') ?>
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

<h3><?php echo $this->translate('Manage Campaigns') ?></h3>

<p>
	<?php echo $this->translate("YNSOCIALADS_MANAGE_CAMPAGIN_DESCRIPTION") ?>
</p>

<br />

<div class="yn_filter">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<form id='multidelete' method="post" action="">
        <input type="hidden" id="ids" name="ids" value=""/>
  </form>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<table class='admin_table ynsocial_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th><?php echo $this->translate("Name") ?></th>
      <th><?php echo $this->translate("Ads") ?></th>
      <th><?php echo $this->translate("Status") ?></th>
      <th><?php echo $this->translate("Impressions") ?></th>
      <th><?php echo $this->translate("Clicks") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
    <?php $countDetail = $item->countDetail();?>
      <tr>
        <td>
        <input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" <?php if ($item->status == 'deleted') echo 'disabled'; ?>/>
        </td>
        <td><?php echo $this->htmlLink($this -> url(array('module' => 'ynsocialads', 'controller' => 'ads', 'campaign_id' => $item -> getIdentity()), 'admin_default', true), $this->translate($item->title), array()) ?></td>
        <td><?php echo $countDetail['ads'] ?></td>
        <td><?php echo $this->translate($item->status) ?></td>
        <td><?php echo $countDetail['impressions'] ?></td>
        <td><?php echo $countDetail['clicks'] ?></td>
        <td>
            <?php if ($item->status == 'active') { ?>
            <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'campaigns', 'action' => 'edit', 'id' => $item->campaign_id), 
            $this->translate('edit'), 
            array('class' => 'smoothbox')
            )?>
             | 
            <?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'campaigns', 'action' => 'delete', 'id' => $item->campaign_id),
            $this->translate("delete"),
            array('class' => 'smoothbox')
            )?> 
            <?php } ?>
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

<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>

</form>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no Campaigns.') ?>
    </span>
  </div>
<?php endif; ?>
