<?php
class Ynbusinesspages_Widget_BusinessProfileMembersController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;

	public function indexAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->setNoRender();
		}

		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('ynbusinesspages_business');
		if (!$subject -> isViewable()) {
            return $this -> setNoRender();
        }
		$this->view->business = $business = Engine_Api::_()->core()->getSubject();
		
		// Get params
		$this->view->page = $page = $this->_getParam('page', 1);
		$this->view->search = $search = $this->_getParam('search');
		$this->view->waiting = $waiting = $this->_getParam('waiting', false);
		$this->view->isOwner = $isOwner = $business -> isOwner($viewer);
		$this->view->isAdmin = $isAdmin = $business -> isAdmin($viewer);
		/**
		 * ajax - for implement set mass action 
		 */
		$this->view->memberIds = $memberIds = $this->_getParam('member_id', array());
		$this->view->action = $action = $this->_getParam('mass_action', '');
		if (count($memberIds) > 0 && in_array($action, array('approve', 'reject', 'cancel')))
		{
			foreach ($memberIds as $memberId)
			{
				if ($memberId != '0')
				{
					if ($action == 'approve')
					{
						$business->approve($memberId);
					}
					else if ($action == 'reject')
					{
						$business->reject($memberId);
					}
					else 
					{
						$business->cancel($memberId);
					}
				}
			}
		}
		
		$listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
		$listTblName = $listTbl->info('name');
		
		// Prepare data
		$members = null;
		
		if( $viewer->getIdentity() && $isOwner ) 
		{
			$select = $business->membership()->getMembersObjectSelect(false);
			$select 
			-> setIntegrityCheck(false)
			-> join($listTblName, "engine4_ynbusinesspages_membership.list_id = {$listTblName}.list_id", array('role_name' => "{$listTblName}.name"))
			-> where("engine4_ynbusinesspages_membership.user_id = engine4_users.user_id");
			
			$this->view->waitingMembers = Zend_Paginator::factory($select);
			if( $waiting ) 
			{
				if ($this->view->waitingMembers->getTotalItemCount())
				{
					$this->view->members = $members = $this->view->waitingMembers;
				}
				else 
				{
					$this->view->members = $members = null;
					$this->view->waiting = false;
				}
			}
		}
		
		if( !$members ) 
		{
			$select = $business->membership()->getMembersObjectSelect();
			if( $search ) 
			{
				$select->where('displayname LIKE ?', '%' . $search . '%');
			}
			
			$select 
			-> setIntegrityCheck(false)
			-> join($listTblName, "engine4_ynbusinesspages_membership.list_id = {$listTblName}.list_id", array('role_name' => "{$listTblName}.name"))
			-> where("engine4_ynbusinesspages_membership.user_id = engine4_users.user_id");
			$this->view->members = $members = Zend_Paginator::factory($select);
		}
		
		$paginator = $members;
		
		// Set item count per page and current page number
		$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
		$paginator->setCurrentPageNumber($this->_getParam('page', $page));

		// Do not render if nothing to show
		if( $paginator->getTotalItemCount() <= 0 && '' == $search ) {
			return $this->setNoRender();
		}

		// Add count to title if configured
		if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
			$this->_childCount = $paginator->getTotalItemCount();
		}
		
	}

	public function getChildCount()
	{
		return $this->_childCount;
	}
}