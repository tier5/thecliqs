<?php
class Ynjobposting_Form_Jobs_Edit extends Ynjobposting_Form_Jobs_Create {  
    public function init() {
        parent::init();
        $this -> setTitle('Edit Job');
        $this -> setAttrib('id', 'edit_job_form');
        $this -> removeElement('company_id');
        $job = Engine_Api::_()->getItem('ynjobposting_job',$this->getJobId());
        if (!$job->isDraft()) {
            $this -> removeElement('published');
            $this -> removeElement('save_draft');
            $this -> publish ->setLabel('Save changes');
        }
        else {
            $this -> save_draft ->setLabel('Save changes');
            $this -> publish ->setLabel('Publish');
        }
    }
}
