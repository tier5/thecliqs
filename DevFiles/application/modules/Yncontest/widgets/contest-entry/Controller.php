<?php

class Yncontest_Widget_ContestEntryController extends Engine_Content_Widget_Abstract
{

    public function indexAction()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->form = $form = new Yncontest_Form_Entries_SearchEntry;
        $params = array();
        if ($request->getParam('entry_id') != "")
            $params['entry_id'] = $request->getParam('entry_id');
        if ($request->getParam('entry_name') != "")
            $params['entry_name'] = $request->getParam('entry_name');
        if (trim($request->getParam('entry_status')) != "")
            $params['entry_status'] = $request->getParam('entry_status');
        if ($request->getParam('owner') != "")
            $params['owner'] = $request->getParam('owner');
        if ($request->getParam('awards') != "")
            $params['awards'] = $request->getParam('awards');

        $params['contest_id'] = $request->getParam('contestId');

        $awards = Engine_Api::_()->yncontest()->getAllAwardByContest($params['contest_id']);

        $arr = array();
        $arr[] = "";
        foreach ($awards AS $award) {
            $arr[$award->award_id] = $award->award_name;
        }
        $form->awards->setMultiOptions($arr);
        $this->view->arrPlugins = Engine_Api::_()->yncontest()->getPlugins();
        $this->view->formValues = $params;
        $this->view->paginator = $paginator = Engine_Api::_()->yncontest()->getEntryPaginator($params);
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.entries.page', 10);
        $this->view->paginator->setItemCountPerPage($items_per_page);
        if (isset($params['page']))
            $this->view->paginator->setCurrentPageNumber($params['page']);
    }

}
