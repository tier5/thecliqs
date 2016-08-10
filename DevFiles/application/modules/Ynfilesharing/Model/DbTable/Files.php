<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Model_DbTable_Files extends Engine_Db_Table {
	protected $_rowClass = "Ynfilesharing_Model_File";
	
	public function getExistedFile($folderId, $fileName)
	{ 
		$select = $this->select()
					->where('folder_id = ?', $folderId)
	        		->where('name = ?', $fileName);
	    $file = $this->fetchRow($select);	
	    return $file;
	}
	
	public function countAllFilesBy($parentObject)
	{
		$select = $this->select();
		$table_name = $this->info('name');
		$select->from($table_name, 'COUNT(file_id) as fileTotal');
		$select->where('parent_id = ?', $parentObject->getIdentity());
		$select->where('parent_type = ?', $parentObject->getType());
		$result = $this->fetchRow($select);
		return $result['fileTotal'];
	}
	
}