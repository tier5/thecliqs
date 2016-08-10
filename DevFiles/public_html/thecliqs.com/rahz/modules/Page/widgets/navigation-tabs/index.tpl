<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-11-04 15:29:11 taalay $
 * @author     Taalay
 */
?>
<div class="headline">
  <h2>
    <?php echo $this->translate('HE_Pages');?>
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
      <div class="page_navigation_loader hidden" id="page_navigation_loader">
      <?php echo $this->htmlImage($this->baseUrl().'/application/modules/Page/externals/images/loader.gif'); ?>
    </div>
    </div>
  <?php endif; ?>
</div>