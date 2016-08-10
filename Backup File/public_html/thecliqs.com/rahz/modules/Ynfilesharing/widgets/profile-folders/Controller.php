<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndonation
 * @author     YouNet Company
 */

class Ynfilesharing_Widget_ProfileFoldersController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
	
	public function indexAction() {
		$this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
		if ($subject == null || !$subject instanceof Core_Model_Item_Abstract)
		{
			return $this->setNoRender();
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if (method_exists($subject, 'membership')) {
			$this->view->canCreate = $subject->membership()->isMember($viewer);
		} else {
			$this->view->canCreate = true;
		}			
		$this->view->folders = $folders = Engine_Api::_()->ynfilesharing()->getSubFolders(NULL, $subject);
		$this->_childCount = count($folders);
	}
	
	public function getChildCount() {
		return $this->_childCount;
	}
}