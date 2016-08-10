<?php

class Yncontest_Model_Category extends Core_Model_Item_Abstract
{

	protected $_searchTriggers = false;

	const MAX_LEVEL = 8;

	public function getId()
	{
		return $this -> category_id;
	}

	public function getType()
	{
		return 'category';
	}

	public function getName()
	{
		return $this -> name;
	}

	public function getTitle()
	{
		return $this -> name;
	}

	public function getAscendant($category_id = null)
	{
		$ascendantIds = array();
		$table = $this -> _table;
		$select = $table -> select();
		if (!$category_id)
		{
			$select -> where('category_id = ?', $this -> parent_category_id);
		}
		else
		{
			$select -> where('category_id = ?', $category_id);
		}
		$result = $table -> fetchRow($select);
		if (count($result) > 0)
		{
			if ($result -> parent_category_id == 0)
			{
				$ascendantIds[] = $result;
			}
			else
			{
				$ascendantIds = $this -> getAscendant($result -> parent_category_id);
				$ascendantIds[] = $result;
			}
		}
		return $ascendantIds;
	}

	public function getHref()
	{
		return $this -> slug;
	}

	public function getPath()
	{
		foreach ($this->getAscendant() as $item)
		{
			$result[] = sprintf('<a href="%s">%s</a>', $item -> getHref(), $item -> getName());
		}
		return implode(' - ', $result);
	}

	public function getSimplePath($glue = ' &raquo; ')
	{

		$result = array();
		foreach ($this->getAscendant() as $item)
		{
			$result[] = $item -> getName();
		}
		return implode($glue, $result);
	}

	public function getParent($recurseType = NULL)
	{
		if ($this -> parent_category_id)
		{
			return $this -> _table -> find($this -> parent_category_id) -> current();
		}
		return NULL;
	}

	public function getLevel()
	{
		return $this -> level;
	}

	public function getIndexKey($index)
	{
		return 'p' . $index;
	}

	public function getIndexValue($index)
	{
		return $this -> {'p' . ($index)};
	}

	public function getIndexTree($index)
	{
		$trees = $this -> getTree();
		if ($index == 0)
		{
			return 0;
		}
		foreach ($trees as $tree)
		{
			if ($tree['level'] == $index)
			{
				return $tree['category_id'];
			}
		}
	}

	public function getTree()
	{
		$tree = array();
		$ascendant = $this -> getAscendant();
		foreach ($ascendant as $asc)
		{
			$tree[] = $asc -> toArray();
		}
		$tree[] = $this -> toArray();
		$descendant = $this -> getDescendantIds();
		foreach ($descendant as $des)
		{
			$tree[] = $des -> toArray();
		}
		return $tree;
	}

	public function countSub()
	{
		$table = $this -> _table;
		$key = 'p' . $this -> level;
		$select = $table -> select() -> where("$key=?", $this -> getIdentity());
		return 0;
	}

	public function getDescendantIds($parent_id = null, $descendantIds = array())
	{
		$temp = array();
		if (!$parent_id)
		{
			$temp = $this -> getDirectDescendant($this -> category_id);
		}
		else
		{
			$temp = $this -> getDirectDescendant($parent_id);
		}
		if (count($temp) > 0 && is_array($temp))
		{
			$descendantIds = array_merge($descendantIds, $temp);
		}
		foreach ($temp as $te)
		{
			$this -> getDescendantIds($te -> category_id, $descendantIds);
		}
		return $descendantIds;
	}

	public function getDirectDescendant($catId)
	{
		$table = $this -> _table;
		$select = $table -> select();
		$cat_id = array();
		$select -> where('parent_category_id = ?', $catId);
		$results = $table -> fetchAll($select);
		foreach ($results as $result)
		{
			$cat_id[] = $result;
		}
		return $cat_id;
	}

	public function getDescendant()
	{
		$key = $this -> getIndexKey($this -> getLevel());
		$value = $this -> getIndexValue($this -> getLevel());
		$table = $this -> _table;
		$db = $this -> _table -> getAdapter();
		$name = $table -> info('name');
		$select = $table -> select() -> where("$key= ?", $value);
		return $table -> fetchAll($select);
	}

	public function getUsedCount()
	{
		$table = Engine_Api::_() -> getDbTable('contests', 'yncontest');
		$rName = $table -> info('name');
		$ids = $this -> getDescendantIds();
		$ids[] = $this -> category_id;
		$select = $table -> select() -> from($rName) -> where($rName . '.category_id in (?)', $ids);
		$row = $table -> fetchAll($select);
		return $row;
	}

	public function getNextValueMultiSelect($i)
	{
		$trees = $this -> getTree();
		//return $this->category_id;
		foreach ($trees as $tree)
		{
			if ($tree['level'] == $i + 1)
			{
				return $tree['category_id'];
			}
		}
	}

}
