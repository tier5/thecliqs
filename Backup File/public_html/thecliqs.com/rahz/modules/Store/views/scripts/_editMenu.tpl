<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _editMenu.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<div class="page_edit_title">
  <div class="l">
    <?php echo $this->htmlLink( $this->page->getHref(), $this->itemPhoto($this->page, 'thumb.icon') ); ?>
  </div>
  <div style="overflow: hidden;">
    <h3><?php echo $this->page->getTitle(); ?></h3>

    <div class="pages_layoutbox_menu" style="float: left; height: auto;">
      <ul>
        <li id="store_layoutbox_menu_products">
          <?php echo $this->htmlLink($this->url(array('page_id' => $this->page->page_id), 'store_products', true), $this->translate('STORE_Manage Products')); ?>
        </li>
        <?php if ($this->page->isOwner($this->viewer)) : ?>
          <li id="store_layoutbox_menu_transactions" style="float:left">
            <?php echo $this->htmlLink($this->url(array('controller' => 'transactions', 'page_id' => $this->page->page_id), 'store_extended', true),
            $this->translate('Transactions')); ?>
          </li>
          <li id="store_layoutbox_menu_request_money">
            <?php echo $this->htmlLink($this->url(array('controller' => 'requests', 'page_id' => $this->page->page_id), 'store_extended', true),
            $this->translate('Request Money')); ?>
          </li>
          <li id="pages_layoutbox_menu_editpage">
            <?php echo $this->htmlLink($this->url(array('action' => 'gateway', 'page_id' => $this->page->page_id), 'store_settings', true), $this->translate('Settings')); ?>
          </li>
          <li id="store_layoutbox_menu_statistics">
            <?php echo $this->htmlLink($this->url(array('page_id' => $this->page->page_id), 'store_statistics', true), $this->translate('Statistics')); ?>
          </li>
        <?php endif; ?>
      </ul>
    </div>

    <div class="pages_layoutbox_menu" style="float: right; height: auto;">
      <ul>
        <li id="pages_layoutbox_menu_viewpage">
          <?php echo $this->htmlLink($this->url(array('page_id' => $this->page->url), 'page_view', true), $this->translate('View Page')); ?>
        </li>
        <li id="pages_layoutbox_menu_todashboard">
          <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'page_id' => $this->page->page_id), 'page_team', true),
          $this->translate('Back to Dashboard')); ?>
        </li>
      </ul>
    </div>
  </div>
  <div class="clr"></div>
</div>

<div class="clr"></div>