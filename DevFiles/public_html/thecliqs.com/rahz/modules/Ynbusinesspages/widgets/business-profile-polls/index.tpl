<script type="text/javascript">
    en4.core.runonce.add(function(){
        var anchor = $('ynbusinesspages_poll').getParent();
        $('ynbusinesspages_poll_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
        $('ynbusinesspages_poll_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

        $('ynbusinesspages_poll_previous').removeEvents('click').addEvent('click', function(){
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

        $('ynbusinesspages_poll_next').removeEvents('click').addEvent('click', function(){
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
                'controller' => 'poll',
                'action' => 'list',
                'business_id' => $this->business->getIdentity(),
                'parent_type' => 'ynbusinesspages_business',
                 'tab' => $this->identity,
            ), '<i class="fa fa-list"></i>'.$this->translate('View all Polls'), array(
                'class' => 'buttonlink'
            ))
            ?>
        <?php endif; ?>

        <?php if ($this->canCreate):?>
            <?php echo $this->htmlLink(array(
                'route' => 'poll_general',
                'controller' => 'index',
                'action' => 'create',
                'business_id' => $this->business->getIdentity(),
                'parent_type' => 'ynbusinesspages_business',
            ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Poll'), array(
                'class' => 'buttonlink'
            ))
            ?>
        <?php endif; ?>
    </div>      

    <div class="ynbusinesspages-profile-header-content">
        <?php if( $this->paginator->getTotalItemCount() > 0 ): 
            $business = $this->business;?>
            <span class="ynbusinesspages-numeric"><?php echo $this->paginator->getTotalItemCount(); ?></span>
            <?php echo $this-> translate(array("ynbusiness_poll", "Polls", $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount());?>
        <?php endif; ?>
    </div>
</div>

<div class="ynbusinesspages_list" id="ynbusinesspages_poll">
    <!-- Content -->
    <?php if( $this->paginator->getTotalItemCount() > 0 ): 
    $business = $this->business;
    ?>

    <ul class="ynbusinesspages_poll polls_browse">  
        <?php foreach ($this->paginator as $poll):
		$owner = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getOwner($poll); ?>
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
    
    <div class="ynbusinesspages-paginator">
        <div id="ynbusinesspages_poll_previous" class="paginator_previous">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => '',
              'class' => 'buttonlink icon_previous'
            )); ?>
        </div>
        <div id="ynbusinesspages_poll_next" class="paginator_next">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
              'onclick' => '',
              'class' => 'buttonlink_right icon_next'
            )); ?>
        </div>
    </div>
   
    <?php else: ?>
    <div class="tip">
        <span>
             <?php echo $this->translate('No polls have been created.');?>
        </span>
    </div>
    <?php endif; ?>
</div>