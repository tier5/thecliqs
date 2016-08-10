<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version $Id: Controller.php 2/11/12 3:23 PM mt.uulu $
 * @author Mirlan
 */

/**
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 */


class Timeline_Widget_PageContentController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject('page');

        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }
        $this->view->dates = $dates = Engine_Api::_()->timeline()->timelinePageDates($subject);

        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('timeline');
        $path = dirname($path) . '/views/scripts';
        $this->view->addScriptPath($path);
    }
}
