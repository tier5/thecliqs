<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: search auctions
 * @author     Minh Nguyen
 */
class Ynauction_Widget_SearchYnauctionsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {  
    $this->view->form = $form = new Ynauction_Form_Search();
    // Populate form
    $this->view->categories = $categories = Engine_Api::_()->ynauction()->getCategories(0);
    foreach( $categories as $category )
    {
      $form->category->addMultiOption($category->category_id, $category->title);
    }
	$request = Zend_Controller_Front::getInstance()->getRequest();
    $module = $request->getParam('module');
    $controller = $request->getParam('controller');
    $action = $request->getParam('action');
	if ($module == 'ynauction') {
        if ($controller == 'index' && $action == 'participate') {
            $form -> status -> clearMultiOptions();
			$form -> status -> addMultiOption(' ', 'All');
			$form -> status -> addMultiOption('3', 'Running');
			$form -> status -> addMultiOption('4', 'Won');
			$form -> status -> addMultiOption('6', 'Ended');
			$form -> status -> isValid('3', 'Running');
			$form -> setAction("");
        }
		
		if($controller == 'index' && $action == 'manageauction') {
			$form -> orderby -> clearMultiOptions();
			$form -> orderby -> addMultiOption('creation_date', 'Most Recent');
			$form -> orderby -> addMultiOption('start_time', 'Start Time');
			$form -> orderby -> addMultiOption('featured', 'Featured');
	
			$form -> status -> clearMultiOptions();
			$form -> status -> addMultiOption(' ', 'All');
			$form -> status -> addMultiOption('0', 'Created');
			$form -> status -> addMultiOption('1', 'Pending');
			$form -> status -> addMultiOption('2', 'Upcoming');
			$form -> status -> addMultiOption('3', 'Running');
			$form -> status -> addMultiOption('4', 'Won');
			$form -> status -> addMultiOption('5', 'Sold');
			$form -> status -> addMultiOption('6', 'Ended');
			$form -> status -> isValid(' ', 'All');
			$form -> setAction("");
		}
		
		if($controller == 'proposal' && $action == 'index') {
			$form->orderby->clearMultiOptions();
	        $form->orderby->addMultiOption('creation_date', 'Most Recent');
	        $form->orderby->addMultiOption('proposal_price ', 'Price');
	        $form->removeElement('status');
			$form -> setAction("");
		}
		
		if($controller == 'win' && $action == 'index') {
			$form->orderby->clearMultiOptions();
            $form->orderby->addMultiOption('creation_date', 'Most Recent');
            $form->orderby->addMultiOption('start_time', 'Start Time');
            $form->orderby->addMultiOption('featured', 'Featured');
        	$form->removeElement('status');
			$form -> setAction("");
		}
		
	}
	
	$post = $request -> getParams();
    if($post['category'] > 0)
	{
		if($post['subcategory'] > 0)
		{
			$category  = Engine_Api::_()->getItem('ynauction_category', $post['subcategory']);
			if($category->parent != $post['category'])
				$post['subcategory'] = 0;
		}
		$this->view->subcategories = $subcategories = Engine_Api::_()->ynauction()->getCategories($post['category']);
	    foreach( $subcategories as $subcategory )
	    {
	      $form->subcategory->addMultiOption($subcategory->category_id, $subcategory->title);
	    }
	}
    else
      $post['subcategory'] = 0;    
    // Process form
    $form->isValid($post);
  }
}