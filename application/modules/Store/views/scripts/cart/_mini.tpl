<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: _pulldown.tpl  5/21/12 11:40 AM mt.uulu $
 * @author     Mirlan
 */
?>
<span onclick="mini_cart.toggle(event, this);" style="display: inline-block;" class="updates_pulldown store_mini_cart">
  <div class="pulldown_contents_wrapper">
    <div class="pulldown_contents">
      <ul class="cartitems_menu notifications_menu">
        <div class="cartitems_loading notifications_loading">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
          <?php echo $this->translate("Loading ...") ?>
        </div>
      </ul>
    </div>
    <div class="pulldown_options">
      <?php echo $this->htmlLink(array('route'=>'store_extended', 'controller'=>'cart'), $this->translate('View Cart'), array(
      'class'=>(($this->totalCount <=0 )?'hidden':''),
    )) ?>
    </div>
  </div>
  <a href="javascript:void(0);" class="label <?php if( $this->totalCount ):?> new_updates<?php endif;?>"><?php echo $this->translate('%s cart', $this->locale()->toNumber($this->totalCount)) ?></a>
</span>