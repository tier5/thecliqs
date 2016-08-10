<?php
/**
 * User: Admin
 * Date: 22.07.11
 * Time: 16:15
 **/
class Page_Model_DbTable_Modules extends Engine_Db_Table
{
  protected $_rowClass = 'Page_Model_Module';

  public function getNewModules()
	{
		$select = $this->select()
			->from(array($this->info('name')), array('module_id','name', 'informed'))
			->where('informed = 0');
		$query = $select->query();
		$modules = $query->fetchAll();
		return $modules;
	}

	public function setInformed($module_id)
	{
		$where = array('module_id = ?' => $module_id);
		$this->update(array('informed' => '1'), $where);
	}

	public function getAvailableModules()
	{
    // Element:modules
    $modules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $features = array();
    $features[0] = 'pagealbum';
    $features[1] = 'pageblog';
    $features[2] = 'pagediscussion';
    $features[3] = 'pagedocument';
    $features[4] = 'pageevent';
    $features[5] = 'pagemusic';
    $features[6] = 'pagevideo';
    $features[7] = 'rate';
    $features[8] = 'pagecontact';
    $features[9] = 'store';
    $features[10] = 'pagefaq';
    $features[11] = 'donation';
    $features[12] = 'offers';

    $names = array();
    $names[0] = 'Album';
    $names[1] = 'Blog';
    $names[2] = 'Discussion';
    $names[3] = 'Documents';
    $names[4] = 'Event';
    $names[5] = 'Music';
    $names[6] = 'Video';
    $names[7] = 'Rate';
    $names[8] = 'Contact';
    $names[9] = 'Store';
    $names[10] = 'FAQ';
    $names[11] = 'Donation';
    $names[12] = 'Offers';

    $available_modules = array();

		for($i=0; $i<count($features); $i++)
    {
      if(in_array($features[$i], $modules))
        $available_modules[$features[$i]] = $names[$i];
    }

		return $available_modules;
	}
}