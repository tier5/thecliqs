

	<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">

  // Populate data
  var maxRecipients = <?php echo sprintf("%d", $this->maxRecipients) ?> || 10;
  var to = {
    id : false,
    type : false,
    guid : false,
    title : false
  };
  var isPopulated = false;

  <?php if( !empty($this->isPopulated) && !empty($this->toObject) ): ?>
    isPopulated = true;
    to = {
      id : <?php echo sprintf("%d", $this->toObject->getIdentity()) ?>,
      type : '<?php echo $this->toObject->getType() ?>',
      guid : '<?php echo $this->toObject->getGuid() ?>',
      title : '<?php echo $this->string()->escapeJavascript($this->toObject->getTitle()) ?>'
    };
  <?php endif; ?>
  
  function removeFromToValue(id) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = $('toValues').value;
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
    if ($('toValues').value==""){
      $('toValues-wrapper').setStyle('height', '0');
    }

    $('item_name').disabled = false;
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    $('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
    if( !isPopulated ) { // NOT POPULATED
		var push =  new Autocompleter.Request.JSON('item_name', '<?php echo $this->url(array('action' => 'suggest', 'contestId'=>$this->contest->getIdentity() ), 'yncontest_mysetting', true) ?>', {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
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
          if( $('toValues').value.split(',').length >= maxRecipients ){
            $('item_name').disabled = true;
          }
        }
      });

      
      new Composer.OverText($('item_name'), {
        'textOverride' : '<?php echo $this->translate('Choose name...') ?>',
        'element' : 'label',
        'isPlainText' : true,
        'positionOptions' : {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });

    } else { // POPULATED

      var myElement = new Element("span", {
        'id' : 'tospan' + to.id,
        'class' : 'tag tag_' + to.type,
        'html' :  to.title /* + ' <a href="javascript:void(0);" ' +
                  'onclick="this.parentNode.destroy();removeFromToValue("' + toID + '");">x</a>"' */
      });
      $('to-element').appendChild(myElement);
      $('to-wrapper').setStyle('height', 'auto');

      // Hide to input?
      $('item_name').setStyle('display', 'none');
      $('toValues-wrapper').setStyle('display', 'none');
    }
  });
</script>

<?php

    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>
<ui id="">	
		<div class="form-wrapper" id="item_name-wrapper"><div class="form-label" id="item_name-label"><label class="optional" for="item_name">Search</label></div>
		<div class="form-element" id="item_name-element">
		<input type="text" value="" id="item_name" name="item_name"></div></div>
		<div class="form-wrapper" id="toValues-wrapper"><div class="form-label" id="toValues-label">&nbsp;</div>
		<div class="form-element" id="toValues-element">
		<input type="hidden" id="toValues" value="" name="toValues"></div></div>	
</ui>
<ul id="list_member" class='members_browse members_list_tab'>
    <?php $viewer = Engine_Api::_()->user()->getViewer();
    foreach( $this->paginator as $member ):?>
      <li>
        <div class = "member_info_left">
          <div class="ynContest_membersPhoto">
          <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.normal')) ?>		           
           
          </div> 
           <div class="members_info">
            <div class = "members_name"><?php echo $member->full_name?></div>            
       
             
            <div class = "members_title"><?php echo $member->summary?></div>
          </div> 
        </div>
        
        <div class="members_value">
        	<div>
        		<?php echo $this->translate('Approve date:')?> <?php echo $member->approve_date?>
        	</div>
        	<!-- <div>
        		<?php echo $this->translate('Entries:')?> 
        		<?php 
        		$table = Engine_Api::_()->getItemTable('yncontest_entries');
        		$entries = $table->getEntriesContest(array('contestID'=>$member->contest_id, 'user_id'=>$member->user_id));
        		echo count($entries);
        			?>
        	</div> -->
        </div>
        
      </li>
    <?php endforeach; ?>
</ul>
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>


