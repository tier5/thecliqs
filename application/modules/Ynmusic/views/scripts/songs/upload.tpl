<?php
$this -> headScript() 
		-> appendFile($this -> layout() -> staticBaseUrl . 'externals/autocompleter/Observer.js') 
		-> appendFile($this -> layout() -> staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/AutocompleterExtend.js')
		-> appendFile($this -> layout() -> staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/Autocompleter.Local.js')
		-> appendFile($this -> layout() -> staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/Autocompleter.Request.js')
		-> appendFile($this -> layout() -> staticBaseUrl . 'externals/autocompleter/Autocompleter.js') 
		-> appendFile($this -> layout() -> staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js') -> appendFile($this -> layout() -> staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php
	require_once APPLICATION_PATH . '/application/modules/Ynmusic/Libs/Soundcloud.php';	
	$setting = Engine_Api::_()->getApi('settings', 'core');
	$cliendId = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_sound_clientid', "");
	$cliendSecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_sound_clientsecret', "");
	
	if(!empty($cliendId) && !empty($cliendSecret)){
		$canSoundCloud = true;
	} else {
		$canSoundCloud = false;
	}
?>	

<div class='global_form'>
<?php    
	 echo $this->form->render($this);
 ?> 
</div>
<script type="text/javascript">

function createPopupNotice(text) {
	var div = new Element('div', {
       'class': 'ynresume-confirm-popup' 
    });
    var p = new Element('p', {
        'class': 'ynmusic-confirm-message',
        text: text,
    });
    var button = new Element('button', {
        text: '<?php echo $this->translate('OK')?>',
        onclick: 'parent.Smoothbox.close();'
    });
    div.grab(p);
	div.grab(button);
	Smoothbox.open(div);
}

var _validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];    

function checkImageExtention(element) {
	 if (element.type == "file") {
        var sFileName = element.value;
        if (sFileName.length > 0) {
            var blnValid = false;
            for (var j = 0; j < _validFileExtensions.length; j++) {
                var sCurExtension = _validFileExtensions[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                }
            }
            return blnValid;
        } else {
        	return true;
        }
    }
}

function validateFormCreate(event) {
	var album_id = $('album_id').get('value');
    if (album_id == 'create') {
    	var date = ($$('input[name="released_date[date]"]')[0]).get('value');
    	if (date) {
    		var hour = ($$('select[name="released_date[hour]"]')[0]).get('value');
    		var minute = ($$('select[name="released_date[minute]"]')[0]).get('value');
    		if (!hour || !minute) {
    			createPopupNotice("<?php echo $this -> translate("Please select time for released date.");?>");
    			return false;
    		}
    	}
    }
    
    var photoInput = $('photo');
    var coverInput = $('cover');
    
    if(!checkImageExtention(photoInput)) {
    	createPopupNotice("<?php echo $this -> translate("Image Photo is invalid.");?>");
    	return false;
    }
    if(!checkImageExtention(coverInput)) {
    	createPopupNotice("<?php echo $this -> translate("Image Cover is invalid.");?>");
    	return false;
    }
    if (!$('ynmusic-confirm-create').checked) {
        createPopupNotice("<?php echo $this -> translate("Please check the Terms And Conditions.");?>");
    	return false;
    }
    var valueIds = $('html5uploadfileids').get('value');
    <?php if($canSoundCloud) :?>
    	var valueSoundcloud = $('songcloud_count').get('value');
    <?php else :?>
    	var valueSoundcloud = "";
    <?php endif;?>
	if(valueIds.trim() == "") {
		if(valueSoundcloud.trim() == "") {
			 createPopupNotice("<?php echo $this -> translate("Please add at least one song.");?>");
    		return false;
		}
		else {
			var flag = true;
			for (i = 1; i <= valueSoundcloud.trim(); i++) { 
				if($('soundcloud_value'+i))
				{
			   	 	if($('soundcloud_value'+i).get('value') != "") {
			   	 		flag = false;
			   	 		break;
			   	 	}
			   	}
			}
			if(flag){
				createPopupNotice("<?php echo $this -> translate("Please add at least one song.");?>");
	    		return false;
    		}
		}
		
	}
	event.preventDefault();
    var form = $('form-upload-music');
	if (!form) return false;
	var params = form.toQueryString().parseQueryString();
	new Request.JSON({
        url : '<?php echo $this->url(array('action'=>'validate-song-count'), 'ynmusic_song', true)?>',
        data : params,
        async: false,
        onSuccess : function(responseJSON) {
           if (responseJSON.status == true){
				form.submit();
	        } else {
	        	createPopupNotice(responseJSON.message);
	        	return false;
	        }
        }
    }).send();
}


function readURL(id, idPreview) {
	var input = $(id);
	if (input.files && input.files[0]) {
	    var reader = new FileReader();
	    
	    reader.onload = function (e) {
	        $(idPreview).set('src', e.target.result);
	            }
	            
	            reader.readAsDataURL(input.files[0]);
	        }
	    }
	  
  function uploadPhoto(id, idPreview){
  	readURL(id, idPreview);
  }

function remove_tags(html)
{
   return html.replace(/<(?:.|\n)*?>/gm, '');
}

// Populate data
var maxGenres = 3;
var maxRecipients = 0;
var to = {
    id : false,
    type : false,
    guid : false,
    title : false
};
    
function addNewChoice(element, toValue) {
	
	var id = element.get('id');
	var value = element.get('value');
	value = value.trim();
 	value = remove_tags(value);
	if(value == "") {
 		return true;
 	}
 	
 	var toValueElement = $(toValue);
 	var arr = toValueElement.get('value').split(',');
 	arr = arr.filter(function(e){ return e === 0 || e });
 	
 	if (arr.indexOf(value) != -1) {
 		element.set('value', "");
 		return false;
 	}
 	
  	var spanClass = ((id.indexOf('genre') != -1)) ? 'genre' : 'artist';
  	var myElement = new Element("span", {
        'id' : toValue+'_tospan_' + value,
        'class': spanClass+'_tag'
    });
    var link = new Element("a", {
    	'target': '_blank',
    	href: 'javascript:void(0)',
    	text: value
    });
    var close = new Element("a" , {
    	href: 'javascript:void(0)',
    	html: "<i class='fa fa-times'></i>",
		events: {
			click: function(){
	            this.parentNode.destroy();
	            removeFromToValue(value, toValue, id);
	        }
		}
	});
	myElement.adopt(link, close);
    
    arr.push(value);
    
    toValueElement.set('value', arr.join(','));
    
    $(toValue+'-element').grab(myElement);
    $(toValue+'-wrapper').show();
    $(toValue+'-wrapper').setStyle('height', 'auto');
    element.set('value', "");
    
    if((id.indexOf('genre') != -1) && (maxGenres != 0) && (arr.length >= maxGenres) ){
        element.hide();
        $(id+'-element').getElement('p.description').set('text', "<?php echo $this->translate('Maximum number of genres reached')?>");
    }
	return false;
}

window.addEvent('domready', function(){
	$('downloadable-wrapper').setStyle('display','none');
	if ($('album_id')) {
		var value = $('album_id').get('value');
		if(value == "create"){
			//create new album
			$('title-wrapper').setStyle('display','block');
			$('description-wrapper').setStyle('display','block');
			$('released_date-wrapper').setStyle('display','block');
			$('photo-wrapper').setStyle('display','block');
			$('cover-wrapper').setStyle('display','block');
			$('album_auth_view-wrapper').setStyle('display','block');
			$('album_auth_comment-wrapper').setStyle('display','block');
			if($('album_auth_download-wrapper'))
				$('album_auth_download-wrapper').setStyle('display','block');
			$('tags-wrapper').setStyle('display','block');
			
			$('song_auth_view-wrapper').setStyle('display','none');
			$('song_auth_comment-wrapper').setStyle('display','none');
			
		} else if (value == "none"){
			//create standalone song
			$('title-wrapper').setStyle('display','none');
			$('description-wrapper').setStyle('display','none');
			$('released_date-wrapper').setStyle('display','none');
			$('photo-wrapper').setStyle('display','none');
			$('cover-wrapper').setStyle('display','none');
			$('album_auth_view-wrapper').setStyle('display','none');
			$('album_auth_comment-wrapper').setStyle('display','none');
			if($('album_auth_download-wrapper'))
				$('album_auth_download-wrapper').setStyle('display','none');
			
			$('genre-wrapper').setStyle('display','block');
			$('artist-wrapper').setStyle('display','block');
			$('tags-wrapper').setStyle('display','block');
			$('song_auth_view-wrapper').setStyle('display','block');
			$('song_auth_comment-wrapper').setStyle('display','block');
			if($('song_auth_download-wrapper'))
				$('song_auth_download-wrapper').setStyle('display','block');
			
			//clear data photo & cover
			$('photo').set('value','');
			$('uploadPreviewMain').set('src','');
			$('cover').set('value','');
			$('uploadPreviewCover').set('src','');
			
		} else {
			//upload to existing album
			$('title-wrapper').setStyle('display','none');
			$('description-wrapper').setStyle('display','none');
			$('released_date-wrapper').setStyle('display','none');
			$('photo-wrapper').setStyle('display','none');
			$('cover-wrapper').setStyle('display','none');
			$('genre-wrapper').setStyle('display','none');
			$('artist-wrapper').setStyle('display','none');
			$('tags-wrapper').setStyle('display','none');
			$('song_auth_view-wrapper').setStyle('display','none');
			$('song_auth_comment-wrapper').setStyle('display','none');
			$('album_auth_view-wrapper').setStyle('display','none');
			$('album_auth_comment-wrapper').setStyle('display','none');
			if($('album_auth_download-wrapper'))
				$('album_auth_download-wrapper').setStyle('display','none');
			
			//clear data photo & cover
			$('photo').set('value','');
			$('uploadPreviewMain').set('src','');
			$('cover').set('value','');
			$('uploadPreviewCover').set('src','');
		}
	}

	$('album_id').addEvent('change', function(){
		var value = this.get('value');
		if(value == "create"){
			//create new album
			$('title-wrapper').setStyle('display','block');
			$('description-wrapper').setStyle('display','block');
			$('released_date-wrapper').setStyle('display','block');
			$('photo-wrapper').setStyle('display','block');
			$('cover-wrapper').setStyle('display','block');
			$('album_auth_view-wrapper').setStyle('display','block');
			$('album_auth_comment-wrapper').setStyle('display','block');
			if($('album_auth_download-wrapper'))
				$('album_auth_download-wrapper').setStyle('display','none');
			$('tags-wrapper').setStyle('display','block');
			
			$('song_auth_view-wrapper').setStyle('display','none');
			$('song_auth_comment-wrapper').setStyle('display','none');
			if($('song_auth_download-wrapper'))
				$('song_auth_download-wrapper').setStyle('display','block');
			
		} else if (value == "none"){
			//create standalone song
			$('title-wrapper').setStyle('display','none');
			$('description-wrapper').setStyle('display','none');
			$('released_date-wrapper').setStyle('display','none');
			$('photo-wrapper').setStyle('display','none');
			$('cover-wrapper').setStyle('display','none');
			$('album_auth_view-wrapper').setStyle('display','none');
			$('album_auth_comment-wrapper').setStyle('display','none');
			if($('album_auth_download-wrapper'))
				$('album_auth_download-wrapper').setStyle('display','none');
			
			$('genre-wrapper').setStyle('display','block');
			$('artist-wrapper').setStyle('display','block');
			$('song_auth_view-wrapper').setStyle('display','block');
			$('song_auth_comment-wrapper').setStyle('display','block');
			if($('song_auth_download-wrapper'))
				$('song_auth_download-wrapper').setStyle('display','block');
			$('tags-wrapper').setStyle('display','block');
			
			//clear data photo & cover
			$('photo').set('value','');
			$('uploadPreviewMain').set('src','');
			$('cover').set('value','');
			$('uploadPreviewCover').set('src','');
			
		} else {
			//upload to existing album
			$('title-wrapper').setStyle('display','none');
			$('description-wrapper').setStyle('display','none');
			$('released_date-wrapper').setStyle('display','none');
			$('photo-wrapper').setStyle('display','none');
			$('cover-wrapper').setStyle('display','none');
			$('genre-wrapper').setStyle('display','none');
			$('artist-wrapper').setStyle('display','none');
			$('tags-wrapper').setStyle('display','none');
			$('song_auth_view-wrapper').setStyle('display','none');
			$('song_auth_comment-wrapper').setStyle('display','none');
			if($('song_auth_download-wrapper'))
				$('song_auth_download-wrapper').setStyle('display','block');
			$('album_auth_view-wrapper').setStyle('display','none');
			$('album_auth_comment-wrapper').setStyle('display','none');
			if($('album_auth_download-wrapper'))
				$('album_auth_download-wrapper').setStyle('display','none');
			
			//clear data photo & cover
			$('photo').set('value','');
			$('uploadPreviewMain').set('src','');
			$('cover').set('value','');
			$('uploadPreviewCover').set('src','');
		}
	});
	
  new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>
		', {
		'postVar' : 'text',
		'customChoices' : true,
		'minLength': 1,
		'selectMode': 'pick',
		'autocompleteType': 'tag',
		'className': 'tag-autosuggest',
		'filterSubset' : true,
		'multiple' : true,
		'injectChoice': function(token){
		var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
		new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
		choice.inputValue = token;
		this.addChoiceEvents(choice).inject(this.choices);
		choice.store('autocompleteChoice', token);
		}
	});
	
	
	 //for genres autocomplete
    new Autocompleter2.Request.JSON('genre', '<?php echo $this->url(array('action' => 'suggest-genre'), 'ynmusic_general', true) ?>', {
            'toValues': 'genre_ids',
            'minLength': 1,
            'delay' : 250,
            'autocompleteType' : 'message',
            'multiple': true,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'injectChoice': function(token){
                var choice = new Element('li', {
                    'class': 'autocompleter-choices',
                    'id':token.label
                });
                new Element('div', {
                    'html': this.markQueryValue(token.label),
                    'class': 'autocompleter-choice'
                }).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            },
            onPush : function(){
                if((maxGenres != 0) && (document.getElementById('genre_ids').value.split(',').length >= maxGenres) ){
                    document.getElementById('genre').style.display = 'none';
                }
            }
    });
    
     //for artist autocomplete
    new Autocompleter2.Request.JSON('artist', '<?php echo $this->url(array('action' => 'suggest-artist'), 'ynmusic_general', true) ?>', {
            'toValues': 'artist_ids',
            'minLength': 1,
            'delay' : 250,
            'autocompleteType' : 'message',
            'multiple': true,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'injectChoice': function(token){
                var choice = new Element('li', {
                    'class': 'autocompleter-choices',
                    'id':token.label
                });
                new Element('div', {
                    'html': this.markQueryValue(token.label),
                    'class': 'autocompleter-choice'
                }).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            },
            onPush : function(){
                if((maxRecipients != 0) && (document.getElementById('artist_ids').value.split(',').length >= maxRecipients) ){
                    document.getElementById('genre').style.display = 'none';
                }
            }
    });
});	

	 function removeFromToValue(id, hideLoc, elem) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = document.getElementById(hideLoc).value;
        var toValueArray = toValues.split(",");
        var toValueIndex = "";

        var checkMulti = id.search(/,/);

        // check if we are removing multiple recipients
        if (checkMulti!=-1){
            var recipientsArray = id.split(",");
            for (var i = 0; i < recipientsArray.length; i++){
                removeToValue(recipientsArray[i], toValueArray, hideLoc);
            }
        }
        else{
            removeToValue(id, toValueArray, hideLoc);
        }

        // hide the wrapper for usernames if it is empty
        if (document.getElementById(hideLoc).value==""){
            document.getElementById(hideLoc+'-wrapper').style.height = '0';
            document.getElementById(hideLoc+'-wrapper').hide();
        }

        document.getElementById(elem).style.display = 'block';
        
        if (elem.indexOf('genre') != -1) {
        	$(elem+'-element').getElement('p.description').set('text', "<?php echo $this->translate('Can add up to 3 genres. Press \'Enter\' to input data')?>");
        }
    }

    function removeToValue(id, toValueArray, hideLoc){
        for (var i = 0; i < toValueArray.length; i++){
            if (toValueArray[i]==id) toValueIndex =i;
        }

        toValueArray.splice(toValueIndex, 1);
        document.getElementById(hideLoc).value = toValueArray.join();
    }
		
	function getSoundCloudInfo(url){
	  var regexp = /^https?:\/\/(soundcloud.com|snd.sc)\/(.*)$/;
	  return url.match(regexp) && url.match(regexp)[2]
	}
	
	<?php if($canSoundCloud) :?>
		var soundcloudID = 0;
		
		$('add_more_soundcloud').addEvent('click', function() {
			
			soundcloudID = parseInt(soundcloudID) + 1;
			$('songcloud_count').set('value', soundcloudID);
			
			var idTitle = 'soundcloud_title' + soundcloudID;
			var idAction = 'soundcloud_' + soundcloudID;
       		var idValue = 'soundcloud_value' + soundcloudID;
       		var idEdit = 'soundcloud_edit' + soundcloudID;
       		var idSave = 'soundcloud_save' + soundcloudID;
       		
        	var inputAction = new Element('input', {
        		'type' : 'text',
			    'class': 'myClass',
			    'id': idAction,
			});
			
			var inputTitle = new Element('input', {
				'styles': {
			        display: 'none',
			    },
        		'type' : 'text',
			    'class': 'myClass',
			    'id': idTitle,
			});
			
			var inputValue = new Element('input', {
				'styles': {
			        display: 'none',
			    },
			    'id': idValue,
			    'name': idValue,
        		'type' : 'text',
			    'class': 'myClass',
			});
			
			var save = new Element('a', {
	            href: 'javascript:void(0)',
	            'class': 'soundcloud-btn save',
	            html: 'Save',
	            id: idSave,
	            title: '<?php echo $this->translate('Save')?>'
	        });
			
			var remove = new Element('a', {
	            href: 'javascript:void(0)',
	            'class': 'soundcloud-btn remove',
	            html: 'Delete',
	            title: '<?php echo $this->translate('Delete')?>',
	            events : {
	                click: function(event) {
	                    this.getParent('.add_more_soundcloud_item').destroy();
	                }
	            }
	        });
	        
	        var edit = new Element('a', {
	            href: 'javascript:void(0)',
	            'class': 'soundcloud-btn edit',
	            html: 'Edit',
	            'styles': {
			        display: 'none',
			    },
			    'id':idEdit,
			    title: '<?php echo $this->translate('Edit')?>',
	            events : {
	                click: function(event) {
	                    event.preventDefault();
	                    $(idTitle).setStyle('display', 'none');
	                    $(idAction).setStyle('display', 'block');
	                    $(idSave).setStyle('display', 'block');
	                    this.setStyle('display', 'none');
	                }
	            }
	        });
	        
	        var btn = new Element('div', {
        		'class': "add_more_soundcloud_btn"
        	});
        	btn.adopt(save, edit, remove);
        	
	        var add_more_soundcloud_item = new Element('div', {
        		'class': "add_more_soundcloud_item"
        	});
        	
        	add_more_soundcloud_item.adopt(inputValue, inputTitle, inputAction, btn);
		
			add_more_soundcloud_item.inject('add_more_soundcloud', 'before');
			addEventSoundCloud(idAction, idValue, idTitle, idEdit, idSave);
			
		});
		
		
		function addEventSoundCloud(idAction, idValue, idTitle, idEdit, idSave)
		{	
			$(idSave).addEvent('click', function() {
				
				var url = $(idAction).get('value');
				var urlJSValidate = getSoundCloudInfo(url);
				
				if(urlJSValidate){
					var validationUrl = '<?php echo $this->url(array('action' => 'validate'), 'ynmusic_general', true) ?>';
					(new Request.JSON({
			            'url' : validationUrl,
			            'data' : {
			              'ajax' : true,
			              'url' : url,
			              'idAction' : idAction,
			              'idValue' : idValue,
			              'idTitle' : idTitle,
			              'idEdit' : idEdit,
			              'idSave' : idSave,
			            },
			            'onSuccess' : function(responseJSON) {
			               if (responseJSON.status == "true"){
								$(responseJSON.idTitle).set('value', responseJSON.title);			               		
			               		$(responseJSON.idValue).set('value', responseJSON.permalink);
			               		$(responseJSON.idEdit).setStyle('display','block');
			               		$(responseJSON.idAction).setStyle('display','none');
			               		$(responseJSON.idTitle).setStyle('display','block');
			               		$(responseJSON.idSave).setStyle('display','none');
					        } else {
					        	createPopupNotice("<?php echo $this -> translate("Could not find song.");?>");
    							return false;
					        }
			            }
		            })).send();
				}
				else {
					createPopupNotice("<?php echo $this -> translate("You are using an invalid URL. Please put a valid one from Soundcloud and try again.");?>");
					return false;
				}
	        });
       }
   <?php endif;?>
</script>

<script>
	window.addEvent('domready', function() {
		var elements = ['info_privacy_header-wrapper', 'upload_song_header-wrapper', 'upload_song_soundcloud-wrapper'];
		elements.each(function(el) {
			element = $(el);
			if (element) {
				element.addEvent('click', function() {
					var next = this.getNext('.form-wrapper');
					next.toggle();
					if (this.hasClass('ynmusic-collapsed')) {
						this.removeClass('ynmusic-collapsed');	
					}
					else {
						this.addClass('ynmusic-collapsed');	
					} 
				});
			}
		});
		if($$('div.event_calendar').length)
			$$('div.event_calendar')[0].style.left = 0;
	});
</script>