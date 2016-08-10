<?php
  if (APPLICATION_ENV == 'production')
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
  else
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
  // Populate data
  var maxRecipients = 1;
  var maxSend = 0;
  var user_id = 0;
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
  
  function removeFromToValue(id) 
  {
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
    if (document.getElementById('toValues').value=="")
    {
      document.getElementById('toValues-wrapper').style.height = '0';
    }
	 $('to-label').getFirst('label').innerHTML = '<?php echo $this -> string()->escapeJavascript($this -> translate("Your friend name"));?>';
     $('to-element').style.display = 'block';
     $('send_credit-element').style.display = 'block';
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    document.getElementById('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
    if( !isPopulated ) 
    { // NOT POPULATED
      new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest'), 'default', true) ?>', {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': false,
        'className': 'message-autosuggest',
        'filterSubset' : true,
        'tokenFormat' : 'object',
        'tokenValueKey' : 'label',
        'injectChoice': function(token)
        {
          if(token.type == 'user')
          {
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
          else 
          {
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
        onPush : function()
        {
          if( document.getElementById('toValues').value.split(',').length >= maxRecipients )
          {
          	user_id = document.getElementById('toValues').value;
          	//check max credit can send credit (min sender and receiver)
          	var request = new Request.JSON({
	            'method' : 'post',
	            'url' :  '<?php echo $this->url(array('action' => 'check-send-credit'), 'yncredit_general') ?>',
	            'data' : {
	                'user_id' : user_id,
	            },
	            'onComplete':function(responseObject)
	            {  
	               	$('to-label').getFirst('label').innerHTML = responseObject.message;
	               	if(responseObject.fail == 0)
	               	{
	               		maxSend = responseObject.max;
	               		$('send_credit-element').style.display = 'block';
	               	}
	               	else
	               	{
	               		$('send_credit-element').style.display = 'none';
	               	}
	            }
	        });
	        request.send();	
            $('to-element').style.display = 'none';
          }
        }
      });
      
      new Composer.OverText($('to'), {
        'textOverride' : '<?php echo $this->translate('Start typing...') ?>',
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

    } 
    else 
    { // POPULATED
      var myElement = new Element("span", {
        'id' : 'tospan' + to.id,
        'class' : 'tag tag_' + to.type,
        'html' :  to.title
      });
      $('to-element').appendChild(myElement);
      $('to-wrapper').setStyle('height', 'auto');

      // Hide to input?
      $('to').setStyle('display', 'none');
      $('toValues-wrapper').setStyle('display', 'none');
    }
  });
  function onlyNumbers(evt) 
  {
    var e = evt;
    if(window.event){ // IE
        var charCode = e.keyCode;
    } else if (e.which) { // Safari 4, Firefox 3.0.4
        var charCode = e.which
    }
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;
    
    return true;
  }
  var sendCredit = function()
  {
  	var credits = 0;
  	if($('credit').value)
  		credits = parseFloat($('credit').value);
    var url = '<?php echo $this->url(array('action' => 'send-credit'), 'yncredit_general')?>/user_id/'+user_id +'/credits/' + credits;
    Smoothbox.open(url);
    return false;
  }
</script>

<?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>
<div class="yncredit-container">
    <div class=""><?php echo $this -> translate("Need never be out of touch with friends? You can send credit to your friends when they are running low"); ?></div>
    <?php echo $this -> form -> render();?>
</div>
