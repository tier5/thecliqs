<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Controller
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       30.07.12
 * @time       16:40
 */
class Donation_Widget_DonationSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $searchForm = $this->view->searchForm = new Donation_Form_Search();
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $searchForm
      ->setMethod('get')
      ->populate($request->getParams())
    ;
  }
}