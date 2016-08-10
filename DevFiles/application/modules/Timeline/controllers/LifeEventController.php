<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version $Id: LifeEventController.php 2/16/12 11:09 AM mt.uulu $
 * @author Mirlan
 */

/**
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 */


class Timeline_LifeEventController extends Core_Controller_Action_Standard
{
    public function init()
    {
        $subject = null;
        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->view->id = $id = $this->_getParam('id');
            $this->_subject_type = $this->_getParam('subject', 'user');

            if (null !== $id) {
                if ($this->_subject_type == 'user') {
                    $subject = Engine_Api::_()->user()->getUser($id);
                    if ($subject->getIdentity()) {
                        $subject = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($subject->getIdentity());
                    }
                } elseif ($this->_subject_type == 'page') {
                    $subject = Engine_Api::_()->page()->getPage($id);
                }
                Engine_Api::_()->core()->setSubject($subject);
            }
        } else {
            $subject = Engine_Api::_()->core()->getSubject();
            $this->_subject_type = $subject->getType();
        }

        if ($this->_subject_type == 'user') {
            $this->_helper->requireSubject('user');
        } elseif ($this->_subject_type == 'page') {
            $this->_helper->requireSubject('page');
        }
        $this->_helper->requireAuth()->setNoForward()->setAuthParams(
            $subject,
            Engine_Api::_()->user()->getViewer(),
            'view'
        );

        $this->_helper->contextSwitch
            ->addActionContext('born', 'html')
            ->initContext();
    }

    public function indexAction()
    {

        $type = $this->_getParam('type', 'born');

        if (!$type || !method_exists($this, $type . 'Action')) {
            return $this->_helper->content->setNoRender();
        }

        return $this->_forward($type);
    }

    public function bornAction()
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


        if ($subject->getType() == 'page') {
            $this->view->birthdate = $subject->creation_date;
            return;
        }

        /**
         * @var $table User_Model_DbTable_Settings
         */
        $table = Engine_Api::_()->getDbTable('settings', 'user');
        if ($subject->getType() == 'user')
            $position = unserialize($table->getSetting($subject, 'timeline-born-position'));

        if (!is_array($position) || !array_key_exists('top', $position) || !array_key_exists('left', $position)) {
            $position = array('top' => 0, 'left' => 0);
        }

        /**
         * Assign Values
         */

        if ($subject->getType() == 'user') {
            $this->view->albumPhoto = $subject->getTimelineAlbumPhoto('born');
            $this->view->photoExists = $subject->hasTimelinePhoto('born');
        }

        $this->view->position = $position;
        $this->view->isAlbumEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album');

        $this->view->canEdit = $subject->authorization()->isAllowed($viewer, 'edit');

        if ($subject->getType() == 'user') {
            $this->view->birthdate = $subject->getBirthdate();
        }
    }
}
