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
<?php $step = 0; $cols_per_line = 3;?>
<div class="page-categories pc-cont">
    <?php foreach ( $this->set as $item):?>
    <?php if($step % $cols_per_line == 0): ?>
    <div class="pc-set-div" style="overflow: hidden;">
        <?php endif; ?>
        <ul class="pc-set-ul">
            <li class="pc-set-li">
                <a id="pc-set-<?php echo $item['id']; ?>"
                   href="<?php echo $this->url(array('sort_type'=>'category_set', 'sort_value'=>$item['caption']), 'page_browse_sort')?>"
                   onclick="page_manager.setCategory( <?php echo $item['id']; ?> , 0);return false;">
                    <b><?php echo $item['caption'];?></b></a> <?php echo '('. intval($item['total']) .')';?>
            </li>
            <?php foreach ($item['items'] as $subCategory): ?>
            <li class="pc-cat-li" style="<?php if($subCategory['value'] == 1) echo 'display: none;';?>" >
                <?php if($subCategory['count']<=0) continue; ?>
                <img style="margin-right: 5px;" src="<?php $this->layout()->staticBaseUrl?>application/themes/clean/images/bullet.png" />
                <a class="category_<?php echo $subCategory['value']?> page_sort_buttons" style="cursor: pointer"
                   id="page_sort_category_<?php echo $subCategory['value']; ?>"
                   title="<?php echo $this->translate($subCategory['caption'])?>"
                   href="<?php echo $this->url(array('sort_type'=>'category_name', 'sort_value'=>$subCategory['caption']), 'page_browse_sort')?>"
                   onclick="page_manager.setCategory( <?php echo $subCategory['set_id']; ?> , <?php echo $subCategory['value']?>);return false;">
                    <?php echo $this->string()->truncate($this->translate($subCategory['caption']), 15, '...'); ?>
                </a>(<?php echo $subCategory['count']; ?>)
            </li>
            <?php endforeach; ?>
        </ul>
        <?php $step++; ?>
        <?php if( $step % $cols_per_line == 0 ): ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if( $step % $cols_per_line !== 0 )
        echo '</div>';
    ?>
</div>