<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: take-action.tpl  23.12.11 11:06 TeaJay $
 * @author     Taalay
 */
?>

<div class="global_form_popup">
	<div class="settings">
	  <form class="global_form" method="post">
	    <div>
	      <?php if ($this->claim->status == 'approved'): ?>
	        <h3><?php echo $this->translate("Details"); ?></h3>
	        <p><?php echo $this->translate("Below are the details of the claim request that was approved.") ?></p>
	      <?php elseif ($this->claim->status == 'declined'): ?>
	        <h3><?php echo $this->translate("Details"); ?></h3>
	        <p><?php echo $this->translate("Below are the details of the claim request that was declined.") ?></p>
	      <?php else: ?>
	        <h3><?php echo $this->translate("Take an Action"); ?></h3>
	        <p>
            <?php echo $this->translate("Please take an appropriate action on the claim for this page: ") . $this->htmlLink($this->page->getHref(), $this->page->getTitle(), array('target' => '_blank')); ?>
          </p>
	        <p><?php echo $this->translate("After save this form, an email will be sent to this claimer stating the action taken by you.") ?></p><br />
	      <?php endif; ?>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Claimer Name") ?>:</label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->claim->claimer_name; ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Claimer Email") ?>:</label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->claim->claimer_email; ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Claimed Date") ?>:</label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->claim->creation_date; ?>
	        </div>
	      </div>
        <div class="form-wrapper">
          <div class="form-label">
            <label><?php echo $this->translate("Claimer Phone Number") ?>:</label>
          </div>
          <div class="form-element">
            <?php echo $this->claim->claimer_phone; ?>
          </div>
        </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("About Claimer and Page") ?>:</label>
	        </div>
	        <div class="form-element">
	          <?php echo $this->claim->description; ?>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label><?php echo $this->translate("Status") ?>:</label>
	        </div>
	        <div class="form-element">
	          <span class="<?php echo $this->claim->status?>"><?php echo $this->claim->status; ?></span>
	        </div>
	      </div>
	      <div class="form-wrapper">
	        <div class="form-label">
	          <label>&nbsp;</label>
	        </div>
	        <div class="form-element">
            <?php if ($this->claim->status == 'approved') : ?>
	            <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close") ?></button>
            <?php elseif ($this->claim->status == 'declined') : ?>
	            <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close") ?></button>
	          <?php else: ?>
	            <button type='submit' name="take_action" value="approve"><?php echo $this->translate('Approve'); ?></button>
              <button type='submit' name="take_action" value="decline"><?php echo $this->translate('Decline'); ?></button>
	            <?php echo $this->translate(" or ") ?>
	            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("cancel") ?></a>
	          <?php endif; ?>
	        </div>
	      </div>
	    </div>
	  </form>
	</div>
</div>