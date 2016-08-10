<?php

class Yncontest_Widget_ProfileEntriesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $this->getElement()->removeDecorator('Title');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        $contest = Engine_Api::_()->core()->getSubject();

        if (!$contest->authorization()->isAllowed($viewer, 'viewentries')) {
            return $this->setNoRender();
        }

        // Get paginator
        $request = Zend_Controller_Front::getInstance()->getRequest();
        //List params for getting new blogs
        $this->view->recentType = $recentType = $request->getParam('recentType', 'creation');
        if (!in_array($recentType, array(
            'creation',
            'modified'
        ))
        ) {
            $recentType = 'creation';
        }
        $this->view->recentCol = $recentType . '_date';
        $contest_id = $request->getParam('contestId');
        $table = Engine_Api::_()->getDbtable('entries', 'yncontest');
        $select = $table->select()
                ->where('contest_id =?', $contest_id)
                ->where("entry_status = 'published' or entry_status = 'win'")
                ->where("approve_status = 'approved'")
                ->order('start_date');
        $this->view->paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage($request->getParam('itemCountPerPage', 4));
        $this->view->paginator->setCurrentPageNumber($request->getParam('page', 1));


    }


}