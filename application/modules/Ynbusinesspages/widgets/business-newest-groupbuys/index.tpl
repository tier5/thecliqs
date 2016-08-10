<!-- Content -->
<?php if( $this->paginator->getTotalItemCount() > 0 ): 
$business = $this->business;?>
<ul class="ynbusinesspages_groupbuy groupbuy_list">           
    <?php foreach ($this->paginator as $groupbuy): 
    	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($groupbuy);?>
    <li>
        <div class="groupbuy_browse_photo">
            <a href="<?php echo $groupbuy->getHref();?>">
                <?php   echo $groupbuy->getImageHtml('deal_thumb_medium','thumb.normal1',339,195,'') ?>
            </a>            
        </div>
        <div class="groupbuy_widget_value">
            <div class="groupbuy_widget_value_value"> 
            <?php echo $this->currencyadvgroup($groupbuy->value_deal, $groupbuy->currency)," ",$this->translate("Value") ?>  
            </div>
            <div class="groupbuy_widget_value_price"> 
                <span class="groupbuy_widget_value_price_price"><?php echo $this->currencyadvgroup($groupbuy->price, $groupbuy->currency) ?></span> 
            </div>
        </div>
        <div class='groupbuy_browse_info'>
            <p class='groupbuy_browse_info_title'>
            <?php echo $this->htmlLink($groupbuy->getHref(), $groupbuy->title) ?>
            </p>
            <p class='groupbuy_browse_info_date'>
            <?php echo $this->timestamp($groupbuy->creation_date) ?>
            <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle()) ?>
            </p>
            <div class='groupbuy_browse_info_blurb'>
            <?php if ($groupbuy->location_title) { ?>
                <span>
                <?php echo $groupbuy->location_title?>
                </span>
                <span>
                <?php echo $this->translate("-"); ?>
                </span>
                <span>
                <?php echo $this->number($groupbuy->current_sold) ?>    
                </span>
                <span> 
                <?php echo $this->translate("Bought");?>
                </span>
            <?php } else { ?>
                <span>
                <?php echo $this->number($groupbuy->current_sold) ?>    
                </span>
                <span> 
                <?php echo $this->translate("Bought");?>
                </span>
            <?php } ?>
            </div>
        </div>
    </li>       
    <?php endforeach; ?>             
</ul>  
<?php endif; ?>