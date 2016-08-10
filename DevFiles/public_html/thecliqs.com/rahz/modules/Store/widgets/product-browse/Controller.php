<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_ProductBrowseController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    /* params */
    $fc = Zend_Controller_Front::getInstance();
    $request = $fc->getRequest();
    $page = $request->getParam('page', 1);
    $search = $request->getParam('search');
    $minPrice = $request->getParam('min_price');
    $maxPrice = $request->getParam('max_price');
    $sort = $request->getParam('sort', 'recent');
    $this->view->tag_id = $tag_id = $request->getParam('tag_id', 0);
    $category_id = $request->getParam('profile_type', 0);
    $this->view->cat_id = $category_id = ($category_id) ? $category_id : $request->getParam('cat', 0);
    $params = $request->getParams();

    /**
     * @var $table Store_Model_DbTable_Products
     */
    $table = Engine_Api::_()->getDbtable('products', 'store');
    $prefix = $table->getTablePrefix();

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('m' => $prefix . 'store_product_fields_maps'), array("m.*"))
      ->where("m.option_id = ?", $category_id)
      ->where("m.field_id = ?", 1)
      ->limit(1);

    if (null !== ($row = $table->fetchRow($select))) {
      $this->view->child_id = $child_id = $row->child_id;
      $this->view->subCat_id = $subCat_id = ($request->getParam('field_' . $child_id)) ? $request->getParam('field_' . $child_id) : $request->getParam('sub_cat', 0);
    }

    /**
     * @var $select Zend_Db_Table_Select
     */

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'))
      ->joinLeft(array('v' => $prefix . 'store_product_fields_values'), "v.item_id = " . $prefix . "store_products.product_id")
      ->joinLeft(array('o' => $prefix . 'store_product_fields_options'), "o.option_id = v.value AND o.field_id = 1", array("category" => "o.label"))
      ->group($prefix . 'store_products.product_id');

    $select = $table->setStoreIntegrity($select);

    $values = array(
      'order' => $prefix . 'store_products.product_id',
      'order_direction' => 'DESC',
    );

    $this->view->assign($values);
    $field = $fc->getRequest()->getParam('field');
    if (!empty($field)) {
      $select
        ->where('v.field_id = 1 AND ' . 'v.value = ?', $field);
    }
    if (!empty($search)) {
      $select
        ->joinLeft($prefix . 'core_tags', $prefix . "core_tags.text LIKE '%$search%'", array())
        ->joinLeft($prefix . 'core_tagmaps', $prefix . "core_tagmaps.tag_id = " . $prefix . "core_tags.tag_id", array())
        ->where($prefix . 'store_products.product_id = ' . $prefix . 'core_tagmaps.resource_id')
        ->where($prefix . 'core_tagmaps.resource_type = ?', 'store_product')
        ->orWhere($prefix . 'store_products.title LIKE ?', '%' . $search . '%');
    }
    if (!empty($minPrice) && is_numeric($minPrice)) {
      $select
        ->where($prefix . 'store_products.price > ?', $minPrice);
    }
    if (!empty($maxPrice) && is_numeric($maxPrice)) {
      $select
        ->where($prefix . 'store_products.price < ?', $maxPrice);
    }
    if (!empty($category_id)) {
      if (!empty($subCat_id)) {
        $select
          ->where('v.value = ?', $subCat_id);
      } else {
        $select
          ->where('o.option_id = ?', $category_id);
      }
    }
    // Tags
    if ($tag_id != 0) {
      $select
        ->joinLeft($prefix . 'core_tags', $prefix . "core_tags.tag_id = $tag_id", array())
        ->joinLeft($prefix . 'core_tagmaps', $prefix . "core_tagmaps.tag_id = $tag_id", array())
        ->where($prefix . 'store_products.product_id = ' . $prefix . 'core_tagmaps.resource_id')
        ->where($prefix . 'core_tagmaps.resource_type = ?', 'store_product');
    }

    if (!isset($params['profile_type'])) {
      $params['fields'] = '';
    } else {
      // Process options
      $tmp = array();
      foreach ($params as $k => $v) {
        if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
          continue;
        } else if (false !== strpos($k, '_field_')) {
          list($null, $field) = explode('_field_', $k);
          $tmp['field_' . $field] = $v;
        } else if (false !== strpos($k, '_alias_')) {
          list($null, $alias) = explode('_alias_', $k);
          $tmp[$alias] = $v;
        }
      }
      $params['fields'] = $tmp;
    }

    if (!empty($params['fields'])) {
      $fields = (is_array($params['fields'])) ? $params['fields'] : array($params['fields']);

      $select
        ->joinLeft($prefix . 'store_product_fields_search', $prefix . 'store_product_fields_search.item_id = ' . $prefix . 'store_products.product_id', array());
      $searchParts = Engine_Api::_()->fields()->getSearchQuery('store_product', $fields);

      foreach ($searchParts as $k => $v) {
        $select->where("`" . $prefix . "store_product_fields_search`.{$k}", $v);
      }
    }

    switch ($sort) {
      case 'recent' :
        $select
          ->order($prefix . 'store_products.creation_date DESC');
        break;
      case 'popular' :
        $select
          ->order($prefix . 'store_products.view_count DESC');
        break;
      case 'sponsored' :
        $select
          ->where($prefix . 'store_products.sponsored = ?', 1);
        break;
      case 'featured' :
        $select
          ->where($prefix . 'store_products.featured = ?', 1);
        break;
    }

    $select
      ->where($prefix . 'store_products.quantity <> 0 OR ' . $prefix . 'store_products.type = ?', 'digital')
      ->order($prefix . 'store_products.sponsored DESC')
      ->order($prefix . 'store_products.featured DESC');

    // Make paginator
    /**
     * @var $viewer User_Model_User
     * @var $paginator Zend_Paginator
     * @var $settings Core_Model_DbTable_Settings
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->view->view = $request->getParam('v', $settings->getSetting('store.browse.mode', 'icons'));
    $this->view->sort = $sort;

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->count = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
    $paginator->setCurrentPageNumber($page);
    $this->getElement()->setTitle('');
  }
}