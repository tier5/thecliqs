<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  3/22/12 2:03 PM mt.uulu $
 * @author     Mirlan
 */
?>
<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if (count($this->navigation)): ?>
<div class='store_admin_tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<?php echo $this->render('admin/_locationsMenu.tpl'); ?>

<div class="admin_home_middle">
  <div class="settings">
    <h2>
      <?php echo $this->translate("STORE_Manage Supported Shipping Locations") ?>
    </h2>

    <p>
      <?php echo $this->translate('STORE_ADMIN_LOCATIONS_SUPPORTED_DESCRIPTION'); ?>
    </p>
    <br/>
    <br/>


    <?php if ($this->parent != null): ?>
    <div class="locations-tree">
      <span style="float:left">
      <?php echo $this->htmlLink(array(
        'reset' => true,
        'route' => 'admin_default',
        'module' => 'store',
        'controller' => 'locations',
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
          'route' => 'admin_default',
          'module' => 'store',
          'controller' => 'locations',
          'parent_id' => $location->getIdentity(),
        ), $this->truncate($location->location)); ?>
        <?php $location = $location->getParent(); ?>
        <?php } while ($location != null); ?>
      </span>

    </div>
    <br/>
    <br/>
    <?php endif; ?>

    <?php if ($this->paginator->count() > 0): ?>

      <table class='admin_table'>
        <thead>
        <tr>
          <th style="width: 500px;"><?php echo $this->translate("STORE_Location Name") ?></th>
          <?php if( $this->parent_id == 0): ?>
          <th><?php echo $this->translate("STORE_Sub-Locations") ?></th>
          <?php endif; ?>
          <th><?php echo $this->translate("STORE_Shipping Price") ?></th>
          <th><?php echo $this->translate("STORE_Shipping Days") ?></th>
          <th class="center"><?php echo $this->translate("Options") ?></th>
        </tr>
        </thead>
        <tbody id="only-locations">
          <?php foreach ($this->paginator as $item): ?>
            <tr>
              <td class='admin_table_bold' style="width: 500px;">
                <?php echo $this->truncate($item->location, 160); ?>
              </td>
              <?php if( $this->parent_id == 0): ?>
                <td class="center">
                  <a href="<?php echo $this->url(array(
                                  'module' => 'store',
                                  'controller' => 'locations',
                                  'action' => 'index',
                                  'parent_id' => $item->getIdentity()), 'admin_default', true); ?>"><?php echo (int)$item->sub_locations; ?></a>
                </td>
              <?php endif; ?>
              <td class="center">
                <?php if (is_null($item->shipping_amt)): ?>
                  <?php echo $this->translate('STORE_Only Sub-Locations'); ?>
                <?php else: ?>
                  <span class="store-price"><?php echo $this->locale()->toCurrency($item->shipping_amt, $this->settings('payment.currency', 'USD')) ?></span>
                <?php endif; ?>
              </td>
              <td class="center">
                <?php echo $this->locale()->toNumber($item->shipping_days) ?>
              </td>
              <td class='center'>
                <a href="<?php echo $this->url(array('action' => 'edit-supported', 'location_id' => $item->getIdentity())); ?>" class="smoothbox">
                  <img title="<?php echo $this->translate('STORE_Edit Location') ?>" class="product-icon" src="application/modules/User/externals/images/edit.png"></a>
                <a href="<?php echo $this->url(array('action' => 'remove-supported', 'location_id' => $item->getIdentity())); ?>" class="smoothbox">
                  <img title="<?php echo $this->translate('STORE_Delete Location') ?>" class="product-icon" src="application/modules/Core/externals/images/delete.png"></a>
                <?php if ($this->parent_id === 0): ?>
                  <a href="<?php echo $this->url(array(
                                  'module' => 'store',
                                  'controller' => 'locations',
                                  'action' => 'index',
                                  'parent_id' => $item->getIdentity()), 'admin_default', true); ?>">
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
      <div style="font-weight: bold; color: #e57c26">
        <?php echo $this->translate("STORE_No locations found."); ?>
      </div>
    <?php endif; ?>

  </div>
</div>