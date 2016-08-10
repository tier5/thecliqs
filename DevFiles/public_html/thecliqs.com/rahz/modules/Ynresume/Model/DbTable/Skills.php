<?php
class Ynresume_Model_DbTable_Skills extends Engine_Db_Table
{
	protected $_rowClass = 'Ynresume_Model_Skill';

	/**
	 * Get the skill table
	 *
	 * @return Engine_Db_Table
	 */
	public function getSkillTable()
	{
		return $this;
	}

	/**
	 * Get the skill map table
	 *
	 * @return Engine_Db_Table
	 */
	public function getMapTable()
	{
		return Engine_Api::_()->getDbtable('SkillMaps', 'ynresume');
	}

	// Skills

	/**
	 * Get an existing or create a new text skill
	 *
	 * @param string $text The skill text
	 * @return Ynresume_Model_Skill
	 */
	public function getSkill($text)
	{
		$text = $this->formatSkillText($text);

		$table = $this->getSkillTable();
		$select = $table->select()
		->where("text = ?", $text);

		$row = $table->fetchRow($select);

		if( null === $row )
		{
			$row = $table->createRow();
			$row->text = $text;
			$row->save();
		}

		return $row;
	}

	public function getSkillsByUser($resume, Core_Model_Item_Abstract $user)
	{
		$mapTable = $this->getMapTable();
		$select = $mapTable->select()
		->where('user_id = ?', $user->getIdentity())
		->where('resume_id = ?', $resume->getIdentity())
		->order("order ASC");
		$identities = array();
		foreach( $mapTable->fetchAll($select) as $skillmap )
		{
			$identities[] = $skillmap->skill_id;
		}
		$skills = array();
		foreach ($identities as $id){
			$skills[] = Engine_Api::_()->getItem('ynresume_skill', $id);
		}
		return $skills;
	}
	
	public function getSkillsByText($text = null, $limit = 10)
	{
		$table = $this->getSkillTable();
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
	 * Called on text skills for formatting
	 *
	 * @param string $text
	 * @return string
	 */
	public function formatSkillText($text)
	{
		// We can do formatting on skills later
        $text = strip_tags($text);
		return trim($text);
	}


	/**
	 * Skill a resource
	 *
	 * @param Core_Model_Item_Abstract $resume The resource being tagged
	 * @param Core_Model_Item_Abstract $user The resource doing the tagging
	 * @param string|Core_Model_Item_Abstract $skill What is tagged in resource
	 * @param array|null $extra
	 * @return Engine_Db_Table_Row|null
	 */
	public function addSkillMap(Core_Model_Item_Abstract $resume, Core_Model_Item_Abstract $user, $skill, $order = null, $extra = null)
	{
		$skill = $this->_getSkill($skill, true);
		
		if( !$skill ) {
			return false;
		}

		if( null !== ($skillmap = $this->getSkillMap($resume, $user, $skill)) ) {
			if (isset($order) && !is_null($order))
			{
				$skillmap -> order = $order;
				$skillmap -> save();	
			}
			return $skillmap; 
		}
		
		// Do the tagging
		$table = $this->getMapTable();
		$skillmap = $table->createRow();
		$skillmap->setFromArray(array(
		      'resume_id' => $resume->getIdentity(),
		      'user_id' => $user->getIdentity(),
		      'skill_id' => $skill->getIdentity(),
		      'creation_date' => new Zend_Db_Expr('NOW()'),
		));
		if (isset($order))
		{
			$skillmap -> order = $order;
		}
		$skillmap->save();
		$resume->endorse_count++;
		$resume->save();
		return $skillmap;
	}

	/**
	 * Add multiple tags
	 *
	 * @param Core_Model_Item_Abstract $resume
	 * @param Core_Model_Item_Abstract $user
	 * @param array $skills
	 * @return array
	 */
	public function addSkillMaps(Core_Model_Item_Abstract $resume, Core_Model_Item_Abstract $user, array $skills)
	{
		$skillmaps = array();
		foreach( $skills as $key => $value )
		{
			// ignore empty tags
			if (empty($value)) continue;
			$skillmaps[] = $this->addSkillMap($resume, $user, $value, $key);
		}
		return $skillmaps;
	}

	/**
	 * Get a tag map on resource and existing tag (for checking if already tagged)
	 *
	 * @param Core_Model_Item_Abstract $resume
	 * @param string|Core_Model_Item_Abstract $skill
	 * @return Engine_Db_Table|null
	 */
	public function getSkillMap(Core_Model_Item_Abstract $resume, Core_Model_Item_Abstract $user, $skill)
	{
		$skill = $this->_getSkill($skill);

		$table = $this->getMapTable();
		$select = $table->select()
		->where('resume_id = ?', $resume->getIdentity())
		->where('user_id = ?', $user->getIdentity())
		->where('skill_id = ?', $skill->getIdentity())
		->limit(1)
		;

		$skillmap = $table->fetchRow($select);
		return $skillmap;
	}

	/**
	 * Get a tagmap by id that is part of resource
	 *
	 * @param Core_Model_Item_Abstract $resume
	 * @param integer $id
	 */
	public function getSkillMapById(Core_Model_Item_Abstract $resume, $id)
	{
		$table = $this->getMapTable();
		$select = $table->select()
		->where('resume_id = ?', $resume->getIdentity())
		->where('skillmap_id = ?', (int) $id)
		->limit(1)
		;

		$skillmap = $table->fetchRow($select);
		return $skillmap;
	}

	/**
	 * Get all tags for a resource
	 *
	 * @param Core_Model_Item_Abstract $resume
	 * @return Engine_Db_Table_Rowset
	 */
	public function getSkillMaps(Core_Model_Item_Abstract $resume)
	{
		return $this->getMapTable()->fetchAll($this->getSkillMapSelect($resume));
	}

	/**
	 * Get a select object for tags on a resource
	 *
	 * @param Core_Model_Item_Abstract $resume
	 * @return Zend_Db_Table_Select
	 */
	public function getSkillMapSelect(Core_Model_Item_Abstract $resume, Core_Model_Item_Abstract $user = null)
	{
		$table = $this->getMapTable();
		$select = $table->select()
		->where('resume_id = ?', $resume->getIdentity())
		->order('skill_id ASC')
		;
		if (!is_null($user))
		{
			$select -> where("user_id = ?", $user->getIdentity() );
		}
		return $select;
	}

	public function setSkillMaps(Core_Model_Item_Abstract $resume, $user, array $skills)
	{
		$existingSkillMaps = $this->getSkillMaps($resume);
		$added = array();
		$setSkillIndex = array();
		$skillObjects = array();
		foreach( $skills as $skill )
		{
			if(!empty($skill)){
				$skillObject = $this->_getSkill($skill);
				$skillObjects[] = $skillObject;
				$setSkillIndex[$skillObject->getGuid()] = $skillObject;
			}
		}

		// Check for new tags
		foreach( $skillObjects as $skill )
		{
			if( !$existingSkillMaps->getRowMatching(array(
	          'skill_id' => $skill->getIdentity(),
			)) ) {
				$added[] = $this->addSkillMap($resume, $user, $skill);
			}
		}

		// Check for removed tags
		foreach( $existingSkillMaps as $skillmap )
		{
			$key = 'ynresume_skill' . '_' . $skillmap->skill_id;
			if( empty($setSkillIndex[$key]) )
			{
				$skillmap->delete();
			}
		}

		return $added;
	}

	public function removeSkillMap(Core_Model_Item_Abstract $resume, Core_Model_Item_Abstract $user, $skill)
	{
		$text = $this->formatSkillText($skill);
		$table = $this->getSkillTable();
		$select = $table->select()->where("text = ?", $text);
		$skillObj = $table->fetchRow($select);
		if (!is_null($skillObj))
		{
			$skillMap = $this -> getSkillMap($resume, $user, $skillObj);
			if (is_object($skillMap))
			{
				$skillMap -> delete();
			}
		}
	}


	// Resources

	public function getResourcesBySkillSelect($skill, array $params = array())
	{
		if( is_string($skill) ) {
			$skill = $this->_getSkill($skill);
		}

		$table = $this->getMapTable();
		$select = $table->select()
		->where('skill_id = ?', $skill->getIdentity())
		;
		return $select;
	}






	// Utility

	/**
	 * Gets an existing string tag or returns the passed item
	 *
	 * @param string|Core_Model_Item_Abstract $skill
	 * @return Core_Model_Item_Abstract
	 * @throws Core_Model_Exception If argument is not a string or an item
	 */
	protected function _getSkill($skill, $return = false)
	{
		if( is_string($skill) )
		{
			$skill = $this->getSkill($skill);
		}

		if( !($skill instanceof Core_Model_Item_Abstract) || !$skill->getIdentity() )
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

		return $skill;
	}
	
	public function removeSkillMapsBySkillIds($resume, $ids)
	{
		if (!is_array($ids)){
			return false;
		}
		$mapTable = $this->getMapTable();
		$select = $mapTable->select()
		->where('skill_id IN (?)', $ids)
		->where('resume_id = ?', $resume->getIdentity())
		;
		$count = 0;
		foreach( $mapTable->fetchAll($select) as $skillmap )
		{
			$skillmap -> delete();
			$count ++;
		}
		if ($resume -> endorse_count > 0)
		{
			$resume -> endorse_count = $resume -> endorse_count - $count;
			$resume -> save();
		}
	}
}