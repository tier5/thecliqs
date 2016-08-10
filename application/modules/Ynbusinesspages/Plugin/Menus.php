<?php
class Ynbusinesspages_Plugin_Menus {
	public function onMenuInitialize_YnbusinesspagesMiniLoginasbusiness() {
		// Must be logged in
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !$viewer -> getIdentity()) {
			return false;
		}
		// check has business?
		$params['user_id'] = $viewer -> getIdentity();
		$table = Engine_Api::_() -> getDbTable('business', 'ynbusinesspages');
		$select = $table -> getBusinessesSelect($params);
		$business = $table -> fetchAll($select);
		if(!count($business))
		{
			return false;
		}
	
		return array(
            'label' => 'Login as Business',
            'class' => 'smoothbox',
            'title' => 'Login as Business',
            'route' => 'ynbusinesspages_general',
			'params' => array(
				'action' => 'login-as-business'
			)
        );
	}
	
	
	public function onMenuInitialize_YnbusinesspagesMainCreateBusiness()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		if( !Engine_Api::_()->authorization()->isAllowed('ynbusinesspages_business', $viewer, 'create') ) {
	      return false;
	    }
		return true;
	}
	
	public function onMenuInitialize_YnbusinesspagesManageAnnouncement()
	{
		// Get viewer, business and manage settings
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		
		// Must be a business
		if ($subject -> getType() !== 'ynbusinesspages_business')
		{
			throw new Ynbusinesspages_Model_Exception('Whoops, not a business!');
		}
		
		if(!$subject->isAllowed('manage_announcement'))
		{
			return false;
		}
		
		// Must be logged-in
		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return array(
			'label' => 'Manage Announcement',
			'icon' => 'application/modules/Ynbusinesspages/externals/images/announcement/manage.png',
			'route' => 'ynbusinesspages_announcement',
			'params' => array(
				'action' => 'manage',
				'business_id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesMainManageBusiness()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
        
		if( !Engine_Api::_()->authorization()->isAllowed('ynbusinesspages_business', $viewer, 'create') ) {
	      return false;
	    }
		return true;
	}
	
	public function onMenuInitialize_YnbusinesspagesBusinessEdit()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		$subject = Engine_Api::_() -> core() -> getSubject();
		
		if ($subject -> getType() !== 'ynbusinesspages_business')
		{
			throw new Ynbusinesspages_Model_Exception('Whoops, not a business!');
		}
		
		if(in_array($subject -> status, array('claimed', 'unclaimed', 'deleted')))
		{
			return false;
		}
		
		if( !$subject->isAllowed('edit') ) {
	      	return false;
	    }
				
		return array(
			'label' => 'Edit Business',
			'route' => 'ynbusinesspages_specific',
			'params' => array(
				'action' => 'edit',
				'business_id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesBusinessDashBoard()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		$subject = Engine_Api::_() -> core() -> getSubject();
		
		if ($subject -> getType() !== 'ynbusinesspages_business')
		{
			throw new Ynbusinesspages_Model_Exception('Whoops, not a business!');
		}
		
		if(in_array($subject -> status, array('claimed', 'unclaimed', 'deleted')))
		{
			return false;
		}
		
		if( !$subject->isAllowed('view_dashboard') ) {
	      	return false;
	    }
				
		return array(
			'label' => 'Dash Board',
			'route' => 'ynbusinesspages_dashboard',
			'params' => array(
				'business_id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesOpenCloseBusiness()
	{
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		$subject = Engine_Api::_() -> core() -> getSubject();
		
		if ($subject -> getType() !== 'ynbusinesspages_business')
		{
			throw new Ynbusinesspages_Model_Exception('Whoops, not a business!');
		}
		
		if(!in_array($subject -> status, array('closed', 'published')))
		{
			return false;
		}
		
		if( !$subject->isAllowed('edit') ) {
	      return false;
	    }
		
		if($subject -> status == 'closed')
		{
			$label = 'Open Business';
		}
		else 
		{
			$label = 'Close Business';
		}	
		return array(
			'label' => $label,
			'class' => 'smoothbox',
			'route' => 'ynbusinesspages_specific',
			'params' => array(
				'action' => 'open-close',
				'business_id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesDeleteBusiness()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		$subject = Engine_Api::_() -> core() -> getSubject();
		
		if ($subject -> getType() !== 'ynbusinesspages_business')
		{
			throw new Ynbusinesspages_Model_Exception('Whoops, not a business!');
		}
		
		if(in_array($subject -> status, array('claimed', 'unclaimed', 'deleted')))
		{
			return false;
		}
		
		if( !$subject->isAllowed('delete') ) {
	      	return false;
	    }
				
		return array(
			'label' => 'Delete Business',
			'class' => 'smoothbox',
			'route' => 'ynbusinesspages_specific',
			'params' => array(
				'action' => 'delete',
				'business_id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesFeatureBusiness()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		$subject = Engine_Api::_() -> core() -> getSubject();
		$view = Zend_Registry::get('Zend_View');
		if ($subject -> getType() !== 'ynbusinesspages_business')
		{
			throw new Ynbusinesspages_Model_Exception('Whoops, not a business!');
		}
		
		if(!in_array($subject -> status, array('published')))
		{
			return false;
		}
		
		if( !$subject->isAllowed('feature_business') ) {
	      	return false;
	    }
		
		return array(
			'label' => 'Feature Business',
			'route' => 'ynbusinesspages_dashboard',
			'params' => array(
				'action' => 'feature',
				'business_id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesMakePaymentBusiness()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		$subject = Engine_Api::_() -> core() -> getSubject();
		$view = Zend_Registry::get('Zend_View');
		if ($subject -> getType() !== 'ynbusinesspages_business')
		{
			throw new Ynbusinesspages_Model_Exception('Whoops, not a business!');
		}
		
		if(in_array($subject -> status, array('claimed', 'unclaimed', 'deleted')))
		{
			return false;
		}
		
		if( !$subject->isAllowed('edit') ) {
	      return false;
	    }
		return array(
			'label' => 'Make Payment',
			'route' => 'ynbusinesspages_dashboard',
			'params' => array(
				'action' => 'package',
				'business_id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesMakePaymentClaimBusiness()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        // Must be logged-in
        if (!$viewer -> getIdentity())
        {
            return false;
        }
		$subject = Engine_Api::_() -> core() -> getSubject();
		$view = Zend_Registry::get('Zend_View');
		if ($subject -> getType() !== 'ynbusinesspages_business')
		{
			throw new Ynbusinesspages_Model_Exception('Whoops, not a business!');
		}
		
		if(!in_array($subject -> status, array('claimed', 'unclaimed', 'deleted')))
		{
			return false;
		}
		
		$claimTable = Engine_Api::_() -> getDbTable('claimrequests', 'ynbusinesspages');
		$request = $claimTable -> getClaimRequest($viewer -> getIdentity(), $subject -> getIdentity());
		
		if(!empty($request))
		{
			switch ($request -> status) {
				case 'pending':
					return array(
						'message' => $view -> translate('YNBUSINESSPAGES_CLAIM_PENDING_MESSAGE'),
						'smoothbox' => 1,
						'class' => 'smoothbox fa fa-trash-o',
						'route' => 'ynbusinesspages_general',
						'label' => 'Delete Claim',
						'params' => array(
							'action' => 'delete-claim',
							'business_id' => $subject -> getIdentity(),
						)
					);
					break;
				case 'denied':
					return array(
						'message' => $view -> translate('YNBUSINESSPAGES_CLAIM_DENIED_MESSAGE'),
						'smoothbox' => 1,
						'class' => 'fa fa-trash-o',
						'label' => 'Delete Claim',
						'route' => 'ynbusinesspages_general',
						'params' => array(
							'action' => 'delete-claim',
							'business_id' => $subject -> getIdentity(),
						)
					);
					break;
				case 'approved':
					return array(
						'message' => $view -> translate('YNBUSINESSPAGES_CLAIM_APPROVE_MESSAGE'),
						'class' => 'fa fa-money',
						'label' => 'Make Payment',
						'route' => 'ynbusinesspages_dashboard',
						'params' => array(
							'action' => 'package',
							'business_id' => $subject -> getIdentity(),
						)
					);
					break;
				default:
					
					break;
			}
		}
		else 
		{
			return false;
		}
	}
	
	public function onMenuInitialize_YnbusinesspagesProfileMember()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();
		if( $subject->getType() !== 'ynbusinesspages_business' ) 
		{
			throw new Exception('Whoops, not a business!');
		}
		if( !$viewer->getIdentity() ) 
		{
			return false;
		}
		
		$package = $subject -> getPackage();
		if(!$package -> getIdentity()) 
		{
			return false;
		}
		
		if(!$package -> allow_user_join_business)
		{
			return false;
		}
		
		$row = $subject->membership()->getRow($viewer);
		
		// Not yet associated at all
		if( null === $row ) {
			if( $subject->membership()->isResourceApprovalRequired() ) 
			{
				return array(
		          'label' => 'Request Invite',
		          'icon' => 'application/modules/Ynbusinesspages/externals/images/member/join.png',
		          'class' => 'smoothbox',
		          'route' => 'ynbusinesspages_extended',
		          'params' => array(
			            'controller' => 'member',
			            'action' => 'request',
			            'business_id' => $subject->getIdentity(),
					),
				);
			} 
			else 
			{
				return array(
		          'label' => 'Join Business',
		          'icon' => 'application/modules/Ynbusinesspages/externals/images/member/join.png',
		          'class' => 'smoothbox',
		          'route' => 'ynbusinesspages_extended',
		          'params' => array(
			            'controller' => 'member',
			            'action' => 'join',
			            'business_id' => $subject->getIdentity()
					),
				);
			}
		}

		// Full member
		// @todo consider owner
		else if( $row->active ) 
		{
			if( !$subject->isOwner($viewer) ) 
			{
				return array(
		          'label' => 'Leave Business',
		          'icon' => 'application/modules/Ynbusinesspages/externals/images/member/leave.png',
		          'class' => 'smoothbox',
		          'route' => 'ynbusinesspages_extended',
		          'params' => array(
			            'controller' => 'member',
			            'action' => 'leave',
			            'business_id' => $subject->getIdentity()
					),
				);
			} 
			else 
			{
				return false;
			}
		} 
		else if( !$row->resource_approved && $row->user_approved ) 
		{
			return array(
		        'label' => 'Cancel Invite Request',
		        'icon' => 'application/modules/Ynbusinesspages/externals/images/member/cancel.png',
		        'class' => 'smoothbox',
		        'route' => 'ynbusinesspages_extended',
		        'params' => array(
			          'controller' => 'member',
			          'action' => 'cancel',
			          'business_id' => $subject->getIdentity()
				),
			);
		} 
		else if( !$row->user_approved && $row->resource_approved ) 
		{
			return array(
				array(
		          'label' => 'Accept Business Invite',
		          'icon' => 'application/modules/Ynbusinesspages/externals/images/member/accept.png',
		          'class' => 'smoothbox',
		          'route' => 'ynbusinesspages_extended',
		          'params' => array(
			            'controller' => 'member',
			            'action' => 'accept',
			            'business_id' => $subject->getIdentity()
					),
				), 
				array(
		          'label' => 'Ignore Business Invite',
		          'icon' => 'application/modules/Ynbusinesspages/externals/images/member/reject.png',
		          'class' => 'smoothbox',
		          'route' => 'ynbusinesspages_extended',
		          'params' => array(
			            'controller' => 'member',
			            'action' => 'reject',
			            'business_id' => $subject->getIdentity()
					),
				)
			);
		}
		else
		{
			throw new Exception('An error has occurred.');
		}
		return false;
	}
	
	public function onMenuInitialize_YnbusinesspagesProfileShare()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();
		if( $subject->getType() !== 'ynbusinesspages_business' )
		{
			throw new Exception('This business does not exist.');
		}

		if( !$viewer->getIdentity() )
		{
			return false;
		}
			
		$package = $subject -> getPackage();
		if(!$package -> getIdentity()) 
		{
			return false;
		}
		
		if(!$package -> allow_user_share_business)
		{
			return false;
		}
		
		return array(
	      'label' => 'Share This Business',
	      'icon' => 'application/modules/Ynbusinesspages/externals/images/share.png',
	      'class' => 'smoothbox',
	      'route' => 'default',
	      'params' => array(
		        'module' => 'activity',
		        'controller' => 'index',
		        'action' => 'share',
		        'type' => $subject->getType(),
		        'id' => $subject->getIdentity(),
		        'format' => 'smoothbox',
			),
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesProfilePromote()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'ynbusinesspages_business')
		{
			throw new Exception('This business does not exist.');
		}
		
		if($subject -> is_claimed)
		{
			return false;
		}
		
		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return array(
			'label' => 'Promote This Business',
			'icon' => 'application/modules/Ynbusinesspages/externals/images/promote.png',
			'class' => 'smoothbox',
			'route' => 'ynbusinesspages_specific',
			'params' => array(
				'action' => 'promote',
				'business_id' => $subject -> getIdentity(),
			),
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesProfileInvite()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'ynbusinesspages_business')
		{
			throw new Exception('This business does not exist.');
		}
		
		$package = $subject -> getPackage();
		if(!$package -> getIdentity()) 
		{
			return false;
		}
		
		if(!$package -> allow_user_invite_friend)
		{
			return false;
		}
		
		if (!$subject->isAllowed('invite'))
		{
			return false;
		}
		$class = 'smoothbox';
		$format = 'smoothbox';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$class = '';
			$format = '';
		}
		return array(
			'label' => 'Invite Guests',
			'icon' => 'application/modules/Ynbusinesspages/externals/images/member/invite.png',
			'class' => $class,
			'route' => 'ynbusinesspages_extended',
			'params' => array(
				'controller' => 'member',
				'action' => 'invite',
				'business_id' => $subject -> getIdentity(),
				'format' => $format,
			),
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesProfileReport()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$viewer->getIdentity() ) {
			return false;
		}

		if( !Engine_Api::_()->core()->hasSubject() ) {
			return false;
		}
		
		$subject = Engine_Api::_()->core()->getSubject();
		if( ($subject instanceof Ynbusinesspages_Model_Business) &&
		$subject->user_id == $viewer->getIdentity() ) 
		{
			return false;
		} 
		
		if($subject -> is_claimed)
		{
			return false;
		}
		
		// Modify params
		return array(
			'label' => 'Report this business',
			'icon' => '',
			'class' => 'smoothbox',
			'route' => 'default',
			'params' => array(
				'module' => 'core',
				'controller' => 'report',
				'action' => 'create',
				'subject' => $subject->getGuid(),
			),
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesProfileMessage()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$viewer->getIdentity() ) {
			return false;
		}

		if( !Engine_Api::_()->core()->hasSubject() ) {
			return false;
		}

		$subject = Engine_Api::_()->core()->getSubject();
		if( ($subject instanceof Ynbusinesspages_Model_Business) &&
		$subject->user_id == $viewer->getIdentity() ) 
		{
			return false;
		} 
		
		if($subject -> is_claimed)
		{
			return false;
		}
		
		// Modify params
		return array(
			'label' => 'Message Owner',
			'class' => 'smoothbox icon_message',
			'route' => 'ynbusinesspages_general',
			'params' => array(
				'action' => 'compose-message',
				'to' => $subject->user_id,
			),
		);
	}
	
	public function onMenuInitialize_YnbusinesspagesProfileCheckin()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if( !$viewer->getIdentity() ) {
			return false;
		}

		if( !Engine_Api::_()->core()->hasSubject() ) {
			return false;
		}

		$subject = Engine_Api::_()->core()->getSubject();
		/*
		if( ($subject instanceof Ynbusinesspages_Model_Business) &&
		$subject->user_id == $viewer->getIdentity() ) 
		{
			return false;
		}
		*/
		if ($subject -> isCheckedIn($viewer))
		{
			return false;
		}
		if($subject -> is_claimed)
		{
			return false;
		}
		return array(
			'label' => 'Check-in Here',
			'class' => 'smoothbox',
			'route' => 'ynbusinesspages_specific',
			'params' => array(
				'action' => 'checkin',
				'business_id' => $subject -> getIdentity(),
			),
		);
		
	}
}
