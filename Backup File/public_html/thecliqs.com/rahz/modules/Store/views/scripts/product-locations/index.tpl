<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  4/16/12 3:15 PM mt.uulu $
 * @author     Mirlan
 */
?>

<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('STORE_Manage Products');?></h2>

  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>

<div class="he-items" style="float: right; margin: 30px 0">
  <ul class="he-item-list">
    <li>
      <div class="he-item-options">
        <?php echo $this->htmlLink(
        $this->url(array('action'     => 'edit',
                         'page_id'    => $this->page->getIdentity(),
                         'product_id' => $this->product->getIdentity()), 'store_products'),
        $this->translate('Back'), array(
          'class' => 'buttonlink product_back',
          'id'    => 'store_product_editsettings',
        )); ?>
        <br>
        <?php echo $this->htmlLink(
        $this->url(array(
          'action'     => 'add',
          'product_id' => $this->product->getIdentity(),
          'parent_id'  => $this->parent_id
        ), 'store_product_locations', true),
        $this->translate('STORE_Add Locations'), array(
          'class' => 'buttonlink product_shipping_locations smoothbox',
          'id'    => 'store_product_addlocations',
        )); ?>
      </div>
    </li>
  </ul>
</div>

<div class="settings">
  <h2>
    <?php echo $this->htmlLink($this->product->getHref(), $this->product->getTitle(), array('target' => '_blank')); ?>
  </h2>

  <p>
    <?php echo $this->translate('STORE_PRODUCT_MANAGE_SHIPPING_LOCATIONS_SETTINGS'); ?>
  </p>
  <br/>

  <?php if ($this->parent != null): ?>
    <div class="locations-tree">
      <span style="float:left">
        <?php echo $this->htmlLink(array(
          'reset'      => true,
          'route'      => 'store_product_locations',
          'product_id' => $this->product->getIdentity(),
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
          'reset'      => true,
          'route'      => 'store_product_locations',
          'parent_id'  => $location->getIdentity(),
          'product_id' => $this->product->getIdentity(),
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
        <th class="center"><?php echo $this->translate("STORE_Shipping Price") ?></th>
        <th class="center"><?php echo $this->translate("STORE_Shipping Days") ?></th>
        <th class="center"><?php echo $this->translate("Options") ?></th>
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
            <a href="<?php echo $this->url(array('parent_id' => $item->getIdentity())); ?>"><?php echo (int)$item->sub_locations; ?></a>
          </td>
        <?php endif; ?>
        <td class="center">
          <?php if (is_null($item->shipping_amt)): ?>
          <?php echo $this->translate('STORE_Only Sub-Locations'); ?>
          <?php else: ?>
          <?php echo $this->locale()->toCurrency($item->shipping_amt, $this->settings('payment.currency', 'USD')) ?>
          <?php endif; ?>
        </td>
        <td class="center">
          <?php echo $this->locale()->toNumber($item->shipping_days) ?>
        </td>
        <td class='center'>
          <a href="<?php echo $this->url(array('action' => 'edit', 'location_id' => $item->getIdentity())); ?>" class="smoothbox">
            <img title="<?php echo $this->translate('STORE_Edit Location') ?>" class="product-icon" src="application/modules/User/externals/images/edit.png"></a>
          <a href="<?php echo $this->url(array('action' => 'remove', 'location_id' => $item->getIdentity())); ?>" class="smoothbox">
            <img title="<?php echo $this->translate('STORE_Delete Location') ?>" class="product-icon" src="application/modules/Core/externals/images/delete.png"></a>
          <?php if ($this->parent_id === 0): ?>
            <a href="<?php echo $this->url(array('parent_id' => $item->getIdentity())); ?>">
              <img title="<?php echo $this->translate('STORE_Edit Sub-location') ?>" class="product-icon" src="application/modules/Store/externals/images/sub_location.png"></a>
          <?php endif; ?>
        </td>
      </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <br/>
    <div>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'query' => $this->filterValues,
        'pageAsQuery' => true,
      )); ?>
    </div>

  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("STORE_No locations have been added for the product. %1s", $this->htmlLink(
        $this->url(array(
          'action'     => 'add',
          'product_id' => $this->product->getIdentity(),
          'parent_id'  => $this->parent_id
        ), 'store_product_locations', true),
        $this->translate('STORE_Add Locations'), array('class' => 'smoothbox',))); ?>
      </span>
    </div>
  <?php endif; ?>
</div>