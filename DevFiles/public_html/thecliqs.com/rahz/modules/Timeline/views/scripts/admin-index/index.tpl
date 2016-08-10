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
<h2><?php echo $this->translate("Timeline Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
<div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>