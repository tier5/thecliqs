<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  10.09.12 15:21 TeaJay $
 * @author     Taalay
 */
?>

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

<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
  function pay($status) {
    var url = '<?php echo $this->url(array('module' => 'credit', 'controller' => 'buy-offer', 'action' => 'pay'), 'default', true) ?>';
    var request = new Request.JSON({
      url : url,
      data : {
        'format': 'json',
        'status': $status
      },
      onSuccess:function($response) {
        if ($response.status == 'active') {
          var return_url = '<?php echo $this->url(array('action' => 'finish', 'state' => 'active', 'offer_id' => $this->offer->offer_id), 'offers_subscription', true)?>';
          window.location.href = return_url;
        } else if ($response.status == 'pending') {
          var return_url = '<?php echo $this->url(array('action' => 'finish', 'state' => 'pending', 'offer_id' => $this->offer->offer_id), 'offers_subscription', true)?>';
          window.location.href = return_url;
        }
      }
    });
    request.send();
  }
</script>

<div class="layout_right">
  <div class="layout_credit_my_credits">
    <div class="credit_my_credits_widget">
      <ul class="credit_lists">
        <li>
          <h3 style="margin: 5px 0; text-align: center"><?php echo $this->translate('Order Summary')?></h3>
        </li>
        <li>
          <div class="my_credits_icon">
            <img src="application/modules/Credit/externals/images/current.png" title="<?php echo $this->translate('Current Balance')?>"/>
          </div>
          <div class="my_credits_desc">
            <b><?php echo $this->locale()->toNumber($this->credits->current_credit)?></b>
            <p><?php echo $this->translate('Current Balance')?></p>
          </div>
        </li>
        <li>
          <div class="my_credits_icon">
            <img src="application/modules/Credit/externals/images/spent.png" title="<?php echo $this->translate('OFFERS_Price')?>"/>
          </div>
          <div class="my_credits_desc">
            <b><?php echo $this->locale()->toNumber(Engine_Api::_()->offers()->getCredits($this->offer->price_offer))?></b>
            <p><?php echo $this->translate('OFFERS_Price')?></p>
          </div>
        </li>
        <li>
          <div class="checkout-item center">
            <?php if ($this->credits->current_credit >= Engine_Api::_()->offers()->getCredits($this->offer->price_offer)) : ?>
              <span>
                <button class="button" onclick="pay('continue');">
                  <?php echo $this->translate('Confirm'); ?>
                </button>
              </span>
              <span><?php echo $this->translate(' or '); ?></span>
            <?php endif; ?>
            <span><?php echo $this->htmlLink('javascript://', $this->translate('cancel'), array('onclick' => 'pay("cancel")')); ?></span>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>

<div class="layout_middle">

<?php if ($this->credits->current_credit < Engine_Api::_()->offers()->getCredits($this->offer->price_offer)) : ?>
  <div class="tip" style="clear: none; font-size: 14px; padding-bottom: 10px;">
    <span>
      <?php echo $this->translate('CREDIT_not-enough-credit'); ?>
    </span>
  </div>
<?php endif; ?>

  <ul class="my_offers">
    <li class="offer_item">
      <div class="offer_photo" style="background-image: url('<?php echo $this->offer->getPhotoUrl('thumb.normal')?>');"></div>
      <div class="right">
        <div class="offer_info">
          <div class="offer_title">
            <h3><?php echo $this->htmlLink($this->offer->getHref(), $this->offer->getTitle()); ?></h3>
          </div>
          <div class="offer_price">
            <label><?php echo $this->translate('OFFERS_offer_price'); ?></label>
            <?php echo $this->getOfferPrice($this->offer); ?>
          </div>
          <div class="offer_discount">
            <label><?php echo $this->translate('OFFERS_offer_discount'); ?></label>
              <span><?php echo $this->offer->discount; ?><?php if ($this->offer->discount_type == 'percent'): ?>
                % <?php endif; ?></span>
          </div>
          <div class="offer_count">
            <label><?php echo $this->translate('OFFERS_offer_available'); ?></label><span><?php echo $this->translate('%s coupons', $this->offer->coupons_count); ?></span>
          </div>
          <div class="offer_redeem">
            <label><?php echo $this->translate('OFFERS_Redeem'); ?></label>
            <span><?php echo Engine_Api::_()->offers()->timeInterval($this->offer);?></span>
          </div>
          <?php if ($this->offer->page_id > 0): ?>
            <div class="offer_presented_by">
              <label><?php echo $this->translate('OFFERS_Presented by'); ?></label>
              <span><?php if ($this->offer->page_id > 0) echo $this->offer->getPage()->getTitle(); ?></span>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </li>
  </ul>
</div>