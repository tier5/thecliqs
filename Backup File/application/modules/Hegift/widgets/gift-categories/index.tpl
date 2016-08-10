<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  08.03.12 12:41 TeaJay $
 * @author     Taalay
 */
?>

<ul class="gift-categories">
  <?php if ( count( $this->categories ) > 1): ?>
    <li class="active" id="category-0">
      <a title="<?php echo $this->translate("HEGIFT_All Categories")?>"
         href="javascript:void(0)"
         onclick="gift_manager.setCategory(0);">
        <?php echo $this->string()->truncate($this->translate("HEGIFT_All Categories"), 15, '...'); ?>
      </a>
    </li>
  <?php endif; ?>

  <?php foreach ( $this->categories as $category):?>
    <li id="category-<?php echo $category['category_id']; ?>">
      <?php if($category['count']<=0) continue; ?>
        <a title="<?php echo $this->translate($category['title'])?>"
           href="javascript:void(0)"
           onclick="gift_manager.setCategory(<?php echo $category['category_id']?>);">
             <?php echo $this->string()->truncate($this->translate($category['title']), 15, '...'); ?>
        </a>(<?php echo $this->locale()->toNumber($category['count']); ?>)
    </li>
  <?php endforeach; ?>
</ul>