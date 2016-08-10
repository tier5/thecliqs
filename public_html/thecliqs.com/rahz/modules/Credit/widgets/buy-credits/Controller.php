<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 18.01.12 19:02 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Widget_BuyCreditsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    /**
     * @var $viewer User_Model_User
     * @var $gatewayTable Payment_Model_DbTable_Gateways
     */

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }

    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');

    if (!$gatewayTable->getEnabledGatewayCount()) {
      return $this->setNoRender();
    }

    $packages = Engine_Api::_()->getDbTable('payments', 'credit')->fetchAll();
    if (!count($packages)) {
      return $this->setNoRender();
    }

    $this->view->form = $form = new Credit_Form_Payments_Buy();
    $form->getDecorator('description')->setOption('escape', false);
  }
}