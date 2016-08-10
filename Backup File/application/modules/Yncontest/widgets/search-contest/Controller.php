<?php

class Yncontest_Widget_SearchContestController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $location = $request -> getParam('location', '');
        $this->view->form = $form = new Yncontest_Form_SearchContest(array('location' => $location));
        $form->isValid($request->getParams());
        $values = $form->getValues();
        $this->view->formValues = array_filter($values);
    }

}
