<script src="<?php $this->baseURL()?>application/modules/Ynlistings/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynlistings/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynlistings/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynlistings/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->baseURL()?>application/modules/Ynlistings/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">
<script type="text/javascript">
    window.addEvent('load', function() {
        new Picker.Date($$('.date_picker'), { 
            positionOffset: {x: 5, y: 0}, 
            pickerClass: 'datepicker_dashboard', 
            useFadeInOut: !Browser.ie,
            onSelect: function(date){
            }
        });
    });
</script>
<h2>
    <?php echo $this->translate('YouNet Listings Plugin') ?>
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

<h3><?php echo $this->translate('Manage Transactions') ?></h3>

<p>
	<?php echo $this->translate('YNLISTINGS_MANAGE_TRANSACTION_DESCRIPTION') ?>
</p>

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
      <th><?php echo $this->translate('Purchased Date') ?></th>
      <th><?php echo $this->translate('Payment Status') ?></th>
      <th><?php echo $this->translate('Listing') ?></th>
      <th><?php echo $this->translate('Listing Owner') ?></th>
      <th><?php echo $this->translate('Description') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
        <?php
            $creation_date = new Zend_Date(strtotime($item->creation_date));
            $creation_date->setTimezone($this->timezone);
            $listing = $item->getListing();
        ?>
      <tr>
        <td><?php echo (($item->payment_transaction_id) ? $item->payment_transaction_id : $item->transaction_id) ?></td>
        <td><?php echo ($this->methods[$item->gateway_id]) ? $this->methods[$item->gateway_id] : $this->translate('Unknown Method') ?></td>
        <td><?php echo $this->locale()->toDate($creation_date) ?></td>
        <td><?php echo $item->status ?></td>
        <td><?php echo ($listing) ? $listing->title : $this->translate('unknown')?></td>
        <td><?php echo ($listing) ? $listing->getOwner() : $this->translate('unknown')?></td>
        <td><?php echo $item->description ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo ($this->translate('Total').' '.$total.' '.$this->translate('result(s)'));
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
      <?php echo $this->translate('There are no Transactions.') ?>
    </span>
  </div>
<?php endif; ?>
