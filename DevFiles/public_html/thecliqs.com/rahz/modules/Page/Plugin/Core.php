<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		$md = $request->getParam('md', null);

		if ($md != 'page'){
			return;
		}

		$module = $request->getModuleName();
		$controller =$request->getControllerName();
		$action = $request->getActionName();

		if ($module != 'payment' || $controller != 'ipn' || strtolower($action) != 'paypal'){
			return;
		}

//        if ($module == 'page' && $controller == 'index' && $action == 'view') {
//            //            if ($settings->__get('timeline.usage', 'choice') == 'force') {
//            //                $request->setModuleName('timeline');
//            //                return;
//            //            }
//
//            $id = $request->getParam('page_id');
//            $request->setModuleName('timeline');
//            //            $user = Engine_Api::_()->user()->getUser($id);
//            //            if ($user->getIdentity()) {
//            //                $user = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($user->getIdentity());
//            //            }
//
//            //            if ($user->getIdentity() && Engine_Api::_()->getDbTable('settings', 'user')->getSetting($user, 'timeline-usage')) {
//            //                $request->setModuleName('timeline');
//            //                return;
//            //            }
//            return;
//        }

    //Redirect to store module'
		if ( Engine_Api::_()->page()->isActiveTransaction() )
		{
			$request->setModuleName($md);
		}
	}
	
  public function onRenderLayoutDefault($event)
  {
    $view = $event->getPayload();

    if ($view instanceof Zend_View) {
      $view->headScript()->appendFile('application/modules/Page/externals/scripts/core.js');
      $view->headTranslate(array('There was error.', 'Add admins', 'Pages sorted by', 'tag', 'Loading...'));

      if (Engine_Api::_()->core()->hasSubject()) {
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() == 'page') {
          $script = "
            page_search.page_id = ".(int)$subject->getIdentity().";
            page_search.tag_url = '".$view->url(array('page_id' => (int)$subject->getIdentity(), 'action' => 'tag'), 'page_search')."';
            en4.core.runonce.add(function(){
              page_search.init_tag_cloud();
            });
          ";
          $view->headScript()->appendScript($script);

          $settings = Engine_Api::_()->getDbTable('settings', 'core');
          $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
          $is_enabled_ads = ($settings->setSetting('page.communityad.enabled', 0) && $modulesTbl->isModuleEnabled('communityad'));
          if ($is_enabled_ads && !$subject->isTimeline()) {
            $adScript = $view->partial('ad/_adsJS.tpl', 'page');
            $view->headScript()->appendScript($adScript);
          }
        }
      }
    }
  }

  public function addActivity($event)
  {
    $payload = $event->getPayload();
    $subject = $payload['subject'];
    $object = $payload['object'];
    $type = @$payload['type'];

    if ( $object instanceof Page_Model_Page && $type != 'page_create') {

      $new_responses = array();
      $event_responces = $event->getResponses();
      $event_responces = ($event_responces) ? $event_responces : array();
      foreach ($event_responces as $key => $response){
        if (!in_array($response['type'], array('registered', 'everyone'))){
          $new_responses[] = $response;
        }
      }
      for ($i=0;$i<count($new_responses);$i++) {
        $response = $new_responses[$i];
        if ($i == 0){
          $event->setResponse($response);
        } else {
          $event->addResponse($response);
        }
      }

      $event->addResponse(array(
        'type' => 'page_feed',
        'identity' => 0,
      ));

      $event->addResponse(array(
        'type' => 'page_registered',
        'identity' => 0,
      ));

      if (Engine_Api::_()->authorization()->context->isAllowed($object, 'registered', 'view')){
        $event->addResponse(array(
          'type' => 'page',
          'identity' => $object->getIdentity()
        ));
      }

      if ($type == 'post') {
        $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
        $select = $activityTbl->select()
          ->where('type = ?', $type)
          ->where('subject_id = ?', $subject->getIdentity())
          ->where('object_type = ?', $object->getType())
          ->where('object_id = ?', $object->getIdentity())
          ->order('action_id DESC')
          ->limit(1);
        $action = $activityTbl->fetchRow($select);
        $admins = $object->getAdmins();
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        foreach ($admins as $admin){
          // if owner
          if ($admin->getIdentity() == $subject->getIdentity()){
            continue;
          }
          // Send Notify
          $notifyApi->addNotification($admin, $subject, $object, $type.'_page', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('PAGE_Page Wall'),
            'action' => $object->getHref(array('action_id' => $action->action_id))
          ));
        }
      }
    }
  }

  public function getActivity($event)
  {
    // Detect viewer and subject
    $payload = $event->getPayload();
    $user = null;
    $subject = null;
    if( $payload instanceof User_Model_User ) {
      $user = $payload;
    } else if( is_array($payload) ) {
      if( isset($payload['for']) && $payload['for'] instanceof User_Model_User ) {
        $user = $payload['for'];
      }
      if( isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract ) {
        $subject = $payload['about'];
      }
    }
    if( null === $user ) {
      $viewer = Engine_Api::_()->user()->getViewer();
      if( $viewer->getIdentity() ) {
        $user = $viewer;
      }
    }
    if( null === $subject && Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
    }

    // Page feed
    if ($subject instanceof Page_Model_Page || $subject instanceof User_Model_User){

      // everyone
      $event->addResponse(array(
        'type' => 'page_feed',
        'data' => 0,
      ));

      // registered
      $viewer = Engine_Api::_()->user()->getViewer();

      if ($viewer->getIdentity()){

        $event->addResponse(array(
          'type' => 'page_registered',
          'data' => 0,
        ));
        
      }
    }

    // Teams
    $data = Engine_Api::_()->getDbtable('membership', 'page')->getMembershipsOfIds($user);
    if ( !empty($data) && is_array($data) ) {
      $event->addResponse(array(
        'type' => 'page',
        'data' => $data,
      ));
    }

    // Likes
    $data = Engine_Api::_()->getDbtable('listItems', 'page')->getLikePageIds($user);
    if ( !empty($data) && is_array($data) ) {
      $event->addResponse(array(
        'type' => 'page',
        'data' => $data,
      ));
    }
  }
  
  public function like($event)
  {
    $payload = $event->getPayload();
    $page = $payload['object'];
    $user = $payload['user'];
    
    if ($page instanceof Page_Model_Page){
      $page->getLikesList()->add($user);
    }
  }

  public function unlike($event)
  {
    $payload = $event->getPayload();
    $page = $payload['object'];
    $user = $payload['user'];

    if ($page instanceof Page_Model_Page){
      $page->getLikesList()->remove($user);
    }
  }
  
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('pages', 'page');
    $select  = new Zend_Db_Table_Select($table);
    $select->from($table->info('name'), array('COUNT(*) AS count'))->where("name <> 'footer'")->where("name <> 'header'")->where('name <> ?', 'default');
    $event->addResponse($select->query()->fetchColumn(0), 'page');
  }
  
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      $table = Engine_Api::_()->getDbtable('pages', 'page');
      $select = $table->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $table->fetchAll($select) as $page ){
        $page->delete();
      }

      /**
       * @var $claimTable Page_Model_DbTable_Claims
       */
      $claimTable = Engine_Api::_()->getDbTable('claims', 'page');
      $claimTable->delete(array('user_id' => $payload->getIdentity()));

    }
  }

  public function typeDelete(Engine_Hooks_Event $e) {
    $params = $e->getPayload();
    $cat_id = $params['option_id'];
    $prefix = Engine_Api::_()->getItemTable('page')->getTablePrefix();

    $q = "DELETE FROM `{$prefix}page_category_set_category` WHERE `cat_id` = {$cat_id}";
    Engine_Db_Table::getDefaultAdapter()->query($q);
  }

  public function typeCreate(Engine_Hooks_Event $e) {
    $params = $e->getPayload();
    if(empty($params['option'])) {
      return;
    }

    $cat_id = $params['option']['option_id'];
    $dba = Engine_Db_Table::getDefaultAdapter();
    $prefix = Engine_Api::_()->getItemTable('page')->getTablePrefix();
    $q = "SELECT MAX(`order`) FROM {$prefix}page_category_set_category where set_id = 1";
    $order = $dba->query($q)->fetchColumn();
    $order += 1;
    $q = "INSERT INTO `{$prefix}page_category_set_category` SET `set_id` = 1, `cat_id` = {$cat_id}, `order` = {$order}";
    Engine_Db_Table::getDefaultAdapter()->query($q);
  }
}