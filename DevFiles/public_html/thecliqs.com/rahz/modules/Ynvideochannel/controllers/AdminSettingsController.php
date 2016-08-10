<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_AdminSettingsController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynvideochannel_admin_main', array(), 'ynvideochannel_admin_main_settings');
        // Make form
        $this->view->form = $form = new Ynvideochannel_Form_Admin_Global();
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

    public function levelAction()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynvideochannel_admin_main', array(), 'ynvideochannel_admin_main_level');
        // Make form
        if (null !== ($id = $this->_getParam('id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;

        // Make form
        $form = new Ynvideochannel_Form_Admin_Settings_Level(array(
                'public' => (in_array($level->type, array('public'))),
                'moderator' => (in_array($level->type, array('admin', 'moderator'))
                ),)
        );
        $form->level_id->setValue($id);
        $this->view->level_id = $id;
        $this->view->form = $form;
        // Populate data
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $formSettingValues = $form->getSettingsValues();
        $video_allowed = $permissionsTable->getAllowed('ynvideochannel_video', $id, array_keys($formSettingValues['video']));
        $videoValues = array();
        foreach ($video_allowed as $key => $value) {
            $videoValues['video_' . $key] = $value;
        }
        $playlist_allowed = $permissionsTable->getAllowed('ynvideochannel_playlist', $id, array_keys($formSettingValues['playlist']));
        $playlistValues = array();
        foreach ($playlist_allowed as $key => $value) {
            $playlistValues['playlist_' . $key] = $value;
        }

        $channel_allowed = $permissionsTable->getAllowed('ynvideochannel_channel', $id, array_keys($formSettingValues['channel']));
        $channelValues = array();
        foreach ($playlist_allowed as $key => $value) {
            $channelValues['channel_' . $key] = $value;
        }
        $form->populate(array_merge($videoValues, $playlistValues, $channelValues));

        // User Credits
        if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
            // Video Item
            $typeTbl = Engine_Api::_()->getDbTable('types', 'yncredit');
            $creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");

            $creditElements = array('first_amount', 'first_credit', 'credit', 'max_credit', 'period');
            $videoSelect = $typeTbl->select()->where('module = ?', 'ynvideochannel')->where('action_type = ?', 'ynvideochannel_video')->limit(1);
            $videoType = $typeTbl->fetchRow($videoSelect);

            if (empty($videoType)) {
                $videoType = $typeTbl->createRow();
                $videoType->module = 'ynvideochannel';
                $videoType->action_type = 'ynvideochannel_video';
                $videoType->group = 'earn';
                $videoType->content = 'Sharing a new video';
                $videoType->credit_default = 5;
                $videoType->link_params = '{"route":"ynvideochannel_general","action":"share-video"}';
                $videoType->save();
            }
            $videoSelect = $creditTbl->select()
                ->where("level_id = ? ", $id)
                ->where("type_id = ?", $videoType->type_id)
                ->limit(1);
            $creditVideo = $creditTbl->fetchRow($videoSelect);
            if (empty($creditVideo)) {
                $creditVideo = $creditTbl->createRow();
            } else {
                foreach ($creditElements as $ele) {
                    $form->getElement('video_' . $ele)->setValue($creditVideo->$ele);
                }
            }
            // Playlist Item
            $playlistSelect = $typeTbl->select()->where('module = ?', 'ynvideochannel')->where('action_type = ?', 'ynvideochannel_playlist')->limit(1);
            $playlistType = $typeTbl->fetchRow($playlistSelect);

            if (empty($playlistType)) {
                $playlistType = $typeTbl->createRow();
                $playlistType->module = 'ynvideochannel';
                $playlistType->action_type = 'ynvideochannel_playlist';
                $playlistType->group = 'earn';
                $playlistType->content = 'Creating a new playlist';
                $playlistType->credit_default = 5;
                $playlistType->link_params = '{"route":"ynvideochannel_general","action":"create-playlist"}';
                $playlistType->save();
            }
            $playlistSelect = $creditTbl->select()
                ->where("level_id = ? ", $id)
                ->where("type_id = ?", $playlistType->type_id)
                ->limit(1);
            $creditPlaylist = $creditTbl->fetchRow($playlistSelect);
            if (empty($creditPlaylist)) {
                $creditPlaylist = $creditTbl->createRow();
            } else {
                foreach ($creditElements as $ele) {
                    $form->getElement('playlist_' . $ele)->setValue($creditPlaylist->$ele);
                }
            }

            // Channel Item
            $channelSelect = $typeTbl->select()->where('module = ?', 'ynvideochannel')->where('action_type = ?', 'ynvideochannel_channel')->limit(1);
            $channelType = $typeTbl->fetchRow($channelSelect);

            if (empty($channelType)) {
                $channelType = $typeTbl->createRow();
                $channelType->module = 'ynvideochannel';
                $channelType->action_type = 'ynvideochannel_channel';
                $channelType->group = 'earn';
                $channelType->content = 'Creating a new channel';
                $channelType->credit_default = 5;
                $channelType->link_params = '{"route":"ynvideochannel_general","action":"add-channel"}';
                $channelType->save();
            }
            $channelSelect = $creditTbl->select()
                ->where("level_id = ? ", $id)
                ->where("type_id = ?", $channelType->type_id)
                ->limit(1);
            $creditChannel = $creditTbl->fetchRow($channelSelect);
            if (empty($creditChannel)) {
                $creditChannel = $creditTbl->createRow();
            } else {
                foreach ($creditElements as $ele) {
                    $form->getElement('channel_' . $ele)->setValue($creditChannel->$ele);
                }
            }

        }

        $this->view->form = $form;
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
            $permissionsTable->setAllowed('ynvideochannel_video', $id, $settingValues['video']);
            $permissionsTable->setAllowed('ynvideochannel_playlist', $id, $settingValues['playlist']);
            $permissionsTable->setAllowed('ynvideochannel_channel', $id, $settingValues['channel']);

            // User Credits
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
                // Video Item
                $creditVideo->level_id = $id;
                $creditVideo->type_id = $videoType->type_id;
                foreach ($settingValues['video'] as $key => $value) {
                    if (in_array($key, $creditElements))
                        $creditVideo->$key = $value;
                }
                $creditVideo->save();
                // Playlist Item
                $creditPlaylist->level_id = $id;
                $creditPlaylist->type_id = $playlistType->type_id;
                foreach ($settingValues['playlist'] as $key => $value) {
                    if (in_array($key, $creditElements))
                        $creditPlaylist->$key = $value;
                }
                $creditPlaylist->save();

                // Channel Item
                $creditChannel->level_id = $id;
                $creditChannel->type_id = $channelType->type_id;
                foreach ($settingValues['channel'] as $key => $value) {
                    if (in_array($key, $creditElements))
                        $creditChannel->$key = $value;
                }
                $creditChannel->save();

            }
            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $form->addNotice($this->view->translate('Your changes have been saved.'));
    }
}