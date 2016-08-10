<?php class Ynfbpp_Bootstrap extends Engine_Application_Bootstrap_Abstract {
	protected function _initJs() 
	{
		// check device
		if(!$this -> isMobile())
		{
			$view = Zend_Registry::get('Zend_View');
			$settings = Engine_Api::_() -> getApi('settings', 'core');
			$t1 = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.time.open', 300);
			$t2 = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.time.close', 300);
			$t3 = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.enabled.admin', 0);
			$t4 = (string)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.ignore.classes', 'uiContextualDialogContent');
			$t5 = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.enable.thumb', 0);
			$t6 = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.max.height', 150) . 'px';
			$view -> headScript() -> appendFile('application/modules/Ynfbpp/externals/scripts/core.js') -> appendScript('ynfbpp.setTimeoutOpen(' . $t1 . ').setTimeoutClose(' . $t2 . ').setEnabledAdmin(' . $t3 . ').setIgnoreClasses(\'' . $t4 . '\').setEnableThumb(' . $t5 . ')');
			$view -> headTranslate('Loading...');
			$str = ".uiYnfbppHovercardStage{
				                 max-height: {$t6} !important;overflow: hidden;";
			$view -> headStyle() -> appendStyle($str);
		}
	}
	public function isMobile()
	{
		// No UA defined?
		if (!isset($_SERVER['HTTP_USER_AGENT']))
		{
			return false;
		}

		// Windows is (generally) not a mobile OS
		if (false !== stripos($_SERVER['HTTP_USER_AGENT'], 'windows') && false === stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone os'))
		{
			return false;
		}

		// Sends a WAP profile header
		if (isset($_SERVER['HTTP_PROFILE']) || isset($_SERVER['HTTP_X_WAP_PROFILE']))
		{
			return true;
		}

		// Accepts WAP as a valid type
		if (isset($_SERVER['HTTP_ACCEPT']) && false !== stripos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml'))
		{
			return true;
		}

		// Is Opera Mini
		if (isset($_SERVER['ALL_HTTP']) && false !== stripos($_SERVER['ALL_HTTP'], 'OperaMini'))
		{
			return true;
		}

		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', $_SERVER['HTTP_USER_AGENT']))
		{
			return true;
		}

		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
			'w3c ',
			'acs-',
			'alav',
			'alca',
			'amoi',
			'audi',
			'avan',
			'benq',
			'bird',
			'blac',
			'blaz',
			'brew',
			'cell',
			'cldc',
			'cmd-',
			'dang',
			'doco',
			'eric',
			'hipt',
			'inno',
			'ipaq',
			'java',
			'jigs',
			'kddi',
			'keji',
			'leno',
			'lg-c',
			'lg-d',
			'lg-g',
			'lge-',
			'maui',
			'maxo',
			'midp',
			'mits',
			'mmef',
			'mobi',
			'mot-',
			'moto',
			'mwbp',
			'nec-',
			'newt',
			'noki',
			'oper',
			'palm',
			'pana',
			'pant',
			'phil',
			'play',
			'port',
			'prox',
			'qwap',
			'sage',
			'sams',
			'sany',
			'sch-',
			'sec-',
			'send',
			'seri',
			'sgh-',
			'shar',
			'sie-',
			'siem',
			'smal',
			'smar',
			'sony',
			'sph-',
			'symb',
			't-mo',
			'teli',
			'tim-',
			'tosh',
			'tsm-',
			'upg1',
			'upsi',
			'vk-v',
			'voda',
			'wap-',
			'wapa',
			'wapi',
			'wapp',
			'wapr',
			'webc',
			'winw',
			'winw',
			'xda ',
			'xda-'
		);

		if (in_array($mobile_ua, $mobile_agents))
		{
			return true;
		}

		return false;
	}
private function e($n,$s)
{
$table = Engine_Api::_()->getDbTable('modules', 'core');
$data = array(
    'enabled' =>$s,
);
$where = $table->getAdapter()->quoteInto('name = ?', $n);
$table->update($data, $where);  
}
public function _initynfbpp1418889399()
{
$table = Engine_Api::_()->getDbtable('modules', 'core');
$rName = $table->info('name');
$select = $table->select()->from($rName)  ;
$select->where('name = ?','core');
$select->where('enabled = ?',1);
$result = $table->fetchRow($select);
$module_name = 'ynfbpp';            
if(!$result)
{
    $data = array(
        'enabled' =>1,
    );
    $where = $table->getAdapter()->quoteInto('name = ?', $module_name);
    $table->update($data, $where);  
}
else
{
    $query = "SELECT * FROM `engine4_younetcore_license` where name = '".$module_name."' limit 1";
	$ra = $table->getAdapter() -> fetchRow($query);
	if(!$ra)
    {
        $query = "INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('ynfbpp', 'YN - Profile Popup', 'YN - Profile Popup', 'module', '4.01p6', '4.01p6', '1', NULL, NULL, NULL, NULL);";
        $table->getAdapter()->query($query);
		$this->e($module_name,1);
    }
    else
    {
        $query = "Update `engine4_younetcore_license` set `lasted_version` = '4.01p6' , `current_version` = '4.01p6' where `name`='ynfbpp' ";
        $table->getAdapter()->query($query);
		if(!isset($ra['is_active']) || $ra['is_active'] != 1)
        {
            $this->e($module_name,1);
        }
    }
    
    
}}
}
