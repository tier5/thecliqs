<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */

        if (!empty($method) && $method == 'Spectacular_Form_Admin_Settings') {
            
        }
        return true;
    }

    public function indexAction() {


        $tempLanguageDataArray = array();
        $tempLanguageTitleDataArray = array();
        if ($this->getRequest()->isPost()) {
            $localeMultiOptions = Engine_Api::_()->spectacular()->getLanguageArray();
            $coreSettings = Engine_Api::_()->getApi('settings', 'core');
            $defaultLanguage = $coreSettings->getSetting('core.locale.locale', 'en');
            $total_allowed_languages = Count($localeMultiOptions);

            if (!empty($localeMultiOptions)) {
                foreach ($localeMultiOptions as $key => $label) {
                    $lang_name = $label;
                    if (isset($localeMultiOptions[$label])) {
                        $lang_name = $localeMultiOptions[$label];
                    }

                    $page_block_field = "spectacular_lending_page_block_$key";
                    $page_block_title_field = "spectacular_lending_page_block_title_$key";
                    if ($total_allowed_languages <= 1) {
                        $page_block_field = "spectacular_lending_page_block";
                        $page_block_title_field = "spectacular_lending_page_block_title";
                        $page_block_label = "Description";
                        $page_block_title_label = "Title";
                    } elseif ($label == 'en' && $total_allowed_languages > 1) {
                        $page_block_field = "spectacular_lending_page_block";
                        $page_block_title_field = "spectacular_lending_page_block_title";
                    }

                    if (!strstr($key, '_')) {
                        $key = $key . '_default';
                    }

                    $tempLanguageDataArray[$key] = @base64_encode($_POST[$page_block_field]);
                    $tempLanguageTitleDataArray[$key] = @base64_encode($_POST[$page_block_title_field]);
                }

                $coreSettings->setSetting('spectacular.lending.block.languages', $tempLanguageDataArray);
                $coreSettings->setSetting('spectacular.lending.block.title.languages', $tempLanguageTitleDataArray);
            }
        }

        include_once APPLICATION_PATH . '/application/modules/Spectacular/controllers/license/license1.php';
    }

    public function imagesAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spectacular_admin_main', array(), 'spectacular_admin_settings_images');

        include_once APPLICATION_PATH . '/application/modules/Spectacular/controllers/license/license2.php';
    }

    public function bannersAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spectacular_admin_main', array(), 'spectacular_admin_settings_banners');

        include_once APPLICATION_PATH . '/application/modules/Spectacular/controllers/license/license2.php';
    }

    public function orderAction() {
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                if (strstr($key, "content_")) {
                    $keyArray = explode("content_", $key);

                    if (!empty($keyArray))
                        $image_id = end($keyArray);

                    if (!empty($image_id)) {
                        $obj = Engine_Api::_()->getItem('spectacular_image', $image_id);
                        $obj->order = $value;
                        $obj->save();
                    }
                }
            }
        }
    }

    public function orderBannersAction() {
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                if (strstr($key, "content_")) {
                    $keyArray = explode("content_", $key);

                    if (!empty($keyArray))
                        $banner_id = end($keyArray);

                    if (!empty($banner_id)) {
                        $obj = Engine_Api::_()->getItem('spectacular_banner', $banner_id);
                        $obj->order = $value;
                        $obj->save();
                    }
                }
            }
        }
    }

    public function addImagesAction() {
        $this->view->form = $form = new Spectacular_Form_Admin_Images_Add();
        $table = Engine_Api::_()->getItemTable('spectacular_image');
        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //CHECK VALIDITY
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $values = $form->getValues();

        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            include_once APPLICATION_PATH . '/application/modules/Spectacular/controllers/license/license2.php';
            //COMMIT
            $db->commit();
            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Images successfully add.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function addBannersAction() {
        $this->view->form = $form = new Spectacular_Form_Admin_Banners_Add();
        $table = Engine_Api::_()->getItemTable('spectacular_banner');
        //CHECK POST
        if (!$this->getRequest()->isPost()) {
            return;
        }

        //CHECK VALIDITY
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $values = $form->getValues();

        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            include_once APPLICATION_PATH . '/application/modules/Spectacular/controllers/license/license2.php';
            //COMMIT
            $db->commit();
            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Banners successfully add.'))
            ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function deleteAction() {

        $this->_helper->layout->setLayout('admin-simple');

        $this->view->id = $id = $this->_getParam('id');

        if ($this->getRequest()->isPost()) {
            $item = Engine_Api::_()->getItem('spectacular_image', $id);

            $item->delete();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Deleted Succesfully.')
            ));
        }
    }

    public function enabledAction() {
        $id = $this->_getParam('id');
        if (!empty($id)) {
            $item = Engine_Api::_()->getItem('spectacular_image', $id);
            $item->enabled = !$item->enabled;
            $item->save();
        }

        $this->_redirect('admin/spectacular/settings/images');
    }

    public function enabledBannersAction() {
        $id = $this->_getParam('id');
        if (!empty($id)) {
            $item = Engine_Api::_()->getItem('spectacular_banner', $id);
            $item->enabled = !$item->enabled;
            $item->save();
        }

        $this->_redirect('admin/spectacular/settings/banners');
    }

    public function deleteBannersAction() {

        $this->_helper->layout->setLayout('admin-simple');

        $this->view->id = $id = $this->_getParam('id');

        if ($this->getRequest()->isPost()) {
            $item = Engine_Api::_()->getItem('spectacular_banner', $id);

            $item->delete();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Deleted Succesfully.')
            ));
        }
    }

    public function faqAction() {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('spectacular_admin_main', array(), 'spectacular_admin_settings_faq');
        $this->view->faq_id = $faq_id = $this->_getParam('faq', 'faq_1');
    }

    public function footerMenuAction() {
        $this->_redirect('admin/menus/index?name=spectacular_footer');
    }

    public function placeHtaccessFileAction() {
        if ($this->getRequest()->isPost()) {
            $successfullyAdded = false;
            $getFileContent = '<FilesMatch ".(ttf|otf|woff)$">
    Header set Access-Control-Allow-Origin "*"
</FilesMatch>';

            $global_directory_name = APPLICATION_PATH . '/application/themes/spectacular';
            $global_settings_file = $global_directory_name . '/.htaccess';
            $is_file_exist = @file_exists($global_settings_file);

            // IF FILE NOT EXIST THEN CREATE NEW .HTACCESS FILE THERE.
            if (empty($is_file_exist)) {
                if (is_dir($global_directory_name)) {
                    @mkdir($global_directory_name, 0777);

                    $fh = @fopen($global_settings_file, 'w') or die('Unable to create .htaccess file; please give the CHMOD 777 recursive permission to the directory "' . APPLICATION_PATH . '/application/themes/spectacular' . '" and then try again.');
                    @fwrite($fh, $getFileContent);
                    @fclose($fh);

                    @chmod($global_settings_file, 0777);
                    $successfullyAdded = true;
                }
            } else {
                if (!is_writable($global_settings_file)) {
                    @chmod($global_settings_file, 0777);
                    if (!is_writable($global_settings_file)) {
                        $form->addError('Unable to create .htaccess file; please give the CHMOD 777 recursive permission to the directory "' . APPLICATION_PATH . '/application/themes/spectacular' . '" and then try again.');
                        return;
                    }
                }
                $successfullyAdded = @file_put_contents($global_settings_file, $getFileContent);
            }

            if (!empty($successfullyAdded)) {
                $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array('File Succesfully Created.')
                ));
            }
        }
    }

    public function placeCustomizationFileAction() {
        if ($this->getRequest()->isPost()) {
            $global_directory_name = APPLICATION_PATH . '/application/themes/spectacular';
            @chmod($global_directory_name, 0777);

            if (!is_readable($global_directory_name)) {
                $this->view->error_message = "<span style='color:red'>Note: You do not have readable permission on the path below, please give 'chmod 777 recursive permission' on it to continue with the installation process : <br /> 
Path Name: <b>" . $global_directory_name . "</b></span>";
                return;
            }

            $global_settings_file = $global_directory_name . '/customization.css';
            $is_file_exist = @file_exists($global_settings_file);
            if (empty($is_file_exist)) {
                @chmod($global_directory_name, 0777);
                if (!is_writable($global_directory_name)) {
                    $this->view->error_message = "<span style='color:red'>Note: You do not have writable permission on the path below, please give 'chmod 777 recursive permission' on it to continue with the installation process : <br /> 
Path Name: " . $global_directory_name . "</span>";
                    return;
                }

                $fh = @fopen($global_settings_file, 'w');
                @fwrite($fh, '/* ADD CUSTOM STYLE */');
                @fclose($fh);

                @chmod($global_settings_file, 0777);
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('File Succesfully Created.')
            ));
        }
    }

    //ACTION FOR MULTI-DELETE OF IMAGES
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {

            $values = $this->getRequest()->getPost();

            foreach ($values as $key => $value) {

                if ($key == 'delete_' . $value) {

                    $item = Engine_Api::_()->getItem('spectacular_image', $value);

                    $item->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'images'));
    }

    //ACTION FOR MULTI-DELETE OF BANNERS
    public function multiDeleteBannersAction() {

        if ($this->getRequest()->isPost()) {

            $values = $this->getRequest()->getPost();

            foreach ($values as $key => $value) {

                if ($key == 'delete_' . $value) {

                    $item = Engine_Api::_()->getItem('spectacular_banner', $value);

                    $item->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'banners'));
    }

}
