<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Carts.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_DbTable_Carts extends Engine_Db_Table
{
  protected $_rowClass = "Store_Model_Cart";

	protected $_user;

	public function init()
  {
		$this->_user = Engine_Api::_()->user()->getViewer();

    $prefix = $this->getTablePrefix();
    $sql = "DELETE ".$this->info('name').".* FROM ".$this->info('name')."
        LEFT JOIN ".$prefix."store_cartitems ON (".$prefix."store_cartitems.cart_id = ".$this->info('name').".cart_id )
        WHERE ".$prefix."store_cartitems.cartitem_id IS NULL";

    $db = $this->getAdapter();
    $db->query($sql);

    parent::init();
	}

	public function setUser( User_Model_User $user)
	{
		$this->_user = $user;
	}

	public function getUser()
	{
		return $this->_user;
	}


	public function getCarts()
	{
    $prefix = $this->getTablePrefix();
    $select = $this->select()
      ->where($prefix.'store_carts.user_id = ?', $this->_user->getIdentity())
      ->where($prefix.'store_carts.active = ?', 1);

    $carts = $this->fetchAll($select);

		/**
		 * @var $cart Store_Model_Cart
		 */
		foreach ( $carts as $cart ){
			if ( !$cart->hasItem() ) { $cart->delete(); }
		}

		return $this->fetchAll($select);
	}

  /**
   * @param $user_id
   * @return Store_Model_Cart
   */
  public function getCart($user_id)
  {
    $select = $this->select()
      ->where('user_id = ?', $user_id)
      ->where('active = ?', 1);

    if (null == ($row = $this->fetchRow($select))) {
      $db = $this->getAdapter();
      $db->beginTransaction();

      try {
        $row = $this->createRow(array(
          'user_id' => $user_id,
          'active' => 1
        ));
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

    return $row;
  }

	public function cancelAll($user, $page_id = 0, $except = null)
  {
    $select = $this->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('active = ?', 1)
      ;

		if ( !is_null($page_id) ){
			$select->where('page_id = ?', $page_id);
		}

    if( $except ) {
      $select->where('cart_id != ?', $except->cart_id);

		}

    foreach( $this->fetchAll($select) as $cart ) {
      try {
        $cart->cancel();
      } catch( Exception $e ) {

			//he@todo error fetching
        /*$cart->getGateway()->getPlugin()->getLog()
          ->log($e->__toString(), Zend_Log::ERR);*/
      }
    }

    return $this;
  }
}