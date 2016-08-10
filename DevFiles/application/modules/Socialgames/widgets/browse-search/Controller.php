<?php

class Socialgames_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Make form
    $this->view->form = $form = new Socialgames_Form_Search();
    

    // Populate form
    $categories = Engine_Api::_()->getDbtable('games', 'socialgames')->getCategoriesAssoc();
    if( !empty($categories) && is_array($categories) && $form->getElement('category') ) {
      $form->getElement('category')->addMultiOptions($categories);
    }

    // Process form
    $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $form->isValid($p);
    $values = $form->getValues();
    $this->view->formValues = array_filter($values);
	
    $this->view->assign($values);
  }
}
