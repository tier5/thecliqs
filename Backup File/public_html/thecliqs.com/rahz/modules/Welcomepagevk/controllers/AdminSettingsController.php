<?php
/**
 * @category   Application_Extensions
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/

class Welcomepagevk_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    // with production mode don't work getSettings
        //$setting = (boolean)Engine_Api::_()->getApi('settings', 'core')->getSetting('welcomepagevk.enable');    
    // so use query
    $settingsTable = Engine_Api::_()->getDbtable('settings', 'core'); 
    $setRes = $settingsTable->fetchRow($settingsTable->select()->where("name = 'welcomepagevk.enable'"));
    $setting = $setRes['value'];

    if ($this->getRequest()->isPost()) {
      $contentTable = Engine_Api::_()->getDbtable('content', 'core'); 
      $contentTableName = $contentTable->info('name');
      if(!$setting) {
        $res = $contentTable->fetchRow(
          $contentTable->select()->where("name = 'welcomepagevk.redirect'")
        );
        if(!$res) {
          $resMiddle = $contentTable->fetchRow(
            $contentTable->select()->where("page_id = '3' AND name = 'middle'")
          );
          if($resMiddle) {
            $data = array( 
		          'page_id' => '3', 
              'type'    => 'widget', 
            	'name'    => 'welcomepagevk.redirect',
              'parent_content_id' => $resMiddle['content_id'],
              'params' => ''
          	); 
            $contentTable->insert($data);
          }
          else { $setting = false; }
        }
      }
      else {
        $contentTable->delete("page_id = '3' AND name = 'welcomepagevk.redirect'");
      }
      $setting = !$setting;
      //Engine_Api::_()->getApi('settings', 'core')->setSetting('welcomepagevk.enable', (int)$setting);
      $settingsTable->update(array('value' => (int)$setting), "name = 'welcomepagevk.enable'");
    }
    $this->view->wenable = $setting;
  }
  
}
