<?php
/**
 * @category   Application_Extensions
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/
?>

<h2><?php echo $this->translate("Welcome VK Page ") ?></h2>

<form action="" method="post">
  <button name="submit" id="submit" type="submit">
    <?php 
      if($this->wenable) { echo $this->translate("Disable Welcome VK Page "); }
      else { echo $this->translate("Enable Welcome VK Page "); }
    ?>
  </button>
</form>
