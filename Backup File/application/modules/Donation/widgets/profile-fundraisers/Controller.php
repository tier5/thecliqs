<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       02.08.12
 * @time       13:41
 */
class Donation_Widget_ProfileFundraisersController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {

    if (!Engine_Api::_()->core()->hasSubject())
    {
      return $this->setNoRender();
    }
    $this->view->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $this->view->donation = $donation = Engine_Api::_()->core()->getSubject();

    /**
     * @var $table Donation_Model_DbTable_Fundraises
     */

    $table = Engine_Api::_()->getDbTable('donations', 'donation');
    $params = array(
      'parent_id' => $donation->getIdentity()
    );
    $this->view->paginator = $paginator = Zend_Paginator::factory($table->getFundraises($params));
    $this->view->count = $count =  $paginator->getTotalItemCount();

    if (!$count) {
      return $this->setNoRender();
    }

  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}
