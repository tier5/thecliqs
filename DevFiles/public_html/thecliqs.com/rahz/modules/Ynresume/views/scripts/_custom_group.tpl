<?php
	$this -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
?>
<?php $fieldStructure = Engine_Api::_()->getApi('fields','ynresume')->getFieldsStructureFullHeading($this -> resume, $this -> heading -> field_id, 1, 1);?>

<?php if($this -> fieldValueLoop($this -> resume, $fieldStructure)):?>
	
	<h3 class="section-label">
		<span class="section-label-icon"><img src="<?php echo Engine_Api::_() -> ynresume() -> getPhoto($this -> heading -> photo_id, 'thumb.icon');?>"/></span>
		<span><?php echo $this -> heading -> label;?></span>
	</h3>	
	
	<div id="ynresume_loading_<?php echo $this -> heading -> field_id;?>" style="display: none; text-align: center">
        <img src='application/modules/Ynresume/externals/images/loading.gif'/>
    </div>
	
	<div class="ynresume-section-content">
		<div rel="field_<?php echo $this -> heading -> field_id;?>" class="group-custom-field section-form" id="custom_field_groups_form_<?php echo $this -> heading -> field_id;?>"></div>
			
		<div class="section-item" id="field_<?php echo $this -> heading -> field_id;?>">
			
			<?php if(isset($this -> params['view']) && $this -> params['view']) :?>
				<!-- keep silent -->
			<?php else :?>
				<a href="javascript:void(0);" id="group-custom-field-edit-btn_<?php echo $this -> heading -> field_id;?>" class="edit-section-btn group-custom-field-edit-btn"><i class="fa fa-pencil"></i></a>
				
				<script type="text/javascript">
					window.addEvent('domready', function() {
						if($('group-custom-field-edit-btn_<?php echo $this -> heading -> field_id;?>'))
						{
							$('group-custom-field-edit-btn_<?php echo $this -> heading -> field_id;?>').addEvent('click', function(){
								var loading = $('ynresume_loading_<?php echo $this -> heading -> field_id;?>');
					            if (loading) {
					                loading.show();
					            }
								
								var url = '<?php echo $this->url(array('action' => 'get-custom-group'), 'ynresume_general', true) ?>';
						  		var request = new Request.HTML({
							      url : url,
							      data : {
							        'type' : 'ajax',
							        'edit' : '1',
					 		        'resume_id':'<?php echo $this -> resume -> getIdentity();?>',
							        'field_id' : '<?php echo $this -> heading -> field_id;?>',
							      },
							      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
										$('custom_field_groups_form_<?php echo $this -> heading -> field_id;?>').innerHTML = responseHTML;
							     		addEventToForm();
							     		loading.hide();
							      }
							    });
						   		request.send();
							});
						}
					});	
				</script>
			<?php endif;?>

			<div class="ynresume-profile-fields sub-section-item">
				<div class="ynresume-overview-content ynresume-section-custom-field">
				   	<?php echo $this -> fieldValueLoop($this -> resume, $fieldStructure); ?>
				</div>
			</div>
		</div>
	</div>
<?php else:?>
		
	<?php if(!$this -> params['hide'] && !$this -> params['view']) :?>
		
		<h3 class="section-label"><?php echo $this -> heading -> label;?></h3>	
		<div class="ynresume-section-content">
		<div rel="field_<?php echo $this -> heading -> field_id;?>" class="group-custom-field section-form" id="custom_field_groups_form_<?php echo $this -> heading -> field_id;?>"></div>
		<script type="text/javascript">
			window.addEvent('domready', function() {
				var url = '<?php echo $this->url(array('action' => 'get-custom-group'), 'ynresume_general', true) ?>';
		  		var request = new Request.HTML({
			      url : url,
			      data : {
			        'type' : 'ajax',
			        'resume_id':'<?php echo $this -> resume -> getIdentity();?>',
			        'field_id' : '<?php echo $this -> heading -> field_id;?>',
			      },
			      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
						$('custom_field_groups_form_<?php echo $this -> heading -> field_id;?>').innerHTML = responseHTML;
			     		addEventToForm();
			      }
			    });
		   		request.send();
			});	
		</script>
		</div>
	<?php endif;?>
<?php endif; ?>