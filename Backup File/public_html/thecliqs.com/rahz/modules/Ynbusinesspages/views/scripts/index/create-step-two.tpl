<?php if ($this->error) : ?>
<div class="tip">
    <span><?php echo $this->message?></span>
</div>
<?php else :?>

<style>
    #to {
        width: 85%;
    }
</style>
	
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/Autocompleter.Request.js');
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
  	
  	function preview()
  	{	
  		//-----------------------main category-----------------------
  		var str_main_category = "";
	    var url = '<?php echo $this->url(array('action'=>'get-category'), 'ynbusinesspages_general') ?>';
	    new Request.JSON({
	        url: url,
	        method: 'post',
	        async: false,
	        data: {
	            'id': $('category_id').value,
	        },
	        onSuccess : function(responseJSON, responseText)
	        {
	            str_main_category = str_main_category + "<li>"+responseJSON.message+"</li>";
	        }
	    }).send();
	    
  		//-----------------------sub category-----------------------
  		var str_sub_category = "";
  		$$('.sub_category').each(function(el) {
		    var url = '<?php echo $this->url(array('action'=>'get-category'), 'ynbusinesspages_general') ?>';
		    new Request.JSON({
		        url: url,
		        method: 'post',
		        async: false,
		        data: {
		            'id': el.value,
		        },
		        onSuccess : function(responseJSON, responseText)
		        {
		            str_sub_category = str_sub_category + "<li>"+responseJSON.message+"</li>";
		        }
		    }).send();
  		});
  		
  		//-----------------------operating hours-----------------------
  		var str_operating_hours = "";
  		var arr_days = [
  			'monday',
	  		'tuesday',
	  		'wednesday',
	  		'thursday',
	  		'friday',
	  		'saturday',
	  		'sunday',
		];
		
		arr_days.forEach(function(entry) {
		   var str_temp = "";
		   var id_from = entry+'_from';
		   var id_to = entry+'_to';
		   
		   var value_id_from =  $(id_from).value;
     	   var value_id_to =  $(id_to).value;
     	   
     	   //if empty ignore this entry
     	   if(value_id_from != '' && value_id_to != '')
     	   {
	     	   str_temp = str_temp + "<li><span>"+capitaliseFirstLetter(entry)+"</span>"
	     	   					   + "<span>"
			   if(value_id_from == 'CLOSED')
			   {	     	
			     	str_temp = str_temp + '<?php echo $this->translate('CLOSED');?>';
			   }
			   else
			   {
			   		str_temp = str_temp + value_id_from + " - " + value_id_to;
			   }
			   str_temp = str_temp + "</li></span>";
			   str_operating_hours = str_operating_hours + str_temp;
		   }
  		});
  		
  		//-----------------------founders---------------------------
  		var str_founders = "";
  		$$('.tag').each(function(el) {
  			var str_temp = "";
  			var new_el = el.clone(true);
  			var children = new_el.getChildren();
  			if(children[1])
	       		children[1].destroy();
	        str_temp = str_temp + "<li>" + new_el.innerHTML + "</li>";
	        str_founders = str_founders + str_temp;
  		});
  		
  		//-----------------------phones---------------------------
  		var str_phone = "";
  		var arr_phone = [];
  		$$('.sub_phone').each(function(el) {
  			if(el.value != "")
  				arr_phone.push(el.value);
  		});
  		if($('phone').value != "")
  		{
  			arr_phone.push($('phone').value);
  		}
  		str_phone = arr_phone.join(', ');
  		
  		//-----------------------faxs---------------------------
  		var str_fax = "";
  		var arr_fax = [];
  		$$('.sub_fax').each(function(el) {
  			if(el.value != "")
  				arr_fax.push(el.value);
  		});
  		if($('fax').value != "")
  		{
  			arr_fax.push($('fax').value);
  		}
  		str_fax = arr_fax.join(', ');
  		
  		//-----------------------web address---------------------------
  		var str_web_address = "";
  		var arr_web_address = [];
  		$$('.sub_web_address').each(function(el) {
  			if(el.value != "")
  				arr_web_address.push(el.value);
  		});
  		if($('web_address').value != "")
  		{
  			arr_web_address.push($('web_address').value);
  		}
  		str_web_address = arr_web_address.join(', ');
  		
  		//-----------------------main location---------------------------
  		var str_main_location = "";
  		var main_lat_value = $('lat').value;
		var main_long_value = $('long').value;
		if(main_lat_value!= 0 && main_long_value != 0)
		{
  			str_main_location = $('location_title').value + '<div id="map-canvas-0" style="height: 220px; margin-top: 20px;"></div>';
  		}
  		else
  		{
  			str_main_location += $('location_title').value + '<div class="tip" style="margin: 10px"><span><?php echo $this->translate('Can not found the location.')?></span></div>';
  		}
  		//-----------------------sub location---------------------------
  		var number_location_index = $('number_location_index').value;
  		var str_sub_location = "";
  		for (i = 1; i <= number_location_index; i++) { 
  			if($('lat_'+i) && $('long_'+i))
  			{
	  			var lat_value = $('lat_'+i).value;
	  			var long_value = $('long_'+i).value;
	  			if(lat_value!= 0 && long_value != 0)
	  			{
			    	str_sub_location += $('location_title_' + i).value + '<div id="map-canvas-'+ i +'" style="height: 220px; margin-top: 20px;"></div>';
				}
				else
				{
					str_sub_location += $('location_title_' + i).value + '<div class="tip" style="margin: 10px"><span><?php echo $this->translate('Can not found the location.')?></span></div>';
				}
			}
	  	}
	  		
		var mypreview = new Element('div', {html: 
			'<div class="preview-overlay"></div>'
			+'<div class="preview-main">'
			+'<span class="btn-preview-popup-close">Close</span>'
			+'<h1>'+ $('name').value +'</h1>'
			+'<div class="profile_fields"><h4><span><?php echo $this->translate('Category');?></span></h4><ul/>'
			+str_main_category
			+str_sub_category
			+'</ul></div>'
			+'<div class="profile_fields"><h4><span><?php echo $this->translate('Short description');?></span></h4><ul><li>'+ tinyMCE.get('short_description').getContent() +'</li></ul></div>'
			+'<div class="profile_fields"><h4><span><?php echo $this->translate('Business Size');?></span></h4><ul><li>'+ $('size').value +'</li></ul></div>'
			+'<div class="profile_fields"><h4><span><?php echo $this->translate('Operating Hours');?></span></h4><ul>'
			+'<li>' + $('timezone').value + '</li>'
			+str_operating_hours
			+'</ul></div>'
			+'<div class="profile_fields"><h4><span><?php echo $this->translate('Founders');?></span></h4><ul>'
			+str_founders
			+'</ul></div>'
			+'<div class="profile_fields"><h4><span><?php echo $this->translate('Contact Information');?></span></h4><ul>'
			+'<li><span><?php echo $this->translate('Phone');?></span><span>'
			+str_phone
			+'</span></li>'
			+'<li><span><?php echo $this->translate('Website');?></span><span>'
			+str_web_address
			+'</span></li>'
			+'<li><span><?php echo $this->translate('Email');?></span><span>'
			+$('email').value
			+'</span></li>'
			+'<li><span><?php echo $this->translate('Fax');?></span><span>'
			+str_fax
			+'</span></li>'
			+'<li><span><?php echo $this->translate('Locations');?></span><span>'
			+str_main_location
			+str_sub_location
			+'</span></li>'
			+'</ul></div>'
			+'<div class="profile_fields"><h4><span><?php echo $this->translate('Description');?></span></h4><ul><li>'+ tinyMCE.get('description').getContent() +'</li></ul></div>'
			+'</div>'
		});
		
		mypreview.addClass('preview-popup');
		$$('.preview-popup').dispose();
		mypreview.inject( $$('body')[0] );
		
		$$('.btn-preview-popup-close').addEvent('click', function(){
			$$('.preview-popup').dispose();
		});	
		
		window.scrollTo(0, 0);
		
		//main location
		var main_lat_value = $('lat').value;
		var main_long_value = $('long').value;
		if(main_lat_value!= 0 && main_long_value != 0)
		{
			google.maps.event.addDomListener(window, 'load', initializeMapView($('lat').value, $('long').value, 'map-canvas-0'));
  		}
  		//sub locations 
  		for (i = 1; i <= number_location_index; i++) { 
  			var map_canvas_id = 'map-canvas-' + i;
  			var lat_value ;
  			var long_value ;
  			if($('lat_'+i))
  				var lat_value = $('lat_'+i).value;
  			if($('long_'+i))	
  				 long_value = $('long_'+i).value;
  			if(lat_value!= 0 && long_value != 0)
  			{
  				if($(map_canvas_id))
  					google.maps.event.addDomListener(window, 'load', initializeMapView(lat_value, long_value, map_canvas_id));
  			}
  		}
  	}
  	
	//preview click
	if($('preview'))
	{
	  	$('preview').addEvent('click', function() {
	  		preview();
	  	}); 	
    }
  	
  	function addFounder(name, id , href )
	{
		  //set value to hidden field
		  var hiddenInputField = document.getElementById('toValues');
		  var previousToValues = hiddenInputField.value;
		  if(!id)
		  {
			  if (checkSpanExists(name, name)){
				if (previousToValues==''){
				  document.getElementById('toValues').value = name;
				}
				else {
				  document.getElementById('toValues').value = previousToValues+","+name;
				}
			  }
			  if (checkSpanExists(name, name)){
				 //create block
				 var myElement = new Element("span");
				 myElement.id = "tospan_"+name+"_"+name;;
				 
				 myElement.innerHTML = name+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+name+"\");'>x</a>";
				 document.getElementById('toValues-wrapper').style.height= 'auto';
		
				 myElement.addClass("tag");
		
				 document.getElementById('toValues-element').appendChild(myElement);
				 this.fireEvent('push');
				 $('to').set('value', "");
				 
				 if( document.getElementById('toValues').value.split(',').length >= maxRecipients ){
					document.getElementById('to').style.display = 'none';
				  }
			}
		}
		else
		{
			if (checkSpanExists(name, id)){
				if (previousToValues==''){
				  document.getElementById('toValues').value = id;
				}
				else {
				  document.getElementById('toValues').value = previousToValues+","+id;
				}
			  }
			  if (checkSpanExists(name, id)){
				 //create block
				 var myElement = new Element("span");
				 myElement.id = "tospan_"+name+"_"+id;;
				 myElement.innerHTML = "<a target='_blank' href="+href+">"+name+"</a>"+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+id+"\");'>x</a>";
				 document.getElementById('toValues-wrapper').style.height= 'auto';
		
				 myElement.addClass("tag");
		
				 document.getElementById('toValues-element').appendChild(myElement);
				 this.fireEvent('push');
				 $('to').set('value', "");
				 
				 if( document.getElementById('toValues').value.split(',').length >= maxRecipients ){
					document.getElementById('to').style.display = 'none';
				  }
			}
		}
	}
  	
  	function initializeMapView(latitude, longitude, map_canvas_id) {
  		 
		  var center =  new google.maps.LatLng(latitude, longitude);
		  var mapOptions = {
		    center: center,
		    zoom: 13
		  };
		
		  directionsDisplay = new google.maps.DirectionsRenderer();
		  map = new google.maps.Map(document.getElementById(map_canvas_id), mapOptions);
		  directionsDisplay.setMap(map);
		  
		  var infowindow = new google.maps.InfoWindow();
		
		  marker = new google.maps.Marker({
		  	map:map,
		  	draggable:true,
		  	animation: google.maps.Animation.DROP,
		  	position: center
		  });
			
		  google.maps.event.addListener(marker, 'dragend', toggleBounce);
		
	  }
	
	  function toggleBounce() {
			if (marker.getAnimation() != null) 
			{
	  			marker.setAnimation(null);
			} 
			else 
			{
	  			marker.setAnimation(google.maps.Animation.BOUNCE);
			}
			var point = marker.getPosition();
			fromPoint = new google.maps.LatLng(point.lat(), point.lng());
	  }
  	
  	if($$('.item-form-theme-choose').length > 0)
		$$('.item-form-theme-choose input')[0].setProperty('checked', 'true');
	
	if($$('.item-form-theme-choose').length > 0)
	{
	  	$$('.item-form-theme-choose').addEvent('click', function(){
	  		this.getElements('input')[0].set('checked','true');
	  	});
    }
  	
  	 function checkSpanExists(name, toID){
	      var span_id = "tospan_"+name+"_"+toID;
	      if ($(span_id)){
	        return false;
	      }
	      else return true;
    } 
  	
  	var name = "";
  	//keyup enter for autosuggest founder
  	$('to').addEvent('keyup', function(e)
  	{
  		 if(e.code === 13)
  		 {
			if(name == "")
  		 	{
  		 		return;
  		 	}
  		 	 //set value to hidden field
	          var hiddenInputField = document.getElementById('toValues');
	          var previousToValues = hiddenInputField.value;
				
	          if (checkSpanExists(name, name)){
	            if (previousToValues==''){
	              document.getElementById('toValues').value = name;
	            }
	            else {
	              document.getElementById('toValues').value = previousToValues+","+name;
	            }
	          }
  		 	  if (checkSpanExists(name, name)){
	  		 	 //create block
	             var myElement = new Element("span");
	             myElement.id = "tospan_"+name+"_"+name;;
	             
	             myElement.innerHTML = "<a>"+name+"</a>"+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+name+"\");'>x</a>";
		         document.getElementById('toValues-wrapper').style.height= 'auto';
		
		         myElement.addClass("tag");
		
		         document.getElementById('toValues-element').appendChild(myElement);
		         this.fireEvent('push');
		         $('to').set('value', "");
		         
		         if( document.getElementById('toValues').value.split(',').length >= maxRecipients ){
		            document.getElementById('to').style.display = 'none';
		          }
	        }
        }
        else
        {
        	name = $('to').value; 
        }
  	});
  	
  	
  	function capitaliseFirstLetter(string)
	{
	    return string.charAt(0).toUpperCase() + string.slice(1);
	}
  	
  	var arr_days = [
  		'monday',
  		'tuesday',
  		'wednesday',
  		'thursday',
  		'friday',
  		'saturday',
  		'sunday',
  	];
  	//check validate
	arr_days.forEach(function(entry) {
	   var id_from = entry+'_from';
	   var id_to = entry+'_to';
	  
	  
	   //add event for from dropbox
	   $(id_from).addEvent("change",function(e){
	   	 var value_id_from =  $(id_from).value;
	     var value_id_to =  $(id_to).value;
	     if(value_id_from == 'CLOSED')
	     {	     	
	     	$(id_to).set('value', 'CLOSED');
	     }
	     if((value_id_from == '' || value_id_from != '') && value_id_to == 'CLOSED')
	     {
	     	$(id_to).set('value', '');
	     }
     	 var from = new Date(Date.parse(value_id_from));
		 var to = new Date(Date.parse(value_id_to));
		 if(from > to && value_id_from != "" && value_id_to != "")
		 {
		 	alert(capitaliseFirstLetter(entry) + ': closing hours must be larger than opening hours. Please select again.');
		 	$(id_from).set('value', '');
		 }			     
      });
      
      //add event for to dropbox
	   $(id_to).addEvent("change",function(e){
         var value_id_from =  $(id_from).value;
	     var value_id_to =  $(id_to).value;
	     if(value_id_to == 'CLOSED')
	     {	     	
	     	$(id_from).set('value', 'CLOSED');
	     }
	     if((value_id_to == '' || value_id_to != '') && value_id_from == 'CLOSED')
	     {
	     	$(id_from).set('value', '');
	     }
	     var from = new Date(Date.parse(value_id_from));
		 var to = new Date(Date.parse(value_id_to));
		 if(to < from && value_id_to != "" && value_id_from != "")
		 {
		 	alert(capitaliseFirstLetter(entry) + ': closing hours must be larger than opening hours. Please select again.');
			$(id_to).set('value', '');
		 }	
      });
      
	});
  	
  	<?php if($this -> number_location_index != 0) :?>
  		var index_location = <?php echo $this -> number_location_index;?>;
  	<?php else:?>
  		var index_location = 0;
  	<?php endif;?>
  	
  	//for addmore location
  	$('add_more_location').addEvent('click', function(event) {
  		index_location++;
  		$('number_location_index').set('value', index_location);
  		$('number_location').set('value', parseInt($('number_location').value) + 1);
  		addMoreAddLocation(this, event, index_location);
  	}); 	
  	function addMoreAddLocation(obj, e, index) {
        e.preventDefault();
        var oriAddInfo_location = $('location-wrapper');
        var oriAddInfo_location_title = $('location_title');
        var oriAddInfo_location_address = $('location_address');
        var oriAddInfo_lat = $('lat');
        var oriAddInfo_long = $('long');
        
        var newAddInfo_location = oriAddInfo_location.clone(true);
        newAddInfo_location.set('class', 'sub_location-wrapper form-wrapper');
        var newAddInfo_location_title = oriAddInfo_location_title.clone(true);
        var newAddInfo_location_address = oriAddInfo_location_address.clone(true);
        var newAddInfo_lat = oriAddInfo_lat.clone(true);
        var newAddInfo_long = oriAddInfo_long.clone(true);
        
        var children = newAddInfo_location.getChildren();
        children[0].getChildren()[1].getChildren()[0].set('name','location_title_'+index_location);
        children[0].getChildren()[1].getChildren()[0].set('id','location_title_'+index_location);
        children[1].getChildren()[1].getChildren()[0].set('name','location_'+index_location);
        children[1].getChildren()[1].getChildren()[0].set('id','location_'+index_location);
        $strNewAddInfo_location = newAddInfo_location.innerHTML;
        var $strNewAddInfo_location = $strNewAddInfo_location.replace("'location'", "'location_"+index_location+"'");
        var $strNewAddInfo_location = $strNewAddInfo_location.replace("'location_title'", "'location_title_"+index_location+"'");
        var $strNewAddInfo_location = $strNewAddInfo_location.replace("'location_address'", "'location_address_"+index_location+"'");
        var $strNewAddInfo_location = $strNewAddInfo_location.replace("'lat'", "'lat_"+index_location+"'");
        var $strNewAddInfo_location = $strNewAddInfo_location.replace("'long'", "'long_"+index_location+"'");
        
        newAddInfo_location.innerHTML = $strNewAddInfo_location;
        
        newAddInfo_location_address.set('name','location_address_'+index_location);
        newAddInfo_location_address.set('id','location_address_'+index_location);
        var newAddInfo_location_address_ID = newAddInfo_location_address.get('id');
        
        newAddInfo_lat.set('name','lat_'+index_location);
        newAddInfo_lat.set('id','lat_'+index_location);
        var newAddInfo_lat_ID = newAddInfo_lat.get('id');
        
        newAddInfo_long.set('name','long_'+index_location);
        newAddInfo_long.set('id','long_'+index_location);
        var newAddInfo_long_ID = newAddInfo_long.get('id');
        
        var remove = new Element('a', {
            href: 'javascript:void(0)',
            html: '',
            'class': 'fa fa-minus-circle remove-add-location',
            events : {
                click: function(event) {
                    event.preventDefault();
                    this.getParent().destroy();
                    $(newAddInfo_location_address_ID).remove();
                    $(newAddInfo_lat_ID).remove();
                    $(newAddInfo_long_ID).remove();
                    $('number_location').set('value', $('number_location').value - 1);
                }
            }
        });
        newAddInfo_location.grab(remove);
        newAddInfo_location.inject('approval-wrapper', 'before');
        newAddInfo_location_address.inject('approval-wrapper', 'before');
        newAddInfo_lat.inject('approval-wrapper', 'before');
        newAddInfo_long.inject('approval-wrapper', 'before');
        google.maps.event.addDomListener(window, 'load', initialize('location_'+index_location, 'location_address_'+index_location,'lat_'+index_location,'long_'+index_location));
        var return_array = [newAddInfo_location_address, newAddInfo_lat, newAddInfo_long]; 
        return return_array;
    }
  	
  	//for addmore category
  	var number_category = 1;
  	$('add_more_category').addEvent('click', function(event) {
  		addMoreAddCategory(this, event);
  	}); 	
  	function addMoreAddCategory(obj, e) {
        e.preventDefault();
        var oriAddInfo = $('category_id-wrapper');
        var newAddInfo = oriAddInfo.clone(true);
        var children = newAddInfo.getChildren();
        children[0].getChildren()[0].destroy();
        children[1].getChildren()[0].set('class','sub_category btn_form_inline');
        children[1].getChildren()[0].set('name','sub_category[]');
        var remove = new Element('a', {
            href: 'javascript:void(0)',
            html: '',
            'class': 'fa fa-minus-circle remove-add-category',
            events : {
                click: function(event) {
                    event.preventDefault();
                    this.getParent('.form-wrapper').destroy();
                    number_category--;
                    if(number_category < 3)
                    {
                        var oriAddInfo = $('add_more_category');
                        if(oriAddInfo)
                        	oriAddInfo.setStyle('display', 'inline-block');
                    }
                }
            }
        });
        newAddInfo.getElement('.description a').destroy();
        newAddInfo.getElement('.description').grab(remove);
        newAddInfo.inject('category_id-wrapper', 'after');
        number_category++;
        if(number_category == 3)
        {
           var oriAddInfo = $('add_more_category');
           if(oriAddInfo)
          		oriAddInfo.setStyle('display', 'none');
        }
        return newAddInfo;
    }
  	
  	//for addmore phone
  	$('add_more_phone').addEvent('click', function(event) {
  		addMoreAddPhone(this, event);
  	}); 	
  	function addMoreAddPhone(obj, e) {
        e.preventDefault();
        var oriAddInfo = $('phone-wrapper');
        var newAddInfo = oriAddInfo.clone(true);
        var children = newAddInfo.getChildren();
        children[0].getChildren()[0].destroy();
        children[1].getChildren()[0].set('name','sub_phone[]');
        children[1].getChildren()[0].set('class','sub_phone btn_form_inline');
        children[1].getChildren()[0].set('value','');
        var remove = new Element('a', {
            href: 'javascript:void(0)',
            html: '',
            'class': 'fa fa-minus-circle remove-add-phone',
            events : {
                click: function(event) {
                    event.preventDefault();
                    this.getParent('.form-wrapper').destroy();
                }
            }
        });
        newAddInfo.getElement('.description a').destroy();
        newAddInfo.getElement('.description').grab(remove);
        newAddInfo.inject('phone-wrapper', 'after');
        return newAddInfo;
    }
  	
  	//for addmore fax
  	$('add_more_fax').addEvent('click', function(event) {
  		addMoreAddFax(this, event);
  	}); 	
  	function addMoreAddFax(obj, e) {
        e.preventDefault();
        var oriAddInfo = $('fax-wrapper');
        var newAddInfo = oriAddInfo.clone(true);
        var children = newAddInfo.getChildren();
        children[0].getChildren()[0].destroy();
        children[1].getChildren()[0].set('name','sub_fax[]');
        children[1].getChildren()[0].set('class','sub_fax btn_form_inline');
        children[1].getChildren()[0].set('value','');
        var remove = new Element('a', {
            href: 'javascript:void(0)',
            html: '',
            'class': 'fa fa-minus-circle remove-add-fax',
            events : {
                click: function(event) {
                    event.preventDefault();
                    this.getParent('.form-wrapper').destroy();
                }
            }
        });
        newAddInfo.getElement('.description a').destroy();
        newAddInfo.getElement('.description').grab(remove);
        newAddInfo.inject('fax-wrapper', 'after');
        return newAddInfo;
    }
  	
  	//for addmore web address
  	$('add_more_web_address').addEvent('click', function(event) {
  		addMoreAddWebAddress(this, event);
  	}); 	
  	function addMoreAddWebAddress(obj, e) {
        e.preventDefault();
        var oriAddInfo = $('web_address-wrapper');
        var newAddInfo = oriAddInfo.clone(true);
        var children = newAddInfo.getChildren();
        children[0].getChildren()[0].destroy();
        children[1].getChildren()[0].set('name','sub_web_address[]');
        children[1].getChildren()[0].set('class','sub_web_address btn_form_inline');
        children[1].getChildren()[0].set('value','');
        var remove = new Element('a', {
            href: 'javascript:void(0)',
            html: '',
            'class': 'fa fa-minus-circle remove-add-web_address',
            events : {
                click: function(event) {
                    event.preventDefault();
                    this.getParent('.form-wrapper').destroy();
                }
            }
        });
        newAddInfo.getElement('.description a').destroy();
        newAddInfo.getElement('.description').grab(remove);
        newAddInfo.inject('web_address-wrapper', 'after');
        return newAddInfo;
    }
  	
  	
  	$('category_id').addEvent('change', function(){
      $(this).getParent('form').submit(); 
    }); 
  	
  	if($('0_0_1-wrapper'))
  	{
  		$('0_0_1-wrapper').setStyle('display','none');
  	}
  	
  	<?php if($this -> package -> allow_owner_add_customfield) :?>
	  	<?php if($this -> number_add_more_index != 0) :?>
  			var index = <?php echo $this -> number_add_more_index;?>;
	  	<?php else:?>
	  		var index = 1;
	  	<?php endif;?>
	  	// add more addtional info
	  	$('add_more_info').addEvent('click', function(){
	  		this.setStyle('display', 'none');
	  		index = index + 1;
	  	 	var url = '<?php echo $this->url(array('action' => 'add-info'), 'ynbusinesspages_general', true) ?>';
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
  <?php endif;?>
  		
  		//TODO popylate data back
  		
  		//founder
		<?php if(!empty($this -> posts['toValues'])):?>
			$('toValues').set('value', "");
			<?php $founders = explode(",", $this -> posts['toValues']); ?>
			<?php foreach($founders as $founder) :?>
				var id = null;
				var href = null;
				var name = null;
				<?php $user = Engine_Api::_() -> getItem('user', $founder); ?>
				<?php if($user -> getIdentity() > 0) :?>
					name =  '<?php echo $user -> getTitle();?>';
					id = '<?php echo $founder;?>';
					href = '<?php echo $user -> getHref() ?>';
					addFounder(name, id, href);
				<?php else :?>
					name = '<?php echo $founder;?>';
					addFounder(name, id, href);
				<?php endif;?>
			<?php endforeach;?>	
		<?php endif;?>
  		
  		<?php if(!empty($this -> posts['sub_category'])):?>
  			number_category = 1;
	  		<?php foreach($this -> posts['sub_category'] as $sub_category_id) :?>
				var event = document.createEvent('Event');
		  		var newAddInfo = addMoreAddCategory(null, event);
		  		var children = newAddInfo.getChildren();
		  		children[1].getChildren()[0].set('value', '<?php echo $sub_category_id ?>');
		  		if(number_category == 3)
		        {
		        	var oriAddInfo = $('add_more_category');
		        	if(oriAddInfo)
		        	{
		        		oriAddInfo.setStyle('display', 'none');
		        	}
		        }
			<?php endforeach;?>
		<?php endif;?>
		
		<?php foreach($this -> posts['sub_phone'] as $sub_phone_value) :?>
			var event = document.createEvent('Event');
	  		var newAddInfo = addMoreAddPhone(null, event);
	  		var children = newAddInfo.getChildren();
	  		children[1].getChildren()[0].set('value', '<?php echo $sub_phone_value ?>');
		<?php endforeach;?>
		
		<?php foreach($this -> posts['sub_fax'] as $sub_fax_value) :?>
			var event = document.createEvent('Event');
	  		var newAddInfo = addMoreAddFax(null, event);
	  		var children = newAddInfo.getChildren();
	  		children[1].getChildren()[0].set('value', '<?php echo $sub_fax_value ?>');
		<?php endforeach;?>
		
		<?php foreach($this -> posts['sub_web_address'] as $sub_web_address_value) :?>
			var event = document.createEvent('Event');
	  		var newAddInfo = addMoreAddWebAddress(null, event);
	  		var children = newAddInfo.getChildren();
	  		children[1].getChildren()[0].set('value', '<?php echo $sub_web_address_value ?>');
		<?php endforeach;?>
		
		<?php if(!empty($this -> subLocation)) :?>
			<?php foreach($this -> subLocation as $location) :?>
		  		index_location++;
		  		$('number_location_index').set('value', index_location);
		  		var event = document.createEvent('Event');
		  		var return_array = addMoreAddLocation(null, event);
		  		var newAddInfo_location_address = return_array[0];
		  		var newAddInfo_lat = return_array[1];
		  		var newAddInfo_long = return_array[2];
		  		var title_location_name = 'location_title_'+ index_location;
		  		var address_location_name = 'location_'+ index_location;
		  		$(address_location_name).set('value', '<?php echo $location -> location ?>');
		  		$(title_location_name).set('value', '<?php echo $location -> title?>');
		  		newAddInfo_location_address.set('value', '<?php echo $location -> location ?>');
		  		newAddInfo_lat.set('value', '<?php echo $location -> latitude ?>');
		  		newAddInfo_long.set('value', '<?php echo $location -> longitude ?>');
		  	<?php endforeach;?>
	  	<?php endif;?>
	  	
	  	<?php if(!empty($this -> posts['location'])) :?>
			$('location').set('value', "<?php echo $this -> posts['location'];?>");
		<?php endif;?>
		
		<?php if(!empty($this -> posts['location_title'])) :?>
			$('location_title').set('value', '<?php echo $this -> posts['location_title'];?>');
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
  
  
  function placeChanged(autocomplete, location, location_address, lat, long) {
		google.maps.event.addListener(autocomplete, 'place_changed', function(){
	    	var place = autocomplete.getPlace();
		    if (!place.geometry) {
		     	return;
		    }
			document.getElementById(location_address).value = place.formatted_address;		
			document.getElementById(lat).value = place.geometry.location.lat();		
			document.getElementById(long).value = place.geometry.location.lng();
		});
    }
  
  function initialize(location, location_address, lat, long) {
		var input = /** @type {HTMLInputElement} */(
					document.getElementById(location));
	  	var autocomplete = new google.maps.places.Autocomplete(input);
	  	placeChanged(autocomplete, location, location_address, lat, long);
  }
  
  google.maps.event.addDomListener(window, 'load', initialize('location', 'location_address','lat','long')); 
  
  var getCurrentLocation = function(obj, location, location_address, lat, long )
	{	
		if(navigator.geolocation) {
			
	    	navigator.geolocation.getCurrentPosition(function(position) {
	    			
	      	var pos = new google.maps.LatLng(position.coords.latitude,
	                                       position.coords.longitude);
	        
			if(pos)
			{
				
				current_posstion = new Request.JSON({
					'format' : 'json',
					'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynbusinesspages_general') ?>',
					'data' : {
						latitude : pos.lat(),
						longitude : pos.lng(),
					},
					'onSuccess' : function(json, text) {
						
						if(json.status == 'OK')
						{
							document.getElementById(location).value = json.results[0].formatted_address;
							document.getElementById(location_address).value = json.results[0].formatted_address;
							document.getElementById(lat).value = json.results[0].geometry.location.lat;		
							document.getElementById(long).value = json.results[0].geometry.location.lng; 		
						}
						else{
							handleNoGeolocation(true,location);
						}
					}
				});	
				current_posstion.send();
				
			}
	      	
	    	}, function() {
	      		handleNoGeolocation(true,location);
	    	});
	  	}
	  	else {
    		// Browser doesn't support Geolocation
    		handleNoGeolocation(false,location);
  		}
		return false;
	}
	
	function handleNoGeolocation(errorFlag, location) {
  		if (errorFlag) {
    		document.getElementById(location).value = 'Error: The Geolocation service failed.';
  		} 
  		else {
   			document.getElementById(location).value = 'Error: Your browser doesn\'t support geolocation.';
   		}
 	}
</script>

<script type="text/javascript">
   
   en4.core.runonce.add(function()
  {
	   new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
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
  });
  
</script>

<script type="text/javascript">
  // Populate data
  var maxRecipients = 5;
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
    if (document.getElementById('toValues').value==""){
      document.getElementById('toValues-wrapper').style.height = '0';
    }
	document.getElementById('to').style.display = 'block';
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    document.getElementById('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
    if( !isPopulated ) { // NOT POPULATED
      new Autocompleter2.Request.JSON('to', '<?php echo $this->url(array('controller' => 'index', 'action' => 'founder-suggest'), 'ynbusinesspages_general', true) ?>', {
        'minLength': 1,
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
          if( document.getElementById('toValues').value.split(',').length >= maxRecipients ){
            document.getElementById('to').style.display = 'none';
          }
        }
      });
      
      new Composer.OverText($document.getElementById('to'), {
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

    } else { // POPULATED
      var myElement = new Element("span", {
        'id' : 'tospan' + to.id,
        'class' : 'tag tag_' + to.type,
        'html' :  to.title /* + ' <a href="javascript:void(0);" ' +
                  'onclick="this.parentNode.destroy();removeFromToValue("' + toID + '");">x</a>"' */
      });
      document.getElementById('to-element').appendChild(myElement);
      document.getElementById('to-wrapper').style.height = 'auto';

      // Hide to input?
      document.getElementById('to').style.display = 'none';
      document.getElementById('toValues-wrapper').style.display = 'none';
    }
  });
</script>

<?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>
<?php endif; ?>