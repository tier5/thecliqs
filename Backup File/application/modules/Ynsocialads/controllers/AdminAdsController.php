<?php
class Ynsocialads_AdminAdsController extends Core_Controller_Action_Admin
{

	public function init()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_ads');
		$viewer = Engine_Api::_() -> user() -> getViewer();
	}

	public function indexAction()
	{
		$this -> view -> form = $form = new Ynsocialads_Form_Admin_Ads_Search();
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$this -> view -> formValues = $params;
		if(!empty($params['campaign_id']))
		{
			$campaign = Engine_Api::_() -> getItem('ynsocialads_campaign', $params['campaign_id']);
			if($campaign)
			{
				$form -> name -> setValue($campaign -> title);
			}
		}
		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = Engine_Api::_() -> getItemTable('ynsocialads_ad') -> getAdsPaginator($params);
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);
	}

	public function deleteSelectedAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));

		// Check post
		if ($this -> getRequest() -> isPost() && $confirm == true)
		{
			//Process delete
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try
			{
				$ids_array = explode(",", $ids);
				foreach ($ids_array as $id)
				{
					$ads = Engine_Api::_() -> getItem('ynsocialads_ad', $id);
					if ($ads)
					{
						$ads -> status = 'deleted';
						$ads -> deleted = true;
						$ads -> save();
						//Add notification
						$user = Engine_Api::_() -> user() -> getUser($ads -> user_id);
						$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
						$notifyApi -> addNotification($user, $viewer, $ads, 'ynsocialads_admin_ad_delete');
					}
				}
				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _helper -> redirector -> gotoRoute(array('action' => ''));
		}
	}

	public function editAction()
	{
		$ads = Engine_Api::_() -> getItem('ynsocialads_ad', $this -> getParam('id'));
		// Get form
		$this -> view -> form = $form = new Ynsocialads_Form_Admin_Ads_Edit();
		$form -> populate($ads -> toArray());

		// Check stuff
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		// Save
		$values = $form -> getValues();
		$ads -> name = $values['name'];
		$ads -> save();

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Ads Edited.')),
			'format' => 'smoothbox',
			'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}

	public function updateStatusAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$status = $this -> _getParam('status');
		$this -> view -> ads_id = $id;
		$this -> view -> status = $status;
		// Check post
		if ($this -> getRequest() -> isPost())
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try
			{
				$ads = Engine_Api::_() -> getItem('ynsocialads_ad', $id);

				$create_date = strtotime($ads -> creation_date);
				$start_date = strtotime($ads -> start_date);
				$end_date = strtotime($ads -> end_date);
				$current_date = $today = strtotime(date("Y-m-d H:i:s"));
				if ($ads)
				{
					//Add notification
					$user = Engine_Api::_() -> user() -> getUser($ads -> user_id);
					$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');

					if ($status == "Delete")
					{
						$ads -> status = 'deleted';
						$ads -> deleted = true;
						$notifyApi -> addNotification($user, $viewer, $ads, 'ynsocialads_admin_ad_delete');
					}
					if ($status == "Approve")
					{
					    $ads->approve();
					}
					if ($status == "Deny")
					{
						$ads -> status = 'denied';
						$ads -> approved = -1;
						$notifyApi -> addNotification($user, $viewer, $ads, 'ynsocialads_admin_ad_deny');
					}
					if ($status == "Pause")
					{
						$ads -> status = 'paused';
						$notifyApi -> addNotification($user, $viewer, $ads, 'ynsocialads_admin_ad_pause');
					}
					if ($status == "Resume")
					{
						$ads -> status = 'running';
						$notifyApi -> addNotification($user, $viewer, $ads, 'ynsocialads_admin_ad_resume');
					}
					$ads -> save();
				}
				$db -> commit();
			}

			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 100,
				'parentRefresh' => 100,
				'messages' => array('')
			));
		}

		// Output
		$this -> renderScript('admin-ads/update-status.tpl');
	}

}
