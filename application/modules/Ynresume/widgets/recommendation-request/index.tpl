<div id="ynresume-request-recommendation">
    <?php if (count($this->pendingRecommendations)) : ?>
    <div id="recommendation-pending-wrapper">
        <h3>
            <span class="recommendation-count"><?php echo count($this->pendingRecommendations)?></span>
            <span><?php echo $this->translate(array('Pending recommendation', 'Pending recommendations', count($this->pendingRecommendations)))?></span>
        </h3>
        <ul class="recommendation-list">
            <?php foreach ($this->pendingRecommendations as $recommendation) :?>
            <li class="recommendation-item" id="recommendation_request-<?php echo $recommendation->getIdentity();?>">
                <?php $receiver = $recommendation->getReceiver();?>
                <div class="recommendation-photo receiver-photo">
                    <?php echo $this->htmlLink($receiver->getHref(), $this->itemPhoto($receiver, 'thumb.icon'))?>
                </div>

                <div class="recommendation-options">
                     <a class="smoothbox" href="<?php echo $this->url(array('action' => 'view-request', 'id' => $recommendation->getIdentity()), 'ynresume_recommend',true)?>"><i class="fa fa-envelope"></i> <?php echo $this->translate('View Message')?></a>
                    <a class="write-recommendation-btn" rel="<?php echo $recommendation->getIdentity()?>" href="javascript:void(0);"><i class="fa fa-pencil-square-o"></i> <?php echo $this->translate('Write A Recommendation')?></a>
                    <a class="ignore-recommendation-btn smoothbox" href="<?php echo $this->url(array('action' => 'ignore-request', 'id' => $recommendation->getIdentity()), 'ynresume_recommend',true)?>"><i class="fa fa-user-times"></i> <?php echo $this->translate('Ignore Request')?></a>
                </div>

                <div class="recommendation-main-content">  
                    <div class="recommendation-info">
                        <div class="receiver-title recommendation-title">
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
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif;?>
</div>
<script type="text/javascript">
function reloadRequests() {
        var params = {};
        params.format = 'html';
        var request = new Request.HTML({
            url : en4.core.baseUrl + 'widget/index/name/ynresume.recommendation-request',
            data : params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                var parent = $('ynresume-request-recommendation').getParent();
                var child = Elements.from(responseHTML)[0].getChildren();
                parent.innerHTML = '';
                parent.adopt(child);
                eval(responseJavaScript);
            }
        });
        request.send();
    }
</script>