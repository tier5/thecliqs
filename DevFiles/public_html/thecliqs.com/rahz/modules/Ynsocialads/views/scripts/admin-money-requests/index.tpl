<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">
<script type="text/javascript">
window.addEvent('load', function() {
     new Picker.Date($$('.date_picker'), { 
	    timePicker: true, 
	    positionOffset: {x: 5, y: 0}, 
	    pickerClass: 'datepicker_dashboard', 
	    useFadeInOut: !Browser.ie 
	});
});

function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('order').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }
</script>

<h2>
  <?php echo $this->translate('SocialAds Plugin') ?>
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

<p>
	<?php echo $this->translate("YNSOCIALADS_MANAGE_MONEY_REQUEST_DESCRIPTION") ?>
</p>

<br />


<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<form method="post" action="<?php echo $this->url();?>">
<table class='admin_table ynsocial_table'>
  <thead>
    <tr>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('moneyreq.request_date', 'DESC');"><?php echo $this->translate("Request Date") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user.displayname', 'DESC');"><?php echo $this->translate("Advertiser") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('moneyreq.amount', 'DESC');"><?php echo $this->translate("Amount") ?></a></th>
	 <th><a href="javascript:void(0);" onclick="javascript:changeOrder('moneyreq.status', 'DESC');"><?php echo $this->translate("Status") ?></a></th>
	  <th><?php echo $this->translate("Request Message") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><?php echo $this->locale()->toDateTime($item->request_date) ?></td>
		<td><?php echo $item->getOwner() ?></td>
		<td><?php echo $this->locale() -> toCurrency($item->amount, $item->currency) ?></td>
		<td><?php echo $item->status ?></td>
		<td><?php echo $item->request_message ?></td>
        <td>
        	<!-- View --> 
         	 <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'money-requests', 'action' => 'view-detail', 'id' => $item->getIdentity()),
                $this->translate("View"),
                array('class' => 'smoothbox')) ?>  
         <?php if($item -> status == "pending"):?>
          |
          <!-- Approve --> 
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'money-requests', 'action' => 'request-payment', 'status' => 'approved', 'id' => $item->getIdentity()),
                $this->translate("Approve")) ?>
           |
            <!-- Reject --> 
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'money-requests', 'action' => 'request-payment', 'status' => 'rejected', 'id' => $item->getIdentity()),
                $this->translate("Reject")) ?>
           <?php endif;?>
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

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no money requests yet.") ?>
    </span>
  </div>
<?php endif; ?>
