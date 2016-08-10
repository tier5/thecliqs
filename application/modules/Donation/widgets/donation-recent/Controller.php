<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       30.07.12
 * @time       11:47
 */
class Donation_Widget_DonationRecentController extends Engine_Content_Widget_Abstract
{
    public $_childCount;
    public function indexAction()
    {
        /**
         * @var $table Donation_Model_DbTable_Donations
         *
         */

        $table = Engine_Api::_()->getDbtable('donations', 'donation');

        $select = $table->select()
                ->order('creation_date DESC');

        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->count = $paginator->getTotalItemCount();

        if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
            $this->_childCount = $paginator->getTotalItemCount();
        }

    }

    public function getChildCount()
    {
        return $this->_childCount;
    }
}
