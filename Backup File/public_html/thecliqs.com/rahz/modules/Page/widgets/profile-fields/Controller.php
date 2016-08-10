<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Widget_ProfileFieldsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

		$view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		
		$this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
  }
}