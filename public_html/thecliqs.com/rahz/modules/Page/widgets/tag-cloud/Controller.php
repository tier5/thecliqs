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

class Page_Widget_TagCloudController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

		$params = array('page_id' => $subject->getIdentity());
		$api = Engine_Api::_()->getApi('core', 'page');

		$data = Engine_Api::_()->getDbTable('tags', 'page')->getCloud($params)->toArray();
		
		$cloud = array();
		foreach ($data as $item){
			$cloud[] = $api->defineTagClass($item);
		}

		if (empty($cloud)){
			return $this->setNoRender();
		}

		$this->view->cloud = $cloud;
  }
}