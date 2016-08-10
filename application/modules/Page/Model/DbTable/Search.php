<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Lists.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Search extends Engine_Db_Table
{
	public function getSelect($params = array())
	{
		$select = $this->select();

		if (!empty($params['keyword'])){

      $where = "
        ( title LIKE '%".$params['keyword']."%' AND object <> 'pagediscussion_pagepost' )
          OR
        ( body LIKE '%".$params['keyword']."%' AND object <> 'pagediscussion_pagetopic' )
      ";
			$select->where($where);
		}

		if (!empty($params['page_id'])){
			$select
				->where("page_id = ?", $params['page_id']);
		}

		if (!empty($params['object'])) {
			if (is_array($params['object'])) {
				$where = "'".implode("','", $params['object'])."'";
				$select
					->where("object IN (".$where.")");
			}else{
				$select
					->where("object = ?", $params['object']);
			}
		}

		if (!empty($params['object_id'])) {
			$select
				->where("object_id = ?", $params['object_id']);
		}

		if (!empty($params['title'])) {
			$select
				->where("title LIKE '%".$params['title']."%' AND object <> 'pagediscussion_pagepost'");
		}

		if (!empty($params['body'])) {
			$select
				->where("body LIKE '%".$params['body']."%' AND object <> 'pagediscussion_pagetopic'");
		}

		return $select;
	}
	
	public function getItems($params = array(), $categorized = true, $itemFetched = false)
	{
		$select = $this->getSelect($params);
		$rawData = $this->fetchAll($select);
		$storage = Engine_Api::_()->storage();
		$api = Engine_Api::_()->getApi('core', 'page');
		$items = array();
		foreach ($rawData as $data){

			if ($categorized){
				$type = $api->shortenType($data['object']);


				if ($data['object'] == 'store_product') {

						$data['photo_id'] = Engine_Api::_()->getItem($data['object'], (int)$data['object_id'])->getPhotoUrl();

				}elseif ($data['photo_id']) {
					$photo = $storage->get($data['photo_id']);

					if ($photo){
						$data['photo_id'] = $photo->map();
					}else{
						$data['photo_id'] = $api->getNoPhoto($data['object']);
					}
				}else{
					$data['photo_id'] = $api->getNoPhoto($data['object']);
				}

				if ($itemFetched){
					$items[$type][] = Engine_Api::_()->getItem($data['object'], (int)$data['object_id']);
				}else{
					$items[$type][] = $data;
				}
			}else{
				if ($itemFetched){
					$items[] = Engine_Api::_()->getItem($data['object'], (int)$data['object_id']);
				}else{
					$items[] = $data;
				}
			}
		}

		$data = array();
		if ($categorized){
			foreach ($items as $key => $value) {
				$data[$key] = Zend_Paginator::factory($value);
        $data[$key]->setItemCountPerPage(100);
			}

			return $data;
		}

		return Zend_Paginator::factory($items);
	}
	
	public function saveData($data)
	{
		if ($data instanceof Core_Model_Item_Abstract) {
			$params = array(
				'object' => $data->getType(),
				'object_id' => (int)$data->getIdentity(),
				'page_id' => (int)$data->getPage()->getIdentity(),
				'title' => strip_tags($data->getTitle()),
				'photo_id' => (int)(isset($data->photo_id) ? $data->photo_id : ($data->getType() == 'pagealbumphoto' ? $data->file_id : 0)),
				'body' => strip_tags(isset($data->description) ? $data->description : (isset($data->body) ? $data->body : ''))
			);
		} elseif (is_array($data)) {
			$params = $data;
		} else {
			return false;
		}

		return $this->saveDataFromArray($params);
	}

	public function saveDataFromArray(array $params)
	{
		$title = (string)$params['title'];
		$body = (string)$params['body'];
		$page_id = (int)$params['page_id'];

		unset($params['title']);
		unset($params['body']);

		$select = $this->getSelect($params);
		$row = $this->fetchRow($select);

		if (!$row){
			$row = $this->createRow();
		}

		$row->object = $params['object'];
		$row->object_id = (int)$params['object_id'];
		$row->photo_id = (int)$params['photo_id'];
		$row->title = $title;
		$row->body = $body;
		$row->page_id = $page_id;

		$row->save();

		return $row;
	}

	public function deleteData($data)
	{
		if ($data instanceof Core_Model_Item_Abstract){
			$params = array(
				'object = ?' => $data->getType(),
				'object_id = ?' => (int)$data->getIdentity(),
				'page_id = ?' => (int)$data->getPage()->getIdentity()
			);
		}elseif (is_array($data)){
			$params = array(
				'object = ?' => $data['object'],
				'object_id = ?' => (int)$data['object_id'],
				'page_id = ?' => (int)$data['page_id']
			);
		}else{
			return false;
		}

		$this->delete($params);

		return $this;
	}
	
}