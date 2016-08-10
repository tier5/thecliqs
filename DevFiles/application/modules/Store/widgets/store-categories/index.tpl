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
<ul class="store-simple-list">
		<?php if ( count( $this->categories ) > 1): ?>
		<li>
      <a class="store_sort_buttons" id="store_all_categories" href="javascript:store_manager.setCategory(0);"><?php echo $this->translate("STORE_All Categories"); ?></a>
		</li>
		<?php endif; ?>

		<?php foreach ( $this->categories as $key=>$category):?>
			<li>
        <a class="category_<?php echo $category['value']?> store_sort_buttons" id="store_sort_popular" href="javascript:store_manager.setCategory(<?php echo $category['value']?>);">
          <?php echo $this->string()->truncate($this->translate($category['category']), 17, '...'); ?>
        </a>(<?php echo $this->locale()->toNumber($category['count']); ?>)
			</li>
		<?php endforeach; ?>
</ul>