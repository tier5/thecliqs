<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Popups.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_Controller_Action_Helper_Popups extends Zend_Controller_Action_Helper_Abstract
{

	function preDispatch()
	{
		$front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
		$action = $front->getRequest()->getActionName();
		$controller = $front->getRequest()->getControllerName();

    if (!Engine_Api::_()->user()->getViewer()->membership()->getMemberCount(true)){
      return ;
    }

    switch ($module) {
      case 'user':
        switch ($controller) {
          case 'friends':
            $request = $front->getRequest();
            $session = new Zend_Session_Namespace();
            switch ($action) {
              case 'add':

                $paginator = Engine_Api::_()->suggest()->getFriends(array(
                  'object_type' => 'user',
                  'object_id' => $request->getParam('user_id', 0)
                ));

                $settings = Engine_Api::_()->getApi('settings', 'core');
                if ($settings->getSetting('suggest.friend.add') && $paginator->getTotalItemCount()) {
                  $session->suggest_type = 'fr_sent';
                  $session->object_type = 'user';
                  $session->object_id = $request->getParam('user_id', 0);
                } else {
                  Engine_Api::_()->suggest()->clearSession();
                }
                
              break;
              case 'confirm':

                $paginator = Engine_Api::_()->suggest()->getFriends(array(
                  'object_type' => 'user',
                  'object_id' => $request->getParam('user_id', 0)
                ));

                $settings = Engine_Api::_()->getApi('settings', 'core');
                if ($settings->getSetting('suggest.friend.confirm') && $paginator->getTotalItemCount()) {
                  $session->suggest_type = 'fr_confirm';
                  $session->object_type = 'user';
                  $session->object_id = $request->getParam('user_id', 0);
                } else {
                  Engine_Api::_()->suggest()->clearSession();
                }
              break;
            }
          break;
        }
      break;
      case 'group':
        switch ($controller) {
          case 'member':
            $request = $front->getRequest();
            $session = new Zend_Session_Namespace();
            switch ($action) {
              case 'join':

                $paginator = Engine_Api::_()->suggest()->getFriends(array(
                  'object_type' => 'group',
                  'object_id' => $request->getParam('group_id', 0)
                ));

                $settings = Engine_Api::_()->getApi('settings', 'core');
                if ($settings->getSetting('suggest.group.join', 1) && $paginator->getTotalItemCount()) {
                  $session->suggest_type = 'gr_join';
                  $session->object_type = 'group';
                  $session->object_id = $request->getParam('group_id', 0);
                } else {
                  Engine_Api::_()->suggest()->clearSession();
                }
              break;
              case 'accept':

                $paginator = Engine_Api::_()->suggest()->getFriends(array(
                  'object_type' => 'group',
                  'object_id' => $request->getParam('group_id', 0)
                ));

                $settings = Engine_Api::_()->getApi('settings', 'core');
                if ($settings->getSetting('suggest.group.accept', 1) && $paginator->getTotalItemCount()) {
                  $session->suggest_type = 'gr_accept';
                  $session->object_type = 'group';
                  $session->object_id = $request->getParam('group_id', 0);
                } else {
                  Engine_Api::_()->suggest()->clearSession();
                }
              break;
            }
          break;
        }
      break;
      case 'event':
        switch ($controller) {
          case 'member':
            $request = $front->getRequest();
            $session = new Zend_Session_Namespace();
            switch ($action) {
              case 'join':

                $paginator = Engine_Api::_()->suggest()->getFriends(array(
                  'object_type' => 'event',
                  'object_id' => $request->getParam('event_id', 0)
                ));

                $settings = Engine_Api::_()->getApi('settings', 'core');
                if ($settings->getSetting('suggest.event.join', 1) && $paginator->getTotalItemCount()) {
                  $session->suggest_type = 'ev_join';
                  $session->object_type = 'event';
                  $session->object_id = $request->getParam('event_id', 0);
                } else {
                  Engine_Api::_()->suggest()->clearSession();
                }
              break;
              case 'accept':

                $paginator = Engine_Api::_()->suggest()->getFriends(array(
                  'object_type' => 'event',
                  'object_id' => $request->getParam('event_id', 0)
                ));

                $settings = Engine_Api::_()->getApi('settings', 'core');
                if ($settings->getSetting('suggest.event.accept', 1) && $paginator->getTotalItemCount()) {
                  $session->suggest_type = 'ev_accept';
                  $session->object_type = 'event';
                  $session->object_id = $request->getParam('event_id', 0);
                } else {
                  Engine_Api::_()->suggest()->clearSession();
                }
              break;
            }
          break;
        }
      break;
    }
	}


	function postDispatch()
	{
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
		$action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();
    $session = new Zend_Session_Namespace();

    if (isset($session->suggest_type)) {
      if ($session->suggest_type == 'fr_sent'
        || $session->suggest_type == 'fr_confirm'
        || $session->suggest_type == 'gr_join'
        || $session->suggest_type == 'gr_accept'
        || $session->suggest_type == 'ev_accept'
        || $session->suggest_type == 'ev_join'
      ) {
        switch ($module) {
          case 'core':
            switch ($controller) {
              case 'utility':
                switch ($action) {
                  case 'success':
                    $session->show_popup = true;
                    $object_type = $session->object_type;
                    $object_id = $session->object_id;
                    $to_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                    $suggests = Engine_Api::_()->suggest()->getAllSuggests(array(
                        'object_type' => $object_type,
                        'object_id' => $object_id,
                        'to_id' => $to_id
                      ), false);
                    foreach ($suggests as $suggest) {
                      $suggest->delete();
                    }
                  break;
                }
              break;
            }
          break;
        }
      }
    }
	}
}