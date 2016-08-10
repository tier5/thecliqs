<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_CategoriesController extends Engine_Content_Widget_Abstract
{
    public function indexAction() {
        $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') .
            'application/modules/Ynvideochannel/externals/scripts/collapsible.js');

        $categoryTable = Engine_Api::_()->getDbTable('categories', 'ynvideochannel');
        $categories = $categoryTable->getCategories();
        unset($categories[0]);
        $this->view->categories = $categories;
        if (count($categories) == 0) {
            $this->setNoRender(true);
        }
        $this -> view -> type = $this -> _getParam('type', 'videos');
    }
}
