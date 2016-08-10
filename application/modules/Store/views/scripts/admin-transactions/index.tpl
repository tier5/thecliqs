<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-09-21 17:53 mirlan $
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

<?php if( !empty($this->error) ): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->error ?>
    </li>
  </ul>

  <br />
<?php return; endif; ?>

<div class="admin_home_middle" style="clear: none;">
  <h3><?php echo $this->translate("STORE_Product Transactions") ?></h3>
  <p>
    <?php echo $this->translate("CORE_VIEWS_SCRIPTS_ADMINTRANSACTIONS_INDEX_DESCRIPTION") ?>
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
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('direction').value = default_direction;
    }
    $('filter_form').submit();
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
        <?php $class = ( $this->order == 'transaction_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('transaction_id', 'DESC');">
            <?php echo $this->translate("ID") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'user_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');">
            <?php echo $this->translate("Member") ?>
          </a>
        </th>
        <th style='width: 1%;' class="<?php echo $class ?>">
          <?php echo $this->translate("Order Key") ?>
        </th>
        <?php $class = ( $this->order == 'item_type' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class="<?php echo $class ?>">
          <a href="javascript:void(0);" onclick="javascript:changeOrder('item_type', 'ASC');">
            <?php echo $this->translate("Item Type") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'gateway_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('gateway_id', 'ASC');">
            <?php echo $this->translate("Gateway") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'state' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('state', 'DESC');">
            <?php echo $this->translate("State") ?>
          </a>
        </th>
        <?php $class = ( $this->order == 'amt' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->direction) : '' ) ?>
        <th style='width: 1%;' class='admin_table_centered <?php echo $class ?>'>
          <a href="javascript:void(0);" onclick="javascript:changeOrder('amt', 'DESC');">
            <?php echo $this->translate("Total Amount") ?>
          </a>
        </th>
        <th class='admin_table_centered'>
          <?php echo $this->translate("Gateway fee") ?>
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
        $user = @$this->users[$item->user_id];
        $gateway = @$this->gateways[$item->gateway_id];
        ?>
        <tr>
          <td class="center"><?php echo $item->transaction_id ?></td>
          <td class='admin_table_bold'>
            <?php echo ( $user ? $user->__toString() : '<i>' . $this->translate('Deleted or Unknown Member') . '</i>' ) ?>
          </td>
          <td class='admin_table_bold center'>
            <?php echo $item->ukey; ?>
          </td>
          <td class='center'>
            <?php echo ucfirst(str_replace('_', ' ', $item->item_type)); ?>
          </td>
          <td class='center'>
            <?php echo ( $gateway ? $gateway->title : '<i>' . $this->translate('Unknown Gateway') . '</i>' ) ?>
          </td>
          <td class='center'>
            <?php echo $this->translate(ucfirst($item->state)) ?>
          </td>
          <td class='center'>
            <?php if ($item->via_credits) : ?>
              <span class="store_credit_icon">
                <span class="store-credit-price"><?php echo $this->api->getCredits($item->amt); ?></span>
              </span>
            <?php else : ?>
              <span class="store-price"><?php echo $this->locale()->toCurrency($item->amt, $item->currency) ?>
              <?php echo $this->translate('(%s)', $item->currency) ?></span>
            <?php endif; ?>
          </td>
          <td class='center'>
            <?php if ($item->via_credits) : ?>
              <span class="store_credit_icon">
                <span class="store-credit-price"><?php echo $this->api->getCredits($item->gateway_fee); ?></span>
              </span>
            <?php else : ?>
              <span class="store-price">&ndash;<?php echo $this->locale()->toCurrency($item->gateway_fee, $item->currency) ?>
              <?php echo $this->translate('(%s)', $item->currency) ?></span>
            <?php endif; ?>
          </td>
          <td>
            <?php echo $this->locale()->toDateTime($item->timestamp) ?>
          </td>
          <td class='admin_table_options'>
            <a class="smoothbox" href='<?php echo $this->url(array('action' => 'detail', 'transaction_id' => $item->transaction_id));?>'>
              <?php echo $this->translate("details") ?>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>