<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2013-01-21 15:31 ratbek $
 * @author     Ratbek
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate("HEADVANCEDALBUM_My Albums"); ?>
  </h2>
  <div class="tabs">
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    <!--<div class="offer_navigation_loader hidden" id="offer_navigation_loader">
      <?php /*echo $this->htmlImage($this->baseUrl().'/application/modules/Offers/externals/images/loader.gif'); */?>
    </div>-->
  </div>
</div>

