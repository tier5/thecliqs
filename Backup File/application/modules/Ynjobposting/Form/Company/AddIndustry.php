<?php
class Ynjobposting_Form_Company_AddIndustry extends Engine_Form
{
	protected $_formArgs;
	protected $_labelIndustry;
	protected $_labelField;
	protected $_industry;
	protected $_main;
	protected $_company;
	
	public function getCompany()
	{
		return $this -> _company;
	}
	
	public function setCompany($company)
	{
		$this -> _company = $company;
	} 
	
	public function getMain()
	{
		return $this -> _main;
	}
	
	public function setMain($main)
	{
		$this -> _main = $main;
	} 
	
	public function getIndustry()
	{
		return $this -> _industry;
	}
	
	public function setIndustry($industry)
	{
		$this -> _industry = $industry;
	} 
	
    public function getFormArgs()
	{
		return $this -> _formArgs;
	}
	
	public function setFormArgs($formArgs)
	{
		$this -> _formArgs = $formArgs;
	} 
  	
	public function getLabelIndustry()
	{
		return $this -> _labelIndustry;
	}
	
	public function setLabelIndustry($labelIndustry)
	{
		$this -> _labelIndustry = $labelIndustry;
	} 
	
	public function getLabelField()
	{
		return $this -> _labelField;
	}
	
	public function setLabelField($labelField)
	{
		$this -> _labelField = $labelField;
	} 
	
    public function init()
    {
    	if(!$this -> _main)
		{
	    	//industry
			$this -> addElement('Dummy', 'industry', array(
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_add_more_industry.tpl',
					'class' => 'form element',
					'labelIndustry' => $this -> _labelIndustry,
					'industry' => $this -> _industry,
				)
				)), 
			));
		}
		
		if($this -> _main)
		{
			$this -> _labelField = 'field_1';
		}
		//Custom field
		if(empty($this->_company))
		{
		    $customFields = new Ynjobposting_Form_Company_Fields(array_merge(array(
	        'labelField' => $this->_labelField,
	      	),$this -> _formArgs));
		}
		else
		{
			 $customFields = new Ynjobposting_Form_Company_Fields(array_merge(array(
			 'item' => $this->_company,
	         'labelField' => $this->_labelField,
	      	),$this -> _formArgs));
		}
			
	    if( get_class($this) == 'Ynjobposting_Form_Company_AddIndustry' ) {
	    	if(empty($this->_company))
	     		 $customFields->setIsCreation(true);
	    }
	
	    $this->addSubForms(array(
	      'field' => $customFields
	    ));
    }
}
