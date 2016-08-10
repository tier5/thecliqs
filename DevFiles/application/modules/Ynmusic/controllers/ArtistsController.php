<?php
class Ynmusic_ArtistsController extends Core_Controller_Action_Standard {
	public function indexAction() {
		$this->_helper->content->setEnabled()->setNoRender();
	}
	
	public function viewAction() {
		$this->_helper->content->setEnabled();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = null;
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			$id = $this -> _getParam('id');
			if (null !== $id) {
				$subject = Engine_Api::_() -> getItem('ynmusic_artist', $id);
				if ($subject && $subject -> getIdentity()) {
					Engine_Api::_() -> core() -> setSubject($subject);
				} else {
					return $this -> _helper -> requireSubject() -> forward();
				}
			}
		}
		if(!$subject -> isAdmin) {
			return $this->_helper->requireSubject()->forward();
		}
		$this -> _helper -> requireSubject('ynmusic_artist');
	}
	
}	
?>