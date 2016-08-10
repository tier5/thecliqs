<?php
    $session = new Zend_Session_Namespace('mobile');
?>

<div class="generic_list_widget">
    <ul class="ymb_menuRight_wapper ynbusinesspages-categories">
        <?php foreach ($this->categories as $category) : ?>
            <li value ='<?php echo $category->getIdentity() ?>' class="ynbusinesspages-category_row <?php if ($category->parent_id > 1) echo 'ynbusinesspages-category-sub-category child_'.$category->parent_id.' level_'.$category->level?>">
                <div <?php  $request = Zend_Controller_Front::getInstance()->getRequest(); 
                if($request-> getParam('category') == $category -> category_id) echo 'class = "active"';?>>
                    <?php if(count($category->getChildList()) > 0 && !$session-> mobile) : ?>
                        <div class="ynbusinesspages-category-collapse-control ynbusinesspages-category-collapsed"></div>
                    <?php else : ?>
                        <div class="ynbusinesspages-category-collapse-nocontrol"></div>
                    <?php endif; ?>

                    <?php 
                        echo $this->htmlLink(
                                $category->getHref(), 
                                $this->string()->truncate($category->getTitle(), 20),
                                array('title' => $category->getTitle()));
                    ?>                    
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php if($session -> mobile): ?>
<script type="text/javascript">
    var btn_mobile_category = new Element('div', {html:'<a onclick="toggleOpenMenuRight(this);" class="ynmb_openMenuRight_btn ynmb_sortBtn_btn ynmb_touchable ynmb_Categories_icon ynmb_a_btnStyle" href="javascript: void(0);"><span class="ynmb_openMenuRight"><span><?php echo $this->translate("Categories")?></span></span></a>'}) ;
    btn_mobile_category.addClass('ynmb_sortBtn_actionSheet');
    $$('.ynmb_sortBtn_Wrapper')[0].grab( btn_mobile_category );
</script>
<?php endif; ?>