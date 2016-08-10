<?php
class Ynjobposting_AdminJobsController extends Core_Controller_Action_Admin {
    
    public function indexAction() {
        
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynjobposting_admin_main', array(), 'ynjobposting_admin_manage_jobs');
        
        $this->view->form = $form = new Ynjobposting_Form_Admin_Jobs_Search();
       
        $industries = Engine_Api::_() -> getItemTable('ynjobposting_industry')->getIndustries();
        unset($industries[0]);
        foreach ($industries as $industry) {
            $form->industry_id->addMultiOption($industry['industry_id'], str_repeat("-- ", $industry['level'] - 1).$industry['title']);
        }
        
        $form->populate($this->_getAllParams());
        $values = $form->getValues();
        $values['admin'] = 1;
        $this->view->formValues = $values;
 
        $page = $this->_getParam('page', 1);
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('ynjobposting_job')->getJobsPaginator($values);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function multiselectedAction() {
        $action = $this -> _getParam('select_action', 'delete');
        $this->view->action = $action;
        $this -> view -> ids = $ids = $this -> _getParam('ids', null);
        $confirm = $this -> _getParam('confirm', false);

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == true) {
            $ids_array = explode(",", $ids);
            switch ($action) {
                case 'delete':
                    foreach ($ids_array as $id) {
                        $job = Engine_Api::_()->getItem('ynjobposting_job', $id);
                        if ($job && $job->isDeletable()) {
                            $job->delete();
                            
                            //send notification
                            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');    
                            $notifyApi -> addNotification($job->getOwner(), $job, $job, 'ynjobposting_job_deleted');
                        }
                    }
                    break;
                
                case 'approve':
                    foreach ($ids_array as $id) {
                        $job = Engine_Api::_()->getItem('ynjobposting_job', $id);
                        if ($job && ($job->status == 'pending')) {
                            $now =  date("Y-m-d H:i:s");
                            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');    
                            //feature job
                            $jobFeature = $job->getFeature();
                            if ($jobFeature && ($jobFeature->active == 0)) {
                                if($jobFeature->period == 1) $type = 'day';
                                else $type = 'days';
                                $expiration_date = date_add(date_create($now),date_interval_create_from_date_string($jobFeature->period.' '.$type));
                                $jobFeature -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
                                $jobFeature -> active = 1;
                                $jobFeature->save();
                                $job -> featured = 1;
                                $job -> save();
                                $notifyApi -> addNotification($job->getOwner(), $job, $job, 'ynjobposting_job_featured');
                            }
                            
                            //publish job
                            $job -> approved_date = $now;
                            if($job->number_day == 1) $type = 'day';
                            else $type = 'days';
                            $expiration_date = date_add(date_create($now),date_interval_create_from_date_string($job->number_day.' '.$type));
                            $job -> expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
                            $job->save();
                            $job->changeStatus('published');
                            Engine_Api::_() -> ynjobposting() -> notifyFollower($job -> getIdentity());
                        }
                    }
                    break;
               
                case 'deny':
                    foreach ($ids_array as $id) {
                        $job = Engine_Api::_()->getItem('ynjobposting_job', $id);
                        if ($job && ($job->status == 'pending')) {
                            $job->changeStatus('denied');
                            //send notification
                            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');    
                            $notifyApi -> addNotification($job->getOwner(), $job, $job, 'ynjobposting_job_deny');
                        }
                    }
                    break; 
                    
                case 'end':
                    foreach ($ids_array as $id) {
                        $job = Engine_Api::_()->getItem('ynjobposting_job', $id);
                        if ($job && $job->isPublished() && $job->isEndable()) {
                            $job->changeStatus('ended');
                            //send notification
                            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');    
                            $notifyApi -> addNotification($job->getOwner(), $job, $job, 'ynjobposting_job_ended');
                        }
                    }
                    break;                   
            }

            $this -> _helper -> redirector -> gotoRoute(array('action' => ''));
        }
    }

    public function featureAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if (!$id) return;
        $job = Engine_Api::_()->getItem('ynjobposting_job', $id);
        if (!$job) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        
        $job->feature($value, true);
    }
    
    public function deleteAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->job_id = $id;
        $job = Engine_Api::_()->getItem('ynjobposting_job', $id);
        if (!$job->isDeletable()) {
            $this->view->error = true;
            $this->view->message = 'You don\'t have permission to delete this job.';
            return;
        }
        
        if( $this->getRequest()->isPost()) {
            $job->delete();

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' =>true,
                'parentRefresh'=> true,
                'messages' => array('Delete job successful.')
            ));
        }
    }
}