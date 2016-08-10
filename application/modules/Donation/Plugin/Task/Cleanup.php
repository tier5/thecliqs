<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 21.08.12
 * Time: 16:41
 * To change this template use File | Settings | File Templates.
 */
class Donation_Plugin_Task_Cleanup extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    $view = Zend_Registry::get('Zend_View');
    $table = Engine_Api::_()->getDbtable('donations', 'donation');

    $select = $table->select()
    ->where('expiry_date <= ?', new Zend_Db_Expr('NOW()'))
    ->where('status != ?', 'expired')
    ->where('parent_id = ?', 0)
    ->order('donation_id ASC')
    ->limit(50);

    $donations = $table->fetchAll($select);
    foreach($donations  as $donation ) {
      /**
       * @var $donation Donation_Model_Donation
       */
      $donation->changeStatus();

      $select = $table->select()
          ->where('parent_id = ?', $donation->getIdentity())
          ->where('status != ?', 'expired')
          ->order('donation_id');

      $fundraises = $table->fetchAll($select);
      foreach($fundraises as $fundraise) {
        $fundraise->changeStatus();

        $tofundraise = $fundraise->getOwner()->email;
        if ($tofundraise){

          Engine_Api::_()->getApi('mail', 'core')->sendSystem(
            $tofundraise,
            'donation_child_fundraise_expired',
            array(
              'child_fundraise' => $view->htmlLink($fundraise->getOwner()->getHref(), $fundraise->getOwner()->getTitle()),
              'owner_name' => $fundraise->getOwner()->getTitle(),
            )
          );
        }

      }

      $to = $donation->getOwner()->email;
      if( $to ) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
          $to,
          'donation_expired',
          array(
            'donation' => $view->htmlLink($donation->getOwner()->getHref(), $donation->getOwner()->getTitle()),
            'owner_name' => $donation->getOwner()->getTitle(),
          )
        );
      }
    }

    $select = $table->select()
      ->where('parent_id != ?', 0)
      ->where('expiry_date <= ?', new Zend_Db_Expr('NOW()'))
      ->where('status != ?', 'expired')
      ->order('donation_id ASC');

    $fundraises = $table->fetchAll($select);
    foreach($fundraises as $fundraise) {
      $fundraise->changeStatus();

      $tofundraise = $fundraise->getOwner()->email;
      if ($tofundraise){

        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
          $tofundraise,
          'donation_fundraise_expired',
          array(
            'fundraise' => $view->htmlLink($fundraise->getOwner()->getHref(), $fundraise->getOwner()->getTitle()),
            'owner_name' => $fundraise->getOwner()->getTitle(),
          )
        );
      }
    }
    /**
     * @var $table Donation_Model_DbTable_Donations
     */
    $select = $table->select()
      ->where('target_sum <= raised_sum')
      ->where('parent_id = ?', 0)
      ->where('status != ?', 'expired')
      ->order('donation_id');
    $donations = $table->fetchAll($select);

    foreach($donations  as $donation ) {
      /**
       * @var $donation Donation_Model_Donation
       */
      $donation->changeStatus();

      $select = $table->select()
        ->where('parent_id = ?', $donation->getIdentity())
        ->where('status != ?', 'expired')
        ->order('donation_id');

      $fundraises = $table->fetchAll($select);
      foreach($fundraises as $fundraise) {
        $fundraise->changeStatus();

        $tofundraise = $fundraise->getOwner()->email;
        if ($tofundraise){

          Engine_Api::_()->getApi('mail', 'core')->sendSystem(
            $tofundraise,
            'donation_child_fundraise_target',
            array(
              'child_fundraise' => $view->htmlLink($fundraise->getOwner()->getHref(), $fundraise->getOwner()->getTitle()),
              'owner_name' => $fundraise->getOwner()->getTitle(),
            )
          );
        }

      }

      $to = $donation->getOwner()->email;
      if( $to ) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
          $to,
          'donation_target',
          array(
            'donation' => $view->htmlLink($donation->getOwner()->getHref(), $donation->getOwner()->getTitle()),
            'owner_name' => $donation->getOwner()->getTitle(),
          )
        );
      }
    }


    $select = $table->select()
      ->where('parent_id != ?', 0)
      ->where('target_sum <= raised_sum')
      ->where('status != ?', 'expired')
      ->order('donation_id ASC');

    $fundraises = $table->fetchAll($select);
    foreach($fundraises as $fundraise) {
      $fundraise->changeStatus();

      $tofundraise = $fundraise->getOwner()->email;
      if ($tofundraise){

        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
          $tofundraise,
          'donation_fundraise_target',
          array(
            'fundraise' => $view->htmlLink($fundraise->getOwner()->getHref(), $fundraise->getOwner()->getTitle()),
            'owner_name' => $fundraise->getOwner()->getTitle(),
          )
        );
      }
    }

    $this->_setWasIdle();
  }
}
