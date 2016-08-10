<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version $Id: IndexController.php 2/9/12 10:39 AM mt.uulu $
 * @author Mirlan
 */

/**
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 */


class Timeline_IndexController extends Core_Controller_Action_Standard
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
//      ->addActionContext('dates', 'json')
            ->initContext();
    }

    public function indexAction()
    {
    }

    public function datesAction()
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
            $this->view->status = false;
            return;
        }

        // Check enabled
        if (!$subject->enabled && !$viewer->isAdmin()) {
            $this->view->status = false;
            return;
        }

        // Check block
        if ($viewer->isBlockedBy($subject)) {
            $this->view->status = false;
            return;
        }

        $this->view->dates = $dates = Engine_Api::_()->timeline()->timelineDates($subject);

        $this->view->html = $html = $this->view->partial('_timelineDates.tpl', null, array(
            'dates' => $dates,
            'htmlOnly' => true,
        ));
        $this->view->status = true;
    }

    public function dateAction()
    {
        $timestamp = time();
        if (null != ($date = $this->_getParam('date'))) {
            $timestamp = strtotime($date);
        }

        $date = date('Y-m-d', $timestamp);

        $dateElement = new Timeline_Form_Element_Date('date');
        $dates = $dateElement->getMultiOptions();

        /**
         * @var $subject Timeline_Model_User
         */

        $subject = Engine_Api::_()->core()->getSubject();

        if ($birthdate = $subject->getBirthdate()) {
            $birthdate = explode('-', $birthdate);
        }

        foreach ($dates['year'] as $key => $year) {
            if ($year < $birthdate[0]) {
                unset($dates['year'][$key]);
            }
        }

        unset($dates['month'][0]);
        unset($dates['day'][0]);

        $this->view->html = $html = $this->view->formDate('date', $date, array(), $dates);
        $this->view->birthdate = $birthdate;
        $this->view->status = 1;
        return;
    }
}
