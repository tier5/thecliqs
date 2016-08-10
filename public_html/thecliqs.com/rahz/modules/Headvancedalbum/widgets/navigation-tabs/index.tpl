<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2013-01-17 14:04:44 ratbek $
 * @author     Ratbek
 */
?>

<div class="headline">
  <h2>
    <?php
      $request = Zend_Controller_Front::getInstance()->getRequest();
      if ($request->getModuleName() == 'headvancedalbum' && $request->getControllerName() == 'index' && $request->getActionName() == 'browse'){
        echo $this->translate('HEADVANCEDALBUM_Browse Albums');
      } else if ($request->getModuleName() == 'headvancedalbum' && $request->getControllerName() == 'index' && $request->getActionName() == 'index'){
        echo $this->translate('HEADVANCEDALBUM_Browse Photos');
      }
    ?>
  </h2>
  <div class="tabs">
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    <!--<div class="offer_navigation_loader hidden" id="offer_navigation_loader">
      <?php /*echo $this->htmlImage($this->baseUrl().'/application/modules/Offers/externals/images/loader.gif'); */?>
    </div>-->
  </div>
</div>