<div class="ynbusinesspages_list">
    <div class="">
        <!-- Content -->
        <?php if( $this->paginator->getTotalItemCount() > 0 ): 
        $business = $this->business;?>
        <ul class="ynbusinesspages_poll polls_browse">  
            <?php foreach ($this->paginator as $poll): 
            	$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($poll);?>
            <li id="poll-item-<?php echo $poll->poll_id ?>">
                <?php echo $this->htmlLink(
                    $poll->getHref(),
                    $this->itemPhoto($owner, 'thumb.icon', $owner->getTitle()),
                    array('class' => 'polls_browse_photo')
                ) ?>
                <div class="polls_browse_info">
                    <h3 class="polls_browse_info_title">
                        <?php echo $this->htmlLink($poll->getHref(), $poll->getTitle()) ?>
                        <?php if( $poll->closed ): ?>
                        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Poll/externals/images/close.png' alt="<?php echo $this->translate('Closed') ?>" />
                        <?php endif ?>
                    </h3>
                    <div class="polls_browse_info_date">
                        <?php echo $this->translate('Posted by %s', $this->htmlLink($owner, $owner->getTitle())) ?>
                        <?php echo $this->timestamp($poll->creation_date) ?>
                    </div>
                    <div class="polls_browse_info_vote">
                        <?php echo $this->translate(array('%s vote', '%s votes', $poll->vote_count), $this->locale()->toNumber($poll->vote_count)) ?>
                        -
                        <?php echo $this->translate(array('%s view', '%s views', $poll->view_count), $this->locale()->toNumber($poll->view_count)) ?>
                    </div>
                    <?php if (!empty($poll->description)): ?>
                    <div class="polls_browse_info_desc">
                    <?php echo $poll->description ?>
                    </div>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; ?>             
        </ul>  
        <?php endif; ?>
    </div>
</div>
