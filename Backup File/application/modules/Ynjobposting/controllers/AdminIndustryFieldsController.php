<?php class Ynjobposting_AdminIndustryFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'ynjobposting_company';

  protected $_requireProfileType = true;
  
  public function indexAction()
  {
    // Make navigation
    $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynjobposting_admin_main', array(), 'ynjobposting_admin_main_industries');
	$option_id =  $this->_getParam('option_id');
    $tableIndustry = Engine_Api::_()->getItemTable('ynjobposting_industry');
    parent::indexAction();
  }
  
  public function headingCreateAction()
  {
  	 parent::headingCreateAction();
	 $form = $this->view->form;
	 if($form){
	 	$form -> removeElement('show');
		 $display = $form->getElement('display');
	     $display->setLabel('Show on business page?');
	     $display->setOptions(array('multiOptions' => array(
	          1 => 'Show on business page',
	          0 => 'Hide on business page'
	        )));
	 }
  }
  
  public function headingEditAction()
  {
  	parent::headingEditAction();
	 $form = $this->view->form;
	 if($form){
	 	$form -> removeElement('show');
		 $display = $form->getElement('display');
	     $display->setLabel('Show on business page?');
	     $display->setOptions(array('multiOptions' => array(
	          1 => 'Show on business page',
	          0 => 'Hide on business page'
	        )));
	 }
  }	
  public function fieldCreateAction(){
    parent::fieldCreateAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form -> removeElement('show');
	  $search = $form->getElement('search');
	  $search->setLabel('Show on search listings?');
      $search->setOptions(array('multiOptions' => array(
          1 => 'Show on search listings',
          0 => 'Hide on search listings'
       )));
      $display = $form->getElement('display');
      $display->setLabel('Show on business page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on business page',
          0 => 'Hide on business page'
        )));
    }
  }

  public function fieldEditAction(){
    parent::fieldEditAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form -> removeElement('show');	
	  $search = $form->getElement('search');
	  $search->setLabel('Show on search listings?');
      $search->setOptions(array('multiOptions' => array(
          1 => 'Show on search listings',
          0 => 'Hide on search listings'
       )));
      $display = $form->getElement('display');
      $display->setLabel('Show on business page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on business page',
          0 => 'Hide on business page'
        )));
    }
  }
}
?>
