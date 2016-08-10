<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _page_optoins_menu.tpl  11.11.11 16:44 TeaJay $
 * @author     Taalay
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
        <?php if( $this->controller == 'statistics' ) echo $this->htmlLink( $this->url(array('action' => 'edit', 'page_id' => $this->page->getIdentity()), 'page_team', true), $this->translate('Back to Dashboard')); ?>
      </li>
	  </ul>
	  </div>
  </div>
  <div class="clr"></div>
  <?php if( $this->packageEnabled && $this->isDefaultPackage) :?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Your pages package is default package. Please change to another package to get more options');?>
    </span>
  </div>
  <?php endif;?>
</div>
<div class="clr"></div>
