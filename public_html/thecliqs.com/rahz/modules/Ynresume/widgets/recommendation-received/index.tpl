<div id="received-recommendations">
<?php if (count($this->occupations)) : ?>
    <div id="occupations-recommendation-wrapper">
        <ul id="occupations-recommendation-list">
        <?php foreach ($this->occupations as $occupation) : ?>
            <li class="occupations-recommendation-item">
                <div class="occupation-title">
                    <?php echo $occupation['item_position'].', '.$occupation['item_title'];?>
                </div>

                <?php $recommendations = Engine_Api::_()->ynresume()->getRecommendationsOfOccupation($occupation['type'], $occupation['item_id'])?>
                <?php if (count($recommendations)) : ?>
                <?php $count = Engine_Api::_()->ynresume()->countRecommendations($recommendations);?>
                    <div class="recommendations-statistic">
                        <span class="count-total"><?php echo $this->translate(array('You have %s recommendation', 'You have %s recommendations', count($recommendations)), count($recommendations))?></span>
                        <span class="count-show-hide"><?php echo $this->translate('(%s visible, %s hidden).', count($count['show']), count($count['hide']))?></span>
                    </div>

                    <a class="recommendation-btn button" href="<?php echo $this->url(array('action'=>'ask','occupation'=>$occupation['id']),'ynresume_recommend',true)?>"><?php echo $this->translate('Ask for a recommendation')?></a>           

                    <div class="recommendation-description">
                        <?php echo $this->translate('Checkmark indicates a particular recommendation is displayed on your resume.')?>
                    </div>
                    
                    
                    <ul class="recomendation-list">
                        <?php foreach ($recommendations as $recommendation) : ?>
                        <li class="recommendation-item">
                            <?php $giver = $recommendation->getGiver();?>
                            <div class="recommendation-item-top">
                                <div class="show-hide-recommendation">
                                    <input <?php if ($recommendation->show ) echo 'checked'?> class="input-show-hide-recommendation" type="checkbox" rel="<?php echo $recommendation->getIdentity()?>" title="<?php echo ($recommendation->show ) ? $this->translate('Uncheck to stop showing on your resume') : $this->translate('Check to show on your resume')?>"/>
                                </div>

                                <div class="giver-avatar"><?php echo $this->htmlLink($giver->getHref(), $this->itemPhoto($giver, 'thumb.icon'))?></div>
                                <div class="recommendation-item-author-info">
                                    <div class="giver-title"><?php echo $this->htmlLink($giver->getHref(), $giver->getTitle())?></div>
                                    
                                    <?php if (isset($giver->headline) && !empty($giver->headline)) : ?>
                                        <div class="giver-headline">
                                            <i class="fa fa-briefcase"></i>
                                            <?php echo $giver->headline?>
                                        </div>
                                    <?php endif;?>
                                    
                                    <div class="recommendation-time-relationship">
                                        <i class="fa fa-clock-o"></i>
                                        <span class="time"><?php echo date('M, d, Y,', $recommendation->getGivenDate()->getTimestamp());?></span>
                                        <span class="relationship"><?php echo $this->translate('YNRESUME_RELATIONSHIP_ASK_'.strtoupper($recommendation->relationship), $this->htmlLink($giver->getHref(), $giver->getTitle()))?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="recommendation-content">
                                <?php echo $recommendation->content?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="recommendations-statistic">
                        <?php echo $this->translate('No one\'s recommended you for this position - yet!')?>
                    </div>
                    
                    <a class="recommendation-btn button" href="<?php echo $this->url(array('action'=>'ask','occupation'=>$occupation['id']),'ynresume_recommend',true)?>"><?php echo $this->translate('Ask for a recommendation')?></a>
                <?php endif; ?>                
            </li>
        <?php endforeach;?>
        </ul>
    </div>
    <script type="text/javascript">
        window.addEvent('domready', function() {
            $$('.input-show-hide-recommendation').each(function(el) {
                el.addEvent('change', function() {
                    var value = (this.checked) ? 1 : 0;
                    var id = this.get('rel');
                    var url = '<?php echo $this->url(array('action'=>'show'), 'ynresume_recommend', true)?>';
                    new Request.JSON({
                        url: url,
                        method: 'post',
                        data: {
                            'id': id,
                            'value': value,
                        }
                    }).send();
                    var title = (value == 1) ? '<?php echo $this->translate('Uncheck to stop showing on your resume')?>' : '<?php echo $this->translate('Check to show on your resume')?>';
                    this.set('title', title);
                    en4.core.language.addData({'(%s visible, %s hidden).': ' <?php echo $this->translate('(%s visible, %s hidden).')?>'});
                    var list = this.getParent('.recomendation-list');
                    var show_count = list.getElements('input.input-show-hide-recommendation[type="checkbox"]:checked').length;
                    var hide_count = list.getElements('input.input-show-hide-recommendation[type="checkbox"]:not(:checked)').length;                     
                    var span = this.getParent('.occupations-recommendation-item').getElements('.count-show-hide')[0];
                    if (span) {
                        span.set('text', en4.core.language.translate('(%s visible, %s hidden).', show_count, hide_count));
                    }
                })    
            });
        });
    </script>
<?php else: ?>
    <div class="tip">
        <span><?php echo $this->translate('Want to get recommended? Add a position or your education to get started.')?></span>
    </div>
<?php endif;?>
</div>
