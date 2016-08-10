<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _appMenu.tpl  5/17/12 2:37 PM mt.uulu $
 * @author     Mirlan
 */
?>

<div class="page_edit_store_products">
  <div class="global_form">
    <div>
      <div>
        <h3><?php echo $this->translate("Store"); ?></h3>

        <p class="form-description"><?php echo $this->translate("PAGE_Store app description"); ?></p>

        <div class="manage_store_products">
          <ul class="page_edit_lists">
            <li>
              <div class="manage_store_icon">
                <?php echo $this->htmlLink($this->url(array('page_id'=> $this->page->getIdentity()), 'store_products'), '<img src="application/modules/Page/externals/images/store_manage.png"/>'); ?>
              </div>
              <div class="manage_store_desc">
                <b><?php echo $this->htmlLink($this->url(array('page_id'=> $this->page->getIdentity()), 'store_products'), $this->translate('STORE_Manage Products')); ?></b>

                <p><?php echo $this->translate('STORE_Manage your products - here you can add new products, edit and delete existing ones etc.'); ?></p>
              </div>
            </li>
            <?php if ($this->page->isOwner($this->viewer)) : ?>
              <li>
                <div class="manage_store_icon">
                  <?php echo $this->htmlLink($this->url(array(
                    'controller' => 'transactions',
                    'page_id'    => $this->page->getIdentity()),
                  'store_extended'), '<img src="application/modules/Store/externals/images/store_transactions.png"/>'); ?>
                </div>
                <div class="manage_store_desc">
                  <b><?php echo $this->htmlLink($this->url(array(
                      'controller' => 'transactions',
                      'page_id'    => $this->page->getIdentity()),
                    'store_extended'), $this->translate('Transactions')); ?></b>

                  <p><?php echo $this->translate('STORE_Manage your transactions - here you can check your transactions and requests.'); ?></p>
                </div>
              </li>

              <li>
                <div class="manage_store_icon">
                  <?php echo $this->htmlLink($this->url(array(
                    'controller' => 'requests',
                    'page_id'    => $this->page->getIdentity()),
                  'store_extended'), '<img src="application/modules/Store/externals/images/store_account_balance.png"/>'); ?>
                </div>
                <div class="manage_store_desc">
                  <b><?php echo $this->htmlLink($this->url(array(
                      'controller' => 'requests',
                      'page_id'    => $this->page->getIdentity()),
                    'store_extended'), $this->translate('STORE_Account Balance')); ?></b>

                  <p><?php echo $this->translate('STORE_Account Balance Description'); ?></p>
                </div>
              </li>

              <li>
                <div class="manage_store_icon">
                  <?php echo $this->htmlLink($this->url(array('action' => 'gateway', 'page_id'=> $this->page->getIdentity()), 'store_settings'), '<img src="application/modules/Store/externals/images/store_settings.png"/>'); ?>
                </div>
                <div class="manage_store_desc">
                  <b><?php echo $this->htmlLink($this->url(array('action' => 'gateway', 'page_id'=> $this->page->getIdentity()), 'store_settings'), $this->translate('Settings')); ?></b>

                  <p><?php echo $this->translate('STORE_Edit your Store Settings - enable your api credentials to be able to sell your products etc.'); ?></p>
                </div>
              </li>
              <li>
                <div class="manage_store_icon">
                  <?php echo $this->htmlLink($this->url(array('page_id'=> $this->page->getIdentity()), 'store_statistics'), '<img src="application/modules/Page/externals/images/statistics_big.png"/>'); ?>
                </div>
                <div class="manage_store_desc">
                  <b><?php echo $this->htmlLink($this->url(array('page_id'=> $this->page->getIdentity()), 'store_statistics'), $this->translate('Statistics')); ?></b>

                  <p><?php echo $this->translate('STORE_View your Store Statistics - check out the transactions to your store, view your products statistics etc.'); ?></p>
                </div>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>