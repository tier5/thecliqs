<?php 
    $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $receiver = $resume = (isset($params['view']) && $params['view']) ? Engine_Api::_()->core()->getSubject() : $this->resume;
    $params = $this->params;
    $manage = ($resume->isOwner($viewer)) && (!isset($params['view']) || !$params['view']);
    $receivedRecommendations = Engine_Api::_()->getDbTable('recommendations','ynresume')->getReceivedRecommendations($receiver->user_id);
    $givenRecommendations = Engine_Api::_()->getDbTable('recommendations','ynresume')->getGivenRecommendations($receiver->user_id);
    $temp_arr = array();
    foreach ($givenRecommendations as $recommendation) {
        if ($recommendation->isViewable()) {
            $temp_arr[] = $recommendation;
        }    
    }
    $givenRecommendations = $temp_arr;
    
    $occupations = Engine_Api::_()->ynresume()->getOccupations($receiver->user_id);
    $viewGiven = (!$manage && isset($params['view_given']) && $params['view_given']) ? true : false;
    $canRecommend = false;
    if (!$manage && !$resume->isOwner($viewer)) {
        foreach ($occupations as $occupation) {
            if (!Engine_Api::_()->ynresume()->hasRecommended($occupation['id'], $receiver, $viewer)) {
                $canRecommend = true;
                break;
            }
        }
    }
    $canRecommend = $canRecommend && (Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynresume_resume', null, 'recommend')->checkRequire());  
?>
<?php if (($manage && count($receivedRecommendations)) || (!$manage && (count($receivedRecommendations) || count($givenRecommendations) || $canRecommend))) :?>
    <div class="section-option">
    <?php if ($manage) :?>
        <?php echo $this->htmlLink(array('route'=>'ynresume_recommend', 'action'=>'received'), $this->translate('Manage'), array('class' => 'ynresume-add-btn'))?>
    <?php else: ?>
        <?php if (count($receivedRecommendations)) :?>
        <a class="ynresume-add-btn recommendation-mode<?php if(!$viewGiven) echo ' actived'?>" href="javascript:void(0);" id="view-received-recommendations-btn"><?php echo $this->translate('Received (%s)', count($receivedRecommendations))?></a>
        <?php endif;?>
        <?php if (count($givenRecommendations)) :?>
        <a class="ynresume-add-btn recommendation-mode<?php if($viewGiven) echo ' actived'?>" href="javascript:void(0);" id="view-given-recommendations-btn"><?php echo $this->translate('Given (%s)', count($givenRecommendations))?></a>
        <?php endif;?>
    <?php endif; ?>

    <h3 class="section-label">
        <span class="section-label-icon"><i class="<?php echo Engine_Api::_()->ynresume()->getSectionIconClass($this->section);?>"></i></span>
        <span><?php echo $label;?></span>
    </h3>
    
    <div class="ynresume_loading" style="display: none; text-align: center">
        <img src='application/modules/Ynresume/externals/images/loading.gif'/>
    </div>
    
    <div class="ynresume-section-content">
        <?php if (count($receivedRecommendations) && !$viewGiven) :?>
        <div class="section-wrapper" id="received-recommendations-wrapper">
            <ul class="occupation-list">
            <?php foreach ($occupations as $occupation) :?>
                <?php $recommendations = Engine_Api::_()->ynresume()->getShowRecommendationsOfOccupation($occupation['type'], $occupation['item_id'], $receiver->user_id)?>
                <?php if (count($recommendations)) :?>
                <li class="occupation-item section-item">
                    <div class="occupation-title section-title"><?php echo $occupation['item_title'];?></div>
                    <ul class="recomendation-list">
                        <?php foreach ($recommendations as $recommendation) : ?>
                        <li class="recommendation-item">
                            <?php $giver = $recommendation->getGiver();?>
                            <div class="recommendation-item-top">
                                <div class="giver-avatar"><?php echo $this->htmlLink($giver->getHref(), $this->itemPhoto($giver, 'thumb.icon'))?></div>
                                <div class="giver-title"><?php echo $this->htmlLink($giver->getHref(), $giver->getTitle())?></div>
                                <?php if (isset($giver->headline) && !empty($giver->headline)) : ?>
                                    <div class="giver-headline"><?php echo $giver->headline?></div>
                                <?php endif;?>
                            </div>
                            <div class="recommendation-item-content">
                                <div class="recommendation-content">
                                     <?php echo $this->viewMore($recommendation->content, 255, 3*1027);?>
                                </div>
                                <div class="recommendation-time-relationship">
                                    <span class="time"><?php echo date('M, d, Y,', $recommendation->getGivenDate()->getTimestamp());?></span>
                                    <?php if ($recommendation->relationship != 'senior_to') :?>
                                    <span class="relationship"><?php echo $this->translate('YNRESUME_RELATIONSHIP_SHOW_'.strtoupper($recommendation->relationship), $this->htmlLink($giver->getHref(), $giver->getTitle()), $this->htmlLink($receiver->getHref(), $receiver->getTitle()), $occupation['item_title'])?></span>
                                    <?php else: ?>
                                    <span class="relationship"><?php echo $this->translate('YNRESUME_RELATIONSHIP_SHOW_'.strtoupper($recommendation->relationship), $this->htmlLink($receiver->getHref(), $receiver->getTitle()), $this->htmlLink($giver->getHref(), $giver->getTitle()), $occupation['item_title'])?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endif;?>    
            <?php endforeach;?>
            </ul>
        </div> 
        <?php endif; ?>

        <?php if ((count($givenRecommendations) && $viewGiven) || (count($givenRecommendations) && !count($receivedRecommendations))) :?>
        <div class="section-wrapper" id="given-recommendations-wrapper">
            <ul class="recomendation-list">
                <?php foreach ($givenRecommendations as $recommendation) : ?>
                <li class="recommendation-item">
                    <?php $receiver_recommendation = $recommendation->getReceiver();?>
                    <div class="recommendation-item-top">
                        <div class="receiver-avatar"><?php echo $this->htmlLink($receiver_recommendation->getHref(), $this->itemPhoto($receiver_recommendation, 'thumb.icon'))?></div>
                        <div class="receiver-title"><?php echo $this->htmlLink($receiver_recommendation->getHref(), $receiver_recommendation->getTitle())?></div>
                        <?php if (isset($receiver_recommendation->headline) && !empty($receiver_recommendation->headline)) : ?>
                        <div class="receiver-headline"><?php echo $receiver_recommendation->headline?></div>
                        <?php endif;?>
                    </div>
                    <div class="recommendation-item-content">
                        <div class="recommendation-content">
                            <?php echo $this->viewMore($recommendation->content, 255, 3*1027);?>
                        </div>
                        <div class="recommendation-time-relationship">
                            <span class="time"><?php echo date('M, d, Y,', $recommendation->getGivenDate()->getTimestamp());?></span>
                            <?php $place = Engine_Api::_()->ynresume()->getPlace($recommendation->receiver_position_type, $recommendation->receiver_position_id)?>               
                            <?php if ($recommendation->relationship != 'senior_to') :?>
                            <span class="relationship"><?php echo $this->translate('YNRESUME_RELATIONSHIP_SHOW_'.strtoupper($recommendation->relationship), $this->htmlLink($receiver->getHref(), $receiver->getTitle()), $this->htmlLink($receiver_recommendation->getHref(), $receiver_recommendation->getTitle()), $place)?></span>
                            <?php else: ?>
                            <span class="relationship"><?php echo $this->translate('YNRESUME_RELATIONSHIP_SHOW_'.strtoupper($recommendation->relationship), $this->htmlLink($receiver_recommendation->getHref(), $receiver_recommendation->getTitle()), $this->htmlLink($receiver->getHref(), $receiver->getTitle()), $place)?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div> 
        <?php endif; ?>

        <div class="section-btn recommendation-btn">
        <?php if ($manage) :?>
            <?php if (count($occupations)) echo $this->htmlLink(array('route'=>'ynresume_recommend', 'action'=>'ask'), $this->translate('Ask for a recommendation'), array('class'=>'button bold'));?>
        <?php else: ?>
            <?php if ($canRecommend) : ?>
            <p><?php $this->translate('%s, would you like to recommend %s?', $this->htmlLink(Engine_Api::_()->ynresume()->getHref($viewer), $viewer->getTitle()), $this->htmlLink($receiver->getHref($viewer), $receiver->getTitle()))?></p>
            <?php echo $this->htmlLink(array('route'=>'ynresume_recommend', 'action'=>'give', 'receiver_id'=>$receiver->user_id), $this->translate('%s, would you like to recommend %s?'))?>
            <?php endif;?>
        <?php endif;?>
        </div>
    </div>
</div>
<script type="text/javascript">
    window.addEvent('domready', function() {
        if ($('view-received-recommendations-btn')) {
            $('view-received-recommendations-btn').addEvent('click', function() {
                renderSection('recommendation', {'reload':true});
            });
        }
        
        if ($('view-given-recommendations-btn')) {
            $('view-given-recommendations-btn').addEvent('click', function() {
                renderSection('recommendation', {'view_given':true, 'reload':true});
            });
        }
    });
</script>
<?php endif; ?>