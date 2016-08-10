<?php
class Ynjobposting_Model_DbTable_Meta extends Engine_Db_Table
{
	protected $_name = 'ynjobposting_submission_fields_meta';
	protected $_rowClass = 'Ynjobposting_Model_Meta';
	
	public function getFields($company)
	{
		if (is_null($company))
		{
			return array();
		}
		$select = $this -> select() -> where ("company_id = ? ", $company->getIdentity());
		$fields = $this -> fetchAll($select);
		if (count($fields) == 0)
		{
			$this -> addCandidateName($company);
			$this -> addCandidatePhoto($company);
			$this -> addCandidateEmail($company);
			$this -> addCandidatePhone($company);
			$fields = $this -> fetchAll($select);
		} 
		return $fields;
	}
	
	public function addCandidateName($company)
	{
		$row = $this -> createRow();
		$row -> setFromArray(array(
			'company_id' => $company -> getIdentity(),
			'type' =>  'text',
			'label' => Zend_Registry::get("Zend_Translate") -> _("Candidate Name")
		));
		$row -> save();
	}
	
	public function addCandidatePhoto($company)
	{
		$row = $this -> createRow();
		$row -> setFromArray(array(
			'company_id' => $company -> getIdentity(),
			'type' =>  'file',
			'label' => Zend_Registry::get("Zend_Translate") -> _("Candidate Photo")
		));
		$row -> save();
	}
	
	public function addCandidateEmail($company)
	{
		$row = $this -> createRow();
		$row -> setFromArray(array(
			'company_id' => $company -> getIdentity(),
			'type' =>  'text',
			'label' => Zend_Registry::get("Zend_Translate") -> _("Candidate Email")
		));
		$row -> save();
	}
	
	public function addCandidatePhone($company)
	{
		$row = $this -> createRow();
		$row -> setFromArray(array(
			'company_id' => $company -> getIdentity(),
			'type' =>  'text',
			'label' => Zend_Registry::get("Zend_Translate") -> _("Candidate Phone")
		));
		$row -> save();
	}
	
	public function setEnabled($ids, $value)
	{
		if (!is_array($ids)){
			return;
		}
		if (!count($ids))
		{
			return;
		}
		if ($value != '1' && $value != '0')
		{
			return;
		}
		foreach ($ids as $id){
			$this->update(array(
				'enabled' => $value
			), array(
				"field_id = ?" => $id
			));
		}
	}
	
	public function setRequired($ids, $value)
	{
		if (!is_array($ids)){
			return;
		}
		if (!count($ids))
		{
			return;
		}
		if ($value != '1' && $value != '0')
		{
			return;
		}
		foreach ($ids as $id){
			$this->update(array(
				'required' => $value
			), array(
				"field_id = ?" => $id
			));
		}
	}
}