<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: gateway.tpl  23.01.12 12:35 TeaJay $
 * @author     Taalay
 */
?>

<?php if( $this->status == 'pending' ): // Check for pending status ?>
  Your subscription is pending payment. You will receive an email when the
  payment completes.
<?php else: ?>

  <form method="post" action="<?php echo $this->escape($this->url(array('action' => 'credit-order'), 'credit_payment', true)) ?>"
        class="global_form" enctype="application/x-www-form-urlencoded">
    <div>
      <div>
        <h3>
          <?php echo $this->translate('Pay for Access') ?>
        </h3>
        <p class="form-description">
          <?php echo $this->translate('CREDIT_PAYMENT_GATEWAY_SELECT') ?>
        </p>
        <p style="font-weight: bold; padding-top: 15px; padding-bottom: 15px;">
          <?php echo $this->translate('You chose %s credits for %s', (int)$this->product->credit, $this->locale()->toCurrency($this->product->price, $this->currency)); ?>
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
            <?php foreach( $this->gateways as $gatewayInfo ):
              $gateway = $gatewayInfo['gateway'];
              $plugin = $gatewayInfo['plugin'];
              $first = ( !isset($first) ? true : false );
              ?>
              <?php if( !$first ): ?>
                <?php echo $this->translate(' or ') ?>
              <?php endif; ?>
              <button type="submit" name="gateway_id" value="<?php echo $gateway->gateway_id ?>">
                <?php echo $this->translate('Pay with %1$s', $this->translate($gateway->title)) ?>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </form>

<?php endif; ?>