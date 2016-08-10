<?php
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
	$project = $resume->getAllProject();
	if (count($project) <= 0 && $manage) 
	{
	    $create = true;
	}
?>
<?php if (!$hide && ($create || $edit)):?>
 <script type="text/javascript">
        window.addEvent('domready', function() {
            if ($('project_members') != null)
            {
				new Autocompleter2.Request.JSON('project_members', '<?php echo $this->url(array('controller' => 'project', 'action' => 'suggest'), 'ynresume_extended', true) ?>', {
			        'minLength': 1,
			        'toElementName': 'memberValues',
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
			        	new Sortables('memberValues-element', {
				            contrain: false,
				            clone: true,
				            handle: 'span',
				            opacity: 0.5,
				            revert: true,
				            onComplete: function(){
				            	ids = [];
				            	$$("div#memberValues-element span").get("id").each(function(e){
					            	if (e){
					            		temp = e.split("_");
					            		ids.push(temp[2]);
						            }
					            });
				                document.getElementById('memberValues').value = ids.join();
				            }
				        });
				        if( document.getElementById('memberValues').value.split(',').length >= 20 ){
				            	document.getElementById('project_members').style.display = 'none';
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
            if ($('project_members') != null){
            	$('project_members').addEvent('keyup', function(e){
    				if(e.code === 13)
    				{
                        name =  name.replace(/(<([^>]+)>)/ig,"");
    					if(name == "")
              		 	{
              		 		return;
              		 	}
            		 	//set value to hidden field
    					var hiddenInputField = document.getElementById('memberValues');
            	        var previousToValues = hiddenInputField.value;
            	        if (checkSpanExists(name, name)){
            	            if (previousToValues==''){
            	              document.getElementById('memberValues').value = name;
            	            }
            	            else {
            	              document.getElementById('memberValues').value = previousToValues + "," + name;
            	            }
            	        }
            	        
              		 	if (checkSpanExists(name, name)){
            	  		 	 //create block
            	             var myElement = new Element("span");
            	             myElement.id = "tospan_"+name+"_"+name;;
            	             
            	             myElement.innerHTML = "<a>"+name+"</a>"+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+name+"\");'>x</a>";
            		         document.getElementById('memberValues-wrapper').style.height= 'auto';
            		
            		         myElement.addClass("tag");
            		
            		         document.getElementById('memberValues-element').appendChild(myElement);
            		         this.fireEvent('push');
            		         $('project_members').set('value', "");
            		         
            		         if( document.getElementById('memberValues').value.split(',').length >= 20 ){
            		            document.getElementById('project_members').style.display = 'none';
            		          }
            	        }
                    }
                    else
                    {
                    	name = $('project_members').value; 
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
	      	
	          // hide the wrapper for usernames if it is empty
	          if (document.getElementById(elementName).value==""){
	            document.getElementById(elementName + '-wrapper').style.height = '0';
	          }
	          
	          document.getElementById('project_members').style.display = 'block';
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

<?php if (count($project) > 0 || (!$hide && ($create || $edit))) : ?>
	<?php $label = Engine_Api::_()->ynresume()->getSectionLabel($this->section); ?>

    <?php if ($manage) : ?>
        <a class="ynresume-add-btn" rel="<?php echo $this->section;?>"><?php echo $this->translate('Add project')?></a>
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
        <div id="ynresume-section-form-project" class="ynresume-section-form">
            <form rel="project" class="section-form" enctype="multipart/form-data">
                <?php $item = null;?>
                <p class="error"></p>
                
                <?php if ($edit && isset($params['item_id'])) : ?>
    	            <?php $item = Engine_Api::_()->getItem('ynresume_project', $params['item_id']);?>
    	            <input type="hidden" name="item_id" class="item_id" id="project-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
                <?php endif; ?>
                
                <div id="project-name-wrapper" class="ynresume-form-wrapper">
                    <label for="project-name"><?php echo $this->translate('*Name')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="project-name" name="name" value="<?php if ($item) echo htmlentities($item->name);?>"/>
                        <p class="error"></p>
                    </div>
                </div>
    
    			<div id="project-occupation-wrapper" class="ynresume-form-wrapper">
                    <label><?php echo $this->translate('Occupation with')?></label>
                    <div class="ynresume-form-input">
                        <select name="occupation" id="project-occupation" value="<?php if ($item) echo $item->occupation_type?>">
                            <?php $occupationArr = $resume -> getProjectOccupationAssoc();?>
                            <?php foreach ($occupationArr as $key => $value) : ?>
                            	<?php $occupation = $item->occupation_type . "::" . $item->occupation_id;?>
                            	<option value="<?php echo $key?>" <?php if ($occupation == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="project-time_period-wrapper" class="ynresume-form-wrapper">                
    				<label><?php echo $this->translate('*Time Period')?></label>
    				<div class="ynresume-form-input ynresume-form-input-4item">
                        <div class="">
	                        <select name="start_month" id="project-start_month" value="<?php if ($item) echo $item->start_month?>">
	                            <?php $month = array('Choose...', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')?>
	                            <?php foreach ($month as $key => $value) : ?>
	                            <option value="<?php echo $key?>" <?php if ($item && $item->start_month == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
	                            <?php endforeach; ?>
	                        </select>
	                        <input type="text" name="start_year" id="project-start_year" value="<?php if ($item) echo $item->start_year?>"/>
	                         - 
	                        <select name="end_month" id="project-end_month">
	                            <?php foreach ($month as $key => $value) : ?>
	                            <option value="<?php echo $key?>" <?php if ($item && $item->end_month == $key) echo 'selected';?>><?php echo $this->translate($value)?></option>
	                            <?php endforeach; ?>
	                        </select>
	                        <input type="text" name="end_year" id="project-end_year" value="<?php if ($item) echo $item->end_year?>"/>
	                        <label id="project-present"><?php echo $this->translate('Present')?></label>
                        </div>
                        <div class="ynresume-form-input-checkbox">
                            <input type="checkbox" id="project-current" name="current" value="1" <?php if ($item && !$item->end_year) echo 'checked'?>/>
                            <label for="project-current"><?php echo $this->translate('Project Ongoing')?></label>
                        </div>
                        <p class="error"></p>
    				</div>
    			</div>
                
                <div id="project-url-wrapper" class="ynresume-form-wrapper">
                    <label for="project-url"><?php echo $this->translate('Project URL')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="project-url" name="url" value="<?php if ($item) echo $item->url?>"/>
                        <p class="error"></p>
                    </div>
                </div>
                
    			<div id="project_members-wrapper" class="ynresume-form-wrapper">
                    <label for="project_members"><?php echo $this->translate('Team Member(s)')?></label>
                    <div class="ynresume-form-input">
                        <input type="text" id="project_members" name="project_members" value=""/>
                        <p class="error"></p>
                    </div>
                </div>   

                <div id="memberValues-wrapper" class="form-wrapper ynresume-form-wrapper">
                    <label id="memberValues-label" class="form-label">&nbsp;</label>
                    <div id="memberValues-element" class="form-element">
                        <input type="hidden" name="memberValues" value="<?php echo ($item) ? $item -> getMemberAsString() : ''; ?>" style="margin-top: -5px" id="memberValues">
                        <?php if ($item):?>
                            <?php $members = $item -> getMemberObjects(); ?>
                            <?php if (count($members)):?>
                                <?php foreach ($members as $member):?>
                                    <?php if ($member->user_id > 0):?>
                                        <?php $member = Engine_Api::_()->getItem('user', $member->user_id)?>
                                        <span id="tospan_<?php echo $member -> getTitle();?>_<?php echo $member -> getIdentity();?>" class="tag">
                                            <a target="_blank" href="<?php echo $member -> getHref();?>"><?php echo $member -> getTitle();?></a> 
                                            <a href="javascript:void(0);" onclick="this.parentNode.destroy();removeFromToValue(<?php echo $i;?>, 'memberValues');">x</a>
                                        </span>
                                    <?php else:?>
                                        <span id="tospan_<?php echo $member -> name;?>_<?php echo $member -> name;?>" class="tag">
                                        <a target="_blank" href="javascript:void(0);"><?php echo $member -> name;?></a> 
                                            <a href="javascript:void(0);" onclick="this.parentNode.destroy();removeFromToValue(<?php echo $i;?>, 'memberValues');">x</a>
                                        </span>
                                    <?php endif;?>
                                <?php endforeach;?>
                                
                                <script type="text/javascript">
                                window.addEvent('domready', function() {
                                    new Sortables('memberValues-element', {
                                        contrain: false,
                                        clone: true,
                                        handle: 'span',
                                        opacity: 0.5,
                                        revert: true,
                                        onComplete: function(){
                                            ids = [];
                                            $$("div#memberValues-element span").get("id").each(function(e){
                                                if (e){
                                                    temp = e.split("_");
                                                    ids.push(temp[2]);
                                                }
                                            });
                                            document.getElementById('memberValues').value = ids.join();
                                        }
                                    });
                                });
                                </script>
                            <?php endif;?>
                        <?php endif;?>
                    </div>
                </div>
    
    			<div style="clear: both;"></div>
    
    			<div id="project-description-wrapper" class="ynresume-form-wrapper">
                    <label for="project-description"><?php echo $this->translate('Description')?></label>
                    <div class="ynresume-form-input">
                        <textarea id="project-description" name="description"/><?php if ($item) echo $item->description?></textarea>
                        <p class="error"></p>
                    </div>
                </div>
                
                <div id="project-photos-wrapper" class="ynresume-form-wrapper upload-photos-wrapper">
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
                                    <input class="section-fileupload" id="project-fileupload" type="file" accept="image/*" name="files[]" multiple>
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
                    
                <input type="hidden" name="memberOrderValues" value="" style="margin-top: -5px" id="memberOrderValues">
                    
                <div class="ynresume-form-buttons ynresume-form-wrapper">
                    <label></label>
                    <div class="ynresume-form-input">
                        <button type="submit" id="submit-btn"><?php echo $this->translate('Save')?></button>
                        <button type="button" class="ynresume-cancel-btn"><?php echo $this->translate('Cancel')?></button>
                        <?php if ($edit && isset($params['item_id'])) : ?>
                        <?php echo $this->translate(' or ')?>
                        <a href="javascript:void(0);" class="ynresume-remove-btn"><?php echo $this->translate('Remove Project')?></a>
                        <?php endif; ?>
                    </div>
                </div>
                
    		</form>
        </div>
        <script type="text/javascript">
                //add event for form
                window.addEvent('domready', function() {
                    var current = $('project-current');
                    if (current) {
                        if (current.checked) {
                            $('project-end_month').hide();
                            $('project-end_year').hide();
                        }
                        else {
                            $('project-present').hide();
                        }
                        
                        current.addEvent('change', function() {
                            if (current.checked) {
                                $('project-end_month').hide();
                                $('project-end_year').hide();
                                $('project-present').show();
                            }
                            else {
                                $('project-present').hide();
                                $('project-end_month').show();
                                $('project-end_year').show();
                            }
                        });
                    }
                });
            </script>
        <?php endif;?>
    <?php endif;?>
    
    <?php if (count($project) > 0) : ?>
    <div id="ynresume-section-list-project" class="ynresume-section-list">
        <ul id="project-list" class="section-list">
        <?php foreach ($project as $item) :?>
        	<li class="section-item" id="project-<?php echo $item->getIdentity()?>">
                <div class="sub-section-item">
                    <?php 
                        $start_month = ($item->start_month) ? $item->start_month : 1;
                        $start_date = date_create($item->start_year.'-'.$start_month.'-'.'1');
                        if ($item->start_month) {
                            $start_time = date_format($start_date, 'M Y');
                        }
                        else {
                            $start_time = date_format($start_date, 'Y');
                        }

                        if ($item->end_year) {
                            $end_month = ($item->end_month) ? $item->end_month : 1;
                            $end_date = date_create($item->end_year.'-'.$end_month.'-'.'1');
                            if ($item->end_month) {
                                $end_time = date_format($end_date, 'M Y');
                            }
                            else {
                                $end_time = date_format($end_date, 'Y');
                            }
                        }
                        else {
                            $end_date = date_create();
                            $end_time = $this->translate('Present');
                        }
                        $diff = date_diff($start_date, $end_date);
                    ?>

                    <div class="project-time section-subline hidden visible_theme_4 span-background-theme-4">                       
                        <span class="start-time"><?php echo $start_time?></span>
                        <span>-</span>
                        <span class="end-time"><?php echo $end_time?></span>
                        <?php $period = $diff->format('%y')*12 + $diff->format('%m');?>
                        <span class="period">(<?php echo $this->translate(array('month_diff','%s months',$period),$period)?>)</span>
                    </div>                  

        			<div class="project-name section-title"><?php echo strip_tags($item->name); ?></div>
        			
        			<?php if ($item -> occupation_type && $item -> occupation_id):?>
        			<?php $occupation = Engine_Api::_()->getItem($item -> occupation_type, $item -> occupation_id);?>
        			<?php if (!is_null($occupation)):?>
        				<div class="project-occupation">
        					<span class="label"><?php echo $this -> translate("Occupation");?></span>
        					<span><?php echo $occupation -> title?></span>
        				</div>
        			<?php endif;?>
        			<?php endif;?>        			
                                            
                    <div class="section-item-calendar">
                        <div>
                            <?php if ($item->start_month) : ?>
                                <span class="month"><?php echo date_format($start_date, 'M');?></span>
                            <?php endif; ?>
                            <span class="year"><?php echo date_format($start_date, 'Y');?></span>
                        </div>

                        <div>
                            <?php if ($item->start_month) : ?>
                                <span class="month"><?php echo date_format($end_date, 'M');?></span>
                            <?php endif; ?>
                            <span class="year"><?php echo date_format($end_date, 'Y');?></span>
                        </div>
                    </div>
        			<div class="project-time section-subline hidden_theme_4">        				
        				<span class="start-time"><?php echo $start_time?></span>
        				<span>-</span>
        				<span class="end-time"><?php echo $end_time?></span>
                        <?php $period = $diff->format('%y')*12 + $diff->format('%m');?>
                        <span class="period">(<?php echo $this->translate(array('month_diff','%s months',$period),$period)?>)</span>
        			</div>        			
        			
        			<?php if ($item->url) : ?>
        			<div class="project-url">
        				<span class="label"><?php echo $this -> translate("Project URL");?></span>
        				<span><a href="<?php echo Engine_Api::_() -> ynresume() -> addScheme($item -> url); ?>"><?php echo $item -> url ?></a></span>
        			</div>
        			<?php endif;?>
                </div>
   			
    			<?php $members = $item -> getMemberObjects(); ?>
    			<?php if (count($members)):?>
    				<div class="section-members">
    					<div class="member-label">
    	                    <a href="javascript:void(0)" class="show-hide-members-btn"><?php echo $this->translate(array('%s member', '%s members', count($members)), count($members))?></a>
    	                </div>
    	                <ul class="member-list">
                            <?php $i = 0;?>
    						<?php foreach ($members as $member):?>
                                <?php if ($i > 0) echo ", "?>
    							<li class="member-item">
    								<?php if ($member->user_id > 0):?>
    									<?php $member = Engine_Api::_()->getItem('user', $member->user_id);?>
    									<?php $link = Engine_Api::_()->ynresume()->getHref($member);?>
    									<?php echo $this->htmlLink($link, $member -> getTitle());?>
    								<?php else:?>    									
    									<span><?php echo $member -> name;?></span>
    								<?php endif;?>
    							</li>
                                <?php $i++;?>
    						<?php endforeach;?>
    					</ul>
    				</div>
    				<script type="text/javascript">
    				window.addEvent('domready', function() {
    					$$('.show-hide-members-btn').removeEvents('click');
    			        $$('.show-hide-members-btn').addEvent('click', function() {
    			            var list = this.getParent('.section-members').getElements('.member-list')[0];
    			            list.toggle();
    			        });
    	            });
                	</script>
    			<?php endif;?>

                <?php if ($item->description) : ?>
                <div class="project-description section-description"><?php echo $item->description?></div>
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