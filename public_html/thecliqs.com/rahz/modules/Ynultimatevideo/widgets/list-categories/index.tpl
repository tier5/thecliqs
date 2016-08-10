<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
    $session = new Zend_Session_Namespace('mobile');
?>

<?php $request = Zend_Controller_Front::getInstance()->getRequest(); ?>
<div class="generic_list_widget">
    <ul class="ymb_menuRight_wapper ynultimatevideo-category">
        <?php foreach ($this->categories as $category) : ?>
        <li value ='<?php echo $category->getIdentity() ?>' class="ynultimatevideo-category_row <?php if ($category->parent_id > 1) echo 'ynultimatevideo-category-sub-category child_'.$category->parent_id.' level_'.$category->level?>">
            <div class="<?php if($request-> getParam('category') == $category -> option_id) echo 'active';?>">
                <?php if(count($category->getChildList()) > 0 && !$session-> mobile) : ?>
                    <div class="ynultimatevideo-category-collapse-control ynultimatevideo-category-collapsed"><i class="fa fa-caret-square-o-right"></i></div>
                <?php else : ?>
                    <div class="ynultimatevideo-category-collapse-nocontrol"></div>
                <?php endif; ?>

                <?php
                    echo $this->htmlLink(
                    $category->getHref(),
                    $this->string()->truncate($this->translate($category->title), 20),
                    array('title' => $this->translate($category->title)));
                ?>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>