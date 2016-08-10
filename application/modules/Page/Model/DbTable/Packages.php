<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Pages.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Packages extends Engine_Db_Table
{
  protected $_rowClass = 'Page_Model_Package';

  public function loadMetaData(Engine_Content $contentAdapter, $name)
  {
    if (gettype($name) == 'string') {
      $select = $this->select()->where('url = ?', $name);
    } else {
      $select = $this->select()->where('page_id = ?', $name);
    }

    $page = $this->fetchRow($select);

    if( !is_object($page) ) {
      // throw?
      return null;
    }

    return $page->toArray();
  }

  public function getEnabledPackageCount()
  {
    $select = $this->select()
      ->from($this, new Zend_Db_Expr('COUNT(*)'))
      ->where('enabled = ?', 1);

    if ( Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0){
      $select->where('price <= 0');
    }

    return $select->query()
      ->fetchColumn()
      ;
  }

  public function getEnabledNonFreePackageCount()
  {
    return $this->select()
      ->from($this, new Zend_Db_Expr('COUNT(*)'))
      ->where('enabled = ?', 1)
      ->where('price > ?', 0)
      ->query()
      ->fetchColumn()
      ;
  }

  public function lastInsertId()
  {
    $db = $this->getAdapter();
    $prefix = $this->getTablePrefix();
    $status = $db->query("SHOW TABLE STATUS LIKE '".$prefix."page_pages'")->fetch();

    return (int)$status['Auto_increment'];
  }

  public function checkUrl($url)
  {
    $url = strtolower(trim($url));
    $url = preg_replace('/[^a-z0-9-]/', '-', $url);
    $url = preg_replace('/-+/', "-", $url);

    $select = $this->select();
    $select->where('url = ?', $url);
    $page = $this->fetchRow($select);

    return isset($page->page_id);
  }

  public function getSelect($params = array())
  {
    $select = $this->select();
    $prefix = $this->getTablePrefix();

    $select
      ->setIntegrityCheck(false)
      ->from($prefix.'page_pages')
      ->where($prefix."page_pages.name <> 'header' AND ".$prefix."page_pages.name <> 'footer'")
      ->joinLeft($prefix."page_fields_values", $prefix."page_fields_values.item_id = ".$prefix."page_pages.page_id", array())
      ->joinLeft($prefix."page_fields_options", $prefix."page_fields_options.option_id = ".$prefix."page_fields_values.value", array('category' => 'label', 'category_id' => 'option_id'))
      ->where($prefix."page_fields_values.field_id = 1")
      ->where($prefix."page_fields_options.field_id = 1");

    if (!empty($params['view']) && $params['view'] == 'map'){
      $select
        ->joinLeft($prefix.'page_markers', $prefix.'page_markers.page_id = '.$prefix.'page_pages.page_id', array('marker_id', 'longitude', 'latitude'));
    }

    if (!empty($params['tag_id'])){
      $select
        ->joinInner($prefix.'core_tagmaps', $prefix.'core_tagmaps.resource_id = '.$prefix.'page_pages.page_id')
        ->where($prefix."core_tagmaps.resource_type = 'page'")
        ->where($prefix."core_tagmaps.tag_id = {$params['tag_id']}");
    }

    if (!empty($params['search'])){
      $select
        ->where($prefix."page_pages.search = {$params['search']}");
    }

    if (isset($params['sort'])){
      switch ($params['sort']){
        case 'newest' :
          $select
            ->order($prefix.'page_pages.modified_date DESC');
          break;
        case 'popular' :
          $select
            ->order($prefix.'page_pages.view_count DESC');
          break;
        case 'alphabet' :
          $select
            ->order($prefix.'page_pages.title ASC');
          break;
        default :
          $select
            ->order($prefix.'page_pages.featured DESC')
            ->order($prefix.'page_pages.modified_date DESC');
          break;
      }
    }

    if (!empty($params['where'])) {
      $select->where($params['where']);
    }

    if (!empty($params['city'])) {
      $select->where($prefix."page_pages.city = '{$params['city']}'");
    }

    if (!empty($params['keyword'])) {
      $select->where($prefix."page_pages.title LIKE '%{$params['keyword']}%' OR ".$prefix."page_pages.description LIKE '%{$params['keyword']}%' OR ".$prefix."page_pages.keywords LIKE '%{$params['keyword']}%'");
    }

    if (!empty($params['fields'])) {
      $select
        ->joinLeft($prefix.'page_fields_search', $prefix.'page_fields_search.item_id = '.$prefix.'page_pages.page_id', array());
      $searchParts = Engine_Api::_()->fields()->getSearchQuery('page', $params['fields']);
      foreach ($searchParts as $k => $v) {
        $select->where("`".$prefix."page_fields_search`.{$k}", $v);
      }
    }

    if (!empty($params['group'])) {
      $select->group($params['group']);
    }

    if (!empty($params['favorite']) && !empty($params['team_id'])) {
      $t = Engine_Api::_()->getDbTable('favorites', 'page');
      $tmpName = $t->info('name');
      $tmp = $t->select()
        ->setIntegrityCheck(false)
        ->from($tmpName, array('page_fav_id'))
        ->where('page_id = ?', $params['favorite'])
        ->where('page_fav_id <> ?', $params['favorite']);
      $favIds = $t->getAdapter()->fetchCol($tmp);

      $t = Engine_Api::_()->getDbTable('membership', 'page');
      $tmpName = $t->info('name');
      $tmp = $t->select()
        ->setIntegrityCheck(false)
        ->from($tmpName, array('resource_id'))
        ->where('user_id = ?', $params['team_id'])
        ->where('user_approved = ?', 1)
        ->where('resource_approved = ?', 1)
        ->where('active = ?', 1)
        ->where('resource_id <> ?', $params['favorite'])
        ->group('resource_id');
      $pageIds = $t->getAdapter()->fetchCol($tmp);

      if (!empty($favIds)) {
        $select
          ->where($prefix."page_pages.page_id NOT IN (?)", $favIds);
      }

      if (!empty($pageIds)) {
        $select
          ->where($prefix."page_pages.page_id IN (?)", $pageIds);
      } else {
        $select
          ->where('FALSE');
      }
    }

    if (!empty($params['page_id'])) {
      $select->where($prefix."page_pages.page_id = {$params['page_id']}");
    }

    if (!empty($params['approved'])) {
      $select->where($prefix."page_pages.approved = {$params['approved']}");
    }

    if (!empty($params['user_id'])) {
      $select->where($prefix."page_pages.user_id = {$params['user_id']}");
    }

    if (!empty($params['featured'])) {
      $select->where($prefix."page_pages.featured = {$params['featured']}");
    }

    return $select;
  }

  public function getPaginator($params = array())
  {
    $select = $this->getSelect($params);
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

  public function createElementParams($row)
  {
    $data = array(
      'identity' => $row->content_id,
      'type' => $row->type,
      'name' => $row->name,
      'order' => $row->order,
    );
    $params = (array) $row->params;
    if( isset($params['title']) ) $data['title'] = $params['title'];
    $data['params'] = $params;

    return $data;
  }

  public function deletePackage(Page_Model_Package $package)
  {
    $package->delete();
    return $this;
  }

  public function deletePackages($package_ids)
  {
    if (empty($package_ids)){
      return $this;
    }

    foreach ($package_ids as $package_id){
      Engine_Api::_()->getItem('page_package', $package_id)->delete();
    }

    return $this;
  }

  public function getEnabledPackages()
  {
    $select = $this->select()->from(array('p' => $this->info('name')))
      ->where('p.enabled = ?', 1);

    if ( Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0){
      $select->where('p.price <= 0');
    }

    $settings =Engine_Api::_()->getDbTable('settings', 'core');

    if (!$settings->getSetting('default.package.enabled', 1)) {
      $select->where('p.package_id <> ?', $settings->getSetting('page.default.package', 1));
    }

    return $this->fetchAll( $select );
  }

  /**
   * @return null|Page_Model_Package
   */
  public function getDefaultPackage()
  {
    $default_id = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.default.package', 1);
    return $this->findRow($default_id);
  }

  /**
   * @param $package
   * @return bool
   */
  public function isDefault( $package )
  {
    /**
     * @var $default Page_Model_Package
     */
    $default = $this->getDefaultPackage();

    if (is_integer($package) && $default->getIdentity() == $package) return true;

    if ($package instanceof Page_Model_Package && $default->getIdentity() == $package->getIdentity()) return true;

    return false;
  }


  public function getPackages( $params = array() )
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !empty($params['not_payed']) ) {
      $selectIds = $this->getDefaultAdapter()
        ->select()
        ->from(array('s' => 'engine4_page_subscriptions'), array('s.package_id'))
        ->where('`s`.`page_id` = 0')
        ->where('`s`.`active` = 1')
        ->joinLeft(array('o' => 'engine4_payment_orders'), "o.source_type = 'page_subscription' AND o.source_id = s.subscription_id AND o.state = 'complete'", array())
        ->joinLeft(array('p' => 'engine4_page_packages'), 'p.package_id = s.package_id', array())
        ->where('o.user_id = ?', $viewer->getIdentity())
      ;
      $select = $this->select();
      $select->where('package_id NOT IN ?', $selectIds);

      $settings = Engine_Api::_()->getDbTable('settings', 'core');
      if( !$settings->getSetting('default.package.enabled', 1) ) {
        $select->where('package_id <> ?', $settings->getSetting('page.default.package', 1));
      }
      return $this->fetchAll($select);
    }

    //if( !empty($params['payed']) )
    $select = $this->select();
    $select->setIntegrityCheck(false);
    $select->from(array('s' => 'engine4_page_subscriptions'), array('subscription_id'));
    $select->joinLeft(array('p' => 'engine4_page_packages'), "s.package_id = p.package_id");
    $select->joinLeft(array('o' => 'engine4_payment_orders'), "o.source_type = 'page_subscription' AND o.source_id = s.subscription_id AND o.state = 'complete'", array());
    $select->where('s.page_id = 0');
    $select->where('s.active = 1');
    $select->where('p.price > 0');

    return $this->fetchAll($select);
  }

}