<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Api_Core extends Core_Api_Abstract
{
  protected $_modules;
  
  public function getContentTable($type)
  {
    return Engine_Api::_()->getItemTable($type);
  }

  public function getNoPhoto($type)
  {
    return Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'home') .
      "application/modules/Page/externals/images/nophoto/" . $type . ".png";
  }
  
  public function shortenType($type)
  {
    $prefix = substr($type, 0, 4);
    if ($prefix == 'page') {
      return substr($type, 4);
    }

    return $type;
  }
  
  public function getLocations()
  {
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $select = $table->select();
    $name = $table->info('name');
    
    $select
      ->setIntegrityCheck(false)
      ->from($name, array('city', 'count' => 'COUNT(city)'))
      ->order('count DESC')
      ->where('city IS NOT NULL AND city <> ""')
      ->where($name.'.approved = 1')
      ->where($name.'.enabled = 1')
      ->group('city')
      ->limit(7);
      
    return $table->fetchAll($select)->toArray();
  }
  

  public function getSetInfo()
  {
    $db = Engine_Db_Table::getDefaultAdapter();

    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $prefix = $table->getTablePrefix();

    $q = "
      SELECT
        `s`.`id` as `set_id`,
          `s`.`caption`,
        `sc`.`cat_id`,
          `fo`.`label` AS `cat_caption`
      FROM `{$prefix}page_category_set` AS `s`
        INNER JOIN `{$prefix}page_category_set_category` AS `sc`
          ON( `s`.`id` = `sc`.`set_id` )
        INNER JOIN `{$prefix}page_fields_options` AS `fo`
          ON(`sc`.`cat_id` = `fo`.`option_id` AND `fo`.`field_id` = 1)";

    $res = $db->query($q);
    $rows = $res->fetchAll();

    return is_array($rows) ? $rows : array();
  }

  public function getCategories()
  {
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $select = $table->select();

    $prefix = $table->getTablePrefix();

    $select
      ->setIntegrityCheck(false)
      ->from($prefix.'page_fields_values', array('value', 'count' => 'COUNT(value)'))
      ->joinLeft($prefix.'page_fields_options', $prefix.'page_fields_options.option_id = '.$prefix.'page_fields_values.value', array('category' => 'label'))
      ->where($prefix.'page_fields_values.field_id = 1')
      ->joinLeft($prefix.'page_pages', $prefix.'page_fields_values.item_id = '.$prefix.'page_pages.page_id', array())
      ->where($prefix.'page_pages.approved = 1')
      ->where($prefix.'page_pages.enabled = 1')
      ->group($prefix.'page_fields_values.value')
      ->order('category ASC');

    return $table->fetchAll($select)->toArray();
  }

  public function getSetCategories()
  {
    $dba = Engine_Db_Table::getDefaultAdapter();
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $prefix = $table->getTablePrefix();

    $q = "
      SELECT
        `s`.`caption` AS `set`,
        `s`.`id` AS `set_id`,
          `sc`.`cat_id`,
          `sc`.`order` AS `cat_order`,
          `fo`.`label` AS `category`
      FROM `{$prefix}page_category_set` as `s`
      LEFT JOIN `{$prefix}page_category_set_category` AS `sc`
        ON ( `s`.id = `sc`.set_id )
      LEFT JOIN `{$prefix}page_fields_options` AS `fo`
        ON ( `sc`.`cat_id` = `fo`.`option_id` AND `fo`.`field_id` = 1 )
      ORDER BY `s`.`id` ASC, `sc`.`order` ASC
    ";

    $rows = $dba->fetchAll($q);

    return is_array($rows) && !empty($rows) ? $rows : array();
  }

  public function getCategoriesWithPages($exludeDefault = false)
  {
    $prefix = Engine_Api::_()->getDbTable('pages', 'page')->getTablePrefix();

    $q = "
      SELECT `cs`.`id`, COUNT(`cs`.`id`) as `count`
      FROM `{$prefix}page_category_set` AS `cs`
        INNER JOIN `{$prefix}page_pages` AS `p`
          ON(`cs`.`id` = `p`.`set_id`)
      GROUP BY `cs`.`id`
    ";

    $stat = array();

    $rows = Engine_Db_Table::getDefaultAdapter()->query($q)->fetchAll();

    foreach ($rows as $row)
      $stat[$row['id']] = $row['count'];

    $q = "
      SELECT `fv`.`value`,
             COUNT(value) AS `count`,
             `csc`.set_id,
             `csc`.cat_id,
             `cs`.`caption` AS `category_set`,
             `fo`.`label` AS `caption`

      FROM `{$prefix}page_fields_values` AS `fv`
           LEFT JOIN `{$prefix}page_fields_options` AS `fo`
            ON(`fo`.`option_id` = `fv`.value)
           LEFT JOIN {$prefix}page_category_set_category AS `csc`
            ON(`fo`.`option_id` = `csc`.`cat_id`)
           LEFT JOIN `{$prefix}page_category_set` AS `cs`
            ON(`csc`.`set_id` = `cs`.`id`)
           LEFT JOIN `{$prefix}page_pages` AS `p`
            ON (`fv`.item_id = `p`.page_id)

      WHERE (`fv`.field_id = 1) AND
            (`p`.approved = 1) AND
            (`p`.enabled = 1)

      GROUP BY `fv`.`value`
      ORDER BY `csc`.`set_id` ASC";
    $set = array();

    $rows = Engine_Db_Table::getDefaultAdapter()->query($q)->fetchAll();
    foreach ($rows as $row) {
      if ($exludeDefault && $row['cat_id'] == 1) {
        continue;
      }
      if (!isset($set[$row['set_id']]))
        $set[$row['set_id']] = array('id' => $row['set_id'], 'caption' => $row['category_set'], 'total' => $stat[$row['set_id']], 'items' => array());

      $set[$row['set_id']]['items'][] = $row;
    }

    return $set;
  }


  public function getCategorizedFieldIds()
  {
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $db = $table->getAdapter();
    $select = $db->select();
    $prefix = $table->getTablePrefix();

    $select
      ->from($prefix.'page_fields_options', array('option_id'))
      ->where($prefix.'page_fields_options.field_id = 1');
      
    $option_ids = $db->fetchAll($select, array(), Zend_Db::FETCH_COLUMN);
    if (empty($option_ids)){
      return array();
    }
    
    $where = $prefix."page_fields_maps.option_id IN (".implode(',' , $option_ids).")";
    $select = $db->select();
    $select
      ->from($prefix.'page_fields_maps', array('child_id', 'option_id'))
      ->where($where);

    return $db->fetchPairs($select);
  }

  public function getPage($id)
  {
    if ($id instanceof Core_Model_Item_Abstract) {
      return $id;
    }

    $pageTable = Engine_Api::_()->getDbTable('pages', 'page');
    $select = $pageTable->select()->where('page_id = ?', $id);

    return $pageTable->fetchRow($select);
  }

  public function getPageByUrl($url)
  {
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    return $table->fetchRow($table->select()->where('url = ?', $url)->limit(1));
  }
  
  public function getToday()
  {
    $timestamp = time();
    $today = getdate($timestamp);
    $month = $today['mon']; 
    $mday = $today['mday']; 
    $year = $today['year']; 
    return date('Y-m-d H:i:s', mktime(0, 0, 0, $month, $mday, $year));
  }
  
  public function getUsersForTeam(array $params)
  {
    if (empty($params['page_id'])){
      return array();
    }

    $table = Engine_Api::_()->getDbTable('membership', 'page');
    $prefix = $table->getTablePrefix();
    
    $select = $table->select();
    $select
      ->from($prefix.'page_membership', array('user_id'))
      ->where('resource_id = ?', $params['page_id']);
      
    $user_ids = $table->getAdapter()->fetchCol($select);
    $user_ids[] = Engine_Api::_()->user()->getViewer()->getIdentity(); 
    
    $table = Engine_Api::_()->getItemTable('user');
    $select = $table->select();
    
    if (!empty($params['keyword'])) {
      $select
        ->where("username LIKE '%{$params['keyword']}%' OR displayname LIKE '%{$params['keyword']}%'");
    }
    
    if (!empty($user_ids)){
      $user_ids = implode(',', $user_ids);
      $select
        ->where("user_id NOT IN ({$user_ids})");
    }

    $paginator = Zend_Paginator::factory($select);
//    $paginator->setItemCountPerPage(1);
    
    return $paginator;
  }

  public function getTags($params = array())
  {
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $db = $table->getAdapter();
    $select = $db->select();

    $prefix = $table->getTablePrefix();

    $select
      ->from($prefix.'core_tagmaps', array('tag_id', 'page_id' => $prefix.'core_tagmaps.resource_id', 'freq' => 'COUNT('.$prefix.'core_tagmaps.tag_id)'))
      ->joinLeft($prefix.'core_tags', $prefix.'core_tagmaps.tag_id = '.$prefix.'core_tags.tag_id', array('text'))
      ->joinLeft($prefix.'page_pages', $prefix.'core_tagmaps.resource_id = '.$prefix.'page_pages.page_id', array())
      ->where($prefix."core_tagmaps.resource_type = 'page'")
      ->where($prefix."page_pages.approved = 1")
      ->group($prefix.'core_tags.text')
      ->order('freq DESC');
    
    if (!empty($params['page_id'])) {
      if (is_array($params['page_id'])) {
        $where = $prefix."core_tagmaps.resource_id IN (".implode(',', $params['page_id']).")";
      } elseif (is_numeric($params['page_id'])) {
        $where = $prefix."core_tagmaps.resource_id = {$params['page_id']}";
      }
      $select
        ->where($where);
    }
    
    $rawData = $db->fetchAll($select);
    $type = '';
    if (!empty($params['categorized'])) {
      $type = $params['categorized'];
    }
    
    return $this->categorizeTags($rawData, $type);
  }

  public function getFeaturedPages()
  {
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $params = array('featured' => 1, 'approved' => 1);

    return $table->getPaginator($params);
  }

	public function getSponsoredPages()
  {
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $params = array('sponsored' => 1, 'approved' => 1);

    return $table->getPaginator($params);
  }
  
  public function getPageTags(array $page_ids)
  {
    if (empty($page_ids)){
      return array();
    }

    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $db = $table->getAdapter();
    $select = $db->select();

    $prefix = $table->getTablePrefix();
    
    $select
      ->from($prefix.'core_tagmaps', array('tag_id', 'page_id' => $prefix.'core_tagmaps.resource_id'))
      ->joinLeft($prefix.'core_tags', $prefix.'core_tagmaps.tag_id = '.$prefix.'core_tags.tag_id', array('text'))
      ->joinLeft($prefix.'page_pages', $prefix.'page_pages.page_id = '.$prefix.'core_tagmaps.resource_id', array())
      ->where($prefix."core_tagmaps.resource_type = 'page'")
      ->where($prefix."page_pages.approved = 1");
    
    if (!empty($page_ids)){
      $where = $prefix."core_tagmaps.resource_id IN (".implode(',', $page_ids).")";
      $select
        ->where($where);
    }
    
    $rawData = $db->fetchAll($select);
    
    return $this->categorizeTags($rawData, 'page');
  }
  
  public function categorizeTags($rawData, $type = '')
  {
    $tags = array();
    $limit = 30; // @todo do something with it
    if ($type == 'page') {
      foreach ($rawData as $item) {
        $tags[$item['page_id']][] = $this->defineTagClass($item);
      }
    } else {
      foreach ($rawData as $item) {
        if (count($tags) >= $limit) {
          break ;
        }
        $tags[] = $this->defineTagClass($item);
      }

      if (is_array($tags)) {
        shuffle($tags);
      }
    }
    
    return $tags;
  }

  public function getPages($params = array())
  {
    return Engine_Api::_()->getDbTable('pages', 'page')->getPaginator($params);
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
  
  public function categorizedBy($item)
  {
    return $this->defineTagClass($item);
  }
  
  public function categorizedByPage($item, &$key)
  {
    $key = $item['page_id'];
    return array($key => $this->defineTagClass($item));
  }

  public function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
  {
    if ($length == 0)
      return '';
    
    if (Engine_String::strlen($string) > $length) {
      $length -= Engine_String::strlen($etc);
      if (!$break_words && !$middle) {
        $string = preg_replace('/\s+?(\S+)?$/', '', Engine_String::substr($string, 0, $length+1));
      }
      if(!$middle) {
        return Engine_String::substr($string, 0, $length).$etc;
      } else {
        return Engine_String::substr($string, 0, $length/2) . $etc . Engine_String::substr($string, -$length/2);
      }
    } else {
      return $string;
    }
  }

  public function isModuleExists($module)
  {
    if (!isset($this->_modules[$module])){
      $table = Engine_Api::_()->getDbTable('pages', 'page');
      $db = $table->getAdapter();
      $prefix = $table->getTablePrefix();
      $select = $db->select()->from($prefix.'core_modules', array('COUNT(*)'))->where('name = ?', $module);
      
      $this->_modules[$module] = (bool)$db->fetchOne($select);
    }
    return $this->_modules[$module];
  }

  public function getEnabledAddOns()
  {
    $enabledAddOns = array();
    $addOns = array(
      "pagevideo" => 'Page Video',
      "pagemusic" => 'Page Music',
      "pageevent" => "Page Event",
      "pagediscussion" => "Page Discussions",
      "pagedocument" => "Page Documents",
      "pageblog" => "Page Blog",
      "pagealbum" => "Page Album",
      "pagecontact" => "Page Contact",
      "pagefaq" => "Page FAQ"
    );

    foreach ($addOns as $type => $add_on) {
      if ($this->isModuleExists($type)) {
        $enabledAddOns[$type] = $add_on;
      }
    }
    asort($enabledAddOns);
    return $enabledAddOns;
  }

  public function isAllowedComment($pageObject)
  {
    return (bool)$pageObject->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'comment');
  }
  
  public function isAllowedView($pageObject)
  {
    if(!$pageObject)
      return false;
    return (bool)$pageObject->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'view');
  }
  
  public function getFriends($params)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getItemTable('user');
    $prefix = $table->getTablePrefix();
    
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($prefix.'users')
      ->joinLeft($prefix.'user_membership', $prefix.'user_membership.user_id = '.$prefix.'users.user_id', array())
      ->where($prefix.'user_membership.resource_id = ?', $viewer->getIdentity())
      ->where($prefix.'user_membership.resource_approved = 1')
      ->where($prefix.'user_membership.user_approved = 1');

    return Zend_Paginator::factory($select);
  }

  public function getFavoritePages(Page_Model_Page $page)
  {
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $favTable = Engine_Api::_()->getDbTable('favorites', 'page');

    $favName = $favTable->info('name');
    $name = $table->info('name');
    
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($name)
      ->joinInner($favName, $name.'.page_id = '.$favName.'.page_id', array())
      ->where($favName.'.page_fav_id = ?', $page->getIdentity())
      ->where($name.'.approved = 1');

    return Zend_Paginator::factory($select);
  }

  public function getFavorites($params = array())
  {
    $page_id = isset($params['page_id']) ? $params['page_id'] : 0;
    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $favTable = Engine_Api::_()->getDbTable('favorites', 'page');

    $favName = $favTable->info('name');
    $name = $table->info('name');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($name)
      ->joinInner($favName, $name.'.page_id = '.$favName.'.page_id', array())
      ->where($favName.'.page_fav_id = ?', $page_id)
      ->where($name.'.approved = 1');

    return Zend_Paginator::factory($select);
  }

  public function isActiveTransaction()
  {
    $session = new Zend_Session_Namespace('Page_Subscription');

    if ( !$session->order_id || !$session->page_id) {
      return false;
    }

    if (null == $order = Engine_Api::_()->getItem('page_order', $session->order_id)){
      return false;
    };

    if ( $order->source_type != 'page_subscription' || !in_array($order->state, array('initial', 'pending')) || $session->page_id != $order->source_id){
      return false;
    }

    if ( null == ($page = Engine_Api::_()->getItem($order->source_type, (int)$order->source_id))) {
      return false;
    }

    if( !in_array($page->status, array('initial', 'pending')) ) {
      return false;
    }

    return true;
  }

  public function sendNotification($item, $type)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    // Get Page Teams
    if ($item instanceof Page_Model_Page) {
      $admins = $item->getAdmins();
    } else {
      $admins = $item->getPage()->getAdmins();
    }
    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
    foreach ($admins as $admin){
      // if owner
      if ($admin->getIdentity() == $viewer->getIdentity()){
        continue;
      }
      // Send Notify
      $notifyApi->addNotification($admin, $viewer, $item, $type, array(
        'label' => $item->getShortType()
      ));
    }
  }

  public function getModuleDirectory($name)
  {
    $name = strtolower($name);
    $fc  = Zend_Controller_Front::getInstance();
    $path = $fc->getControllerDirectory($name);

    return dirname($path);
  }

  public function isPrivacyOld()
  {
    /**
     * @var $authTbl Authorization_Model_DbTable_Allow
     */
    $authTbl = Engine_Api::_()->getDbTable('Allow', 'Authorization');
    $select =$authTbl
      ->select()
      ->from($authTbl->info('name'), array(new Zend_Db_Expr("COUNT(*)")))
      ->where('resource_type = ?', 'page')
      ->where('action = ?', 'posting')
    ;


    return $select->query()->fetchColumn();
  }

  public function getSetIdByCaption($caption){

    $table = Engine_Api::_()->getDbTable('pages', 'page');
    $dba = $table->getDefaultAdapter();
    $prefix = $table->getTablePrefix();

    return $dba->fetchOne("SELECT id FROM {$prefix}page_category_set WHERE `caption` = ?", array($caption) );
  }

}