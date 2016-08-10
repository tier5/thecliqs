<?php
class Ynjobposting_Model_Jobapply extends Core_Model_Item_Abstract
{
	public function getJob() {
        return Engine_Api::_()->getItem('ynjobposting_job', $this->job_id);
    }
    
    public function getFieldValue()
    {
    	$metaTbl = Engine_Api::_()->getDbTable('meta', 'ynjobposting');
    	$metaTblName = $metaTbl -> info('name');
    	
    	$valueTbl = Engine_Api::_()->getDbTable('submissionvalues', 'ynjobposting');
    	$valueTblName = $valueTbl -> info('name');
    	
    	$select = $valueTbl -> select() -> from($valueTblName)
    	-> setIntegrityCheck(false)
    	-> joinLeft($metaTblName, "{$metaTblName}.field_id = {$valueTblName}.field_id AND {$valueTblName}.item_id = {$this->jobapply_id}")
		-> where("{$valueTblName}.item_id = {$this->jobapply_id}")
    	;
    	return $valueTbl -> fetchAll($select);
    }
    
	public function getTextFieldValue()
    {
    	$metaTbl = Engine_Api::_()->getDbTable('meta', 'ynjobposting');
    	$metaTblName = $metaTbl -> info('name');
    	
    	$valueTbl = Engine_Api::_()->getDbTable('submissionvalues', 'ynjobposting');
    	$valueTblName = $valueTbl -> info('name');
    	
    	$select = $valueTbl -> select() -> from($valueTblName)
    	-> setIntegrityCheck(false)
    	-> joinLeft($metaTblName, "{$metaTblName}.field_id = {$valueTblName}.field_id AND {$valueTblName}.item_id = {$this->jobapply_id}")
		-> where("{$valueTblName}.item_id = {$this->jobapply_id}")
		-> where("{$metaTblName}.type = ?", 'text')
    	;
    	return $valueTbl -> fetchAll($select);
    }
    
	public function getPhotoFieldValue()
    {
    	$metaTbl = Engine_Api::_()->getDbTable('meta', 'ynjobposting');
    	$metaTblName = $metaTbl -> info('name');
    	
    	$valueTbl = Engine_Api::_()->getDbTable('submissionvalues', 'ynjobposting');
    	$valueTblName = $valueTbl -> info('name');
    	
    	$select = $valueTbl -> select() -> from($valueTblName)
    	-> setIntegrityCheck(false)
    	-> joinLeft($metaTblName, "{$metaTblName}.field_id = {$valueTblName}.field_id AND {$valueTblName}.item_id = {$this->jobapply_id}")
		-> where("{$valueTblName}.item_id = {$this->jobapply_id}")
		-> where("{$metaTblName}.type = ?", 'file')
    	;
    	return $valueTbl -> fetchRow($select);
    }
    
    public function getNote()
    {
    	$noteTbl = Engine_Api::_()->getDbTable('applynotes', 'ynjobposting');
        $select = $noteTbl -> select() -> where("jobapply_id = ? ", $this->jobapply_id);
        return $noteTbl -> fetchAll($select);
    }
}