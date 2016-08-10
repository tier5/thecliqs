<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 24.08.12
 * Time: 14:35
 * To change this template use File | Settings | File Templates.
 */?>

<h2><?php echo $this->translate("Donation Plugin") ?></h2>
<?php if (count($this->navigation)): ?>
<div class='donation_admin_tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<div class="clr"></div>


<div class="menu_right" style="width:200px">
  <ul class="menu_dashboard_links">
    <li style="width:200px">
      <ul >
        <li class="hecore-menu-tab">
          <a href="<?php echo $this->url(array('module' => 'donation' ,'controller'=>'statistics', 'action' => 'index'),'admin_default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('DONATION_Chart'); ?>
          </a>
        </li>

        <li class="hecore-menu-tab active-menu-tab">
          <a href="<?php echo $this->url(array('module' => 'donation', 'controller'=>'statistics', 'action' => 'list'),'admin_default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('DONATION_List'); ?>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</div>

<div class="admin_home_middle" style="clear: none;">
  <h3><?php echo $this->translate("DONATION_Statistics") ?></h3>
  <p>
    <?php echo $this->translate("DONATION_VIEWS_SCRIPTS_ADMINSTATS_INDEX_DESCRIPTION") ?>
  </p>

  <br />
<div class="admin_search">
  <?php echo $this->form->render($this) ?>
</div>

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

  <div class="admin_results">
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

  <table class='admin_table' style="width:100%">
    <thead>
    <tr>
      <th class='table_short'>
        <a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'DESC');">
          <?php echo $this->translate("ID") ?>
        </a>
      </th>

      <th>
        <?php echo $this->translate("Donation") ?>
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
      <td class="admin_table_bold">
        <?php echo $item->transaction_id ?>
      </td>

      <td class="admin_table_bold">
        <?php echo $donation->__toString(); ?>
      </td>

      <td>
        <?php echo $user ? $user->__toString() : '<i>' .$item->name . " " . $this->translate('(anonymously)') . '</i>' ?>
      </td>

      <td>
        <?php echo $item->gateway;?>
      </td>

      <td >
        <?php echo $item->state;?>
      </td>

      <td>
        <?php echo $item->gateway_transaction_id;?>
      </td>

      <td class="center">
        <span class="donation_credit_icon">
          <span class="admin_table_bold donation_credit_price" style="color: red;"> <?php echo $this->locale()->toCurrency($item->amount, $item->currency) ?> </span>
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
<?php if ($this->paginator->getTotalItemCount() <= 0): ?>
<div class="tip">
      <span>
        <?php echo $this->translate('DONATION_There no transaction has been done yet.');?>
      </span>
</div>
<?php endif; ?>