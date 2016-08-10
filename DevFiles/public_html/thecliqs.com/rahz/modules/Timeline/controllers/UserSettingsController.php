<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: UserSettingsController.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_UserSettingsController extends Core_Controller_Action_Standard
{
    public function init()
    {
        // Can specifiy custom id
        $id = $this->_getParam('id', null);
        $subject = null;
        if (null === $id) {
            $subject = Engine_Api::_()->user()->getViewer();
            Engine_Api::_()->core()->setSubject($subject);
        }
        else
        {
            $subject = Engine_Api::_()->getItem('user', $id);
            Engine_Api::_()->core()->setSubject($subject);
        }

        // Set up require's
        $this->_helper->requireUser();
        $this->_helper->requireSubject();
        $this->_helper->requireAuth()->setAuthParams(
            $subject,
            null,
            'edit'
        );

        // Set up navigation
        $param = $this->_getParam('param', false);
        if (!$param) {
            $this->view->navigation = $navigation = Engine_Api::_()
                ->getApi('menus', 'core')
                ->getNavigation('user_settings', ($id ? array('params' => array('id' => $id)) : array()));
        } else {
            $this->view->navigation = $navigation = Engine_Api::_()
                ->getApi('menus', 'core')
                ->getNavigation('user_edit', ($id ? array('params' => array('id' => $id)) : array()));
        }

    }

    public function indexAction()
    {

        /**
         * @var $settings Core_Api_Settings
         * @var $subject User_Model_User
         */
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->user = $subject = Engine_Api::_()->core()->getSubject();
        $this->view->viewer = Engine_Api::_()->user()->getViewer();

        if ($settings->__get('timeline.usage') == 'force') {
            return $this->_helper->redirector->gotoUrl($subject->getHref(), array('prependBase' => false));
        }

        /**
         * @var $settings User_Model_DbTable_Settings
         */
        $settings = Engine_Api::_()->getDbTable('settings', 'hecore');

        $this->view->form = $form = new Timeline_Form_User_Settings();

        $selected = $settings->getSetting($subject, 'timeline-usage');

        $form->getElement('usage')->setValue($selected);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $usage = $form->getValue('usage');
        $settings->setSetting($subject, 'timeline-usage', $usage);
        $form->addNotice('TIMELINE_Settings have been successfully saved');
    }
}

