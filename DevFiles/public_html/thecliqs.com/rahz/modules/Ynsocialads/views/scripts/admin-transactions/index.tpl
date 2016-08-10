<h2>
    <?php echo $this->translate('YouNet Social Ads Plugin') ?>
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

<h3><?php echo $this->translate('YNSOCIALADS_MANAGE_TRANSACTION_TITLE') ?></h3>

<p>
	<?php echo $this->translate("YNSOCIALADS_MANAGE_TRANSACTION_DESCRIPTION") ?>
</p>

<br />
<div class="yn_filter">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<table class='admin_table ynsocial_table' style="width: 100%">
  <thead>
    <tr>
      <th><?php echo $this->translate('Payment Method') ?></th>
      <th><?php echo $this->translate('Transaction ID') ?></th>
      <th><?php echo $this->translate('Start Date') ?></th>
      <th><?php echo $this->translate('Status') ?></th>
      <th><?php echo $this->translate('Amount') ?></th>
      <th><?php echo $this->translate('Ad') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><?php echo ($this->methods[$item->gateway_id] != null) ? $this->methods[$item->gateway_id] : $this->translate('Unknown Method')?></td>
        <td><?php echo (($item->payment_transaction_id) ? $item->payment_transaction_id : $item->transaction_id) ?></td>
        <td><?php echo $this->locale()->toDate($item->start_date) ?></td>
        <td><?php echo $item->status ?></td>
        <td><?php echo $this->locale() -> toCurrency($item->amount, $item->currency) ?></td>
       <td>
          <?php $ads = Engine_Api::_() -> getItem('ynsocialads_ad', $item->ad_id); 
          echo $this->htmlLink($ads -> getHref(), $this -> string() -> truncate($ads -> name, 10), array()); ?>
      </td>
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
