<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ProfileController.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_ProfileController extends Core_Controller_Action_Standard
{
    public function init()
    {
        // @todo this may not work with some of the content stuff in here, double-check
        $subject = null;
        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->view->id = $id = $this->_getParam('id');

            if (null !== $id) {
                $subject = Engine_Api::_()->user()->getUser($id);
                if ($subject->getIdentity()) {
                    $subject = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($subject->getIdentity());
                    Engine_Api::_()->core()->setSubject($subject);
                }
            }
        }

        $this->_helper->requireSubject('user');
        $this->_helper->requireAuth()->setNoForward()->setAuthParams(
            $subject,
            Engine_Api::_()->user()->getViewer(),
            'view'
        );

        $this->_helper->contextSwitch
            ->addActionContext('date', 'json')
            ->addActionContext('dates', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        /**
         * @var $subject Timeline_Model_User
         * @var $viewer User_Model_User
         */
        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();

        // check public settings
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_profile;
        if (!$require_check && !$this->_helper->requireUser()->isValid()) {
            return;
        }

        // Check enabled
        if (!$subject->enabled && !$viewer->isAdmin()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        // Check block
        if ($viewer->isBlockedBy($subject)) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        // Increment view count
        if (!$subject->isSelf($viewer)) {
            $subject->view_count++;
            $subject->save();
        }

        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('timeline');
        $path = dirname($path) . '/layouts';

        $layout = Zend_Layout::startMvc();
        $layout->setViewBasePath($path);

        $this->view->headTitle()->setAutoEscape(false);
        $this->view->headMeta()->setAutoEscape(false);

        // Render
        return $this->_helper->content
            ->setNoRender()
            ->setEnabled();
    }

    public function widgetAction()
    {
        $content_id = (int)$this->_getParam('content_id');
        if (!($content_id)) {
            // Render
            return $this->_helper->content
                ->setNoRender()
                ->setEnabled();
        }

        /**
         * Profile Applications
         */
        /**
         * @var $contentTb Core_Model_DbTable_Content
         * @var $pagesTb Core_Model_DbTable_Pages
         * @var $page Core_Model_Page
         * @var $tabs Engine_Db_Table_Row
         */

        $activeTab = $this->_getParam('content_id');
        $contentTb = Engine_Api::_()->getDbTable('content', 'core');
        $pagesTb = Engine_Api::_()->getDbTable('pages', 'core');

        $select = $pagesTb->select()->where('name=?', 'timeline_profile_index')->limit(1);
        $page = $pagesTb->fetchRow($select);

        $select = $contentTb->select()->where('page_id=?', $page->getIdentity())->where('name=?', 'core.container-tabs')->limit(1);
        if (null == ($tabs = $contentTb->fetchRow($select))) {
            $this->view->status = false;
            return;
        }

        $select = $contentTb->select()
//      ->where('content_id=?', $content_id)
            ->where('page_id=?', $page->getIdentity())
            ->where('parent_content_id=?', $tabs->content_id)
            ->where('type=?', 'widget');

        if (null == ($widgets = $contentTb->fetchAll($select))) {
            $this->view->status = false;
            $this->view->message = $this->view->translate('TIMELINE_Widget not found.');
            return;
        }

        /*// Set up element
        $element = $tab->getElement();
        $element->clearDecorators()
          //->addDecorator('Children', array('placement' => 'APPEND'))
          ->addDecorator('Container');

        // If there is action_id make the activity_feed tab active
        $action_id = (int) Zend_Controller_Front::getInstance()->getRequest()->getParam('action_id');
        $activeTab = $action_id ? 'activity.feed' : $this->_getParam('tab');

        if( empty($activeTab) ) {
          $activeTab = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
        }*/

        // Iterate over children
        $tabs = array();
        $childrenContent = '';
        foreach ($widgets as $content) {

            $child = new Engine_Content_Element_Widget(array(
                'identity' => $content->content_id,
                'name' => $content->name,
                'order' => $content->order,
                'params' => $content->params,
                'elements' => array()
            ));

            // First tab is active if none supplied
            if (null === $activeTab) {
                $activeTab = $child->getIdentity();
            }
            // If not active, set to display none
            if ($child->getIdentity() != $activeTab && $child->getName() != $activeTab) {
                $child->getDecorator('Container')->setParam('style', 'display:none;');
            }
            // Set specific class name
            $child_class = $child->getDecorator('Container')->getParam('class');
            $child->getDecorator('Container')->setParam('class', $child_class . ' tab_' . $child->getIdentity());

            // Remove title decorator
            $child->removeDecorator('Title');
            // Render to check if it actually renders or not
            $childrenContent .= $child->render() . PHP_EOL;
            // Get title and childcount
            $title = $child->getTitle();
            $childCount = null;
            if (method_exists($child, 'getWidget') && method_exists($child->getWidget(), 'getChildCount')) {
                $childCount = $child->getWidget()->getChildCount();
            }
            if (!$title) $title = $child->getName();
            // If it does render, add it to the tab list
            if (!$child->getNoRender()) {
                $tabs[] = array(
                    'id' => $child->getIdentity(),
                    'name' => $child->getName(),
                    'containerClass' => $child->getDecorator('Container')->getClass(),
                    'title' => $title,
                    'childCount' => $childCount
                );
            }
        }

        // Don't bother rendering if there are no tabs to show
        if (empty($tabs)) {
            return $this->setNoRender();
        }

        $this->view->activeTab = $activeTab;
        $this->view->tabs = $tabs;
        $this->view->childrenContent = $childrenContent;
        $this->view->max = $this->_getParam('max');

        $this->_renderWidgets();
    }
}