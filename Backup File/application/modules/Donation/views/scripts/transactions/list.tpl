<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 22.08.12
 * Time: 19:18
 * To change this template use File | Settings | File Templates.
 */
?>
<?php if ($this->subject):?>
  <?php echo $this->render('editMenu.tpl')?>
<?php endif; ?>

<div class="headline">
  <h2><?php echo $this->translate('DONATION_transaction %s', $this->donation->__toString());?></h2>
</div>
<div class="clr"></div>

<?php echo $this->render('statistics.tpl');?>

<div class="layout_middle" style="clear: none;">
  <p>
    <?php echo $this->translate("DONATION_VIEWS_SCRIPTS_STATISTICS_TRANSACTIONS_DESCRIPTION %s", $this->donation->type) ?>
  </p>

  <br/>

  <?php if ($this->paginator->getTotalItemCount() > 0): ?>

  <div>
    <?php echo $this->form->render($this) ?>
  </div>
  <?php endif; ?>

</div>



<script type="text/javascript">
  var currentOrder = '<?php echo $this->values['order'] ?>';
  var currentOrderDirection = '<?php echo $this->values['direction'] ?>';
  var changeOrder = function (order, default_direction) {
    // Just change direction
    if (order == currentOrder) {
      $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('direction').value = default_direction;
    }
    $('search_form').submit();
  }
</script>

<br/>


<div class="layout_middle">
  <?php if ($this->paginator->getTotalItemCount() <= 0): ?>
  <div class="tip">
      <span>
        <?php echo $this->translate('DONATION_There no transaction has been done yet.');?>
      </span>
  </div>
  <?php return; endif; ?>
  <div class="donation-list-result">
    <div>
      <?php $count = $this->paginator->getTotalItemCount() ?>
      <?php echo $this->translate(array("%s transaction found", "%s transactions found", $count), $count) ?>
    </div>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->values,
      'pageAsQuery' => true,
    )); ?>
    </div>
  </div>

  <br/>

  <?php if ($this->paginator->getTotalItemCount() > 0): ?>

  <table class='table store-product-list' style="width:100%">
    <thead>
    <tr>
      <th class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'DESC');">
          <?php echo $this->translate("ID") ?>
        </a>
      </th>

      <th>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');">
          <?php echo $this->translate("Member") ?>
        </a>
      </th>

      <th>
        <?php echo $this->translate("Gateway");?>
      </th>

      <th>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'ASC');">
          <?php echo $this->translate("Status");?>
        </a>
      </th>

      <th>
        <?php echo $this->translate('Gateway Transaction ID');?>
      </th>

      <th class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('amount', 'DESC');">
          <?php echo $this->translate("Total Amount") ?>
        </a>
      </th>

      <th style="width: 100px">
        <a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');">
          <?php echo $this->translate("Date") ?>
        </a>
      </th>

      <th class='table_short'>
        <?php echo $this->translate("Options") ?>
      </th>

    </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item):
      $user = @$this->users[$item->user_id];
      $order = @$this->orders[$item->order_id];
      $donation = isset($this->donations[$item->item_id]) ? @$this->donations[$item->item_id] : null;
      ?>
    <tr>
      <td>
        <?php echo $item->transaction_id ?>
      </td>

      <td>
        <?php echo $user ? $user->__toString() : '<i>' .$item->name . " " . $this->translate('(anonymously)') . '</i>' ?>
      </td>

      <td>
        <?php echo $item->gateway;?>
      </td>

      <td>
        <?php echo $item->state;?>
      </td>

      <td>
        <?php echo $item->gateway_transaction_id;?>
      </td>

      <td class="center">
        <span class="donation_credit_icon">
          <span class="donation_credit_price" style="color: red;"> <?php echo $this->locale()->toCurrency($item->amount, $item->currency) ?> </span>
        </span>
      </td>
      <td>
        <?php echo $this->timestamp($item->creation_date) ?>
      </td>
      <td>
        <a href='<?php echo $this->url(array('action' => 'detail',
          'transaction_id' => $item->transaction_id));?>'>
          <?php echo $this->translate("details") ?>
        </a>
      </td>
    </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>