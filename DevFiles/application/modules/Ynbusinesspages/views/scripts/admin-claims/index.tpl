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

<h3><?php echo $this->translate('Manage Claim Requests') ?></h3>

<br />
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<table class='admin_table ynsocial_table' style="width: 100%">
  <thead>
    <tr>
      <th><?php echo $this->translate('Claimed Date') ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('business.name', 'ASC');"><?php echo $this->translate('Business') ?></a></th>
      <th><?php echo $this->translate('Claimed By') ?></th>
      <th><?php echo $this->translate('Options') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
      	<td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
        <td>
        	<?php $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $item -> business_id); ?>
        	<?php echo $this->htmlLink($business->getHref(), $business->getTitle()); ?>
        </td>
        <?php $user = Engine_Api::_() -> getItem('user', $item -> user_id); ?>
        <?php if($user -> getIdentity() > 0): ?>
       		<td><a href='<?php echo $user -> getHref() ?>'><?php echo $user -> getTitle(); ?></a></td>
        <?php else:?>
        	<td><?php echo $this->translate('Unknown')?></td>	
        <?php endif;?>
        <td>
			<!-- Approve -->
      		<?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynbusinesspages', 'controller' => 'claims', 'action' => 'update', 'type' => 'approve', 'id' => $item->getIdentity()), 
           		  $this->translate('Approve'), 
           		   array('class' => 'smoothbox')) ?>
      		|
      		<!-- Deny -->
      		<?php echo $this->htmlLink(
            array('route' => 'admin_default', 'module' => 'ynbusinesspages', 'controller' => 'claims', 'action' => 'update', 'type' => 'deny', 'id' => $item->getIdentity()), 
           		  $this->translate('Deny'), 
           		   array('class' => 'smoothbox')) ?>
        </td>
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
      <?php echo $this->translate('There are no claim requests.') ?>
    </span>
  </div>
<?php endif; ?>
