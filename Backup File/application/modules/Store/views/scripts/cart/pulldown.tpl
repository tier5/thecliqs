<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: pulldown.tpl  5/21/12 12:19 PM mt.uulu $
 * @author     Mirlan
 */
?>
<?php
/**
 * @var $item Store_Model_Cartitem
 */
if ($this->items->count() <= 0): ?>
<li class="notifications_unread">
    <span class="notification_item_general">
      <?php echo $this->translate('STORE_Your cart is empty');  ?>
    </span>
</li>
<?php else:
  foreach ($this->items as $item):
    if (null != ($product = $item->getProduct())):
      ?>
    <li class="notifications_unread">
    <span class="notification_item_general">
      <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.icon')) ?>
      <?php echo $product->__toString() ?>
      <?php if( !$product->isDigital() ): ?>
      <span><?php echo $this->translate(array('(%s item)','(%s items)', $item->qty), $this->locale()->toNumber($item->qty)); ?></span>
      <?php endif;?>
    </span>
    </li>
    <?php endif;
  endforeach; ?>
<?php endif; ?>