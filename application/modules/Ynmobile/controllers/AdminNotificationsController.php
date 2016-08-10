<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2014 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: AdminNotificationsController.php minhnc $
 * @author     MinhNC
 */
class Ynmobile_AdminNotificationsController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynmobile_admin_main', array(), 'ynmobile_admin_main_notifications');
		$settings = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmobile_notifications','');
		// Build the different notification types
		$modules = Engine_Api::_() -> getDbtable('modules', 'core') -> getModulesAssoc();
		$notificationTypes = Engine_Api::_() -> getDbtable('notificationTypes', 'activity') -> getNotificationTypes();
		
		$notificationTypesAssoc = array();
		$notificationSettingsAssoc = array();
		$notificationSettings = Zend_Json::decode($settings);
		foreach ($notificationTypes as $type)
		{
			if (in_array($type -> module, array(
				'core',
				'activity',
				'fields',
				'authorization',
				'messages',
				'user'
			)))
			{
				$elementName = 'general';
				$category = 'General';
			}
			else
			if (isset($modules[$type -> module]))
			{
				$elementName = preg_replace('/[^a-zA-Z0-9]+/', '-', $type -> module);
				$category = $modules[$type -> module] -> title;
			}
			else
			{
				$elementName = 'misc';
				$category = 'Misc';
			}

			$notificationTypesAssoc[$elementName]['category'] = $category;
			$notificationTypesAssoc[$elementName]['types'][$type -> type] = 'ACTIVITY_TYPE_' . strtoupper($type -> type);

			if (!in_array($type -> type, $notificationSettings))
			{
				$notificationSettingsAssoc[$elementName][] = $type -> type;
			}
		}

		ksort($notificationTypesAssoc);

		$notificationTypesAssoc = array_filter(array_merge(array(
			'general' => array(),
			'misc' => array(),
		), $notificationTypesAssoc));

		// Make form
		$this -> view -> form = $form = new Engine_Form( array(
			'title' => 'Notification Settings',
			'description' => 'Which of the these do you want to receive notifications about?',
		));

		foreach ($notificationTypesAssoc as $elementName => $info)
		{
			$form -> addElement('MultiCheckbox', $elementName, array(
				'label' => $info['category'],
				'multiOptions' => $info['types'],
				'value' => (array)@$notificationSettingsAssoc[$elementName],
			));
		}

		$form -> addElement('Button', 'execute', array(
			'label' => 'Save Changes',
			'type' => 'submit',
		));

		// Check method
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		
		// Process
		$enabledTypes = array();
		$values = array();
		foreach ($form->getValues() as $key => $value)
		{
			if (!is_array($value))
				continue;

			foreach ($value as $skey => $svalue)
			{
				if (!isset($notificationTypesAssoc[$key]['types'][$svalue]))
				{
					continue;
				}
				$enabledTypes[] = $svalue;
			}
		}
		foreach( $notificationTypes as $type )
    {
      if(!in_array($type->type, $enabledTypes))
      {
        $values[] = $type->type;
      }
    }
		// Set notification setting
		Engine_Api::_() -> getApi('settings', 'core') -> setSetting('ynmobile_notifications', Zend_Json::encode($values));
		
		$form -> addNotice('Your changes have been saved.');
	}

}
