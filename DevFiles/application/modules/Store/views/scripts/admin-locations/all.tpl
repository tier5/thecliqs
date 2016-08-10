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

<?php if (!$this->locationsOnly): ?>
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
      <?php echo $this->translate("STORE_Manage Default Shipping Locations") ?>
    </h2>

    <p>
      <?php echo $this->translate("STORE_ADMIN_LOCATIONS_DEFAULT_DESCRIPTION"); ?>
    </p>
    <br/>
    <br/>
    <script type="text/javascript">
      function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
      }

      en4.core.runonce.add(function () {
        var location = $('add-location').getElement('input[name=location]');
        var location_code = $('add-location').getElement('input[name=location_code]');
        var shipping_amt = $('add-location').getElement('input[name=shipping_amt]');
        var shipping_days = $('add-location').getElement('input[name=shipping_days]');
        var $parent_id = <?php echo $this->parent_id; ?>;

        $('post-location').addEvent('click', function () {
          var loc_value = location.get('value').trim();
          var loc_code_value = location_code.get('value').trim();
          var ship_value = shipping_amt.get('value').trim();
          var ship_days = shipping_days.get('value').trim();
          if (loc_value.length <= 0) {
            alert("<?php echo $this->translate('STORE_Please, provide valid location.')?>");
            location.focus();
            return;
          }
          if (loc_code_value.length <= 0) {
            alert("<?php echo $this->translate('STORE_Please, provide valid location code.')?>");
            location.focus();
            return;
          }

          if (ship_value.length <= 0) {
            ship_value = null;
          }
          if (ship_days.length <= 0) {
            ship_days = null;
          }

          if (ship_value != null && !isNumber(ship_value)) {
            alert("<?php echo $this->translate('STORE_Please, provide valid Default Shipping Price.')?>");
            shipping_amt.set('value', '').focus();
            return;
          }


          if (ship_value != null && !isNumber(ship_value)) {
            alert("<?php echo $this->translate('STORE_Please, provide valid Shipping Days.')?>");
            shipping_days.set('value', '').focus();
            return;
          }

          new Request.JSON({
            'url':"<?php echo $this->url(array('module'     => 'store',
                                               'controller' => 'locations',
                                               'action'     => 'add'), 'admin_default', true);?>",
            'method':'post',
            'data':{'format':'json', 'location':loc_value, 'location_code':loc_code_value, 'shipping_amt':ship_value, 'shipping_days':ship_days, 'parent_id':$parent_id},
            'onRequest':function () {
              location.set('disabled', true);
              location_code.set('disabled', true);
              shipping_amt.set('disabled', true);
              shipping_days.set('disabled', true);
              $('post-location').set('disabled', true);
              $('check-location').set('disabled', true);
              $('error-messages').addClass('hidden');
            },
            'onSuccess':function (response) {
              shipping_amt.set('disabled', false);
              shipping_days.set('disabled', false);
              location_code.set('disabled', false);
              location_code.set('value', '');
              location.set('value', '');
              location.set('disabled', false);
              location.focus();
              $('post-location').set('disabled', false);
              $('check-location').set('disabled', false);
              $('notice-messages').addClass('hidden');

              if (response.status) {
                $('only-locations').set('html', response.html);

                Smoothbox.bind($('only-locations'));
              } else {
                $('error-messages').removeClass('hidden');
                var $element = $('error-messages').getElementsByTagName('li')[0];
                $element.innerHTML = response.errorMessage;
              }
            }
          }).send();
        });

        $('check-location').addEvent('click', function() {
          var loc_value = location.get('value').trim();
          var loc_code_value = location_code.get('value').trim();
          new Request.JSON({
            'url':"<?php echo $this->url(array('module'     => 'store',
                                               'controller' => 'locations',
                                               'action'     => 'validate'), 'admin_default', true);?>",
            'method':'post',
            'data':{'format':'json', 'location':loc_value, 'location_code':loc_code_value, 'parent_id':$parent_id},
            'onRequest':function () {
              location.set('disabled', true);
              location_code.set('disabled', true);
              shipping_amt.set('disabled', true);
              shipping_days.set('disabled', true);
              $('post-location').set('disabled', true);
              $('check-location').set('disabled', true);
              $('error-messages').addClass('hidden');
              $('notice-messages').addClass('hidden');
            },
            'onSuccess':function (response) {
              shipping_amt.set('disabled', false);
              shipping_days.set('disabled', false);
              location_code.set('disabled', false);
              location_code.set('value', '');
              location.set('value', '');
              location.set('disabled', false);
              location.focus();
              $('post-location').set('disabled', false);
              $('check-location').set('disabled', false);

              if (response.status) {
                $('notice-messages').removeClass('hidden');
                var $element = $('notice-messages').getElementsByTagName('li')[0];
                $element.innerHTML = response.noticeMessage;
              } else {
                $('error-messages').removeClass('hidden');
                var $element = $('error-messages').getElementsByTagName('li')[0];
                $element.innerHTML = response.errorMessage;
              }
            }
          }).send();
        });
      });

      var locationDelete = function (location_id, location_name) {
        if (confirm("<?php echo $this->translate("STORE_Are you sure you want to delete '"); ?>" + location_name + "' ?")) {
          console.log(location_id);
        }
      }

      var supported = function (location_id, action, location) {
        if (action != 'add' && action != 'remove') return;

        if (action == 'remove') {
          if (!confirm("<?php echo $this->translate('STORE_Are you sure you want to remove the following location from supported list of locations?'); ?>" + ': ' + location))
            return;
        }

        new Request.JSON({
          'url':"<?php echo $this->url(array('module'     => 'store',
                                             'controller' => 'locations',
                                             'action'     => 'supported'), 'admin_default', true);?>",
          'method':'post',
          'data':{'format':'json', 'location_id':location_id, 'do':action},
          'onRequest':function () {
            $('add_location_' + location_id).addClass('hidden');
            $('remove_location_' + location_id).addClass('hidden');
            $('loading_location_' + location_id).removeClass('hidden');
          },
          'onSuccess':function (response) {
            $('loading_location_' + location_id).addClass('hidden');
            if (response.status) {
              if (action == 'add') {
                $('remove_location_' + location_id).removeClass('hidden');
              } else {
                $('add_location_' + location_id).removeClass('hidden');
              }
            } else {
              if (action == 'add') {
                $('add_location_' + location_id).removeClass('hidden');
              } else {
                $('remove_location_' + location_id).removeClass('hidden');
              }
            }
          }
        }).send();

      }
    </script>

    <?php if ($this->parent != null): ?>
      <div class="locations-tree">
        <span style="float:left">
          <?php echo $this->htmlLink(array(
            'reset'      => true,
            'route'      => 'admin_default',
            'module'     => 'store',
            'controller' => 'locations',
            'action'     => 'all',
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
            'route'      => 'admin_default',
            'module'     => 'store',
            'controller' => 'locations',
            'action'     => 'all',
            'parent_id'  => $location->getIdentity(),
          ), $this->truncate($location->location)); ?>
          <?php $location = $location->getParent(); ?>
          <?php } while ($location != null); ?>
        </span>
      </div>
      <br/>
    <?php endif; ?>
  </div>
</div>

<table id="add-location">
  <tr>
    <td valign="bottom">
      <?php echo $this->translate("STORE_Location Name"); ?>:<br/>
      <input name="location" type="text" size="50">
    </td>
    <td valign="bottom">
      <?php echo $this->translate("STORE_Location Code"); ?>:<br/>
      <input name="location_code" type="text" size="10">
    </td>
    <td valign="bottom">
      <?php echo $this->translate("STORE_Shipping Price"); ?>:<br/>
      <input name="shipping_amt" type="text" size="15">

          <span style="font-weight: bold; font-size: 15px">
            <?php echo $this->settings('payment.currency'); ?>
          </span>
    </td>
    <td valign="bottom">
      <?php echo $this->translate("STORE_Shipping Days"); ?>:<br/>
      <input name="shipping_days" type="text" size="15">
    </td>
    <td valign="bottom">
      <button id="post-location"><?php echo $this->translate('STORE_+Add')?></button>
    </td>
    <td valign="bottom">
      <button id="check-location"><?php echo $this->translate('STORE_Validate')?></button>
    </td>
  </tr>
</table>


<table class='admin_table'>
  <thead>
  <tr>
    <th style="width: 500px;"><?php echo $this->translate("STORE_Location Name") ?></th>
    <th class="center"><?php echo $this->translate("STORE_Location Code") ?></th>
    <?php if ($this->parent_id === 0): ?>
      <th><?php echo $this->translate("STORE_Sub-Locations") ?></th>
    <?php endif; ?>
    <th><?php echo $this->translate("STORE_Shipping Price") ?></th>
    <th><?php echo $this->translate("STORE_Shipping Days") ?></th>
    <th style="width: 11%" class="center"><?php echo $this->translate("Options") ?></th>
    <th class="center"><?php echo $this->translate("Support") ?></th>
  </tr>
  </thead>
  <tbody id="only-locations">
<?php endif; ?>

<div>
  <ul id="notice-messages" class="form-notices hidden">
    <li>

    </li>
  </ul>
  <ul id="error-messages" class="form-errors hidden">
    <li>

    </li>
  </ul>
</div>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <?php foreach ($this->paginator as $item): ?>
    <tr>
      <td class='admin_table_bold' style="width: 500px;">
        <?php echo $this->truncate($item->location, 160); ?>
      </td>
      <td class="center">
        <?php echo $item->location_code; ?>
      </td>
      <?php if ($this->parent_id === 0): ?>
        <td class="center">
          <a href="<?php echo $this->url(array(
                          'module' => 'store',
                          'controller' => 'locations',
                          'action' => 'all',
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
      <td class='center'>
        <?php echo $this->locale()->toNumber($item->shipping_days) ?>
      </td>
      <td class='center'>
        <a href="<?php echo $this->url(array('action' => 'edit', 'location_id' => $item->getIdentity())); ?>" class="smoothbox">
          <img title="<?php echo $this->translate('STORE_Edit Location') ?>" class="product-icon" src="application/modules/User/externals/images/edit.png"></a>
        <a href="<?php echo $this->url(array('action' => 'delete', 'location_id' => $item->getIdentity())); ?>" class="smoothbox">
          <img title="<?php echo $this->translate('STORE_Delete Location') ?>" class="product-icon" src="application/modules/Core/externals/images/delete.png"></a>
        <?php if ($this->parent_id === 0): ?>
          <a href="<?php echo $this->url(array(
                          'module' => 'store',
                          'controller' => 'locations',
                          'action' => 'all',
                          'parent_id' => $item->getIdentity()), 'admin_default', true); ?>">
            <img title="<?php echo $this->translate('STORE_Edit Sub-location') ?>" class="product-icon" src="application/modules/Store/externals/images/sub_location.png"></a>
        <?php endif; ?>
      </td>
      <td class="center">
        <?php if ($item->isSubLocationExists()) : ?>
          <a href="javascript:supported('<?php echo $item->getIdentity(); ?>', 'add')"
             class="st-location-item add-location <?php echo ($item->supported) ? 'hidden' : ''; ?> "
             id="add_location_<?php echo $item->getIdentity(); ?>">
            <?php echo $this->translate("Add to supported"); ?> </a>

          <div id="loading_location_<?php echo $item->getIdentity(); ?>" class="loading-location hidden">&nbsp;</div>
          <a
            href="javascript:supported('<?php echo $item->getIdentity(); ?>', 'remove', '<?php echo $item->location;?>');"
            class="st-location-item remove-location <?php echo ($item->supported) ? '' : 'hidden'; ?>"
            id="remove_location_<?php echo $item->getIdentity(); ?>">
            <?php echo $this->translate("Remove from supported"); ?> </a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php endif; ?>

<?php if (!$this->locationsOnly): ?>
  </tbody>
</table>

<br/>
<div>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'query' => $this->filterValues,
    'pageAsQuery' => true,
  )); ?>
</div>
<?php endif; ?>