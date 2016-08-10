<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 10.09.12
 * Time: 13:35
 * To change this template use File | Settings | File Templates.
 */
?>

<div class="page_edit_title">
  <div class="l">
    <?php echo $this->htmlLink( $this->subject->getHref(), $this->itemPhoto($this->subject, 'thumb.icon') ); ?>
  </div>
  <div style="overflow: hidden;">
    <h3><?php echo $this->subject->getTitle(); ?></h3>

    <div class="pages_layoutbox_menu" style="float: left; height: auto;">
      <ul>
        <li id="donation_layoutbox_menu_donations">
          <?php echo $this->htmlLink($this->url(array('controller' =>'page', 'action' => 'index', 'page_id' => $this->subject->page_id), 'donation_extended', true), $this->translate('DONATION_Manage Donations')); ?>
        </li>
      </ul>
    </div>

    <div class="pages_layoutbox_menu" style="float: right; height: auto;">
      <ul>
        <li id="pages_layoutbox_menu_viewpage">
          <?php echo $this->htmlLink($this->url(array('page_id' => $this->subject->url), 'page_view', true), $this->translate('View Page')); ?>
        </li>
        <li id="pages_layoutbox_menu_todashboard">
          <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'page_id' => $this->subject->page_id), 'page_team', true),
          $this->translate('Back to Dashboard')); ?>
        </li>
      </ul>
    </div>
  </div>
  <div class="clr"></div>
</div>

<div class="clr"></div>