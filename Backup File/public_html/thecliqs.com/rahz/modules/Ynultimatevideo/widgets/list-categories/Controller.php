<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListCategoriesController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') .
            'application/modules/Ynultimatevideo/externals/scripts/collapsible.js');

        $categoryTable = Engine_Api::_()->getDbTable('categories', 'ynultimatevideo');
        $categories = $categoryTable->getCategories();
        unset($categories[0]);
        $this->view->categories = $categories;
        if (count($categories) == 0) {
            $this->setNoRender(true);
        }
    }
}