<?php
class Viewed_AdminPackageController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('whoviewedme_admin_main', array(), 'whoviewedme_admin_main_settings');
		// Test curl support
		if( !function_exists('curl_version') ||
				!($info = curl_version()) ) {
			$this->view->error = $this->view->translate('The PHP extension cURL ' .
					'does not appear to be installed, which is required ' .
					'for interaction with payment gateways. Please contact your ' .
					'hosting provider.');
		}
		// Test curl ssl support
		else if( !($info['features'] & CURL_VERSION_SSL) ||
				!in_array('https', $info['protocols']) ) {
			$this->view->error = $this->view->translate('The installed version of ' .
					'the cURL PHP extension does not support HTTPS, which is required ' .
					'for interaction with payment gateways. Please contact your ' .
					'hosting provider.');
		}
		// Check for enabled payment gateways
		else if( Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ) {
			$this->view->error = $this->view->translate('There are currently no ' .
					'enabled payment gateways. You must %1$sadd one%2$s before this ' .
					'page is available.', '<a href="' .
					$this->view->escape($this->view->url(array('controller' => 'gateway'))) .
					'">', '</a>');
		}
	
	
	
		// Make form
		$this->view->formFilter = $formFilter = new Payment_Form_Admin_Package_Filter();
	
		// Process form
		if( $formFilter->isValid($this->_getAllParams()) ) {
			if( null === $this->_getParam('enabled') ) {
				$formFilter->populate(array('enabled' => 1));
			}
			$filterValues = $formFilter->getValues();
		} else {
			$filterValues = array(
					'enabled' => 1,
			);
			$formFilter->populate(array('enabled' => 1));
		}
		if( empty($filterValues['order']) ) {
			$filterValues['order'] = 'package_id';
		}
		if( empty($filterValues['direction']) ) {
			$filterValues['direction'] = 'DESC';
		}
		$this->view->filterValues = $filterValues;
		$this->view->order = $filterValues['order'];
		$this->view->direction = $filterValues['direction'];
	
		// Initialize select
		$table = Engine_Api::_()->getDbtable('packages', 'payment');
		$select = $table->select();
	
		// Add filter values
		if( !empty($filterValues['query']) ) {
			$select->where('title LIKE ?', '%' . $filterValues['package_id'] . '%');
		}
		if( !empty($filterValues['level_id']) ) {
			$select->where('level_id = ?', $filterValues['level_id']);
		}
		if( isset($filterValues['enabled']) && '' != $filterValues['enabled'] ) {
			$select->where('enabled = ?', $filterValues['enabled']);
		}
		if( isset($filterValues['signup']) && '' != $filterValues['signup'] ) {
			$select->where('signup = ?', $filterValues['signup']);
		}
		if( !empty($filterValues['order']) ) {
			if( empty($filterValues['direction']) ) {
				$filterValues['direction'] = 'ASC';
			}
			$select->order($filterValues['order'] . ' ' . $filterValues['direction']);
		}
	
		// Make paginator
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
	
		// Get member totals for each plan
		$memberCounts = array();
		foreach( $paginator as $item ) {
			$memberCounts[$item->package_id] = Engine_Api::_()->getDbtable('subscriptions', 'payment')
			->select()
			->from('engine4_payment_subscriptions', new Zend_Db_Expr('COUNT(*)'))
			->where('package_id = ?', $item->package_id)
			->where('active = ?', true)
			->where('status = ?', 'active')
			->query()
			->fetchColumn();
		}
		$this->view->memberCounts = $memberCounts;
	}
}