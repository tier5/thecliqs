<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2014 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: .php longl $
 * @author     LONGL
 */

class Ynmobile_Api_Subscription extends Core_Api_Abstract
{
    
    
	protected function getPackages($isSignUp = false, $currentPackage = null)
	{
		// Get available subscriptions
		$packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
		$packagesSelect = $packagesTable
		-> select()
		-> from($packagesTable)
		-> where('enabled = ?', true);
			
		if ($isSignUp)
		{
			$packagesSelect -> where('signup = ?', true);
		}
		 
		$multiOptions = array();
		$packages = $packagesTable->fetchAll($packagesSelect);
		$result = array();
		$view = Zend_Registry::get("Zend_View");
		$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
        
		foreach( $packages as $package )
		{
			if (!is_null($currentPackage) && ($package->package_id == $currentPackage->package_id))
			{
				continue;
			}

			$result[] = array(
	    		'iPackageId' => $package->package_id,
	    		'sTitle' => $package->title,
	    		'sBriefDescription' => $package->getPackageDescription(),
	    		'sDescription' => $package->description,
	    		'fPrice' => $package->price,
	    		'sPrice' => $view->locale()->toCurrency($package->price, $currency),
	    		'iRecurrence' => $package->recurrence,
	    		'sRecurrenceType' => $package->recurrence_type,
	    		'iDuration' => $package->duration,	
	    		'sDurationType' => $package->duration_type,
	    		'bEnabled' => ($package->enabled) ? true : false,
	    		'sCurrency' => $currency,

			);
		}
		return $result;
	}

	public function packages($aData)
	{
		extract($aData);
		
		$user = Engine_Api::_()->user()->getViewer();
		$packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
		$subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
			
		$currentSubscription = $subscriptionsTable->fetchRow(array(
	      'user_id = ?' => $user->getIdentity(),
	      'active = ?' => true,
		));
		$currentPackage = NULL;
		if (!$currentSubscription)
		{
			$currentSubscription = $subscriptionsTable->activateDefaultPlan($user);
		}
		if($currentSubscription)
		{
			$currentPackage = $packagesTable->fetchRow(array(
		        'package_id = ?' => $currentSubscription->package_id,
			));
		}
		return $this->getPackages(false, $currentPackage);
	}

	public function signup_packages($aData)
	{
		extract($aData);
		return $this->getPackages(true);
	}

	public function current_package($aData)
	{
		try {
			extract($aData);
			$user = Engine_Api::_()->user()->getViewer();
			$packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
			$subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
            
            
				
			$currentSubscription = $subscriptionsTable->fetchRow(array(
		      'user_id = ?' => $user->getIdentity(),
		      'active = ?' => true,
			));
			
			if (!$currentSubscription)
			{
				$currentSubscription = $subscriptionsTable->activateDefaultPlan($user);
			}
			if($currentSubscription)
			{
				$package = $currentPackage = $packagesTable->fetchRow(array(
			        'package_id = ?' => $currentSubscription->package_id,
				));
			}
			if($package)
			{
				$view = Zend_Registry::get("Zend_View");
				$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
				return array(
			    		'iPackageId' => $package->package_id,
			    		'sTitle' => $package->title,
			    		'sBriefDescription' => $package->getPackageDescription(),
			    		'sDescription' => $package->description,
			    		'fPrice' => $package->price,
			    		'sPrice' => $view->locale()->toCurrency($package->price, $currency),
			    		'iRecurrence' => $package->recurrence,
			    		'sRecurrenceType' => $package->recurrence_type,
			    		'iDuration' => $package->duration,	
			    		'sDurationType' => $package->duration_type,
			    		'bEnabled' => ($package->enabled) ? true : false,
			    		'sCurrency' => $currency,
					
				);
			}
			else 
			{
				return array();
			}
		} 
		catch (Exception $e) 
		{
			return array(
				'error_code' => 1,
				'error_message' => $e->getTraceAsString()
			);
		}
	}

	public function add_subscription($aData)
	{
		extract($aData);
		if (!isset($iPackageId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPackageId!"),
			);
		}
		// Process
		if (isset($iUserId))
		{
			$user = Engine_Api::_()->user()->getUser($iUserId);
		}
		else 
		{
			$user = Engine_Api::_()->user()->getViewer();
		}

		// Get packages
		$packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
		$package = $packagesTable->fetchRow(array(
	      'enabled = ?' => 1,
	      'package_id = ?' => (int) $iPackageId,
		));

		// Get current subscription and package
		$subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
		$currentSubscription = $subscriptionsTable->fetchRow(array(
	      'user_id = ?' => $user->getIdentity(),
	      'active = ?' => true,
		));

		// Get current package
		$currentPackage = null;
		if( $currentSubscription ) {
			$currentPackage = $packagesTable->fetchRow(array(
	        	'package_id = ?' => $currentSubscription->package_id,
			));
		}

		// Cancel any other existing subscriptions
		Engine_Api::_()->getDbtable('subscriptions', 'payment')
		->cancelAll($user, 'User cancelled the subscription.', $currentSubscription);


		// Insert the new temporary subscription
		$db = $subscriptionsTable->getAdapter();
		$db -> beginTransaction();

		try 
		{
			$subscription = $subscriptionsTable->createRow();
			$subscription->setFromArray(array(
		        'package_id' => $package->package_id,
		        'user_id' => $user->getIdentity(),
		        'status' => 'initial',
		        'active' => false, // Will set to active on payment success
		        'creation_date' => new Zend_Db_Expr('NOW()'),
			));
			$subscription->save();

			// If the package is free, let's set it active now and cancel the other
			if( $package->isFree() ) 
			{
				$subscription->setActive(true);
				$subscription->onPaymentSuccess();
				if( $currentSubscription ) 
				{
					$currentSubscription->cancel();
				}
			}

			$subscription_id = $subscription->subscription_id;
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'iSubscriptionId' => $subscription_id
			);
		}
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage(),
			);
		}
	}
	
	public function update_subscription($aData)
	{
		extract($aData);
		if (!isset($iSubscriptionId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iSubscriptionId!"),
			);
		}
		$subscription = Engine_Api::_()->getItem("payment_subscription", $iSubscriptionId);
		if ($sStatus == 'success')
		{
			$subscription->onPaymentSuccess();
		}
		elseif ($sStatus == 'fail')
		{
			$subscription->onPaymentFailure();
		}
		return array(
			'error_code' => 0,
			'error_message' => '',
			'message' => Zend_Registry::get('Zend_Translate') -> _("Updated subscription successfully!"),
		);
	}
	
	public function detail($aData)
	{
		extract($aData);
		if (!isset($iPackageId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPackageId!"),
			);
		}
		// Process
		$user = Engine_Api::_()->user()->getViewer();
		$package = Engine_Api::_()->getItem('payment_package', $iPackageId);
		if (!$package)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This package is not existed!"),
			);
		}
		
		$storekitTable = Engine_Api::_() -> getDbtable('storekitpurchases', 'ynmobile');
		$iphoneProduct = $storekitTable->getProduct('iphone', $iPackageId);
		$ipadProduct = $storekitTable->getProduct('ipad', $iPackageId);
		$androidProduct = $storekitTable->getProduct('android', $iPackageId);
		
		$view = Zend_Registry::get("Zend_View");
		$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
		return array(
    		'iPackageId' => $package->package_id,
    		'sTitle' => $package->title,
    		'sBriefDescription' => $package->getPackageDescription(),
    		'sDescription' => $package->description,
    		'fPrice' => $package->price,
    		'sPrice' => $view->locale()->toCurrency($package->price, $currency),
    		'iRecurrence' => $package->recurrence,
    		'sRecurrenceType' => $package->recurrence_type,
    		'iDuration' => $package->duration,	
    		'sDurationType' => $package->duration_type,
    		'bEnabled' => ($package->enabled) ? true : false,
    		'sCurrency' => $currency,
			'sIphoneProduct' => ($iphoneProduct) ? $iphoneProduct->storekitpurchase_key : '',
			'sIpadProduct' => ($ipadProduct) ? $ipadProduct->storekitpurchase_key : '',
			'sAndroidProduct' => ($androidProduct) ? $androidProduct->storekitpurchase_key : '',
		);
	}
	
}
	
	
	
