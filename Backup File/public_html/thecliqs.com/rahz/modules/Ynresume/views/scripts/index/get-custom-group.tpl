<div class='global_form'>
  <?php echo $this->form->render($this) ?> 
</div>

<script type="text/javascript">
	
	function confirmRemove(heading)
	{
		var div = new Element('div', {
                   'class': 'ynresume-confirm-popup' 
        });
        var text = '';
        text = '<?php echo $this->translate('Do you want to remove this?')?>';
        var p = new Element('p', {
            'class': 'ynresume-confirm-message',
            text: text,
        });
        var button = new Element('button', {
            'class': 'ynfeedback-confirm-button',
            text: '<?php echo $this->translate('Remove')?>',
            onclick: 'parent.Smoothbox.close();removeForm('+heading+');'
            
        });
        var span = new Element('span', {
           text: '<?php echo $this->translate(' or ')?>' 
        });
        
        var cancel = new Element('a', {
            text: '<?php echo $this->translate('Cancel')?>',
            onclick: 'parent.Smoothbox.close();',
            href: 'javascript:void(0)'
        });
        
        div.grab(p);
        div.grab(button);
        div.grab(span);
        div.grab(cancel);
        Smoothbox.open(div);
	}
	
	function removeForm(heading)
	{
		var data = new Object();
		data.field_id = heading;
		data.resume_id = '<?php echo $this -> resume_id;?>';
		
		var url = "<?php echo $this -> url(array('action' => 'remove-group'), 'ynresume_general', true);?>";
		new Request.JSON({
	        url: url,
	        method: 'post',
	        data: data,
	        'onSuccess' : function(responseJSON, responseText)
	        {
	             $('sections-content-item_field_'+heading).destroy();
			}
		}).send();
	}
	
	function submitForm(heading)
	{
		var form = $('custom-field-group-form-'+heading);
		if(form)
		{
			var inputs = document.forms['custom-field-group-form-'+heading].getElementsByTagName("input");
			var textareas = document.forms['custom-field-group-form-'+heading].getElementsByTagName("textarea");
			var selects = document.forms['custom-field-group-form-'+heading].getElementsByTagName("select");
			
			var data = new Object();
			for (i = 0; i < inputs.length; i++) { 
				var type = inputs[i].get('type');
				switch(type) {
				    case 'radio':
				        if(inputs[i].get('checked')) {
				        	var name = inputs[i].name;
							data[name] = inputs[i].value;
						}
				        break;
				    case 'checkbox':
				    	if(inputs[i].get('checked')) {
				    		var nameEle = inputs[i].name;
				    		if(nameEle.slice(-2) == "[]") {
				    			//if multi checkbox
				    			var name = inputs[i].name.substr(0, inputs[i].name.length - 2);
					    		if(typeof(data[name]) != 'undefined'){
					    			data[name].push(inputs[i].value) ;
					    		}
					    		else {
									data[name] = [inputs[i].value] ;
								}
				    		} else {
				    			//else single checkbox
				    			var name = inputs[i].name;
				    			data[name] = inputs[i].value ;
				    		}
				    		
						}
						break;
				    default:
				        var name = inputs[i].name;
						data[name] = inputs[i].value;
						break;
				}
			}
			for (i = 0; i < textareas.length; i++) { 
				var name = textareas[i].name;
				data[name] = textareas[i].value;
			}
			for (i = 0; i < selects.length; i++) { 
				var multiple = selects[i].get('multiple');
				if(multiple){
			    	for (var j = 0; j < selects[i].length; j++) {
			    		if (selects[i].options[j].selected)	{
				    		var name = selects[i].name.substr(0, selects[i].name.length - 2);
				    		if(typeof(data[name]) != 'undefined'){
				    			data[name].push(selects[i].options[j].value) ;
				    		}
				    		else {
								data[name] = [selects[i].options[j].value] ;
							}
						}
					}
				}
				else{
			        var name = selects[i].name;
					data[name] = selects[i].value;
				}
			}
			data.field_id = heading;
			data.resume_id = '<?php echo $this -> resume_id;?>';
			
			var url = "<?php echo $this -> url(array('action' => 'save-group'), 'ynresume_general', true);?>";
			new Request.JSON({
		        url: url,
		        method: 'post',
		        data: data,
		        'onSuccess' : function(responseJSON, responseText)
		        {
		        	if($$('.error_'+heading))
		        		$$('.error_'+heading).destroy();
		        		
		        	if(responseJSON.error_code ==  1)
		        	{
			        	var field_message = responseJSON.message.fields;
			        	for (var key in field_message) {
							  if (field_message.hasOwnProperty(key)) {
							    	for (var object_key in field_message[key]) {
								  		if (field_message[key].hasOwnProperty(object_key)) {
								  			//console.log(object_key + " -> " + field_message[key][object_key]);
								  			if($(key+'-element'))
									  		{
									  			var html = '<ul class="form-errors" style="margin: 0"><li class="form-errors">'+field_message[key][object_key]+'</li></ul>';
									  			var error = new Element('div', {html: html});
									  			error.addClass('error_'+heading);
												$(key+'-element').grab(error,'before');
									  		}
								  		}
							  		}
							  }
						}
					}
					else
					{
			             renderSection('field_'+heading, []);
					}
				}
			}).send();
		}
	}
</script>