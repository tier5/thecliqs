<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_AdminSettingsController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynultimatevideo_admin_main', array(), 'ynultimatevideo_admin_main_settings');

        // Check ffmpeg path for correctness
        if (function_exists('exec')) {
            $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->ynultimatevideo_ffmpeg_path;

            $output = null;
            $return = null;
            if (!empty($ffmpeg_path)) {
                exec($ffmpeg_path . ' -version', $output, $return);                
            }
            // Try to auto-guess ffmpeg path if it is not set correctly
            $ffmpeg_path_original = $ffmpeg_path;
            if (empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false) {
                $ffmpeg_path = null;
                // Windows
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    // @todo
                }
                // Not windows
                else {
                    $output = null;
                    $return = null;
                    @exec('which ffmpeg', $output, $return);
                    if (0 == $return) {
                        $ffmpeg_path = array_shift($output);
                        $output = null;
                        $return = null;
                        exec($ffmpeg_path . ' -version', $output, $return);
                        if (0 != $return) {
                            $ffmpeg_path = null;
                        }
                    }
                }
            }
            if ($ffmpeg_path != $ffmpeg_path_original) {
                Engine_Api::_()->getApi('settings', 'core')->ynultimatevideo_ffmpeg_path = $ffmpeg_path;
            }
        }

        // Make form
        $this->view->form = $form = new Ynultimatevideo_Form_Admin_Global();

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        // Check ffmpeg path
        if (!empty($values['ynultimatevideo_ffmpeg_path'])) {
            if (function_exists('exec')) {
                $ffmpeg_path = $values['ynultimatevideo_ffmpeg_path'];
                $output = null;
                $return = null;
                exec($ffmpeg_path . ' -version', $output, $return);
                if ($return > 0) {
                    $form->ynultimatevideo_ffmpeg_path->addError('FFMPEG path is not valid or does not exist');
                    $values['ynultimatevideo_ffmpeg_path'] = '';
                }
            } else {
                $form->ynultimatevideo_ffmpeg_path->addError('The exec() function is not available. The ffmpeg path has not been saved.');
                $values['ynultimatevideo_ffmpeg_path'] = '';
            }
        }

        // Okay, save
        foreach ($values as $key => $value) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
    }

    public function levelAction() {
        // Make navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynultimatevideo_admin_main', array(), 'ynultimatevideo_admin_main_level');

        // Get level id
        if (null !== ($id = $this->_getParam('id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $level_id = $id = $level->level_id;

        // Make form
        $this->view->form = $form = new Ynultimatevideo_Form_Admin_Settings_Level(array(
                    'public' => ( in_array($level->type, array('public')) ),
                    'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
                ));
        $form->level_id->setValue($id);

        // Populate values
        $formSettingValues = $form->getSettingsValues();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $videoKeyValues = $permissionsTable->getAllowed('ynultimatevideo_video', $id, array_keys($formSettingValues['ynultimatevideo_video']));
        
        // TODO [DangTH] : get the max number of video that a user leven can upload
        $videoKeyValues['max'] = Engine_Api::_()->ynultimatevideo()->getAllowedMaxValue('ynultimatevideo_video', $id, 'max');
        foreach($videoKeyValues as $key => $value) {
            $videoKeys['video_' . $key] = $value;
        }
        $playlistKeyValues = $permissionsTable->getAllowed('ynultimatevideo_playlist', $id, array_keys($formSettingValues['playlist']));
        $playlistKeys = array();
        foreach($playlistKeyValues as $key => $value) {
            $playlistKeys['playlist_' . $key] = $value;
        }
        $form->populate(array_merge($videoKeys, $playlistKeys));

        // Check post
        if (!$this->getRequest()->isPost()) {
            return;
        }

        // Check validitiy
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $settingValues = $form->getSettingsValues();

        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();

        try {
            // Set permissions
            $permissionsTable->setAllowed('ynultimatevideo_video', $id, $settingValues['video']);
            $permissionsTable->setAllowed('ynultimatevideo_playlist', $id, $settingValues['playlist']);
            
            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $form->addNotice('Your changes have been saved.');
    }

    public function utilityAction() {
        if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
            return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
        }

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynultimatevideo_admin_main', array(), 'ynultimatevideo_admin_main_utility');

        $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->ynultimatevideo_ffmpeg_path;
        if (function_exists('shell_exec')) {
            // Get version
            $this->view->version = $version
                    = shell_exec(escapeshellcmd($ffmpeg_path) . ' -version 2>&1');
            $command = "$ffmpeg_path -formats 2>&1";
            $this->view->format = $format
                    = shell_exec(escapeshellcmd($ffmpeg_path) . ' -formats 2>&1')
                    . shell_exec(escapeshellcmd($ffmpeg_path) . ' -codecs 2>&1');
        }
    }

	public function youtubeAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynultimatevideo_admin_main', array(), 'ynultimatevideo_admin_main_youtubesettings');

        $this->view->form = $form = new Ynultimatevideo_Form_Admin_Settings_Youtube();

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        // Okay, save
        foreach ($values as $key => $value) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
	}
}