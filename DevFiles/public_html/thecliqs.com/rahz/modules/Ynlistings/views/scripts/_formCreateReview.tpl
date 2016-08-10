

<form id="customer_reviews_create" enctype="application/x-www-form-urlencoded"
	class="global_form sr_create_list_form"

	method="post">
	<div>
		<div>
			<h3><?php echo $this->translate("Share your thought with other customers")?></h3>
			<p class="form-description"><?php echo $this->translate("Write your own review")?></p>
			<div class="p_l_12">
			<div><?php echo $this->translate("How many stars you would like to rate this item?")?></div>
			
			<div class="form-wrapper" id="overall_rating">
		   
		    <div id="overall_rating-element" class="form-element">
		      <ul id= 'rate_0' class='store_product_ug_rating'>
		        <li id="1" class="rate one"><a href="javascript:void(0);" onclick="doDefaultRating('star_1', '0', 'onestar');" title="<?php echo $this->translate("1 Star"); ?>"   id="star_1_0">1</a></li>
		        <li id="2" class="rate two"><a href="javascript:void(0);"  onclick="doDefaultRating('star_2', '0', 'twostar');" title="<?php echo $this->translate("2 Star"); ?>"   id="star_2_0">2</a></li>
		        <li id="3" class="rate three"><a href="javascript:void(0);"  onclick="doDefaultRating('star_3', '0', 'threestar');" title="<?php echo $this->translate("3 Star"); ?>" id="star_3_0">3</a></li>
		        <li id="4" class="rate four"><a href="javascript:void(0);"  onclick="doDefaultRating('star_4', '0', 'fourstar');" title="<?php echo $this->translate("4 Star"); ?>"   id="star_4_0">4</a></li>
		        <li id="5" class="rate five"><a href="javascript:void(0);"  onclick="doDefaultRating('star_5', '0', 'fivestar');" title="<?php echo $this->translate("5 Star"); ?>"  id="star_5_0">5</a></li>
		      </ul>
		      <input type="hidden" name='review_rate_0' id='review_rate_0' value='' />
		    </div>
		  </div>
			
			
			</div>
			<div class="form-elements">
				<div id="reivew_title-wrapper" class="form-wrapper">
					<div id="reivew_title-label" class="form-label">
						<label for="reivew_title" class="required"><?php echo $this->translate("Please enter a title for your review*")?></label>
					</div>
					<div id="reivew_title-element" class="form-element">
						<input type="text" name="reivew_title" id="reivew_title" value="">
					</div>
				</div>
				<div id="reivew_description-wrapper" class="form-wrapper">
					<div id="reivew_description-label" class="form-label">
						<label for="reivew_description" class="optional"><?php echo $this->translate("Type your review in the space below")?></label>
					</div>
					<div id="reivew_description-element" class="form-element">
						<textarea name="reivew_description" id="reivew_description"
							cols="180" rows="24"
							style="width: 300px; max-width: 400px; height: 120px;"></textarea>
					</div>
				</div>
				<div id="buttons-wrapper" class="form-wrapper">
					<div id="buttons-label" class="form-label">&nbsp;</div>
					<div id="buttons-element" class="form-element">

						<button name="execute" onclick="return submitForm('<?php echo $this->item->getIdentity();?>', $('customer_reviews_create'));" id="execute" type="submit"><?php echo $this->translate("Submit Review")?></button>

						<?php echo $this->translate("or")?> <a name="cancel" id="cancel" type="button"	href="javascript:void(0);"	onclick="history.go(-1); return false;"><?php echo $this->translate("cancel")?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>


<script type="text/javascript">

  function doDefaultRating(element_id, ratingparam_id, classstar) {
    $(element_id + '_' + ratingparam_id).getParent().getParent().className= 'store_product_ug_rating ' + classstar;
    $('review_rate_' + ratingparam_id).value = $(element_id + '_' + ratingparam_id).getParent().id;
  }

  function submitForm(product_id, formObject) {
  	
  		
	  	var flag=true;
	  	formElement = formObject;
	  	var focusEl='';
	  
	  	currentValues = formElement.toQueryString();
	  	console.log(currentValues);
	  	if($('overallrating_error'))
	    	$('overallrating_error').destroy();
	  	if($('reivew_title_error'))
	    	$('reivew_title_error').destroy();
	  	if($('reivew_description_error'))
	    	$('reivew_description_error').destroy();


	   
	  if(typeof formElement['review_rate_0'] != 'undefined' &&  formElement['review_rate_0'].value == 0) {
	      liElement = new Element('span', {'html':'<?php echo $this->translate("* Please complete this field - it is required."); ?>', 'class':'review_error', 'id' :'overallrating_error'}).inject($('overall_rating-element'));
	      flag = false;
	    } 
	  var rate = formElement['review_rate_0'].value;

	  if(formElement['reivew_title']  && formElement['reivew_title'].value.trim() == '') {
			liElement = new Element('span', {'html':'<?php echo $this->translate("* Please complete this field - it is required."); ?>',  'class':'review_error', 'id' :'reivew_title_error'}).inject($('reivew_title-element'));
			flag=false;     
			if(focusEl == '') {
				focusEl = 'reivew_title';
			}   
		} 

	 var title = formElement['reivew_description'].value;
	  if(formElement['reivew_description']  && formElement['reivew_description'].value.trim() == '') {
			liElement = new Element('span', {'html':'<?php echo $this->translate("* Please complete this field - it is required."); ?>',  'class':'review_error', 'id' :'reivew_description_error'}).inject($('reivew_description-element'));
			flag=false;  
			if(focusEl == '') {
				focusEl = 'reivew_description';
			}
		} else if(formElement['reivew_description'] && formElement['reivew_description'].value != '') {
			var str = formElement['reivew_description'].value;
			var length = str.replace(/\s+/g, '').length;
			if(length < 10) {
				var message = en4.core.language.translate('<?php echo $this->translate("* Please enter at least 10 characters (you entered %s characters).") ?>',length);
				liElement = new Element('span', {'html':message,  'class':'review_error', 'id' :'reivew_description_length_error'}).inject($('reivew_description-element')); 
				flag=false; 
				if(focusEl == '') {
					focusEl = 'reivew_description';
				}       
			} 
		}

	var description = formElement['reivew_description'].value;
	  
	if(flag == false) {
		if($(focusEl))
			$(focusEl).focus();
		return false;
	}	
			
	Smoothbox.open('<?php echo $this->url(array('controller' => 'customer-listings', 'action' => 'submit-review', 'item_id' => $this->item->getIdentity(), 'type' => $this->type), 'socialstore_extended', true)?>' + '/rate/'+ formElement['review_rate_0'].value + '/title/' + formElement['reivew_title'].value + '/description/' + formElement['reivew_description'].value);
  	
  	

	 

	return false;	
  }

</script>