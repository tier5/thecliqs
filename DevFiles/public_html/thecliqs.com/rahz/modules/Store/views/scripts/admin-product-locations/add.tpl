<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: add.tpl  4/6/12 2:00 PM mt.uulu $
 * @author     Mirlan
 */
?>

<?php if ($this->parent != null): ?>
  <div class="locations-tree" style="padding: 5px;">
  <span style="float:left">
  <?php echo $this->htmlLink(array(
    'route'      => 'admin_default',
    'module'     => 'store',
    'controller' => 'product-locations',
    'action'     => 'add',
    'product_id' => $this->product->getIdentity(),
    'format'     => 'smoothbox'
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
          'route'      => 'admin_default',
          'module'     => 'store',
          'controller' => 'product-locations',
          'action'     => 'add',
          'product_id' => $this->product->getIdentity(),
          'parent_id'  => $location->getIdentity(),
          'format'     => 'smoothbox',
        ), $this->truncate($location->location)); ?>
        <?php $location = $location->getParent(); ?>
        <?php } while ($location != null); ?>
    </span>

  </div>
  <br/>
<?php endif; ?>

<form action="<?php echo $this->url(array('action'=> 'add')); ?>" method="post">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style="width: 10px"><input type="checkbox"
           onclick="$(this).getParent('table').getElements('.add-locations').set('checked', ($(this).checked))"/>
        </th>
        <th style="width: 500px;"><?php echo $this->translate("STORE_Location Name") ?></th>
        <?php if ($this->parent_id === 0): ?>
          <th><?php echo $this->translate("STORE_Sub-Locations") ?></th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody id="only-locations">
      <?php if ($this->paginator->getTotalItemCount() > 0): ?>
        <?php foreach ($this->paginator as $item): ?>
          <tr>
            <td><input type="checkbox" name="locations[]" class="add-locations"
                       id="location_<?php echo $item->location_id; ?>" value="<?php echo $item->location_id; ?>"/></td>
            <td class='admin_table_bold' style="width: 500px;">
              <label for="location_<?php echo $item->location_id; ?>" style="display: block"><?php echo $this->truncate($item->location, 160);?></label>
            </td>
            <?php if ($this->parent_id === 0): ?>
              <td class="center">
                <a href="<?php echo $this->url(array('parent_id' => $item->location_id, 'format' => 'smoothbox')); ?>"
                  style="display: block; text-align: center">
                  <?php echo (int)$item->sub_locations; ?>
                </a>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td style="font-weight: bold; color: #e57c26">
            <?php echo $this->translate("STORE_No locations found."); ?>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <div style="padding: 5px;">
    <input type="hidden" name="format" value="smoothbox"/>
    <button type="submit"><?php echo $this->translate("STORE_Add Locations"); ?></button>
  </div>
</form>