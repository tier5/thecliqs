<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  5/17/12 6:12 PM mt.uulu $
 * @author     Mirlan
 */
?>
<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if( count($this->navigation) ): ?>
  <div class='store_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class="admin_home_middle" style="clear: none;">
  <h3><?php echo $this->translate("STORE_Product Orders") ?></h3>
  <p>
    <?php echo $this->translate("CORE_VIEWS_SCRIPTS_ADMINTRANSACTION_ORDER_DESCRIPTION") ?>
  </p>

  <br/>

  <div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
  </div>
</div>

<br/>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->filterValues['order'] ?>';
  var currentOrderDirection = '<?php echo $this->filterValues['direction'] ?>';
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


<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s transaction found", "%s transactions found", $count), $count) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->filterValues,
      'pageAsQuery' => true,
    )); ?>
  </div>
</div>

<br />

<?php if ($count > 0) : ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'>
          <?php echo $this->translate("Order Key") ?>
        </th>
        <?php $class = ( $this->order == 'name' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('name', 'ASC');">
            <?php echo $this->translate("Product") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'user_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');">
            <?php echo $this->translate("Member") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'status' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('status', 'DESC');">
            <?php echo $this->translate("Status") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'qty' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('qty', 'DESC');">
            <?php echo $this->translate("STORE_Quantity") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'total_amt' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('total_amt', 'DESC');">
            <?php echo $this->translate("Total Amount") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'commission_amt' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 100px;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('commission_amt', 'DESC');">
            <?php echo $this->translate("Commission") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'timestamp' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 100px;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('timestamp', 'DESC');">
            <?php echo $this->translate("Date") ?>
          </a>
        </th>
        <th style='width: 1%;' class='admin_table_options'>
          <?php echo $this->translate("Options") ?>
        </th>
      </tr>
    </thead>
    <tbody>
      <?php foreach( $this->paginator as $item ):
        $user    = @$this->users[$item->user_id];
        $order   = @$this->orders[$item->order_id];
        /**
         * @var $product Store_Model_Product
         */
        $product = isset($this->products[$item->item_id])?@$this->products[$item->item_id]:null;
        ?>
        <tr>
          <td><?php echo $item->ukey ?></td>
          <td>
            <?php echo (($product instanceof Store_Model_Product)? $product->__toString() : '<i>' . $item->name . '</i>') ?>
          </td>
          <td>
            <?php echo ($user ? $user->__toString() : '<i>' . $this->translate('Deleted or Unknown Member') . '</i>') ?>
          </td>
          <td class="center">
            <?php echo $this->translate(ucfirst($item->status)); ?>
            <?php if( in_array($item->status, array('processing', 'shipping')) ): ?>
              &nbsp;
              (<?php echo $this->htmlLink($this->url(array('action'=>'status', 'orderitem_id' => $item->getIdentity())), $this->translate('Change'), array('class'=>'smoothbox')); ?>)
            <?php endif; ?>
          </td>
          <td class="center">
            <?php echo $this->locale()->toNumber($item->qty); ?>
          </td>
          <td class="center">
            <?php if ($item->via_credits) : ?>
              <span class="store_credit_icon">
                <span class="store-credit-price"><?php echo $this->api->getCredits($item->total_amt * $item->qty); ?></span>
              </span>
            <?php else : ?>
              <span class="store-price"><?php echo $this->locale()->toCurrency(($item->total_amt * $item->qty), $item->currency) ?>
              <?php echo $this->translate('(%s)', $item->currency) ?></span>
            <?php endif; ?>
          </td>
          <td class="center">
            <?php if ($item->via_credits) : ?>
              <span class="store_credit_icon">
                <span class="store-credit-price" style="color: red;"><?php echo $this->api->getCredits($item->commission_amt * $item->qty); ?></span>
              </span>
            <?php else : ?>
              <span class="store-price">&ndash;<?php echo $this->locale()->toCurrency(($item->commission_amt * $item->qty), $item->currency) ?>
              <?php echo $this->translate('(%s)', $item->currency) ?></span>
            <?php endif; ?>
          </td>
          <td>
            <?php if(isset($item->timestamp)): ?>
            <?php echo $this->locale()->toDateTime($item->timestamp) ?>
            <?php else: ?>
            <?php echo $this->locale()->toDateTime($item->payment_date) ?>
            <?php endif; ?>
          </td>
          <td class="center">
            <a class="smoothbox" href='<?php echo $this->url(array('action' => 'detail',
              'orderitem_id' => $item->orderitem_id));?>'>
              <?php echo $this->translate("details") ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>