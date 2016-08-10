<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       02.08.12
 * @time       12:49
 */
?>
<?php
  $address = $this->donation->getAddress();
  $contact = $this->donation->getContacts();
?>
  <?php if ($address != ''): ?>
  <a
    href="<?php echo $this->url(array('action' => 'map', 'donation_id' => $this->donation->donation_id), 'donation_extended', true);?>"
    class="smoothbox" style="float: right;">Enlarge</a>

  <br/>
  <div class="donation_address">
    <?php echo $address;?>
</div>
<?php endif;?>
<?php if ($contact != '') :?>
  <div class="donation_contacts">
    <?php echo $this->donation->getContacts();?>
  </div>
<?php endif;?>
<div class="donation_owner">
  <?php echo $this->translate('Created by %1$s on %2$s', $this->htmlLink($this->donation->getOwner()->getHref(), $this->donation->getOwner()->getTitle(),
  array('class' => 'member_donation_members_icon')), $this->locale()->toDate($this->donation->creation_date)); ?>
</div>