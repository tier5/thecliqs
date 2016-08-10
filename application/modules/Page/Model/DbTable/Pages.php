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

class Page_Model_DbTable_Pages extends Engine_Db_Table implements Engine_Content_Storage_Interface
{
	protected $_rowClass = 'Page_Model_Page';
  protected $_features = array();
  protected $owner;
	
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
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
		$select = $this->select();
    $prefix = $this->getTablePrefix();

		$select
			->setIntegrityCheck(false)
			->from($prefix.'page_pages')
			->joinInner($prefix."page_fields_values", $prefix."page_fields_values.item_id = ".$prefix."page_pages.page_id", array())
			->joinInner($prefix."page_fields_options", $prefix."page_fields_options.option_id = ".$prefix."page_fields_values.value", array('category' => 'label', 'category_id' => 'option_id'))
      ->joinLeft($prefix."page_category_set", "{$prefix}page_pages.set_id = {$prefix}page_category_set.id", array('category_set'=>'caption'))
			->where($prefix."page_fields_values.field_id = 1")
      ->where($prefix."page_pages.name <> 'header' AND ".$prefix."page_pages.name <> 'footer'")
			->where($prefix.'page_fields_options.field_id = ?', 1);

    if( !empty($params['setId']) )
      $select->where( "`{$prefix}page_pages`.`set_id` = {$params['setId']}");

    // Does have pages active offers? If they have, then sort pages by active offers
    $isOffersModuleEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('offers');
    $isPageSortOffersEnabled = $settings->getSetting('page.sort.active.offers', 1);
    if ($isOffersModuleEnabled && $isPageSortOffersEnabled) {
      $db = $this->getAdapter();
      $query = "select distinct page_id from `engine4_offers_offers` where page_id != 0 AND enabled = 1 AND (time_limit='unlimit' OR CURRENT_DATE() < endtime)";
      $offer_page_ids = $db->fetchCol($query);

      if ($offer_page_ids) {
        $offer_page_ids_str = implode(',', $offer_page_ids);
        $select
          ->columns(array('offer_order' => new Zend_Db_Expr('FIELD(' . $prefix . 'page_pages.page_id, ' . $offer_page_ids_str . ')')))
          ->order('offer_order DESC');
      }
    }

    // ------------ advanced search -------------
    if( !empty($params['adv_keyword']) && $params['adv_keyword'] != '') {
      $params['keyword'] = $params['adv_keyword'];
    }

    if( !empty($params['adv_street']) && $params['adv_street'] != '' ) {
      $params['street'] = $params['adv_street'];
    }

    if( !empty($params['adv_city']) && $params['adv_city'] != '' ) {
      $params['city'] = $params['adv_city'];
    }

    if( !empty($params['adv_state']) && $params['adv_state'] != '' ) {
      $params['state'] = $params['adv_state'];
    }

    if( !empty($params['adv_country']) && $params['adv_country'] != '' ) {
      $params['country'] = $params['adv_country'];
    }

    if( !empty($params['adv_category']) && $params['adv_category'] != '' ) {
      $params['fields']['profile_type'] = (isset($params['adv_category']) && $params['adv_category']) ? $params['adv_category'] : 0;
    }

    if( !empty($params['adv_approved']) && $params['adv_approved'] == 'true' ) {
      $params['approved'] = 1;
    }

    if( !empty($params['adv_featured']) && $params['adv_featured'] == 'true') {
      $params['featured'] = 1;
    }

    if( !empty($params['adv_sponsored']) && $params['adv_sponsored'] == 'true') {
      $params['sponsored'] = 1;
    }

    $unit = 1;
    if( $settings->getSetting('page.advsearch.unit', 'Miles') == 'Km' )
      $unit = 1.609344;
    if (!empty($params['adv_location'])) {
      $params['adv_location'] = urlencode($params['adv_location']);
      $http_prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
      $url = $http_prefix.'maps.google.com/maps/geo?&q=' . $params['adv_location'] . '&output=csv';
      if (($result = file_get_contents($url)) != false) {
        $resultParts = explode(',', $result);
        if ($resultParts[0] == 200) {
          $latitude = $resultParts[2];
          $longitude = $resultParts[3];
          $distance = $params['adv_within'];
          $select->joinLeft(
            $prefix . 'page_markers',
            $prefix . 'page_markers.page_id = ' . $prefix . 'page_pages.page_id',
            array(
              'distance' => new Zend_Db_Expr("(((acos(sin((" . $latitude . "*pi()/180)) * sin((" . $prefix."page_markers.`latitude`*pi()/180))+cos((" . $latitude . "*pi()/180)) * cos((".$prefix."page_markers.`latitude`*pi()/180)) * cos(((" . $longitude . "- ".$prefix."page_markers.`longitude`)*pi()/180))))*180/pi())*60*1.1515*{$unit})")
            )
          )->where("((acos(sin((" . $latitude . "*pi()/180)) * sin((" . $prefix."page_markers.`latitude`*pi()/180))+cos((" . $latitude . "*pi()/180)) * cos((".$prefix."page_markers.`latitude`*pi()/180)) * cos(((" . $longitude . "- ".$prefix."page_markers.`longitude`)*pi()/180))))*180/pi())*60*1.1515*{$unit} <= ".$distance);
        }
      }
    }

    //-----------------------------------------------
		
    // for SEO by Kirill
    if (!empty($params['tag_name'])){
			$select
				->joinInner($prefix.'core_tagmaps', $prefix.'core_tagmaps.resource_id = '.$prefix.'page_pages.page_id')
                ->joinInner($prefix.'core_tags', $prefix.'core_tagmaps.tag_id = '.$prefix.'core_tags.tag_id')
				->where($prefix."core_tagmaps.resource_type = ?", 'page')
				->where($prefix."core_tags.text = ?", $params['tag_name']);
		}

    if (!empty($params['category_name'])) {
      $db = $this->getAdapter();
      $query = 'select * from `engine4_page_fields_options` where `label` ="'.$params['category_name'].'"';
      $result = $db->fetchOne($query);
      if($result) {
        $fields = array('separator1' => '', 'separator2' => '', 'profile_type' => $result);
        $select
          ->joinLeft($prefix.'page_fields_search', $prefix.'page_fields_search.item_id = '.$prefix.'page_pages.page_id', array());

        $searchParts = Engine_Api::_()->fields()->getSearchQuery('page', $fields);
        foreach ($searchParts as $k => $v)
          $select->where("`".$prefix."page_fields_search`.{$k}", $v);
      }
		}
            
        // for SEO by Kirill
		if (!empty($params['tag_id'])){
			$select
				->joinInner($prefix.'core_tagmaps', $prefix.'core_tagmaps.resource_id = '.$prefix.'page_pages.page_id')
				->where($prefix."core_tagmaps.resource_type = 'page'")
				->where($prefix."core_tagmaps.tag_id = ?", $params['tag_id']);
		}

    if (!empty($params['badge'])){
      $select
          ->joinInner($prefix.'hebadge_pagemembers', $prefix.'hebadge_pagemembers.page_id = '.$prefix.'page_pages.page_id')
          ->where($prefix."hebadge_pagemembers.approved = ?", 1)
          ->where($prefix."hebadge_pagemembers.pagebadge_id = ?", $params['badge']);
    }
		
		if (!empty($params['search'])){
			$select->where($prefix."page_pages.search = ?", $params['search']);
		}

    if (!empty($params['abc'])) {
      $params['abc'] = trim($params['abc']);
      if($params['abc'] == '#') {
        $select
          ->where($prefix."page_pages.title REGEXP '^[0-9]' ");
      } else {
        $select
          ->where($prefix."page_pages.title like ?", $params['abc'].'%');
      }
		}

    $select->order($prefix.'page_pages.sponsored DESC');

		if (isset($params['sort'])){
			switch ($params['sort']){
				case 'recent' :
					$select
						->order($prefix.'page_pages.creation_date DESC');
					break;
				case 'popular' : 
					$select
						->order($prefix.'page_pages.view_count DESC');
					break;
				case 'sponsored' :
					$select
						->where($prefix.'page_pages.sponsored = ?', 1);
					break;
				case 'featured' :
					$select
						->where($prefix.'page_pages.featured = ?', 1);
					break;
			}
		}
		
		if (!empty($params['where'])) {
			$select->where($params['where']);
		}

    if(!empty($params['country'])) {
      $select->where($prefix.'page_pages.country LIKE ?', '%'.$params['country'].'%');
    }

    if(!empty($params['state'])) {
      $select->where($prefix.'page_pages.state LIKE ?', '%'.$params['state'].'%');
    }

		if (!empty($params['city'])) {
			$select->where($prefix."page_pages.city LIKE ?", '%'.$params['city'].'%');
		}

    if(!empty($params['street'])) {
      $select->where($prefix.'page_pages.street LIKE ?', '%'.$params['street'].'%');
    }

		if (!empty($params['keyword'])) {
			$select->where($prefix."page_pages.title LIKE '%{$params['keyword']}%' OR ".$prefix."page_pages.description LIKE '%{$params['keyword']}%' OR ".$prefix."page_pages.keywords LIKE '%{$params['keyword']}%'");
		}
		
		if (!empty($params['fields'])) {
      $fields = (is_array($params['fields'])) ? $params['fields'] : array($params['fields']);
			$select
				->joinLeft($prefix.'page_fields_search', $prefix.'page_fields_search.item_id = '.$prefix.'page_pages.page_id', array());
			$searchParts = Engine_Api::_()->fields()->getSearchQuery('page', $fields);
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
			$select->where($prefix."page_pages.page_id = ?", $params['page_id']);
		}

    if (!empty($params['approved'])) {
			$select->where($prefix."page_pages.approved = ?", $params['approved']);
		}

    if (!empty($params['user_id'])) {
			$select->where($prefix."page_pages.user_id = ?", $params['user_id']);
		}

    if (!empty($params['featured'])) {
			$select->where($prefix."page_pages.featured = ?", $params['featured']);
		}

    if (!empty($params['sponsored'])) {
			$select->where($prefix."page_pages.sponsored = ?", $params['sponsored']);
		}

    if ($settings->getSetting('page.package.enabled', 0)) {
      $select->where($prefix.'page_pages.enabled = ?', 1);

      if (!$settings->getSetting('default.package.enabled', 1)) {
        $select
          ->where($prefix . 'page_pages.package_id <> ?', $settings->getSetting('page.default.package', 1))
          ->where($prefix . 'page_pages.package_id <> ?', 0);
      }
    }

		return $select;
	}
	
	public function getPaginator($params = array())
	{
		$select = $this->getSelect($params);
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));

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

	public function loadContent(Engine_Content $content, $name)
  {
    if( is_array($name) ) {
      $name = join('_', $name);
    }

    if( !is_string($name) && !is_numeric($name) ) {
      throw new Exception('not string');
    }

    $this->pageObject = $pageObject = Engine_Api::_()->core()->getSubject();

    $this->owner = $pageObject->owner;

    if( !is_object($pageObject) ) {
      // throw?
      return null;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    // Get all content
    $contentTable = Engine_Api::_()->getDbtable('content', 'page');

    // Timeline Page
    $is_timeline = false;
    if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('timeline')) {
      $is_timeline = $pageObject->isTimeline();
    }

    $select = $contentTable->select()
      ->where('page_id = ?', $pageObject->page_id)
      ->where('is_timeline = ?', $is_timeline)
      ->order('order ASC');
      
    $content = $contentTable->fetchAll($select);


    $api = Engine_Api::_()->getDbTable('modules' ,'hecore');

    if ($content && $api->isModuleEnabled('wall')) {

      if (!($api->isModuleEnabled('touch') && Engine_Api::_()->touch()->siteMode() == 'touch') && !($api->isModuleEnabled('mobile') && Engine_Api::_()->mobile()->siteMode() == 'mobile')){
        foreach ($content as $key => $content_info) {
          if (!empty($content_info['name']) && $content_info['name'] == 'page.feed') {
            $content[$key]['name'] = 'wall.feed';
          }
        }
      }
    }

    $user = isset($this->owner) ? $this->owner : null;

    if ($settings->getSetting('page.package.enabled', 0)) {
      $this->_features = (array) $pageObject->getPackage()->modules;
    } else {
      $this->_features = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('page', $user, 'auth_features');
    }

    // Create structure
    $structure = $this->prepareContentArea($content);

    // Create element (with structure)
    $element = new Engine_Content_Element_Container(array(
      'class' => 'layout_page_' . $pageObject->name,
      'elements' => $structure
    ));

    return $element;
  }
	
	public function prepareContentArea($content, $current = null)
  {
    $parent_content_id = 0;
    if( null !== $current ) {
      $parent_content_id = $current->content_id;
    }

    $struct = array();

    $tmp = array();
		$tmp[0]="pagealbum";
    $tmp[1]="pageblog";
    $tmp[2]="pagediscussion";
    $tmp[3]="pageevent";
    $tmp[4]="pagemusic";
    $tmp[5]="pagevideo";
    $tmp[6]="rate";
    $tmp[7]="pagecontact";
    $tmp[8]="pagefaq";

    foreach( $content->getRowsMatching('parent_content_id', $parent_content_id) as $child ) {
      $arrayName = explode('.', $child->name);
      $name = $arrayName[0];

      if(in_array($name, $tmp) && !in_array($name, $this->_features) && !is_null($this->owner))
        continue;
      $elStruct = $this->createElementParams($child);
      $elStruct['elements'] = $this->prepareContentArea($content, $child);
      $struct[] = $elStruct;
    }

    return $struct;
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
  
  public function deletePage(Page_Model_Page $page)
  {    
    $page->delete();
    return $this;
  }
   
  public function deletePages($page_ids)
  {
  	if (empty($page_ids)) {
      return $this;
    }
    
    foreach ($page_ids as $page_id) {
    	Engine_Api::_()->getItem('page', (int)$page_id)->delete();
    }
    
    return $this;
  }

	public function createContentFirstTime($pageId)
	{
		$contentTable = Engine_Api::_()->getDbtable('content', 'page');
		$contentRow = $this->createContentItem(array('page_id' => $pageId, 'type' => 'container', 'order' => 2, 'name' => 'main', 'parent_content_id' => 0));
		$contentRow2 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'container', 'order' => 6, 'name' => 'middle', 'parent_content_id' => $contentRow->content_id));
		$contentRow3 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'container', 'order' => 4, 'name' => 'left', 'parent_content_id' => $contentRow->content_id));
		$contentRow4 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"max":"10"}', 'order' => 12, 'name' => 'core.container-tabs', 'parent_content_id' => $contentRow2->content_id));
		$contentRow5 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"title":"Photo", "titleCount":false}', 'order' => 3, 'name' => 'page.profile-photo', 'parent_content_id' => $contentRow3->content_id));
		$contentRow6 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"title":"Options", "titleCount":false}', 'order' => 4, 'name' => 'page.profile-options', 'parent_content_id' => $contentRow3->content_id));

		$table = Engine_Api::_()->getDbTable('modules', 'core');
		if ($table->isModuleEnabled('rate'))
		{
			$contentRow6 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"title":"Rate", "titleCount":false}', 'order' => 5, 'name' => 'rate.widget-rate', 'parent_content_id' => $contentRow3->content_id));
		}

		$contentRow7 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"title":"Note", "titleCount":false}', 'order' => 6, 'name' => 'page.profile-note', 'parent_content_id' => $contentRow3->content_id));
		$contentRow8 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"title":"Map", "titleCount":false}', 'order' => 7, 'name' => 'page.profile-map', 'parent_content_id' => $contentRow3->content_id));
		$contentRow9 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"title":"Team", "titleCount":true}', 'order' => 8, 'name' => 'page.profile-admins', 'parent_content_id' => $contentRow3->content_id));
		$contentRow10 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"title":"like_Like Club", "titleCount":true}', 'order' => 9, 'name' => 'like.box', 'parent_content_id' => $contentRow3->content_id));
		$contentRow14 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"title":"Tag Cloud", "titleCount":false}', 'order' => 10, 'name' => 'page.tag-cloud', 'parent_content_id' => $contentRow3->content_id));

		$contentRow10 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"title":"Updates"}', 'order' => 13, 'name' => 'page.feed', 'parent_content_id' => $contentRow4->content_id));
		$contentRow11 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'params' => '{"title":"Info"}', 'order' => 14, 'name' => 'page.profile-fields', 'parent_content_id' => $contentRow4->content_id));

		$contentRow13 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'order' => 11, 'name' => 'page.search', 'parent_content_id' => $contentRow2->content_id));
		$contentRow12 = $this->createContentItem(array('page_id' => $pageId, 'type' => 'widget', 'order' => 10, 'name' => 'like.status', 'parent_content_id' => $contentRow2->content_id));

		$db = $table->getAdapter();

		$prefix = $table->getTablePrefix();

		$select = $db->select()->from($prefix.'page_modules');
		$modules = $db->fetchAll($select);



		foreach ($modules as $module)
		{
			if (!$table->isModuleEnabled($module['name'])) continue ;
			if ($module['name'] == 'weather') continue ;
      if ($module['name'] == 'inviter') continue ;
			$contentRow13 = $this->createContentItem(array(
				'type' => 'widget',
				'params' => $module['params'],
				'order' => $module['order'],
				'name' => $module['widget'],
				 'page_id' => $pageId,
				'parent_content_id' => $contentRow4->content_id
			));
		}
	}

	public function createContentItem(Array $params)
	{
		if (empty($params)) {
			return false;
		}
		if (!isset($contentTable)) {
			$contentTable = Engine_Api::_()->getDbtable('content', 'page');
		}

		$contentRow = $contentTable->createRow();
		foreach ($params as $key => $value) {
			if ($key == "") {
				continue;
			}
			if ($value === null) {
				$value = "";
			}
			$contentRow->$key = $value;
		}
		$contentRow->save();
		return $contentRow;
	}

  public function getCountPagesByLetter($letter)
  {
    $select = $this->select()
      ->from($this->info('name'), array('count' => 'COUNT(page_id)'));
    if ($letter == '#') {
      $select
        ->where("LEFT(title, 1) IN('0', '1', '2', '3', '4', '5', '6', '7', '8', '9')");
    } else {
      $select
        ->where("title like '{$letter}%'");
    }

    return $this->fetchRow($select)->count;
  }

  public function hasPages( $user_id, $page_id = false ) {
    if( $user_id instanceof Core_Model_Item_Abstract ) {
      $user_id = $user_id->getIdentity();
    }

    $select = $this->select()
      ->from($this->info('name'), array('count' => new Zend_Db_Expr("COUNT(*)")))
      ->where('user_id = ?', $user_id);
    if( $page_id ) {
      $select->where('page_id <> ?', $page_id);
    }

    $res = $select->query()->fetch();
    return ($res['count'] > 0);
  }

  public function getRandomSelect($params = array())
  {
    $prefix = $this->getTablePrefix();
    $select = $this->select()
      ->from($prefix.'page_pages', '*, page_id*0+RAND() as random')
      ->where($prefix."page_pages.approved = 1")
      ;

    if (!empty($params['featured'])) {
      $select->where($prefix."page_pages.featured = ?", $params['featured']);
    }

    if (!empty($params['sponsored'])) {
      $select->where($prefix."page_pages.sponsored = ?", $params['sponsored']);
    }

    $select->order('random');

    return $select;
  }

  public function getRandomPaginator($params = array())
  {
    $select = $this->getRandomSelect($params);
    $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));

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
}