<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<div class="store_product_populars">
  <ul>
    <?php foreach( $this->products as $product ): ?>
      <li>
        <div class='product_name'>
          <?php echo $this->htmlLink($product->getHref(), $this->string()->truncate($product->getTitle(), 23, '...')) ?>
        </div>
        <?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.icon'), array('class' => 'product_thumb')) ?>
        <div class='product_info'>
          <div class='product_date'>
            <?php echo $this->getPriceBlock($product); ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>