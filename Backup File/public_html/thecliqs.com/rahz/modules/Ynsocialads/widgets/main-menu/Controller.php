<?php
class Ynsocialads_Widget_MainMenuController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $viewer = Engine_Api::_()->user()->getViewer();
        // if (!Engine_Api::_()->authorization()->isAllowed('ynsocialads', $viewer, 'view')) {
            // return $this->setNoRender();
        // }
		$this->view->navigation = Engine_Api::_()
		->getApi('menus', 'core')
		->getNavigation('ynsocialads_main', array());
		if (count($this->view->navigation) == 1) {
			$this->view->navitigation = null;
		}
	}
}
