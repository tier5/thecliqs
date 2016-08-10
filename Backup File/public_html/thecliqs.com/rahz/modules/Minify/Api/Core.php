<?php

class Minify_Api_Core {

	/**
	 * @var array
	 */
	protected $_config = null;

	/**
	 * @var array
	 */
	protected $_allJsItems = null;

	/**
	 * @var array
	 */
	protected $_allCssItems = null;

	/**
	 * @var array
	 */
	protected $_allJsGroups = null;

	/**
	 * @var array
	 */
	protected $_allCssGroups = null;

	/**
	 * @var int
	 */
	protected $_maxCombinedFile = 9;

	/**
	 * @var int
	 */
	protected $_siteCounter = 1;

	/**
	 * @var bool
	 */
	protected $_enableJs = false;

	/**
	 * @var bool
	 */
	protected $_enableCss = false;

	/**
	 * @return int
	 */
	function getMaxCombinedFile() {
		return $this -> _maxCombinedFile;
	}

	/**
	 * @return int
	 */
	function getSiteCounter() {
		return $this -> _siteCounter;
	}

	/**
	 * @return bool
	 */
	public function minifyJs() {
		return $this -> _enableJs;
	}

	/**
	 * @return bool
	 */
	function minifyCss() {
		return $this -> _enableCss;
	}

	/**
	 * constructor
	 */
	public function __construct() {
		$settingApi = Engine_Api::_() -> getApi('settings', 'core');
		$this -> _siteCounter = $settingApi -> getSetting('core.site.counter', 1);
		$this -> _maxCombinedFile = $settingApi -> getSetting('minify.maxcombinedjs.enable', 9);
		$this -> _enableCss = $settingApi -> getSetting('minify.mincss.enable', 1);
		$this -> _enableJs = $settingApi -> getSetting('minify.minjs.enable', 1);
	}

	/**
	 * @param array $data
	 * @return ftp
	 */
	public function writeMinifySetting($data) {
		$filename = APPLICATION_PATH . '/temporary/ynminifysettings.php';
		if ($fp = fopen($filename, 'w')) {
			fwrite($fp, '<?php return ' . var_export($data, true) . ';?>');
			fclose($fp);
			return true;
		}
	}

	public function readMinifySetting() {
		return array();
		if (is_readable(APPLICATION_PATH . '/temporary/ynminifysettings.php')) {
			$minifyConfig = (
			include APPLICATION_PATH . '/temporary/ynminifysettings.php');

			return $minifyConfig;
		}
		return array();
	}

	public function getConfig() {
		if (null === $this -> _config) {
			$this -> _config = $this -> readMinifySetting();
		}
		return $this -> _config;
	}

	/**
	 * @return array
	 */
	function getAllJsGroups() {
		
		if (null === $this -> _allJsGroups) {
			$this -> _allJsGroups = array();
			$config = $this -> getConfig();
			if (isset($config['groups'])) {
				foreach ($config['groups'] as $groupKey => $groupValue) {
					if (strpos($groupKey,'js') !== false && !empty($groupValue) && !empty($groupValue[0])) {
						$this -> _allJsGroups[$groupKey] = $groupValue;
					}
				}
			}
		}
		return $this -> _allJsGroups;
	}

	/**
	 * @return array
	 */
	function getAllCssGroups() {
		
		if (null === $this -> _allCssGroups) {
			$this -> _allCssGroups = array();
			$config = $this -> getConfig();

			if (isset($config['groups'])) {
				foreach ($config['groups'] as $groupKey => $groupValue) {
					
					if (strpos($groupKey, 'css') !== false && !empty($groupValue)&& !empty($groupValue[0])) {
						$this -> _allCssGroups[$groupKey] = $groupValue;
					}
				}
			}
		}
		return $this -> _allCssGroups;
	}

	function getAllJsItems() {
		
		if (null === $this -> _allJsItems) {
			$this -> _allJsItems = array();
			$allGroups = $this -> getAllJsGroups();
			foreach ($allGroups as $groupKey => $groupValue) {
				foreach ($groupValue as $src) {
					$this -> _allJsItems[$src] = $groupKey;
				}
			}
		}
		return $this -> _allJsItems;
	}

	function getAllCssItems() {
		return array();
		if (null === $this -> _allCssItems) {
			$this -> _allCssItems = array();
			$allGroups = $this -> getAllCssGroups();
			foreach ($allGroups as $groupKey => $groupValue) {
				foreach ($groupValue as $src) {
					$this -> _allCssItems[$src] = $groupKey;
				}
			}
		}
		return $this -> _allCssItems;
	}

}
