<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-11-04 17:07:11 taalay $
 * @author     Taalay
 */

?>

<script type="text/javascript" >
    pcs = new PageCategoriesSimple('page-categories-simple');
</script>
<?php
    $modeClass = ($this->isMultiMode) ? 'multi-mode' : 'simple-mode';
?>

<ul class="page-categories page-categories-simple">
    <li>
        <a title="<?php echo $this->translate('PAGE_All Categories')?>"
           href="javascript://"
           onclick="page_manager.setCategory(0, 0 );return false;">
            <?php echo $this->translate('PAGE_All Categories')?>
      </a>
    </li>
    <?php foreach ( $this->set as $item):?>
    <li id="pcs-set-<?php echo $item['id']; ?>-li" class="pcs-set-li <?php echo($modeClass);?>">
        <?php if(count($item['items'])<=0) continue; ?>
        <img id="set-<?php echo $item['id']; ?>-plus" class="set-plus <?php echo($modeClass);?>" onclick="pcs.toggleSet(<?php echo $item['id'];?>)" style="cursor: pointer" src="application/modules/Page/externals/images/icons/plus.gif" />
        <img id="set-<?php echo $item['id']; ?>-minus" class="set-minus <?php echo($modeClass);?>" onclick="pcs.toggleSet(<?php echo $item['id'];?>)" style="cursor: pointer; display: none;" src="application/modules/Page/externals/images/icons/minus.gif" />
        <a class="pcs-set-a page_sort_buttons"
           id="pcs-set-<?php echo $item['id'];?>-a"
           title="<?php echo $this->translate($item['caption'])?>"
           href="<?php echo $this->url(array('sort_type'=>'category_set', 'sort_value'=>$item['caption']), 'page_browse_sort')?>"
           onclick="page_manager.setCategory(<?php echo $item['id']?>, 0 );return false;">
            <?php echo $this->string()->truncate($this->translate($item['caption']), 15, '...'); ?>
        </a>
    </li>
    <?php foreach ( $item['items'] as $category):?>
    <li id="pcs-cat-<?php echo $category['cat_id'];?>-li" class="pcs-set-<?php echo $item['id']; ?>-cat-li pcs-cat-li" style="<?php if($this->isMultiMode) echo 'display: none;'; else echo 'display: block;';?>">
        <img class="cat-minus <?php echo($modeClass);?>" class="cat-minus <?php echo($modeClass);?>" style="margin-left: 10px;" src="application/modules/Page/externals/images/icons/minus_disabled.gif" />
        <a class="page_sort_buttons"
           id="pcs-cat-<?php echo $category['cat_id'];?>-a"
           title="<?php echo $this->translate($category['caption'])?>"
           href="<?php echo $this->url(array('sort_type'=>'category_name', 'sort_value'=>$category['caption']), 'page_browse_sort')?>"
           onclick="page_manager.setCategory(<?php echo $item['id']?>, <?php echo $category['cat_id']?> );return false;">
            <?php echo $this->string()->truncate($this->translate($category['caption']), 15, '...'); ?>
        </a>
    </li>
    <?php endforeach; ?>
  <?php endforeach; ?>
</ul>