<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Products.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Products extends Engine_Db_Table
{
  protected $_rowClass = "Store_Model_Product";

  protected $_serializedColumns = array('params');

  public function deleteProducts($products_ids)
  {
  	if (empty($products_ids)){
      return $this;
    }

    foreach ($products_ids as $product_id){
    	Engine_Api::_()->getItem('store_product', $product_id)->delete();
    }

    return $this;
  }

	public function getProducts( $params = array() )
	{
		if (!empty($params['count']) && $params['count']){
			return $this->getAdapter()->fetchOne($this->getSelect($params));
		}

		return $this->getPaginator($params);
	}

  public function getProduct($params = array())
	{
		$select = $this->getSelect($params);
		return $this->fetchRow($select);
	}

	public function getPaginator($params = array())
	{
		$select = $this->getSelect($params);
		$paginator = Zend_Paginator::factory($select);

		if (!empty($params['ipp'])) {
			$paginator->setItemCountPerPage($params['ipp']);
		}

		if (!empty($params['p'])) {
			$paginator->setCurrentPageNumber($params['p']);
		}

		return $paginator;
	}

  /**
   * @param array $params
   * @return Zend_Db_Table_Select
   */
	public function getSelect($params = array())
	{
		$prefix = $this->getTablePrefix();

		$select = $this->select()
      ->setIntegrityCheck(false)
    ;

    if (!empty($params['count']) && $params['count']) {
      $select
        ->from($prefix.'store_products', array('count' => 'COUNT(*)'))
        ->group($prefix.'store_products.page_id');
    } else {
      $select
        ->from($prefix.'store_products')
      ;
    }

    if (empty($params['owner']) || !$params['owner']) {
      $select = $this->setStoreIntegrity($select);
    }

		if (!empty($params['page_id'])) {
			$select
				->where($prefix.'store_products.page_id = ?', $params['page_id']);
		}

		if (!empty($params['user_id'])) {
			$select
				->where($prefix.'store_products.owner_id = ?', $params['user_id']);
		}

		if (!empty($params['product_id'])) {
			$select
				->where($prefix.'store_products.product_id = ?', $params['product_id']);
		}

		if (!empty($params['featured'])) {
			$select
				->where($prefix.'store_products.featured = ?', $params['featured']);
		}

		if (!empty($params['sponsored'])) {
			$select
				->where($prefix.'store_products.sponsored = ?', $params['sponsored']);
		}

		if (!empty($params['order'])) {
			$select
				->order($prefix.'store_products.modified_date '.$params['order']);
		}

    if (!empty($params['price_type']) && $params['price_type'] === true) {
      $select
        ->where($prefix.'store_products.price_type = ?', 'discount')
      ;
    }

    if (!empty($params['quantity'])) {
      $select
        ->where($prefix.'store_products.quantity <> 0 OR ' . $prefix.'store_products.type = ?', 'digital')
      ;
    }

		return $select;
	}

	public function setStoreIntegrity(Zend_Db_Select $select)
	{
    /**
     * @var $adminGateways Store_Model_DbTable_Gateways
     * @var $storeGateways Store_Model_DbTable_Apis
     */

    $storeGateways = Engine_Api::_()->getDbTable('apis', 'store');
    $adminGateways = Engine_Api::_()->getDbTable('gateways', 'store');
		$prefix = $this->getTablePrefix();

		// Check if Page Module Enabled Or is Page Store
    if (!Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page')) {
      $select
        ->where($prefix . 'store_products.page_id = 0');
    } else {
      $select
        ->joinLeft($prefix . 'page_pages', $prefix . 'page_pages.page_id=' . $prefix . 'store_products.page_id', array())
        ->joinLeft($prefix . 'page_content', $prefix . 'page_content.page_id=' . $prefix . 'store_products.page_id', array())
        //->joinLeft(array('apis' => $storeGateways->info('name')), 'apis.page_id = ' . $prefix . 'page_pages.page_id', array())
        ->where($prefix . 'page_content.name=?', 'store.page-profile-products')
        ->where($prefix . 'page_content.is_timeline=' . $prefix . 'page_pages.is_timeline')
        ->where($prefix . 'page_pages.approved=1')
        //->where('apis.enabled = 1')
      ;

      /**
       * @var $settings Core_Api_Settings
       */
      $settings = Engine_Api::_()->getApi('settings', 'core');
      if ($settings->__get('page.package.enabled')) {
        $select
          ->joinLeft($prefix . 'page_packages', $prefix . 'page_packages.package_id=' . $prefix . 'page_pages.package_id', array())
          ->where($prefix . "page_packages.modules LIKE('%\"store\"%')");
      } else {
        $select
          ->joinLeft($prefix . 'users', $prefix . 'users.user_id=' . $prefix . 'page_pages.user_id', array())
          ->joinLeft(array('auth' => $prefix . 'authorization_permissions'),
          '(auth.level_id=' . $prefix . 'users.level_id && auth.type=\'page\' && auth.name=\'auth_features\')',
          array())
          ->where("auth.params LIKE('%\"store\"%')");
      }

      if ((int)$adminGateways->getEnabledGatewayCount() > 0 || Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('credit')) {
        $select
          ->orWhere($prefix . 'store_products.page_id = 0');
      }

      $where = $select->getPart('where');
      $select->reset('where');
      if (count($where) > 0) {
        $where = implode(' ', $where);
        $select->where($where);
      }
    }

		return $select;
	}

  public function postProduct(array $values)
	{
		if (empty($values)) {
			return false;
		}

    $user = Engine_Api::_()->user()->getViewer();
    $title = $values['title'];
    $body = $values['description'];
    $tags = preg_split('/[,]+/', $values['tags']);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
    	$row = null;

    	if (!empty($values['product_id']) && $values['product_id']){
    		$row = $this->getProduct(array('product_id' => $values['product_id']));
    	}
    	if (!$row){
    		$row = $this->createRow();
    		$row->owner_id = $user->getIdentity();
    	}
      $row->title = $values['title'];
      $row->price_type = $values['price_type'];
      $row->price = $values['price'];
      $row->list_price = $values['list_price'];
      $row->quantity = $values['quantity'];
      $row->description = $values['description'];

      $row->modified_date = date('Y-m-d H:i:s');
      $row->title = $title;
      $row->description = $body;
      $row->save();

      if ($tags) {
        $row->tags()->setTagMaps($user, $tags);
      }

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $row;
	}

  public function checkProduct(array $values)
	{
		if (empty($values)) {
			return false;
		}

    return false;
	}

  public function getProductOfTheDay() {
    $select = $this->getSelect(array('quantity' => true));
    $select
      ->order($this->getTablePrefix() . 'store_products.view_count DESC')
      ->limit(1);
		return $this->fetchRow($select);
  }

	public function getRandoms($limit = 1)
  {
    $table = Engine_Api::_()->getDbTable('products', 'store');
    $select = $this->getSelect(array('quantity' => true));
		$select
			->order('RAND()')
      ->limit($limit);

    return $table->fetchAll($select);
  }

  public function getPopulars($limit = 1)
  {
    $table = Engine_Api::_()->getDbTable('products', 'store');
    $select = $this->getSelect(array('quantity' => true));
		$select
			->order('view_count DESC')
      ->limit($limit);

    return $table->fetchAll($select);
  }

  public function getBestSellers($limit = 5)
  {
    $table = Engine_Api::_()->getDbTable('products', 'store');
    $select = $this->getSelect(array('quantity' => true));
		$select
			->order('sell_count DESC')
      ->limit($limit);

    return $table->fetchAll($select);
  }
}