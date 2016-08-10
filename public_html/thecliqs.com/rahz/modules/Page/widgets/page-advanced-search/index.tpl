<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
?>

<script type="text/javascript">

  var myFx1, myFx2;
  window.addEvent('domready', function(){
    myFx1 = new Fx.Slide($('fieldset-adv_search_location')).hide();
    myFx2 = new Fx.Slide($('fieldset-adv_search_pagetype')).hide();

    $('page_advanced_search_option').addEvent('click', function(){
      myFx1.toggle();
      myFx2.toggle();
    });

    $('adv_submit').addEvent('click', function(e) {
      e.stop();
      page_manager.page_num = 1;
      page_manager.setAdvSearch(1);
    });
  });
</script>

<?php
  echo $this->advSearch->render($this);
?>