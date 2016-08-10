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
$staticBaseUrl = $this->layout()->staticBaseUrl;
		$this->headScript()
  	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/jquery.js')	
		->appendScript('jQuery.noConflict();')
  	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/vendor/jquery.ui.widget.js')	
  	->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.iframe-transport.js')
		->appendFile($staticBaseUrl . 'application/modules/Ynmusic/externals/scripts/js/jquery.fileupload.js')	
		->appendFile('//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js');	
?>


<div class='global_form'>
<?php    
	 echo $this->form->render($this);
 ?> 
</div>
<script type="text/javascript">

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

function validateFormCreate(photoId, coverId) {
    var photoInput = $(photoId);
    var coverInput = $(coverId);
    if(!checkImageExtention(photoInput)) {
    	createPopupNotice("<?php echo $this -> translate("Image Photo is invalid.");?>");
    	return false;
    }
    if(!checkImageExtention(coverInput)) {
    	createPopupNotice("<?php echo $this -> translate("Image Cover is invalid.");?>");
    	return false;
    }
    return true;
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

function remove_tags(html) {
   	return html.replace(/<(?:.|\n)*?>/gm, '');
}

// Populate data
var artist = "";
var genre = "";
var maxGenres = 3;
var maxArtists = 0;
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
        'class': spanClass+'_tag',
        'html' :  "<a target='_blank' href='javascript:void(0)'>"+value+"  </a><a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+encodeURI(value)+"\", \""+toValue+"\",\""+id+"\");'><i class='fa fa-times'></i></a>"
    });
    
    arr.push(value);
    
    toValueElement.set('value', arr.join(','));
    
    $(toValue+'-element').grab(myElement);
    $(toValue+'-wrapper').show();
    $(toValue+'-wrapper').setStyle('height', 'auto');
    element.set('value', "");
    
    if((id.indexOf('genre') != -1) && (maxGenres != 0) && (arr.length >= maxGenres) ){
        element.hide();
        $(id+'-element').getElement('p.description').set('text', '<?php echo $this->translate('Maximum number of genres reached')?>');
    }
	return false;
}
    
window.addEvent('domready', function(){
  $('downloadable-wrapper').setStyle('display','none');
  new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>',
	{
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
    
    <?php foreach ($this->genreMappings as $genreMapping) : ?>
    	<?php 
    		$genre_id = $genreMapping -> genre_id;
			$genre = Engine_Api::_() -> getItem('ynmusic_genre', $genre_id);
    	?>
    	<?php if($genre) :?>
    	    var genre_ids = $('genre_ids').get('value');
    	    if(genre_ids.trim() != "") {
    	    	genre_ids += ',' + '<?php echo $genre_id;?>';
    	    } else {
    	    	genre_ids = '<?php echo $genre_id;?>';
    	    }
    	    $('genre_ids').set('value', genre_ids);
    	    
	        var myElement = new Element("span", {
	            'id' : 'genre_ids_tospan_' + '<?php echo $genre->getIdentity()?>',
	            'class': 'user_tag',
	            'html' :  "<a target='_blank' href='<?php echo $genre->getHref()?>'>"+'<?php echo $this->string()->escapeJavascript($genre->getTitle())?>'+"</a> <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\"<?php echo $genre->getIdentity()?>\", \"genre_ids\",\"genre\");'><i class='fa fa-times'></i></a>"
	        });
	        document.getElementById('genre_ids-element').appendChild(myElement);
	        document.getElementById('genre_ids-wrapper').show();
	        document.getElementById('genre_ids-wrapper').style.height = 'auto';
	        
	        if((maxGenres != 0) && (document.getElementById('genre_ids').value.split(',').length >= maxGenres) ){
                document.getElementById('genre').style.display = 'none';
            }
         <?php endif;?>
    <?php endforeach; ?>
    if (document.getElementById('genre_ids').value == '') {
    	document.getElementById('genre_ids-wrapper').hide();
    }
    
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
                if((maxArtists != 0) && (document.getElementById('artist_ids').value.split(',').length >= maxArtists) ){
                    document.getElementById('artist').style.display = 'none';
                }
            }
    });
    
     <?php foreach ($this->artistMappings as $artistMapping) : ?>
    	<?php 
    		$artist_id = $artistMapping -> artist_id;
			$artist = Engine_Api::_() -> getItem('ynmusic_artist', $artist_id);
    	?>
    	<?php if($artist) :?>
    	    var artist_ids = $('artist_ids').get('value');
    	    if(artist_ids.trim() != "") {
    	    	artist_ids += ',' + '<?php echo $artist_id;?>';
    	    } else {
    	    	artist_ids = '<?php echo $artist_id;?>';
    	    }
    	    $('artist_ids').set('value', artist_ids);
    	    
	        var myElement = new Element("span", {
	            'id' : 'artist_ids_tospan_' + '<?php echo $artist->getIdentity()?>',
	            'class': 'user_tag',
	            'html' :  "<a target='_blank' href='<?php echo $artist->getHref()?>'>"+'<?php echo $this->string()->escapeJavascript($artist->getTitle())?>'+"</a> <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\"<?php echo $artist->getIdentity()?>\", \"artist_ids\",\"artist\");'><i class='fa fa-times'></i></a>"
	        });
	        document.getElementById('artist_ids-element').appendChild(myElement);
	        document.getElementById('artist_ids-wrapper').show();
	        document.getElementById('artist_ids-wrapper').style.height = 'auto';
	        
	        if((maxArtists != 0) && (document.getElementById('artist_ids').value.split(',').length >= maxArtists) ){
                document.getElementById('artist').style.display = 'none';
            }
         <?php endif;?>
    <?php endforeach; ?>
    if (document.getElementById('artist_ids').value == '') {
    	document.getElementById('artist_ids-wrapper').hide();
    }

    if($$('div.event_calendar').length)
        $$('div.event_calendar')[0].style.left = 0;
    
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
		
</script>