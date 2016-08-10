<?php

//-------------------------------------------------
// When you integrate this code
// look for TODO as an indication
// that you may need to provide a value or take action
// before executing this code
//-------------------------------------------------
session_start();
require_once ("paypalplatform.php");


// ==================================
// PayPal Platform Preapproval Module
// ==================================

// Request specific required fields
$cancelURL		= "http://localhost";	// TODO - The landing page on your site where the customer is sent when they cancel the Preapproval action on PayPal
$returnURL		= "http://localhost/palapi/paralell.php";	// TODO - The landing page on your site where the customer returns to after the Preapproval is agreed to on PayPal
$currencyCode	= Engine_Api::_()->getApi('settings', 'core')->getSetting('YnAuction.currency', 'USD');
$startingDate	= "2011-02-25T13:00:00";	// TODO - The datetime when this preapproval agreement starts, cannot be in the past
$endingDate		= "2011-02-28T13:00:00";	// TODO - The datetime when this preapproval agreement ends
$maxTotalAmountOfAllPayments	= "2000";	// TODO - The maximum total amount of all payments, cannot exceed $2,000 USD or the equivalent in other currencies

// Request specific optional fields
//   Provide a value for each field that you want to include in the request, if left as an empty string the field will not be passed in the request
$senderEmail					= "";		// TODO - The paypal account email address of the sender
$maxNumberOfPayments			= "";		// TODO - The maximum number of payments for this preapproval
$paymentPeriod					= "";		// TODO - If this preapproval is for periodic payments, this defines the payment period as one of the following:
											//			NO_PERIOD_SPECIFIED
											//			DAILY - each day
											//			WEEKLY - each week
											//			BIWEEKLY - every other week
											//			SEMIMONTHLY - twice a month
											//			MONTHLY - each month
											//			ANNUALLY - each year
$dateOfMonth					= "";		// TODO - The day of the month on which a monthly payment is to be made, number between 1 and 31
$dayOfWeek						= "";		// TODO - The day of the week that a weekly payment is to be made, see DayOfWeek in the WSDL for valid enumerations
$maxAmountPerPayment			= "";		// TODO - The maximum amount per payment, it cannot exceed the value in maxTotalAmountOfAllPayments
$maxNumberOfPaymentsPerPeriod	= "";		// TODO - The maximum number of all payments per period
$pinType						= "";		// TODO - Whether or not a personal identification number (PIN) is required each time a Payment is made via the Pay API call
											//			NOT_REQUIRED - a PIN is not required (default)
											//			REQUIRED - a PIN is required; the sender must specify a PIN when setting up the preapproval on PayPal, and the PIN must be in the request of each subsequent Pay API call corresponding to this preapproval
											// A PIN is typically required if a Pay call against the preapproval can be made for a purchase or payment in which the sender takes an explicit action to send the money.

//-------------------------------------------------
// Make the Preapproval API call
//
// The CallPreapproval function is defined in the paypalplatform.php file,
// which is included at the top of this file.
//-------------------------------------------------
$resArray = CallPreapproval ($returnURL, $cancelURL, $currencyCode, $startingDate, $endingDate, $maxTotalAmountOfAllPayments,
								$senderEmail, $maxNumberOfPayments, $paymentPeriod, $dateOfMonth, $dayOfWeek,
								$maxAmountPerPayment, $maxNumberOfPaymentsPerPeriod, $pinType
);

$ack = strtoupper($resArray["responseEnvelope.ack"]);
if($ack=="SUCCESS")
{
	$cmd = "cmd=_ap-preapproval&preapprovalkey=" . urldecode($resArray["preapprovalKey"]);
    RedirectToPayPal ( $cmd );
} 
else  
{
	//Display a user friendly Error on the page using any of the following error information returned by PayPal
	//TODO - There can be more than 1 error, so check for "error(1).errorId", then "error(2).errorId", and so on until you find no more errors.
	$ErrorCode = urldecode($resArray["error(0).errorId"]);
	$ErrorMsg = urldecode($resArray["error(0).message"]);
	$ErrorDomain = urldecode($resArray["error(0).domain"]);
	$ErrorSeverity = urldecode($resArray["error(0).severity"]);
	$ErrorCategory = urldecode($resArray["error(0).category"]);
	
	echo "Preapproval API call failed. ";
	echo "Detailed Error Message: " . $ErrorMsg;
	echo "Error Code: " . $ErrorCode;
	echo "Error Severity: " . $ErrorSeverity;
	echo "Error Domain: " . $ErrorDomain;
	echo "Error Category: " . $ErrorCategory;
}

?>