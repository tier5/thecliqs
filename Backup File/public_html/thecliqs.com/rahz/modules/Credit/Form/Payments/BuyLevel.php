<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: BuyLevel.php 01.08.12 16:19 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Form_Payments_BuyLevel extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'buy_level_form',
        'class' => 'global_form_box'
      ));

    $view = Zend_Registry::get('Zend_View');
    $user = Engine_Api::_()->user()->getViewer();

    // Get packages
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $packages = $packagesTable->fetchAll(array('enabled = ?' => 1));

    // Get current subscription and package
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
    $currentSubscription = $subscriptionsTable->fetchRow(array(
      'user_id = ?' => $user->getIdentity(),
      'active = ?' => true
    ));

    // Get current package
    $currentPackage = null;
    if ($currentSubscription) {
      $currentPackage = $packagesTable->fetchRow(array(
        'package_id = ?' => $currentSubscription->package_id
      ));
    }

    $now = time();
    $expiration_date = strtotime($currentSubscription->expiration_date);
    $description = $view->translate('%s You can buy level via credits.', $view->htmlImage($view->layout()->staticBaseUrl . 'application/modules/Credit/externals/images/levels.png', '', array('class' => 'buy_credits_icon'))) . ' ';

    if ($currentPackage && $currentSubscription) {
      $description .= $view->translate('The plan you are currently subscribed to is: %1$s', '<strong>' . $view->translate($currentPackage->title) . '</strong>. ') .
        $view->translate('You are currently paying: %1$s credits', '<strong>' . Engine_Api::_()->credit()->getPackageDescription($currentPackage) . '</strong>. ');
      if ($now < $expiration_date) {
        $description .= $view->translate('CREDIT_If you would like to change your subscription, please click on the button \"Upgrade\"');
      } else {
        $description .= $view->translate('CREDIT_Your subscription plan has been expired. But you can choose one now below again.');
      }

    } else {
      $description .= $view->translate('You have not yet selected a subscription plan. Please choose one now below.');
    }

    $this->setDescription($description);

    $multiOptions = array();
    foreach ($packages as $package) {
      if ($currentPackage && $currentPackage->package_id == $package->package_id) {
        continue;
      }
      $multiOptions[$package->package_id] = $package->title .
        ' <a href="javascript://" onclick="moreDetails(' . $package->package_id . ')" title="' . $view->translate('details') . '">[?]</a>';
    }

    $buttonLabel = 'Continue';
    $typeButton = 'submit';
    $onClickFunction = '';
    $style = '';

    if ($currentPackage && $currentSubscription && $now < $expiration_date) {
      $expiration_date = date('d/m/Y', $expiration_date);
      $this->addElement('Heading', 'expiration_date', array(
        'label' => 'Expiration date:',
        'value' => $expiration_date
      ));
      //echo '<div style="float:left; margin-left:115px; margin-top:-99px;">' . $expiration_date . '</div>';

      $this->addElement('Hidden', 'already_paid', array(
        'value' => 1
      ));
      $buttonLabel = 'CREDIT_Upgrade';
      $typeButton = 'button';
      $onClickFunction = 'redirect()';
      //$style = 'display: none';
    } else {
      $this->addElement('Radio', 'package_id', array(
        'label' => 'Choose Plan:',
        'required' => true,
        'allowEmpty' => false,
        'multiOptions' => $multiOptions,
        'escape' => false
      ));

      $this->addElement('Hidden', 'already_paid', array(
        'value' => 0
      ));
    }

    $this->addElement('Button', 'buy_level', array(
      'label' => $buttonLabel,
      'type' => $typeButton,
      'onclick' => $onClickFunction,
      'style' => $style
    ));
  }
}