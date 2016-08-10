<?php
    $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/jquery-1.7.1.min.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/jquery.flexslider.js');
    $this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/styles/flexslider.css');
?>

<div id="ynbusinesspages-profile-related-listings" class="ynbusinesspages-profile-related-listings flexslider">
<ul class="slides">
<?php foreach ($this->paginator as $business):?>
    <li>
        <div class="ynbusinesspages-profile-related-item">
            <div class="ynbusinesspages-profile-related-item-header">
                <div class="ynbusinesspages-profile-related-item-title">
                    <?php echo $this->htmlLink($business->getHref(), $business->name); ?>
                </div>

                <?php $category = $business->getMainCategory();?>
                <?php if ($category):?>
                <div class="ynbusinesspages-profile-related-item-category">
                    <?php echo $this->htmlLink($category->getHref(), $category-> title);?>
                </div>
                <?php endif;?>
            </div>

            <div class="ynbusinesspages-profile-related-item-image">
                <div class="ynbusinesspages-profile-related-item-photo">
                   <?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($business); ?>
                </div>
            </div>
            <div class="ynbusinesspages-profile-related-item-content">
                <div class="ynbusinesspages-profile-related-item-info">
                    <?php echo Engine_Api::_()->ynbusinesspages()->renderBusinessRating($business->getIdentity(), false) . ' &nbsp;(' . $business->getReviewCount().')'; ?>
                </div>
                <div class="ynbusinesspages-profile-related-item-location">
                    <i class="fa fa-map-marker"></i>
                    <?php echo $business->getMainLocation();?>
                </div>                                      
            </div>
        </div>
    </li>
<?php endforeach;?>
</ul>
</div>

<script type="text/javascript">
    // Can also be used with $(document).ready()
    jQuery.noConflict();
    jQuery(window).load(function() {
        jQuery('#ynbusinesspages-profile-related-listings').flexslider({
            animation: "slide",
            controlNav: false,
            useCSS: false,
            prevText: "",
            nextText: "",
            itemWidth: 210,
            itemMargin: 0,
            minItems: 2, // use function to pull in initial value
            maxItems: 4 // use function to pull in initial value
        });
    });

</script>