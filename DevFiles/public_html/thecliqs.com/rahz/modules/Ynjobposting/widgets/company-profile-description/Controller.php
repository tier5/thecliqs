<?php
class Ynjobposting_Widget_CompanyProfileDescriptionController extends Engine_Content_Widget_Abstract {

     public function indexAction() 
     {
          // Don't render this if not authorized
          $viewer = Engine_Api::_()->user()->getViewer();
          if (!Engine_Api::_()->core()->hasSubject()) {
          		return $this->setNoRender();
          }
         
          // Get subject and check auth
          $subject = Engine_Api::_()->core()->getSubject('ynjobposting_company');
          if (!is_null($subject))
          {
          		return $this->setNoRender();
          }
          
          // Checking description
          if (!$subject->description) {
          		return $this->setNoRender();
          }
          $this->view->company = $subject;
     }

}