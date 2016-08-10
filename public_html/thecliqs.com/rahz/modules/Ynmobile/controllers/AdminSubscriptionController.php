<?php
class Ynmobile_AdminSubscriptionController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('ynmobile_admin_main', array(), 'ynmobile_admin_main_subscription');

		$packageTable = Engine_Api::_() -> getDbtable('packages', 'payment');
    	$select = $packageTable -> select();
    	$this->view->packages = $packages = $packageTable -> fetchAll($select);
		
		$this->view->form  = $form = new Ynmobile_Form_Admin_Product();
		$storekitTable = Engine_Api::_() -> getDbtable('storekitpurchases', 'ynmobile');
		
		if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
		{
			$values = $form->getValues();
			$appType = $values['storekitpurchase_type'];
			
			foreach ($values as $k => $v)
			{
				if (strpos($k, "package_") !== false)
				{
					$packageId = intval(str_replace("package_", "", $k));
					$product = 	$storekitTable->getProduct($appType, $packageId);
					if ($product)
					{
						$product -> storekitpurchase_key = $v;
					}				
					else
					{
						$product = $storekitTable -> createRow();
						$product -> setFromArray(array(
							'storekitpurchase_key' => $v,
							'storekitpurchase_module_id' => 'payment',
							'storekitpurchase_type' => $appType,
							'storekitpurchase_item_id' => $packageId
						));
					}
					$product -> save();
				}	
			}
			$form->addNotice('Your changes have been saved.');
		}
	}
}