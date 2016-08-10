<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _page_stat_tabs.tpl  14.11.11 12:50 TeaJay $
 * @author     Taalay
 */
?>

<div id="sideNav" class="page_edit_dashboard">
  <ul class="page_edit_tabs" role="navigation">
    <li class="sideNavItem <?php if ($this->action == 'visitors') echo 'selectedItem' ?>">
      <a class="item clearfix" href="<?php echo $this->url(array('action' => 'visitors', 'page_id' => $this->page->getIdentity()), 'page_stat', true)?>">
        <div>
          <span class="imgWrap">
            <i class="img view_stat_icon"></i>
          </span>
          <div class="linkWrap">
            <?php echo $this->translate('Visitors')?>
          </div>
        </div>
      </a>
    </li>
    <li class="sideNavItem <?php if ($this->action == 'views') echo 'selectedItem' ?>">
      <a class="item clearfix" href="<?php echo $this->url(array('action' => 'views', 'page_id' => $this->page->getIdentity()), 'page_stat', true)?>">
        <div>
          <span class="imgWrap">
            <i class="img view_stat_icon"></i>
          </span>
          <div class="linkWrap">
            <?php echo $this->translate('Views')?>
          </div>
        </div>
      </a>
    </li>
    <li class="sideNavItem <?php if ($this->action == 'map') echo 'selectedItem' ?>">
      <a class="item clearfix" href="<?php echo $this->url(array('action' => 'map', 'page_id' => $this->page->getIdentity()), 'page_stat', true)?>">
        <div>
          <span class="imgWrap">
            <i class="img view_stat_icon"></i>
          </span>
          <div class="linkWrap">
            <?php echo $this->translate('Map Overlay')?>
          </div>
        </div>
      </a>
    </li>
  </ul>
</div>