<?php
/**
 * SocialEngine
 *
 * @category  Application_Extensions
 * @package   Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license   http://www.hire-experts.com
 * @version   $Id: Controller.php 2/9/12 11:03 AM mt.uulu $
 * @author    Mirlan
 */

/**
 * @category  Application_Extensions
 * @package   Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license   http://www.hire-experts.com
 */

class Timeline_Widget_HeaderController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
      $likeEnabled = (bool)(Engine_Api::_()->like()->isAllowed($subject));
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->menuitems = $settings->__get('timeline.menuitems', 20);

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {

      $this->view->private = 1;

    } else {
      /**
       * Profile Applications
       */
      /**
       * @var $timeline  Timeline_Api_Core
       * @var $contentTb Core_Model_DbTable_Content
       * @var $pagesTb   Core_Model_DbTable_Pages
       * @var $page      Core_Model_Page
       * @var $tabs      Engine_Db_Table_Row
       */
      $active = array();
      $noneActive = array();
      $timeline = Engine_Api::_()->timeline();

      $contentTb = Engine_Api::_()->getDbTable('content', 'core');
      $pagesTb = Engine_Api::_()->getDbTable('pages', 'core');

      $select = $pagesTb->select()->where('name=?', 'timeline_profile_index')->limit(1);
      $page = $pagesTb->fetchRow($select);

      $select = $contentTb->select()->where('page_id=?', $page->getIdentity())->where('name=?', 'core.container-tabs')->limit(1);
      if (null != ($tabs = $contentTb->fetchRow($select))) {
        $select = $contentTb->select()
          ->where('page_id=?', $page->getIdentity())
          ->where('parent_content_id=?', $tabs->content_id)
          ->where('type=?', 'widget');

        $tmp_widgets = $contentTb->fetchAll($select);

        $widgets = array();
        $params = array();
        $i = 0;
        foreach ($tmp_widgets as $content) {

          try {
            $temp_params = (is_array($content->params)) ? $content->params : array();
            $child = new Engine_Content_Element_Widget(array(
              'identity' => $content->content_id,
              'name' => $content->name,
              'order' => $content->order,
              'params' => $temp_params,
              'elements' => array()
            ));
            $child->render();


            if ($child->getNoRender()) {
              continue;
            }


            if (array_key_exists($content->name, $params)) {
               $content->name = $content->name . $i ++;
            }

            $widgets[] = $content;

            $params[$content->name]['title'] = (string)(array_key_exists('title', $content->params)) ? $content->params['title'] : "TIMELINE_Application";

            if (method_exists($child, 'getWidget') && method_exists($child->getWidget(), 'getChildCount')) {
              $params[$content->name]['count'] = $child->getWidget()->getChildCount();
            }
          } catch (Exception $e) {
            print_log($e);
          }
        }

        $applications = $timeline->getApplications($tmp_widgets);

        foreach ($applications as $key => $application) {
          if (array_key_exists($key, $params) && array_key_exists('title', $application) && $params[$key]['title'] == "TIMELINE_Application") {
            $params[$key]['title'] = $application['title'];
          }

          if (
            !array_key_exists('add-link', $applications[$key]) ||
            array_key_exists($key, $params) && array_key_exists('count', $params[$key]) && $params[$key]['count'] > 0 ||
            array_key_exists('items', $application) && $application['items'] instanceof Zend_Paginator && $application['items']->count() > 0
          ) {
            $active[$key] = $application;
          } elseif (array_key_exists('add-link', $application)) {
            $noneActive[$key] = $application;
          }
        }
      }
      $this->view->isLikeEnabled = $likeEnabled;
      $this->view->private = 0;
      $this->view->widgets = $widgets;
      $this->view->widget_params = $params;
      $this->view->activeApplications = $active;
      $this->view->noneActiveApplications = $noneActive;

      //HE Gift
      /**
       * @var $settings  User_Model_DbTable_Settings
       * @var $recipient Hegift_Model_Recipient
       */

      $this->view->hegiftEnabled = $hegiftEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('hegift');
      if ($hegiftEnabled) {
        $settings = Engine_Api::_()->getDbTable('settings', 'hecore');
        $this->view->recipient_id = $recipient_id = $settings->getSetting($subject, 'active_gift');
        $this->view->recipient = $recipient = Engine_Api::_()->getItem('hegift_recipient', $recipient_id);

        if ($recipient !== null) {
          $this->view->from = Engine_Api::_()->getItem('user', $recipient->subject_id);
          $this->view->message = nl2br($recipient->message);
          $this->view->privacy = $recipient->getPrivacyForUser($viewer);
          $this->view->gift = Engine_Api::_()->getItem('gift', $recipient->gift_id);
        }

        $this->view->storage = Engine_Api::_()->storage();
      }
    }
    $this->view->profile_navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('user_profile');
  }
}