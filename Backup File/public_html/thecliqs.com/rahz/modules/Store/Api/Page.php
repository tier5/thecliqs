<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Page.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Api_Page extends Core_Api_Abstract
{

  /**
   * @param Zend_Db_Table_Select $select
   *
   * @return Zend_Db_Table_Select
   */
  public function setStoreIntegrity(Zend_Db_Select $select, $isClient = true)
  {
    /**
     * @var $_settings Core_Api_Settings
     * @var $table Page_Model_DbTable_Pages
     * @var $storeGateways Store_Model_DbTable_Apis
     */

    $storeGateways = Engine_Api::_()->getDbTable('apis', 'store');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $table    = Engine_Api::_()->getDbTable('pages', 'page');
    $prefix   = $table->getTablePrefix();

    $select
      ->joinInner($prefix.'page_content', $prefix.'page_content.page_id='.$prefix.'page_pages.page_id', array())
      ->where($prefix.'page_content.name = ?', 'store.page-profile-products')
      ->where($prefix.'page_content.is_timeline='.$prefix.'page_pages.is_timeline')
      ->where($prefix.'page_pages.approved = 1')
    ;

    if ($settings->__get('page.package.enabled')) {
      $select
        ->joinInner($prefix . 'page_packages', $prefix . 'page_packages.package_id=' . $prefix . 'page_pages.package_id', array())
        ->where($prefix . "page_packages.modules LIKE('%\"store\"%')");
    } else {
      $select
        ->joinInner($prefix . 'users', $prefix . 'users.user_id=' . $prefix . 'page_pages.user_id', array())
        ->joinInner(array('auth' => $prefix . 'authorization_permissions'),
          '(auth.level_id=' . $prefix . 'users.level_id && auth.type=\'page\' && auth.name=\'auth_features\')', array()
        )->where("auth.params LIKE('%\"store\"%')");
    }

    if ($isClient) {
      $select
        ->joinLeft(array('apis' => $storeGateways->info('name')), 'apis.page_id = ' . $prefix . 'page_pages.page_id', array())
        ->where('apis.enabled = 1')
      ;
      $select
        ->where("(select SUM(" . $prefix . "store_products.quantity) from " . $prefix . "store_products where " .
            $prefix . "store_products.page_id = ". $prefix . "page_pages.page_id) <> 0");
    }

    return $select;
  }

  public function isStore($page_id)
  {
    if (!$page_id) return false;
    /**
     * @var $select Zend_Db_Table_Select
     * @var $table  Page_Model_DbTable_Pages
     * @db Engine_Db_Table;
     */
    $table = $this->getTable();
    $db    = Engine_Db_Table::getDefaultAdapter();

    $select = $db
      ->select()
      ->from($table->info('name'), array($table->info('name') . '.page_id'));

    $select = $this->setStoreIntegrity($select, false);
    $select->where($table->info('name') . '.page_id = ?', $page_id);

    if ($db->fetchOne($select)) {
      return true;
    }

    return false;
  }

  /**
   * @return Page_Model_DbTable_Pages
   */
  public function getTable()
  {
    return Engine_Api::_()->getDbtable('pages', 'page');
  }


  /**
   * @param array $params
   *
   * @return Zend_Paginator
   */
  public function getPaginator($params = array())
  {
    /**
     * @var $table     Page_Model_DbTable_Pages
     * @var $select    Zend_Db_Table_Select
     * @var $paginator Zend_Paginator
     */
    $table              = Engine_Api::_()->getDbTable('pages', 'page');
    $params['approved'] = 1;
    $select             = $this->setStoreIntegrity($table->getSelect($params));

    $paginator = Zend_Paginator::factory($select);

    if (!empty($params['ipp'])) {
      $params['ipp'] = (int)$params['ipp'];
      $paginator->setItemCountPerPage($params['ipp']);
    }

    if (!empty($params['page'])) {
      $params['page'] = (int)$params['page'];
      $paginator->setCurrentPageNumber($params['page']);
    }

    return $paginator;
  }

  /**
   * @return array
   */
  public function getPopularCategories()
  {
    /**
     * @var $table  Store_Model_DbTable_Products
     * @var $select Zend_Db_Table_Select
     */
    $table  = Engine_Api::_()->getDbTable('products', 'store');
    $select = $table->select();

    $prefix = $table->getTablePrefix();

    $select
      ->setIntegrityCheck(false)
      ->from($prefix . 'page_fields_values', array('value',
      'count' => 'COUNT(' . $prefix . 'page_fields_values.value)'))
      ->joinLeft($prefix . 'page_fields_options', $prefix . 'page_fields_options.option_id = ' . $prefix . 'page_fields_values.value', array('category' => 'label'))
      ->where($prefix . 'page_fields_values.field_id = 1')
      ->joinLeft($prefix . 'page_pages', $prefix . 'page_fields_values.item_id = ' . $prefix . 'page_pages.page_id', array())
      ->where($prefix . 'page_pages.approved = 1')
      ->group($prefix . 'page_fields_values.value')
      ->order('count DESC')
      ->limit(7);

    $select = $this->setStoreIntegrity($select);

    return $table->fetchAll($select)->toArray();
  }

  public function getProductsCategories()
  {
    /**
     * @var $table  Store_Model_DbTable_Products
     * @var $select Zend_Db_Table_Select
     */
    $table  = Engine_Api::_()->getDbTable('products', 'store');
    $prefix = $table->getTablePrefix();

    $select = $table
      ->select()
      ->setIntegrityCheck(false)
      ->from($prefix . 'store_product_fields_values', array('value',
      'count' => 'COUNT(value)'))
      ->joinLeft($prefix . 'store_product_fields_options', $prefix . 'store_product_fields_options.option_id = ' . $prefix . 'store_product_fields_values.value', array('category' => 'label'))
      ->joinLeft($prefix . 'store_products', $prefix . 'store_products.product_id = ' . $prefix . 'store_product_fields_values.item_id', array());

    $select = $table->setStoreIntegrity($select);

    $select
      ->where($prefix . 'store_product_fields_values.field_id = 1')
      ->group($prefix . 'store_product_fields_values.value');

    return $table->fetchAll($select)->toArray();
  }

  public function getStoreOfTheDay()
  {
    $table  = Engine_Api::_()->getDbTable('pages', 'page');
    $prefix = $table->getTablePrefix();
    $select = $table->select();

    $select
      ->setIntegrityCheck(false)
      ->from($prefix . 'page_pages')
      ->order($prefix . 'page_pages.view_count DESC')
      ->limit(1);

    $select = $this->setStoreIntegrity($select);

    return $table->fetchRow($select);
  }

  public function getMaps($params)
  {
    $table  = Engine_Api::_()->getDbTable('products', 'store');
    $select = $table->select();

    $prefix = $table->getTablePrefix();

    $select
      ->setIntegrityCheck(false)
      ->from($prefix . 'store_product_fields_maps', array())
      ->joinLeft($prefix . 'store_product_fields_options',
      $prefix . 'store_product_fields_options.field_id = ' . $prefix . 'store_product_fields_maps.child_id', array('label'))
      ->joinLeft($prefix . 'store_product_fields_values',
      $prefix . 'store_product_fields_values.value = ' . $prefix . 'store_product_fields_options.option_id', array('field_id' => $prefix . 'store_product_fields_values.value',
                                                                                                                   'count'    => "COUNT($prefix" . 'store_product_fields_values.item_id)'))
      ->where($prefix . 'store_product_fields_maps.option_id = ?', $params)
      ->group($prefix . 'store_product_fields_options.label');
    return $table->fetchAll($select)->toArray();
  }

  public function getPopularLocations()
  {
    $table  = Engine_Api::_()->getDbTable('pages', 'page');
    $prefix = $table->getTablePrefix();
    $select = $table->select();

    $select
      ->setIntegrityCheck(false)
      ->from($prefix . 'page_pages', array($prefix . 'page_pages.city',
      'count' => 'COUNT(' . $prefix . 'page_pages.city)'))
      ->where($prefix . 'page_pages.city IS NOT NULL AND ' . $prefix . 'page_pages.city <> ""')
      ->where($prefix . 'page_pages.approved = 1')
      ->group($prefix . 'page_pages.city')
      ->order('count DESC')
      ->limit(7);

    $select = $this->setStoreIntegrity($select);

    return $table->fetchAll($select)->toArray();
  }

  public function getTags($params = array())
  {
    $table  = Engine_Api::_()->getDbTable('pages', 'page');
    $db     = $table->getAdapter();
    $select = $db->select();

    $prefix = $table->getTablePrefix();

    $select
      ->from($prefix . 'core_tagmaps', array('tag_id',
      'page_id' => $prefix . 'core_tagmaps.resource_id',
      'freq'    => 'COUNT(' . $prefix . 'core_tagmaps.tag_id)'))
      ->joinInner($prefix . 'core_tags', $prefix . 'core_tagmaps.tag_id = ' . $prefix . 'core_tags.tag_id', array('text'))
      ->joinInner($prefix . 'page_pages', $prefix . 'core_tagmaps.resource_id = ' . $prefix . 'page_pages.page_id', array())
      ->where($prefix . "core_tagmaps.resource_type = 'page'")
      ->where($prefix . "page_pages.approved = 1")
      ->group($prefix . 'core_tags.text')
      ->order('freq DESC')
      ->limit(5);

    $select = $this->setStoreIntegrity($select);

    if (!empty($params['page_id'])) {
      if (is_array($params['page_id'])) {
        $where = $prefix . "core_tagmaps.resource_id IN (" . implode(',', $params['page_id']) . ")";
      } elseif (is_numeric($params['page_id'])) {
        $where = $prefix . "core_tagmaps.resource_id = {$params['page_id']}";
      }
      $select
        ->where($where);
    }

    $rawData = $db->fetchAll($select);
    $type    = '';
    if (!empty($params['categorized'])) {
      $type = $params['categorized'];
    }

    return $this->categorizeTags($rawData, $type);
  }

  public function getProductsTags($params = array())
  {
    /**
     * @var $table Store_Model_DbTable_Products
     */
    $table  = Engine_Api::_()->getDbTable('products', 'store');
    $db     = $table->getAdapter();
    $select = $db->select();

    $prefix = $table->getTablePrefix();

    $select
      ->from($prefix . 'core_tagmaps', array('tag_id',
      'product_id' => $prefix . 'core_tagmaps.resource_id',
      'freq'       => 'COUNT(' . $prefix . 'core_tagmaps.tag_id)'))
      ->joinLeft($prefix . 'core_tags', $prefix . 'core_tagmaps.tag_id = ' . $prefix . 'core_tags.tag_id', array('text'))
      ->joinInner($prefix . 'store_products', $prefix . 'core_tagmaps.resource_id = ' . $prefix . 'store_products.product_id AND ('.$prefix.'store_products.quantity <> 0 OR '.$prefix.'store_products.type = "digital")', array())
    ;

    $select = $table->setStoreIntegrity($select);

    $select
      ->where($prefix . "core_tagmaps.resource_type = 'store_product'")
      ->group($prefix . 'core_tags.text')
      ->order('freq DESC')
      ->limit(5);


    if (!empty($params['product_id'])) {
      if (is_array($params['product_id'])) {
        $where = $prefix . "core_tagmaps.resource_id IN (" . implode(',', $params['product_id']) . ")";
      } elseif (is_numeric($params['product_id'])) {
        $where = $prefix . "core_tagmaps.resource_id = {$params['product_id']}";
      }
      $select
        ->where($where);
    }

    $rawData = $db->fetchAll($select);
    $type    = '';
    if (!empty($params['categorized'])) {
      $type = $params['categorized'];
    }

    return $this->categorizeTags($rawData, $type);
  }

  public function categorizeTags($rawData, $type = '')
  {
    $tags  = array();
    $limit = 30; // he@todo do something with it
    if ($type == 'page') {
      foreach ($rawData as $item) {
        $tags[$item['page_id']][] = $this->defineTagClass($item);
      }
    } else {

      if (is_array($rawData)) {
        shuffle($rawData);
      }

      foreach ($rawData as $item) {
        if (count($tags) >= $limit) {
          break;
        }
        $tags[] = $this->defineTagClass($item);
      }
    }

    return $tags;
  }

  public function defineTagClass($tag)
  {
    if (empty($tag['freq'])) {
      return $tag;
    }

    if ($tag['freq'] <= 1) {
      $tag['class'] = 1;
    } elseif ($tag['freq'] <= 3) {
      $tag['class'] = 2;
    } elseif ($tag['freq'] <= 7) {
      $tag['class'] = 3;
    } elseif ($tag['freq'] <= 10) {
      $tag['class'] = 4;
    } elseif ($tag['freq'] <= 20) {
      $tag['class'] = 5;
    } elseif ($tag['freq'] <= 40) {
      $tag['class'] = 6;
    } elseif ($tag['freq'] <= 65) {
      $tag['class'] = 7;
    } elseif ($tag['freq'] <= 100) {
      $tag['class'] = 8;
    } else {
      $tag['class'] = 9;
    }

    return $tag;
  }

  public function getMyStores(User_Model_User $user)
  {
    $membershipTbl = Engine_Api::_()->getDbTable('membership', 'page');
    $pagesTbl      = Engine_Api::_()->getDbTable('pages', 'page');
    $name          = $pagesTbl->info('name');
    $m_name        = $membershipTbl->info('name');
    $select        = $pagesTbl->select()
      ->setIntegrityCheck(false)
      ->from($name, array($name . '.*'))
      ->joinLeft($m_name, $name.'.page_id = '.$m_name.'.resource_id', array())
      ->where($m_name.'.user_id = ?', $user->getIdentity())
      ->order($name . '.creation_date DESC');

    $select = $this->setStoreIntegrity($select, false);
    return Zend_Paginator::factory($select);
  }

  public function getNavigation($page, $type = 'products')
  {
    $page_id    = $page->getIdentity();
    $navigation = new Zend_Navigation();

    if ($type == 'settings') {
      $navigation->addPages(array(
          'store_settings_gateway'=> array(
            'label'  => "Gateway",
            'route'  => 'store_settings',
            'action' => 'gateway',
            'params' => array('page_id' => $page_id)
          ),
          array(
            'label'      => "Locations",
            'route'      => 'store_settings',
            'controller' => 'locations',
            'params'     => array('page_id' => $page_id)
          )
        )
      );
    } elseif ($type == 'statistics') {
      $navigation->addPages(array(
        array(
          'label'  => "Chart Statistic",
          'route'  => 'store_statistics',
          'action' => 'chart',
          'params' => array('page_id' => $page_id)
        ),
        array(
          'label'  => "List Statistic",
          'route'  => 'store_statistics',
          'action' => 'list',
          'params' => array('page_id' => $page_id)
        )
      ));
    } else {
      $navigation->addPages(array(
        array(
          'label'  => "Edit Products",
          'route'  => 'store_products',
          'params' => array('page_id' => $page_id)
        ),
        array(
          'label'  => "STORE_Add New Product",
          'route'  => 'store_products',
          'action' => 'create',
          'params' => array('page_id' => $page_id)
        ))
      );
    }
    return $navigation;
  }

  /**
   * @param $page_id
   * @param $gateway_id
   *
   * @return bool
   */
  public function isGatewayEnabled($page_id, $gateway_id)
  {
    /**
     * @var $table Store_Model_DbTable_Apis
     */
    $table = Engine_Api::_()->getDbTable('apis', 'store');

    return (boolean)$table->select()
      ->from($table, new Zend_Db_Expr('TRUE'))
      ->where('page_id = ?', $page_id)
      ->where('gateway_id = ?', $gateway_id)
      ->where('enabled = 1')
      ->query()
      ->fetchColumn();
  }

  /**
   * @param $page_id
   *
   * @return null|Store_Model_Balance
   */
  public function getBalance($page_id)
  {
    if (!$this->isStore($page_id)) return null;

    /**
     * @var $table Store_Model_DbTable_Balances
     */
    $table  = Engine_Api::_()->getDbTable('balances', 'store');
    $select = $table
      ->select()
      ->where('page_id = ?', $page_id)
    ;

    if (null == ($balance = $table->fetchRow($select))) {
      $balance = $table->createRow(array(
        'page_id' => $page_id
      ));
      $balance->save();
    }

    return $balance;
  }
}