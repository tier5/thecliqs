<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _storeEditMenu.tpl  4/16/12 2:17 PM mt.uulu $
 * @author     Mirlan
 */
?>

<div class="he-items" style="float: right; margin: 30px 0">
  <ul class="he-item-list">
    <li>
      <div class="he-item-options">
        <?php if ($this->product->isDigital()) : ?>
        <?php echo $this->htmlLink($this->url(array('controller' => 'digital', 'action' => 'edit-file', 'product_id' => $this->product->getIdentity()), 'store_extended'), $this->translate('STORE_Manage File'), array(
          'class' => 'buttonlink product_file_manage',
          'id' => 'store_product_editfile',
        )) ?>
        <br>
        <?php else: ?>
        <?php echo $this->htmlLink($this->url(array('product_id' => $this->product->getIdentity()), 'store_product_locations'), $this->translate('STORE_Manage Shipping Locations'), array(
          'class' => 'buttonlink product_shipping_locations',
          'id' => 'store_product_locations',
        )) ?>
        <br>
        <?php endif; ?>
        <?php echo $this->htmlLink($this->url(array('controller' => 'photo', 'action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_extended'), $this->translate('STORE_Manage photos'), array(
          'class' => 'buttonlink product_photos_manage',
          'id' => 'store_product_editphotos',
        )) ?>
        <br>
        <?php echo $this->htmlLink($this->url(array('controller' => 'video', 'action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_extended'), $this->translate('STORE_Manage video'), array(
          'class' => 'buttonlink product_video_manage',
          'id' => 'store_product_editvideo',
        )) ?>
        <br>
        <?php echo $this->htmlLink($this->url(array('controller' => 'audios', 'action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_extended'), $this->translate('STORE_Manage audios'), array(
          'class' => 'buttonlink product_audios_manage',
          'id' => 'store_product_editaudios',
        )) ?>
        <br>
      </div>
    </li>
  </ul>
</div>