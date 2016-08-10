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
class Netlogtemplatedefault_Widget_NetlogMembersActiveController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		// Should we consider views or comments popular?
	$popularType = $this->_getParam('popularType', 'member');
	if( !in_array($popularType, array('view', 'member')) ) {
		$popularType = 'member';
	}
	$this->view->popularType = $popularType;
	$this->view->popularCol = $popularCol = $popularType . '_count';

		// Get paginator
	$table = Engine_Api::_()->getDbtable('users', 'user');
	$select = $table->select()
		->where('search = ?', 1)
		->where('enabled = ?', 1)
		->where($popularCol . ' >= ?', 0)
		->order($popularCol . ' DESC');

	$this->view->paginator = $paginator = Zend_Paginator::factory($select);

		// Set item count per page and current page number
	$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 9));
	$paginator->setCurrentPageNumber($this->_getParam('page', 1));

		// Do not render if nothing to show
	if( $paginator->getTotalItemCount() <= 0 ) {
		return $this->setNoRender();
	}
  }

}