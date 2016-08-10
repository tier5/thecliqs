<?php
    $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/picker/Locale.en-US.DatePicker.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/picker/Picker.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/picker/Picker.Attach.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/picker/Picker.Date.js');
    
    $this->headLink() 
	->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/styles/picker/datepicker_dashboard.css');
	
  	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/Autocompleter.Custom.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynresume/externals/scripts/Autocompleter.Request.js');
?>
<?php 
    $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $level_id = 5;
    if ($viewer->getIdentity()) $level_id = $viewer->level_id;
    $resume = (isset($params['view']) && $params['view']) ? Engine_Api::_()->core()->getSubject() : $this->resume;
    $params = $this->params;
    $manage = ($resume->isOwner($viewer)) && (!isset($params['view']) || !$params['view']);
    $create = (isset($params['create'])) ? $params['create'] : false;
    $edit = (isset($params['edit'])) ? $params['edit'] : false;
    $hide = (isset($params['hide'])) ? $params['hide'] : false;
    
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $max_photo = $permissionsTable->getAllowed('ynresume_resume', $level_id, 'max_photo');
    if ($max_photo == null) {
        $row = $permissionsTable->fetchRow($permissionsTable->select()
        ->where('level_id = ?', $level_id)
        ->where('type = ?', 'ynresume_resume')
        ->where('name = ?', 'max_photo'));
        if ($row) {
            $max_photo = $row->value;
        }
    }
?>
<?php
	$publication = $resume->getAllPublication();
	if (count($publication) <= 0 && $manage) 
	{
	    $create = true;
	}
?>
<?php if (!$hide && ($create || $edit)):?>
 <script type="text/javascript">
        window.addEvent('domready', function() {
            if ($('publication_authors') != null)
            {
				new Autocompleter2.Request.JSON('publication_authors', '<?php echo $this->url(array('controller' => 'publication', 'action' => 'suggest'), 'ynresume_extended', true) ?>', {
			        'minLength': 1,
			        'toElementName': 'authorValues',
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
			        	new Sortables('authorValues-element', {
				            contrain: false,
				            clone: true,
				            handle: 'span',
				            opacity: 0.5,
				            revert: true,
				            onComplete: function(){
				            	ids = [];
				            	$$("div#authorValues-element span").get("id").each(function(e){
					            	if (e){
					            		temp = e.split("_");
					            		ids.push(temp[2]);
						            }
					            });
				                document.getElementById('authorValues').value = ids.join();
				            }
				        });
				        if( document.getElementById('authorValues').value.split(',').length >= 20 ){
				            	document.getElementById('publication_authors').style.display = 'none';
				        }
			        }
				});
            }

            function checkSpanExists(name, toID){
	      	      var span_id = "tospan_" + name + "_" + toID;
	      	      if ($(span_id)){
	      	        	return false;
	      	      }
	      	      else return true;
          	} 
			
            var name = "";
            if ($('publication_authors') != null){
            	$('publication_authors').addEvent('keyup', function(e){
    				if(e.code === 13)
    				{
                        name =  name.replace(/(<([^>]+)>)/ig,"");
    					if(name == "")
              		 	{
              		 		return;
              		 	}
            		 	//set value to hidden field
    					var hiddenInputField = document.getElementById('authorValues');
            	        var previousToValues = hiddenInputField.value;
            				
            	        if (checkSpanExists(name, name)){
            	            if (previousToValues==''){
            	              document.getElementById('authorValues').value = name;
            	            }
            	            else {
            	              document.getElementById('authorValues').value = previousToValues + "," + name;
            	            }
            	        }
            	        
              		 	if (checkSpanExists(name, name)){
            	  		 	 //create block
            	             var myElement = new Element("span");
            	             myElement.id = "tospan_"+name+"_"+name;

            	             myElement.innerHTML = name + " <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+name+"\",\"authorValues\");'>x</a>";
            		
            		         myElement.addClass("tag");
            		
            		         document.getElementById('authorValues-element').appendChild(myElement);
            		         this.fireEvent('push');
            		         $('publication_authors').set('value', "");
            		         
            		         if( document.getElementById('authorValues').value.split(',').length >= 20 ){
            		            document.getElementById('publication_authors').style.display = 'none';
            		          }
            	        }
                    }
                    else
                    {
                    	name = $('publication_authors').value; 
                    }
              	});
            }
        });
        function removeFromToValue(id, elementName) {
	          // code to change the values in the hidden field to have updated values
	          // when recipients are removed.
	          var toValues = document.getElementById(elementName).value;
	          var toValueArray = toValues.split(",");
	          var toValueIndex = "";
	
	          var checkMulti = id.search(/,/);
	
	          // check if we are removing multiple recipients
	          if (checkMulti!=-1){
	            var recipientsArray = id.split(",");
	            for (var i = 0; i < recipientsArray.length; i++){
	              removeToValue(elementName, recipientsArray[i], toValueArray);
	            }
	          }
	          else{
	            removeToValue(elementName, id, toValueArray);
	          }
	      	          
	          document.getElementById('publication_authors').style.display = 'block';
        }
        function removeToValue(elementName, id, toValueArray){
            for (var i = 0; i < toValueArray.length; i++){
              if (toValueArray[i]==id) toValueIndex =i;
            }

            toValueArray.splice(toValueIndex, 1);
            document.getElementById(elementName).value = toValueArray.join();
		}
</script>
<?php endif;?>

<?php if (count($publication) > 0 || (!$hide && ($create || $edit))) : ?>
	<?php $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section); ?>

    <?php if ($manage) : ?>
        <a class="ynresume-add-btn" rel="<?php echo $this->section;?>"><?php echo $this->translate('Add publication')?></a>
    <?php endif; ?>

	<h3 class="section-label">
        <span class="section-label-icon"><i class="<?php echo Engine_Api::_()->ynresume()->getSectionIconClass($this->section);?>"></i></span>
        <span><?php echo $label;?></span>
    </h3>
    
    <div class="ynresume_loading" style="display: none; text-align: center">
        <img src='application/modules/Ynresume/externals/images/loading.gif'/>
    </div>
    
    <div class="ynresume-section-content">
    <?php if ($manage) : ?>
    
        <?php if (!$hide && ($create || $edit)) : ?>
        
        <script type="text/javascript">
            window.addEvent('domready', function() {
    	        new Picker.Date($$('.date_picker'), { 
    	            positionOffset: {x: 5, y: 0}, 
    	            pickerClass: 'datepicker_dashboard', 
    	            useFadeInOut: !Browser.ie,
    	            onSelect: function(date){
    	            }
    	        }); 
            });
    	</script>
        <div id="ynresume-section-form-publication" class="ynresume-section-form">
            <form rel="publication" class="section-form" enctype="multipart/form-data">
                <?php $item = null;?>
                <p class="error"></p>
                
                <?php if ($edit && isset($params['item_id'])) : ?>
    	            <?php $item = Engine_Api::_()->getItem('ynresume_publication', $params['item_id']);?>
    	            <input type="hidden" name="item_id" class="item_id" id="publication-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
                <?php endif; ?>
                
                <div id="publication-title-wrapper" class="ynresume-form-wrapper">
                    <label for="publication-title"><?php echo $this->translate('*Title')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="publication-title" name="title" value="<?php if ($item) echo htmlentities($item->title)?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="publication-publisher-wrapper" class="ynresume-form-wrapper">
                    <label for="publication-publisher"><?php echo $this->translate('Publication/Publisher')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="publication-publisher" name="publisher" value="<?php if ($item) echo htmlentities($item->publisher)?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="publication-date-wrapper" class="ynresume-form-wrapper">
                    <label for="publication-date"><?php echo $this->translate('Publication Date')?></label>
                    <div class="ynresume-form-input ynresume-form-input-4item">
                        <div class="">
                            <?php
                                if ($item)
                                {
                                    $monthTemp = 0; $yearTemp = '';
                                    if (!is_null($item -> publication_date))
                                    {
                                        $monthTemp = date('n', strtotime($item->publication_date));
                                        $yearTemp = date('Y', strtotime($item->publication_date));
                                    }
                                }
                            ?>
                            <select name="publication_month" id="publication-month" value="<?php if ($monthTemp) echo $monthTemp; ?>">
                                <?php $month = array('Choose...', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')?>
                                <?php foreach ($month as $key => $value) : ?>
                                <option value="<?php echo $key?>" <?php if ($item &&  $monthTemp == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="publication_year" id="publication_year" value="<?php if ($yearTemp) echo $yearTemp;?>"/>
                        </div>
                        <p class="error"></p>
                    </div>

                </div>

                <div id="publication-url-wrapper" class="ynresume-form-wrapper">
                    <label for="publication-url"><?php echo $this->translate('Publication URL')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="publication-url" name="url" value="<?php if ($item) echo $item->url?>"/>
                        <p class="error"></p>
                    </div>
                </div>
    
    			<div id="publication_authors-wrapper" class="ynresume-form-wrapper">                    
                    <label for="publication_authors"><?php echo $this->translate('Authors')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="publication_authors" name="publication_authors" value=""/>
                        <p class="error"></p>
                    </div>
                </div>
    
    			<div id="authorValues-wrapper" class="form-wrapper ynresume-form-wrapper">
    				<label id="authorValues-label" class="form-label">&nbsp;</label>
    				<div id="authorValues-element" class="form-element ynresume-form-input">
    					<input type="hidden" name="authorValues" value="<?php echo ($item) ? $item -> getAuthorAsString() : ''; ?>" style="margin-top: -5px" id="authorValues">
    					<?php if ($item):?>
    						<?php $authors = $item -> getAuthorObjects(); ?>
    						<?php if (count($authors)):?>
    							<?php foreach ($authors as $author):?>
    								<?php if ($author->user_id > 0):?>
    									<?php $author = Engine_Api::_()->getItem('user', $author->user_id)?>
    									<span id="tospan_<?php echo $author -> getTitle();?>_<?php echo $author -> getIdentity();?>" class="tag">
    										<a target="_blank" href="<?php echo $author -> getHref();?>"><?php echo $author -> getTitle();?></a> 
    										<a href="javascript:void(0);" onclick="this.parentNode.destroy();removeFromToValue('<?php echo $author -> getIdentity();?>', 'authorValues');">x</a>
    									</span>
    								<?php else:?>
    									<span id="tospan_<?php echo $author -> name;?>_<?php echo $author -> name;?>" class="tag">
    									<a target="_blank" href="javascript:void(0);"><?php echo $author -> name;?></a> 
    										<a href="javascript:void(0);" onclick="this.parentNode.destroy();removeFromToValue('<?php echo $author -> name;?>', 'authorValues');">x</a>
    									</span>
    								<?php endif;?>
    							<?php endforeach;?>
    							<script type="text/javascript">
    							window.addEvent('domready', function() {
    								new Sortables('authorValues-element', {
    						            contrain: false,
    						            clone: true,
    						            handle: 'span',
    						            opacity: 0.5,
    						            revert: true,
    						            onComplete: function(){
    						            	ids = [];
    						            	$$("div#authorValues-element span").get("id").each(function(e){
    							            	if (e){
    							            		temp = e.split("_");
    							            		ids.push(temp[2]);
    								            }
    							            });
    						                document.getElementById('authorValues').value = ids.join();
    						            }
    						        });
    							});
    							</script>
    						<?php endif;?>
    					<?php endif;?>
    				</div>
    			</div>
    
    			<div id="publication-description-wrapper" class="ynresume-form-wrapper">
                    <label for="publication-description"><?php echo $this->translate('Description')?></label>
                    <div class="ynresume-form-input">
                        <textarea id="publication-description" name="description"/><?php if ($item) echo $item->description?></textarea>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="publication-photos-wrapper" class="ynresume-form-wrapper upload-photos-wrapper">
                    <label><?php echo $this->translate('Add photos')?></label>
                    <div class="ynresume-form-input">
                        <div id="file-wrapper">
                            <div class="form-element">
                                <!-- The fileinput-button span is used to style the file input field as button -->
                                <p class="element-description"><?php echo $this->translate(array('add_photo_description', 'You can add up to %s photos', $max_photo), $max_photo)?></p>
                                <span class="btn fileinput-button btn-success" type="button">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span><?php echo $this->translate("Add Photos")?></span>
                                    <!-- The file input field used as target for the file upload widget -->
                                    <input class="section-fileupload" id="publication-fileupload" type="file" accept="image/*" name="files[]" multiple>
                                </span>
                                <button type="button" class="btn btn-danger delete" onclick="clearList(this)">
                                    <i class="glyphicon glyphicon-trash"></i>
                                    <span><?php echo $this->translate("Clear List")?></span>
                                </button>
                                <br /> 
                                <br />  
                                
                                <!-- The global progress bar -->
                                <div id="progress" class="progress" style="display: none; width: 400px; float:left">
                                    <div class="progress-bar progress-bar-success"></div>
                                </div>
                                <span id="progress-percent"></span>
                                <!-- The container for the uploaded files -->
                                <?php $upload_photos = ($item) ? Engine_Api::_()->getItemTable('ynresume_photo')->getPhotosItem($item) : array();?>
                                <?php $photos = array()?>
                                <ul id="files" class="files" style="<?php if (count($upload_photos)) echo 'display:block;'?>">
                                <?php foreach ($upload_photos as $photo) : ?>
                                    <li class="file-success">
                                        <a class="file-remove" onclick="removeFile(this, <?php echo $photo->getIdentity()?>)" href="javascript:;" title="<?php echo $this->translate('Click to remove this photo.')?>">Remove</a>
                                        <span class="file-name"><?php echo $photo->title?></span>
                                    </li>
                                <?php $photos[] = $photo->getIdentity();?>
                                <?php endforeach;?>
                                </ul>
                                <input type="hidden" class="upload-photos" name="photo_ids" value="<?php echo implode(' ', $photos)?>"/>
                            </div>
                        </div>
                        <p class="error"></p>
                    </div>
                </div>
                
                <input type="hidden" name="authorOrderValues" value="" style="margin-top: -5px" id="authorOrderValues">
                
                <div class="ynresume-form-buttons ynresume-form-wrapper">
                    <label></label>
                    <div class="ynresume-form-input">
                        <button type="submit" id="submit-btn"><?php echo $this->translate('Save')?></button>
                        <button type="button" class="ynresume-cancel-btn"><?php echo $this->translate('Cancel')?></button>
                        <?php if ($edit && isset($params['item_id'])) : ?>
                        <?php echo $this->translate(' or ')?>
                        <a href="javascript:void(0);" class="ynresume-remove-btn"><?php echo $this->translate('Remove Publication')?></a>
                        <?php endif; ?>
                    </div>
                </div>
    			
    	</form>
        </div>
        <?php endif;?>
    <?php endif;?>
    
    <?php if (count($publication) > 0) : ?>
    <div id="ynresume-section-list-publication" class="ynresume-section-list">
        <ul id="publication-list" class="section-list">
        <?php foreach ($publication as $item) :?>
        <li class="section-item" id="publication-<?php echo $item->getIdentity()?>">
            <div class="sub-section-item">
                <div class="publication-title section-title"><?php echo strip_tags($item->title)?></div>
                <div class="publication-publisher">
        	        <span class="label"><?php echo $this -> translate("Publication/Publisher");?></span>
        	        <span><?php echo $item -> publisher?></span>
                </div>
                
                <?php
        		if ($item)
        		{
        			$pubDateObject = null;
        			if (!is_null($item->publication_date) && !empty($item->publication_date) && $item->publication_date) 
        			{
        				if (strtotime($item->publication_date) != '')
        				{
        					$pubDateObject = new Zend_Date(strtotime($item->publication_date));
        				}	
        			}
        		}
        		?>
        	    	
        		<?php if(!is_null($pubDateObject)) :?>
        	    <div class="publication-date">
        	       	<span class="label"><?php echo $this->translate('Publication Date')?></span>
        			<span class="value"><?php echo date('M Y', $pubDateObject -> getTimestamp());?></span>
        		</div>
        	    <?php endif;?>
                
                <?php if ($item->url) : ?>
                <div class="publication-url">
                	<span class="label"><?php echo $this -> translate("Publication URL");?></span>
        	        <span><a href="<?php echo Engine_Api::_() -> ynresume() -> addScheme($item -> url); ?>"><?php echo $item -> url ?></a></span>
                </div>
                <?php endif;?>
                
                <?php $authors = $item -> getAuthors();?>
                <?php if (count($authors)):?>
                <div class="publication-authors">
                	<span class="label"><?php echo $this -> translate("Author(s)");?></span>
                	<?php $i = 0;?>
                	<?php foreach ($authors as $author):?>
                		<?php if ($i > 0) echo ", "?>
        	        	<span><a href="<?php echo $author -> getHref();?>"><?php echo $author -> getTitle();?></a></span>
        	        	<?php $i++;?>
        	        <?php endforeach;?>
                </div>
                <?php endif;?>
            </div>
            
            <?php if ($item->description) : ?>
            <div class="publication-description section-description"><?php echo $item->description?></div>
            <?php endif;?>
            
            <?php $photos = Engine_Api::_()->getItemTable('ynresume_photo')->getPhotosItem($item);?>
            <?php if (count($photos)) :?>
        	<?php $count = 0;?>
            <div class="section-photos">
                <ul class="photo-lists">
                    <?php foreach ($photos as $photo) : ?>
	                <li class="<?php if ($count >= 3) echo 'view-more'?>">
	                    <div class="photo-item">
                            <a href="<?php echo $photo->getPhotoUrl();?>" data-lightbox-gallery="gallery" class="photoSpan" style="background-image: url('<?php echo $photo->getPhotoUrl('thumb.main');?>');"></a>
                            <div class="photo-title"><?php echo $photo->getTitle();?></div>
                        </div>
	                </li>
		            <?php $count++;?>
		            <?php endforeach;?>
                </ul>
                <?php if (count($photos) > 3) : ?>
                <div class="ynresume-photos-showmore">
                    <a href="javascript:void(0)" class="view-more-photos"><i class="fa fa-arrow-down"></i> <?php echo $this->translate('View more')?></a>
                    <a href="javascript:void(0)" class="view-less-photos"><i class="fa fa-arrow-up"></i> <?php echo $this->translate('View less')?></a>
                </div>
            <?php endif; ?>
            </div>
            <?php endif;?>
            
            <?php if ($manage) : ?>
            <a href="javascript:void(0);" class="edit-section-btn"><i class="fa fa-pencil"></i></a>
            <?php endif; ?>
        </li>
        <?php endforeach;?>    
        </ul>
    </div>    
    <?php endif; ?>
    </div>    
<?php endif; ?>