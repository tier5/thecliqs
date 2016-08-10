
<?php $recommendation = $this->recommendation;
if ($recommendation) :?>
<?php $receiver = $recommendation->getReceiver();?>
<div class="recommendation-view-message-simple">    
    <h3><?php echo $this->translate('%s\'s request', $receiver->getTitle())?></h3>

    <div class="recommendation-view-message">    
        <div class="receiver-photo">
            <?php echo $this->htmlLink($receiver->getHref(), $this->itemPhoto($receiver, 'thumb.icon'))?>
        </div>

        <div class="recommendation-info">
            <div class="receiver-title">
                <?php echo $this->htmlLink($receiver->getHref(), $receiver->getTitle())?>
            </div>
            <div class="receiver-headline">
                <?php echo $receiver->headline;?>
            </div>
            <div class="receiver-position">
                <?php $position = Engine_Api::_()->ynresume()->getPosition($recommendation->receiver_position_type, $recommendation->receiver_position_id);?>
                <?php echo $this->translate('%s has requested a recommendation form you as %s', $this->htmlLink($receiver->getHref(), $receiver->getTitle()), $position)?>
            </div>
        </div>
    </div>

    <div class="receiver-ask-message">
        <?php echo $recommendation->ask_message?>
    </div>

    <div class="recommendation-options">
        <a class="write-recommendation-btn button bold" onclick="writeRecommendation(<?php echo $recommendation->getIdentity();?>)" href="javascript:void(0);"><?php echo $this->translate('Write A Recommendation')?></a>
        <a class="ignore-recommendation-btn button bold" href="<?php echo $this->url(array('action' => 'ignore-request', 'id' => $recommendation->getIdentity()), 'ynresume_recommend',true)?>"><?php echo $this->translate('Ignore Request')?></a>
        <a class="button bold" href="javascript:void(0);" onclick="parent.Smoothbox.close()"><?php echo $this->translate('Cancel')?></a>
    </div>    
    <?php endif; ?>
</div>
<script>
    function writeRecommendation(id) {
        var li = parent.$('recommendation_request-'+id);
        var btn = li.getElements('.write-recommendation-btn')[0];
        btn.fireEvent('click');
        parent.Smoothbox.close();
    }
    
</script>