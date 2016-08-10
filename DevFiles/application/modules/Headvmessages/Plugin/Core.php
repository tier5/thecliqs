<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advnotifications
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Core.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Advnotifications
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Headvmessages_Plugin_Core
{
  public function onRenderLayoutDefault($event)
  {
    $view = $event->getPayload();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $auth_table = Engine_Api::_()->getDbTable('permissions', 'authorization');
    $viewer = Engine_Api::_()->user()->getViewer();

    $isAllow = ((int)$settings->__get('headvmessages.enabled', 0)) && $auth_table->isAllowed('headvmessages', $viewer, 'use');
    $allowEnter = (int)$settings->__get('headvmessages.enter.send.enabled', 0);
    $allowSmiles = Engine_Api::_()->headvmessages()->allowSmiles();

    if (!$isAllow) {
      return;
    }

    if ($view instanceof Zend_View) {
      $url = $view->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'message' => true), 'default', true);
      $script = <<<SCRIPT
window.addEvent('load', function (){
  headvmessagesCore.allowSmiles = {$allowSmiles};
  headvmessagesCore.allowEnter = {$allowEnter};
  headvmessagesCore.prepare();
  headvmessagesCore.friendsSuggestUrl = '{$url}';
});
console.log('after load');
SCRIPT;

      $front = Zend_Controller_Front::getInstance();
      $module = $front->getRequest()->getModuleName();

      if ($module != 'messages') {
        $view->headScript()
          ->appendFile($view->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . (APPLICATION_ENV != 'development' ? '.min' : '') . '.js')
          ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js')
          ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Album/externals/scripts/composer_photo.js')
          ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer_link.js')
          ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Music/externals/scripts/composer_music.js')
          ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Video/externals/scripts/composer_video.js')
          ->appendFile($view->layout()->staticBaseUrl . 'externals/fancyupload/Swiff.Uploader.js')
          ->appendFile($view->layout()->staticBaseUrl . 'externals/fancyupload/Fx.ProgressBar.js')
          ->appendFile($view->layout()->staticBaseUrl . 'externals/fancyupload/FancyUpload2.js');

        $view->headLink()
          ->appendStylesheet($view->layout()->staticBaseUrl . 'externals/fancyupload/fancyupload.css');

        if (APPLICATION_ENV == 'production') {
          $view->headScript()
            ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
        } else {
          $view->headScript()
            ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
            ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
            ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
            ->appendFile($view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
        }
      }

      $view->headScript()->appendFile('application/modules/Headvmessages/externals/scripts/core.js')
        ->appendScript($script);
    }
    $view->headTranslate(array('HEADVMESSAGES_No active dialogs'));
  }
}
