<?php
class Ynmusic_Widget_SearchMusicController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$this -> view -> form = $form = new Ynmusic_Form_Search();
		
		$request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $forwardListing = true;
        if ($module == 'ynmusic') {
            if (in_array($controller, array('index', 'albums', 'songs', 'playlists', 'artists', 'history')) && in_array($action, array('index' ,'listing', 'manage')) && ($controller != $action)) {
                //$form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => $controller, 'action' => $action), 'ynmusic_general', true));	
                $forwardListing = false;
            }
			
			if (in_array($controller, array('albums', 'songs', 'playlists', 'artists'))) {
				$form->removeElement('type');
			}
			
			if ($controller == 'artists' && $action == 'index') {
				$form->removeElement('owner');
			}
			
			if ($controller == 'history') {
				$form->getElement('type')->removeMultiOption('artist');
				$browse_by = array(
					'recently_created' => 'Recently Viewed',
					'most_liked' => 'Most Liked',
					'most_viewed' => 'Most Viewed',
					'most_played' => 'Most Played',
					'a_z' => 'A - Z',
					'z_a' => 'Z - A'
				);
				$form->getElement('browse_by')->setMultiOptions($browse_by);				
			}
        }
        if ($forwardListing) {
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'listing'), 'ynmusic_general', true));
        }
		
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $form->populate($params);
        $values = $values = $form->getValues();
        $this->view->formValues = $values;
	}

}
