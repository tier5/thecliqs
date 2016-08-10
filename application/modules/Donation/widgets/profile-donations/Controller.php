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
 * @time       13:39
 */

class Donation_Widget_ProfileDonationsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    if (!Engine_Api::_()->core()->hasSubject())
    {
      return $this->setNoRender();
    }
    $this->view->donation = $donation = Engine_Api::_()->core()->getSubject();

    /**
     * @var $table Donation_Model_DbTable_Transactions
     */

    $table = Engine_Api::_()->getDbTable('transactions', 'donation');
    $params = array(
      'donation_id' => $donation->getIdentity()
    );
    $this->view->paginator = $paginator = Zend_Paginator::factory($table->getDonations($params));
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
