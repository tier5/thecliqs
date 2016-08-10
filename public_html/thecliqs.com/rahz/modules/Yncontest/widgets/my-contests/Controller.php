<?php

class Yncontest_Widget_MyContestsController extends Engine_Content_Widget_Abstract
{

    public function indexAction()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->form = $form = new Yncontest_Form_Contest_Search;
        $params = $request->getParams();
        $form->populate($params);
        if (empty($params['orderby'])) $params['orderby'] = 'contest_id';
        if (empty($params['direction'])) $params['direction'] = 'DESC';
        $viewer = Engine_Api::_()->user()->getViewer();
        $params['admin'] = 1;
        $params['manage'] = 1;
        $params['owner_id'] = $viewer->getIdentity();
        $this->view->paginator = $paginator = Engine_Api::_()->yncontest()->getContestPaginator($params);
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.page', 10);
        $this->view->paginator->setItemCountPerPage($items_per_page);
        if (isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);
    }

}
