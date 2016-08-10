<?php if (count($this->occupations)) : ?>
<?php 
    $viewer = $this->viewer;
    $params = $this->params;
    $occpation_p = (isset($params['occupation'])) ? $params['occupation'] : null;
    $giverIds_p = (isset($params['giver_ids'])) ? $params['giver_ids'] : null;
    $givers = array();
    if ($giverIds_p) {
        $giverIds_p = explode(',', $giverIds_p);
        if (count($giverIds_p) > $this->max_giver) {
            $giverIds_p = array_splice($giverIds_p, $this->max_giver);
        } 
        $givers = Engine_Api::_()->user()->getUserMulti($giverIds_p);
    }
?>

<?php
    $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/Autocompleter2.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/Autocompleter.Request.js');
?>
<h3><?php echo $this->translate('Ask your friends to recommend you')?></h3>
<?php if ($this->success) : ?>
<div class="recommendation-ask-success"><?php echo $this->translate('Request sent.')?></div>
<?php endif;?>
<form method="post" id="recomendation-ask-form">
    <div class="recomendation-wrapper">
        <label class="recommendation-label" for="recomendation-occupation"><?php echo $this->translate('What do you want to be recommended for?')?></label>
        <div class="element-wrapper">
            <select class="recommendation-element" id="recomendation-occupation" name="occupation">
                <option value="0"><?php echo $this->translate('Choose...')?></option>
                <?php foreach ($this->occupations as $occupation): ?>
                <option value="<?php echo $occupation['id']?>" <?php if ($occpation_p == $occupation['id']) echo 'selected'?>><?php echo $occupation['title']?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <?php if ($occpation_p) :?>
    <div class="recomendation-wrapper" id="to-wrapper">
        <label class="recommendation-label" for="to"><?php echo $this->translate('Who do you want to ask?')?></label>
        <p class="recommendation-description"><?php echo $this->translate(array('friend_can_ask_recommendations', 'Your friends (you can add up to %s friends)', $this->max_giver), $this->max_giver)?></p>
        <div class="element-wrapper">
            <input type="text" id="to"/>
            <div id="to-element"></div>
        </div>
    </div>
    <div class="recomendation-wrapper" id="toValues-wrapper">        
        <div class="element-wrapper">
            <div id="toValues-element">
                <input type="hidden" id="toValues" class="recommendation-element" name="giver_ids" value="<?php if ($giverIds_p) echo $params['giver_ids'];?>"/>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // Populate data
        var maxRecipients = <?php echo $this->max_giver;?>;
        var to = {
            id : false,
            type : false,
            guid : false,
            title : false
        };
        var isPopulated = false;
    
        <?php if($giverIds_p): ?>
            isPopulated = true;
            to = [];
            <?php foreach ($givers as $giver) : ?>
            toElement = {
                id : <?php echo sprintf("%d", $giver->getIdentity()) ?>,
                type : '<?php echo $giver->getType() ?>',
                href : '<?php echo Engine_Api::_()->ynresume()->getHref($giver) ?>',
                title : '<?php echo $this->string()->escapeJavascript($giver->getTitle()) ?>',
                photo: '<?php echo $this->itemPhoto($giver, 'thumb.profile') ?>'
            };
            to.push(toElement);
            <?php endforeach;?>
        <?php endif; ?>
      
        function removeFromToValue(id) {
            // code to change the values in the hidden field to have updated values
            // when recipients are removed.
            var toValues = document.getElementById('toValues').value;
            var toValueArray = toValues.split(",");
            var toValueIndex = "";
    
            var checkMulti = id.search(/,/);
    
            // check if we are removing multiple recipients
            if (checkMulti!=-1){
                var recipientsArray = id.split(",");
                for (var i = 0; i < recipientsArray.length; i++){
                    removeToValue(recipientsArray[i], toValueArray);
                }
            }
            else{
                removeToValue(id, toValueArray);
            }
    
            // hide the wrapper for usernames if it is empty
            if (document.getElementById('toValues').value==""){
                document.getElementById('toValues-wrapper').style.height = '0';
            }
    
            document.getElementById('to').style.display = 'block';
            document.getElementById('toValues').fireEvent('change');
        }
    
        function removeToValue(id, toValueArray){
            for (var i = 0; i < toValueArray.length; i++){
                if (toValueArray[i]==id) toValueIndex =i;
            }
    
            toValueArray.splice(toValueIndex, 1);
            document.getElementById('toValues').value = toValueArray.join();
        }
    
        function addEventToGiver() {
            if ($('toValues-wrapper')) {
                new Autocompleter2.Request.JSON('to', '<?php echo $this->url(array('action' => 'suggest-friends', 'includeSelf' => false), 'ynresume_recommend', true)  ?>', {
                    'minLength': 1,
                    'maxChoices': 10,
                    'delay' : 250,
                    'selectMode': 'pick',
                    'autocompleteType'  : 'message',
                    'multiple': true,
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
                        'html' :  to[i].photo+ "<a target='_blank' href="+to[i].href+">"+to[i].title+"</a>"+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+to[i].id+"\", \""+"toValues"+"\");'><i class='fa fa-times fa-lg'></i></a>"
                    });
                    document.getElementById('to-element').appendChild(myElement);
                }
                document.getElementById('to-wrapper').style.height = 'auto';
                // Hide to input?
                if (to.length >= <?php echo $this->max_giver?>) {
                    document.getElementById('to').style.display = 'none';
                    document.getElementById('toValues-wrapper').style.display = 'none';
                }
            }
        }
    };
    addEventToGiver();
    </script>
    
    <?php if (!empty($givers)) : ?>    
    <div class="recomendation-wrapper">
        <label class="recommendation-label"><?php echo $this->translate('What are your relationships?')?></label>
        <ul id="relationships-list" class="element-wrapper">
        <?php foreach ($givers as $giver) : ?>
            <?php $canRecommend = false;?>
            <li class="relationship-item" id="relationship-<?php echo $giver->getIdentity()?>">
                <div class="relationship-description">
                    <?php echo $this->translate('With %s', $this->htmlLink(Engine_Api::_()->ynresume()->getHref($giver), $giver->getTitle()))?>
                </div>
                <?php $hasRecommended = Engine_Api::_()->ynresume()->hasRecommended($occpation_p, $viewer, $giver);
                if ($hasRecommended['status']) : ?>
                <div class="relationship-error"><?php echo $this->translate($hasRecommended['message'], $this->htmlLink(Engine_Api::_()->ynresume()->getHref($giver), $giver->getTitle()))?></div>
                <?php else: ?>
                <?php $canRecommend = true;?>
                <?php $relationships = Engine_Api::_()->ynresume()->getRelationships()?>
                <div class="element-wrapper">
                    <p class="error"></p>
                    <?php $relationship_p = (isset($params['relationship-'.$giver->getIdentity()])) ? $params['relationship-'.$giver->getIdentity()] : null;?>
                    <select class="recomendation-input" rel="relationship" id="relationship-<?php echo $giver->getIdentity()?>" name="relationship-<?php echo $giver->getIdentity()?>" value="<?php if ($relationship_p) echo $relationship_p;?>">
                        <option value="0"><?php echo $this->translate('Choose relationship')?></option>
                        <optgroup label="<?php echo $this->translate('Professional')?>">
                        <?php foreach ($relationships as $key => $relationship): ?>
                        <option value="<?php echo $relationship?>" <?php if ($relationship == $relationship_p) echo 'selected';?>><?php echo $this->translate('YNRESUME_RELATIONSHIP_ASK_'.strtoupper($relationship), $giver->getTitle())?></option>
                        <?php if ($key == 8) :?>
                        </optgroup>
                        <optgroup label="<?php echo $this->translate('Education')?>">
                        <?php endif;?>
                        <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                <?php $giver_occupations = Engine_Api::_()->ynresume()->getOccupations($giver->getIdentity());
                if (empty($giver_occupations)) : ?>
                <div class="relationship-error"><?php echo $this->translate('giver_no_occupations', $this->htmlLink(Engine_Api::_()->ynresume()->getHref($giver), $giver->getTitle()))?></div>
                <?php else: ?>
                <div class="element-wrapper">
                    <p class="error"></p>
                    <?php $position_p = (isset($params['occupation-'.$giver->getIdentity()])) ? $params['occupation-'.$giver->getIdentity()] : null;?>
                    <select class="recomendation-input" rel="position" id="occupation-<?php echo $giver->getIdentity()?>" name="occupation-<?php echo $giver->getIdentity()?>" value="<?php if ($relationship_p) echo $relationship_p;?>">
                        <option value="0"><?php echo $this->translate('What was %s\'s postion at the time', $giver->getTitle())?></option>
                        <?php foreach ($giver_occupations as $occupation): ?>
                        <option value="<?php echo $occupation['id']?>" <?php if ($occupation['id'] == $position_p) echo 'selected';?>><?php echo $occupation['title']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <?php endif; ?>                    
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php if ($canRecommend) : ?>
    <div class="recomendation-wrapper">
        <label class="recommendation-label"><?php echo $this->translate('Write your message')?></label>
        <div class="element-wrapper">
            <p class="error"></p>
            <div class="element-label"><?php echo $this->translate('Subject')?></div>
            <input class="recomendation-input" rel="subject" type="text" id="recommendation-subject" name="ask_subject" value="<?php echo $this->translate('YNRESUME_RECOMMENDATION_ASK_SUBJECT')?>"/>
        </div>
        <div class="element-wrapper">
            <p class="error"></p>
            <textarea class="recomendation-input" rel="message" id="recommendation-message" name="ask_message"><?php echo $this->translate('YNRESUME_RECOMMENDATION_ASK_MESSAGE', $viewer->getTitle())?></textarea>
        </div>
    </div>
    <div class="ynresume_loading" style="display: none; text-align: center">
        <img src='application/modules/Ynresume/externals/images/loading.gif'/>
    </div>
    <div class="recomendation-button">
        <button type="submit"><?php echo $this->translate('Send')?></button>
        <button type="button" class="recomendation-cancel"><?php echo $this->translate('Cancel')?></button>
    </div>
    <?php endif;?>
    <?php endif;?>
    <?php endif;?>
</form>

<script type="text/javascript">
    //script for reload widget
    window.addEvent('domready', function() {
        $$('.recommendation-element').addEvent('change', function() {
            var form = $('recomendation-ask-form');
            if (form) {
                var params = form.toQueryString().parseQueryString();
                reloadWidget(form, params);
            }
        });
        
        var form = $('recomendation-ask-form');
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
                            case 'subject':
                                text = '<?php echo $this->translate('Please enter a subject for your message.')?>';
                                break;
                            case 'message':
                                text = '<?php echo $this->translate('Please enter your message.')?>';
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
        }
    });
    
    function reloadWidget(form, params) {
        params.format = 'html';
        form.getElements('.recomendation-button').hide();
        form.getElements('.ynresume_loading').show();
        var request = new Request.HTML({
            url : en4.core.baseUrl + 'widget/index/name/ynresume.recommendation-ask',
            data : params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                var parent = form.getParent();
                var child = Elements.from(responseHTML)[0].getChildren();
                parent.innerHTML = '';
                parent.adopt(child);
                eval(responseJavaScript);
            }
        });
        request.send();
    }
</script>
<?php else: ?>
    <div class="tip">
        <span><?php echo $this->translate('Want to get recommended? Add a position or your education to get started.')?></span>
    </div>
<?php endif;?>