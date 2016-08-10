<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Installer extends Engine_Package_Installer_Module {

    function onPreinstall() {
        $db = $this->getDb();

        $getErrorMsg = $this->_getVersion();
        if (!empty($getErrorMsg)) {
            return $this->_error($getErrorMsg);
        }

        $PRODUCT_TYPE = 'spectacular';
        $PLUGIN_TITLE = 'Spectacular';
        $PLUGIN_VERSION = '4.8.9p1';
        $PLUGIN_CATEGORY = 'plugin';
        $PRODUCT_DESCRIPTION = 'Responsive Spectacular Theme';
        $PRODUCT_TITLE = 'Responsive Spectacular Theme';
        $_PRODUCT_FINAL_FILE = 0;
        $SocialEngineAddOns_version = '4.8.9p12';
        $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
        $is_file = file_exists($file_path);
        if (empty($is_file)) {
            include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
        } else {
            $db = $this->getDb();
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
            $is_Mod = $select->query()->fetchObject();
            if (empty($is_Mod)) {
                include_once $file_path;
            }
        }

        parent::onPreinstall();
    }

    public function onInstall() {
        $db = $this->getDb();
        $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='spectacular';");

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'spectacular');
        $is_spectacular_object = $select->query()->fetchObject();

        if (!empty($is_spectacular_object)) {
            $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`)
        VALUES 
        ("spectacular_admin_layout_index", "spectacular", "Layout Settings", "", \'{"route":"admin_default","module":"spectacular","controller":"layout", "action": "index"}\', "spectacular_admin_main", "", 1, 0, 2)
        ');
        }

        $this->_createCustomizationFileInTheme("spectacular");

        parent::onInstall();
    }

    private function _createCustomizationFileInTheme($themeName) {
        $global_directory_name = APPLICATION_PATH . '/application/themes/' . $themeName;
        @chmod($global_directory_name, 0777);

        if (!is_readable($global_directory_name)) {
            return $this->_error("<span style='color:red'>Note: You do not have readable permission on the path below, please give 'chmod 777 recursive permission' on it to continue with the installation process : <br /> 
  Path Name: <b>" . $global_directory_name . "</b></span>");
        }

        $global_settings_file = $global_directory_name . '/customization.css';
        $is_file_exist = @file_exists($global_settings_file);
        if (empty($is_file_exist)) {
            @chmod($global_directory_name, 0777);
            if (!is_writable($global_directory_name)) {
                return $this->_error("<span style='color:red'>Note: You do not have writable permission on the path below, please give 'chmod 777 recursive permission' on it to continue with the installation process : <br /> 
  Path Name: " . $global_directory_name . "</span>");
            }

            $fh = @fopen($global_settings_file, 'w');
            @fwrite($fh, '/* ADD CUSTOM STYLE */');
            @fclose($fh);

            @chmod($global_settings_file, 0777);
        }
    }

    private function _getVersion() {

        $db = $this->getDb();

        $errorMsg = '';
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

        $modArray = array(
            'siteevent' => '4.8.8p3',
            'siteeventticket' => '4.8.8p3',
            'sitecontentcoverphoto' => '4.8.8p5',
            'siteusercoverphoto' => '4.8.8p4',
            'sitereview' => '4.8.8p1',
            'sitereviewlistingtype' => '4.8.8p1',
            'sitealbum' => '4.8.8p1',
            'sitemenu' => '4.8.9'
        );

        $finalModules = array();
        foreach ($modArray as $key => $value) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_core_modules')
                    ->where('name = ?', "$key")
                    ->where('enabled = ?', 1);
            $isModEnabled = $select->query()->fetchObject();
            if (!empty($isModEnabled)) {
                $select = new Zend_Db_Select($db);
                $select->from('engine4_core_modules', array('title', 'version'))
                        ->where('name = ?', "$key")
                        ->where('enabled = ?', 1);
                $getModVersion = $select->query()->fetchObject();

                $running_version = $getModVersion->version;
                $product_version = $value;
                $shouldUpgrade = false;
                if (!empty($running_version) && !empty($product_version)) {
                    $temp_running_verion_2 = $temp_product_verion_2 = 0;
                    if (strstr($product_version, "p")) {
                        $temp_starting_product_version_array = @explode("p", $product_version);
                        $temp_product_verion_1 = $temp_starting_product_version_array[0];
                        $temp_product_verion_2 = $temp_starting_product_version_array[1];
                    } else {
                        $temp_product_verion_1 = $product_version;
                    }
                    $temp_product_verion_1 = @str_replace(".", "", $temp_product_verion_1);


                    if (strstr($running_version, "p")) {
                        $temp_starting_running_version_array = @explode("p", $running_version);
                        $temp_running_verion_1 = $temp_starting_running_version_array[0];
                        $temp_running_verion_2 = $temp_starting_running_version_array[1];
                    } else {
                        $temp_running_verion_1 = $running_version;
                    }
                    $temp_running_verion_1 = @str_replace(".", "", $temp_running_verion_1);


                    if (($temp_running_verion_1 < $temp_product_verion_1) || (($temp_running_verion_1 == $temp_product_verion_1) && ($temp_running_verion_2 < $temp_product_verion_2))) {
                        $shouldUpgrade = true;
                    }
                }

                if (!empty($shouldUpgrade)) {
                    $finalModules[$key] = $getModVersion->title;
                }
            }
        }

        foreach ($finalModules as $modArray) {
            $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "' . $modArray . '".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
        }

        return $errorMsg;
    }

}
