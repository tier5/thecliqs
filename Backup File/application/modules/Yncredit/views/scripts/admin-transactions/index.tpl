<h2>
  <?php echo $this->translate('User Credits Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<p>
	<?php echo $this -> translate("This page lists all of the transactions your users have created.")?>
</p>
<br />
<div class="admin_search">
  <div class="search">
    <?php echo $this->form->render($this) ?>
  </div>
</div>
<?php if($this -> transactions -> getTotalItemCount() > 0):?>
<br />
<table class='admin_table' style="width: 100%">
    <thead>
        <tr>
            <th>
            	<a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');">
                	<?php echo $this->translate("Member") ?>
               </a>
            </th>
            <th>
            	<a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');">
                	<?php echo $this->translate("Date") ?>
                </a>
            </th>
            <th>
            	<a href="javascript:void(0);" onclick="javascript:changeOrder('group', 'ASC');">
                	<?php echo $this->translate("Group Type") ?>
                </a>
            </th>
            <th>
            	<a href="javascript:void(0);" onclick="javascript:changeOrder('module', 'ASC');">
                	<?php echo $this->translate("Module") ?>
                </a>
            </th>
            <th>
            	<a href="javascript:void(0);" onclick="javascript:changeOrder('action_type', 'ASC');">
                	<?php echo $this->translate("Action Type") ?>
                </a>
            </th>
            <th>
            	<a href="javascript:void(0);" onclick="javascript:changeOrder('credit', 'DESC');">
                	<?php echo $this->translate("Credits") ?>
                </a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->transactions as $transaction): ?>
            <tr>
            	<td><?php echo $transaction -> getOwner()?></td>
            	<td><?php 
	                $start_time = strtotime($transaction -> creation_date);
					$oldTz = date_default_timezone_get();
					if($this->viewer() && $this->viewer()->getIdentity())
					{
						date_default_timezone_set($this -> viewer() -> timezone);
					}
					else 
					{
						date_default_timezone_set( $this->locale() -> getTimezone());
					}
					echo date("F j, Y H:i:s", $start_time);
	                date_default_timezone_set($oldTz);?></td>
                <td><?php echo $this -> translate('YNCREDIT_GROUP_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $transaction->group), '_')))?></td>
                <td><?php echo $this -> translate('YNCREDIT_MODULE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $transaction->module), '_')))?></td>
                <td><?php
		        	if(!empty($transaction -> object_type) && !empty($transaction -> object_id))
					{
						$obj = Engine_Api::_() -> getItem($transaction -> object_type, $transaction -> object_id);
						if($obj && $obj -> getType() == 'activity_action')
						{
							$obj = Engine_Api::_() -> getItem($obj -> object_type, $obj -> object_id);
						}
						if($obj)
						{
							if(substr_count($transaction -> content, "%") > 1)
							{
								echo $this -> translate($transaction -> content, $transaction -> item_count, $obj);
							}
							else
							{
								echo $this -> translate($transaction -> content, $obj);
							}
						}
					}
					else 
					{
						echo $this -> translate($transaction -> content,"");
					}
		        	?></td>
		        <td class="yncredit-color-<?php echo ($transaction -> credit >= 0)?'up':'down' ?>">
		        	<?php echo $this->locale()->toNumber($transaction -> credit)?>
		        </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
	<br/>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no transactions.") ?>
        </span>
    </div>
<?php endif; ?>
<br/>
<div class="pages">
   <?php echo $this->paginationControl($this->transactions, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"> </script>
<script src="application/modules/Yncredit/externals/scripts/jquery-ui-1.8.17.custom.min.js"></script>
<script type="text/javascript">
	var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
    var changeOrder = function(order, default_direction){
        // Just change direction
        if( order == currentOrder ) {
            $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
        } else {
            $('order').value = order;
            $('direction').value = default_direction;
        }
        $('filter_form').submit();
    }
	jQuery.noConflict();
    jQuery(document).ready(function(){
        // Datepicker
        jQuery('#start_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Yncredit/externals/images/calendar.png',
            buttonImageOnly: true
        });
        jQuery('#end_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Yncredit/externals/images/calendar.png',
            buttonImageOnly: true
        });
    });
</script>