<?php
class Yncontest_Widget_ProfileMyContestsController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	public function indexAction()
	{
		$limit = $this->_getParam('number',5);
		$viewer = Engine_Api::_() -> core() -> getSubject();			
		$this->view->height = (int)$this -> _getParam('height',200);
		$this->view->width = (int)$this -> _getParam('width',200);
		$params = array(
			'contest_status' => 'published',
			'approve_status' => 'approved',
			'order' => 'approved_date',
			'limit' => $limit,
			'owner_id' => $viewer -> getIdentity()
		);		
		$this -> view -> items = $paginator = Engine_Api::_()->yncontest()->getContestPaginator($params);
		if($paginator->getTotalItemCount()==0) {
			$this -> setNoRender();			
		}
		$this->_childCount = $paginator->getTotalItemCount();		
		 
	}
	
	public function getChildCount() {
        return $this->_childCount;
    }

}
