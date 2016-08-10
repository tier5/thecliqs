<div id="ynresume-given-recommendation">
<?php $recommendations = $this->recommendations;
if (count($recommendations)) : ?>
    <div class="recomendation-wrapper">
        <ul id="recommendation-list">
            <?php foreach ($recommendations as $recommendation) : ?>
            <li class="recommendation-item">
                <?php $receiver = $recommendation->getReceiver();?>
                <div class="recommendation-photo receiver-photo">
                    <?php echo $this->htmlLink($receiver->getHref(), $this->itemPhoto($receiver, 'thumb.icon'))?>
                </div>

                <div class="recommendation-options">
                    <a href="javascript:void(0)" class="edit-recommendation-btn"><i class="fa fa-comments-o"></i> <?php echo $this->translate('Edit Recommendation')?></a>
                    <a href="<?php echo $this->url(array('action'=>'edit-privacy', 'id'=>$recommendation->getIdentity()),'ynresume_recommend', true)?>" class="smoothbox recommendation-btn"><i class="fa fa-pencil"></i> <?php echo $this->translate('Edit Privacy')?></a>
                    <a href="<?php echo $this->url(array('action'=>'remove', 'id'=>$recommendation->getIdentity()),'ynresume_recommend', true)?>" class="smoothbox recommendation-btn"><i class="fa fa-times"></i> <?php echo $this->translate('Remove')?></a>
                </div>

                <div class="recommendation-main-content">             
                    <div class="recommendation-info">
                        <div class="receiver-title recommendation-title">
                            <?php echo $this->htmlLink($receiver->getHref(), $receiver->getTitle())?>
                        </div>
                        <div class="receiver-occupation-info recommendation-occupation-info">
                            <?php $position = Engine_Api::_()->ynresume()->getPosition($recommendation->receiver_position_type, $recommendation->receiver_position_id)?>
                            <?php echo $this->translate('As %s', $position)?>
                        </div>
                    </div>
                    
                    <div class="recommendation-view">
                        <div class="recommendation-time">
                            <i class="fa fa-clock-o"></i> <?php echo date('M, d, Y', $recommendation->getGivenDate()->getTimestamp());?>
                        </div>
                        <div class="recommendation-content">
                            <?php echo $recommendation->content?>
                        </div>
                       
                    </div>
                    
                    <div class="recommendation-form-wrapper">
                        <form class="recommendation-form" rel="<?php echo $recommendation->getIdentity()?>">
                            <div class="element-wrapper">
                                <p class="error"></p>
                                <textarea class="edit-content" name="content" rel="content"><?php echo $recommendation->content?></textarea>
                            </div>
                            <div class="element-wrapper">
                                <div class="element-description"><?php echo $this->translate('Write a message to %s to send with your recommendation.', $this->htmlLink($receiver->getHref(), $receiver->getTitle()))?></div>
                                <textarea class="edit-message" name="given_message"><?php echo $this->translate('YNRESUME_RECOMMENDATION_EDIT_MESSAGE', $receiver->getTitle())?></textarea>
                                <input type="hidden" value="<?php echo $this->translate('YNRESUME_RECOMMENDATION_EDIT_MESSAGE', $receiver->getTitle())?>" class="edit-message-default" />
                            </div>
                            <div class="element-wrapper recomendation-button">
                                <button type="submit"><?php echo $this->translate('Send')?></button>
                                <button type="button" class="recomendation-cancel"><?php echo $this->translate('Cancel')?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

<script type="text/javascript">
    window.addEvent('domready', function() {
        $$('.recommendation-form').addEvent('submit', function(e){
            e.preventDefault();
            var content = this.getElements('.edit-content')[0];
            var obj = this;
            if (content.get('value') == '') {
                content.getParent('.element-wrapper').getElements('.error')[0].set('text', '<?php echo $this->translate('Please enter your recommendation.')?>');
            }
            else {
                var url = '<?php echo $this->url(array('action'=>'edit-content'), 'ynresume_recommend', true)?>';
                new Request.JSON({
                    url: url,
                    method: 'post',
                    data: {
                        'id': this.get('rel'),
                        'content': content.get('value'),
                        'message': this.getElements('.edit-message')[0].get('value'),
                    },
                    onSuccess : function(json, text) {
                        var message = obj.getElements('.edit-message-default')[0].get('value');
                        obj.getElements('.edit-message')[0].set('value', message);
                        obj.getParent().hide();
                        var li = obj.getParent('.recommendation-item');
                        li.getElements('.recommendation-content')[0].set('text', content.get('value'));
                        li.getElements('.recommendation-time')[0].set('html', '<i class="fa fa-clock-o"></i> '+json.time);
                        li.getElement('.recommendation-options').show();
                        li.getElements('.recommendation-view')[0].show();
                    }
                }).send();
            }
        });
        
        $$('.edit-recommendation-btn').addEvent('click', function() {
            var li = this.getParent('.recommendation-item');
            li.getElements('.recommendation-view').hide();
            li.getElement('.recommendation-options').hide();
            li.getElements('.recommendation-form-wrapper').show();
        });
        
        $$('.recomendation-cancel').addEvent('click', function() {
            var li = this.getParent('.recommendation-item');
            li.getElements('.edit-content')[0].set('value', (li.getElements('.recommendation-content')[0].get('text')).trim());
            li.getElements('.edit-message')[0].set('value', li.getElements('.edit-message-default')[0].get('value'));
            li.getElement('.recommendation-options').show();
            li.getElements('.recommendation-view')[0].show();
            li.getElements('.recommendation-form-wrapper')[0].hide();
        });
    });
</script>
<?php else :?>
    <div class="tip">
        <?php if ($this->can_recommend) :?>
        <span><?php echo $this->translate('You haven\'t written any recommendations yet! %s', $this->htmlLink(array('route'=>'ynresume_recommend','action'=>'give'), $this->translate('Recommend someone')))?></span>
        <?php else: ?>
        <span><?php echo $this->translate('You haven\'t written any recommendations yet!')?></span>
        <?php endif;?>
    </div>
<?php endif; ?>
</div>

