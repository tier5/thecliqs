<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Subscription.php 27.07.11 15:15 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Model_Subscription extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  protected $_modifiedTriggers = false;

  protected $_page;

  protected $_gateway;

  protected $_package;

  protected $_statusChanged;

	protected $_type = 'page_subscription';

  public function getPage()
  {
    if( empty($this->page_id) ) {
      return null;
    }
    if( null === $this->_page ) {
      $this->_page = Engine_Api::_()->getItem('page', (int)$this->page_id);
    }
    return $this->_page;
  }

	/**
	 * @return Page_Model_Package
	 */
  public function getPackage()
  {
    if( empty($this->package_id) || is_null($this->package_id) ) {
      return null;
    }
		
    if( null === $this->_package ) {
      $this->_package = Engine_Api::_()->getItem('page_package', $this->package_id);
    }
    return $this->_package;
  }

  // Active

  public function setActive($flag = true, $deactivateOthers = null)
  {
    $this->active = true;

    if( (true === $flag && null === $deactivateOthers) ||
        $deactivateOthers === true ) {
      $table = $this->getTable();
      $select = $table->select()
        ->where('page_id = ?', $this->page_id)
        ->where('active = ?', true)
        ;
      foreach( $table->fetchAll($select) as $otherSubscription ) {
        $otherSubscription->setActive(false);
      }
    }

    $this->save();
    return $this;
  }

  public function getGateway()
  {
    if( empty($this->gateway_id) ) {
      return null;
    }
    if( null === $this->_gateway ) {
      $this->_gateway = Engine_Api::_()->getItem('page_gateway', $this->gateway_id);
    }
    return $this->_gateway;
  }

  public function onPaymentSuccess()
  {
    $this->_statusChanged = false;
    if( in_array($this->status, array('initial', 'trial', 'pending', 'active')) ) {

      // If the subscription is in initial or pending, set as active and
      // cancel any other active subscriptions
      if( in_array($this->status, array('initial', 'pending')) ) {
        $this->setActive(true);
        if( $this->getPage() )
          Engine_Api::_()->getDbtable('subscriptions', 'page')
            ->cancelAll($this->getPage(), 'User cancelled the subscription.', $this);
      }

      // Update expiration to expiration + recurrence or to now + recurrence?
		/**
		 * Check if the page should be enabled
		 *
		 * @var $package Page_Model_Package
		 */
      $package = $this->getPackage();
      $expiration = $package->getExpirationDate();
      if( $expiration ) {
        $this->expiration_date = date('Y-m-d H:i:s', $expiration);
      }

      // Change status
      if( $this->status != 'active' ) {
        $this->status = 'active';
        $this->_statusChanged = true;
      }

      // Update page if active
      if( $this->active ) {
        $this->upgradePage();
      }
    }
    $this->save();

    return $this;
  }

  // Actions

  public function upgradePage()
  {
		/**
		 * Add new activity
		 *
		 * @var $package Page_Model_Package
		 * @var $page Page_Model_Page
		 * @var $user User_Model_User
		 * @var $activityApi Activity_Model_DbTable_Actions
		 * @var $action Activity_Model_Action
		 * @var $authTb Authorization_Model_DbTable_Permissions
		 */
		$package = $this->getPackage();
		$page = $this->getPage();
    if( !$page ) {
      return $this;
    }
		$user = $page->getOwner();
		$page->package_id = $package->getIdentity();
		$page->featured = $package->featured;
		$page->sponsored = $package->sponsored;
		$page->approved = $package->autoapprove;
		$page->enabled = 1;

		if ( $page->save() && $page->isNew())
		{
			$availableLabels = array(
				'everyone' => 'Everyone',
				'registered' => 'Registered Members',
				'likes' => 'Likes, Admins and Owner',
				'team' => 'Admins and Owner Only'
			);

			$view_options = array_intersect_key($availableLabels, array_flip($package->auth_view));
			$comment_options = array_intersect_key($availableLabels, array_flip($package->auth_comment));
			$posting_options = array_intersect_key($availableLabels, array_flip($package->auth_posting));

			$values = array('auth_view' => key($view_options), 'auth_comment' => key($comment_options), 'auth_posting' => key($posting_options));
			$page->setPrivacy($values);

			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($user, $page, 'page_create');
			if ($action) {
				$activityApi->attachActivity($action, $page);
			}
		}

    return $this;
  }

  public function downgradePage()
  {
    $page = $this->getPage();
    if( !$page ) {
      return $this;
    }
    $package = Engine_Api::_()->getDbtable('packages', 'page')->getDefaultPackage();
    if( $page && $package && $page->package_id != $package->getIdentity() ) {
      $page->package_id = $package->getIdentity();

      switch($page->auto_set){
        case 0:
          $page->sponsored = $package->sponsored;
          $page->featured = $package->featured;
          break;
        case 1:
          $page->sponsored = $package->sponsored;
          break;
        case 2:
          $page->featured = $package->featured;
          break;
      }
    }

    $page->save();

    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'page');

    $row = $subscriptionsTable->createRow(array(
      'page_id' => $page->getIdentity(),
      'package_id' => $package->getIdentity(),
      'status' => 'active',
      'active' => true
    ));
    $row->save();

    return $this;
  }

  public function cancel()
  {
    // Try to cancel recurring payments in the gateway
    if( !empty($this->gateway_id) && !empty($this->gateway_profile_id) ) {
      try {
        $gateway = Engine_Api::_()->getItem('payment_gateway', $this->gateway_id);
	      /**
	       * @var $gatewayPlugin Page_Plugin_Gateway_PayPal @todo remove in next build
	       */
        $gatewayPlugin = $gateway->getPlugin();
        if( method_exists($gatewayPlugin, 'cancelSubscription') ) {
          $gatewayPlugin->cancelSubscription($this->gateway_profile_id);
        }
      } catch( Exception $e ) {
        // Silence?
      }
    }
    // Cancel this row
    $this->active = false; // Need to do this to prevent clearing the user's session
    $this->onCancel();
    return $this;
  }

  // Events

  public function clearStatusChanged()
  {
    $this->_statusChanged = null;
    return $this;
  }

  public function didStatusChange()
  {
    return (bool) $this->_statusChanged;
  }

  public function onPaymentPending()
  {
    $this->_statusChanged = false;
    if( in_array($this->status, array('initial', 'trial', 'pending', 'active')) ) {
      // Change status
      if( $this->status != 'pending' ) {
        $this->status = 'pending';
        $this->_statusChanged = true;
      }

      // Downgrade and log out user if active
      if( $this->active ) {
        // @todo should we do this?
        // Downgrade user
        $this->downgradePage();

      }
    }
    $this->save();

    // Check if the member should be enabled
    $page = $this->getPage();
    if( $page ) {
      $page->enabled = true; // This will get set correctly in the update hook
      $page->save();

    }

    return $this;
  }

  public function onPaymentFailure()
  {
    $this->_statusChanged = false;
    if( in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue')) ) {
      // Change status
      if( $this->status != 'overdue' ) {
        $this->status = 'overdue';
        $this->_statusChanged = true;
      }

      // Downgrade and log out user if active
      if( $this->active ) {
        // Downgrade user
        $this->downgradePage();

        // Remove active sessions?
//        Engine_Api::_()->getDbtable('session', 'core')->removeSessionByAuthId($this->user_id);
      }
    }
    $this->save();

    // Check if the member should be enabled
    $page = $this->getPage();
    if( $page ) {
      $page->enabled = false; // This will get set correctly in the update hook
      $page->save();
    }

    return $this;
  }

  public function onCancel()
  {
    $this->_statusChanged = false;
    if( in_array($this->status, array('initial', 'trial', 'pending', 'active', 'overdue', 'cancelled')) ) {
      // Change status
      if( $this->status != 'cancelled' ) {
        $this->status = 'cancelled';
        $this->_statusChanged = true;
      }

      // Downgrade and log out user if active
      if( $this->active ) {
        // Downgrade user
        $this->downgradePage();

        // Remove active sessions?
//        Engine_Api::_()->getDbtable('session', 'core')->removeSessionByAuthId($this->user_id);
      }
    }
    $this->save();

    // Check if the member should be enabled
    $page = $this->getPage();
    if( $page ) {
      $page->enabled = false; // This will get set correctly in the update hook
      $page->save();
    }

    return $this;
  }

  public function onExpiration()
  {
    $this->_statusChanged = false;
    if( in_array($this->status, array('initial', 'trial', 'pending', 'active', 'expired')) ) {
      // Change status
      if( $this->status != 'expired' ) {
        $this->status = 'expired';
        $this->_statusChanged = true;
      }

      if( $this->active ) {
        $this->active = false;
        $this->downgradePage();
      }
    }
    $this->save();

    // Check if the member should be enabled
    $page = $this->getPage();
    $page->enabled = false; // This will get set correctly in the update hook
    $page->save();

    return $this;
  }

  public function onRefund()
  {
    $this->_statusChanged = false;
    if( in_array($this->status, array('initial', 'trial', 'pending', 'active', 'refunded')) ) {
      // Change status
      if( $this->status != 'refunded' ) {
        $this->status = 'refunded';
        $this->_statusChanged = true;
      }

      // Downgrade and log out user if active
      if( $this->active ) {
        // Downgrade user
        $this->downgradePage();

        // Remove active sessions?
//        Engine_Api::_()->getDbtable('session', 'core')->removeSessionByAuthId($this->user_id);
      }
    }
    $this->save();

    // Check if the member should be enabled
    $page = $this->getPage();
    $page->enabled = true; // This will get set correctly in the update hook
    $page->save();

    return $this;
  }
}