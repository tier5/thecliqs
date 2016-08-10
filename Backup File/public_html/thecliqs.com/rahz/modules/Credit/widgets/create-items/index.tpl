<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  02.02.12 14:54 TeaJay $
 * @author     Taalay
 */
?>

<div class="quicklinks" style="padding: 0 0 8px 0">
  <p class="credit_description"><?php echo $this->translate('CREDIT_Quick Links Description')?></p>
  <?php
    // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
  ?>
</div>