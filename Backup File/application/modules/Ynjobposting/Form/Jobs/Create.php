<?php
class Ynjobposting_Form_Jobs_Create extends Engine_Form {
    protected $_jobId = 0;
    protected $_oldParams = array();
    
    public function getJobId() {
        return $this -> _jobId;
    }
    
    public function setJobId($jobId) {
        $this -> _jobId = $jobId;
    }
    
    public function getOldParams() {
        return $this -> _oldParams;
    }
    
    public function setOldParams($oldParams) {
        $this -> _oldParams = $oldParams;
    } 
    
    public function init() {
            
        $view = Zend_Registry::get('Zend_View');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $id = Engine_Api::_() -> user() -> getViewer() -> level_id;
        $oldParams = $this->getOldParams();
        $job = null;
        if ($this->getJobId()) {
            $job = Engine_Api::_()->getItem('ynjobposting_job', $this->getJobId());
        }

        $this -> setTitle('Create New Job');
        $this -> setAttrib('id', 'create_job_form');
        // select company
        $this -> addElement('Select', 'company_id', array(
            'label' => '*Company Name',
            'required' => true
        ));
        
        // input job title
        $this->addElement('Text', 'title', array(
            'label' => '*Job Title',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        
        $allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
    
        $editorOptions['plugins'] =  array(
                    'table', 'fullscreen', 'media', 'preview', 'paste',
                    'code', 'image', 'textcolor'
        );
        $editorOptions['toolbar1'] = array(
              'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|', 
              'image', '|', 'link', '|', 'fullscreen', '|', 'preview'
        );       
        $editorOptions['html'] = 1;
        $editorOptions['bbcode'] = 1;
        $editorOptions['mode'] = 'exact';
        $editorOptions['elements'] = 'description, skill_experience, content'; 
        if (!empty($oldParams)) {
            $index = 1;
            foreach ($oldParams as $key => $value) {
                if (strpos($key, 'header_') !== false) {
                    $editorOptions['elements'] = $editorOptions['elements'].', content_'.$index;
                    $index++;
                }
            }
        }
        else if ($job) {
            $addInfos = Engine_Api::_()->getDbTable('jobinfos', 'ynjobposting')->getRowInfoByJobId($this->getJobId());
            $index = 0;
            $addInfos = $addInfos->toArray();
            foreach ($addInfos as $addInfo) {
                if ($index > 0) {
                    $editorOptions['elements'] = $editorOptions['elements'].', content_'.$index;
                }
                $index++;
            }
        }
        
        // description
        $this -> addElement('TinyMce', 'description', array(
            'label' => '*Job Description', 
            'editorOptions' => $editorOptions, 
            'required' => true, 
            'allowEmpty' => false, 
            'filters' => array(
                new Engine_Filter_Censor(), 
                new Engine_Filter_Html( array('AllowedTags' => $allowed_html)))
            )
        );
        
        // Desired Skills & Experience
        $this -> addElement('TinyMce', 'skill_experience', array(
            'label' => '*Desired Skills & Experience', 
            'editorOptions' => $editorOptions, 
            'required' => true, 
            'allowEmpty' => false, 
            'filters' => array(
                new Engine_Filter_Censor(), 
                new Engine_Filter_Html( array('AllowedTags' => $allowed_html)))
            )
        );
        
        // select industry
        $this -> addElement('Select', 'industry_id', array(
            'label' => '*Industry',
            'required' => true
        ));
        
        // select level
        $this -> addElement('Select', 'level', array(
            'label' => 'Job Level',
        ));
        
		$tableLevel = Engine_Api::_() -> getDbTable('joblevels', 'ynjobposting');
		$this -> level -> addMultiOptions($tableLevel -> getJobLevelArray());
		
        // select type
        $this -> addElement('Select', 'type', array(
            'label' => 'Job Type',
        ));
        
		$tableType = Engine_Api::_() -> getDbTable('jobtypes', 'ynjobposting');
		$this -> type -> addMultiOptions($tableType -> getJobTypeArray());
		
        // language prefer
        $this->addElement('Text', 'language_prefer', array(
            'label' => 'Language Prefer',
            'allowEmpty' => true,
            'required' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        
        // education prefer
        $this -> addElement('Select', 'education_prefer', array(
            'label' => 'Education Prefer',
            'multiOptions' => array(
                'highschool' => 'Highschool Diploma',
                'associated' => 'Associated Degree',
                'bachelor' => 'Bachelor Degree',
                'master' => 'Master Degree',
                'doctorate' => 'Doctorate Degree'
            )
        ));
        
        // salary
        $this->addElement('Float', 'salary_from', array(
            'label' => 'Salary',
            'description' => 'from',
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        $this->addElement('Float', 'salary_to', array(
            'description' => 'to',
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        $this->addElement('Select', 'salary_currency', array(
            'value' => 'USD',
        ));
        $this->getElement('salary_currency')->getDecorator('Description')->setOption('placement', 'APPEND');
        $this->addElement('Checkbox', 'negotiable', array(
            'label' => 'Negotiable',
            'value' => 0
        ));
        
        // working place
        $location_default = isset($oldParams['location']) ? $oldParams['location'] : (($job) ? $job->working_place : '');
        
        $this -> addElement('Dummy', 'location_map', array(
            'label' => 'Full Address', 
            'decorators' => array( 
                array('ViewScript', 
                    array(
                        'viewScript' => '_location_search.tpl', 
                        'label' => 'Working Place',
                        'class' => 'form element', 
                        'alert' => 'false',
                        'location' => $location_default,
                    )
                )
            ), 
        ));
        $this -> addElement('hidden', 'location_address', array('value' => '0', 'order' => '97'));
        $this -> addElement('hidden', 'lat', array('value' => '0', 'order' => '98'));
        $this -> addElement('hidden', 'long', array('value' => '0', 'order' => '99'));

        // working time
        $this->addElement('Text', 'working_time', array(
            'label' => 'Working Time',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        // additional information
        $this->addElement('Text', 'header', array(
            'label' => 'Additional Information',
            'description' => 'Header'
        ));
        if (isset($oldParams['header'])) $this->header->setValue($oldParams['header']);
        $this -> addElement('TinyMce', 'content', array(
            'description' => 'Content', 
            'editorOptions' => $editorOptions, 
            'filters' => array(
                new Engine_Filter_Censor(), 
                new Engine_Filter_Html( array('AllowedTags' => $allowed_html)))
            )
        );
        if (isset($oldParams['content'])) $this->header->setValue($oldParams['content']);
        
        $this->addDisplayGroup(array('header', 'content'), 'add_info', array(
            'class' => 'add-info-job'
        ));
        
        if (!empty($oldParams)) {
            $index = 1;
            foreach ($oldParams as $key => $value) {
                if (strpos($key, 'header_') !== false) {
                    $index2 = explode('_', $key);
                    $header = 'header_'.$index;
                    $this->addElement('Text', $header, array(
                        'label' => 'Additional Information',
                        'description' => 'Header',
                        'value' => $oldParams[$key],
                        'class' => 'header_remove',
                    ));
                    $content = 'content_'.$index;
                    $this -> addElement('TinyMce', $content, array(
                        'description' => 'Content', 
                        'editorOptions' => $editorOptions, 
                        'value' => $oldParams['content_'.$index2[1]],
                        'filters' => array(
                            new Engine_Filter_Censor(), 
                            new Engine_Filter_Html( array('AllowedTags' => $allowed_html)))
                        )
                    );
                    
                    $group = array($header, $content);
                    
                    $add_info = 'add_info_'.$index;
                    $this->addDisplayGroup($group, $add_info, array(
                        'class' => 'add-info-job'
                    ));
                    
                    $index ++;
                }  
            }
        }
        else if ($job) {
            $addInfos = Engine_Api::_()->getDbTable('jobinfos', 'ynjobposting')->getRowInfoByJobId($this->getJobId());
            $index = 0;
            $addInfos = $addInfos->toArray();
            
            foreach ($addInfos as $addInfo) {
                if ($index == 0) {
                    $this->header->setValue($addInfo['header']);
                    $this->content->setValue($addInfo['content']);
                    $index ++;
                    continue;
                }
                $header = 'header_'.$index;
                $this->addElement('Text', $header, array(
                    'label' => 'Additional Information',
                    'description' => 'Header',
                    'value' => $addInfo['header'],
                    'class' => 'header_remove',
                ));
                $content = 'content_'.$index;
                $this -> addElement('TinyMce', $content, array(
                    'description' => 'Content', 
                    'editorOptions' => $editorOptions, 
                    'value' => $addInfo['content'],
                    'filters' => array(
                        new Engine_Filter_Censor(), 
                        new Engine_Filter_Html( array('AllowedTags' => $allowed_html)))
                    )
                );
                
                $group = array($header, $content);
                
                $add_info = 'add_info_'.$index;
                $this->addDisplayGroup($group, $add_info, array(
                    'class' => 'add-info-job'
                ));
                
                $index ++;
            }
        }
        
        // select packages
        if (!$job) {
            $this -> addElement('Radio', 'package_id', array(
                'label' => 'Selecting packages',
            ));
        }
        else {
            if ($job->status == 'draft') {
                $this -> addElement('Radio', 'package_id', array(
                    'label' => 'Selecting packages',
                ));
            }
            else if ($job->status == 'pending') {
                $this -> addElement('Radio', 'package_id', array(
                    'label' => 'Expiration',
                    'description' => $view->translate('Your job will be displayed on listing for %s days from the day this job gets the approval.', $job->number_day),
                ));
            }
            else {
                $timezone = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core_locale_timezone', 'GMT');
                if ($viewer && $viewer -> getIdentity() && !empty($viewer -> timezone)) {
                    $timezone = $viewer -> timezone;
                }
                $expiration_date = new Zend_Date($job->expiration_date);
                $now = new Zend_Date();
                $over = ($now > $expiration_date) ? true : false;
                $expiration_date->setTimezone($timezone);
                if (!$over) {
                    $this -> addElement('Radio', 'package_id', array(
                        'label' => 'Expiration',
                        'description' => $view->translate('Your job will be expired on %s.', $view->locale()->toDate($expiration_date)),
                    ));
                }
                else {
                    $this -> addElement('Radio', 'package_id', array(
                        'label' => 'Expiration',
                        'description' => $view->translate('Your job has been expired from %s.', $view->locale()->toDate($expiration_date)),
                    ));
                } 
            } 
        }
        
        // feature job
        $amount = $settings->getSetting('ynjobposting_fee_featurejob', 10);
        $currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
        
        if (!$this->getJobId()) {
            $this -> addElement('Checkbox', 'feature', array(
                'label' => $view->translate('Feature this Job with %s per day.', $view->locale()->toCurrency($amount, $currency)),
                'description' => 'Feature Job',
                'value' => 1,
            ));
            $this->addElement('Integer', 'feature_period', array(
                'description' => 'Feature period (days)'
            ));
        }
        else {
            $job = Engine_Api::_()->getItem('ynjobposting_job',$this->getJobId());
            if ($job->status == 'draft') {
                $this -> addElement('Checkbox', 'feature', array(
                    'label' => $view->translate('Feature this Job with %s per day.', $view->locale()->toCurrency($amount, $currency)),
                    'description' => 'Feature Job',
                    'value' => 1,
                ));
                $this->addElement('Integer', 'feature_period', array(
                    'description' => 'Feature period (days)'
                ));
            }
            else {
                $feature = $job->getFeature();
                if ($feature) {
                        if ($job->status == 'pending') {
                            $this->addElement('Integer', 'feature_period', array(
                                'description' => $view->translate('This job will be featured for %1s day(s), from the day this job gets the approval.', $feature->period).$view->translate(' Add more featured days with %s per day.', $view->locale()->toCurrency($amount, $currency)),
                            ));
                        }
                        else {
                            if (($feature->active == 1) && (is_null($feature->expiration_date))){
                                $this -> addElement('Heading', 'feature', array(
                                    'label' => $view->translate('Feature this Job.'),
                                    'description' => $view->translate('This Job has been featured.'),
                                ));
                            }
                            else {
                                $this -> addElement('Checkbox', 'feature', array(
                                    'label' => $view->translate('Feature this Job.'),
                                    'description' => $view->translate('Feature Job'),
                                    'value' => 1,
                                ));
                                
                                $timezone = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core_locale_timezone', 'GMT');
                                if ($viewer && $viewer -> getIdentity() && !empty($viewer -> timezone)) {
                                    $timezone = $viewer -> timezone;
                                }
                                $approved_date = new Zend_Date($job->approved_date);
                                $expiration_date = new Zend_Date($feature->expiration_date);
                                $approved_date->setTimezone($timezone);
                                $expiration_date->setTimezone($timezone);
                                $this->addElement('Integer', 'feature_period', array(
                                    'description' => $view->translate('This job has already been featured for %1s day(s), from %2s to %3s.', $feature->period, $view->locale()->toDate($approved_date), $view->locale()->toDate($expiration_date)).$view->translate(' Add more featured days with %s per day.', $view->locale()->toCurrency($amount, $currency)),
                                ));
                                if (!$feature->active) {
                                    $this->feature->setValue(0);
                                    $this->feature_period->setAttrib('disabled', true);
                                    $this->feature_period->setAttrib('class', 'disabled');
                                }
                            }
                        }
                }
                else {
                    $this -> addElement('Checkbox', 'feature', array(
                        'label' => $view->translate('Feature this Job with %s per day.', $view->locale()->toCurrency($amount, $currency)),
                        'description' => 'Feature Job',
                        'value' => 1,
                    ));
                    $this->addElement('Integer', 'feature_period', array(
                        'description' => 'Feature period (days)'
                    ));
                    $this->feature->setValue(0);
                    $this->feature_period->setAttrib('disabled', true);
                    $this->feature_period->setAttrib('class', 'disabled');
                }
            }
        }
        // privacy
        $availableLabels = array(
            'everyone' => 'Everyone', 
            'registered' => 'All Registered', 
            'network' => 'My Network', 
            'owner_member' => 'My Friends', 
            'owner' => 'Only Me'
        );
        
        // view
        $viewOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynjobposting_job', $id, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        
        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            // Make a hidden field
            if (count($viewOptions) == 1) {
                $this -> addElement('hidden', 'view', array('value' => key($viewOptions)));
                // Make select box
            } else {
                $this -> addElement('Select', 'view', array(
                    'label' => 'View Privacy',
                    'description' => 'Control who can see your job', 
                    'multiOptions' => $viewOptions, 
                    'value' => key($viewOptions), 
                ));
            }
        }
        
        // comment
        $commentOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynjobposting_job', $id, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
        
        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
            if (count($commentOptions) == 1) {
                $this -> addElement('hidden', 'comment', array('value' => key($commentOptions)));
                // Make select box
            } else {
                $this -> addElement('Select', 'comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Control who can comment your job', 
                    'multiOptions' => $commentOptions, 
                    'value' => key($commentOptions), 
                ));
            }
        }
        
        $this->addElement('Text', 'tags',array(
          'label'=>'Tags (Keywords)',
          'autocomplete' => 'off',
          'description' => 'Separate tags with commas.',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
        
        $this -> addElement('hidden', 'published', array('value' => 1, 'order' => '101'));
        
        if ($job) {
             if ($job->isPublished() || $job->isEnded()) { 
                 $this -> addElement('Radio', 'end', array(
                    'label' => 'Ended this Job?', 
                    'multiOptions' => array(
                        1 => 'Yes',
                        0 => 'No',
                    ),
                    'value' => ($job->status == 'ended') ? 1 : 0,
                 ));
             }
        }
         // Buttons
        $this->addElement('Button', 'save_draft', array(
          'value' => 'save_draft',
          'label' => 'Save as Draft',
          'type' => 'button',
          'ignore' => true,
          'onclick' => 'submitWithDraft()',
          'decorators' => array(
            'ViewHelper',
          ),
        ));
        
        $this->addElement('Button', 'publish', array(
          'value' => 'submit_button',
          'label' => 'Publish Job',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array(
            'ViewHelper',
          ),
    ));
    
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('save_draft', 'publish', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
    }

}
