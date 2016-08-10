<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Cleanup.php 8221 2011-01-15 00:24:02Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Page_Plugin_Task_Cleanup extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
		/**
		 * Declare variables
		 *
		 * @var $subscriptionsTable Page_Model_DbTable_Subscriptions
		 * @var $subscription Page_Model_Subscription
		 * @var $package Page_Model_Package
		 */
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'page');


    // Get subscriptions that have expired or have finished their trial period
    // (trial is not yet implemented)
    $select = $subscriptionsTable->select()
      ->where('expiration_date <= ?', new Zend_Db_Expr('NOW()'))
      ->where('status = ?', 'active')
      ->order('subscription_id ASC')
      ->limit(10)
      ;

    foreach( $subscriptionsTable->fetchAll($select) as $subscription ) {
      $package = $subscription->getPackage();
      // Check if the package has an expiration date

      $expiration = $package->getExpirationDate();
      if( !$expiration ) {
        continue;
      }
      // It's expired
      // @todo send an email
      $subscription->onExpiration();

      //Send message
      $page = $subscription->getPage();

      $to = $page->getOwner()->email;
      if( $to ) {
        $message = Zend_Registry::get('Zend_Translate')->_('PAGE_EXPIRED_MESSAGE');
        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
          $to,
          'page_expired',
          array(
            'page' => $page->getTitle(),
            'owner_name' => $page->getOwner()->getTitle(),
            'message' => $message,
          )
        );
      }
    }

    
    // Get subscriptions that are old and are pending payment
    $select = $subscriptionsTable->select()
      ->where('status IN(?)', array('initial', 'pending'))
      ->where('expiration_date <= ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 2 DAY)'))
      ->order('subscription_id ASC')
      ->limit(10)
      ;

    foreach( $subscriptionsTable->fetchAll($select) as $subscription ) {
      $subscription->onCancel();
    }
  }
}


