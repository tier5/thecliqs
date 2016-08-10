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
<script type="text/javascript">
  en4.core.runonce.add(function(){
    var elements = $$('a.store_main_wish_list, a.store_main_panel, a.store_main_cart');
    elements.each(function(el){
      if($type(el) == 'element'){
        var li = el.getParent();
        if(li.get('tag') == 'li'){
          li.addClass('store_main_right')
        }
      }
    });
  });
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Store');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>