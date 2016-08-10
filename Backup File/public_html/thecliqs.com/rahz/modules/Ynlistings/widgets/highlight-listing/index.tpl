<?php if ($this->listing) : ?>
    <div class="highlight_listing">
        <div class="listing_title">
            <?php echo $this->htmlLink($this->listing->getHref(), $this->listing->title); ?>
        </div>

        <div class="listing_photo">
            <?php echo $this->htmlLink($this->listing->getHref(), $this->itemPhoto($this->listing)); ?>

            <div class="prices">
                <?php echo $this -> locale()->toCurrency($this->listing->price, $this->listing->currency); ?>
            </div>

            <div class="listing_owner">
                <span><?php echo $this->translate('by').' '?></span>
                <span><?php echo $this->listing->getOwner()?></span>
            </div>
        </div>

        <div class="listing_owner_avatar"><?php echo $this->htmlLink($this->listing->getOwner(), $this->itemPhoto($this->listing->getOwner(), 'thumb.icon'))?></div>

        <div class="listing_rating">
            <?php 
                echo $this->partial('_listing_rating_big.tpl', 'ynlistings', array('listing' => $this->listing));
            ?>            
        </div>

        <div class="listing_category">            
            <span class="fa fa-folder-open-o"></span>
            <?php 
                $category = $this->listing->getCategory();
                if ($category) {
                    echo $this->htmlLink($category->getHref(), $category->title);
                }
            ?>
        </div>

        <?php if ($this->listing->location): ?>
            <div class="listing_location">
                <span class="fa fa-map-marker"></span>
                <?php echo $this->listing->location;?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>