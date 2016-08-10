<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  4/12/12 4:05 PM mt.uulu $
 * @author     Mirlan
 */
?>

<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('Settings');?></h2>

  <div class="tabs">
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
</div>
<div class="clr"></div>

<div class="settings">
  <p>
    <?php echo $this->translate('STORE_PAGE_LOCATIONS_SUPPORTED_DESCRIPTION'); ?>
  </p>
  <br/>
  <br/>
  <?php if ($this->count > 0): ?>
  <a href="<?php echo $this->url(array('action' => 'add'))?>" style="float: right; padding: 10px; text-decoration: none"
     class="smoothbox">
    <button>
      <?php echo $this->translate("STORE_Add Locations"); ?>
    </button>
  </a>
  <?php endif; ?>
  <?php if ($this->parent != null): ?>
  <div class="locations-tree">
      <span style="float:left">
        <?php echo $this->htmlLink(array(
        'reset' => true,
        'route' => 'store_settings',
        'controller' => 'locations',
        'page_id' => $this->page->page_id,
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
        'reset' => true,
        'route' => 'store_settings',
        'controller' => 'locations',
        'page_id' => $this->page->page_id,
        'parent_id' => $location->getIdentity(),
      ), $this->truncate($location->location)); ?>
      <?php $location = $location->getParent(); ?>
      <?php } while ($location != null); ?>
  </span>
  </div>
  <br/><br/>
  <?php endif; ?>

  <?php if ($this->count > 0): ?>
  <table class='store-product-list locations'>
    <thead>
    <tr>
      <th style="width: 400px;"><?php echo $this->translate("STORE_Location Name") ?></th>
      <?php if ($this->parent_id === 0): ?>
        <th class="center"><?php echo $this->translate("STORE_Sub-Locations") ?></th>
      <?php endif; ?>
      <th class="center"><?php echo $this->translate("STORE_Shipping Price") ?></th>
      <th class="center"><?php echo $this->translate("STORE_Shipping Days") ?></th>
      <th class="center"><?php echo $this->translate("Options") ?></th>
    </tr>
    </thead>
    <tbody id="only-locations">
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td class='admin_table_bold' style="width: 400px;">
            <?php echo $this->truncate($item->location, 160); ?>
          </td>
          <?php if ($this->parent_id === 0): ?>
            <td class="center">
              <a href="<?php echo $this->url(array('controller' => 'locations',
                              'page_id' => $this->page->page_id,
                              'parent_id' => $item->getIdentity()), 'store_settings', true); ?>"><?php echo (int)$item->sub_locations; ?></a>
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
          <td class="center">
            <a href="<?php echo $this->url(array('action' => 'edit', 'location_id' => $item->getIdentity())); ?>" class="smoothbox">
              <img title="<?php echo $this->translate('STORE_Edit Location') ?>" class="product-icon" src="application/modules/User/externals/images/edit.png"></a>
            <a href="<?php echo $this->url(array('action' => 'remove', 'location_id' => $item->getIdentity())); ?>" class="smoothbox">
              <img title="<?php echo $this->translate('STORE_Delete Location') ?>" class="product-icon" src="application/modules/Core/externals/images/delete.png"></a>
            <?php if ($this->parent_id === 0): ?>
              <a href="<?php echo $this->url(array('controller' => 'locations',
                              'page_id' => $this->page->page_id,
                              'parent_id' => $item->getIdentity()), 'store_settings', true); ?>">
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
        <?php echo $this->translate("STORE_No default locations have been added to your store. %1s", $this->htmlLink(
        $this->url(array(
          'action' => 'add'
        )),
        $this->translate('STORE_Add Locations'), array('class' => 'smoothbox',))); ?>
      </span>
  </div>
  <?php endif; ?>

</div>