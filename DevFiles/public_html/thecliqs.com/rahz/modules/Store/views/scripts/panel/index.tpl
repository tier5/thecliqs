<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  4/17/12 3:15 PM mt.uulu $
 * @author     Mirlan
 */
?>

<?php echo $this->content()->renderWidget('store.navigation-tabs'); ?>

<div class="layout_left">
  <div id='panel_options'>
    <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->setPartial(array('_navIcons.tpl', 'core'))
      ->render()
    ?>
  </div>
</div>

<div class="layout_middle">
  <h3>
    <?php echo $this->translate('My Stores'); ?>
  </h3>
  <?php if (count($this->paginator) == 0): ?>
    <div class="tip">
      <span><?php echo $this->translate("There are no stores."); ?> <?php echo $this->htmlLink($this->url(array(), 'page_create'), $this->translate('STORE_Create Store')); ?>.</span>
    </div>
  <?php endif; ?>
  <ul class="page_list_items" id="page_list_cont">
    <?php foreach ($this->paginator as $page): ?>
      <li class="<?php if ($page->sponsored) echo 'page_list_item_sponsored'?>">
        <div class="page_list_item_photo  <?php if ($page->featured) echo 'featured_page'?>">
          <a href="<?php echo $page->getHref()?>">
            <?php echo $this->itemPhoto($page, 'thumb.normal')?>
          </a>

          <?php if($page->featured):?>
            <div class="page_featured">
              <span><?php echo $this->translate('Featured') ?></span>
            </div>
          <?php endif;?>
        </div>

        <div class="page_list_item_info">
          <?php if( $page->sponsored ) :?>
            <div class="sponsored_page"><?php echo $this->translate('Sponsored')?></div>
          <?php endif;?>
          <div class="page_list_title">
            <a href="<?php echo $page->getHref(); ?>"><?php echo $page->title; ?></a><?php echo ' (' . $this->translate(
                array('%s product', '%s products', $this->products[$page->getIdentity()]),
                $this->locale()->toNumber($this->products[$page->getIdentity()])
              ) . ')'; ?>
          </div>
          <div class="page_list_info">
            <div class="page_list_desc"><?php echo $page->getDescription(true, true, false, 300); ?></div>
            <div class="clr"></div>

            <div class="store_layoutbox_menu">
              <ul>
                <li id="store_layoutbox_menu_products">
                  <?php echo $this->htmlLink($this->url(array('page_id' => $page->page_id), 'store_products'), $this->translate('STORE_Manage Products'), array('class' => 'buttonlink')); ?>
                </li>
                <?php if ($page->isOwner($this->viewer)) : ?>
                  <li id="store_layoutbox_menu_transactions">
                    <?php echo $this->htmlLink($this->url(array('controller' => 'transactions', 'page_id' => $page->page_id), 'store_extended'), $this->translate('Transactions'), array('class' => 'buttonlink')); ?>
                  </li>
                  <li id="store_layoutbox_menu_request_money">
                    <?php echo $this->htmlLink($this->url(array('controller' => 'requests', 'page_id' => $page->page_id), 'store_extended'), $this->translate('Request Money'), array('class' => 'buttonlink')); ?>
                  </li>
                  <li id="pages_layoutbox_menu_editpage">
                    <?php echo $this->htmlLink($this->url(array('action' => 'gateway', 'page_id' => $page->page_id), 'store_settings'), $this->translate('Settings'), array('class' => 'buttonlink')); ?>
                  </li>
                  <li id="store_layoutbox_menu_statistics">
                    <?php echo $this->htmlLink($this->url(array('page_id' => $page->page_id), 'store_statistics'), $this->translate('Statistics'), array('class' => 'buttonlink')); ?>
                  </li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php if ($this->paginator->count() > 1): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>
</div>