<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Subscriptions.php 27.07.11 15:17 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_DbTable_Subscriptions extends Engine_Db_Table
{
  protected $_rowClass = 'Page_Model_Subscription';

  public function cancelAll(Page_Model_Page $page, $note = null,
      Page_Model_Subscription $except = null)
  {
    $select = $this->select()
      ->where('page_id = ?', $page->getIdentity())
      ->where('status = \'initial\' OR active = ?', true)
      ;

    if( $except ) {
      $select->where('subscription_id != ?', $except->subscription_id);
    }

    foreach( $this->fetchAll($select) as $subscription ) {
      try {
        $subscription->cancel();
      } catch( Exception $e ) {
        $subscription->getGateway()->getPlugin()->getLog()
          ->log($e->__toString(), Zend_Log::ERR);
      }
    }

    return $this;
  }

	public function getSubscription( $page_id, $active = false)
	{
		$select = $this
			->select()
			->where('page_id = ?', $page_id)
			->order('subscription_id DESC');

    if( $active ) {
      $select->where('active = 1');
    }
		return $this->fetchRow($select);
	}

}