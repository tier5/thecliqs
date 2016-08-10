<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Page.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_Package extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  protected $_modifiedTriggers = false;

	protected $_type = 'page_package';

  protected $_product;

	public function init()
	{
		$this->modules = unserialize($this->modules);
		$this->auth_view = unserialize($this->auth_view);
		$this->auth_comment = unserialize($this->auth_comment);
		$this->auth_posting = unserialize($this->auth_posting);

		parent::init();
	}

  public function setFromArray($values)
  {
    if ($this->getIdentity())
    {
      $featured = ($values['featured'])? 1:0;
      $sponsored = ($values['sponsored'])? 1:0;

      $this->resetPages(array('featured'=>$featured, 'sponsored'=>$sponsored));
    }

    return parent::setFromArray($values);
  }

	public function save()
	{
		//Modules
	  if (isset($this->modules))
      $this->modules = serialize($this->modules);
    else
      $this->modules = serialize(array());

		//Authorization views
	  if (isset($this->auth_view))
      $this->auth_view = serialize($this->auth_view);
    else
      $this->auth_view = serialize(array());

		//Authorization comment
	  if (isset($this->auth_comment))
      $this->auth_comment = serialize($this->auth_comment);
    else
      $this->auth_comment = serialize(array());

		//Authorization posting
	  if (isset($this->auth_posting))
      $this->auth_posting = serialize($this->auth_posting);
    else
      $this->auth_posting = serialize(array());
		
		parent::save();
	}
	
  public function hasDuration()
  {
    return ( $this->duration > 0 && $this->duration_type != 'forever' );
  }

  public function isFree()
  {
    return ( $this->price <= 0 );
  }

  public function isOneTime()
  {
    return ( $this->recurrence <= 0 || $this->recurrence_type == 'forever' );
  }

  public function getLevel()
  {
		return null;
  }

	public function getPackageDescription()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
		$view = Zend_Registry::get('Zend_View');
		$priceStr = $view->locale()->toCurrency($this->price, $currency);

		// Package is free
		if( $this->isFree() ) {
			$str = $translate->translate('Free');
		}

		// Package is recurring
		else if( $this->recurrence > 0 && $this->recurrence_type != 'forever' ) {

			// Make full string
			if( $this->recurrence == 1 ) { // (Week|Month|Year)ly
				if( $this->recurrence_type == 'day' ) {
					$typeStr = $translate->translate('daily');
				} else {
					$typeStr = $translate->translate($this->recurrence_type . 'ly');
				}
				$str = sprintf($translate->translate('%1$s %2$s'), $priceStr, $typeStr);
			} else { // per x (Week|Month|Year)s
				$typeStr = $translate->translate(array($this->recurrence_type, $this->recurrence_type . 's', $this->recurrence));
				$str = sprintf($translate->translate('%1$s per %2$s %3$s'), $priceStr,
					$this->recurrence, $typeStr); // @todo currency
			}
		}

		// Plan is one-time
		else {
			$str = sprintf($translate->translate('One-time fee of %1$s'), $priceStr);
		}

		// Add duration, if not forever
		if( $this->duration > 0 && $this->duration_type != 'forever' ) {
			$typeStr = $translate->translate(array($this->duration_type, $this->duration_type . 's', $this->duration));
			$str = sprintf($translate->translate('%1$s for %2$s %3$s'), $str, $this->duration, $typeStr);
		}

		return $str;
	}

	public function getExpirationDate($rel = null)
	{
	  if( null === $rel ) {
	    $rel = time();
	  }

	  // If it's a one-time payment or a free package with no duration, there
	  // is no expiration
	  if( ($this->isOneTime() || $this->isFree()) && !$this->hasDuration() ) {
	    return false;
	  }

	  // If this is a free or one-time package, the expiration is based on the
	  // duration, otherwise the expirations is based on the recurrence
	  $interval = null;
	  $interval_type = null;
	  if( $this->isOneTime() || $this->isFree() ) {
	    $interval = $this->duration;
	    $interval_type = $this->duration_type;
	  } else {
	    $interval = $this->recurrence;
	    $interval_type = $this->recurrence_type;
	  }

	  // This is weird, it should have been handled by the statement at the top
	  if( $interval == 'forever' ) {
	    return false;
	  }

	  // Calculate when the next payment should be due
	  switch( $interval_type ) {
	    case 'day':
	      $part = Zend_Date::DAY;
	      break;
	    case 'week':
	      $part = Zend_Date::WEEK;
	      break;
	    case 'month':
	      $part = Zend_Date::MONTH;
	      break;
	    case 'year':
	      $part = Zend_Date::YEAR;
	      break;
	    default:
	      throw new Engine_Payment_Exception('Invalid recurrence_type');
	      break;
	  }

	  $relDate = new Zend_Date($rel);
	  $relDate->add((int) $interval, $part);

	  return $relDate->toValue();
	}

	public function getTotalBillingCycleCount()
  {
    // One-time
    if( $this->isOneTime() ) {
      return 1;
    }
    // Indefinite
    else if( !$this->hasDuration() ) {
      return null;
    }
    // Calculate
    else {
      $multiplier = null;
      switch( $this->recurrence_type . '-' . $this->duration_type ) {
        case 'day-day':
        case 'week-week':
        case 'month-month':
        case 'year-year':
          $multiplier = 1;
          break;

        case 'day-week':
          $multiplier = 7;
          break;
        case 'day-month':
          $multiplier = 30; // Not accurate
          break;
        case 'day-year':
          $multiplier = 365; // Not accurate
          break;
        case 'week-month':
          $multiplier = 4; // Not accurate
          break;
        case 'week-year':
          $multiplier = 52; // Not accurate
          break;
        case 'month-year':
          $multiplier = 12;
          break;

        case 'week-day':
          $multiplier = 1 / 7;
          break;
        case 'month-day':
          $multiplier = 1 / 30;
          break;
        case 'month-week':
          $multiplier = 1 / 4;
          break;
        case 'year-day':
          $multiplier = 1 / 365;
          break;
        case 'year-week':
          $multiplier = 1 / 52;
          break;
        case 'year-month':
          $multiplier = 1 / 12;
          break;
        default:
          // Sigh, what should we do here?
          break;
      }

      return ceil($this->duration * $multiplier / $this->recurrence);
    }
  }
	
	public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',
      'reset' => true,
    ), $params);
    
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
  }

  public function viewPage()
  {
  	$user_id = (int)Engine_Api::_()->user()->getViewer()->getIdentity();
  	$today = Engine_Api::_()->page()->getToday();
  	$table = Engine_Api::_()->getDbTable('views', 'page');
  	
  	$view = $table->createRow();
  	
  	if ($user_id == $this->user_id) {
  		return ;
  	}
  	
  	$db = $table->getAdapter();
    $prefix = $table->getTablePrefix();

 		$select = $db->select();
 		$select
 			->from($prefix.'page_views', array('view_id'))
 			->where('page_id = ?', $this->page_id)
 			->where('view_date >= ?', $today)
 			->where('user_id = ?', $user_id);
 			
  	$ip = ip2long($_SERVER['REMOTE_ADDR']);
 		$select->where('ip = ?', $ip);
 		
 		$view->user_id = $user_id;
		$view->ip = $ip;
 		
 		if (!((bool)$db->fetchOne($select))){
	 		$this->unique_views++;
 		}
 		
 		$view->view_date = $today;
 		$view->page_id = $this->page_id;
 		$view->save();
 		
 		$this->view_count++;
 		$this->save();
  }

	public function getEnabledModules()
	{
	}

	public function isAllowLayout()
	{
		return (bool) $this->edit_layout;
	}

  /**
   * @param array $params[featured], $params[sponsored]
   * @return Zend_Db_Statement_Interface
   */
  public function resetPages( $params = array('featured' => false, 'sponsored' => false)  )
  {
    $featured = ($params['featured']) ? 1:0;
    $sponsored = ($params['sponsored']) ? 1: 0;

    $table = $this->getTable();
    $db = $table->getAdapter();
    $prefix = $table->getTablePrefix();

    $sql = 'UPDATE ' . $prefix . 'page_pages SET ' .
          'featured = IF  ((auto_set = 0 || auto_set = 2), ' . $featured . ' ,featured ), ' .
          'sponsored = IF  ((auto_set = 0 || auto_set = 1), ' . $sponsored . ' ,sponsored ) ' .
          'WHERE package_id = ' . $this->getIdentity()
    ;

    return $db->query($sql);
  }

  /**
   * @return Zend_Db_Statement_Interface
   */
  public function downgradePages()
  {
    /**
     * @var $default Page_Model_Package
     */
    $default = $this->getTable()->getDefaultPackage();
    $featured = ($default->featured) ? 1:0;
    $sponsored = ($default->sponsored) ? 1: 0;

    $table = $this->getTable();
    $db = $table->getAdapter();
    $prefix = $table->getTablePrefix();

    $sql = 'UPDATE ' . $prefix . 'page_pages SET ' .
          'package_id = ' . $default->getIdentity() . ', ' .
          'featured = IF  ((auto_set = 0 || auto_set = 2), ' . $featured . ' ,featured ), ' .
          'sponsored = IF  ((auto_set = 0 || auto_set = 1), ' . $sponsored . ' ,sponsored ) ' .
          'WHERE package_id = ' . $this->getIdentity()
    ;

    return $db->query($sql);
  }


  /**
   * @return bool
   */
  public function isDefault()
  {
    /**
     * @var $default Page_Model_Package
     */
    $default_id = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.default.package', 1);

    if ($this->getIdentity() == $default_id) return true;

    return false;
  }

  public function isForever()
  {
    return ( $this->duration <= 0 && $this->duration_type == 'forever' );
  }

  public function getGatewayParams()
  {
    $params = array();

    // General
    $params['name'] = $this->title;
    $params['price'] = $this->price;
    $params['description'] = $this->description;
    $params['vendor_product_id'] = $this->getGatewayIdentity();
    $params['tangible'] = false;

    // Non-recurring
    if( $this->recurrence_type == 'forever' ) {
      $params['recurring'] = false;
    }

    // Recurring
    else {
      $params['recurring'] = true;
      $params['recurrence'] = $this->recurrence . ' ' . ucfirst($this->recurrence_type);

      // Duration
      if( $this->duration_type == 'forever' ) {
        $params['duration'] = 'Forever';
      } else {
        $params['duration'] = $this->duration . ' ' . ucfirst($this->duration_type);
      }
    }

    return $params;
  }

  public function getGatewayIdentity()
  {
    return $this->getProduct()->sku;
  }

  public function getProduct()
  {
    if( null === $this->_product ) {
      $productsTable = Engine_Api::_()->getDbtable('products', 'payment');
      $this->_product = $productsTable->fetchRow($productsTable->select()
        ->where('extension_type = ?', 'page_package')
        ->where('extension_id = ?', $this->getIdentity())
        ->limit(1));

      // Create a new product?
      if( !$this->_product ) {
        $this->_product = $productsTable->createRow();
        $this->_product->setFromArray($this->getProductParams());
        $this->_product->save();
      }
    }

    return $this->_product;
  }

  public function getProductParams()
  {
    return array(
      'title' => $this->title,
      'description' => $this->description,
      'price' => $this->price,
      'extension_type' => 'page_package',
      'extension_id' => $this->package_id,
    );
  }

  protected function _postInsert()
  {
    // Update product
    $product = $this->getProduct();
    $product->setFromArray($this->getProductParams());
    $product->save();

    parent::_postInsert();
  }

  protected function _postUpdate()
  {
    // Update product
    $product = $this->getProduct();
    $product->setFromArray($this->getProductParams());
    $product->save();

    parent::_postUpdate();
  }
}