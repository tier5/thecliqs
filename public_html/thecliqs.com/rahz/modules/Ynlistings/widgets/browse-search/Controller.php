<?php
class Ynlistings_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->form = $form = new Ynlistings_Form_Search(array(
            'type' => 'ynlistings_listing'
        ));
        $categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category->addMultiOption($category['option_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
            }
        }
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $forwardListing = true;
        if ($module == 'ynlistings') {
            if ($controller == 'index' && ($action == 'manage' || $action=='browse')) {
                $forwardListing = false;
            }
            if ($action != 'manage') {
                $form->removeElement('status');
            }
        }
        if ($forwardListing) {
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'browse'), 'ynlistings_general', true));
        }

        // Process form
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if ($form->isValid($p)) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $this->view->formValues = $values;
        $this->view->topLevelId = $form->getTopLevelId();
        $this->view->topLevelValue = $form->getTopLevelValue();
    }
}