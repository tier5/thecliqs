<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2012-03-04 15:33 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_Plugin_Core extends Zend_Controller_Plugin_Abstract
{

  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    $enabledModuleWall = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('wall');
    $enabledModuleLike  = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like');
    $moduleName = $request->getModuleName();
    $controllerName = $request->getControllerName();
    $actionName = $request->getActionName();

    if ($enabledModuleWall) {
      if ($moduleName == 'wall' && $controllerName == 'tips' && $actionName == 'index') {
        $request->setModuleName('hetips');
        $request->setControllerName('index');
        $request->setActionName('index');
      }
    }

    if ($enabledModuleLike) {
      if ($moduleName == 'like' && $controllerName == 'index' && $actionName == 'show-matches') {
          $request->setModuleName('hetips');
          $request->setControllerName('index');
          $request->setActionName('show-matches');
      }
    }
  }

  public function onRenderLayoutDefault($event)
  {
    $view = $event->getPayload();

    $profileurl_enabled = (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('profileurls')) ? 1 : 0;

    $script = <<<EOL
    window.addEvent('load', function()
    {
      (function(){
        Hetips.profileUrlEnabled = $profileurl_enabled;
        Hetips.attach();
      }).periodical(2000);
    });
EOL;

    $view->headTranslate(array('HETIPS_LOADING'));

    $view->headScript()->appendScript($script);
    $view->headScript()->appendFile($view->layout()->staticBaseUrl.'application/modules/Hetips/externals/scripts/core.js');

  }
}