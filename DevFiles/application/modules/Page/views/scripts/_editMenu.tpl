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
  <div class="r">
    <h3><?php echo $this->page->getTitle(); ?></h3>
    <div class="pages_layoutbox_menu">
    <ul>
      <li id="pages_layoutbox_menu_createpage">
        <?php echo $this->htmlLink( $this->url(array(), 'page_create'), $this->translate('Create Page') ); ?>
       </li>
       <li id="pages_layoutbox_menu_viewpage">
        <?php echo $this->htmlLink( $this->url(array( 'page_id' => $this->page->url ), 'page_view'), $this->translate('View Page') ); ?>
       </li>
       <li id="pages_layoutbox_menu_deletepage">
        <?php echo $this->htmlLink( $this->url(array( 'action' => 'delete', 'page_id' => $this->page->page_id), 'page_team'), $this->translate('Delete Page') ); ?>
       </li>
       <li id="pages_layoutbox_menu_editpage">
        <?php echo $this->htmlLink( $this->url(array( 'action' => 'edit', 'page_id' => $this->page->page_id), 'page_team'), $this->translate('Back To Edit Page Dashboard') ); ?>
       </li>
    </ul>
    </div>
  </div>
  <div class="clr"></div>
</div>

<div class="clr"></div>