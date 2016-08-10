<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 8221 2011-07-25 00:24:02Z taalay $
 * @author     Taalay (TJ)
 */
?>

<script type="text/javascript">
	en4.core.runonce.add(function(){
		var miniTipsOptions1 = {
			'htmlElement': '.he-hint-text',
			'delay': 1,
			'className': 'he-tip-mini',
			'id': 'he-mini-tool-tip-id',
			'ajax': false,
			'visibleOnHover': false
		};

		var internalTips1 = new HETips($$('.he-hint-tip-links'), miniTipsOptions1);
	});
</script>
<div class="headline">
  <h2>
    <?php echo $this->translate('HE_Pages');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
  <div class="tabs">
    <?php
    // Render the menu
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->render();
    ?>
  </div>
  <?php endif; ?>
</div>

<div class="layout_middle page_subscription_wrapper">

  <?php if( $this->page ) :?>
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
          <?php if ($this->page->isEnabled()): ?>
          <li id="pages_layoutbox_menu_editpage">
            <?php echo $this->htmlLink( $this->url(array( 'action' => 'edit', 'page_id' => $this->page->page_id), 'page_team'), $this->translate('Back To Edit Page Dashboard') ); ?>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="clr"></div>
  </div>
  <?php endif;?>

<?php if( count($this->payed_packages) ): ?>
  <div class="package-list-layout">
    <div>
      <div class="package-list-description">
        <h3>
          <?php echo $this->translate('PAGE_Subscribe Paid Page'); ?>
        </h3>

        <p>
          <?php echo $this->translate('PAGE_PACKAGE_CHOOSE_PAID'); ?>
        </p>
      </div>

      <?php foreach($this->payed_packages as $package) : ?>

      <div class="package_list">
        <div>
          <div class="package_title"><?php echo $package->getTitle();?></div>

          <div class="package_info">
            <div>
              <b><?php echo $this->translate('Price');?>:</b>
              <div style="color: #FF0000"><?php echo $this->locale()->toCurrency($package->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'))?></div>
            </div>

            <div>
              <b><?php echo $this->translate('PAGE_Auto Approved'); ?>:</b>
              <div class="<?php if($package->autoapprove) echo 'he-hint-tip-links'?>"><?php echo ($package->autoapprove) ? $this->translate('Yes') : $this->translate('No'); ?></div>
              <?php if($package->autoapprove) : ?>
                <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Auto Approved_DESC')?></div>
              <?php endif;?>
            </div>

            <div>
              <b><?php echo $this->translate('PAGE_Sponsored'); ?>:</b>
              <div class="<?php if($package->sponsored) echo 'he-hint-tip-links'?>"><?php echo ($package->sponsored) ? $this->translate('Yes') : $this->translate('No'); ?></div>
              <?php if($package->sponsored) : ?>
              <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Sponsored_DESC')?></div>
              <?php endif;?>
            </div>

            <div>
              <b><?php echo $this->translate('PAGE_Featured'); ?>:</b>
              <div class="<?php if($package->featured) echo 'he-hint-tip-links'?>"><?php echo ($package->featured) ? $this->translate('Yes') : $this->translate('No'); ?></div>
              <?php if($package->featured) : ?>
              <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Featured_DESC')?></div>
              <?php endif;?>
            </div>

            <div>
              <b><?php echo $this->translate('Billing'); ?>:</b>
              <div class=""><?php echo $package->getPackageDescription(); ?></div>
            </div>

            <div>
              <b><?php echo $this->translate('PAGE_Column Change'); ?>:</b>
              <div class="<?php if($package->edit_columns) echo 'he-hint-tip-links'?>"><?php echo ($package->edit_columns) ? $this->translate('Yes') : $this->translate('No'); ?></div>
              <?php if( $package->edit_columns ) : ?>
              <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Column Change_DESC')?></div>
              <?php endif;?>
            </div>

            <div>
              <b><?php echo $this->translate('PAGE_Layout Editor'); ?>:</b>
              <div class="<?php if($package->edit_layout) echo 'he-hint-tip-links'?>"><?php echo ($package->edit_layout) ? $this->translate('Yes') : $this->translate('No'); ?></div>
              <?php if( $package->edit_layout ) : ?>
              <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Layout Editor_DESC')?></div>
              <?php endif;?>
            </div>

            <?php foreach($this->available_modules as $key => $module):?>
            <div>
              <b><?php echo $this->translate($module); ?>:</b>
              <div class="<?php if(in_array($key,is_array($package->modules) ? $package->modules : array())) echo 'he-hint-tip-links'?>"><?php echo (in_array($key,is_array($package->modules) ? $package->modules : array())) ? $this->translate('Yes') : $this->translate('No'); ?></div>
              <?php if(in_array($key,is_array($package->modules) ? $package->modules : array())) : ?>
              <div class="he-hint-text hidden"><?php echo $this->translate($module . '_DESC')?></div>
              <?php endif;?>
            </div>
            <?php endforeach;?>

          </div>

          <div class="package_description"> <?php echo $package->description; ?> </div>
          <?php if( $this->page ) :?>
          <form action="<?php echo $this->url(); ?>" method="post">
              <?php echo $this->translate('or')?>
              <input type="hidden" name="package_id" value="<?php echo $package->getIdentity(); ?>"/>
              <input type="hidden" name="subscription_id" value="<?php echo $package->subscription_id?>">
              <input type="hidden" name="is_active" value="1">
              <button type="submit"><?php echo $this->translate('Change Package') .'&nbsp;&raquo';?> </button>
          </form>
          <?php endif;?>
          <a class="create_button" href="<?php echo $this->url(array('id'=>$package->subscription_id), 'page_create')?>">
            <button><?php echo $this->translate('Create New Page') . '&nbsp;&raquo'?></button>
          </a>
        </div>
      </div>
      <?php endforeach;?>

    </div>
  </div>
<?php endif;?>

<?php if(count($this->packages)) : ?>
<div class="package-list-layout">
	<div>
		<div class="package-list-description">
			<h3>
			<?php echo $this->translate('PAGE_Subscribe Page'); ?>
			</h3>
			
			<p>
				<?php echo $this->translate('PAGE_VIEWS_SCRIPTS_PACKAGE_CHOOSE_DESCRIPTION'); ?>
			</p>
		</div>

    <?php foreach($this->packages as $package) : ?>
    <div class="package_list">
      <div>
      <div class="package_title"><?php echo $package->getTitle();?></div>

      <div class="package_info">
        <div>
          <b><?php echo $this->translate('Price');?>:</b>
          <div style="color: #FF0000"><?php echo $this->locale()->toCurrency($package->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'))?></div>
        </div>

        <div>
          <b><?php echo $this->translate('PAGE_Auto Approved'); ?>:</b>
          <div class="<?php if($package->autoapprove) echo 'he-hint-tip-links'?>"><?php echo ($package->autoapprove) ? $this->translate('Yes') : $this->translate('No'); ?></div>
          <?php if($package->autoapprove) : ?>
          <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Auto Approved_DESC')?></div>
          <?php endif;?>
        </div>

        <div>
          <b><?php echo $this->translate('PAGE_Sponsored'); ?>:</b>
          <div class="<?php if($package->sponsored) echo 'he-hint-tip-links'?>"><?php echo ($package->sponsored) ? $this->translate('Yes') : $this->translate('No'); ?></div>
          <?php if($package->sponsored) : ?>
          <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Sponsored_DESC')?></div>
          <?php endif;?>
        </div>

        <div>
          <b><?php echo $this->translate('PAGE_Featured'); ?>:</b>
          <div class="<?php if($package->featured) echo 'he-hint-tip-links'?>"><?php echo ($package->featured) ? $this->translate('Yes') : $this->translate('No'); ?></div>
          <?php if($package->featured) : ?>
          <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Featured_DESC')?></div>
          <?php endif;?>
        </div>

        <div>
          <b><?php echo $this->translate('Billing'); ?>:</b>
          <div class=""><?php echo $package->getPackageDescription(); ?></div>
        </div>

        <div>
          <b><?php echo $this->translate('PAGE_Column Change'); ?>:</b>
          <div class="<?php if($package->edit_columns) echo 'he-hint-tip-links'?>"><?php echo ($package->edit_columns) ? $this->translate('Yes') : $this->translate('No'); ?></div>
          <?php if( $package->edit_columns ) : ?>
          <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Column Change_DESC')?></div>
          <?php endif;?>
        </div>

        <div>
          <b><?php echo $this->translate('PAGE_Layout Editor'); ?>:</b>
          <div class="<?php if($package->edit_layout) echo 'he-hint-tip-links'?>"><?php echo ($package->edit_layout) ? $this->translate('Yes') : $this->translate('No'); ?></div>
          <?php if( $package->edit_layout ) : ?>
          <div class="he-hint-text hidden"><?php echo $this->translate('PAGE_Layout Editor_DESC')?></div>
          <?php endif;?>
        </div>

        <?php foreach($this->available_modules as $key => $module):?>
        <div>
          <b><?php echo $this->translate($module); ?>:</b>
          <div class="<?php if(in_array($key,is_array($package->modules) ? $package->modules : array())) echo 'he-hint-tip-links'?>"><?php echo (in_array($key,is_array($package->modules) ? $package->modules : array())) ? $this->translate('Yes') : $this->translate('No'); ?></div>
          <?php if(in_array($key,is_array($package->modules) ? $package->modules : array())) : ?>
          <div class="he-hint-text hidden"><?php echo $this->translate($module . '_DESC')?></div>
          <?php endif;?>
        </div>
        <?php endforeach;?>

      </div>

      <div class="package_description"> <?php echo $package->description; ?> </div>
        <form action="<?php echo $this->url(); ?>" method="post">
          <input type="hidden" name="package_id" value="<?php echo $package->getIdentity(); ?>"/>
          <button type="submit"><?php echo $this->translate('Continue') .'&nbsp;&raquo';?> </button>
        </form>
      </div>
    </div>
    <?php endforeach;?>

	</div>
</div>
</div>
<?php endif;?>