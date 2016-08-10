<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Product.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_Product extends Core_Model_Item_Collection
{
  protected $_parent_type = 'page';

  protected $_owner_type = 'user';

  protected $_type = 'store_product';

  protected $_collectible_type = 'store_photo';

  public function init()
  {
    if (!empty($this->price)) {
      $this->price = (float)round($this->price, 2);
    }

    if (!empty($this->list_price)) {
      $this->list_price = (float)round($this->list_price, 2);
    }
  }

  public function getHref($params = array())
  {
    $title = $this->getTitle();
    $title = $this->getSlug($title);
    
    $params = array_merge(array(
      'route'      => 'store_profile',
      'reset'      => true,
      'product_id' => $this->product_id,
      'title'      => $title
    ), $params);
    $route  = $params['route'];
    $reset  = $params['reset'];
    unset($params['route']);
    unset($params['reset']);

    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getCategoryHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'store_general',
      'reset' => true,
      'cat'   => $this->getCategory()->value,
    ), $params);
    $route  = $params['route'];
    $reset  = $params['reset'];
    unset($params['route']);
    unset($params['reset']);

    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  /**
   * Gets the description of the item. This might be about me for users (todo
   *
   * @return string The description
   */
  public function getDescription()
  {
    if (isset($this->description)) {
      return Engine_String::strip_tags($this->description);
    }
    return '';
  }

  public function getCategory()
  {
    $table  = $this->getTable();
    $prefix = $table->getTablePrefix();

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('v'=> $prefix . 'store_product_fields_values'), array())
      ->joinLeft(array('o'=> $prefix . 'store_product_fields_options'), "o.option_id = v.value AND o.field_id = 1", array("category" => "o.label",
                                                                                                                          "all"      => "v.*"))
      ->joinLeft(array('m' => $prefix . 'store_product_fields_meta'), "v.field_id = m.field_id", array("label" => "m.label"))
      ->where("v.item_id = ?", $this->product_id)
      ->limit(1);

    if (null !== ($row = $table->fetchRow($select))) {
      return $row;
    }

    return null;
  }

  public function getInfo()
  {
    $view = Zend_Registry::get('Zend_View');
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $view->viewer = Engine_Api::_()->user()->getViewer();

    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($this);

    return $view->fieldValueLoop($this, $fieldStructure);
  }

  public function hasStore()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
      return false;
    }

    $page = Engine_Api::_()->getDbtable('pages', 'page')->findRow($this->page_id);

    if ($page && $page->getIdentity()) return true;

    return false;
  }

  /**
   * @return null|Page_Model_Page
   */
  public function getStore()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
      return null;
    }

    /**
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->getDbTable('pages', 'page')->findRow($this->page_id);

    return $page;
  }

  public function getPage()
  {
    return $this->getStore();
  }

  public function getOwner()
  {
    return Engine_Api::_()->getItem('user', $this->owner_id);
  }

  /**
   * Override method setFromArray.
   *
   * @param  array $data
   *
   * @return Zend_Db_Table_Row_Abstract Provides a fluent interface
   */
  public function createAlbum(array $data)
  {
    $set_cover = true;

    $fileids = explode(' ', $data['fancyuploadfileids']);
    if (count($fileids) <= 1)
      return $this;

    // Do other stuff
    foreach ($fileids as $photo_id) {
      $photo = Engine_Api::_()->getItem("store_photo", $photo_id);
      if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) continue;

      if ($set_cover) {
        $this->photo_id = $photo_id;
        $set_cover      = false;
        $this->save();
      }

      $photo->collection_id = $this->product_id;
      $photo->save();
    }

    return $this;
  }

  public function createLocations()
  {
    /**
     * @var $aTable Store_Model_DbTable_Locationships
     * @var $bTable Store_Model_DbTable_Locations
     * @var $cTable Store_Model_DbTable_Productships
     */
    $aTable = Engine_Api::_()->getDbTable('locationships', 'store');
    $bTable = Engine_Api::_()->getDbTable('locations', 'store');
    $cTable = Engine_Api::_()->getDbTable('productships', 'store');

    $select = $aTable->select()
      ->setIntegrityCheck(false)
      ->from(array('a' => $aTable->info('name')))
      ->joinInner(array('b' => $bTable->info('name')), 'b.location_id = a.location_id', array())
      ->where('page_id = ?', $this->page_id);

    foreach ($aTable->fetchAll($select) as $location) {
      $cTable->insert(array(
        'product_id'    => $this->product_id,
        'location_id'   => $location->location_id,
        'shipping_amt'  => $location->shipping_amt,
        'shipping_days' => $location->shipping_days,
        'creation_date' => new Zend_Db_Expr('NOW()'),
      ));
    }
  }

  public function getPhotoUrl($type = null)
  {
    if (null != ($photo = Engine_Api::_()->getItem('store_photo', $this->photo_id))) {
      $url = $photo->getPhotoUrl($type);
    } else {
      $view = Zend_Registry::get('Zend_View');
      return $view->layout()->staticBaseUrl . 'application/modules/Store/externals/images/nophoto_product_thumb_normal.png';
    }

    return ((strpos($url, 'product') !== false) || (strpos($url, 'store_photo') !== false)) ? $url : null;
  }


  public function getVideo()
  {
    $table  = Engine_Api::_()->getDbTable('videos', 'store');
    $select = $table->select()
      ->where('product_id = ?', $this->getIdentity())
      ->limit(1);

    $video = $table->fetchRow($select);

    return $video;
  }

  public function hasVideo()
  {
    return ($this->getVideo()) ? true : false;
  }

  public function addAudio($file_id)
  {
    if ($file_id instanceof Store_Model_Audio) {
      $file_id = $file_id->file_id;
    }
    if ($file_id instanceof Storage_Model_File) {
      $file = $file_id;
    } else {
      $file = Engine_Api::_()->getItem('storage_file', $file_id);
    }

    if ($file) {
      $product_audios             = Engine_Api::_()->getDbtable('audios', 'store')->createRow();
      $product_audios->product_id = $this->getIdentity();
      $product_audios->file_id    = $file->getIdentity();
      $product_audios->title      = preg_replace('/\.(mp3|m4a|aac|mp4)$/i', '', $file->name);
      $product_audios->save();
      return $product_audios;
    }

    return false;
  }

  public function sponsoredStatus($value)
  {
    if ($value) {
      $value = 1;
    }
    if ($this->sponsored != $value) {
      $this->sponsored = $value;
    }
    $this->save();
  }

  public function featuredStatus($value)
  {
    if ($value) {
      $value = 1;
    }
    if ($this->featured != $value) {
      $this->featured = $value;
    }
    $this->save();
  }

  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

  public function removeTags()
  {
    $table = Engine_Api::_()->getDbTable('products', 'store');
//    $table = Engine_Api::_()->fields()->getTable('products', 'store');
    $prefix = $table->getTablePrefix();

    $db = $table->getAdapter();
    $db->delete($prefix . 'store_product_fields_search', "item_id = {$this->product_id}");
    $db->delete($prefix . 'store_product_fields_values', "item_id = {$this->product_id}");

    $where = "resource_type = 'page' AND resource_id = {$this->product_id}";
    $db->delete($prefix . 'core_tagmaps', $where);
  }

  public function removeAudios()
  {
    $audios = Engine_Api::_()->getDbTable('audios', 'store')->getAudios($this->getIdentity());

    foreach ($audios as $audio) {
      Engine_Api::_()->getApi('core', 'store')->deleteAudio($audio);
    }
  }

  public function removeVideo()
  {
    $video = $this->getVideo();
    if (!$video) {
      return false;
    }
    Engine_Api::_()->getApi('core', 'store')->deleteVideo($video);
  }

  public function removeFile()
  {
    if (null !== ($file = $this->getFile())) {
      Engine_Api::_()->getApi('core', 'store')->deleteFile($file->file_id);
    }
  }

  public function removeLocations()
  {
    /**
     * @var $table Store_Model_DbTable_Productships
     */
    $table = Engine_Api::_()->getDbTable('productships', 'store');
    $table->delete(array(
      'product_id = ?'=>$this->getIdentity()
    ));
  }

  public function getLink()
  {
    return sprintf("<a href='%s'>%s</a>", $this->getHref(), $this->getTitle());
  }

  public function isProductStoreEnabled()
  {
    $table  = Engine_Api::_()->getItemTable('store_product');
    $select = $table->getSelect(array('product_id'=> $this->product_id));
    $result = $table->fetchAll($select);
    $rows   = $result->toArray();

    if (count($rows) == 0)
      return false;
    return true;
  }

  public function isDigital()
  {
    return ($this->type == 'digital') ? true : false;
  }

  public function getFile()
  {
    $storage = Engine_Api::_()->getItemTable('storage_file');
    $select  = $storage->select()
      ->where('parent_id = ?', $this->getIdentity())
      ->where('parent_type = ?', $this->getType());
    return $storage->fetchRow($select);
  }

  public function hasFile()
  {
    return ($this->getFile() !== null) ? true : false;
  }

  public function isFree()
  {
    return ($this->price <= 0);
  }

  public function isLocationSupported($location_id)
  {
    /**
     * @var $table Store_Model_DbTable_Productships
     */
    $table = Engine_Api::_()->getDbTable('productships', 'store');
    return (bool)$table->select()
      ->from($table, new Zend_Db_Expr('IF(location_id > 0, 1, 0)'))
      ->where('location_id = ?', $location_id)
      ->where('product_id = ?', $this->getIdentity())
      ->query()
      ->fetchColumn();
  }

  public function isWished()
  {
    /**
     * @var $table  Store_Model_DbTable_Wishes
     * @var $viewer User_Model_User
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $table  = Engine_Api::_()->getDbTable('wishes', 'store');
    $select = $table->select()
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('product_id = ?', $this->getIdentity())
      ->limit(1);
    ;

    if ($table->fetchRow($select) !== null) {
      return true;
    }
    return false;
  }

  public function isAddedToCart()
  {
    /**
     * @var $viewer     User_Model_User
     * @var $cartTb     Store_Model_DbTable_Carts
     * @var $cart       Store_Model_Cart
     */

    $viewer  = Engine_Api::_()->user()->getViewer();
    $cartTb  = Engine_Api::_()->getItemTable('store_cart');
    $cart    = $cartTb->getCart($viewer->getIdentity());
    if (!$cart) {
      return false;
    }
    return $cart->hasProduct($this->getIdentity());
  }

  public function getExpirationDate()
  {
    return strtotime($this->discount_expiry_date);
  }

  public function clearDiscount()
  {
    $this->discount_expiry_date = new Zend_Db_Expr('NULL');
    $this->price_type           = 'simple';
    $this->price                = $this->list_price;
    $this->save();
  }

  public function getShippingPrice($location_id)
  {

    if (!$this->isLocationSupported($location_id) || $this->isDigital()) {
      return 0;
    }

    /**
     * @var $table Store_Model_DbTable_Productships
     */
    $table = Engine_Api::_()->getDbTable('productships', 'store');
    return (double)$table->select()
      ->from($table, new Zend_Db_Expr('shipping_amt'))
      ->where('location_id = ?', $location_id)
      ->where('product_id = ?', $this->getIdentity())
      ->query()
      ->fetchColumn();
  }

  public function getShippingDays($location_id)
  {

    if (!$this->isLocationSupported($location_id) || $this->isDigital())
      return false;

    /**
     * @var $table Store_Model_DbTable_Productships
     */
    $table = Engine_Api::_()->getDbTable('productships', 'store');
    return (double)$table->select()
      ->from($table, new Zend_Db_Expr('shipping_days'))
      ->where('location_id = ?', $location_id)
      ->where('product_id = ?', $this->getIdentity())
      ->query()
      ->fetchColumn();
  }

  public function delete()
  {
    $this->removeFile();
    $this->removeAudios();
    $this->removeVideo();
    $this->removeTags();
    $this->removeLocations();
    $this->clearWishlist();

    return parent::delete();
  }

  public function getTax()
  {
    $table = Engine_Api::_()->getDbTable('taxes', 'store');

    $percent = (double) $table->select()
      ->from($table, new Zend_Db_Expr('percent'))
      ->where('tax_id = ?', $this->tax_id)
      ->query()
      ->fetchColumn();

    return (double)(($percent*$this->price)/100);
  }

  public function getTaxInfo()
  {
    if (!$this->tax_id) {
      return '';
    }

    $tax = Engine_Api::_()->getDbTable('taxes', 'store')->getRow($this->tax_id);
    if (!$tax) {
      return '';
    }

    return $tax->title . ': ' . number_format($tax->percent, 2, '.', '') . '%';
  }

  public function getPrice()
  {
    return (double) $this->price;
  }

  public function getQuantity()
  {
    return ($this->type == 'digital') ? true : $this->quantity;
  }

  public function clearWishlist()
  {
    /**
     * @var $table Store_Model_DbTable_Wishes
     */
    $table = Engine_Api::_()->getDbTable('wishes', 'store');
    $table->delete(array('product_id = ?' => $this->getIdentity()));
  }

  public function isStoreCredit()
  {
    $isCreditEnabled = Engine_Api::_()->store()->isCreditEnabled();
    if (!$isCreditEnabled) {
      return false;
    }

    if (!$this->hasStore()) {
      return $this->via_credits;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $isStoreEnabled = $settings->getSetting('store.credit.store', 0);
    if (!$isStoreEnabled) {
      return false;
    }

    return $this->via_credits;
  }

  public function getActions()
  {
    $attachmentTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $name = $attachmentTable->info('name');

    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

    $action_ids = $attachmentTable->select()
      ->from(array($name), 'action_id')
      ->where('type = ?', "store_product")
      ->where('id = ?', $this->getIdentity())
      ->query()
      ->fetchAll(null, 'action_id');
     ;

    if(!empty($action_ids)){
      $select = $actionTable->select()->where('action_id IN(?)', $action_ids);
      return $actionTable->fetchAll($select);
    }

    return $action_ids;
  }
}