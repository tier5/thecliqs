<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ModuleController.php 2010-07-02 19:52 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_ModuleController extends Core_Controller_Action_Admin
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('license', 'json')
            ->addActionContext('edit', 'json')
            ->initContext();
    }

    public function licenseAction()
    {
		return;
    }

    public function editAction()
    {
		return;
    }

    public function upgradeAction()
    {
		return;
    }

    public function apptouchAction()
    {
        $step = $this->_getParam('step');
        switch ($step) {
            case 'disable_touch':
                $db = Engine_Api::_()->getDbTable('modules', 'core');
                $module = $db->getModule('touch');
                if ($module) {
                    $module->enabled = 0;
                    $module->save();
                }
                $this->view->redirect = true;
                break;
            case 'import_settings':
                if ($this->_getParam('import')){
                    $this->importSettings();
                    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('apptouch.settings.import', true);
                } else {
                    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('apptouch.settings.import', false);
                }
                $this->view->redirect = true;
                break;
            default:
                $this->view->redirect = false;
                break;
        }
    }

    private function importSettings()
    {
        $settingsDb = Engine_Api::_()->getDbTable('settings', 'core');

        // general settings
      $settings = array();
      $settings['apptouch.default'] = $settingsDb->getSetting('touch.default');
//      $settings['apptouch.integrations.only'] = $settingsDb->getSetting('touch.integrations.only'); todo this setting is unused
      $settings['apptouch.include.tablets'] = $settingsDb->getSetting('touch.include.tablets');
      $settings['apptouch.homescreen.extension'] = $settingsDb->getSetting('touch.homescreen.extension');
      $settings['apptouch.homescreen.enabled'] = $settingsDb->getSetting('touch.homescreen.enabled');
      foreach($settings as $key => $val){
        if($val !== null)
          $settingsDb->setSetting($key, $val);
      }
        // icon settings
        $apptouch_path = DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'apptouch' . DIRECTORY_SEPARATOR . 'homescreen';
        $has_app_dir = true;
        if (!is_dir(APPLICATION_PATH . '/public/apptouch/')) {
            $has_app_dir = mkdir(APPLICATION_PATH . '/public/apptouch/');
        }
        if ($has_app_dir && !is_dir(APPLICATION_PATH . '/public/apptouch/homescreen/')) {
            $has_app_dir = mkdir(APPLICATION_PATH . '/public/apptouch/homescreen/');
        }


        $touch_path = DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'touch' . DIRECTORY_SEPARATOR . 'homescreen';
        // Check if folder exists and is writable
        $has_dir = true;

        // Creating touch folder if not exists
        if (!is_dir(APPLICATION_PATH . '/public/touch/')) {
            $has_dir = mkdir(APPLICATION_PATH . '/public/touch/');
        }
        // Creating homescreen folder if not exists
        if ($has_dir && !is_dir(APPLICATION_PATH . '/public/touch/homescreen/')) {
            $has_dir = mkdir(APPLICATION_PATH . '/public/touch/homescreen/');
        }
        if (!($has_app_dir && $has_dir)) {
            return;
        }
        $this->copy_files(APPLICATION_PATH . '/' . $touch_path, APPLICATION_PATH . '/' . $apptouch_path);

    }

    private function copy_files($src, $dest)
    {
        $files = scandir($src);
        if(!empty($files)) {
            foreach($files as $file) {
                if(is_file($src . '/' . $file)) {
                    copy($src . '/' . $file, $dest . '/' . $file);
                }
            }
        }
    }

    private function dircpy($basePath, $source, $dest, $overwrite = false)
    {
        if (!is_dir($basePath . $dest)) //Lets just make sure our new folder is already created. Alright so its not efficient to check each time... bite me
            mkdir($basePath . $dest);
        if ($handle = opendir($basePath . $source)) { // if the folder exploration is sucsessful, continue
            while (false !== ($file = readdir($handle))) { // as long as storing the next file to $file is successful, continue
                if ($file != '.' && $file != '..') {
                    $path = $source . '/' . $file;
                    if (is_file($basePath . $path)) {
                        if (!is_file($basePath . $dest . '/' . $file) || $overwrite)
                            if (!@copy($basePath . $path, $basePath . $dest . '/' . $file)) {
                                echo '<font color="red">File (' . $path . ') could not be copied, likely a permissions problem.</font>';
                            }
                    } elseif (is_dir($basePath . $path)) {
                        if (!is_dir($basePath . $dest . '/' . $file))
                            mkdir($basePath . $dest . '/' . $file); // make subdirectory before subdirectory is copied
                        $this->dircpy($basePath, $path, $dest . '/' . $file, $overwrite); //recurse!
                    }
                }
            }
            closedir($handle);
        }
    }
}