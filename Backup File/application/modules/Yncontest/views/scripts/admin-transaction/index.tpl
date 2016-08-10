<h2><?php
echo $this->translate("Contest Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('yncontest.admin-main-menu') ?>

<br /> 
<div class='yncontest_admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
 <?php //echo $this->count." ".$this->translate('order(s)');   ?>
 <br/>

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
	var jqueryNew = jQuery.noConflict();
	jqueryNew( "#from" ).datepicker({
		showOn: "button",
		buttonImage: "<?php echo $this->layout()->staticBaseUrl;?>application/modules/Yncontest/externals/images/calendar.gif",
		buttonImageOnly: true
	});
	jqueryNew( "#to" ).datepicker({
		showOn: "button",
		buttonImage: "<?php echo $this->layout()->staticBaseUrl;?>application/modules/Yncontest/externals/images/calendar.gif",
		buttonImageOnly: true
	});
  </script>
<?php if( count($this->paginator) ): ?>  
<table class='yncontest_admin_table admin_table'>
  <thead>
    <tr>
      <th class="yncontest_cell_center"><?php echo $this->translate("ID") ?></th>
      <th class="yncontest_cell_center"><?php echo $this->translate("Contest Name") ?></th>
      <th class="yncontest_cell_center"><?php echo $this->translate("Owner") ?></th>
      <th class="yncontest_cell_center"><a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_date', 'DESC');"><?php echo $this->translate("Registered Date") ?></a></th>
      <th class="yncontest_cell_center"><a href="javascript:void(0);" onclick="javascript:changeOrder('option_service', 'DESC');"><?php echo $this->translate("Service Type") ?></a></th>
      <th class="yncontest_cell_center"><a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');"><?php echo $this->translate("Fee") ?></a></th>
      <th class="yncontest_cell_center"><?php echo $this->translate("Status") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
    	
      <tr>
        <td class="yncontest_cell_center">
        	<?php echo $item->params; ?>
        </td>
		<td class="yncontest_cell_center">
        	<?php
        			$contest = Engine_Api::_()->getItem('yncontest_contest',$item->contest_id);
        			if(is_object($contest))
        				echo $this->htmlLink($contest->getHref(), $item->contest_name);
        			else
        				echo $this->translate("Deleted Contest");
        	?>
        </td>
        
        <td class="yncontest_cell_center">
        	<?php 
        		$user = Engine_Api::_()->getItem('user',$item->user_buyer);						
        		echo $user;
        	?>
        </td>
        
        <td class="yncontest_cell_center">
        <?php                 	
			echo $this->locale()->toDate( $item->transaction_date, array('size' => 'short'));
		 ?>
		</td>
                
		<td class="yncontest_cell_center">
        	<?php 
        	//echo Engine_Api::_()->yncontest()->arrPlugins[$item->contest_type]; 
        	if($item->option_service ==1)
				echo $this->translate("Publish");
			if($item->option_service ==2)
				echo $this->translate("Feature");
			if($item->option_service ==3)
				echo $this->translate("Premium");
			if($item->option_service ==4)
				echo $this->translate("Ending Soon");
        	?>        	
        </td>
        
        <td class="yncontest_cell_right">
        	<?php echo Engine_Api::_()->yncontest()->getSymbol($item->currency).$item->amount; ?>
        </td>
        
        <td class="yncontest_cell_center">
        	<?php echo $item->transaction_status; ?>
        </td>
      </tr>
    
    <?php endforeach; ?>
  </tbody>
</table>

<br />
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no transactions yet.") ?>
    </span>
  </div>
<?php endif; ?>