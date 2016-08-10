<?php
    $this->headScript()-> appendScript('jQuery.noConflict();');
?>
<script type="text/javascript">
    en4.core.runonce.add(function(){
        var anchor = $('ynbusinesspages_groupbuy').getParent();
        $('ynbusinesspages_groupbuy_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        $('ynbusinesspages_groupbuy_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

        $('ynbusinesspages_groupbuy_previous').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                }
            }), {
                'element' : anchor
            })
        });

        $('ynbusinesspages_groupbuy_next').removeEvents('click').addEvent('click', function(){
            en4.core.request.send(new Request.HTML({
                url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                data : {
                    format : 'html',
                    subject : en4.core.subject.guid,
                    page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                }
            }), {
                'element' : anchor
            })
        });
    });
</script>

<div class="ynbusinesspages_list" id="ynbusinesspages_groupbuy">
    <div class="ynbusinesspages-profile-module-header">
        <!-- Menu Bar -->
        <div class="ynbusinesspages-profile-header-right">
            <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
                <?php echo $this->htmlLink(array(
                    'route' => 'ynbusinesspages_extended',
                    'controller' => 'groupbuy',
                    'action' => 'list',
                    'business_id' => $this->business->getIdentity(),
                    'parent_type' => 'ynbusinesspages_business',
                     'tab' => $this->identity,
                ), '<i class="fa fa-list"></i>'.$this->translate('View all Deals'), array(
                    'class' => 'buttonlink'
                ))
                ?>
            <?php endif; ?>

            <?php if ($this->canCreate):?>
                <?php echo $this->htmlLink(array(
                    'route' => 'groupbuy_general',
                    'controller' => 'index',
                    'action' => 'create',
                    'business_id' => $this->business->getIdentity(),
                    'parent_type' => 'ynbusinesspages_business',
                ), '<i class="fa fa-plus-square"></i>'.$this->translate('Post A New Deal'), array(
                    'class' => 'buttonlink'
                ))
                ?>
            <?php endif; ?>
        </div>      

        <div class="ynbusinesspages-profile-header-content">
            <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
                <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
                <?php echo $this-> translate(array("ynbusiness_deal", "Deals", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
            <?php endif; ?>
        </div>
    </div>

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
    
    <div class="ynbusinesspages-paginator">
        <div id="ynbusinesspages_groupbuy_previous" class="paginator_previous">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => '',
              'class' => 'buttonlink icon_previous'
            )); ?>
        </div>
        <div id="ynbusinesspages_groupbuy_next" class="paginator_next">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
              'onclick' => '',
              'class' => 'buttonlink_right icon_next'
            )); ?>
        </div>
    </div>
    
    <?php else: ?>
    <div class="tip">
        <span>
             <?php echo $this->translate('No deals have been created.');?>
        </span>
    </div>
    <?php endif; ?>
</div>