<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: see-details.tpl  4/26/12 4:23 PM mt.uulu $
 * @author     Mirlan
 */
?>

<div class="global_form_popup">
  <?php if (!$this->isProductQuantityEnough): ?>
  <div class='tip'>
  <span>
      <?php echo $this->translate(
    'STORE_You can not purchase %1s amount of this product. Please, try to contact to the store owner or select available amount of quantity.',
    $this->item->qty
  ); ?>
  </span></div>
  <h4>
    <?php echo $this->translate(
    'STORE_%1s - available quantity: %2s',
    array(
      $this->htmlLink($this->product->getHref(), $this->product->getTitle(), array('target' => '_blank')),
      $this->product->quantity
    )
  ); ?>
  </h4>
  <?php endif;?>

  <?php if (!$this->isUserLocationSupported): ?>
  <br/>
  <div class='tip'>
    <span>
    <?php echo $this->translate(
      'STORE_Unfortunately, the store of this product does not support your location. Please, try to contact to the store owner or %1s your shipping details',
      $this->htmlLink('javascript:parent.location.href="' . $this->url(array(
                'controller'=> 'panel',
                'action'    => 'address',
              ), 'store_extended', true) . '"',
        $this->translate('update'), array('style'=>'font-weight: bold'))
    ); ?></span>
  </div>

  <h4>
    <?php echo $this->translate(
    'STORE_%1s available shipping locations',
    $this->htmlLink($this->product->getHref(), $this->product->getTitle(), array('target' => '_blank'))); ?>
  </h4>
  <?php if ($this->parent != null): ?>
    <div class="locations-tree">
<span style="float:left">
<?php echo $this->htmlLink(array(
  'route'      => 'store_extended',
  'controller' => 'cart',
  'action'     => 'see-details',
  'item_id'    => $this->item->getIdentity(),
  'format'     => 'smoothbox',
), $this->translate('Locations')); ?>
</span>

      <?php
      /**
       * @var $location Store_Model_Location
       */
      $location = $this->parent;
      do {
        ?>
<span style="float:left">
&nbsp;&#187;&nbsp;
        <?php echo $this->htmlLink(array(
          'route'      => 'store_extended',
          'controller' => 'cart',
          'action'     => 'see-details',
          'parent_id'  => $location->getIdentity(),
          'item_id'    => $this->item->getIdentity(),
          'format'     => 'smoothbox',
        ), $this->truncate($location->location)); ?>
        <?php $location = $location->getParent(); ?>
        <?php } while ($location != null); ?>
    </span>

    </div>
    <br/>
    <br/>
    <?php endif; ?>

  <?php if ($this->paginator->count() > 0): ?>
    <table class='store-product-list locations'>
      <thead>
      <tr>
        <th style="width: 500px;"><?php echo $this->translate("STORE_Location Name") ?></th>
        <?php if ($this->parent_id === 0): ?>
          <th><?php echo $this->translate("STORE_Sub-Locations") ?></th>
        <?php endif; ?>
        <th><?php echo $this->translate("STORE_Shipping Price") ?></th>
        <th><?php echo $this->translate("STORE_Shipping Days") ?></th>
      </tr>
      </thead>
      <tbody id="only-locations">
        <?php foreach ($this->paginator as $item): ?>
          <tr>
            <td class='admin_table_bold' style="width: 500px;">
              <?php echo $this->truncate($item->location, 160); ?>
            </td>
            <?php if ($this->parent_id === 0): ?>
              <td class="center">
                <a href="<?php echo $this->url(array('parent_id' => $item->getIdentity(),
                                                    'format'    => 'smoothbox')); ?>"
                  style="display: block; text-align: center">
                  <?php echo (int)$item->sub_locations; ?>
                </a>
              </td>
            <?php endif; ?>
            <td>
              <?php if (is_null($item->shipping_amt)): ?>
              <?php echo $this->translate('STORE_Only Sub-Locations'); ?>
              <?php else: ?>
              <?php echo $this->locale()->toCurrency($item->shipping_amt, $this->settings('payment.currency', 'USD')) ?>
              <?php endif; ?>
            </td>
            <td>
              <?php echo $this->locale()->toNumber($item->shipping_days) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <br>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
    )); ?>

    <?php else: ?>
    <div class="tip"><span>
    <?php echo $this->translate("STORE_No locations found."); ?>
      <?php echo $this->htmlLink('javascript:parent.Smoothbox.close()', 'close'); ?>
  </span></div>
    <?php endif; ?>
  <?php endif; ?>

</div>
