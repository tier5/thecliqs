<?php foreach ($this->paginator as $item): ?>
  <div class="ynfeed_popup_items ynfeed-clearfix">
    <a href="javascript:void(0);" id="contener_<?php echo $item->getType() ?>-<?php echo $item->getIdentity() ?>" class="" onclick="setContentInList(this,'<?php echo $item->getType()?>','<?php echo $item->getIdentity() ?>')">      
      <span> <?php echo $this->itemPhoto($item, 'thumb.icon', '', array('align' => 'left')); ?>
        <span></span>
      </span>    
      <p> <?php echo $item->getTitle() ?></p>
      <input type="hidden" id="<?php echo $item->getType() ?>-<?php echo $item->getIdentity() ?>" value="0" />
    </a>
  </div>
<?php endforeach; ?>

<?php if (empty($this->count)): ?>
	<div class="tip" style="margin:10px">
	  <span>
	    <?php echo $this->translate('No items were found matching this criteria.'); ?>
	  </span>
	</div>
<?php endif; ?>

<?php if (!empty($this->count)): ?>
<div id="view_more_sea" class="clr"  style="display:<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>">
  <div id="view_more_link" onclick="getNextPage()" class="ynfeed_item_list_popup_more">
  	<a href="javascript:void(0);" class="more_icon buttonlink"><?php echo $this->translate('More'); ?></a>
  </div>
  <div id="view_more_loding" style="display:none" class="ynfeed_item_list_popup_more">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Ynfeed/externals/images/loading.gif' />
  </div>
</div>
<?php endif; ?>
