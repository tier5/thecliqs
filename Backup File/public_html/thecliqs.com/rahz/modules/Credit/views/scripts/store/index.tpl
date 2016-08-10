<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  11.06.12 17:44 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
  <?php if ($this->status == 'failed') : ?>
    var url = '<?php echo $this->return_url ?>';
    var status = 'failed';
    var request = new Request.Post({
      url : url,
      data : {
        'status': status
      }
    });
    request.send();

    <?php exit(); ?>
  <?php endif; ?>

  function pay() {
    var url = '<?php echo $this->url(array('module' => 'credit', 'controller' => 'store', 'action' => 'pay'), 'default', true) ?>';
    var ukey = '<?php echo $this->ukey; ?>';
    var request = new Request.JSON({
      url : url,
      data : {
        'format': 'json',
        'ukey': ukey
      },
      onSuccess:function($response) {
        if ($response.status == 1) {
          back_to_store($response.data);
        } else {
          he_show_message($response.message, 'error', 3000);
        }
      }
    });
    request.send();
  }

  function back_to_store($data) {
    var url = '<?php echo $this->return_url ?>';
    var request = new Request.Post({
      url : url,
      data : $data
    });
    request.send();
  }
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Credits');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class="layout_left">

<div id="accordion">
  <h3><?php echo $this->translate('Order Summary')?></h3>
  <div class="content">
    <ul id="store-credit-panel">
      <li class="checkout-item">
        <div class="checkout-price">
          <span><?php echo $this->translate('STORE_items'); ?>:&nbsp;</span>
          <span class="store-price">
            <span class="store_credit_icon">
              <span class="store-credit-price"><?php echo $this->api->getCredits($this->order->item_amt); ?></span>
            </span>
          </span>
        </div>
      </li>
      <li class="checkout-item">
        <div class="checkout-tax">
          <span><?php echo $this->translate('STORE_tax');?>:&nbsp;</span>
          <span class="store-price">
            <span class="store_credit_icon">
              <span class="store-credit-price"><?php echo $this->api->getCredits($this->order->tax_amt); ?></span>
            </span>
          </span>
        </div>
      </li>
      <li class="checkout-item">
        <div class="checkout-shipping-price">
          <span><?php echo $this->translate('STORE_shipping');?>:&nbsp;</span>
          <span class="store-price">
            <span class="store_credit_icon">
              <span class="store-credit-price"><?php echo $this->api->getCredits($this->order->shipping_amt); ?></span>
            </span>
          </span>
        </div>
      </li>
      <div>
        <div class="checkout-total-price">
          <span class="checkout-title"><?php echo $this->translate('STORE_total');?>:&nbsp;</span>
          <span class="store-price">
            <span class="store_credit_icon">
              <span class="store-credit-price">
                <?php echo $this->api->getCredits($this->order->item_amt + $this->order->tax_amt + $this->order->shipping_amt); ?>
              </span>
            </span>
          </span>
        </div>

        <div class="checkout-item center">
          <?php if ($this->balance >= $this->api->getCredits($this->order->item_amt + $this->order->tax_amt + $this->order->shipping_amt)) : ?>
            <span>
              <button class="button" onclick="pay();">
                <?php echo $this->translate('Confirm'); ?>
              </button>
            </span>
            <span><?php echo $this->translate(' or '); ?></span>
          <?php endif; ?>
          <span><?php echo $this->htmlLink($this->cancel_url, $this->translate('cancel')); ?></span>
        </div>
      </div>
    </ul>
  </div>
</div>

</div>

<?php if ($this->balance < $this->api->getCredits($this->order->item_amt + $this->order->tax_amt + $this->order->shipping_amt)) : ?>
  <div class="tip" style="clear: none; font-size: 14px; padding-bottom: 10px;">
    <span>
      <?php echo $this->translate('CREDIT_not-enough-credit'); ?>
    </span>
  </div>
<?php endif; ?>

<div class="shipping-details">
  <?php if ($this->details): ?>
    <span class="float_left" style="font-weight: bold;"><?php echo $this->translate('Shipping Details');?>:&nbsp;</span>
    <?php if (isset($this->details['zip'])): ?>
      <span class="float_left">
        <?php
          echo $this->details['first_name'] . ' ' . $this->details['last_name'] . "<br />" .
            $this->details['city'] . ', ' . $this->region . ' ' . $this->details['zip'] . ', ' . $this->country . "<br />" .
            $this->details['address_line_1'] . (($this->details['address_line_2']) ? $this->translate(' or ') . $this->details['address_line_2'] : '') ."<br />" .
            $this->details['phone'] . (($this->details['phone_extension']) ? $this->translate(' or ') . $this->details['phone_extension'] : '')
          ;
        ?>
      </span>
    <?php endif; ?><br />
  <?php endif; ?>
  <span class="float_left" style="margin-top: 3px; font-weight: bold;"><?php echo $this->translate('Your Balance'); ?>:&nbsp;</span>
  <span class="store_credit_icon float_left">
    <span class="store-credit-price"><?php echo ($this->balance) ? $this->balance : 0; ?></span>
  </span>
</div>

<div class="layout_middle">

  <ul class="he-item-list" id="store_cart_items">
    <?php
    /**
     * @var $item    Store_Model_OrderItem
     * @var $product Store_Model_Product
     */
    foreach ($this->orderItems as $item): $product = $item->getProduct(); ?>
      <li>
        <div class="he-item-photo">
          <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.normal')) ?>
        </div>

        <div class="he-item-options store-item-options">
          <div class="store-price-block">
            <span class="store_credit_icon">
              <span class="store-credit-price"><?php echo $this->api->getCredits($item->item_amt); ?></span>
            </span> <br/>
            <?php if ($product->type == 'simple'): ?>
              <div class="store_products_count"><?php echo $this->translate('STORE_Quantity') . ': ' . $item->qty; ?></div>
            <?php endif; ?>
          </div>
        </div>

        <div class="he-item-info store-item-info">
          <div class="he-item-title">
            <h3><?php echo $this->htmlLink($product->getHref(), $this->string()->truncate($product->getTitle(), 20))?></h3>
          </div>
          <div style="display: inline-block;">
            <div class="rating">
              <?php echo $this->itemRate('store_product', $product->getIdentity()); ?>
            </div>
            <div class="clr"></div>
            <div class="he-item-details">
              <span class="float_left"><?php echo $this->translate('Posted'); ?>&nbsp;</span>
              <span class="float_left"><?php echo $this->timestamp($product->creation_date); ?>&nbsp;</span>
              <?php if ($product->hasStore()): ?>
                <?php echo $this->translate('in %s store', $this->htmlLink($product->getStore()->getHref(), $this->string()->truncate($product->getStore()->getTitle(), 20), array('target' => '_blank', 'title' => $product->getStore()->getTitle()))); ?>
              <?php endif; ?>
              <br />
            </div>
          </div>

          <?php if ($item->params != '[]') : ?>
            <div class="float_right">
              <table class="float_left">
                <?php foreach ($item->params as $param): ?>
                  <tr>
                    <td class="label"><strong><?php echo $param['label']; ?><strong>:&nbsp;&nbsp;</td>
                    <td><u><?php echo $param['value']; ?></u></td>
                  </tr>
                <?php endforeach; ?>
              </table>
            </div>
          <?php endif; ?>
          <br />
          <div class="he-item-desc">
            <?php echo $this->viewMore(Engine_String::strip_tags($product->getDescription())) ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>