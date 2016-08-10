<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminSettingsController.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Minify_AdminSettingsController extends Core_Controller_Action_Admin
{

	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('minify_admin_main', array(), 'minify_admin_main_settings');
		// Make form
		$this -> view -> form = $form = new Minify_Form_Admin_Global();
		$settings = Engine_Api::_() -> getApi('settings', 'core');

		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
		{

			$values = $form -> getValues();
			foreach ($values as $key => $value)
			{
				$key = $key . ".enable";
				$settings -> setSetting(str_replace('_', '.', $key), (int)$value);
			}
			$form -> addNotice('Your changes have been saved.');
		}

	}

	public function groupsAction()
	{

		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('minify_admin_main', array(), 'minify_admin_main_groups');

		$form = $this -> view -> form = new Minify_Form_Admin_Groups;

		if ($this -> getRequest() -> isGet())
		{
			if ($this -> checkWriteable() == false)
			{
				$filename = APPLICATION_PATH . '/temporary/yn_minify.php';
				if(is_file($filename))
					$form -> addNotice('Warning: '. $filename .' is not writable.');
			}
			else
			{
				$setting = $this -> readMinifySetting();
				if (isset($setting['groups']))
				{
					foreach ($setting['groups'] as $key => $arr)
					{
						if ($form -> getElement($key))
						{
							$form -> getElement($key) -> setValue(implode(PHP_EOL, $arr));
						}
					}
				}
			}

		}

		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
		{
			$values = $form -> getValues();

			$arr = array();
			foreach ($values as $key => $value)
			{
				if ($values != '')
				{
					$arr[$key] = explode("\n", trim($value));
				}
				else
					$arr[$key] = array();
			}

			foreach ($arr as $key => $val)
			{
				foreach ($val as $k => $v)
				{
					$arr[$key][$k] = trim($v);
				}
			}

			$arr['groups'] = $arr;

			$filename = APPLICATION_PATH . '/temporary/yn_minify.php';
			if ($fp = fopen($filename, 'w'))
			{
				fwrite($fp, '<?php return ' . var_export($arr, true) . ';?>');
				fclose($fp);
			}
			$form -> addNotice('Your changes have been saved.');
		}
	}

	public function checkWriteable()
	{
		$filename = APPLICATION_PATH . '/temporary/yn_minify.php';
		if (!file_exists($filename))
		{
			if (!is_writable(dirname($filename)))
			{
				return false;
			}
		}
		else
		if (is_writable($filename))
		{
			return TRUE;
		}
		return false;
	}

	public function writeMinifySetting($data)
	{
		$filename = APPLICATION_PATH . '/temporary/yn_minify.php';
		$fp = fopen($filename, 'w');
		fwrite($fp, '<?php return ' . var_export($data, true) . ';?>');
		fclose($fp);
	}

	public function readMinifySetting()
	{
		if (is_readable(APPLICATION_PATH . '/temporary/yn_minify.php'))
		{
			$minifyConfig = (
			include APPLICATION_PATH . '/temporary/yn_minify.php');
			return $minifyConfig;
		}
		return array();
	}

}
