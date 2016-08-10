<?php
    $session = new Zend_Session_Namespace('mobile');
?>

<div class="generic_list_widget">
    <ul class="ynjobposting-industry">
        <?php foreach ($this->industries as $industry) : ?>
            <li value ='<?php echo $industry->getIdentity() ?>' class="ynjobposting-industry_row <?php if ($industry->parent_id > 1) echo 'ynjobposting-industry-sub-industry child_'.$industry->parent_id.' level_'.$industry->level?>">
                <div <?php  $request = Zend_Controller_Front::getInstance()->getRequest(); 
                if($request-> getParam('industry') == $industry -> industry_id) echo 'class = "active"';?>>
                    <?php if(count($industry->getChildList()) > 0 && !$session-> mobile) : ?>
                        <div class="ynjobposting-industry-collapse-control ynjobposting-industry-collapsed"></div>
                    <?php else : ?>
                        <div class="ynjobposting-industry-collapse-nocontrol"></div>
                    <?php endif; ?>

                    <?php 
                        echo $this->htmlLink(
                                $industry->getHref($this->type), 
                                $this->string()->truncate($this->translate($industry->title), 20),
                                array('title' => $this->translate($industry->title)));
                    ?>                    
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
