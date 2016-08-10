<script type="text/javascript">
    en4.core.runonce.add(function(){
        var anchor = $('ynbusinesspages_listing').getParent();
        $('ynbusinesspages_listing_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        $('ynbusinesspages_listing_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

        $('ynbusinesspages_listing_previous').removeEvents('click').addEvent('click', function(){
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

        $('ynbusinesspages_listing_next').removeEvents('click').addEvent('click', function(){
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

<div class="ynbusinesspages-profile-module-header">
    <!-- Menu Bar -->
    <div class="ynbusinesspages-profile-header-right">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <?php echo $this->htmlLink(array(
                'route' => 'ynbusinesspages_extended',
                'controller' => 'listings',
                'action' => 'list',
                'business_id' => $this->business->getIdentity(),
                'parent_type' => 'ynbusinesspages_business',
                 'tab' => $this->identity,
            ), '<i class="fa fa-list"></i>'.$this->translate('View all Listings'), array(
                'class' => 'buttonlink'
            ))
            ?>
        <?php endif; ?>

        <?php if ($this->canCreate):?>
            <?php echo $this->htmlLink(array(
                'route' => 'ynlistings_general',
                'controller' => 'index',
                'action' => 'create',
                'business_id' => $this->business->getIdentity(),
                'parent_type' => 'ynbusinesspages_business',
            ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Listing'), array(
                'class' => 'buttonlink'
            ))
            ?>
        <?php endif; ?>
    </div>      

    <div class="ynbusinesspages-profile-header-content">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): 
            $business = $this->business;?>
            <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span> 
            <?php echo $this-> translate(array("ynbusiness_listing", "Listings", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
        <?php endif; ?>
    </div>
</div>

<div class="ynbusinesspages_list ynlistings_grid-view" id="ynbusinesspages_listing">

    <!-- Content -->
    <?php if( $this->paginator->getTotalItemCount() > 0 ): 
    $business = $this->business;?>

    <ul class="ynbusinesspages_listing generic_list_widget listing_browse listing_browse_view_content ynlistings-tabs-content clearfix">  
        <?php foreach ($this->paginator as $listing): 
        	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($listing);?>
        <li>
            <div class="grid-view">
                <div class="ynlisting-grid-item">
                    <div class="ynlisting-grid-item-content">
                        <?php $photo_url = ($listing->getPhotoUrl('thumb.profile')) ? $listing->getPhotoUrl('thumb.profile') : "application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png";?>
                        <div class="item-background" style="background-image: url(<?php echo $photo_url; ?>);">

                            <?php if ($listing->featured) : ?>
                                <div class="featureListing"></div>
                            <?php endif; ?>

                            <?php if ($listing->isNew()) : ?>
                                <div class="newListing"></div>
                            <?php endif; ?>

                            <div class="ynlisting-item-rating">
                                <?php echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $listing)); ?>
                            </div>
                        </div>
                        <div class="item-front-info">
                            <div class="listing_title">
                                <?php echo $this->htmlLink($listing->getHref(), $listing->title);?>
                            </div>    

                            <div class="listing_price">
                                <?php echo $this -> locale()->toCurrency($listing->price, $listing->currency)?>
                            </div>
                        </div>
                    </div>
                    <div class="ynlisting-grid-item-hover">
                        <div class="ynlisting-grid-item-hover-background">
                            <div class="listing_view_more"> 
                                <?php echo $this->htmlLink($listing->getHref(), $this->translate('View more <span class="fa fa-arrow-right"></span> ') );?>
                            </div>

                            <div class="short_description">
                                <?php echo strip_tags($listing->short_description)?>
                            </div>

                            <div class="listing_creation">
                                <span class="author-avatar"><?php echo $this->htmlLink($owner, $this->itemPhoto($owner, 'thumb.icon'))?></span>
                                <span><?php echo $this->translate('by ')?></span>
                                <span class="author-title"><?php echo $owner?></span>
                            </div>
                        </div>
                    </div>
                </div>            
            </div> 
        </li> 
        <?php endforeach; ?>             
    </ul>  
    
    <div class="ynbusinesspages-paginator">
        <div id="ynbusinesspages_listing_previous" class="paginator_previous">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => '',
              'class' => 'buttonlink icon_previous'
            )); ?>
        </div>
        <div id="ynbusinesspages_listing_next" class="paginator_next">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
              'onclick' => '',
              'class' => 'buttonlink_right icon_next'
            )); ?>
        </div>
    </div>
    
    <?php else: ?>
    <div class="tip">
        <span>
             <?php echo $this->translate('No listings have been created.');?>
        </span>
    </div>
    <?php endif; ?>
</div>