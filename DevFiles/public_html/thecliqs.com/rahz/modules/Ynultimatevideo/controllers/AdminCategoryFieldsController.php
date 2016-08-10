<?php class Ynultimatevideo_AdminCategoryFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'ynultimatevideo_video';

  protected $_requireProfileType = true;
  
  public function indexAction()
  {
    // Make navigation
    $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynultimatevideo_admin_main', array(), 'ynultimatevideo_admin_main_categories');
    $this -> view -> option_id = $option_id =  $this->_getParam('option_id');
    $tableCategory = Engine_Api::_()->getItemTable('ynultimatevideo_category');
	$category = $tableCategory -> getCategoryByOptionId($option_id);
	$this -> view -> category = $category;
    parent::indexAction();
  }
  
  public function headingCreateAction()
  {
  	 parent::headingCreateAction();
	 $form = $this->view->form;
	 if($form){
	 	$form -> removeElement('show');
		 $display = $form->getElement('display');
	     $display->setLabel('Show on video page?');
	     $display->setOptions(array('multiOptions' => array(
	          1 => 'Show on video page',
	          0 => 'Hide on video page'
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
	     $display->setLabel('Show on video page?');
	     $display->setOptions(array('multiOptions' => array(
	          1 => 'Show on video page',
	          0 => 'Hide on video page'
	        )));
	 }
  }	
  public function fieldCreateAction(){
    parent::fieldCreateAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form -> removeElement('show');
        $required = $form->getElement('required');
        $required->setOptions(array('multiOptions' => array(
            0 => 'Not required'
        )));
	  $search = $form->getElement('search');
	  $search->setLabel('Show on search videos?');
      $search->setOptions(array('multiOptions' => array(
          0 => 'Hide on search videos'
       )));
        $search->setValue(0);
      $display = $form->getElement('display');
      $display->setLabel('Show on video page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on video page',
        )));
    }
  }

  public function fieldEditAction(){
    parent::fieldEditAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form -> removeElement('show');
        $required = $form->getElement('required');
        $required->setOptions(array('multiOptions' => array(
            0 => 'Not required'
        )));
	  $search = $form->getElement('search');
	  $search->setLabel('Show on search videos?');
      $search->setOptions(array('multiOptions' => array(
          0 => 'Hide on search videos'
       )));
        $search->setValue(0);
      $display = $form->getElement('display');
      $display->setLabel('Show on video page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on video page',
        )));
    }
  }
}
?>
