<?php
class Yncontest_Widget_ProfileMyWinningEntriesController extends Engine_Content_Widget_Abstract
{
protected $_childCount;
	public function indexAction()
	{		
		$viewer = Engine_Api::_() -> core() -> getSubject();		
		$params['user_id'] = $viewer -> getIdentity();
		$params['status'] = 'win';		
		
		$this -> view -> paginator = $paginator = Engine_Api::_() -> yncontest() -> getEntryPaginator($params);
		$items_per_page = $paginator->getTotalItemCount();//Engine_Api::_() -> getApi('settings', 'core') -> getSetting('contest.page', 10);
		$this -> view -> paginator -> setItemCountPerPage($items_per_page);
		if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        } else {
            $this->_childCount = $paginator->getTotalItemCount();
        }		
	}
	
	public function getChildCount() {
        return $this->_childCount;
    }

}
