<?php
class Ynjobposting_Plugin_Company_Menus
{
	public function onMenuInitialize_YnjobpostingProfileCompanySponsor()
	{
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}
		
		if (!$subject->isSponsorable()) {
            return false;
        }
				
		return array(
			'label' => 'Sponsor',
			'class' => 'smoothbox',
			'route' => 'ynjobposting_extended',
			'params' => array(
				'controller' => 'company',
				'action' => 'sponsor',
				'id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnjobpostingProfileCompanyEdit()
	{
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}
		
		if (!$subject->isEditable())
		{
			return false;
		}
				
		return array(
			'label' => 'Edit Company Info',
			'route' => 'ynjobposting_extended',
			'params' => array(
				'controller' => 'company',
				'action' => 'edit',
				'id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnjobpostingProfileCompanyEditSubmissionForm()
	{
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}
		
		if (!$viewer -> isSelf($subject -> getOwner()))
		{
			return false;
		}
				
		return array(
			'label' => 'Edit Submission Form',
			'route' => 'ynjobposting_extended',
			'params' => array(
				'controller' => 'submission',
				'action' => 'edit',
				'id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnjobpostingProfileCompanyManagePostedJob()
	{
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}
		
		if (!$viewer -> isSelf($subject -> getOwner()))
		{
			return false;
		}
				
		return array(
			'label' => 'Manage Posted Jobs',
			'route' => 'ynjobposting_extended',
			'params' => array(
				'controller' => 'company',
				'action' => 'manage-jobs',
				'company_id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnjobpostingProfileCompanyViewApplications()
	{
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}
		
		if (!$viewer -> isSelf($subject -> getOwner()))
		{
			return false;
		}
				
		return array(
			'label' => 'View Applications',
			'route' => 'ynjobposting_extended',
			'params' => array(
				'controller' => 'jobs',
				'action' => 'applications',
				'company_id' => $subject -> getIdentity(),
				//'reset' => true
			)
		);
	}
	
	public function onMenuInitialize_YnjobpostingProfileCompanyClose()
	{
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}
		
		if (!$subject->isClosable())
		{
			return false;
		}
		
		if($subject -> status == 'closed')
		{
			$label = 'Publish This Company';
			$status = 'published';
		}
		elseif($subject -> status == 'published')
		{
			$label = 'Close This Company';
			$status = 'closed';
		}
		else
		{
			return false;
		}
			
		return array(
			'label' => $label,
			'class' => 'smoothbox',
			'route' => 'ynjobposting_extended',
			'params' => array(
				'controller' => 'company',
				'action' => 'update-status',
				'status' => $status,
				'id' => $subject -> getIdentity(),
			)
		);
	}
	
	public function onMenuInitialize_YnjobpostingProfileCompanyDelete()
	{
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}
		
		if (!$subject->isDeletable())
		{
			return false;
		}
		
		return array(
			'label' => 'Delete This Company',
			'class' => 'smoothbox',
			'route' => 'ynjobposting_extended',
			'params' => array(
				'controller' => 'company',
				'action' => 'update-status',
				'status' => 'deleted',
				'id' => $subject -> getIdentity(),
			)
		);
	}
	
	//Share Tab
	public function onMenuInitialize_YnjobpostingProfileCompanyShare()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}
		
		return array(
			'label' => 'Share',
			'class' => 'smoothbox',
			'route' => 'default',
			'params' => array(
				'module' => 'activity',
				'controller' => 'index',
				'action' => 'share',
				'type' => $subject -> getType(),
				'id' => $subject -> getIdentity(),
				'format' => 'smoothbox',
			),
		);
	}

	//Report Tab
	public function onMenuInitialize_YnjobpostingProfileCompanyReport()
	{
		// Get viewer and group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		//Must be a group
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}
		/*
		if ($viewer -> isSelf($subject -> getOwner()))
		{
			return false;
		}
		*/
		return array(
			'label' => 'Report',
			'class' => 'smoothbox',
			'route' => 'default',
			'params' => array(
				'module' => 'core',
				'controller' => 'report',
				'action' => 'create',
				'subject' => $subject->getGuid(),	
				'format' => 'smoothbox',
			),
		);
	}
	
	public function onMenuInitialize_YnjobpostingProfileCompanyFollow()
	{
		// Get viewer and group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		//Must be a group
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}
		
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		
		if ($viewer -> isSelf($subject -> getOwner()))
		{
			return false;
		}
		$tableFollow = Engine_Api::_() -> getItemTable('ynjobposting_follow');
		$followRow = $tableFollow -> getFollowBy($subject -> getIdentity(), $viewer -> getIdentity());
		
		$label = "";
		if(isset($followRow))
		{
			if($followRow -> active == 1)
			{
				$label = 'Unfollow';
			}	
			else 
			{
				$label = 'Follow';
			}
		}
		else 
		{
			$label = 'Follow';
		}
		
		return array(
			'label' => $label,
			'class' => 'smoothbox',
			'route' => 'ynjobposting_extended',
			'params' => array(
				'controller' => 'company',
				'action' => 'follow',
				'id' => $subject->getIdentity(),	
				'format' => 'smoothbox',
			),
		);
	}

	public function onMenuInitialize_YnjobpostingProfileCompanyContact()
	{
		// Get viewer and group
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		//Must be a group
		if ($subject -> getType() !== 'ynjobposting_company')
		{
			throw new Ynjobposting_Model_Exception('Whoops, not a company!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}
		
		if ($viewer -> isSelf($subject -> getOwner()))
		{
			return false;
		}
		$href = 'mailto:'. $subject -> contact_email; 
		return array(
			'label' => 'Contact Us',
			'href' => $href,
			'params' => array(
				'controller' => 'company',
				'action' => 'follow',
				'id' => $subject->getIdentity(),	
				'format' => 'smoothbox',
			),
		);
	}
}
