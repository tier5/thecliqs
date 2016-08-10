<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
class Ynprofilestyler_Plugin_Menus
{
	/**
	 * allow only onwer and users have the profile style editing
	 */
	public function onMenuInitialize_YnprofilestylerProfileEdit($row)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();
		
		if ($viewer->isSelf($subject))
		{
			if (Engine_Api::_()->authorization()->isAllowed('theme', null, 'edit'))
			{
				$style = Engine_Api::_()->ynprofilestyler()->getProfileStyle($subject);
				
				$view = Zend_Registry::get('Zend_View');
				$translate = Zend_Registry::get('Zend_Translate');
				$view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Ynprofilestyler/externals/scripts/core.js');

				$trans = array(
					'changeTheme' => $translate->_('Do you want to save changes your theme ?'),
				);
				
				$view->headScript()->appendScript('var ynpsPackageTrans = ' . (Zend_Json::encode($trans)));

				$request = Zend_Controller_Front::getInstance()->getRequest();
				$isAdminEdit = $request->getParam('adminEditing', 0);
                
				if ($isAdminEdit || $request->getParam('edit-style',0)==1)
				{
					$view->headScript()->appendScript("
                    	window.addEvent('domready',function(e) {
                    		ynps.open('$style');
                    	});
                    ");
				}
				return array(
				    'icon' => 'application/modules/Ynprofilestyler/externals/images/icon.png',
					'label' => $row->label,
					'class' => 'no-dloader',
					'uri'   => "javascript: ynps.open(" . json_encode(array('style' => $style)) . ")",
				);
			}
		}
	}

	public function onMenuInitialize_YnprofilestylerApplyLayout($row)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();

		if (Engine_Api::_()->ynprofilestyler()->isUserLayoutAllowedApply($subject->getIdentity()))
		{
		    $view = Zend_Registry::get('Zend_View');
			$translate = Zend_Registry::get('Zend_Translate');
				
			return array(
				'icon' => 'application/modules/Ynprofilestyler/externals/images/icon.png',
				'label' => $row->label,
				'class' => 'no-dloader',
				'uri'   => 'javascript: ynps.useLayout(' . $subject->getIdentity() . ')',
			);
		}
	}
}