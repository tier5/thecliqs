<script src="<?php $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->layout()->staticBaseUrl?>application/modules/Ynbusinesspages/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">

<script type="text/javascript">
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
    window.addEvent('domready', function() {
    	
        new Picker.Date($$('.date_picker'), { 
            positionOffset: {x: 5, y: 0}, 
            pickerClass: 'datepicker_dashboard', 
            useFadeInOut: !Browser.ie,
            onSelect: function(date){
            }
        });
    });
</script>

<h2><?php echo $this->translate("YouNet Business Pages Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Transactions') ?></h3>

<br />
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<table class='admin_table ynsocial_table' style="width: 100%">
  <thead>
    <tr>
      <th><?php echo $this->translate('Transaction ID') ?></th>
      <th><?php echo $this->translate('Payment Method') ?></th>
      <th><?php echo $this->translate('Business Owner') ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('business.name', 'ASC');"><?php echo $this->translate('Business') ?></a></th>
      <th><?php echo $this->translate('Paid Date') ?></th>
      <th><?php echo $this->translate('Amount') ?></th>
      <th><?php echo $this->translate('Description') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><?php echo (($item->payment_transaction_id) ? $item->payment_transaction_id : $item->transaction_id) ?></td>
        <td><?php echo ($this->methods[$item->gateway_id]) ? $this->methods[$item->gateway_id] : $this->translate('Unknown Method') ?></td>
        <?php $user = Engine_Api::_() -> getItem('user', $item -> user_id); ?>
        <?php if($user): ?>
       	 <td><a href='<?php echo $user -> getHref() ?>'><?php echo $user -> getTitle(); ?></a></td>
        <?php else:?>
        	<td><?php echo $this->translate('Unknown')?></td>	
        <?php endif;?>
        <td>
        	<?php $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $item -> item_id); ?>
        	<?php if($business):?>
        		<?php echo $this->htmlLink($business->getHref(), $business->getTitle()); ?>
        	<?php else:?>
        		<i><?php echo $this -> translate('Deleted business');?></i>
        	<?php endif;?>
        </td>
        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
        <td><?php echo $this -> locale()->toCurrency($item->amount, $item->currency)?></td>
        <td><?php echo $item->description ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
	echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>
<br/>
<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no transactions.') ?>
    </span>
  </div>
<?php endif; ?>
