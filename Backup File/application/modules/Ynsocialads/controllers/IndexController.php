<?php

class Ynsocialads_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
     return $this->_helper->redirector->gotoRoute(array(''), 'ynsocialads_campaigns', true);
  }
}
