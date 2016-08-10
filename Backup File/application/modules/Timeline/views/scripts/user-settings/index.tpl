<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

?>
<div class="headline">
  <h2>
    <h2>
      <?php if ($this->viewer->isSelf($this->user)):?>
        <?php echo $this->translate('My Settings');?>
      <?php else:?>
        <?php echo $this->translate('%1$s\'s Settings', $this->htmlLink($this->user->getHref(), $this->user->getTitle()));?>
      <?php endif;?>
    </h2>

  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class="global_form">
  <?php echo $this->form->render($this) ?>
</div>