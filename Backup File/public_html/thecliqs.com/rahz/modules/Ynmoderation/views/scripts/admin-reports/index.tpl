<script type="text/javascript">
  en4.core.runonce.add(function() {
    $$('th.admin_table_short input[type=checkbox]').addEvent('click', function(event) {
      var el = $(event.target);
      $$('input[type=checkbox]').set('checked', el.get('checked'));
    });
  });
  
	var delectSelected = function() {
		var checkboxes = $$('input[type=checkbox]');
	    var selecteditems = [];
	
	    checkboxes.each(function(item, index){
	      var checked = item.get('checked');
	      var value = item.get('value');
	      if (checked == true && value != 'on'){
	        selecteditems.push(value);
	      }
	    });
	
	    $('ids').value = selecteditems;
	    $('delete_selected').submit();
	}

</script>

<h2>
  <?php echo $this->translate('Moderation Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      /*---- Render the menu ----*/
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>
<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>


<br/>

<?php if( count($this->paginator) ): ?>
<form id='ynmoderation_form' method="post" action="<?php echo $this->url();?>" onSubmit="return confirm('Are you sure you want to dismiss the selected report?');">
<div class="admin_table_form">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class="admin_table_short"><input type='checkbox' class='checkbox'></th>
        <th>
            <?php echo $this->translate("Description") ?>
        </th>
        <th>
          <?php echo $this->translate("Reporter") ?>
        </th>
        <th>
            <?php echo $this->translate("Date") ?>
        </th>
        <th>
            <?php echo $this->translate("Content Type") ?>
        </th>
        <th>
            <?php echo $this->translate("Reasons") ?>
        </th>
        <th>
          <?php echo $this->translate("Options") ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->paginator as $item ): ?>
      	<td><input type='checkbox' class='checkbox' name='<?php echo $item['report_type']; ?>[]' value="<?php echo $item['report_id']; ?>" /></td>
        <td style="white-space: normal;"><?php echo $item['description'] ?></td>
        <td class="nowrap"><?php echo ($item['user_id']) ? Engine_Api::_()->user()->getUser($item['user_id']) : "" ?></td>
        <td class="nowrap"><?php echo $item['creation_date'] ?></td>
        <td class="nowrap"><?php echo $item['subject_type'] ?></td>
        <td class="nowrap"><?php echo $item['category'] ?></td>
        <td class="admin_table_options">
          <?php echo $this->htmlLink(
		                array('route' => 'default', 'module' => 'ynmoderation', 'controller' => 'admin-moderations', 'action' => 'delete','type' => $item['subject_type'], 'id' => $item['subject_id']),
		                $this->translate("delete content"),
		                array('class' => 'smoothbox')) ?>
          <span class="sep">|</span>
          
          <?php if( !empty($item['subject_type']) ): ?>
	          	<?php echo $this->htmlLink(
						array('route' => 'default', 'module' => 'ynmoderation', 'controller' => 'admin-moderations', 'action' => 'view','type' => $item['subject_type'], 'id' => $item['subject_id']),
			          	$this->translate('view content')) ?>
				<span class="sep">|</span>
          <?php endif; ?>
          
          <?php echo $this->htmlLink(
          				array('route' => 'default', 'module' => 'ynmoderation', 'controller' => 'admin-reports', 'action' => 'delete', 'r_type' => $item['report_type'], 'r_id' => $item['report_id']),
          				$this->translate("dismiss"),
          				array('class' => 'smoothbox')) ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <br/>

  <div class='buttons'>
    <button onclick="javascript:delectSelected();" type='submit'><?php echo $this->translate("Dismiss Selected") ?></button>
  </div>



<?php else:?>

  <div class="tip">
    <span><?php echo $this->translate("There are currently no outstanding reports.") ?></span>
  </div>

<?php endif; ?>
</div>
</form>
