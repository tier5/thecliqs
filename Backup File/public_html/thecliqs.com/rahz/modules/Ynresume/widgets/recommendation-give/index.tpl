<?php
    $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/Autocompleter3.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/Autocompleter.Request.js');

    $viewer = $this->viewer;
    $params = $this->params;
?>
<?php if ($this->success) : ?>
<div class="recommendation-ask-success"><?php echo $this->translate('Your recommendation was sent.')?></div>
<?php endif; ?>
<form id="recomendation-give-form">
<div class="recomendation-give-form">
<?php $recommendation = $this->recommendation;
if ($recommendation) : ?>
<?php $receiver = $recommendation->getReceiver();?>
	<div class="form-description"><?php echo $this->translate('Give %s a recommendation', $this->htmlLink($receiver->getHref(), $receiver->getTitle()))?></div>
    <input type="hidden" name="recommendation_id" value="<?php echo $recommendation->getIdentity()?>"/>
    
    <div class="recommendation-info">
        <div class="receiver-photo">
            <?php echo $this->htmlLink($receiver->getHref(), $this->itemPhoto($receiver, 'thumb.icon'))?>
        </div>
        <div class="receiver-title">
            <?php echo $this->htmlLink($receiver->getHref(), $receiver->getTitle())?>
        </div>
        <div class="receiver-occupation-info">
            <?php $position = Engine_Api::_()->ynresume()->getPosition($recommendation->receiver_position_type, $recommendation->receiver_position_id)?>
            <?php echo $this->translate('Recommend %s\'s work as %s', $receiver->getTitle(), $position)?>
        </div>
    </div>

    <div class="recomendation-wrapper">
        <img src="application/modules/Ynresume/externals/images/give_recommendation_1.png" alt="">
        <label class="recommendation-label"><?php echo $this->translate('Write a recommendation', $receiver->getTitle())?></label>
        <div class="element-wrapper">
            <div class="element-description"><?php echo $this->translate('If needed, you can make changes or delete it even after you send it.')?></div>
            <p class="error"></p>
            <textarea name="content" class="recomendation-input" rel="content" placeholder="<?php echo $this->translate('Ex. %s is very detailed-oriented and produced great results for the company...', $receiver->getTitle())?>"></textarea>
        </div>
    </div>
    <div class="recomendation-wrapper">
        <label class="recommendation-label"><?php echo $this->translate('Your message to %s', $receiver->getTitle())?></label>
        <div class="element-wrapper">
            <div class="element-description"><?php echo $this->translate('You can personalize this message if you\'d like.')?></div>
            <textarea name="given_message"><?php echo $this->translate('YNRESUME_RECOMMENDATION_GIVE_MESSAGE', $receiver->getTitle(), $viewer->getTitle())?></textarea>
        </div>
    </div>    
    <div class="recomendation-wrapper">
        <img src="application/modules/Ynresume/externals/images/give_recommendation_2.png" alt="">
        <label class="recommendation-label"><?php echo $this->translate('How %s described your relationship', $receiver->getTitle())?></label>
        <div class="element-wrapper">
            <div class="element-description"><?php echo $this->translate('If this doesn\'t seem right, choose different options from the dropdown.')?></div>
            <p class="error"></p>
            <?php $relationships = Engine_Api::_()->ynresume()->getRelationships()?>
            <select name="relationship" rel="relationship" class="recomendation-input">
                <optgroup label="<?php echo $this->translate('Professional')?>">
                <?php foreach ($relationships as $key => $relationship): ?>
                <option <?php if($relationship == $recomemndation->relationship) echo 'selected'?> value="<?php echo $relationship?>"><?php echo $this->translate('YNRESUME_RELATIONSHIP_GIVE_'.strtoupper($relationship), $receiver_title)?></option>
                <?php if ($key == 8) :?>
                </optgroup>
                <optgroup label="<?php echo $this->translate('Education')?>">
                <?php endif;?>
                <?php endforeach; ?>
                </optgroup>
            </select>
        </div>
    </div>
    <div class="recomendation-wrapper">
        <label class="recommendation-label"><?php echo $this->translate('Your position was')?></label>
        <div class="element-wrapper">
            <p class="error"></p>
            <?php $giver_occupation = Engine_Api::_()->ynresume()->getOccupations($viewer->getIdentity());?>
            <?php $giver_position = ($recommendation->giver_position_type && $recommendation->giver_position_id) ? $recommendation->giver_position_type.'-'.$recommendation->giver_position_id : null;?>
            <?php if (count($giver_occupation)) : ?>
            <select name="giver-occupation" rel="position" class="recomendation-input">
                <?php if (!$giver_position) : ?>
                <option value="0"><?php echo $this->translate('Choose...')?></option>
                <?php endif; ?>
                <?php foreach ($giver_occupation as $occupation): ?>
                <option <?php if (!$giver_position == $occupation['id']) echo 'selected'; ?> value="<?php echo $occupation['id']?>"><?php echo $occupation['title']?></option>
                <?php endforeach; ?>
            </select>
            <?php else: ?>
            <div class="element-description"><?php echo $this->translate('YNRESUME_RECOMMENDATION_NO_GIVER_POSITION')?></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="ynresume_loading" style="display: none; text-align: center">
        <img src='application/modules/Ynresume/externals/images/loading.gif'/>
    </div>
    <div class="recomendation-button">
        <button type="submit"><?php echo $this->translate('Send')?></button>
        <button type="button" class="recomendation-cancel"><?php echo $this->translate('Cancel')?></button>
    </div>    
<?php else: ?>
    <?php 
        $receiver_id = (isset($params['receiver_id'])) ? $params['receiver_id'] : null;
        $receiver_occupation = ($receiver_id) ? Engine_Api::_()->ynresume()->getAvailableOccupations($receiver_id, $viewer->getIdentity()) : array();
        $receiver_title = (isset($params['receiver_title'])) ? $params['receiver_title'] : null;
        if ($receiver_id && !$receiver_title) $receiver_title = Engine_Api::_()->user()->getUser($receiver_id)->getTitle();
    ?>
    <div class="recomendation-wrapper" id="to-wrapper">
        <label class="recommendation-label" for="to"><?php echo $this->translate('Who do you want to recommend?')?></label>
        <div class="element-wrapper">
            <input type="text" id="to" name="receiver_title" value="<?php if ($receiver_title) echo $receiver_title;?>"/>
            <div id="to-element"></div>
        </div>
    </div>
    <div class="recomendation-wrapper" id="toValues-wrapper">
        <div id="toValues-element">
            <input type="hidden" id="toValues" class="recommendation-element" name="receiver_id" value="<?php if ($receiver_id) echo $receiver_id;?>"/>
        </div>
    </div>
    <script type="text/javascript">
        // Populate data
        var maxRecipients = 3;
        var to = {
            id : false,
            type : false,
            guid : false,
            title : false
        };
        var isPopulated = false;
    
        function addEventToGiver() {
            if ($('toValues-wrapper')) {
                new Autocompleter2.Request.JSON('to', '<?php echo $this->url(array('action' => 'suggest-friends', 'includeSelf' => false), 'ynresume_recommend', true)  ?>', {
                    'minLength': 1,
                    'maxChoices': 10,
                    'delay' : 250,
                    'selectMode': 'pick',
                    'autocompleteType'  : 'message',
                    'multiple': false,
                    'className': 'message-autosuggest',
                    'filterSubset' : true,
                    'tokenFormat' : 'object',
                    'tokenValueKey' : 'label',
                    'injectChoice': function(token){
                        if(token.type == 'user'){
                            var choice = new Element('li', {
                                'class': 'autocompleter-choices',
                                'html': token.photo,
                                'id':token.label
                            });
                            new Element('div', {
                                'html': this.markQueryValue(token.label),
                                'class': 'autocompleter-choice'
                            }).inject(choice);
                            this.addChoiceEvents(choice).inject(this.choices);
                            choice.store('autocompleteChoice', token);
                        }
                        else {
                            var choice = new Element('li', {
                                'class': 'autocompleter-choices friendlist',
                                'id':token.label
                            });
                            new Element('div', {
                                'html': this.markQueryValue(token.label),
                                'class': 'autocompleter-choice'
                            }).inject(choice);
                            this.addChoiceEvents(choice).inject(this.choices);
                            choice.store('autocompleteChoice', token);
                        }
                    },
                    onPush : function(){
                        if( (maxRecipients != 0) && (document.getElementById('toValues').value.split(',').length >= maxRecipients) ){
                            document.getElementById('to').style.display = 'none';
                        }
                        document.getElementById('toValues').fireEvent('change');
                    }
            });
              
            if(isPopulated ) { // NOT POPULATED
                for (var i = 0; i < to.length; i ++) {
                    var myElement = new Element("span", {
                        'id' : 'tospan_' + to[i].title + '_' + to[i].id,
                        'class' : 'tag tag_' + to[i].type,
                        'html' :  to[i].photo+ "<a target='_blank' href="+to[i].href+">"+to[i].title+"</a>"+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+to[i].id+"\", \""+"toValues"+"\");'>x</a>"
                    });
                    document.getElementById('to-element').appendChild(myElement);
                }
                document.getElementById('to-wrapper').style.height = 'auto';
                // Hide to input?
                if (to.length >= 3) {
                    document.getElementById('to').style.display = 'none';
                    document.getElementById('toValues-wrapper').style.display = 'none';
                }
            }
        }
    };
    addEventToGiver();
    </script>
    <?php if ($receiver_id) : ?>
    <?php if (count($receiver_occupation) && ($viewer->getIdentity() != $receiver_id)) : ?>
    <div class="recomendation-wrapper">
        <img src="application/modules/Ynresume/externals/images/give_recommendation_2.png" alt="">
        <label class="recommendation-label"><?php echo $this->translate('What are your relationships?')?></label>
        <div class="element-wrapper">
            <p class="error"></p>
            <?php $relationships = Engine_Api::_()->ynresume()->getRelationships()?>
            <div class="element-description">
                <?php echo $this->translate('What were your positions at the time?')?>
            </div>
            <select name="relationship" rel="relationship" class="recomendation-input">
                <option value="0"><?php echo $this->translate('Choose relationship')?></option>
                <optgroup label="<?php echo $this->translate('Professional')?>">
                <?php foreach ($relationships as $key => $relationship): ?>
                <option value="<?php echo $relationship?>"><?php echo $this->translate('YNRESUME_RELATIONSHIP_GIVE_'.strtoupper($relationship), $receiver_title)?></option>
                <?php if ($key == 8) :?>
                </optgroup>
                <optgroup label="<?php echo $this->translate('Education')?>">
                <?php endif;?>
                <?php endforeach; ?>
                </optgroup>
            </select>
        </div>
        
        <div class="element-wrapper">
            <p class="error"></p>
            <div class="element-description"><?php echo $this->translate('You:')?></div>
            <?php $giver_occupation = Engine_Api::_()->ynresume()->getOccupations($viewer->getIdentity());?>
            <?php if (count($giver_occupation)) : ?>
            <select name="giver-occupation" rel="position" class="recomendation-input">
                <option value="0"><?php echo $this->translate('Choose...')?></option>
                <?php foreach ($giver_occupation as $occupation): ?>
                <option value="<?php echo $occupation['id']?>"><?php echo $occupation['title']?></option>
                <?php endforeach; ?>
            </select>
            <?php else: ?>
            <div class="element-description"><?php echo $this->translate('YNRESUME_RECOMMENDATION_NO_GIVER_POSITION')?></div>
            <?php endif; ?>
        </div>

        <div class="element-wrapper">
            <p class="error"></p>
            <div class="element-description"><?php echo $receiver_title.':'?></div>
            <select name="receiver-occupation" rel="position" class="recomendation-input">
                <option value="0"><?php echo $this->translate('Choose...')?></option>
                <?php foreach ($receiver_occupation as $occupation): ?>
                <option value="<?php echo $occupation['id']?>"><?php echo $occupation['title']?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>
    <div class="recomendation-wrapper">
        <img src="application/modules/Ynresume/externals/images/give_recommendation_1.png" alt="">
        <label class="recommendation-label"><?php echo $this->translate('Write a recommendation')?></label>
        <div class="element-wrapper">
            <div class="element-description"><?php echo $this->translate('If needed, you can make changes or delete it even after you send it.')?></div>
            <p class="error"></p>
            <textarea name="content" class="recomendation-input" rel="content" placeholder="<?php echo $this->translate('Ex. %s is very detailed-oriented and produced great results for the company...', $receiver_title)?>"></textarea>
        </div>
    </div>
    <div class="recomendation-wrapper">
        <label class="recommendation-label"><?php echo $this->translate('Your message to %s', $receiver_title)?></label>
        <div class="element-wrapper">
            <div class="element-description"><?php echo $this->translate('You can personalize this message if you\'d like.')?></div>
            <textarea name="given_message"><?php echo $this->translate('YNRESUME_RECOMMENDATION_GIVE_MESSAGE', $receiver_title, $viewer->getTitle())?></textarea>
        </div>
    </div>
    <div class="ynresume_loading" style="display: none; text-align: center">
        <img src='application/modules/Ynresume/externals/images/loading.gif'/>
    </div>
    <div class="recomendation-button">
        <div class="element-wrapper">
            <button type="submit"><?php echo $this->translate('Send')?></button>
            <button type="button" class="recomendation-cancel"><?php echo $this->translate('Cancel')?></button>
        </div>
    </div>


    <?php else: ?>
        <label class="recommendation-description"><?php echo $this->translate('Sorry, %s can’t get recommended yet.', $receiver_title)?></label>
    <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
</div>
</form>
<script type="text/javascript">
    //script for reload widget
    window.addEvent('domready', function() {
        $$('.recommendation-element').addEvent('change', function() {
            var form = $('recomendation-give-form');
            if (form) {
                var params = form.toQueryString().parseQueryString();
                reloadWidget(form, params);
            }
        });
        
        var form = $('recomendation-give-form');
        if (form) {
            form.addEvent('submit', function(e) {
                e.preventDefault();
                var error = false;
                $$('.recomendation-input').each(function(el) {
                    var value = el.get('value');
                    if (value == '0' || value == '') {
                        error = true;
                        var text = '';
                        switch(el.get('rel')) {
                            case 'relationship':
                                text = '<?php echo $this->translate('Please select a relationship.')?>';
                                break;
                            case 'position':
                                text = '<?php echo $this->translate('Please select a position or education.')?>';
                                break;
                            case 'content':
                                text = '<?php echo $this->translate('Please enter your recommendation.')?>';
                                break;
                        }
                        el.getParent('.element-wrapper').getElements('.error')[0].set('text', text);    
                    }
                });
                if (error == false) {
                    var params = form.toQueryString().parseQueryString();
                    params.send = true;
                    reloadWidget(form, params);
                }
            });
            
            $$('.recomendation-cancel').addEvent('click', function() {
                reloadWidget(form, {});
            });
            
            $$('.write-recommendation-btn').addEvent('click', function() {
                var id = this.get('rel');
                var params = {};
                params.recommendation_id = id;
                reloadWidget(form, params);
            });
        }
    });
    
    function reloadWidget(form, params) {
        params.format = 'html';
        form.getElements('.recomendation-button').hide();
        form.getElements('.ynresume_loading').show();
        var request = new Request.HTML({
            url : en4.core.baseUrl + 'widget/index/name/ynresume.recommendation-give',
            data : params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                var parent = form.getParent();
                var child = Elements.from(responseHTML)[0].getChildren();
                if (params.send && params.recommendation_id) {
                    reloadRequests();
                }
                parent.innerHTML = '';
                parent.adopt(child);
                eval(responseJavaScript);
            }
        });
        request.send();
    }
</script>
