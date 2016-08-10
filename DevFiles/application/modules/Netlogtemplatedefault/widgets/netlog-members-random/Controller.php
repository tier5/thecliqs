<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlogtemplatedefault
 * @copyright  Copyright 2010-2012 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     altrego aka Vadim ( provadim@gmail.com )
 */

class Netlogtemplatedefault_Widget_NetlogMembersRandomController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

	$table = Engine_Api::_()->getDbtable('users', 'user');
	$select = $table->select()
		->where('search = ?', 1)
		->where('enabled = ?', 1)
		->where('photo_id <> ?', 0)
		->order( new Zend_Db_Expr('RAND() ASC') )
	;
	
	$this->view->paginator = $paginator = Zend_Paginator::factory($select);

		// Set item count per page and current page number
	$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 25));
	$paginator->setCurrentPageNumber($this->_getParam('page', 1));

  }

}