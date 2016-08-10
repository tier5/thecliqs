<script type="text/javascript">
  en4.core.runonce.add(function(){
    <?php if( !$this->renderOne ): ?>
    var anchor = $('ynbusinesspages_profile_reviews').getParent();
    $('ynbusinesspages_profile_reviews_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('ynbusinesspages_profile_reviews_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('ynbusinesspages_profile_reviews_previous').removeEvents('click').addEvent('click', function(){
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

    $('ynbusinesspages_profile_reviews_next').removeEvents('click').addEvent('click', function(){
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

<div class="ynbusinesspages-profile-module-header">
<?php if (!$this->my_review && ($this -> viewer -> getIdentity() > 0)) : ?>
    <div class="ynbusinesspages-profile-header-right">
    <?php if ($this->can_review): ?>
    <div id="add_review">
    <?php echo $this->htmlLink(
        array(
            'route' => 'ynbusinesspages_review',
            'action' => 'create',
            'business_id' => $this->business->getIdentity(),
            'tab' => $this->identity,
            'page' => $this->page
        ),'<i class="fa fa-plus-square"></i>'.$this->translate('Write a Review'),
        array(
            'class' => 'smoothbox buttonlink'
        )
    )?>
    </div>
    <?php endif; ?>
    </div>
<?php endif; ?>
<div class="ynbusinesspages-profile-header-content">
    <?php 
    $total = $this->paginator->getTotalItemCount();
    if($this->my_review)
    {
        $total += 1;
    }
    if( $total > 0 ):?> 
        <span class="ynbusinesspages-numeric"><?php echo $total; ?></span> 
        <?php echo ucfirst($this-> translate(array("ynbusiness_review", "reviews", $total), $total));?>
    <?php endif; ?>
</div>
</div>

<ul id="ynbusinesspages_profile_reviews">	
    <?php if ($this->my_review) : 
    	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($this->my_review);?>
        <li id="my_review" class="my-review">
            <?php if ($this->my_review && ($this -> viewer -> getIdentity() > 0)) : ?>
                <div class="option_div">
                    <?php echo $this->htmlLink(
                            array(
                                'route' => 'ynbusinesspages_review',
                                'action' => 'delete',
                                'id' => $this->my_review->getIdentity(),
                                'tab' => $this->identity,
                                'page' => $this->page
                            ),
                            $this->translate('<i class="fa fa-trash-o"></i>'),
                            array(
                                'class' => 'smoothbox',
                            )
                        )?>

                        <?php echo $this->htmlLink(
                            array(
                                'route' => 'ynbusinesspages_review',
                                'action' => 'edit',
                                'id' => $this->my_review->getIdentity(),
                                'tab' => $this->identity,
                                'page' => $this->page
                            ),
                            $this->translate('<i class="fa fa-pencil-square-o"></i>'),
                            array(
                                'class' => 'smoothbox'
                            )
                        )?>
                </div>
            <?php endif; ?>               
            <div class="user_name">
                <?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'))?>
                <div>
                    <div class="review-title"><?php echo $this->my_review -> title?></div>
                    <div class="review-rating">
                        <span><?php echo $this->partial('_review_rating_big.tpl', 'ynbusinesspages', array('review' => $this->my_review));?></span>
                        <span class="small_description">
                        <?php 
                            $modified_date = new Zend_Date(strtotime($this->my_review->modified_date));
                            $modified_date->setTimezone($this->timezone);
                            echo $this->timestamp($modified_date);
                        ?>
                        </span>
                        <span class="small_description"><?php echo $this->translate('(My Review)')?></span>
                    </div>
                </div>
            </div>
            <div class="review_detail"> 
                
                <div><?php echo $this -> viewMore($this->my_review->body)?></div>
            </div>
        </li>
    <?php endif; ?>
    <?php foreach ($this->paginator as $review) : 
    	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($review);?>
        <li>
            <div class="option_div">
                <?php if ($review->isDeletable()) : ?>
                    <?php echo $this->htmlLink(
                        array(
                            'route' => 'ynbusinesspages_review',
                            'action' => 'delete',
                            'id' => $review->getIdentity(),
                            'tab' => $this->identity,
                            'page' => $this->page
                        ),
                        $this->translate('<i class="fa fa-trash-o"></i>'),
                        array(
                            'class' => 'smoothbox'
                        )
                    ); ?>
                <?php endif; ?>

                <?php
                    if ($review->isEditable()) {
                        echo $this->htmlLink(
                            array(
                                'route' => 'ynbusinesspages_review',
                                'action' => 'edit',
                                'id' => $review->getIdentity(),
                                'tab' => $this->identity,
                                'page' => $this->page
                            ),
                            $this->translate('<i class="fa fa-pencil-square-o"></i>'),
                            array(
                                'class' => 'smoothbox'
                            )
                        );
                    }
                ?>
            </div>  

            <div class="user_name">
                <?php echo $this->htmlLink($owner, $this->itemPhoto($owner, 'thumb.icon'))?>
                <div>
                    <div class="review-title"><?php echo $review -> title?></div>
                    <div class="review-rating">
                        <span><?php echo $this->partial('_review_rating_big.tpl', 'ynbusinesspages', array('review' => $review));?></span>
                        <span class="small_description">
                            <span>
                                <?php echo $this -> translate('by');?>
                                <?php echo $owner?>
                            </span>
                            <span>
                                <?php 
                                    $modified_date = new Zend_Date(strtotime($review->modified_date));
                                    $modified_date->setTimezone($this->timezone);
                                    echo $this->timestamp($modified_date);
                                ?>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="review_detail">
                          
                <div><p><?php echo $this -> viewMore($review->body)?></p></div>   
            </div>
        </li>
    <?php endforeach; ?>
</ul>

<?php if(count($this->paginator) > 0) :?>
 	<div class="ynbusinesspages-paginator">
      <div id="ynbusinesspages_profile_reviews_previous" class="paginator_previous">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
          'onclick' => '',
          'class' => 'buttonlink icon_previous'
        )); ?>
      </div>
      <div id="ynbusinesspages_profile_reviews_next" class="paginator_next">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
          'onclick' => '',
          'class' => 'buttonlink_right icon_next'
        )); ?>
      </div>
    </div>
<?php endif;?>

<?php if (!$this->my_review && count($this->paginator) == 0) : ?>
<div class="tip">
    <span><?php echo $this->translate('No reviews have been posted in this business yet.')?></span>
</div>
<?php endif;?>