<?php $item = $this -> item?>
<div class="ynvideochannel_categories_breadcrumb">
    <?php $i = 0;  $category = $item->getCategory();
    if($category):
        foreach($category->getBreadCrumNode() as $node):
            if($node -> category_id != 1):
                if($i != 0) :?>
                    &nbsp;<i class="fa fa-angle-right"></i>&nbsp;
            <?php endif;
                $i++;
                echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array());
            endif;
        endforeach;
        if($category -> parent_id != 0 && $category -> parent_id  != 1) :?>
            &nbsp;<i class="fa fa-angle-right"></i>&nbsp;
        <?php endif;
        echo $this->htmlLink($category->getHref(), $this->translate($category->title));
    endif;?>
</div>