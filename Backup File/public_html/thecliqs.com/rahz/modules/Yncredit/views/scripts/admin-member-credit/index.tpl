<h2>
  <?php echo $this->translate('User Credits Plugin') ?>
</h2>
<script type="text/javascript">
	en4.core.runonce.add(function(){
		$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ 
			var checked = $(this).checked;
			var checkboxes = $$('td.yncredit_check input[type=checkbox]');
			checkboxes.each(function(item){
				item.checked = checked;
			});
		})
  });

  function actionSelected()
  {
    var checkboxes = $$('td.yncredit_check input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    var url = '<?php echo $this -> url(array('module' => 'yncredit', 'controller' => 'member-credit', 'action' => 'send-mass-credits'),'admin_default')?>/ids/' + selecteditems;
    Smoothbox.open(url);
  }
	function changeOrder(listby, default_direction)
	{
	    var currentOrder = '<?php echo $this->formValues['orderby'] ?>';
	    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      	// Just change direction
      	if( listby == currentOrder ) {
        	$('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      	} else {
        	$('orderby').value = listby;
        	$('direction').value = default_direction;
      	}
      	$('filter_form').submit();
    }
</script>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<br/>
<div>
	<div style="float: left; padding-right: 30px; margin-top: 6px;">
   		<?php echo $this -> translate(array("%s member found","%s members found", $this -> members -> getTotalItemCount()), $this -> members -> getTotalItemCount())?>
   </div>
   <?php echo $this->paginationControl($this->members, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
</div>
<div style="clear: both;"></div>
<br />
<?php if($this -> members -> getTotalItemCount()): ?>
	<div style="overflow: auto">
		<table class='admin_table'>
		  <thead>
		    <tr>
		      <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
		      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');">ID</a></th>
		      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
		      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('current_credit', 'DESC');"><?php echo $this->translate("Total Balance") ?></a></th>
		      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('earned_credit', 'DESC');"><?php echo $this->translate("Total Earned") ?></a></th>
		      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('spent_credit', 'DESC');"><?php echo $this->translate("Total Spent")?></a></th>
		      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('bought_credit', 'DESC');"><?php echo $this->translate("Total Bought") ?></a></th>
		      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('sent_credit', 'DESC');"><?php echo $this->translate("Total Gave") ?></a></th>
		      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('received_credit', 'DESC');"><?php echo $this->translate("Total Received") ?></a></th>
		      <th><?php echo $this->translate("Action") ?></th>
		    </tr>
		  </thead>
		  <tbody> 
		    <?php foreach ($this->members as $item):?>
		      <tr>
		        <td class="yncredit_check"><input type='checkbox' class='checkbox' value="<?php echo $item->user_id ?>"/></td>
		        <td><?php echo $item->getIdentity();?></td>
		        <td><?php echo $item->getOwner();?></td>
		        <td><?php echo $this->locale()->toNumber($item->current_credit?$item->current_credit:0); ?></td>
		        <td><?php echo $this->locale()->toNumber($item->earned_credit?$item->earned_credit:0); ?></td>
		        <td><?php echo $this->locale()->toNumber($item->spent_credit?$item->spent_credit:0); ?></td>
		        <td><?php echo $this->locale()->toNumber($item->bought_credit?$item->bought_credit:0); ?></td>
		        <td><?php echo $this->locale()->toNumber($item->sent_credit?$item->sent_credit:0); ?></td>
		        <td><?php echo $this->locale()->toNumber($item->received_credit?$item->received_credit:0); ?></td>
		        <td>
		          <?php echo $this->htmlLink(
		          		  $this -> url(array('module' => 'yncredit', 'controller' => 'member-credit', 'action' => 'transactions', 'id' => $item -> user_id), 'admin_default'),
		                  $this->translate('View Transactions'),
		                  array('class' => 'smoothbox')) ?>
		        </td>
		      </tr>
		    <?php endforeach; ?>
		  </tbody>
		</table>
		<br/>
		<div class='buttons'>
		  <button onclick="javascript:actionSelected();" type='button'>
		    <?php echo $this->translate("Send Mass Credits/Debits") ?>
		  </button>
		</div>
	</div>
<?php else:?>
	<div class="tip">
	    <span>
	        <?php echo $this->translate("There are no members.") ?>
	    </span>
	</div>
<?php endif; ?>