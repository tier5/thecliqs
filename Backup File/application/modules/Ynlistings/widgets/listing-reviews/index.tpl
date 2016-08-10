<?php if (!$this->listing->isOwner($this->viewer)) : ?>
<?php if (!$this->my_review && $this->can_rate) : ?>
    <div id="add_review">
    <?php echo $this->htmlLink(
        array(
            'route' => 'ynlistings_review',
            'action' => 'create',
            'listing_id' => $this->listing->getIdentity(),
            'tab' => $this->identity,
            'page' => $this->page
        ),
        $this->translate('Write a Review'),
        array(
            'class' => 'smoothbox buttonlink icon_reviews_add'
        )
    )?>
    </div>
<?php else :?>
<?php if ($this->my_review) : ?>
    <div id="delete_review">
    <?php echo $this->htmlLink(
        array(
            'route' => 'ynlistings_review',
            'action' => 'delete',
            'id' => $this->my_review->getIdentity(),
            'tab' => $this->identity,
            'page' => $this->page
        ),
        $this->translate('Delete Review'),
        array(
            'class' => 'smoothbox buttonlink icon_listings_delete',
        )
    )?>
    </div>
    <div id="edit_review">
    <?php echo $this->htmlLink(
        array(
            'route' => 'ynlistings_review',
            'action' => 'edit',
            'id' => $this->my_review->getIdentity(),
            'tab' => $this->identity,
            'page' => $this->page
        ),
        $this->translate('Edit Review'),
        array(
            'class' => 'smoothbox buttonlink icon_listings_edit'
        )
    )?>
    </div>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
<ul id="review_table">

    <?php if (!$this->listing->isOwner($this->viewer) && $this->my_review) : ?>
    <li id="my_review">
        <div class="user_name">
            <?php echo $this->htmlLink($this->viewer->getHref(), $this->itemPhoto($this->viewer, 'thumb.icon'))?>
            <?php echo $this->viewer?>
        </div>
        <div class="review_detail">
            <div>
                <span><?php echo $this->partial('_review_rating_big.tpl', 'ynlistings', array('review' => $this->my_review));?></span>
                <span class="small_description">
                <?php 
                    $modified_date = new Zend_Date(strtotime($this->my_review->modified_date));
                    $modified_date->setTimezone($this->timezone);
                    echo $this->timestamp($modified_date);
                ?>
                </span>
                <span class="small_description"><?php echo $this->translate('(My Review)')?></span>
            </div>
            <div><?php echo $this->my_review->body?></div>
        </div>
    </li>
    <?php endif; ?>

    <?php foreach ($this->paginator as $review) : ?>
    <li>
        <div class="user_name">
            <?php echo $this->htmlLink($review->getOwner(), $this->itemPhoto($review->getOwner(), 'thumb.icon'))?>
            <?php echo $review->getOwner()?>
        </div>
        <div class="review_detail">
            <div class="option_div">
                <span>
                <?php if ($review->isDeletable()) : ?>
                    <?php echo $this->htmlLink(
                        array(
                            'route' => 'ynlistings_review',
                            'action' => 'delete',
                            'id' => $review->getIdentity(),
                            'tab' => $this->identity,
                            'page' => $this->page
                        ),
                        $this->translate('delete'),
                        array(
                            'class' => 'smoothbox'
                        )
                    ); ?>
                    
                    <?php if ($review->isEditable()) : ?>
                    <span>|</span>
                    <?php endif; ?>
                <?php endif; ?>
                </span>
                <span>
                <?php
                    if ($review->isEditable()) {
                        echo $this->htmlLink(
                            array(
                                'route' => 'ynlistings_review',
                                'action' => 'edit',
                                'id' => $review->getIdentity(),
                                'tab' => $this->identity,
                                'page' => $this->page
                            ),
                            $this->translate('edit'),
                            array(
                                'class' => 'smoothbox'
                            )
                        );
                    }
                ?>
                </span>
            </div>
            <div>
                <span><?php echo $this->partial('_review_rating_big.tpl', 'ynlistings', array('review' => $review));?></span>
                <span class="small_description">
                <?php 
                    $modified_date = new Zend_Date(strtotime($review->modified_date));
                    $modified_date->setTimezone($this->timezone);
                    echo $this->timestamp($modified_date);
                ?>
                </span>
            </div>
            <div><p><?php echo $review->body?></p></div>   
        </div>
    </li>
    <?php endforeach; ?>

</ul>
<?php $formValues = array('tab' => $this->identity)?>
<?php if( count($this->paginator) > 1 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, array(
        'paginator.tpl',
        'ynlistings',
    ), array(
        'pageAsQuery' => true,
        'query' => $formValues,
    )); ?>
<?php endif; ?>

<?php if (!$this->my_review && count($this->paginator) == 0) : ?>
<div class="tip">
    <span><?php echo $this->translate('No reviews have been posted in this listing yet.')?></span>
</div>
<?php endif;?>