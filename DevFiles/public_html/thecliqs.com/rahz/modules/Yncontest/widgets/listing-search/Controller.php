<?php

class Yncontest_Widget_ListingSearchController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {

        $view = Zend_Registry::get('Zend_View');
        $headScript = new Zend_View_Helper_HeadScript();
        $headScript->appendFile($view->layout()->staticBaseUrl . 'application/modules/Yncontest/externals/scripts/jquery-1.8.3.js');
        $headScript->appendScript('jQuery.noConflict()');
        $viewer = Engine_Api::_()->user()->getViewer();
        $items_per_page = (int)$this->_getParam('number', 12);
        $this->view->height = (int)$this->_getParam('height', 200);
        $this->view->width = (int)$this->_getParam('width', 200);

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $form = new Yncontest_Form_SearchContest();
        if( $form->isValid($request->getParams()) ) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $this->view->values = $values;
        $contestsocial = $request->getParam('contestsocial', '');
        if ($contestsocial == 'friend_contest') {
            $friends = $viewer->membership()->getMembershipsOfIds();
            $temp_key = 0;
            foreach ($friends as $friend) {
                if (!$temp_key) {
                    $values['userfriends'] = $friend;
                } else {
                    $values['userfriends'] .= "," . $friend;
                }
                $temp_key++;
            }
            if (!isset($values['userfriends'])) {
                $this->view->flag = true;
            }
        }
        $values['approve_status'] = 'approved';
        if ($request->getParam('typed') != "") {
            if ($request->getParam('typed') == 1)
                $values['browsebylist'] = 'endingsoon';
            if ($request->getParam('typed') == 2)
                $values['browsebylist'] = 'premium';
            if ($request->getParam('typed') == 3) {
                $values['direction'] = 'DESC';
                $values['orderby'] = 'approved_date';
                $values['contest_status'] = 'published';
            }

        }
        $this->view->user_id = $user_id = $viewer->getIdentity();

        $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'yncontest')->getContestPaginator($values);
        if ($paginator->getTotalItemCount() == 0)
            $this->view->flag = 1;
        $paginator->setItemCountPerPage($items_per_page);
    }
}
