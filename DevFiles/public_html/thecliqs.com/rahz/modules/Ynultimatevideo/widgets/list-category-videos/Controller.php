<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListCategoryVideosController extends Engine_Content_Widget_Abstract {
    public function indexAction() {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $this -> _getAllParams();
        $numberOfItems = $this->_getParam('itemCountPerPage', 6);
        $categoryId = $this->_getParam('category_id', 0);

        if ($request->isPost()) {
            $element = $this->getElement();
            $element->clearDecorators();
        }

        // get category
        $category = null;
        if ($categoryId) {
            $category = Engine_Api::_()->getItem('ynultimatevideo_category', $categoryId);
        }
        if (!$categoryId || !$category) {
            return $this->setNoRender();
        }

        // set Title
        $this->getElement()->setTitle($category->getTitle());

        // view mode
        $mode_enabled = array();
        if(isset($params['mode_simple']) && $params['mode_simple'])
        {
            $mode_enabled[] = 'simple';
        }
        if(isset($params['mode_grid']) && $params['mode_grid'])
        {
            $mode_enabled[] = 'grid';
        }
        if(isset($params['mode_list']) && $params['mode_list'])
        {
            $mode_enabled[] = 'list';
        }
        if(isset($params['mode_casual']) && $params['mode_casual'])
        {
            $mode_enabled[] = 'casual';
        }
        if(isset($params['view_mode']) && in_array($params['view_mode'], $mode_enabled))
        {
            $view_mode = $params['view_mode'];
        } else if ($mode_enabled) {
            $view_mode = $mode_enabled[0];
        } else {
            $view_mode = 'simple';
        }

        $class_mode = 'ynultimatevideo_'. $view_mode .'-view';

        $this -> view -> mode_enabled = $mode_enabled;
        $this -> view -> class_mode = $class_mode;
        $this -> view -> view_mode = $view_mode;
        
        // Get paginator
        $selectParams = array(
            'category_id' => $categoryId,
            'search' => 1,
            'status' => 1,
        );

        $select = Engine_Api::_()->getItemTable('ynultimatevideo_video') -> getVideosSelect($selectParams);
        $select->limit($numberOfItems);
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $this->view->paginator->setItemCountPerPage($numberOfItems);

        $this->view->can_create = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')
                ->setAuthParams('ynultimatevideo_video', null, 'create')->checkRequire();
    }
}