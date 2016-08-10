<?php
class Yncredit_Model_Balance extends Core_Model_Item_Abstract
{
	protected $_shortType = "user";
	public function saveCredits($credits, $group = 'earn')
	{
		$this->current_credit += $credits;
		switch ($group) {
			case 'earn':
				$this->earned_credit += $credits;
				break;
			case 'spend':
				$this->spent_credit -= $credits;
				break;
			case 'buy':
				$this->bought_credit += $credits;
				break;
			case 'send':
				$this->sent_credit -= $credits;
				break;
			case 'receive':
				$this->received_credit += $credits;
				break;
			
		}
		$this->save();
	}

	public function getHref() {
		$params = array(
			'route' => 'yncredit_my',
			'reset' => true,
		);

		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance()->getRouter()
			->assemble($params, $route, $reset);
	}

	public function getTitle() {
		return '';
	}
}