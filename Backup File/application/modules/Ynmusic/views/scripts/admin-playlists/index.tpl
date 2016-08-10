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
    $('multidelete').action = '<?php echo $this->url(array('module'=>'ynmusic','controller'=>'playlists','action'=>'multidelete'), 'admin_default', true) ?>';
    $('ids').value = selecteditems;
    $('multidelete').submit();
}
</script>

<h2><?php echo $this->translate("YouNet Music Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
<br>
<?php if( count($this->paginator) ): ?>
<form id='multidelete' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
</form>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>">
  <table class='admin_table' style="width: 100%;">
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th style="width: 40%;"><?php echo $this->translate("Name") ?></th>
        <th><?php echo $this->translate("Added by") ?></th>
        <th><?php echo $this->translate("No of Songs") ?></th>
        <th><?php echo $this->translate("Last Update") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' <?php echo ($item->isDeletable()) ? 'class="checkbox"' : 'disabled'?> name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
          <td><?php echo $item ?></td>
          <td><?php echo $item->getOwner()?></td>
          <td><?php echo $item->getCountSongs(NULL, $item) ?>
          <td><?php echo $this->locale()->toDateTime($item->getModifiedDate()) ?></td>
          <td class="option-link">
          	<?php if ($item->isEditable()) :?>
            <?php echo $this->htmlLink(array('route'=>'ynmusic_playlist','action'=>'edit','id'=>$item->getIdentity()),
              'edit',
              array()) ?>
            <?php endif; ?>
            
          	<?php if ($item->isDeletable()) :?>
      		<?php echo $this->htmlLink(array('route'=>'ynmusic_playlist','action'=>'delete','id'=>$item->getIdentity()),
              'delete',
              array('class'=>'smoothbox')) ?>
            <?php endif; ?>
          </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<br />
</form>

<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>
<div class='buttons'>
    <button type='button' onclick="deleteSelected()"><?php echo $this->translate('Delete Selected') ?></button>
</div>

  <br />
  <div>
     <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no playlists.") ?>
    </span>
  </div>
<?php endif; ?>
