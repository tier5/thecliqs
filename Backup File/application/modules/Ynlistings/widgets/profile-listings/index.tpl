<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_listings').getParent();
    $('profile_listings_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_listings_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_listings_previous').removeEvents('click').addEvent('click', function(){
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

    $('profile_listings_next').removeEvents('click').addEvent('click', function(){
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
    <?php endif; ?>
  });
</script>

<div id="profile_listings" class="listings_profile_tab">
<div class="content clearfix">
    <?php foreach ($this->paginator as $listing) : ?>
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
              <?php echo $this->htmlLink($listing->getHref(), $this->translate('View more ').'<span class="fa fa-arrow-right"></span>' );?>
            </div>
            
            <div class="short_description">
                <?php echo strip_tags($listing->short_description)?>
            </div>
            
            <div class="listing_creation">
              <span class="author-avatar"><?php echo $this->htmlLink($listing->getOwner(), $this->itemPhoto($listing->getOwner(), 'thumb.icon'))?></span>
              <span><?php echo $this->translate('by ')?></span>
              <span class="author-title"><?php echo $listing->getOwner()?></span>
            </div>
          </div>
        </div>
      </div> 
    <?php endforeach; ?>
    </div>
</div>

<div style="clear:both">
  <div id="profile_listings_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="profile_listings_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>