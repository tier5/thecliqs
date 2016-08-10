<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_SearchController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$this->view->content = $this->_getParam('content');
		$this->view->keyword = trim(strip_tags($this->_getParam('keyword')));
		$this->view->page_id = (int)$this->_getParam('page_id');

		$this->view->labels = array(
			'album' => 'Albums',
			'albumphoto' => 'Photos',
      'document' => 'Documents',
			'video' => 'Videos',
			'blog' => 'Blogs',
			'playlist' => 'Playlists',
			'song' => 'Songs',
			'review' => 'Reviews',
      'discussion_pagetopic' => 'Topic',
      'discussion_pagepost' => 'Post',
      'event' => 'Event',
			'store_product' => 'STORE_Products',
      'offer' => 'Offers',
      'donation' => 'Donations'
		);
	}
	
	public function indexAction()
	{
		$page_id = (int)$this->_getParam('page_id');
		$keyword = trim(strip_tags($this->_getParam('keyword')));

		if (!$page_id || !$keyword){
			$this->view->html = false;
			return ;
		}

		/**
		 * @var $api Page_Model_DbTable_Search
		 */
		$api = Engine_Api::_()->getDbTable('search', 'page');
		$apiTag = Engine_Api::_()->getDbTable('tags', 'page');

    $modules = Engine_Api::_()->getDbTable('content', 'page')->getEnabledExistAddOns($page_id);

    $content = array();
    foreach ($modules as $module) {
      if ($module['name'] == 'rate'){
        $content[] = 'pagereview';
      } elseif ($module['name'] == 'pagemusic'){
        $content[] = 'playlist';
				$content[] = 'song';
      } elseif ($module['name'] == 'pagealbum'){
        $content[] = 'pagealbum';
				$content[] = 'pagealbumphoto';
      } elseif ($module['name'] == 'pagediscussion'){
        $content[] = 'pagediscussion_pagetopic';
				$content[] = 'pagediscussion_pagepost';
      } elseif ($module['name'] == 'store') {
        $content[] = 'store_product';
      } elseif ($module['name'] == 'offers') {
        $content[] = 'offer';
      } else {
        $content[] = $module['name'];
      }
    }

		$params = array('page_id' => $page_id, 'keyword' => $keyword, 'object' => $content);
		$this->view->items = $items = $api->getItems($params);

		$params = array('page_id' => $page_id, 'keyword' => $keyword, 'group' => $apiTag->info('name').'.tag_id', 'object' => $content);
		$this->view->tags = $apiTag->getPaginator($params);
		$this->view->tags->setItemCountPerPage(4);

		if (!count($items)){
			$this->view->html = false;
			$this->view->tab_html = "<ul class='form-errors'><li>".$this->view->translate('There is no items matching your criteria.')."</li></ul>	";
			return ;
		}

		foreach ($items as $paginator){
			$paginator->setItemCountPerPage(2);
		}

		$this->view->html = $this->view->render('_searchItems.tpl');

		foreach ($items as $paginator){
			$paginator->setItemCountPerPage(100);
		}

		$this->view->tab_html = $this->view->render('_searchTab.tpl');
	}

	public function filterAction()
	{
		$api = Engine_Api::_()->getDbTable('search', 'page');
		$object = is_array($this->view->content) ? array_keys($this->view->content) : array();
		$keyword = $this->view->keyword;
		$page_id = $this->view->page_id;

		$objects = array();
		foreach ($object as $ob) {
			if ($ob == 'pagemusic'){
				$objects[] = 'playlist';
				$objects[] = 'song';
			}elseif ($ob == 'pagealbum') {
				$objects[] = 'pagealbum';
				$objects[] = 'pagealbumphoto';
      }elseif ($ob == 'pagediscussion') {
				$objects[] = 'pagediscussion_pagetopic';
				$objects[] = 'pagediscussion_pagepost';
			}else{
				$objects[] = $ob;
			}
		}
		
		$params = array('page_id' => $page_id, 'keyword' => $keyword, 'object' => $objects);

		$this->view->items = $items = $api->getItems($params);

		if (!count($items)) {
			$this->view->html = "<ul class='form-errors'><li>".$this->view->translate('There is no items matching your criteria.')."</li></ul>	";
			return ;
		}

		foreach ($items as $paginator){
			$paginator->setItemCountPerPage(100);
		}
		
		$this->view->html = $this->view->render('_searchTab.tpl');
	}

	public function tagAction()
	{
		$params = array(
			'page_id' => (int)$this->_getParam('page_id'),
			'tag_id' => (int)$this->_getParam('tag_id'),
		);

		$api = Engine_Api::_()->getDbTable('tags', 'page');
		$data = $api->getItems($params);

		$this->view->items = $items = $data['data'];
		$this->view->tag = $data['tag'];
		
		foreach ($items as $paginator){
			$paginator->setItemCountPerPage(100);
		}

		$this->view->html = $this->view->render('_tagTab.tpl');
	}
}