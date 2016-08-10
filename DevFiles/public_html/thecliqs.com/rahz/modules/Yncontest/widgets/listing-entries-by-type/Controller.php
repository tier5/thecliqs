<?php
class Yncontest_Widget_ListingEntriesByTypeController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
		$this->view->options = array(
				'start_date' => 'Recent Entries',				
				'vote_count' => 'Most Voted Entries',				
		);
		$this->getElement()->removeDecorator('Title');
		$this->view->entryType  = $entryType = $this -> _getParam('typeyncontest','advalbum');
		$module_enable = Engine_Api::_() -> hasModuleBootstrap($entryType);
		if (!$module_enable) {
			return $this -> setNoRender();
		}
		$this->view->height = (int)$this -> _getParam('height'.$entryType,245);
		$this->view->width = (int)$this -> _getParam('width'.$entryType,142);
		$this->view->browseby = $this->_getParam('browseby', 'start_date');
		$this->view->formValues = $values = array('entry_type' => $entryType, 'max' => (int)$this -> _getParam('max',12), 'browseby' => $this->_getParam('browseby', 'start_date'));
		$table = Engine_Api::_()->getItemTable('yncontest_entries');
		$select = Engine_Api::_()->yncontest()->getEntries($values);
		$this->view->entries = $entries = $table->fetchAll($select);
		if(!count($entries))
			$this -> setNoRender();
	}
}