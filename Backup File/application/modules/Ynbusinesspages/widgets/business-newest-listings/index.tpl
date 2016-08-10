
<div class="ynlistings_grid-view">
    <div class="">
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
                                    <span class="author-title"><?php echo $listing->getOwner()?></span>
                                </div>
                            </div>
                        </div>
                    </div>            
                </div> 
            </li> 
            <?php endforeach; ?>             
        </ul>  
        <?php endif; ?>
    </div>
</div>
  