<?php if ($this->error) : ?>
<div class="tip">
    <span><?php echo $this->message?></span>
</div>
<?php else :?>
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">

  window.addEvent('domready', function() {
  	
  	var number_industry = 1;
  	$('add_more').addEvent('click', function(event) {
  		addMoreAddInfo(this, event);
  	}); 	
  	function addMoreAddInfo(obj, e) {
        e.preventDefault();
        var oriAddInfo = $('industry_id-wrapper');
        var newAddInfo = oriAddInfo.clone(true);
        var children = newAddInfo.getChildren();
        children[0].getChildren()[0].destroy();
        children[1].getChildren()[0].set('name','sub_industry[]');
        children[1].getChildren()[0].set('class','sub_industry btn_form_inline');
        var remove = new Element('a', {
            href: 'javascript:void(0)',
            html: '',
            'class': 'fa fa-minus-circle remove-add-info',
            events : {
                click: function(event) {
                    event.preventDefault();
                    this.getParent('.form-wrapper').destroy();
                    number_industry--;
                    if(number_industry < 3)
        			{
        				var oriAddInfo = $('add_more');
        				if(oriAddInfo)
        				{
        					oriAddInfo.setStyle('display', 'inline-block');
        				}	
        			}
                }
            }
        });
        newAddInfo.getElement('.description a').destroy();
        newAddInfo.getElement('.description').grab(remove);
        newAddInfo.inject('industry_id-wrapper', 'after');
        number_industry++;
        if(number_industry == 3)
        {
        	var oriAddInfo = $('add_more');
        	oriAddInfo.setStyle('display', 'none');
        }
        return newAddInfo;
    }
  	
  	<?php foreach($this -> sub_industries as $sub_industry) :?>
  	    var event = document.createEvent('Event');
  		var newAddInfo = addMoreAddInfo(null, event);
  		var children = newAddInfo.getChildren();
  		children[1].getChildren()[0].set('value', '<?php echo $sub_industry->industry_id ?>');
  		if(number_industry == 3)
        {
        	var oriAddInfo = $('add_more');
        	if(oriAddInfo)
        	{
        		oriAddInfo.setStyle('display', 'none');
        	}
        }
  	<?php endforeach;?>
  	
	$('industry_id').addEvent('change', function(){
      $(this).getParent('form').submit(); 
    }); 
  	
  	if($('0_0_1-wrapper'))
  	{
  		$('0_0_1-wrapper').setStyle('display','none');
  	}
  	<?php if($this -> number_add_more_index != 0) :?>
  		var index = <?php echo $this -> number_add_more_index;?>;
  	<?php else:?>
  		var index = 1;
  	<?php endif;?>
  	// add more addtional info
  	
  	$('add_more_info').addEvent('click', function(){
  		this.setStyle('display', 'none');
  		index = index + 1;
  	 	var url = '<?php echo $this->url(array('controller' => 'company','action' => 'add-info'), 'ynjobposting_extended', true) ?>';
  		var request = new Request.HTML({
	      url : url,
	      data : {
	        'type' : 'ajax',
	        'index' : index,
	      },
	      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
	           var el = document.createElement( 'div' );
			   el.innerHTML = responseHTML;
			   var sub_html  = el.getElementsByClassName( 'form-elements' )[0];
	           var str_sub_html = nodeToString(sub_html)
	           var infoHtml = Elements.from(str_sub_html);
			   var optionParent = $('number_add_more-wrapper');
			   infoHtml.inject(optionParent);
			   
			   tinymce.init({ mode: "exact", elements: "content_" + index, plugins: "table,fullscreen,media,preview,paste,code,image,textcolor", theme: "modern", menubar: false, statusbar: false, toolbar1: "undo,|,redo,|,removeformat,|,pastetext,|,code,|,media,|,image,|,link,|,fullscreen,|,preview", toolbar2: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,bullist,numlist,|,outdent,indent,blockquote", toolbar3: "", element_format: "html", height: "225px", convert_urls: false, language: "en", directionality: "ltr" });
			   $('add_more_info').setStyle('display', 'inline-block');
			   $('number_add_more_index').set('value', index);
			   $('number_add_more').set('value', parseInt($('number_add_more').value) + 1);
			   
				$$('.remove_content').addEvent('click', function(){
					var removeParent = this.getParent().getParent().getParent();
					$('number_add_more').set('value', $('number_add_more').value - 1);
					removeParent.destroy();
				});
				
	        }
	    });
   		request.send();
    });
  	
  	<?php if(!empty($this -> posts)) :?>
	  		$$('.sub_industry').each(function(el) {
	  			el.getParent().getParent().destroy();
	  		});
  	<?php endif;?>
  	
  	<?php if(!empty($this -> posts['sub_industry'])):?>
  		number_industry = 1;
	  	<?php foreach($this -> posts['sub_industry'] as $sub_industry_id) :?>
			var event = document.createEvent('Event');
	  		var newAddInfo = addMoreAddInfo(null, event);
	  		var children = newAddInfo.getChildren();
	  		children[1].getChildren()[0].set('value', '<?php echo $sub_industry_id ?>');
	  		if(number_industry == 3)
	        {
	        	var oriAddInfo = $('add_more');
	        	if(oriAddInfo)
	        	{
	        		oriAddInfo.setStyle('display', 'none');
	        	}
	        }
		<?php endforeach;?>
	<?php endif;?>
	
	<?php if(!empty($this -> posts['location'])) :?>
		$('location').set('value', '<?php echo $this -> posts['location'];?>');
	<?php endif;?>
  	
  });
  
  function nodeToString ( node ) {
	   var tmpNode = document.createElement( "div" );
	   tmpNode.appendChild( node.cloneNode( true ) );
	   tmpNode.set('class', 'form-elements');
	   var str = tmpNode.innerHTML;
	   tmpNode = node = null; // prevent memory leaks in IE
	   return str;
  }
  
  function removeSubmit()
  {
   $('buttons-wrapper').hide();
  }
</script>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
      'topLevelId' => (int) @$this->topLevelId,
      'topLevelValue' => (int) @$this->topLevelValue
    ))
?>

<?php
	$allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
	
	$editorOptions = array(
      'html' => (bool) $allowed_html,
    );
	
	$editorOptions['plugins'] = array(
        'table', 'fullscreen', 'media', 'preview', 'paste',
        'code', 'image', 'textcolor', 'jbimages', 'link'
      );

	  $editorOptions['toolbar1'] = array(
	    'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
	    'media', 'image', 'jbimages', 'link', 'fullscreen',
	    'preview'
	  );
?>

<?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");?>
<div class='global_form'>
  <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">
 var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
  en4.core.runonce.add(function()
  {
   if($('text'))
    {
      new OverText($('text'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }
  });
  
  function initialize() {
	 	var input = /** @type {HTMLInputElement} */(
			document.getElementById('location'));
	
	  	var autocomplete = new google.maps.places.Autocomplete(input);
	
	  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
	    	var place = autocomplete.getPlace();
		    if (!place.geometry) {
		     	return;
		    }
			document.getElementById('location_address').value = place.formatted_address;		
			document.getElementById('lat').value = place.geometry.location.lat();		
			document.getElementById('long').value = place.geometry.location.lng();
	    });
	}
  
   google.maps.event.addDomListener(window, 'load', initialize); 
  
  var getCurrentLocation = function(obj)
	{	
		if(navigator.geolocation) {
			
	    	navigator.geolocation.getCurrentPosition(function(position) {
	    			
	      	var pos = new google.maps.LatLng(position.coords.latitude,
	                                       position.coords.longitude);
	        
			if(pos)
			{
				current_posstion = new Request.JSON({
					'format' : 'json',
					'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynjobposting_general') ?>',
					'data' : {
						latitude : pos.lat(),
						longitude : pos.lng(),
					},
					'onSuccess' : function(json, text) {
						
						if(json.status == 'OK')
						{
							document.getElementById('location').value = json.results[0].formatted_address;
							document.getElementById('location_address').value = json.results[0].formatted_address;
							document.getElementById('lat').value = json.results[0].geometry.location.lat;		
							document.getElementById('long').value = json.results[0].geometry.location.lng; 		
						}
						else{
							handleNoGeolocation(true);
						}
					}
				});	
				current_posstion.send();
				
			}
	      	
	    	}, function() {
	      		handleNoGeolocation(true);
	    	});
	  	}
	  	else {
    		// Browser doesn't support Geolocation
    		handleNoGeolocation(false);
  		}
		return false;
	}
	
	function handleNoGeolocation(errorFlag) {
  		if (errorFlag) {
    		document.getElementById('location').value = 'Error: The Geolocation service failed.';
  		} 
  		else {
   			document.getElementById('location').value = 'Error: Your browser doesn\'t support geolocation.';
   		}
 	}
</script>
<?php endif; ?>