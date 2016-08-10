<?php

class Ynbusinesspages_Widget_BusinessNewestDiscussionsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->setNoRender();
		}

		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('ynbusinesspages_business');
		if (!$subject -> isViewable() || !$subject -> getPackage() -> checkAvailableModule('ynbusinesspages_topic')) {
			return $this -> setNoRender();
		}

		// Get paginator
		$table = Engine_Api::_()->getItemTable('ynbusinesspages_topic');
		$select = $table->select()
			->where('business_id = ?', $subject->getIdentity()) -> limit($this->_getParam('itemCountPerPage', 1))
			->order('lastpost_id DESC');
		$this->view->topics = $topics = $table -> fetchAll($select);

		// Do not render if nothing to show
		if(!count($topics)) 
		{
			return $this->setNoRender();
		}
	}
}