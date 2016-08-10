<?php
class Ynjobposting_Form_Jobs_Application extends Engine_Form {
    protected $_jobId = 0;
    protected $_apply = null;
    
    public function getJobId() {
        return $this -> _jobId;
    }
    
    public function setJobId($jobId) {
        $this -> _jobId = $jobId;
    } 
    
    public function getApply() {
        return $this -> _apply;
    }
    
    public function setApply($apply) {
        $this -> _apply = $apply;
    } 
    
    public function init() {
            
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this -> setAttrib('id', 'apply_job_form');
        $this -> setAttrib('method', 'post');
        $this -> setAttrib('enctype', 'multipart/form-data');
        $this->loadDefaultDecorators();
    
        $job = Engine_Api::_()->getItem('ynjobposting_job', $this->getJobId());
        $submissionForm = $job->getSubmissionForm();
        
        if (!$submissionForm) return;
        /*
        $this -> setTitle($submissionForm->form_title);
        $this -> setDescription($submissionForm->form_description);
        */
        /*
        if ($submissionForm->show_job_title) {
            $this->addElement('Heading', 'job_title', array(
                'label' => $job->getTitle(),
            ));
        }
        */
        
        $company = $job->getCompany();
        /*
        if ($submissionForm->show_job_location && $job->working_place) {
            $this->addElement('Heading', 'job_location', array(
                'description' => $view->translate('at %s', $job->working_place),
            ));
        }

        if ($submissionForm->show_company_name && $company) {
            $this->addElement('Heading', 'company_name', array(
                'description' => $company->getTitle(),
            ));
        }  
        */
        /*
        if ($submissionForm->show_company_logo) {
            $this->addElement('Image', 'company_logo', array(
                'src' => $company->getPhotoUrl('thumb.icon'),
            ));
        }
        */
        
        
        $apply = $this->getApply();
        $applyFields = $apply -> getFieldValue();
        
        /*
        foreach ($applyFields as $field)
        {
        	if ($field->type == 'file')
        	{
				if ($field->value)
				{
					$file = Engine_Api::_()->getItem('storage_file', $field->value);
					if (!is_null($file)){
						$this->addElement('Dummy', 'filed'. $field->field_id, array(
							'label' => $field -> label, 
							'content' => "<img style='max-width: 48px' src='{$file->map()}' />"
						));
					}
				}        		
        	}
        	else
        	{
	        	$this->addElement('Dummy', 'filed'. $field->field_id, array(
					'label' => $field -> label, 
					'content' => $field -> value
				));	
        	}
			
        }
		*/
        /*
       $notes = $apply -> getNote();
       if (count($notes))
       {
       		$this->addElement('Dummy', 'note_list', array(
				  'label' => 'Note',	
				  'decorators' => array(
						  array('ViewScript', array(
								'viewScript' => '_note_list.tpl',
								'notes' => $notes,
						  ))
				  ),
			));	
       } 
        */
       $this->addElement('Textarea', 'content', array(
			  'allowEmpty' => false,
		      'required' => true,
		      'validators' => array(
		        array('NotEmpty', true),
		        array('StringLength', false, array(1, 255)),
		      ),
		      'filters' => array(
		        'StripTags',
		        new Engine_Filter_Censor(),
		      ),
		      'description' => Zend_Registry::get("Zend_Translate")->_("Max 255 characters"),
		      'maxlength' => 255,
		));	
       $this->content->getDecorator('Description')->setOption('placement', 'append');
		
        // Submit
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Add Note',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));
        
        $this->addElement('Cancel', 'cancel', array(
		      'label' => 'Cancel',
		      'link' => true,
		      'prependText' => ' or ',
		      'href' => '',
		      'onclick' => 'parent.Smoothbox.close();',
		      'decorators' => array(
		        'ViewHelper'
	      )
    	));
	    $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons');
	    $button_group = $this->getDisplayGroup('buttons');
    }
}
