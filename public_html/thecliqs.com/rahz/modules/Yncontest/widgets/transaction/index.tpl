<div class='mycontest_search clearfix'>   
<?php  echo $this->form->render($this); ?>
</div>
 <?php //echo $this->count." ".$this->translate('order(s)');   ?>
 <br/>
<script type = "text/javascript">
	var jqueryNew = jQuery.noConflict();
	jqueryNew( "#from" ).datepicker({
		showOn: "button",
		buttonImage: "<?php echo $this->layout()->staticBaseUrl;?>application/modules/Yncontest/externals/images/calendar.gif",
		buttonImageOnly: true,
		onClose: function( selectedDate ) {
                jqueryNew( "#to" ).datepicker( "option", "minDate", selectedDate );
            }
	});
	jqueryNew( "#to" ).datepicker({
		showOn: "button",
		buttonImage: "<?php echo $this->layout()->staticBaseUrl;?>application/modules/Yncontest/externals/images/calendar.gif",
		buttonImageOnly: true,
		onClose: function( selectedDate ) {
                jqueryNew( "#from" ).datepicker( "option", "maxDate", selectedDate );
            }
	});
</script>
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
  </script>
<?php if( count($this->paginator) ): ?>

<table class='admin_table' width="100%">
  <thead>
    <tr>
      <th style = "text-align: center;"><?php echo $this->translate("ID") ?></th>
      <th style = "text-align: left;"><?php echo $this->translate("Contest Name") ?></th>
      <th style = "text-align: center;"><?php echo $this->translate("Owner") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_date', 'DESC');"><?php echo $this->translate("Registered Date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('option_service', 'DESC');"><?php echo $this->translate("Service Type") ?></a></th>
      <th style = "text-align: right;"><a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');"><?php echo $this->translate("Fee") ?></a></th>
      <th style = "text-align: center;"><?php echo $this->translate("Status") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
    	<?php if($item->payment_type): ?>
      <tr>
        <td style = "text-align: center;">
        	<?php echo $item->params; ?>
        </td>
        
		<td style = "text-align: left;">
        	<?php echo $item->contest_name; ?>
        </td>
        
        <td style = "text-align: center;">
        	<?php echo $item->owner_name; ?>
        </td>
        
        <td>
        <?php         
        	//date_default_timezone_set($this->viewer->timezone);
			//echo date('Y-m-d',strtotime($item->transaction_date));
			echo $this->locale()->toDate( $item->transaction_date, array('size' => 'short')) ;
		 ?>
		</td>
                
		<td style = "text-align: center;">
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
        
        <td style = "text-align: right;">
        	<?php echo Engine_Api::_()->yncontest()->getSymbol($item->currency).$item->amount; ?>
        </td>
        
        <td style = "text-align: center;">
        	<?php echo $this->translate($item->transaction_status); ?>
        </td>
        
      </tr>
      <?php endif; ?>
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
	
<style type="text/css">

.admin_search {
    max-width: 950px !important;
}
</style>
 