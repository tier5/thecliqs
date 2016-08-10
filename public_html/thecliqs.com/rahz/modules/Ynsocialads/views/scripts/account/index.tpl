<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<br />


<div class="yn_filter">
    <?php echo $this->form->render($this);?>
</div>
<?php if( count($this->paginator) ): ?>
<div class="fixed-scrolling">
<table class='ynsocial_table frontend_table'>
  <tr>
    <th><?php echo $this->translate('Payment Method') ?></th>
    <th><?php echo $this->translate('Transaction ID') ?></th>
    <th><?php echo $this->translate('Date') ?></th>
    <th><?php echo $this->translate('Status') ?></th>
    <th><?php echo $this->translate('Amount') ?></th>
    <th><?php echo $this->translate('Ad') ?></th>
  </tr>
  <?php foreach ($this->paginator as $item): 
  		$show_date = 0;
		if(!empty($item->start_date))
		{
	    	$startDateObject = new Zend_Date(strtotime($item->start_date));
			if( $this->viewer() && $this->viewer()->getIdentity() ) 
			{
				$tz = $this->viewer()->timezone;
				$startDateObject->setTimezone($tz);
			}
			$show_date = 1;
		}
		else {
			$show_date = 0;
		}
  	?>
    <tr>
      <td><?php echo ($this->methods[$item->gateway_id] != null) ? $this->methods[$item->gateway_id] : $this->translate('Unknown Method')?></td>
      <td><?php echo (($item->payment_transaction_id) ? $item->payment_transaction_id : $item->transaction_id) ?></td>
      <td><?php echo $this->locale()->toDate($startDateObject) ?></td>
      <td><?php echo $this->translate($item->status) ?></td>
      <td><?php echo $this->locale() -> toCurrency($item->amount, $item->currency) ?></td>
      <td>
          <?php $ads = Engine_Api::_() -> getItem('ynsocialads_ad', $item->ad_id); 
          echo $this->htmlLink($ads -> getHref(), $this -> string() -> truncate($ads -> name, 10), array()); ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
</div>

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

<script type="text/javascript">
  $$('.core_main_ynsocialads').getParent().addClass('active');
</script>