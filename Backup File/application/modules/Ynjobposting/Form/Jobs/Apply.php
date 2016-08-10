<?php
class Ynjobposting_Form_Jobs_Apply extends Engine_Form {
    protected $_jobId = 0;
    
    public function getJobId() {
        return $this -> _jobId;
    }
    
    public function setJobId($jobId) {
        $this -> _jobId = $jobId;
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
        //$this -> setTitle($submissionForm->form_title);
        //$this -> setDescription($submissionForm->form_description);
        $company = $job->getCompany();
        
        /*
    	if ($submissionForm->show_company_logo) {
            $this->addElement('Image', 'company_logo', array(
                'src' => $company->getPhotoUrl('thumb.normal'),
                'onclick' => 'event.preventDefault();',
            ));
        }
        */
        /*
        if ($submissionForm->show_job_title) {
            $this->addElement('Heading', 'job_title', array(
            	'label' => $view->htmlLink(array('route' => 'ynjobposting_job', 'action' => 'view', 'id' => $job->getIdentity() ), $job->getTitle()),
            ));
            $this->job_title->getDecorator('Label')->setOption('escape', false);
        }
        */
        /*
        if ($submissionForm->show_company_name && $company) {
            $this->addElement('Heading', 'company_name', array(
                //'description' => $company->getTitle(),
            	'description' => $view->htmlLink(array('route' => 'ynjobposting_extended', 'controller' => 'company', 'action' => 'detail', 'id' => $company->getIdentity() ), $company->getTitle()),	
            ));
            $this->company_name->getDecorator('Description')->setOption('escape', false);
        } 
        */
        /*
        if ($submissionForm->show_job_location && $job->working_place) {
            $this->addElement('Heading', 'job_location', array(
                'description' => $view->translate('at %s', $job->working_place),
            ));
        }
		*/
        $questionFields = $company->getSubmissionQuestionFields();
        $fieldOptionTbl = new Ynjobposting_Model_DbTable_Options();
        foreach ($questionFields as $questionField) {
            if (!$questionField->enabled) continue;
            $label = str_replace('Candidate', 'Your', $questionField->label);
            switch ($questionField->type) {
                case 'text':
                    $this->addElement('Text', 'field_'.$questionField->field_id, array(
                        'label' => $label,
                        'required' => $questionField->required,
                        'maxlength' => 255,
                        'filters' => array(
                            'StripTags',
                            new Engine_Filter_Censor(),
                        ),
                        'value' => ($questionField->label == 'Candidate Email') ? $viewer->email : (($questionField->label == 'Candidate Name') ? $viewer->getTitle() : ''),
                    ));
                    
                    break;
                case 'file':
                    $this->addElement('File', 'photo', array(
                        'label' => $label,
                        'required' => $questionField->required,
                        'accept' => 'image/*',
                    ));
                    break;
                case 'textarea':
                    $this->addElement('Textarea', 'field_'.$questionField->field_id, array(
                        'label' => $label,
                        'required' => $questionField->required,
                        'filters' => array(
                            'StripTags',
                        ),
                    ));
                    break;
                case 'checkbox':
                    $options = $fieldOptionTbl->getOptions($questionField->field_id);
                    $this->addElement('MultiCheckbox', 'field_'.$questionField->field_id, array(
                        'label' => $label,
                        'required' => $questionField->required,
                        'multiOptions' => $options,
                    ));
                    break;
                case 'radio':
                    $options = $fieldOptionTbl->getOptions($questionField->field_id);
                    $this->addElement('Radio', 'field_'.$questionField->field_id, array(
                        'label' => $label,
                        'required' => $questionField->required,
                        'multiOptions' => $options,
                    ));
                    break;
            }
        }

        $this->addElement('File', 'upload_files', array(
            'label' => 'Resume',
            'multiple' => true,
            'description' => 'Upload files (Format: Ms Word, PDF, ZIP, JPEG, PNG) - '.$settings->getSetting('ynjobposting_max_uploadsize', 500). ' KB',
            'accept' => '.doc,.docx,.pdf,.zip,.jpge,.png,.DOC,.DOCX,.PDF,.ZIP,.JPNG,.PNG',
        ));
        $this->upload_files->addValidator('Extension', false, 'doc,docx,pdf,zip,jpge,png');
        $this->upload_files->addValidator('Size', false, array('min' => 0, 'max' => $settings->getSetting('ynjobposting_max_uploadsize', 500)*1024));
        $this ->upload_files->setAttrib('name', 'upload_files[]');
		
		if (Engine_Api::_()->hasModuleBootstrap('ynresume') && Engine_Api::_() -> ynresume() -> getResumeByUserId()) {
			$this->addElement('checkbox', 'resume', array(
	            'label' => 'Use My Resume',
	        ));
        }

        $videoOptions = array (
            1 => $view->translate('Video resume (Youtube, Google Video, ...)'),
        );
        
        $hasVideoModule = Engine_Api::_() -> hasItemType('video');
        if ($submissionForm->allow_video) {
            if ($hasVideoModule) {
                $videoOptions[2] = $view->translate('Existing Video resume');
            }
        }
        $this->addElement('Radio', 'resume_video', array(
            'multiOptions' => $videoOptions,
            'value' => 1,
        ));
        
        $this->addElement('Text', 'video_link', array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        
        if ($submissionForm->allow_video) {
            if ($hasVideoModule) {
                $videos = Engine_Api::_()->ynjobposting()->getMyVideos();
                $this->addElement('Select', 'video_id', array(
                   'multiOptions' =>  $videos,
                ));
                if (sizeof($videos)) {
                }
                else {
                    $this->addElement('Heading', 'no_video', array(
                    ));
                    $description = $this->getTranslator()->translate('YNJOBPOSTING_NO_VIDEO_DESCRIPTION');
                    $description = vsprintf($description, array(
                        Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'create'), 'video_general', true)
                    ));
                    $this->no_video->setDescription($description);
                    $this->no_video->getDecorator('Description')->setOption('escape', false);
                }
            }
        }

        // Submit
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Apply',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));

        $this->addElement('Cancel', 'cancel', array(
            'prependText' => ' or ',
            'label' => 'cancel',
            'link' => true,
            'decorators' => array(
                'ViewHelper'
            ),
        ));

        $this->addDisplayGroup(array(
                'submit_btn',
                'cancel'
            ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
    }
}