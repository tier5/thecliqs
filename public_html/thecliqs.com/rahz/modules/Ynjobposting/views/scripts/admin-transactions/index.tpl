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
</script>

<h2><?php echo $this->translate("YouNet Job Posting Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Manage Transactions') ?></h3>

<br />
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<table class='admin_table' style="width: 100%">
  <thead>
    <tr>
      <th><?php echo $this->translate('Transaction ID') ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('transaction.gateway_id', 'ASC');"><?php echo $this->translate('Payment Method') ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('job.title', 'ASC');"><?php echo $this->translate('Job Title') ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('company.name', 'ASC');"><?php echo $this->translate('Company Name') ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('transaction.creation_date', 'ASC');"><?php echo $this->translate('Paid Date') ?></th>
      <th><?php echo $this->translate('Amount') ?></th>
      <th><?php echo $this->translate('Description') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><?php echo (($item->payment_transaction_id) ? $item->payment_transaction_id : $item->transaction_id) ?></td>
        
        <td><?php echo ($this->methods[$item->gateway_id]) ? $this->methods[$item->gateway_id] : 'Unknown Method' ?></td>
        
        <?php if ($item->type == 'company') : ?>
        <td><?php echo '-'?></td>
        <?php else :?>
        <?php $job = Engine_Api::_()->getItem('ynjobposting_job', $item->item_id)?>
        <td><?php echo ($job) ? $this->htmlLink($job->getHref(), $job->getTitle()) : 'Unknown Job' ?></td>
        <?php endif; ?>
        
        <?php if ($item->type == 'company') : ?>
        <?php $company = Engine_Api::_()->getItem('ynjobposting_company', $item->item_id)?>
        <?php else :?>
        <?php $company = ($job) ? $job->getCompany() : null;?>
        <?php endif; ?>
        
        <td><?php echo ($company) ? $this->htmlLink($company->getHref(), $company->getTitle()) : 'Unknown Company' ?></td>
        
        <td><?php echo $this->locale()->toDate($item->creation_date) ?></td>
        
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
