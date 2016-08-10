<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        if (
            Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('touch') &&
            Engine_Api::_()->touch()->isTouchMode() ||
            Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('mobile') &&
            Engine_Api::_()->mobile()->isMobileMode()
        ) {
            return false;
        }


        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        /**
         * @var $settings Core_Api_Settings
         */
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if ($module == 'user' && $controller == 'profile' && $action == 'index') {

            if ($settings->__get('timeline.usage', 'choice') == 'force') {
                $request->setModuleName('timeline');
                return;
            }

            $id = $request->getParam('id');

            $user = Engine_Api::_()->user()->getUser($id);
            if ($user->getIdentity()) {
                $user = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($user->getIdentity());
            }

            if ($user->getIdentity() && Engine_Api::_()->getDbTable('settings', 'user')->getSetting($user, 'timeline-usage')) {
                $request->setModuleName('timeline');
                return;
            }
        }
    }


    public function onUserCoverPagePhotoUpload($event)
    {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return;
        }

        $payload = $event->getPayload();

        if (empty($payload['page']) || !($payload['page'] instanceof Core_Model_Item_Abstract)) {
            return;
        }
        if (empty($payload['file']) || !($payload['file'] instanceof Storage_Model_File)) {
            return;
        }

        $page = $payload['page'];
        $viewer = $page->getOwner();
        $file = $payload['file'];

        // Get album
        $table = Engine_Api::_()->getDbtable('albums', 'timeline');
        $album = $table->getSpecialPageAlbum($viewer, $page, 'page_cover');

        $album_type = $album->getType();

        $isPageAlbumsEnabled = (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum') && ($album_type == 'pagealbum'));

        if ($isPageAlbumsEnabled) {
            $photoTable = Engine_Api::_()->getDbtable('pagealbumphotos', 'pagealbum');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_id' => $viewer->getIdentity(),
                'collection_id' => $album->getIdentity()
            ));
            $photo->setPhoto($file);
            $photo->save();
        } else {
            $photoTable = Engine_Api::_()->getDbtable('photos', 'album');

            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            ));
            $photo->save();
            $photo->setPhoto($file);
            $photo->album_id = $album->album_id;
            $photo->save();
        }

        if (!$album->photo_id) {
            $album->photo_id = $photo->getIdentity();
            $album->save();
        }

        $event->addResponse($photo);
    }

    public function onUserCoverPhotoUpload($event)
    {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return;
        }

        $payload = $event->getPayload();

        if (empty($payload['user']) || !($payload['user'] instanceof Core_Model_Item_Abstract)) {
            return;
        }
        if (empty($payload['file']) || !($payload['file'] instanceof Storage_Model_File)) {
            return;
        }

        $viewer = $payload['user'];
        $file = $payload['file'];

        // Get album
        $table = Engine_Api::_()->getDbtable('albums', 'timeline');
        $album = $table->getSpecialAlbum($viewer, 'cover');

        $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
            'owner_type' => 'user',
            'owner_id' => $viewer->getIdentity()
        ));
        $photo->save();
        $photo->setPhoto($file);

        $photo->album_id = $album->album_id;
        $photo->save();

        if (!$album->photo_id) {
            $album->photo_id = $photo->getIdentity();
            $album->save();
        }

        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
        $auth->setAllowed($album, 'everyone', 'view', true);
        $auth->setAllowed($album, 'everyone', 'comment', true);

        $event->addResponse($photo);
    }

    public function onUserBornPhotoUpload($event)
    {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return;
        }

        $payload = $event->getPayload();

        if (empty($payload['user']) || !($payload['user'] instanceof Core_Model_Item_Abstract)) {
            return;
        }
        if (empty($payload['file']) || !($payload['file'] instanceof Storage_Model_File)) {
            return;
        }

        $viewer = $payload['user'];
        $file = $payload['file'];

        // Get album
        $table = Engine_Api::_()->getDbtable('albums', 'timeline');
        $album = $table->getSpecialAlbum($viewer, 'birth');

        $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
            'owner_type' => 'user',
            'owner_id' => $viewer->getIdentity()
        ));
        $photo->save();
        $photo->setPhoto($file);

        $photo->album_id = $album->album_id;
        $photo->save();

        if (!$album->photo_id) {
            $album->photo_id = $photo->getIdentity();
            $album->save();
        }

        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
        $auth->setAllowed($album, 'everyone', 'view', true);
        $auth->setAllowed($album, 'everyone', 'comment', true);

        $event->addResponse($photo);
    }
}
