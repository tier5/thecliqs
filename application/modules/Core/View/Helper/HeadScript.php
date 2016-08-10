<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc.
 * (http://www.zend.com)
 * @version    $Id: HeadScript.php 16971 2009-07-22 18:05:45Z mikaelkael $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder_Container_Standalone */
require_once 'Zend/View/Helper/Placeholder/Container/Standalone.php';

/**
 * Helper for setting and retrieving script elements for HTML head section
 *
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc.
 * (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Core_View_Helper_HeadScript extends Zend_View_Helper_Placeholder_Container_Standalone {

	/**
	 * Registry key for placeholder
	 * @var string
	 */
	protected $_regKey = 'Zend_View_Helper_HeadScript';

	/**
	 * Are arbitrary attributes allowed?
	 * @var bool
	 */
	protected $_arbitraryAttributes = false;

	/**#@+
	 * Capture type and/or attributes (used for hinting during capture)
	 * @var string
	 */
	protected $_captureLock;
	protected $_captureScriptType = null;
	protected $_captureScriptAttrs = null;
	protected $_captureType;
	protected $_maxCombinedFile = null;
	/**#@-*/

	/**
	 * Optional allowed attributes for script tag
	 * @var array
	 */
	protected $_optionalAttributes = array('charset', 'defer', 'language', 'src');

	protected $_mapJsfiles = array();

	/**
	 * Required attributes for script tag
	 * @var string
	 */
	protected $_requiredAttributes = array('type');

	/**
	 * Whether or not to format scripts using CDATA; used only if doctype
	 * helper is not accessible
	 * @var bool
	 */
	public $useCdata = false;

	/**
	 * Constructor
	 *
	 * Set separator to PHP_EOL.
	 *
	 * @return void
	 */
	/**
	 *
	 * The folder to be appended to the base url to find minify on your server.
	 * The default assumes you installed minify in your documentroot\min
	 * directory
	 * if you modified the directory name at all, you need to let the helper know
	 * here.
	 * @var string
	 */
	protected $_minifyLocation = 'externals/minify/minify.php';

	/**
	 * @var Zend_Log
	 */
	private static $_log;

	/**
	 * @return void
	 */
	public function __construct() {

		parent::__construct();

		$this -> setSeparator(PHP_EOL);
	}

	/**
	 * @return Zend_Log
	 */
	public function getLog() {
		if (self::$_log == null) {
			self::$_log = new Zend_Log(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/headscript.log'));
		}
		return self::$_log;
	}

	/**
	 * write log to temporary/log/headscript.log
	 * @param string $intro
	 * @param string $message
	 * @param string $type [Zend_Log::INFO]
	 */
	public function log($intro = null, $message, $type) {
		return $this -> getLog() -> log(PHP_EOL . $intro . PHP_EOL . $message, $type);
	}

	public function toString($indent = null) {

		if (APPLICATION_ENV == 'development' || !Engine_Api::_() -> hasModuleBootstrap('minify')) {
			return $this -> toString2();
		}

		$minifyApi = Engine_Api::_() -> getApi('core', 'minify');

		if (false == $minifyApi -> minifyJs()) {
			return $this -> toString2();
		}

		$off = false;

		if (isset($_REQUEST['minify']) && $_REQUEST['minify'] === 'off') {
			$off = true;
		}

		$maxCombinedFile = $minifyApi -> getMaxCombinedFile();

		//$counter = '&'. (int) $this->view->layout()->counter;
		$counter = '&' . $minifyApi -> getSiteCounter();
		$indent = (null !== $indent) ? $this -> getWhitespace($indent) : $this -> getIndent();
		
		// Determining the appropriate way to handle inline scripts
		if ($this -> view) {
			$useCdata = $this -> view -> doctype() -> isXhtml() ? true : false;
		} else {
			$useCdata = $this -> useCdata ? true : false;
		}

		$escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
		$escapeEnd = ($useCdata) ? '//]]>' : '//-->';

		$this -> getContainer() -> ksort();

		$staticBaseUrl = $this -> view -> layout() -> staticBaseUrl;

		$configName = isset($_REQUEST['config_name']) ? $_REQUEST['config_name'] : 'none';

		$allJs = array();
		$tempJs = array();

		// scan all items of head scripts
		foreach ($this as $item) {
			if (isset($item -> attributes['src'])) {
				// externals scripts
				$src = $item -> attributes['src'];
				$src = str_replace('/index.php', '', $src);

				$check1 = (stripos($src, $staticBaseUrl) === 0 && strpos($staticBaseUrl, 'http://') === 0);
				$check2 = stripos($src, 'http://') !== 0 && stripos($src, 'https://') !== 0 && stripos($src, '.php') === FALSE && stripos($src, '.js') != FALSE && stripos($src, 'tinymce') === FALSE && stripos($src, 'jquery') === FALSE;

				if ($check1 or $check2) {

					// get beauty src file.

					if ($staticBaseUrl && $staticBaseUrl != '/') {
						$src = str_replace($staticBaseUrl, '', $src);
					}

					$src = str_replace('/index.php', '', $src);
					$src = preg_replace("#\?.*$#", '', $src);
					$src = str_replace('\\', '/', $src);
					$src = str_replace('//', '/', $src);
					$src = str_replace('//', '/', $src);
					$src = trim($src, '/');
					// compare this source to js1, js2

					if (!$matched) {
						$tempJs[] = $src;
					}
					
				}
				else {
					if (!empty($tempJs)) {
						$allJs[] = implode(',', $tempJs);
						$tempJs = array();
					}
					$allJs[] = $item;
				}
			} else {
				if (!empty($tempJs)) {
					$allJs[] = implode(',', $tempJs);
					$tempJs = array();
				}
				// inline script
				$allJs[] = $item;
			}
		}

		if (!empty($tempJs)) {
			$allJs[] = implode(',', $tempJs);
			$tempJs = array();
		}
		
		// resort all js to skipup this issue.
		$resortJs  = array();
		

		$xhtml = array();
		//$b = trim($staticBaseUrl,'/');
		$b = NULL;
				
		foreach ($allJs as $js) {
			if (is_string($js)) {
				$item = new stdClass();
				$item -> type = 'text/javascript';

				if (stripos($js, 'g=') !== FALSE) {
					$item -> attributes['src'] = $this -> getMinUrl() . '?' . $js . $counter;

				} else if ($b == NULL) {
					$item -> attributes['src'] = $this -> getMinUrl() . '?f=' . $js . $counter;
				} else {
					$item -> attributes['src'] = $this -> getMinUrl() . '?b=' . $b . '&f=' . $js . $counter;
				}
				$xhtml[] = $this -> itemToString($item, $indent, $escapeStart, $escapeEnd);
			} else {
				$xhtml[] = $this -> itemToString($js, $indent, $escapeStart, $escapeEnd);
			}
		}
		
		// $this -> log('alljs', var_export($allJs, true), Zend_Log::INFO);

		if ($off) {
			$arr = array();

			foreach ($allJs as $js) {
				if (is_string($js)) {
					$arr = array_merge($arr, explode(',', $js));
				}
			}

			$config = $minifyApi -> readMinifySetting();

			$this -> log('name', $configName, Zend_Log::INFO);
			$this -> log('js', var_export($arr, true), Zend_Log::INFO);

			$config[$configName]['js'] = $arr;

			$minifyApi -> writeMinifySetting($config);

			return $this -> toString2($indent);
		}

		return $indent . implode($this -> _escape($this -> getSeparator()) . $indent, $xhtml);
	}

	/**
	 * Return headScript object
	 *
	 * Returns headScript helper object; optionally, allows specifying a script
	 * or script file to include.
	 *
	 * @param  string $mode Script or file
	 * @param  string $spec Script/url
	 * @param  string $placement Append, prepend, or set
	 * @param  array $attrs Array of script attributes
	 * @param  string $type Script type and/or array of script attributes
	 * @return Zend_View_Helper_HeadScript
	 */
	public function headScript($mode = Zend_View_Helper_HeadScript::FILE, $spec = null, $placement = 'APPEND', array $attrs = array(), $type = 'text/javascript') {
		if ((null !== $spec) && is_string($spec)) {
			$action = ucfirst(strtolower($mode));
			$placement = strtolower($placement);
			switch ($placement) {
				case 'set' :
				case 'prepend' :
				case 'append' :
					$action = $placement . $action;
					break;
				default :
					$action = 'append' . $action;
					break;
			}
			$this -> $action($spec, $type, $attrs);
		}

		return $this;
	}

	/**
	 * Start capture action
	 *
	 * @param  mixed $captureType
	 * @param  string $typeOrAttrs
	 * @return void
	 */
	public function captureStart($captureType = Zend_View_Helper_Placeholder_Container_Abstract::APPEND, $type = 'text/javascript', $attrs = array()) {
		if ($this -> _captureLock) {
			// require_once
			// 'Zend/View/Helper/Placeholder/Container/Exception.php';
			throw new Zend_View_Helper_Placeholder_Container_Exception('Cannot nest headScript captures');
		}

		$this -> _captureLock = true;
		$this -> _captureType = $captureType;
		$this -> _captureScriptType = $type;
		$this -> _captureScriptAttrs = $attrs;
		ob_start();
	}

	/**
	 * End capture action and store
	 *
	 * @return void
	 */
	public function captureEnd() {
		$content = ob_get_clean();
		// flush content there will save our resource to message all request, did
		// you think it is helpful.
		//return $content;
		$type = $this -> _captureScriptType;
		$attrs = $this -> _captureScriptAttrs;
		$this -> _captureScriptType = null;
		$this -> _captureScriptAttrs = null;
		$this -> _captureLock = false;

		switch ($this->_captureType) {
			case Zend_View_Helper_Placeholder_Container_Abstract::SET :
			case Zend_View_Helper_Placeholder_Container_Abstract::PREPEND :
			case Zend_View_Helper_Placeholder_Container_Abstract::APPEND :
				$action = strtolower($this -> _captureType) . 'Script';
				break;
			default :
				$action = 'appendScript';
				break;
		}
		$this -> $action($content, $type, $attrs);
	}

	/**
	 * Overload method access
	 *
	 * Allows the following method calls:
	 * - appendFile($src, $type = 'text/javascript', $attrs = array())
	 * - offsetSetFile($index, $src, $type = 'text/javascript', $attrs = array())
	 * - prependFile($src, $type = 'text/javascript', $attrs = array())
	 * - setFile($src, $type = 'text/javascript', $attrs = array())
	 * - appendScript($script, $type = 'text/javascript', $attrs = array())
	 * - offsetSetScript($index, $src, $type = 'text/javascript', $attrs =
	 * array())
	 * - prependScript($script, $type = 'text/javascript', $attrs = array())
	 * - setScript($script, $type = 'text/javascript', $attrs = array())
	 *
	 * @param  string $method
	 * @param  array $args
	 * @return Zend_View_Helper_HeadScript
	 * @throws Zend_View_Exception if too few arguments or invalid method
	 */
	public function __call($method, $args) {
		if (preg_match('/^(?P<action>set|(ap|pre)pend|offsetSet)(?P<mode>File|Script)$/', $method, $matches)) {
			if (1 > count($args)) {
				// require_once 'Zend/View/Exception.php';
				throw new Zend_View_Exception(sprintf('Method "%s" requires at least one argument', $method));
			}

			$action = $matches['action'];
			$mode = strtolower($matches['mode']);
			$type = 'text/javascript';
			$attrs = array();

			if ('offsetSet' == $action) {
				$index = array_shift($args);
				if (1 > count($args)) {
					// require_once 'Zend/View/Exception.php';
					throw new Zend_View_Exception(sprintf('Method "%s" requires at least two arguments, an index and source', $method));
				}
			}

			$content = $args[0];

			if (isset($args[1])) {
				$type = (string)$args[1];
			}
			if (isset($args[2])) {
				$attrs = (array)$args[2];
			}

			switch ($mode) {
				case 'script' :
					$item = $this -> createData($type, $attrs, $content);
					if ('offsetSet' == $action) {
						$this -> offsetSet($index, $item);
					} else {
						$this -> $action($item);
					}
					break;
				case 'file' :
				default :
					if (!$this -> _isDuplicate($content)) {
						$attrs['src'] = $content;
						$item = $this -> createData($type, $attrs);
						if ('offsetSet' == $action) {
							$this -> offsetSet($index, $item);
						} else {
							$this -> $action($item);
						}
					}
					break;
			}

			return $this;
		}

		return parent::__call($method, $args);
	}

	/**
	 * Is the file specified a duplicate?
	 *
	 * @param  string $file
	 * @return bool
	 */
	protected function _isDuplicate($file) {
		foreach ($this->getContainer() as $item) {
			if (is_object($item) && null === $item -> source && isset($item -> attributes) && is_array($item -> attributes) && array_key_exists('src', $item -> attributes) && ($file == $item -> attributes['src'])) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Is the script provided valid?
	 *
	 * @param  mixed $value
	 * @param  string $method
	 * @return bool
	 */
	protected function _isValid($value) {
		if ((!$value instanceof stdClass) || !isset($value -> type) || (!isset($value -> source) && !isset($value -> attributes))) {
			return false;
		}

		return true;
	}

	/**
	 * Override append
	 *
	 * @param  string $value
	 * @return void
	 */
	public function append($value) {
		if (!$this -> _isValid($value)) {
			// require_once 'Zend/View/Exception.php';
			throw new Zend_View_Exception('Invalid argument passed to append(); please use one of the helper methods, appendScript() or appendFile()');
		}

		return $this -> getContainer() -> append($value);
	}

	/**
	 * Override prepend
	 *
	 * @param  string $value
	 * @return void
	 */
	public function prepend($value) {
		if (!$this -> _isValid($value)) {
			// require_once 'Zend/View/Exception.php';
			throw new Zend_View_Exception('Invalid argument passed to prepend(); please use one of the helper methods, prependScript() or prependFile()');
		}

		return $this -> getContainer() -> prepend($value);
	}

	/**
	 * Override set
	 *
	 * @param  string $value
	 * @return void
	 */
	public function set($value) {
		if (!$this -> _isValid($value)) {
			// require_once 'Zend/View/Exception.php';
			throw new Zend_View_Exception('Invalid argument passed to set(); please use one of the helper methods, setScript() or setFile()');
		}

		return $this -> getContainer() -> set($value);
	}

	/**
	 * Override offsetSet
	 *
	 * @param  string|int $index
	 * @param  mixed $value
	 * @return void
	 */
	public function offsetSet($index, $value) {
		if (!$this -> _isValid($value)) {
			// require_once 'Zend/View/Exception.php';
			throw new Zend_View_Exception('Invalid argument passed to offsetSet(); please use one of the helper methods, offsetSetScript() or offsetSetFile()');
		}

		return $this -> getContainer() -> offsetSet($index, $value);
	}

	/**
	 * Set flag indicating if arbitrary attributes are allowed
	 *
	 * @param  bool $flag
	 * @return Zend_View_Helper_HeadScript
	 */
	public function setAllowArbitraryAttributes($flag) {
		$this -> _arbitraryAttributes = (bool)$flag;
		return $this;
	}

	/**
	 * Are arbitrary attributes allowed?
	 *
	 * @return bool
	 */
	public function arbitraryAttributesAllowed() {
		return $this -> _arbitraryAttributes;
	}

	/**
	 * Create script HTML
	 *
	 * @param  string $type
	 * @param  array $attributes
	 * @param  string $content
	 * @param  string|int $indent
	 * @return string
	 */
	public function itemToString($item, $indent, $escapeStart, $escapeEnd) {
		$attrString = '';

		if ($this -> _isFirstRun) {
			$this -> _isFirstRun = true;
		}

		if (!empty($item -> attributes)) {
			foreach ($item->attributes as $key => $value) {
				if (!$this -> arbitraryAttributesAllowed() && !in_array($key, $this -> _optionalAttributes)) {
					continue;
				}
				if ('defer' == $key) {
					$value = 'defer';
				}
				$attrString .= sprintf(' %s="%s"', $key, ($this -> _autoEscape) ? $this -> _escape($value) : $value);
			}
		}

		$type = ($this -> _autoEscape) ? $this -> _escape($item -> type) : $item -> type;
		$html = $indent . '<script type="' . $type . '"' . $attrString . '>';
		if (!empty($item -> source)) {
			$html .= PHP_EOL . $indent . '    ' . $escapeStart . PHP_EOL . $item -> source . $indent . '    ' . $escapeEnd . PHP_EOL . $indent;
		}
		$html .= '</script>';

		if (isset($item -> attributes['conditional']) && !empty($item -> attributes['conditional']) && is_string($item -> attributes['conditional'])) {
			$html = '<!--[if ' . $item -> attributes['conditional'] . ']> ' . $html . '<![endif]-->';
		}

		return $html;
	}

	/**
	 * Retrieve string representation
	 *
	 * @param  string|int $indent
	 * @return string
	 */
	public function toString2($indent = null) {
		$indent = (null !== $indent) ? $this -> getWhitespace($indent) : $this -> getIndent();

		if ($this -> view) {
			$useCdata = $this -> view -> doctype() -> isXhtml() ? true : false;
		} else {
			$useCdata = $this -> useCdata ? true : false;
		}
		$escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
		$escapeEnd = ($useCdata) ? '//]]>' : '//-->';

		$items = array();
		$this -> getContainer() -> ksort();
		foreach ($this as $item) {
			if (!$this -> _isValid($item)) {
				continue;
			}

			$items[] = $this -> itemToString($item, $indent, $escapeStart, $escapeEnd);
		}

		$return = implode($this -> getSeparator(), $items);

		return $return;
	}

	/**
	 * Create data item containing all necessary components of script
	 *
	 * @param  string $type
	 * @param  array $attributes
	 * @param  string $content
	 * @return stdClass
	 */
	public function createData($type, array $attributes, $content = null) {
		$data = new stdClass();
		$data -> type = $type;
		$data -> attributes = $attributes;
		$data -> source = $content;
		return $data;
	}

	/**
	 * Retrieve the minify url
	 *
	 * @return string
	 */
	public function getMinUrl() {
		return $this -> view -> layout() -> staticBaseUrl . $this -> _minifyLocation;
	}

	/**
	 * Retrieve the currently set base URL
	 *
	 * @return string
	 */
	public function getBaseUrl() {
		return Zend_Controller_Front::getInstance() -> getBaseUrl();
	}

	/**
	 * Retrieve array url
	 *
	 * @return array
	 */
	public function cleanURLForMinify($urls) {
		/*
		 echo '<=====';
		 echo '<pre>';
		 print_r($urls);
		 echo '</pre>';
		 echo '=====>';
		 */
		$clean = array();
		$baseUrl = trim($this -> getBaseUrl(), '/index.php');
		$staticBaseUrl = $this -> view -> layout() -> staticBaseUrl;
		$group1 = $this -> getSocialEngineFile();
		$group2 = $this -> get3rdPartyFile();

		foreach ($urls as $url) {
			$url = str_replace($baseUrl . '/', '/', $url);
			$url = str_replace('/index.php', '', $url);
			$url = preg_replace("#\?.*$#", '', $url);
			$url = str_replace('\\', '/', $url);
			$url = str_replace('//', '/', $url);
			$url = str_replace('//', '/', $url);
			$url = trim($url, '/');
			if ($url) {
				if (array_search($url, $group1) !== FALSE) {
					$clean[] = $url;
				} else {

				}

			}
		}

		$clean = array_unique($clean);
		/*
		 echo '<br/><=========================================================';
		 echo '<pre>';
		 print_r($clean);
		 echo '</pre>';
		 echo
		 '===============================================================><br/>';
		 */
		return $clean;
	}

}
