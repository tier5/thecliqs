<head>
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
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled && !inputs[i].hasClass('ajax_checkbox')) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}

function enable(obj, event, id) {
    event.preventDefault();
    var url = en4.core.baseUrl+'admin/ynsocialads/ad-blocks/enable/id/'+id;
    new Request.JSON({
        url: url,
        data: {},
        onSuccess: function(data) {
            if (data == "1") {
                obj.set('text','disable');
            }
            else {
                obj.set('text','enable');
            }
        }
    }).send();
}

function ajax(id) {
    var url = en4.core.baseUrl+'admin/ynsocialads/ad-blocks/ajax/id/'+id;
    new Request.JSON({
        url: url,
        data: {},
        onSuccess: function() {
        }
    }).send();
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
    $('multidelete').action = en4.core.baseUrl +'admin/ynsocialads/ad-blocks/multidelete';
    $('ids').value = selecteditems;
    $('multidelete').submit();
}
</script>
</head>
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

<h3><?php echo $this->translate('Manage Ad Blocks') ?></h3>

<p>
    <?php echo $this->translate("YNSOCIALADS_MANAGE_AD_BLOCK_DESCRIPTION") ?>
</p>

<div class="add_link">
<?php echo $this->htmlLink(
array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'ad-blocks', 'action' => 'create'),
$this->translate('Create New Ad Block'), 
array('class' => 'buttonlink add_adblock')) ?>
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
      <th><?php echo $this->translate("ID") ?></th>
      <th><?php echo $this->translate("Ad Block Title") ?></th>
      <th><?php echo $this->translate("Page") ?></th>
      <th><?php echo $this->translate("Placement Name") ?></th>
      <th><?php echo $this->translate("Creation Date") ?></th>
      <th><?php echo $this->translate("Limit") ?></th>
      <th><?php echo $this->translate("Ajax Based") ?></th>
      <th><?php echo $this->translate("Deleted") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" <?php if($item->deleted) echo 'disabled';?>/></td>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $item->title ?></td>
        <td><?php echo $this->translate($item->getPageName()) ?></td>
        <td><?php echo $this->placementMap[$item->placement] ?></td>
        <td><?php echo $this->locale()->toDate($item->creation_date) ?></td>
        <td><?php echo $item->ads_limit ?></td>
        <td><input type='checkbox' class='ajax_checkbox' onclick="javascript:ajax(<?php echo $item->getIdentity() ?>)"<?php if($item->ajax) echo 'checked';?> <?php if($item->deleted) echo 'disabled';?>/></td>
        <td><?php echo ($item->deleted) ? $this->translate('true') : $this->translate('false') ?></td>
        <td>
              <?php if (!$item -> deleted) : ?>
              <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'ad-blocks', 'action' => 'edit', 'id' => $item->getIdentity()),
                    $this->translate('edit'),
                    array('class' => 'smoothbox')
              )?>
              |
              <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'ad-blocks', 'action' => 'delete', 'id' => $item->getIdentity()),
                    $this->translate("delete"),
                    array('class' => 'smoothbox')
              )?>
               |  
              <?php if ($item->enable) : ?>
              <?php echo $this->htmlLink(
                    "",
                    $this->translate('disable'),
                    array('id'=>'enable', 'onclick'=>'javascript:enable(this, event, '.$item->getIdentity().')')
              )?>
              <?php else : ?>
                  <?php echo $this->htmlLink(
                    "",
                    $this->translate('enable'),
                    array('id'=>'enable', 'onclick'=>'javascript:enable(this, event, '.$item->getIdentity().')')
              )?>
              <?php endif; ?>
              <?php endif; ?>
                
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br />
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
      <?php echo $this->translate("There are no Ad Blocks.") ?>
    </span>
  </div>
<?php endif; ?>