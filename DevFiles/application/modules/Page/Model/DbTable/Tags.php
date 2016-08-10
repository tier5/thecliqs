<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Tags.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Page_Model_DbTable_Tags extends Engine_Db_Table
{
	protected $_name = 'page_tags';

  protected $_rowClass = 'Page_Model_Tag';

	public function getCloud($params = array())
	{
		$select = $this->select();
		$name = $this->info('name');

		$tmTable = Engine_Api::_()->getDbTable('tagMaps', 'page');
		$tmName = $tmTable->info('name');

		$sTable = Engine_Api::_()->getDbTable('search', 'page');
		$sName = $sTable->info('name');

		$select
			->setIntegrityCheck(false)
			->from($name)
			->joinLeft($tmName, $tmName.".tag_id=".$name.".tag_id", array('freq' => 'COUNT(*)'))
			->joinLeft($sName, $sName.'.object = '.$tmName.'.resource_type AND '.$sName.'.object_id = '.$tmName.'.resource_id', array())
			->where($sName.'.object_id IS NOT NULL')
			->group($name.'.tag_id');

		if (!empty($params['page_id'])){
			$select
				->where($tmName.'.page_id = ?', $params['page_id']);
		}

		return $this->fetchAll($select);
	}

	public function getSelect($params = array())
	{
		$select = $this->select();
		$name = $this->info('name');

		$tmTable = Engine_Api::_()->getDbTable('tagMaps', 'page');
		$tmName = $tmTable->info('name');

		$sTable = Engine_Api::_()->getDbTable('search', 'page');
		$sName = $sTable->info('name');

		$select
			->setIntegrityCheck(false)
			->from($name)
			->joinLeft($tmName, $tmName.".tag_id=".$name.".tag_id")
			->joinLeft($sName, $sName.'.object = '.$tmName.'.resource_type AND '.$sName.'.object_id = '.$tmName.'.resource_id')
			->where($sName.'.object_id IS NOT NULL');

    if (!empty($params['object'])) {
				$where = "'".implode("','", $params['object'])."'";
				$select
					->where($tmName.".resource_type IN (".$where.")");
		}

		if (!empty($params['keyword'])){
			$select
				->where($name.".text LIKE '%".$params['keyword']."%'");
		}

		if (!empty($params['page_id'])){
			$select
				->where($tmName.".page_id = ?", $params['page_id']);
		}

		if (!empty($params['tag_id'])){
			$select
				->where($tmName.'.tag_id = ?', $params['tag_id']);
		}

		if (!empty($params['limit'])){
			$select
				->limit($params['limit']);
		}

		if (!empty($params['group'])){
			$select
				->group($params['group']);
		}

		return $select;
	}

	public function getPaginator($params = array())
	{
		$select = $this->getSelect($params);
		$paginator = Zend_Paginator::factory($select);

		if (!empty($params['ipp'])){
			$paginator->setItemCountPerPage($params['ipp']);
		}

		if (!empty($params['p'])){
			$paginator->setCurrentPageNumber($params['p']);
		}
		
		return $paginator;
	}

	public function getItems($params = array())
	{
		$select = $this->getSelect($params);
		$rawData = $this->fetchAll($select);
		$storage = Engine_Api::_()->storage();
		$api = Engine_Api::_()->getApi('core', 'page');
		$tag = '';

		$items = array();
		foreach ($rawData as $data){
			$type = $api->shortenType($data['object']);

			if ($data['photo_id']){
				$photo = $storage->get($data['photo_id']);
				if ($photo){
					$data['photo_id'] = $photo->map();
				}else{
					$data['photo_id'] = $api->getNoPhoto($data['object']);
				}
			}else{
				$data['photo_id'] = $api->getNoPhoto($data['object']);
			}
			
			$items[$type][] = $data;
			$tag = $data['text'];
		}

		$data = array();
		foreach ($items as $key => $value) {
			$data[$key] = Zend_Paginator::factory($value);
		}

		return array('data' => $data, 'tag' => $tag);
	}

  /**
   * Get the tag table
   *
   * @return Engine_Db_Table
   */
  public function getTagTable()
  {
    return $this;
  }

  /**
   * Get the tag map table
   *
   * @return Engine_Db_Table
   */
  public function getMapTable()
  {
    return Engine_Api::_()->getDbtable('TagMaps', 'page');
  }



  // Tags

  /**
   * Get an existing or create a new text tag
   *
   * @param string $text The tag text
   * @return Core_Model_Tag
   */
  public function getTag($text)
  {
    $text = $this->formatTagText($text);

    $table = $this->getTagTable();
    $select = $table->select()
      ->where('text = ?', $text);

    $row = $table->fetchRow($select);

    if( null === $row )
    {
      $row = $table->createRow();
      $row->text = $text;
      $row->save();
    }

    return $row;
  }

  public function getTagsByTagger($resource, Core_Model_Item_Abstract $tagger)
  {
    if( $resource instanceof Core_Model_Item_Abstract )
    {
      $resource_type = $resource->getType();
    }
    else
    {
      $resource_type = (string) $resource;
    }

    $mapTable = $this->getMapTable();
    $select = $mapTable->select()
      ->where('resource_type = ?', $resource_type)
      //->where('resource_id = ?', $resource->getIdentity())
      ->where('tagger_type = ?', $tagger->getType())
      ->where('tagger_id = ?', $tagger->getIdentity())
      ->order('tag_id ASC');

    $identities = array();
    foreach( $mapTable->fetchAll($select) as $tagmap )
    {
      $identities[] = $tagmap->tag_id;
    }

    return Engine_Api::_()->getItemMulti('page_tag', $identities);
  }

  public function getTagsByText($text = null, $limit = 10)
  {
    $table = $this->getTagTable();
    $select = $table->select()
      ->order('text ASC')
      ->limit($limit);

    if( $text )
    {
      $select->where('text LIKE ?', '%'.$text.'%');
    }

    return $table->fetchAll($select);
  }

  /**
   * Called on text tags for formatting
   *
   * @param string $text
   * @return string
   */
  public function formatTagText($text)
  {
    // We can do formatting on tags later
    return trim($text);
  }



  // Tagging

  /**
   * Tag a resource
   *
   * @param Core_Model_Item_Abstract $resource The resource being tagged
   * @param Core_Model_Item_Abstract $tagger The resource doing the tagging
   * @param string|Core_Model_Item_Abstract $tag What is tagged in resource
   * @param array|null $extra
   * @return Engine_Db_Table_Row|null
   */
  public function addTagMap(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $tagger, $tag)
  {
    $tag = $this->_getTag($tag, true);

    if( !$tag ) return;

    // Check if resource was already tagged with this
    if( null !== $this->getTagMap($resource, $tag) )
    {
      return;
      //throw new Core_Model_Exception('Resource was already tagged as this');
    }

    // Do the tagging
    $table = $this->getMapTable();
    $tagmap = $table->createRow();
    $tagmap->setFromArray(array(
      'resource_type' => $resource->getType(),
      'resource_id' => $resource->getIdentity(),
			'page_id' => $resource->getPage()->getIdentity(),
      'tagger_type' => $tagger->getType(),
      'tagger_id' => $tagger->getIdentity(),
      'tag_id' => $tag->getIdentity()
    ));
    $tagmap->save();

    return $tagmap;
  }

  /**
   * Add multiple tags
   *
   * @param Core_Model_Item_Abstract $resource
   * @param Core_Model_Item_Abstract $tagger
   * @param array $tags
   * @return array
   */
  public function addTagMaps(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $tagger, array $tags)
  {
    $tagmaps = array();
    foreach( $tags as $key => $value )
    {
      if( is_numeric($key) )
      {
        $tagmaps[] = $this->addTagMap($resource, $tagger, $value);
      }
      else
      {
        $tagmaps[] = $this->addTagMap($resource, $tagger, $key, $value);
      }
    }
    return $tagmaps;
  }

  /**
   * Get a tag map on resource and existing tag (for checking if already tagged)
   *
   * @param Core_Model_Item_Abstract $resource
   * @param string|Core_Model_Item_Abstract $tag
   * @return Engine_Db_Table|null
   */
  public function getTagMap(Core_Model_Item_Abstract $resource, $tag)
  {
    $tag = $this->_getTag($tag);

    $table = $this->getMapTable();
    $select = $table->select()
      ->where('resource_type = ?', $resource->getType())
      ->where('resource_id = ?', $resource->getIdentity())
      ->where('tag_id = ?', $tag->getIdentity())
      ->limit(1)
      ;

    $tagmap = $table->fetchRow($select);
    return $tagmap;
  }

  /**
   * Get a tagmap by id that is part of resource
   *
   * @param Core_Model_Item_Abstract $resource
   * @param integer $id
   */
  public function getTagMapById(Core_Model_Item_Abstract $resource, $id)
  {
    $table = $this->getMapTable();
    $select = $table->select()
      ->where('resource_type = ?', $resource->getType())
      ->where('resource_id = ?', $resource->getIdentity())
      ->where('tagmap_id = ?', (int) $id)
      ->limit(1)
      ;

    $tagmap = $table->fetchRow($select);
    return $tagmap;
  }

  /**
   * Get all tags for a resource
   *
   * @param Core_Model_Item_Abstract $resource
   * @return Engine_Db_Table_Rowset
   */
  public function getTagMaps(Core_Model_Item_Abstract $resource)
  {
    return $this->getMapTable()->fetchAll($this->getTagMapSelect($resource));
  }

  /**
   * Get a select object for tags on a resource
   *
   * @param Core_Model_Item_Abstract $resource
   * @return Zend_Db_Table_Select
   */
  public function getTagMapSelect(Core_Model_Item_Abstract $resource)
  {
    $table = $this->getMapTable();
    $select = $table->select()
      ->where('resource_type = ?', $resource->getType())
      ->where('resource_id = ?', $resource->getIdentity())
      ->order('tag_id ASC');

    return $select;
  }

  public function setTagMaps(Core_Model_Item_Abstract $resource, $tagger, array $tags)
  {
    $existingTagMaps = $this->getTagMaps($resource);
    $added = array();
    $setTagIndex = array();
    $tagObjects = array();
		
    foreach( $tags as $tag )
    {
      if(!empty($tag)){
        $tagObject = $this->_getTag($tag);
        $tagObjects[] = $tagObject;
        $setTagIndex[$tagObject->getGuid()] = $tagObject;
      }
    }

    // Check for new tags
    foreach( $tagObjects as $tag )
    {
      if( !$existingTagMaps->getRowMatching(array(
          'tag_id' => $tag->getIdentity(),
        )) ) {
        $added[] = $this->addTagMap($resource, $tagger, $tag);
      }
    }

    // Check for removed tags
    foreach( $existingTagMaps as $tagmap )
    {
      $key = 'page_tag_' . $tagmap->tag_id;
      if( empty($setTagIndex[$key]) )
      {
        $tagmap->delete();
      }
    }

    return $added;
  }

  public function removeTagMap(Core_Model_Item_Abstract $resource, $tag)
  {
    $tag = $this->_getTag($tag);
  }




  // Resources

  public function getResourcesByTagSelect($tag, array $params = array())
  {
    if( is_string($tag) ) {
      $tag = $this->_getTag($tag);
    }

    $table = $this->getMapTable();
    $select = $table->select()
      ->where('tag_id = ?', $tag->getIdentity())
      ;

    if( !empty($params['resource_types']) ) {
      $types = array();
      foreach( $params['resource_types'] as $type )
      {
        if( !Engine_Api::_()->hasItemType($type) ) continue;
        $types[] = $type;
      }
      $select->where('resource_type IN(\''.join("', '", $types).'\')');
    }

    return $select;
  }






  // Utility

  /**
   * Gets an existing string tag or returns the passed item
   *
   * @param string|Core_Model_Item_Abstract $tag
   * @return Core_Model_Item_Abstract
   * @throws Core_Model_Exception If argument is not a string or an item
   */
  protected function _getTag($tag, $return = false)
  {
    if( is_string($tag) )
    {
      $tag = $this->getTag($tag);
    }

    if( !($tag instanceof Core_Model_Item_Abstract) || !$tag->getIdentity() )
    {
      if( $return )
      {
        return null;
      }
      else
      {
        throw new Core_Model_Exception('Improper tag');
      }
    }

    return $tag;
  }
}