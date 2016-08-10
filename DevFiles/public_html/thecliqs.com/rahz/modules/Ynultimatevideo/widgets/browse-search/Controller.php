<?php
class Ynultimatevideo_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->form = $form = new Ynultimatevideo_Form_Search(array(
            'type' => 'ynultimatevideo_video'
        ));

        // Get category list and nest by level
        $categories = Engine_Api::_() -> getItemTable('ynultimatevideo_category') -> getCategories();
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category->addMultiOption($category['option_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
            }
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $module = $request->getParam('module');
        $controller = $request->getParam('controller');

        // modify label and add owner field to playlist search box
        if ($controller == 'playlist') {
            $form->keyword->setLabel(Zend_Registry::get('Zend_Translate')->_("Search Playlist"));
        } else if ($controller == 'history') {
            $form->keyword->setLabel(Zend_Registry::get('Zend_Translate')->_("Search Videos/Playlists"));
        } else {
            $form->removeElement('owner');
        }

        $action = $request->getParam('action');
        $forwardVideo = true;
        if ($module == 'ynultimatevideo') {
            if (in_array($controller,array('playlist','history','favorite')) || $action == 'manage') {
                $forwardVideo = false;
            }
            if ($action != 'manage') {
                $form->removeElement('status');
            }
        }
        if ($forwardVideo) {
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'list'), 'ynultimatevideo_general', true));
        }

        // Process form
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if ($form->isValid($p)) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $form->populate($params);
        $values = $values = $form->getValues();
        $this->view->formValues = $values;
        $this->view->topLevelId = $form->getTopLevelId();
        $this->view->topLevelValue = $form->getTopLevelValue();
    }
}